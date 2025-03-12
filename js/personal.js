/**
 * Valida el formulario de personal.
 *
 * Verifica que el campo "nombre" tenga al menos 3 caracteres, que la dirección tenga más de 9 caracteres,
 * que el teléfono tenga 9 caracteres, que la población tenga al menos 2 caracteres y que se hayan aceptado las condiciones.
 * Muestra alertas en caso de error y retorna el estado de validación.
 *
 * @returns {boolean} Retorna false si alguna validación falla; de lo contrario, retorna el valor de "comprobado".
 */
function Validar() {
    var comprobado;

    if (nombre.value.length < 3) { // Valida que el campo nombre tenga más de dos caracteres
        alert("Escriba mas de dos caracteres para su nombre");
        comprobado = false;
    }

    if (direccion.value.length < 9) { // Valida que el campo dirección tenga más de 10 caracteres
        alert("La dirección no puede estar vacia\n y ha de tener más de 10 caracteres");
        comprobado = false;
    }
    if (Phone.value.length < 9) { // Valida que el campo teléfono tenga 9 caracteres
        alert("El Telfeono no puede estar vacio\n y ha de tener 9 caracteres");
        comprobado = false;
    }
    if (poblacion.value.length < 2) { // Recomienda introducir población si es muy corta
        alert("Sería recomendable que introdujese una población \n para poder tener más datos");
        comprobado = false;
    }
    if (!condiciones.checked) { // Valida que se haya aceptado las condiciones
        alert("Debe de aceptar las condiciones");
        comprobado = false;
    }

    alert("El formulario ha sido validado");
    return comprobado;
}

/**
 * Valida el formato de un correo electrónico.
 *
 * Utiliza una expresión regular para comprobar que el valor del campo "mail"
 * tenga el formato correcto. Si el correo es válido, muestra una alerta, enfoca el campo y actualiza el texto
 * del elemento "valido". Si no, muestra una alerta de error y selecciona el contenido.
 *
 * @param {HTMLInputElement} mail - Elemento de entrada del correo electrónico.
 * @returns {void}
 */
function ValidarMail(mail) {
    var emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;

    if (emailRegex.test(mail.value)) {
        alert("mail valido");
        mail.focus();
        valido.innerText = "válido";
    }
    else {
        alert("mail no valido, introduza mail formato mail@server.com");
        this.focus();
        mail.select();
    }
}

/**
 * Cambia el color de fondo de un campo según si está vacío o no.
 *
 * Si el valor del objeto es vacío, cambia el fondo a rojo; en caso contrario, lo cambia a blanco.
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
 * Limpia el campo de búsqueda y los resultados anteriores.
 *
 * Establece el valor del campo con id "criterioBusqueda" a vacío y limpia el contenido de la tabla con id "tablaResultados".
 *
 * @returns {void}
 */
function prellenarModal() {
    document.getElementById('criterioBusqueda').value = '';
    const tabla = document.getElementById('tablaResultados');
    tabla.innerHTML = '';
}

/**
 * Realiza una búsqueda de registros de personal.
 *
 * Envía una petición fetch al endpoint '/../../controllers/buscarPersonal.php' mediante el método POST,
 * enviando el criterio de búsqueda en formato JSON. Procesa la respuesta y actualiza la tabla de resultados.
 *
 * @returns {void}
 */
function buscarRegistros() {
    const criterio = document.getElementById('criterioBusqueda').value.trim();
    console.log(`Criterio de búsqueda: ${criterio}`);

    fetch('/../../controllers/buscarPersonal.php', {
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
        tabla.innerHTML = '';
        if (data.length === 0) {
            alert("No se encontraron registros de personal.");
        } else {
            data.forEach(registro => {
                console.log('Registro:', registro);
                const fila = `
                <tr>
                    <td>${registro.ID_Personal}</td>
                    <td>${registro.Nombre}</td>
                    <td>${registro.Apellido1}</td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="seleccionarRegistro('${registro.ID_Personal}')">
                            Seleccionar
                        </button>
                    </td>
                </tr>`;
                tabla.innerHTML += fila;
            });
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        alert(`Error: ${error.message}`);
    });
}

/**
 * Selecciona un registro de personal y rellena los campos del formulario principal.
 *
 * Realiza una petición fetch al endpoint '/../../controllers/getPersonal.php' utilizando el ID proporcionado.
 * Procesa la respuesta en formato JSON y actualiza los campos del formulario. Además, cierra el modal de búsqueda
 * y actualiza el enlace para ver la ficha del personal.
 *
 * @param {string} id - El ID del registro de personal seleccionado.
 * @returns {void}
 */
function seleccionarRegistro(id) {
    const url = `/../../controllers/getPersonal.php?id=${id}`;
    console.log(`URL generada para personal: ${url}`);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data);
            document.getElementById('nombre').value = data.Nombre;
            document.getElementById('apellido1').value = data.Apellido1;
            document.getElementById('apellido2').value = data.Apellido2;
            document.getElementById('id_personal').value = data.ID_Personal;
            document.getElementById('direccion').value = data.Direccion;
            document.getElementById('poblacion').value = data.Poblacion;
            document.getElementById('provincia').value = data.Provincia;
            document.getElementById('cpostal').value = data.Codigo_Postal;
            document.getElementById('Phone').value = data.Telefono;
            document.getElementById('mail').value = data.Email;
            document.getElementById('fechanac').value = data.Fecha_Nacimiento;
            document.getElementById('fechalta').value = data.Fecha_Alta;
            document.getElementById('estudios').value = data.Nivel_Estudios;

            // Cerrar el modal de búsqueda
            const modalElement = document.getElementById('modalBuscar');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }

            // Eliminar el backdrop y restablecer el scroll
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';

            // Actualizar el enlace en la sidebar para ver la ficha del personal
            const enlaceFicha = document.getElementById('verFichaPersonal');
            if (enlaceFicha) {
                enlaceFicha.href = `/listados/listadoDetalleDelPersonal.php?id=${data.ID_Personal}`;
                enlaceFicha.style.display = "block";
            }

            // Mostrar botón "Modificar Alumno"
            document.getElementById('modificarPersonalLi').style.display = "block";
            console.log("✅ Alumno seleccionado. Botón Modificar ahora visible.");
        })
        .catch(error => console.error('Error:', error));
}

/**
 * Deshabilita todos los campos del formulario de personal.
 *
 * Selecciona todos los inputs, selects y textareas dentro de un fieldset del formulario con id "formPersonal"
 * y los deshabilita.
 *
 * @returns {void}
 */
function disableFormFieldsPersonal() {
    const fields = document.querySelectorAll('#formPersonal fieldset input, #formPersonal fieldset select, #formPersonal fieldset textarea');
    fields.forEach(function (field) {
        field.disabled = true;
    });
}

/**
 * Habilita todos los campos del formulario de personal.
 *
 * Selecciona todos los inputs, selects y textareas dentro del formulario con id "formPersonal"
 * y los habilita.
 *
 * @returns {void}
 */
function enableFormFieldsPersonal() {
    const fields = document.querySelectorAll('#formPersonal input, #formPersonal select, #formPersonal textarea');
    fields.forEach(function (field) {
        field.disabled = false;
    });
}

/**
 * Configura el formulario de personal para insertar un nuevo registro.
 *
 * Reinicia el formulario, habilita los campos, muestra el botón de insertar y oculta el de modificar,
 * actualiza el título del formulario y el campo oculto "accion", y desplaza la vista hacia el formulario.
 *
 * @returns {void}
 */
function mostrarInsertarPersonal() {
    document.getElementById('formPersonal').reset();
    enableFormFieldsPersonal();
    document.getElementById('btnInsertar').style.display = "inline-block";
    document.getElementById('btnModificar').style.display = "none";
    document.getElementById('formTitulo').innerText = "Insertar Personal";
    document.getElementById('accion').value = "insert";
    document.getElementById('formPersonal').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Configura el formulario de personal para modificar un registro existente.
 *
 * Habilita los campos del formulario, muestra el botón de modificar y oculta el de insertar,
 * actualiza el título del formulario y el campo oculto "accion", y desplaza la vista hacia el formulario.
 *
 * @returns {void}
 */
function mostrarModificarPersonal() {
    enableFormFieldsPersonal();
    document.getElementById('btnInsertar').style.display = "none";
    document.getElementById('btnModificar').style.display = "inline-block";
    document.getElementById('formTitulo').innerText = "Modificar Personal no docente";
    document.getElementById('accion').value = "update";
    document.getElementById('formPersonal').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Al cargar la página, deshabilita los campos del formulario de personal.
 *
 * Este evento se ejecuta cuando el DOM está completamente cargado y llama a la función
 * disableFormFieldsPersonal.
 *
 * @event DOMContentLoaded
 */
document.addEventListener('DOMContentLoaded', function () {
    disableFormFieldsPersonal();
});
