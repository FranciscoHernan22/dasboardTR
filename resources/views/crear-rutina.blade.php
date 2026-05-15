@extends('layouts.entrenador')
@section('titulo', 'Generador de Series')
@section('contenido')

<style>
.fila-ejercicio { display:flex; align-items:center; gap:15px; margin-bottom:10px; }
.fila-ejercicio select { width:220px; }
.dropdown-ejercicio { width:350px; position:relative; font-family:Arial; display:flex; justify-content:flex-end; }
.dropdown-btn { padding:8px; border:1px solid #aaa; border-radius:5px; background:white; cursor:pointer; }
.dropdown-list { position:absolute; width:100%; background:white; border:1px solid #ddd; max-height:200px; overflow-y:auto; z-index:999; }
.dropdown-item { display:flex; align-items:center; padding:5px; cursor:pointer; }
.dropdown-item img { width:120px; height:120px; object-fit:cover; border-radius:6px; margin-right:10px; }
.dropdown-item:hover { background:#f0f0f0; }
.modal-circ-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center; }
.modal-circ-overlay.open { display:flex; }
.modal-circ-box { background:white; border-radius:14px; width:100%; max-width:320px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal-circ-box h3 { font-size:1rem; font-weight:700; margin:0 0 6px; }
.modal-circ-box p { font-size:0.85rem; color:#6b7280; margin:0 0 16px; }
.circ-num-input { width:100%; border:1.5px solid #d0d5dd; border-radius:8px; padding:10px; font-size:1.4rem; text-align:center; margin-bottom:14px; box-sizing:border-box; }
.circ-num-input:focus { outline:none; border-color:#2563eb; }
.circ-btns { display:flex; gap:8px; }
.circ-btn-cancel { flex:1; padding:8px; border:1px solid #d0d5dd; border-radius:7px; background:white; color:#6b7280; font-size:0.85rem; font-weight:600; cursor:pointer; }
.circ-btn-ok { flex:1; padding:8px; border:none; border-radius:7px; background:#2563eb; color:white; font-size:0.85rem; font-weight:600; cursor:pointer; }
.circ-btn-ok:hover { background:#1d4ed8; }
</style>

{{-- Modal circuito --}}
<div class="modal-circ-overlay" id="modalCircuito" onclick="if(event.target===this)cerrarModalCircuito()">
    <div class="modal-circ-box">
        <h3>Circuito</h3>
        <p>¿Cuántos ejercicios? (2 – 12)</p>
        <input type="number" class="circ-num-input" id="circuitoNum" min="2" max="12" value="4">
        <div class="circ-btns">
            <button class="circ-btn-cancel" onclick="cerrarModalCircuito()">Cancelar</button>
            <button class="circ-btn-ok"     onclick="confirmarCircuito()">Agregar</button>
        </div>
    </div>
</div>

<h2>Datos del Entrenamiento</h2>

<form id="form-rutina" action="{{ route('guardarRutina') }}" method="POST">
    @csrf

    <label>Entrenamiento:</label>
    <select name="entrenamiento" required>
        <option value="">-- Selecciona entrenamiento --</option>
        <option value="Fuerza">Fuerza</option>
        <option value="Hipertrofia">Hipertrofia</option>
        <option value="Resistencia">Resistencia</option>
        <option value="Funcional">Funcional</option>
    </select>
    <br><br>

    <label>Semana:</label>
    <select name="semana" required>
        <option value="">-- Selecciona semana --</option>
        <option value="1">Semana 1</option>
        <option value="2">Semana 2</option>
        <option value="3">Semana 3</option>
        <option value="4">Semana 4</option>
    </select>
    <br><br>

    <label>Día:</label>
    <select name="dia" required>
        <option value="">-- Selecciona día --</option>
        <option value="1">Lunes</option>
        <option value="2">Martes</option>
        <option value="3">Miércoles</option>
        <option value="4">Jueves</option>
        <option value="5">Viernes</option>
        <option value="6">Sábado</option>
        <option value="7">Domingo</option>
    </select>
    <hr>

    <label>Seleccionar Cliente:</label>
    <select name="user_id" required>
        <option value="">-- Selecciona un cliente --</option>
        @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
        @endforeach
    </select>
    <hr>

    <div id="contenedor-ejercicios"></div>
    <hr>

    <div id="controles" style="margin-top:20px;">
        <label for="tipo-serie">Seleccionar tipo:</label>
        <select id="tipo-serie">
            <option value="">-- Selecciona --</option>
            <option value="monoserie">Monoserie</option>
            <option value="biserie">Biserie</option>
            <option value="triserie">Triserie</option>
            <option value="circuito">Circuito</option>
        </select>
        <br><br>
        <button type="submit" id="btn-finalizar" style="display:none;">
            Guardar Rutina
        </button>
    </div>
</form>

<script>
const ejerciciosPorGrupo = @json($ejercicios->groupBy('segmento'));
let contadorGlobal = 0;

const contenedor   = document.getElementById("contenedor-ejercicios");
const btnFinalizar = document.getElementById("btn-finalizar");
const selectTipo   = document.getElementById("tipo-serie");

/* ── Modal circuito ── */
function abrirModalCircuito() {
    document.getElementById('circuitoNum').value = 4;
    document.getElementById('modalCircuito').classList.add('open');
    setTimeout(() => document.getElementById('circuitoNum').focus(), 50);
}
function cerrarModalCircuito() {
    document.getElementById('modalCircuito').classList.remove('open');
}
function confirmarCircuito() {
    const n        = parseInt(document.getElementById('circuitoNum').value) || 4;
    const cantidad = Math.min(12, Math.max(2, n));
    cerrarModalCircuito();
    agregarBloque('circuito', cantidad);
    btnFinalizar.style.display = 'inline-block';
}
document.getElementById('circuitoNum').addEventListener('keydown', e => {
    if (e.key === 'Enter')  confirmarCircuito();
    if (e.key === 'Escape') cerrarModalCircuito();
});

/* ── Selector de tipo ── */
selectTipo.addEventListener("change", function () {
    const tipo = this.value;
    if (tipo === "") return;

    if (tipo === "circuito") {
        selectTipo.value = "";
        abrirModalCircuito();
        return;
    }

    const cantidad = { monoserie:1, biserie:2, triserie:3 }[tipo] ?? 1;
    agregarBloque(tipo, cantidad);
    btnFinalizar.style.display = "inline-block";
    selectTipo.value = "";
});

/* ── Agregar bloque ── */
function agregarBloque(tipo, cantidad) {
    contadorGlobal++;

    const bloque = document.createElement("div");
    bloque.style.cssText = "border:1px solid #ccc;padding:15px;margin-bottom:20px;border-radius:8px;background:#f9f9f9;position:relative;";

    const btnQuitar = document.createElement("button");
    btnQuitar.textContent = "Quitar";
    btnQuitar.type = "button";
    btnQuitar.style.cssText = "position:absolute;top:10px;right:10px;background:#ff5252;color:white;border:none;padding:5px 10px;cursor:pointer;border-radius:5px;";
    btnQuitar.addEventListener("click", () => { bloque.remove(); validarBloques(); });
    bloque.appendChild(btnQuitar);

    const titulo = document.createElement("h3");
    titulo.innerText = tipo.toUpperCase() + " #" + contadorGlobal +
        (tipo === 'circuito' ? ` · ${cantidad} ejercicios` : '');
    titulo.style.marginTop = "0";
    bloque.appendChild(titulo);

    for (let i = 0; i < cantidad; i++) {
        bloque.appendChild(crearGrupoEjercicio(i + 1, tipo, contadorGlobal));
    }

    contenedor.appendChild(bloque);
}

/* ── Crear fila de ejercicio ── */
function crearGrupoEjercicio(num, tipo, idUnico) {
    const div = document.createElement("div");
    div.style.marginBottom = "12px";

    const gruposMusculares = Object.keys(ejerciciosPorGrupo);

    div.innerHTML = `
        <div class="fila-ejercicio">
            <select class="form-control grupo-muscular" required>
                <option value="">Grupo muscular</option>
                ${gruposMusculares.map(g => `<option value="${g}">${g}</option>`).join('')}
            </select>
            <div class="dropdown-ejercicio">
                <input type="hidden" name="rutina[${idUnico}][${num}][ejercicio_id]" class="ejercicio-id">
                <div class="dropdown-btn">Selecciona un ejercicio</div>
                <div class="dropdown-list" style="display:none;"></div>
            </div>
        </div>
        <input type="hidden" name="rutina[${idUnico}][tipo]" value="${tipo}">
        <input type="hidden" name="rutina[${idUnico}][grupo]" value="${tipo}_${idUnico}">
        <input type="number" name="rutina[${idUnico}][${num}][series]" placeholder="Series" required>
        <input type="number" name="rutina[${idUnico}][${num}][reps]"   placeholder="Repeticiones" required>
    `;

    const grupoSelect  = div.querySelector(".grupo-muscular");
    const dropdown     = div.querySelector(".dropdown-ejercicio");
    const dropdownBtn  = dropdown.querySelector(".dropdown-btn");
    const dropdownList = dropdown.querySelector(".dropdown-list");
    const hiddenInput  = dropdown.querySelector(".ejercicio-id");

    grupoSelect.addEventListener("change", function () {
        const grupo = this.value;
        dropdownList.innerHTML = "";
        if (!grupo) { dropdown.style.display = "none"; return; }
        dropdown.style.display = "block";

        ejerciciosPorGrupo[grupo].forEach(e => {
            const item = document.createElement("div");
            item.classList.add("dropdown-item");
            item.innerHTML = `
                <img src="https://res.cloudinary.com/ddls3oqbe/image/upload/${e.imagen}">
                <span>${e.nombre}</span>
            `;
            item.addEventListener("click", () => {
                dropdownBtn.innerHTML = e.nombre;
                hiddenInput.value     = e.id;
                dropdownList.style.display = "none";
            });
            dropdownList.appendChild(item);
        });
    });

    dropdownBtn.addEventListener("click", () => {
        dropdownList.style.display =
            dropdownList.style.display === "none" ? "block" : "none";
    });

    return div;
}

function validarBloques() {
    if (contenedor.children.length === 0) {
        btnFinalizar.style.display = "none";
    }
}
</script>

@endsection