#!/usr/bin/env python3
"""
generar_rutina_pdf.py  v9
- Nota de sesion arriba de todo
- Nota por ejercicio debajo del nombre
- Tempo con tabla visual Excentrica / Pausa / Concentrica
- RIR / RPE por serie
- Descanso entre series a nivel de bloque
- Colores extendidos hasta 12 ejercicios
"""
import sys, json, argparse, os
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import mm
from reportlab.platypus import (
    SimpleDocTemplate, Table, TableStyle,
    Paragraph, Spacer
)
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.platypus.flowables import Flowable
from reportlab.lib.utils import ImageReader

C_HEADER    = colors.HexColor('#1e40af')
C_S_HEAD    = colors.HexColor('#2563eb')
C_S_HEAD_L  = colors.HexColor('#eff6ff')
C_BORDER    = colors.HexColor('#e2e5ea')
C_MUTED     = colors.HexColor('#6b7280')
C_TEXT      = colors.HexColor('#111827')
C_GREEN     = colors.HexColor('#059669')
C_GREEN_L   = colors.HexColor('#ecfdf5')
C_PURPLE    = colors.HexColor('#7c3aed')
C_PURPLE_L  = colors.HexColor('#f5f3ff')
C_TEMPO_FG  = colors.HexColor('#1d4ed8')
C_TEMPO_BG  = colors.HexColor('#eff6ff')
C_TEMPO_BD  = colors.HexColor('#bfdbfe')
C_NOTA_S_FG = colors.HexColor('#92400e')
C_NOTA_S_BG = colors.HexColor('#fffbeb')
C_NOTA_S_BD = colors.HexColor('#fde68a')
C_NOTA_EJ_FG = colors.HexColor('#92400e')
C_NOTA_EJ_BG = colors.HexColor('#fffbeb')
C_NOTA_EJ_BD = colors.HexColor('#f59e0b')

TIPO_COLORS = {
    'MONOSERIE': (colors.HexColor('#1d4ed8'), colors.HexColor('#dbeafe')),
    'BISERIE':   (colors.HexColor('#065f46'), colors.HexColor('#d1fae5')),
    'TRISERIE':  (colors.HexColor('#92400e'), colors.HexColor('#fef3c7')),
    'CIRCUITO':  (colors.HexColor('#9d174d'), colors.HexColor('#fce7f3')),
}

NUM_FG = [
    colors.HexColor('#1d4ed8'), colors.HexColor('#065f46'),
    colors.HexColor('#92400e'), colors.HexColor('#9d174d'),
    colors.HexColor('#1d4ed8'), colors.HexColor('#065f46'),
    colors.HexColor('#92400e'), colors.HexColor('#9d174d'),
    colors.HexColor('#1e40af'), colors.HexColor('#166534'),
    colors.HexColor('#854d0e'), colors.HexColor('#831843'),
]
NUM_BG = [
    colors.HexColor('#eff6ff'), colors.HexColor('#f0fdf4'),
    colors.HexColor('#fffbeb'), colors.HexColor('#fdf2f8'),
    colors.HexColor('#e0f2fe'), colors.HexColor('#dcfce7'),
    colors.HexColor('#fef9c3'), colors.HexColor('#fce7f3'),
    colors.HexColor('#dbeafe'), colors.HexColor('#d1fae5'),
    colors.HexColor('#fef3c7'), colors.HexColor('#fdf2f8'),
]
EJ_BGS = [
    colors.white,
    colors.HexColor('#f8f9fb'), colors.HexColor('#f4f6f9'),
    colors.HexColor('#f0f3f7'), colors.HexColor('#eef0f4'),
    colors.HexColor('#eaf0f6'), colors.HexColor('#e8edf2'),
    colors.HexColor('#e5ebf0'), colors.HexColor('#e2e8ee'),
    colors.HexColor('#dfe5ec'), colors.HexColor('#dce2ea'),
    colors.HexColor('#d9dfe8'),
]


def st(name, **kw):
    base = dict(fontName='Helvetica', fontSize=8, textColor=C_TEXT, leading=11)
    base.update(kw)
    return ParagraphStyle(name, **base)


class CroppedImage(Flowable):
    CROP = 0.02

    def __init__(self, path, width, height):
        Flowable.__init__(self)
        self.img_path = path
        self.width    = width
        self.height   = height

    def wrap(self, *args):
        return self.width, self.height

    def draw(self):
        try:
            reader   = ImageReader(self.img_path)
            iw, ih   = reader.getSize()
            scale    = self.width / iw
            draw_w   = self.width
            draw_h   = ih * scale
            y_offset = (self.height - draw_h) / 2
            crop_pts = draw_h * self.CROP
            c = self.canv
            c.saveState()
            p = c.beginPath()
            p.rect(0, crop_pts, draw_w, self.height - 2 * crop_pts)
            c.clipPath(p, stroke=0, fill=0)
            c.drawImage(reader, 0, y_offset,
                        width=draw_w, height=draw_h,
                        preserveAspectRatio=True, mask='auto')
            c.restoreState()
        except Exception:
            pass


def load_image(imagen_path, storage_root, w, h):
    if not imagen_path:
        return None
    full = os.path.join(storage_root, imagen_path)
    if not os.path.exists(full):
        return None
    try:
        return CroppedImage(full, w, h)
    except Exception:
        return None


def formato_serie(serie):
    m = serie.get('metodo', 'normal')
    if m == 'normal':
        return f"{serie.get('reps', '-')} reps"
    elif m == '888':
        r = serie.get('reps_888', 8)
        return f"{r}-{r}-{r}"
    elif m == 'restpause':
        reps = serie.get('reps_rp') or serie.get('reps', '-')
        return f"{reps} reps<br/>Pausa {serie.get('descanso', 15)}s"
    elif m == '21s':
        r = serie.get('reps_21s', 7)
        return f"{r}+{r}+{r}"
    elif m == '10_21':
        return "x10 -> 21s"
    elif m == 'isometria':
        return f"{serie.get('reps_brazo', 4)}r+{serie.get('reps_ambos', 8)}r"
    elif m == 'forzadas':
        reps  = serie.get('reps_fz') or serie.get('reps', '-')
        asist = serie.get('reps_asistidas', '-')
        return f"{reps}+{asist} reps"
    elif m == 'parciales':
        reps = serie.get('reps_pc') or serie.get('reps', '-')
        return f"Parcial<br/>{reps} reps"
    elif m == 'negativas':
        reps = serie.get('reps_ng') or serie.get('reps', '-')
        return f"Excentrica<br/>{reps} reps"
    return f"{serie.get('reps', '-')} reps"


def etiqueta_metodo(m):
    return {
        'normal':    '',
        '888':       'DESC.',
        'restpause': 'REST-PAUSE',
        '21s':       '3 RANGOS',
        '10_21':     '10+21S',
        'isometria': 'ISO+ROM',
        'forzadas':  'FORZADAS',
        'parciales': 'PARCIALES',
        'negativas': 'NEGATIVAS',
    }.get(m, m.upper())


def build_tempo_table(serie, col_width):
    if str(serie.get('tempo_activo', '0')) not in ('1', 'true', 'True'):
        return None

    tE = str(serie.get('tempo_excentrica',  '') or '0').strip()
    tP = str(serie.get('tempo_pausa',        '') or '0').strip()
    tC = str(serie.get('tempo_concentrica', '') or '0').strip()

    if tE == '0' and tP == '0' and tC == '0':
        return None

    # Una sola linea compacta: etiqueta + valor e-p-c
    s_lbl = ParagraphStyle('tclbl',
        fontName='Helvetica', fontSize=5.5, textColor=C_MUTED,
        alignment=TA_CENTER, leading=7)
    s_val = ParagraphStyle('tcval',
        fontName='Helvetica-Bold', fontSize=7, textColor=C_TEMPO_FG,
        alignment=TA_CENTER, leading=9)

    t = Table(
        [[Paragraph('TEMPO', s_lbl)],
         [Paragraph(f'{tE}  - {tP}  - {tC}', s_val)]],
        colWidths=[col_width - 6]
    )
    t.setStyle(TableStyle([
        ('BACKGROUND',    (0,0), (-1,-1), C_TEMPO_BG),
        ('BOX',           (0,0), (-1,-1), 0.5, C_TEMPO_BD),
        ('TOPPADDING',    (0,0), (-1,-1), 2),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2),
        ('LEFTPADDING',   (0,0), (-1,-1), 2),
        ('RIGHTPADDING',  (0,0), (-1,-1), 2),
        ('ALIGN',         (0,0), (-1,-1), 'CENTER'),
        ('VALIGN',        (0,0), (-1,-1), 'MIDDLE'),
    ]))
    return t


def get_rir(serie):
    if str(serie.get('rir_activo', '0')) not in ('1', 'true', 'True'):
        return None
    modo = str(serie.get('rir_modo', 'rir')).upper()
    val  = serie.get('rir_valor', '')
    if not val:
        return None
    return f'{modo} {val}'


def celda_serie(serie, s_val, s_met, s_rir, s_dash, S_W, vacia=False):
    if vacia:
        return Paragraph('-', s_dash)

    metodo    = serie.get('metodo', 'normal')
    val       = formato_serie(serie)
    label     = etiqueta_metodo(metodo)
    tempo_tbl = build_tempo_table(serie, S_W)
    rir       = get_rir(serie)

    rows = []
    if label:
        rows.append([Paragraph(label, s_met)])
    rows.append([Paragraph(val, s_val)])
    if tempo_tbl:
        rows.append([tempo_tbl])
    if rir:
        s_rir_local = ParagraphStyle('rrl',
            fontName='Helvetica-Bold', fontSize=5.5,
            textColor=C_PURPLE, backColor=C_PURPLE_L,
            alignment=TA_CENTER, leading=8)
        rows.append([Paragraph(rir, s_rir_local)])

    if len(rows) == 1 and not label:
        return rows[0][0]

    inner = Table(rows, colWidths=[S_W - 4])
    inner.setStyle(TableStyle([
        ('ALIGN',         (0,0), (-1,-1), 'CENTER'),
        ('VALIGN',        (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING',    (0,0), (-1,-1), 1),
        ('BOTTOMPADDING', (0,0), (-1,-1), 1),
        ('LEFTPADDING',   (0,0), (-1,-1), 0),
        ('RIGHTPADDING',  (0,0), (-1,-1), 0),
    ]))
    return inner


def build_nota_sesion(texto, page_w):
    if not texto or not texto.strip():
        return None
    s_label = ParagraphStyle('nsl',
        fontName='Helvetica-Bold', fontSize=7,
        textColor=C_NOTA_S_FG, leading=10)
    s_body = ParagraphStyle('ns',
        fontName='Helvetica', fontSize=8,
        textColor=C_NOTA_S_FG, leading=12,
        leftIndent=4, rightIndent=4)
    t = Table(
        [[Paragraph('NOTA DE SESION', s_label)],
         [Paragraph(texto.strip(), s_body)]],
        colWidths=[page_w]
    )
    t.setStyle(TableStyle([
        ('BACKGROUND',    (0,0), (-1,-1), C_NOTA_S_BG),
        ('BOX',           (0,0), (-1,-1), 1.2, C_NOTA_S_BD),
        ('LINEBELOW',     (0,0), (-1,0),  0.5, C_NOTA_S_BD),
        ('TOPPADDING',    (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
        ('LEFTPADDING',   (0,0), (-1,-1), 8),
        ('RIGHTPADDING',  (0,0), (-1,-1), 8),
    ]))
    return t


def build_nombre_cell(nombre_p, nota_ej_texto, nombre_w):
    """Combina nombre del ejercicio + nota en una mini tabla vertical."""
    nota_ej_texto = (nota_ej_texto or '').strip()
    if not nota_ej_texto:
        return nombre_p

    s_nota = ParagraphStyle('nej',
        fontName='Helvetica', fontSize=7,
        textColor=C_NOTA_EJ_FG, leading=10,
        leftIndent=3)
    nota_p = Paragraph(nota_ej_texto, s_nota)

    cell = Table(
        [[nombre_p], [nota_p]],
        colWidths=[nombre_w - 4]
    )
    cell.setStyle(TableStyle([
        ('TOPPADDING',    (0,0), (-1,-1), 1),
        ('BOTTOMPADDING', (0,0), (-1,-1), 1),
        ('LEFTPADDING',   (0,0), (-1,-1), 0),
        ('RIGHTPADDING',  (0,0), (-1,-1), 0),
        ('LINEABOVE',     (0,1), (-1,1),  0.5, C_NOTA_EJ_BD),
        ('BACKGROUND',    (0,1), (-1,1),  C_NOTA_EJ_BG),
        ('LEFTPADDING',   (0,1), (-1,1),  3),
        ('TOPPADDING',    (0,1), (-1,1),  3),
        ('BOTTOMPADDING', (0,1), (-1,1),  3),
    ]))
    return cell


def generar_pdf(data: dict, output_path: str, storage_root: str):
    IMG_H = 52 * mm
    ROW_H = IMG_H + 6 * mm

    doc = SimpleDocTemplate(
        output_path, pagesize=A4,
        leftMargin=12*mm, rightMargin=12*mm,
        topMargin=14*mm,  bottomMargin=14*mm,
    )

    s_sh    = st('sh', fontSize=7,   fontName='Helvetica-Bold',
                 textColor=C_S_HEAD, alignment=TA_CENTER)
    s_lh    = st('lh', fontSize=6.5, fontName='Helvetica-Bold',
                 textColor=C_MUTED,  alignment=TA_CENTER)
    s_nombre = st('nm', fontSize=10, fontName='Helvetica-Bold', leading=13)
    s_val   = st('vl', fontSize=8,   fontName='Helvetica-Bold',
                 alignment=TA_CENTER, leading=12)
    s_met   = st('mt', fontSize=6,   textColor=C_MUTED, alignment=TA_CENTER)
    s_dash  = st('ds', fontSize=10,
                 textColor=colors.HexColor('#d0d5dd'), alignment=TA_CENTER)
    s_rir   = st('rr', fontSize=5.5, fontName='Helvetica-Bold',
                 textColor=C_PURPLE, backColor=C_PURPLE_L, alignment=TA_CENTER)

    story = []

    PAGE_W   = A4[0] - 24*mm
    NUM_W    = 12*mm
    NOMBRE_W = 44*mm
    IMG_W    = 50*mm
    EJ_TOT   = NUM_W + NOMBRE_W + IMG_W

    # ── Nota de sesion ──
    nota_sesion_tbl = build_nota_sesion(
        data.get('nota_sesion', ''), PAGE_W)
    if nota_sesion_tbl:
        story.append(nota_sesion_tbl)
        story.append(Spacer(1, 8))

    for bloque in data.get('bloques', []):
        tipo            = bloque.get('tipo', 'MONOSERIE').upper()
        ejercicios      = bloque.get('ejercicios', [])
        descanso_val    = bloque.get('descanso_valor',  '')
        descanso_unidad = bloque.get('descanso_unidad', 'seg')

        if not ejercicios:
            continue

        max_series = max(len(e.get('series', [])) for e in ejercicios)
        if max_series == 0:
            continue

        tipo_fg, tipo_bg = TIPO_COLORS.get(tipo, (C_HEADER, colors.HexColor('#dbeafe')))
        REST_W = PAGE_W - EJ_TOT
        S_W    = REST_W / max_series

        # ── Banner tipo + descanso ──
        banner_text = f'<b>{tipo}</b>'
        if descanso_val:
            banner_text += f'     Descanso entre series: {descanso_val} {descanso_unidad}'

        banner_p = Paragraph(
            banner_text,
            ParagraphStyle('bn', fontName='Helvetica-Bold',
                           fontSize=9, textColor=tipo_fg, alignment=TA_CENTER)
        )
        banner = Table([[banner_p]], colWidths=[PAGE_W])
        banner.setStyle(TableStyle([
            ('BACKGROUND',    (0,0), (-1,-1), tipo_bg),
            ('TOPPADDING',    (0,0), (-1,-1), 5),
            ('BOTTOMPADDING', (0,0), (-1,-1), 5),
            ('BOX',           (0,0), (-1,-1), 1.2, tipo_fg),
        ]))
        story.append(banner)

        # ── Encabezado columnas ──
        col_widths = [NUM_W, NOMBRE_W, IMG_W] + [S_W] * max_series
        header = [
            Paragraph('', s_lh),
            Paragraph('<b>Ejercicio</b>', s_lh),
            Paragraph('', s_lh),
        ] + [Paragraph(f'<b>S{s+1}</b>', s_sh) for s in range(max_series)]

        table_data  = [header]
        row_heights = [7*mm]

        for i, ej in enumerate(ejercicios):
            n_fg  = NUM_FG[i % len(NUM_FG)]
            n_bg  = NUM_BG[i % len(NUM_BG)]
            ej_bg = EJ_BGS[i % len(EJ_BGS)]

            num_p = Paragraph(
                f'<b>{i+1}</b>',
                ParagraphStyle('np', fontName='Helvetica-Bold',
                               fontSize=12, textColor=n_fg, alignment=TA_CENTER)
            )

            nombre_p = Paragraph(f"<b>{ej.get('nombre','')}</b>", s_nombre)

            # ── Nota por ejercicio ──
            nombre_cell = build_nombre_cell(
                nombre_p,
                ej.get('nota_ej', ''),
                NOMBRE_W
            )

            img_obj = load_image(
                ej.get('imagen', ''), storage_root,
                IMG_W - 4*mm, IMG_H
            )
            if img_obj:
                img_inner = Table([[img_obj]], colWidths=[IMG_W - 2*mm])
                img_inner.setStyle(TableStyle([
                    ('ALIGN',         (0,0), (-1,-1), 'CENTER'),
                    ('VALIGN',        (0,0), (-1,-1), 'MIDDLE'),
                    ('TOPPADDING',    (0,0), (-1,-1), 2),
                    ('BOTTOMPADDING', (0,0), (-1,-1), 2),
                    ('LEFTPADDING',   (0,0), (-1,-1), 0),
                    ('RIGHTPADDING',  (0,0), (-1,-1), 0),
                ]))
                img_cell = img_inner
            else:
                img_cell = Paragraph(
                    'Sin imagen',
                    ParagraphStyle('ni', fontSize=6, textColor=C_MUTED,
                                   alignment=TA_CENTER)
                )

            series = ej.get('series', [])
            celdas = []
            for s in range(max_series):
                if s < len(series):
                    celdas.append(
                        celda_serie(series[s], s_val, s_met, s_rir,
                                    s_dash, S_W)
                    )
                else:
                    celdas.append(Paragraph('-', s_dash))

            table_data.append([num_p, nombre_cell, img_cell] + celdas)
            row_heights.append(ROW_H)

        t = Table(table_data, colWidths=col_widths, rowHeights=row_heights)
        ts = TableStyle([
            ('BACKGROUND',    (0,0),  (-1,0),  colors.HexColor('#f0f2f5')),
            ('BACKGROUND',    (3,0),  (-1,0),  C_S_HEAD_L),
            ('TOPPADDING',    (0,0),  (-1,0),  4),
            ('BOTTOMPADDING', (0,0),  (-1,0),  4),
            ('LINEBELOW',     (0,0),  (-1,0),  1.2, C_S_HEAD),
            ('TOPPADDING',    (0,1),  (-1,-1), 4),
            ('BOTTOMPADDING', (0,1),  (-1,-1), 4),
            ('LEFTPADDING',   (0,0),  (-1,-1), 4),
            ('RIGHTPADDING',  (0,0),  (-1,-1), 4),
            ('VALIGN',        (0,0),  (-1,-1), 'MIDDLE'),
            ('ALIGN',         (0,0),  (0,-1),  'CENTER'),
            ('ALIGN',         (1,0),  (1,-1),  'LEFT'),
            ('ALIGN',         (2,0),  (2,-1),  'CENTER'),
            ('ALIGN',         (3,0),  (-1,-1), 'CENTER'),
            ('GRID',          (0,0),  (-1,-1), 0.4, C_BORDER),
            ('LINEAFTER',     (2,0),  (2,-1),  1.2, C_BORDER),
            ('BOX',           (0,0),  (-1,-1), 1.2, C_BORDER),
        ])
        for i in range(len(ejercicios)):
            nb = NUM_BG[i % len(NUM_BG)]
            bg = EJ_BGS[i % len(EJ_BGS)]
            ts.add('BACKGROUND', (0, i+1), (0, i+1),  nb)
            ts.add('BACKGROUND', (1, i+1), (2, i+1),  bg)
            ts.add('BACKGROUND', (3, i+1), (-1, i+1), colors.white)
        for i in range(1, len(ejercicios)):
            ts.add('LINEABOVE', (0, i+1), (-1, i+1), 0.8, C_BORDER)
        t.setStyle(ts)

        story.append(t)
        story.append(Spacer(1, 10))

    doc.build(story)


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--data',    required=True)
    parser.add_argument('--output',  required=True)
    parser.add_argument('--storage', default='/var/www/html/storage/app/public')
    args = parser.parse_args()
    try:
        data = json.loads(args.data)
        generar_pdf(data, args.output, args.storage)
        print(f"PDF generado: {args.output}")
        sys.exit(0)
    except Exception as e:
        import traceback; traceback.print_exc()
        print(f"ERROR: {e}", file=sys.stderr)
        sys.exit(1)