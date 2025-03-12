/**
 * Valida el formulario comprobando que los campos cumplan los requisitos mínimos.
 *
 * Verifica que:
 * - El campo "nombre" tenga al menos 3 caracteres.
 * - Se haya marcado la casilla de "condiciones".
 *
 * Muestra alertas en caso de error y retorna el estado de validación.
 *
 * @returns {boolean} Retorna false si alguna validación falla; de lo contrario, retorna el valor de "comprobado".
 */
function Validar() {
    var comprobado;

    if (nombre.value.length < 3) { // Valida el campo "nombre" para que tenga más de dos caracteres
        alert("Escriba mas de dos caracteres para su nombre");
        comprobado = false;
    }

    if (!condiciones.checked) { // Valida que la casilla de "condiciones" esté marcada
        alert("Debe de aceptar las condiciones");
        comprobado = false;
    }

    alert("El formulario ha sido validado"); // Notificación de validación
    return comprobado;
}

/**
 * Comprueba si un campo está vacío y cambia su color de fondo.
 *
 * Si el valor del objeto es vacío, establece el fondo a rojo; de lo contrario, lo cambia a blanco.
 *
 * @param {HTMLElement} obj - Elemento HTML a comprobar.
 * @returns {void}
 */
function comprueba(obj) {
    if (obj.value == '') {
        if (document.getElementById) {
            obj.style.backgroundColor = "red";
        }
    } else {
        if (document.getElementById) {
            obj.style.backgroundColor = "white";
        }
    }
}

/**
 * Reinicia el campo de búsqueda y limpia los resultados anteriores.
 *
 * Establece el valor del elemento con id "criterioBusqueda" a vacío y vacía el contenido
 * de la tabla con id "tablaResultados".
 *
 * @returns {void}
 */
function prellenarModal() {
    // Limpiar campo de búsqueda
    document.getElementById('criterioBusqueda').value = '';

    // Limpiar resultados anteriores
    const tabla = document.getElementById('tablaResultados');
    tabla.innerHTML = '';
}

/**
 * Realiza una búsqueda de registros (aulas) en base a un criterio.
 *
 * Envía una petición fetch al endpoint '../../controllers/buscarAulas.php' usando el método POST
 * y enviando el criterio de búsqueda en formato JSON. Procesa la respuesta y actualiza la tabla de resultados.
 *
 * @returns {void}
 */
function buscarRegistros() {
    const criterio = document.getElementById('criterioBusqueda').value;

    console.log(`Criterio de búsqueda: ${criterio}`);

    fetch('../../controllers/buscarAulas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ criterio })
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        const tabla = document.getElementById('tablaResultados');
        tabla.innerHTML = ''; // Limpiar resultados previos

        data.forEach(registro => {
            console.log('Registro:', registro);
            const fila = `
                <tr>
                    <td>${registro.ID_Aula}</td>
                    <td>${registro.Nombre}</td>
                    <td>${registro.Capacidad}</td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="seleccionarRegistro('${registro.ID_Aula}')">
                            Seleccionar
                        </button>
                    </td>
                </tr>
            `;
            console.log('Estado del modal:', bootstrap.Modal.getInstance(document.getElementById('modalBuscar')));
            console.log('Registro:', registro);
            tabla.innerHTML += fila;
        });
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        alert(`Error: ${error.message}`);
    });
}

/**
 * Selecciona un registro (aula) y rellena los campos del formulario principal.
 *
 * Realiza una petición fetch al endpoint '../../controllers/getAula.php' pasando el ID del aula,
 * procesa la respuesta en formato JSON y actualiza los campos del formulario. Además, cierra el modal
 * de búsqueda y restablece la interfaz.
 *
 * @param {string} id - El ID del aula seleccionada.
 * @returns {void}
 */
function seleccionarRegistro(id) {
    const url = `../../controllers/getAula.php?id=${id}`;
    console.log(`URL generada: ${url}`);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data);

            // Rellenar los campos del formulario principal
            document.getElementById('id_aula').value = data.ID_Aula;
            document.getElementById('nombre').value = data.Nombre;
            document.getElementById('capacidad').value = data.Capacidad;

              // Mostrar botón "Modificar Aulas"
              document.getElementById('modificarAulaLi').style.display = "block";
              console.log("✅ Alumno seleccionado. Botón Modificar ahora visible.");

            // Cerrar el modal de búsqueda
            const modalElement = document.getElementById('modalBuscar');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) {
                modalInstance.hide();
            }

            // Eliminar el backdrop y restablecer el scroll
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto'; // Habilitar el scroll
        })
        .catch(error => console.error('Error:', error));
}

/**
 * Deshabilita todos los campos del formulario de aulas.
 *
 * Selecciona todos los elementos input, select y textarea dentro de un fieldset
 * del formulario con id "formAulas" y los deshabilita.
 *
 * @returns {void}
 */
function disableFormFieldsAulas() {
    const fields = document.querySelectorAll('#formAulas fieldset input, #formAulas fieldset select, #formAulas fieldset textarea');

    fields.forEach(function (field) {
        field.disabled = true;
    });
}

/**
 * Habilita todos los campos del formulario de aulas.
 *
 * Selecciona todos los elementos input, select y textarea dentro del formulario con id "formAulas"
 * y los habilita.
 *
 * @returns {void}
 */
function enableFormFieldsAulas() {
    const fields = document.querySelectorAll('#formAulas input, #formAulas select, #formAulas textarea');
    fields.forEach(function (field) {
        field.disabled = false;
    });
}

/**
 * Configura el formulario de aulas para insertar un nuevo registro.
 *
 * Reinicia el formulario, habilita los campos, muestra el botón de insertar y oculta el de modificar,
 * actualiza el título del formulario y el campo oculto "accion", y desplaza la vista hacia el formulario.
 *
 * @returns {void}
 */
function mostrarInsertarAulas() {
    // Reiniciar el formulario
    document.getElementById('formAulas').reset();
    // Habilitar los campos
    enableFormFieldsAulas();
    // Configurar botones: mostrar "Insertar", ocultar "Modificar"
    document.getElementById('btnInsertar').style.display = "inline-block";
    document.getElementById('btnModificar').style.display = "none";
    // Actualizar título del formulario y campo oculto "accion"
    document.getElementById('formTitulo').innerText = "Insertar Aula";
    document.getElementById('accion').value = "insert";
    // Desplazar la vista hacia el formulario
    document.getElementById('formAulas').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Configura el formulario de aulas para modificar un registro existente.
 *
 * Habilita los campos del formulario, muestra el botón de modificar y oculta el de insertar,
 * actualiza el título del formulario y el campo oculto "accion", y desplaza la vista hacia el formulario.
 *
 * @returns {void}
 */
function mostrarModificarAulas() {
    // Habilitar campos para modificar
    enableFormFieldsAulas();
    // Configurar botones: mostrar "Modificar", ocultar "Insertar"
    document.getElementById('btnInsertar').style.display = "none";
    document.getElementById('btnModificar').style.display = "inline-block";
    // Actualizar título del formulario y campo oculto "accion"
    document.getElementById('formTitulo').innerText = "Modificar Aulas";
    document.getElementById('accion').value = "update";
    // Desplazar la vista hacia el formulario
    document.getElementById('formAulas').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Evento que se ejecuta al cargar la página.
 *
 * Deshabilita los campos del formulario de aulas cuando el DOM está completamente cargado.
 *
 * @returns {void}
 */
document.addEventListener('DOMContentLoaded', function () {
    disableFormFieldsAulas();
});
