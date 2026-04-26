#!/usr/bin/env python3
"""
generar_rutina_pdf.py  v6
- Sin nombre, semana ni dia en el encabezado
- Sin pesos en las series
- Imagen con 2% recortado arriba y abajo via clipPath
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

C_HEADER   = colors.HexColor('#1e40af')
C_S_HEAD   = colors.HexColor('#2563eb')
C_S_HEAD_L = colors.HexColor('#eff6ff')
C_BORDER   = colors.HexColor('#e2e5ea')
C_MUTED    = colors.HexColor('#6b7280')
C_TEXT     = colors.HexColor('#111827')

TIPO_COLORS = {
    'MONOSERIE': (colors.HexColor('#1d4ed8'), colors.HexColor('#dbeafe')),
    'BISERIE':   (colors.HexColor('#065f46'), colors.HexColor('#d1fae5')),
    'TRISERIE':  (colors.HexColor('#92400e'), colors.HexColor('#fef3c7')),
    'CIRCUITO':  (colors.HexColor('#9d174d'), colors.HexColor('#fce7f3')),
}
NUM_FG = [colors.HexColor('#1d4ed8'), colors.HexColor('#065f46'),
          colors.HexColor('#92400e'), colors.HexColor('#9d174d')]
NUM_BG = [colors.HexColor('#eff6ff'), colors.HexColor('#f0fdf4'),
          colors.HexColor('#fffbeb'), colors.HexColor('#fdf2f8')]
EJ_BGS = [colors.white, colors.HexColor('#f8f9fb'),
          colors.HexColor('#f4f6f9'), colors.HexColor('#f0f3f7')]


def st(name, **kw):
    base = dict(fontName='Helvetica', fontSize=8, textColor=C_TEXT, leading=11)
    base.update(kw)
    return ParagraphStyle(name, **base)


# ── Imagen con 2% crop arriba y abajo usando clipPath nativo de ReportLab ────
class CroppedImage(Flowable):
    """
    Dibuja la imagen escalada para ocupar todo el ancho de la celda,
    luego recorta un 2% arriba y abajo usando save/clip/restore del canvas.
    Sin Pillow, sin pérdida de calidad.
    """
    CROP = 0.02  # 2% de cada lado

    def __init__(self, path, width, height):
        Flowable.__init__(self)
        self.img_path = path
        self.width    = width
        self.height   = height

    def wrap(self, *args):
        return self.width, self.height

    def draw(self):
        try:
            reader    = ImageReader(self.img_path)
            iw, ih    = reader.getSize()

            # Escalar para que el ancho encaje exactamente
            scale     = self.width / iw
            draw_w    = self.width
            draw_h    = ih * scale

            # Centrar verticalmente dentro de self.height
            y_offset  = (self.height - draw_h) / 2

            # Cuánto recortar en puntos (2% de la altura dibujada)
            crop_pts  = draw_h * self.CROP

            c = self.canv
            c.saveState()

            # Clip: deja ver solo la franja interior (sin el 2% de arriba y abajo)
            p = c.beginPath()
            p.rect(0, crop_pts, draw_w, self.height - 2 * crop_pts)
            c.clipPath(p, stroke=0, fill=0)

            # Dibujar imagen completa (el clip oculta los bordes)
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


# ── Formato de serie SIN pesos ───────────────────────────────────────────────
def formato_serie(serie):
    m = serie.get('metodo', 'normal')
    if m == 'normal':
        return f"{serie.get('reps', '–')} reps"
    elif m == '888':
        r = serie.get('reps_888', 8)
        return f"{r}-{r}-{r}"
    elif m == 'restpause':
        return f"{serie.get('reps', '–')} reps<br/>Pausa {serie.get('descanso', 15)}s"
    elif m == '21s':
        r = serie.get('reps_21s', 7)
        return f"{r}+{r}+{r}"
    elif m == '10_21':
        return "x10 → 21s"
    elif m == 'isometria':
        return f"{serie.get('reps_brazo', 4)}r+{serie.get('reps_ambos', 8)}r"
    elif m == 'forzadas':
        return f"{serie.get('reps', '–')}+{serie.get('reps_asistidas', '–')} reps"
    elif m == 'parciales':
        return f"Parcial<br/>{serie.get('reps', '–')} reps"
    elif m == 'negativas':
        return f"Excéntrica<br/>{serie.get('reps', '–')} reps"
    return f"{serie.get('reps', '–')} reps"


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


# ── Generador principal ───────────────────────────────────────────────────────
def generar_pdf(data: dict, output_path: str, storage_root: str):
    IMG_H = 52 * mm
    ROW_H = IMG_H + 6 * mm

    doc = SimpleDocTemplate(
        output_path, pagesize=A4,
        leftMargin=12*mm, rightMargin=12*mm,
        topMargin=14*mm,  bottomMargin=14*mm,
    )

    s_sh     = st('sh', fontSize=7,   fontName='Helvetica-Bold', textColor=C_S_HEAD,  alignment=TA_CENTER)
    s_lh     = st('lh', fontSize=6.5, fontName='Helvetica-Bold', textColor=C_MUTED,   alignment=TA_CENTER)
    s_nombre = st('nm', fontSize=10,  fontName='Helvetica-Bold', leading=13)
    s_val    = st('vl', fontSize=8,   fontName='Helvetica-Bold', alignment=TA_CENTER, leading=12)
    s_met    = st('mt', fontSize=6,   textColor=C_MUTED,         alignment=TA_CENTER)
    s_dash   = st('ds', fontSize=10,  textColor=colors.HexColor('#d0d5dd'), alignment=TA_CENTER)

    story = []

    PAGE_W   = A4[0] - 24*mm
    NUM_W    = 12*mm
    NOMBRE_W = 44*mm
    IMG_W    = 50*mm
    EJ_TOT   = NUM_W + NOMBRE_W + IMG_W

    for bloque in data.get('bloques', []):
        tipo       = bloque.get('tipo', 'MONOSERIE').upper()
        ejercicios = bloque.get('ejercicios', [])
        if not ejercicios:
            continue

        max_series = max(len(e.get('series', [])) for e in ejercicios)
        if max_series == 0:
            continue

        tipo_fg, tipo_bg = TIPO_COLORS.get(tipo, (C_HEADER, colors.HexColor('#dbeafe')))
        REST_W = PAGE_W - EJ_TOT
        S_W    = REST_W / max_series

        # ── Banner tipo ──
        banner_p = Paragraph(
            f'<b>{tipo}</b>',
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

            # Número
            num_p = Paragraph(
                f'<b>{i+1}</b>',
                ParagraphStyle('np', fontName='Helvetica-Bold',
                               fontSize=12, textColor=n_fg, alignment=TA_CENTER)
            )

            # Nombre
            nombre_p = Paragraph(f"<b>{ej.get('nombre','')}</b>", s_nombre)

            # Imagen con 2% crop
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
                    ParagraphStyle('ni', fontSize=6, textColor=C_MUTED, alignment=TA_CENTER)
                )

            # Series — solo reps, sin pesos
            celdas = []
            series = ej.get('series', [])
            for s in range(max_series):
                if s < len(series):
                    serie  = series[s]
                    metodo = serie.get('metodo', 'normal')
                    val    = formato_serie(serie)
                    label  = etiqueta_metodo(metodo)
                    if label:
                        inner = Table(
                            [[Paragraph(label, s_met)],
                             [Paragraph(val,   s_val)]],
                            colWidths=[S_W - 4],
                        )
                        inner.setStyle(TableStyle([
                            ('ALIGN',         (0,0), (-1,-1), 'CENTER'),
                            ('VALIGN',        (0,0), (-1,-1), 'MIDDLE'),
                            ('TOPPADDING',    (0,0), (-1,-1), 1),
                            ('BOTTOMPADDING', (0,0), (-1,-1), 1),
                            ('LEFTPADDING',   (0,0), (-1,-1), 0),
                            ('RIGHTPADDING',  (0,0), (-1,-1), 0),
                        ]))
                        celdas.append(inner)
                    else:
                        celdas.append(Paragraph(val, s_val))
                else:
                    celdas.append(Paragraph('–', s_dash))

            table_data.append([num_p, nombre_p, img_cell] + celdas)
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
