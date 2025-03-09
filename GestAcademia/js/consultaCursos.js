/**
 * Evento que se ejecuta cuando el DOM se ha cargado completamente.
 * Asigna el evento "change" al elemento select con id "cursoSelect" para llamar a la función cargarCurso.
 *
 * @event DOMContentLoaded
 */
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("cursoSelect").addEventListener("change", cargarCurso);
});

/**
 * Carga los detalles del curso seleccionado.
 *
 * Obtiene el ID del curso del select "cursoSelect". Si no se selecciona ningún curso,
 * limpia el contenido del área de detalles y oculta los botones de edición. Si se ha seleccionado un curso,
 * realiza una petición fetch al endpoint correspondiente para obtener módulos y unidades formativas.
 * Dependiendo de la respuesta, muestra los detalles del curso o un mensaje de error.
 *
 * @returns {void}
 */
function cargarCurso() {
    const cursoId = document.getElementById("cursoSelect").value;
    console.log("ID del curso seleccionado:", cursoId);
    
    if (!cursoId) {
        document.getElementById("detalleCurso").innerHTML = "";
        document.getElementById("editarCursoBtn").style.display = "none";
        document.getElementById("guardarCambiosBtn").style.display = "none";
        return;
    }

    fetch(`router.php?route=getModulosYUnidadesPorCurso&id_curso=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del servidor:", data);
            if (data.success) {
                mostrarDetallesCurso(data);
                document.getElementById("editarCursoBtn").style.display = "inline-block";
                document.getElementById("guardarCambiosBtn").style.display = "none";
            } else {
                document.getElementById("detalleCurso").innerHTML = "<p>No se encontraron módulos ni unidades formativas para este curso.</p>";
                document.getElementById("editarCursoBtn").style.display = "none";
                document.getElementById("guardarCambiosBtn").style.display = "none";
            }
        })
        .catch(error => console.error("Error al cargar los datos del curso:", error));
}

/**
 * Guarda los cambios realizados en los módulos y unidades formativas del curso.
 *
 * Recorre cada módulo mostrado en la vista, recopilando sus datos y los de sus unidades,
 * y envía esta información en formato JSON mediante una petición POST al endpoint "router.php?route=updateCurso".
 * Si la actualización es exitosa, notifica al usuario y bloquea los campos del formulario.
 *
 * @returns {void}
 */
function guardarCambios() {
    const cursoId = document.getElementById("cursoSelect").value;
    if (!cursoId) {
        alert("Selecciona un curso antes de guardar.");
        return;
    }

    let modulos = [];
    document.querySelectorAll("#detalleCurso .modulo-row").forEach(modulo => {
        let moduloData = {
            id_modulo: modulo.querySelector(".idModulo").value,
            nombre: modulo.querySelector(".nombreModulo").value,
            duracion: parseInt(modulo.querySelector(".duracionModulo").value),
            unidades: []
        };

        modulo.querySelectorAll(".unidad-row").forEach(unidad => {
            moduloData.unidades.push({
                id_unidad: unidad.querySelector(".idUnidad").value,
                nombre: unidad.querySelector(".nombreUnidad").value,
                duracion: parseInt(unidad.querySelector(".duracionUnidad").value)
            });
        });

        modulos.push(moduloData);
    });
    console.log("Datos enviados al servidor:", JSON.stringify({ id_curso: cursoId, modulos }, null, 2));

    fetch("router.php?route=updateCurso", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_curso: cursoId, modulos })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Cambios guardados correctamente.");
                document.getElementById("editarCursoBtn").style.display = "inline-block";
                document.getElementById("guardarCambiosBtn").style.display = "none";
                document.querySelectorAll("#detalleCurso input").forEach(el => {
                    el.setAttribute("readonly", true);
                });
            } else {
                alert("Error al guardar los cambios: " + result.message);
            }
        })
        .catch(error => console.error("Error al actualizar curso:", error));
}

/**
 * Muestra los detalles del curso, incluyendo módulos y unidades formativas.
 *
 * Genera dinámicamente el HTML para visualizar los módulos y sus unidades a partir de los datos
 * recibidos y lo inserta en el elemento con id "detalleCurso".
 *
 * @param {Object} data - Objeto que contiene los detalles del curso.
 * @returns {void}
 */
function mostrarDetallesCurso(data) {
    let html = `<h3>Detalles del Curso</h3>
                <button class='btn btn-primary btn-sm' onclick='agregarModulo()'>Añadir Módulo</button>`;

    data.modulos.forEach(modulo => {
        html += `<div class='card mt-3 modulo-row' data-id-modulo='${modulo.ID_Modulo}'>
                    <div class='card-header d-flex justify-content-between'>
                        <span><b>Módulo:</b> 
                        <input type='text' class='idModulo form-control' value='${modulo.ID_Modulo}' readonly>
                        <input type='text' class='nombreModulo form-control' value='${modulo.Nombre}' readonly></span>
                        <button class='btn btn-danger btn-sm' onclick='eliminarModulo(this)'>Eliminar Módulo</button>
                    </div>
                    <div class='card-body'>
                        <p><b>Duración:</b> 
                        <input type='text' class='duracionModulo form-control' value='${modulo.Duracion_Horas}' readonly> horas</p>
                        <h5>Unidades Formativas:</h5>
                        <ul>`;
        modulo.unidades.forEach(unidad => {
            html += `<li class='unidad-row d-flex align-items-center' data-id-unidad='${unidad.ID_Unidad_Formativa}'>
                        <b class='me-2'>Unidad:</b> 
                        <input type='text' class='idUnidad form-control w-25' value='${unidad.ID_Unidad_Formativa}' readonly>
                        <input type='text' class='nombreUnidad form-control flex-grow-1 me-2' value='${unidad.Nombre}' readonly>
                        <b class='me-2'>Duración:</b> 
                        <input type='text' class='duracionUnidad form-control w-25' value='${unidad.Duracion_Unidad}' readonly> horas
                        <button class='btn btn-danger btn-sm ms-2' onclick='eliminarUnidad(this)'>X</button>
                    </li>`;
        });
        html += `</ul>
                 <button class='btn btn-primary btn-sm mt-2' onclick='agregarUnidad(this)'>Añadir Unidad</button>
                 </div></div>`;
    });

    document.getElementById("detalleCurso").innerHTML = html;
}

/**
 * Agrega una nueva unidad formativa al módulo.
 *
 * Crea dinámicamente un nuevo elemento <li> que contiene campos de entrada para el ID, nombre y duración
 * de la unidad, y lo añade al contenedor de unidades (una lista <ul>) dentro del módulo.
 *
 * @param {HTMLElement} button - El botón que invoca la función (usado para identificar el módulo correspondiente).
 * @returns {void}
 */
function agregarUnidad(button) {
    const moduloContainer = button.closest(".card-body").querySelector("ul");
    const nuevaUnidad = document.createElement("li");
    nuevaUnidad.classList.add("unidad-row", "d-flex", "align-items-center");
    nuevaUnidad.innerHTML = `
        <b class='me-2'>Unidad:</b> 
        <input type='text' class='idUnidad form-control w-25' placeholder='ID Unidad'>
        <input type='text' class='nombreUnidad form-control flex-grow-1 me-2' placeholder='Nueva Unidad'>
        <b class='me-2'>Duración:</b> 
        <input type='text' class='duracionUnidad form-control w-25' placeholder='Horas'> horas
        <button class='btn btn-danger btn-sm ms-2' onclick='eliminarUnidad(this)'>X</button>
    `;
    moduloContainer.appendChild(nuevaUnidad);
}

/**
 * Habilita la edición de los campos del formulario de curso.
 *
 * Elimina el atributo "readonly" de todos los campos de entrada dentro del área de detalles del curso,
 * y actualiza la visualización de los botones de edición.
 *
 * @returns {void}
 */
function habilitarEdicion() {
    document.querySelectorAll("#detalleCurso input").forEach(el => {
        el.removeAttribute("readonly");
        el.classList.add("form-control");
    });

    document.getElementById("editarCursoBtn").style.display = "none";
    document.getElementById("guardarCambiosBtn").style.display = "inline-block";
}

/**
 * Elimina un módulo del curso.
 *
 * Muestra un mensaje de confirmación. Si el usuario confirma, elimina el módulo (y sus unidades)
 * del DOM y llama a la función guardarCambios para persistir los cambios.
 *
 * @param {HTMLElement} button - El botón que invoca la eliminación.
 * @returns {void}
 */
function eliminarModulo(button) {
    if (confirm("¿Seguro que deseas eliminar este módulo y todas sus unidades formativas?")) {
        button.closest(".modulo-row").remove();
        guardarCambios();
    }
}

/**
 * Elimina una unidad formativa del módulo.
 *
 * Remueve el elemento que contiene la unidad del DOM y llama a la función guardarCambios para actualizar los datos.
 *
 * @param {HTMLElement} button - El botón que invoca la eliminación.
 * @returns {void}
 */
function eliminarUnidad(button) {
    button.closest(".unidad-row").remove();
    guardarCambios();
}

/**
 * Agrega un nuevo módulo al curso.
 *
 * Crea dinámicamente un nuevo elemento <div> que representa un módulo con sus campos de entrada para ID, nombre,
 * duración y un contenedor para unidades formativas, y lo añade al contenedor de detalles del curso.
 *
 * @returns {void}
 */
function agregarModulo() {
    const cursoContainer = document.getElementById("detalleCurso");
    const nuevoModulo = document.createElement("div");
    nuevoModulo.classList.add("card", "mt-3", "modulo-row");
    nuevoModulo.innerHTML = `
        <div class='card-header d-flex justify-content-between'>
            <span><b>Módulo:</b>
            <input type='text' class='idModulo form-control' placeholder='ID Módulo'>
            <input type='text' class='nombreModulo form-control' placeholder='Nombre del Módulo'></span>
            <button class='btn btn-danger btn-sm' onclick='eliminarModulo(this)'>Eliminar</button>
        </div>
        <div class='card-body'>
            <p><b>Duración:</b> <input type='text' class='duracionModulo form-control' placeholder='Horas'> horas</p>
            <h5>Unidades Formativas:</h5>
            <ul></ul>
            <button class='btn btn-primary btn-sm mt-2' onclick='agregarUnidad(this)'>Añadir Unidad</button>
        </div>`;
    cursoContainer.appendChild(nuevoModulo);
}
