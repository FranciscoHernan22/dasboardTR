<style>

.fila-ejercicio {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.fila-ejercicio select {
    width: 220px;
}

.dropdown-ejercicio {
    width: 350px;
}


.dropdown-ejercicio {
    width: 350px;
    position: relative;
    font-family: Arial;
       display: flex;
    justify-content: flex-end;        
}

.dropdown-btn {
    padding: 8px;
    border: 1px solid #aaa;
    border-radius: 5px;
    background: white;
    cursor: pointer;
}

.dropdown-list {
    position: absolute;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
    z-index: 999; 
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 5px;
    cursor: pointer;
}

.dropdown-item img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 6px;
    margin-right: 10px;
}

.dropdown-item:hover {
    background: #f0f0f0;
}
</style>


<script>
    const ejerciciosPorGrupo = @json(
        $ejercicios->groupBy('segmento')
    );
</script>

<!DOCTYPE html>
<html>
<head>
    <title>Generador de Series</title>
</head>
<body>

<h2>Datos del Entrenamiento</h2>

<!-- FORMULARIO -->
<form id="form-rutina" action="{{ route('guardarRutina') }}" method="POST">
    @csrf

    <!-- ENTRENAMIENTO -->
    <label>Entrenamiento:</label>
    <select name="entrenamiento" required>
        <option value="">-- Selecciona entrenamiento --</option>
        <option value="Fuerza">Fuerza</option>
        <option value="Hipertrofia">Hipertrofia</option>
        <option value="Resistencia">Resistencia</option>
        <option value="Funcional">Funcional</option>
    </select>

    <br><br>

    <!-- SEMANA -->
    <select name="semana" required>
    <option value="">-- Selecciona día --</option>
    <option value="1">Semana 1</option>
    <option value="2">Semana 2</option>
    <option value="3">Semana 3</option>
    <option value="4">Semana 4</option>
</select>

    <br><br>

    <!-- DÍA -->
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

    <!-- SELECT CLIENTE -->
    <label>Seleccionar Cliente:</label>
    <select name="user_id" required>
        <option value="">-- Selecciona un cliente --</option>

        @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
        @endforeach
    </select>

    <hr>

    <!-- CONTENEDOR DE BLOQUES DE SERIES -->
    <div id="contenedor-ejercicios"></div>

    <hr>

    <!-- CONTROLES -->
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

        <!-- BOTÓN FINALIZAR -->
        <button type="submit" id="btn-finalizar" style="display:none;">
            Guardar Rutina
        </button>

    </div>
</form>


<script>
    let contadorGlobal = 0;

    const contenedor = document.getElementById("contenedor-ejercicios");
    const btnFinalizar = document.getElementById("btn-finalizar");
    const selectTipo = document.getElementById("tipo-serie");

    selectTipo.addEventListener("change", function () {

        let tipo = this.value;
        if (tipo === "") return;

        let cantidad = 0;

        switch (tipo) {
            case "monoserie": cantidad = 1; break;
            case "biserie": cantidad = 2; break;
            case "triserie": cantidad = 3; break;
            case "circuito": cantidad = 4; break;
        }

        agregarBloque(tipo, cantidad);

        btnFinalizar.style.display = "inline-block";
        selectTipo.value = ""; 
    });

    function agregarBloque(tipo, cantidad) {

        contadorGlobal++;

        const bloque = document.createElement("div");
        bloque.style.border = "1px solid #ccc";
        bloque.style.padding = "15px";
        bloque.style.marginBottom = "20px";
        bloque.style.borderRadius = "8px";
        bloque.style.background = "#f9f9f9";
        bloque.style.position = "relative";

        const btnQuitar = document.createElement("button");
        btnQuitar.textContent = "Quitar";
        btnQuitar.style.position = "absolute";
        btnQuitar.style.top = "10px";
        btnQuitar.style.right = "10px";
        btnQuitar.style.background = "#ff5252";
        btnQuitar.style.color = "white";
        btnQuitar.style.border = "none";
        btnQuitar.style.padding = "5px 10px";
        btnQuitar.style.cursor = "pointer";
        btnQuitar.style.borderRadius = "5px";

        btnQuitar.addEventListener("click", () => {
            bloque.remove();
            validarBloques();
        });

        bloque.appendChild(btnQuitar);

        const titulo = document.createElement("h3");
        titulo.innerText = tipo.toUpperCase() + " #" + contadorGlobal;
        titulo.style.marginTop = "0";

        bloque.appendChild(titulo);

        for (let i = 0; i < cantidad; i++) {
            bloque.appendChild(crearGrupoEjercicio(i + 1, tipo, contadorGlobal));
        }

        contenedor.appendChild(bloque);
    }
   function crearGrupoEjercicio(num, tipo, idUnico) {
    const div = document.createElement("div");
    div.style.marginBottom = "12px";

    let tipoMayus = tipo.charAt(0).toUpperCase() + tipo.slice(1);
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
    <input type="number" name="rutina[${idUnico}][${num}][reps]" placeholder="Repeticiones" required>
`;


     

    const grupoSelect = div.querySelector(".grupo-muscular");
    const dropdown = div.querySelector(".dropdown-ejercicio");
    const dropdownBtn = dropdown.querySelector(".dropdown-btn");
    const dropdownList = dropdown.querySelector(".dropdown-list");
    const hiddenInput = dropdown.querySelector(".ejercicio-id");

    // CUANDO SE SELECCIONA GRUPO MUSCULAR
    grupoSelect.addEventListener("change", function () {
        const grupo = this.value;
        dropdownList.innerHTML = "";

        if (!grupo) {
            dropdown.style.display = "none";
            return;
        }

        dropdown.style.display = "block";

        const lista = ejerciciosPorGrupo[grupo];

        lista.forEach(e => {
            const item = document.createElement("div");
            item.classList.add("dropdown-item");

console.log(e.imagen);


            item.innerHTML = `
            <img src="https://res.cloudinary.com/ddls3oqbe/image/upload/${e.imagen}"  >
 
                <span>${e.nombre}</span>
            `;

            item.addEventListener("click", () => {
                dropdownBtn.innerHTML = `
                     ${e.nombre}
                `;
                hiddenInput.value = e.id;
                dropdownList.style.display = "none";
            });

            dropdownList.appendChild(item);
        });
    });

    // ABRIR/CERRAR LISTA
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

</body>
</html>
