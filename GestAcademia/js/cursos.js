/**
 * @file script.js
 * @description Este script gestiona la lógica para agregar y guardar módulos, unidades formativas y cursos.
 * Incluye funciones para alternar la visualización de contenedores, añadir filas a tablas,
 * enviar datos al servidor mediante fetch, y actualizar la interfaz de usuario.
 */

/**
 * Contenedor de módulos.
 * @type {HTMLElement}
 */
const modulosContainer = document.getElementById('modulosContainer');

/**
 * Contenedor de unidades formativas.
 * @type {HTMLElement}
 */
const unidadesFormativasContainer = document.getElementById('unidadesFormativasContainer');

/**
 * Cuerpo (tbody) de la tabla de módulos.
 * @type {HTMLElement}
 */
const modulosTable = document.getElementById('modulosTable').querySelector('tbody');

/**
 * Cuerpo (tbody) de la tabla de unidades formativas.
 * @type {HTMLElement}
 */
const unidadesFormativasTable = document.getElementById('unidadesFormativasTable').querySelector('tbody');

/**
 * Almacena el ID del módulo actualmente en edición.
 * @type {string|null}
 */
let currentModuloId = null;

/**
 * Variable para almacenar el ID del curso creado.
 * @type {HTMLElement}
 */
let currentCursoId = document.getElementById('idcurso');

/**
 * Alterna la visibilidad del contenedor de módulos.
 *
 * Muestra u oculta el contenedor de módulos según el estado del checkbox con id "esModular".
 *
 * @returns {void}
 */
function toggleModulosTable() {
    modulosContainer.classList.toggle('hidden', !document.getElementById('esModular').checked);
}

document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("esModular");

    checkbox.addEventListener("change", function () {
        if (this.checked) { 
            alert('⚠️ Recuerde guardar el curso antes de habilitar módulos.');
        }
    });
});


/**
 * Añade una nueva fila para un módulo en la tabla de módulos.
 *
 * Verifica que se haya guardado el curso antes de añadir un módulo. Si no se ha guardado,
 * muestra una alerta.
 *
 * @returns {void}
 */
function addModuloRow() {
    if (!currentCursoId) {
        alert('Por favor, guarde primero el curso antes de añadir módulos.');
        return;
    }

    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="codigoModulo" placeholder="Código (ej. MF4009_3)" required></td>
        <td><input type="text" name="nombreModulo" placeholder="Nombre del módulo" required></td>
        <td><input type="text" name="duracionModulo" placeholder="Duración" required></td>
        <td><input type="checkbox" onchange="toggleUnidadesFormativas(this)"></td>
        <td><button type="button" class="btn btn-success btn-sm" onclick="saveModulo(this)">Guardar</button></td>
    `;

    modulosTable.appendChild(row);
}

/**
 * Guarda los datos de un módulo.
 *
 * Recopila los datos de la fila correspondiente y envía una solicitud POST
 * al endpoint "mainpage.php?route=storeModulo". Si la respuesta contiene HTML inesperado,
 * muestra un error.
 *
 * @param {HTMLElement} button - Botón que invoca la función, para identificar la fila del módulo.
 * @returns {void}
 */
function saveModulo(button) {
    if (!currentCursoId) {
        alert('Por favor, guarde primero el curso antes de añadir módulos.');
        return;
    }
    const row = button.closest('tr');
    const codigo = row.querySelector('[name="codigoModulo"]').value.trim();
    const nombre = row.querySelector('[name="nombreModulo"]').value.trim();
    const duracion = row.querySelector('[name="duracionModulo"]').value.trim();

    // Verifica si algún campo está vacío antes de permitir el guardado
    if (codigo === "" || nombre === "" || duracion === "") {
        alert("⚠️ Todos los campos del módulo son obligatorios.");
        return;
    }
    const modulo = {
        id_curso: currentCursoId,
        codigo: row.querySelector('[name="codigoModulo"]').value,
        nombre: row.querySelector('[name="nombreModulo"]').value,
        duracion: parseInt(row.querySelector('[name="duracionModulo"]').value) || 0,
    };
    console.log("Datos del módulo antes de validar:", modulo);
    console.log("Datos del módulo enviados al servidor:", JSON.stringify(modulo, null, 2));

    fetch('mainpage.php?route=storeModulo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(modulo),
    })
    .then(response => response.text())  // Leer como texto primero
    .then(text => {
        if (text.startsWith('<')) {
            console.error("La respuesta contiene contenido HTML inesperado:", text);
            return;
        }
        console.log("Respuesta cruda del servidor:", text);
        let jsonResponse = JSON.parse(text);
        console.log("Respuesta JSON parseada:", jsonResponse);
    })
    .catch(error => console.error('Error al guardar el módulo:', error));
}

/**
 * Alterna la visualización de las unidades formativas para un módulo.
 *
 * Verifica que se haya ingresado el código del módulo; si no, muestra una alerta y desmarca el checkbox.
 * Si se marca el checkbox, muestra el contenedor de unidades formativas y almacena el ID del módulo actual;
 * si se desmarca, oculta el contenedor y limpia la tabla.
 *
 * @param {HTMLInputElement} checkbox - Checkbox que activa o desactiva las unidades formativas.
 * @returns {void}
 */
function toggleUnidadesFormativas(checkbox) {
    const row = checkbox.closest('tr');
    const moduloId = row.querySelector('[name="codigoModulo"]').value.trim();

    if (!moduloId) {
        alert('Por favor, ingrese el código del módulo antes de añadir unidades formativas. Recuerde guardar el módulo antes de cumplimentar las unidades formativas');
        checkbox.checked = false;  // Desmarcar el checkbox si no hay código
        return;
    }

    if (checkbox.checked) {
        currentModuloId = moduloId;
        unidadesFormativasContainer.classList.remove('hidden');
    } else {
        unidadesFormativasContainer.classList.add('hidden');
        unidadesFormativasTable.innerHTML = '';
        currentModuloId = null;
    }
}

/**
 * Añade una nueva fila para una unidad formativa en la tabla de unidades.
 *
 * Verifica que se haya guardado primero el módulo actual; si no, muestra una alerta.
 *
 * @returns {void}
 */
function addUnidadRow() {
    if (!currentModuloId) {
        alert('Por favor, guarde primero el módulo antes de añadir unidades formativas.');
        return;
    }

    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="codigoUnidad" placeholder="Código" required></td>
        <td><input type="text" name="denominacionUnidad" placeholder="Denominación" required></td>
        <td><input type="text" name="duracionUnidad" placeholder="Duración" required></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">Eliminar</button></td>
    `;
    unidadesFormativasTable.appendChild(row);
}

/**
 * Guarda las unidades formativas para el módulo actual.
 *
 * Recorre cada fila de la tabla de unidades, recopila los datos y verifica su validez.
 * Envía la información en formato JSON al endpoint "mainpage.php?route=storeUnidadFormativa" mediante
 * una petición POST. Si la operación es exitosa, oculta el contenedor y limpia la tabla.
 *
 * @returns {void}
 */
function saveUnidadesFormativas() {
    if (!currentModuloId) {
        alert('Por favor, guarde primero el módulo antes de añadir unidades formativas.');
        return;
    }

    const unidades = [];
    const rows = unidadesFormativasTable.querySelectorAll('tr');
    rows.forEach(row => {
        const codigo = row.querySelector('[name="codigoUnidad"]').value.trim();
        const denominacion = row.querySelector('[name="denominacionUnidad"]').value.trim();
        const duracion = parseInt(row.querySelector('[name="duracionUnidad"]').value, 10);

        if (!codigo || !denominacion || isNaN(duracion) || duracion <= 0) {
            alert('Todos los campos son obligatorios y la duración debe ser un número válido mayor a 0');
            return;
        }

        const unidad = {
            id_modulo: currentModuloId,
            denominacion,
            codigo,
            duracion,
        };
        unidades.push(unidad);
    });

    console.log("Datos de la unidad antes de enviar:", JSON.stringify(unidades, null, 2));
    fetch('mainpage.php?route=storeUnidadFormativa', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(unidades),
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Unidades formativas guardadas correctamente.');
            unidadesFormativasContainer.classList.add('hidden');
            unidadesFormativasTable.innerHTML = '';
        } else {
            alert('Error al guardar las unidades formativas.');
        }
    })
    .catch(error => console.error('Error al guardar las unidades formativas:', error));
}

/**
 * Guarda los datos del curso.
 *
 * Recopila los datos del curso desde el formulario. Si el curso no es "Privado", asigna
 * valores por defecto para el tipo de cuota y precio. Valida que los campos obligatorios estén
 * completos y, si es correcto, envía los datos al servidor mediante una petición POST.
 * Actualiza la variable currentCursoId con el ID del curso creado.
 *
 * @returns {void}
 */
function saveCurso() {
    const curso = {
        nombre: document.getElementById('nombrecurso')?.value.trim() || '',
        codigo: document.getElementById('idcurso')?.value.trim() || '',
        duracion: parseInt(document.getElementById('duracioncurso')?.value) || 0,
        tipo_curso: document.getElementById('tipoCurso')?.value.trim() || 'Oficial',
        tipo_cuota: document.getElementById('tipoCuota')?.value.trim() || '',
        precio: parseFloat(document.getElementById('preciocurso')?.value) || 0.00,
    };

    console.log("Datos del curso antes de validar:", curso);

    // Asignar valores por defecto si el curso no es privado
    if (curso.tipo_curso !== 'Privado') {
        curso.tipo_cuota = 'Gratuito';
        curso.precio = 0.00;
    }

    // Validación de campos obligatorios
    if (!curso.nombre || !curso.codigo || curso.duracion === null || curso.duracion <= 0 || !curso.tipo_curso) {
        alert('Por favor, complete todos los campos obligatorios.');
        return;
    }

    if (curso.tipo_curso === 'Privado' && (!curso.tipo_cuota || curso.precio <= 0)) {
        alert('Para los cursos privados, el tipo de cuota y el precio son obligatorios y deben ser mayores que 0.');
        return;
    }

    console.log("Datos finales enviados al servidor:", JSON.stringify(curso, null, 2));
    console.log('Enviando solicitud...');
    fetch('mainpage.php?route=storeCurso', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(curso),
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Curso guardado correctamente.');
            currentCursoId = result.idcurso;  // Guardar el ID del curso
            console.log('ID del curso guardado:', currentCursoId);
        } else {
            alert(`Error al guardar el curso: ${result.message}`);
        }
    })
    .catch(error => console.error('Error al guardar el curso salta excepcion:', error));
}

/**
 * Elimina una fila de una tabla.
 *
 * Remueve la fila (tr) que contiene el botón que invoca esta función.
 *
 * @param {HTMLElement} button - Botón que invoca la eliminación.
 * @returns {void}
 */
function deleteRow(button) {
    const row = button.closest('tr');
    row.remove();
}

/**
 * Controla la visualización de los campos de precio y tipo de cuota
 * en función del tipo de curso seleccionado.
 *
 * Escucha el evento "change" del elemento select con id "tipoCurso". Si el curso es "Privado",
 * muestra el contenedor de precio; de lo contrario, lo oculta y limpia los campos correspondientes.
 *
 * @returns {void}
 */
document.getElementById("tipoCurso").addEventListener("change", function () {
    var tipoCurso = this.value;
    var precioContainer = document.getElementById("precioContainer");

    if (tipoCurso === "Privado") {
        precioContainer.style.display = "flex";
    } else {
        precioContainer.style.display = "none";
        document.getElementById("tipoCuota").value = "";
        document.getElementById("preciocurso").value = "";
    }
});
