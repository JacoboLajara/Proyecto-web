/**
 * Valida los campos del formulario.
 *
 * Esta función comprueba que:
 * - El campo "nombre" tenga al menos 3 caracteres.
 * - El campo "direccion" tenga al menos 9 caracteres.
 * - El campo "Phone" tenga 9 caracteres.
 * - El campo "poblacion" tenga al menos 2 caracteres.
 * - Se hayan aceptado las condiciones (checkbox "condiciones").
 *
 * Muestra alertas en caso de errores y retorna el estado de validación.
 *
 * @returns {boolean} Retorna 'false' si alguna validación falla; de lo contrario, retorna el valor de la variable "comprobado".
 */
function Validar() {
    var comprobado;

    if (nombre.value.length < 3) { // Valida que el nombre tenga más de dos caracteres
        alert("Escriba mas de dos caracteres para su nombre");
        comprobado = false;
    }

    if (direccion.value.length < 9) { // Valida que la dirección tenga más de 10 caracteres
        alert("La dirección no puede estar vacia\n y ha de tener más de 10 caracteres");
        comprobado = false;
    }
    if (Phone.value.length < 9) { // Valida que el teléfono tenga 9 caracteres
        alert("El Telfeono no puede estar vacio\n y ha de tener 9 caracteres");
        comprobado = false;
    }
    if (poblacion.value.length < 2) { // Recomienda introducir la población si es muy corta
        alert("Sería recomendable que introdujese una población \n para poder tener más datos");
        comprobado = false;
    }
    if (!condiciones.checked) { // Valida que se hayan aceptado las condiciones
        alert("Debe de aceptar las condiciones");
        comprobado = false;
    }

    alert("El formulario ha sido validado"); // Notificación posterior a la validación
    return comprobado;
}

/**
 * Valida el formato de un correo electrónico.
 *
 * Utiliza una expresión regular para comprobar que el valor del campo "mail"
 * tenga el formato correcto (contenga "@" y "."). Si es válido, se muestra una alerta,
 * se enfoca el campo y se actualiza el elemento "valido". Si no, muestra una alerta de error
 * y selecciona el contenido del campo.
 *
 * @param {HTMLInputElement} mail - Elemento de entrada que contiene el correo.
 */
function ValidarMail(mail) {
    var emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;

    if (emailRegex.test(mail.value)) {
        alert("mail valido");
        mail.focus();
        valido.innerText = "válido";
    } else {
        alert("mail no valido, introduza mail formato mail@server.com");
        this.focus();
        mail.select();
    }
}

/**
 * Comprueba si un campo de entrada está vacío y cambia su color de fondo.
 *
 * Si el valor del objeto está vacío, se cambia el fondo a rojo;
 * de lo contrario, se cambia a blanco.
 *
 * @param {HTMLInputElement} obj - Elemento de entrada a comprobar.
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
 * Limpia el contenido del campo de búsqueda y la tabla de resultados.
 *
 * Establece el valor del campo de búsqueda (con id "criterioBusqueda") a vacío
 * y elimina el contenido HTML de la tabla de resultados (con id "tablaResultados").
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
 * Busca registros de alumnos en base a un criterio de búsqueda.
 *
 * Lee el valor del campo de búsqueda, realiza una petición fetch a la URL
 * '/../../controllers/buscarAlumno.php' utilizando el método POST y envía el criterio
 * en formato JSON. Procesa la respuesta en formato JSON, limpia la tabla de resultados y
 * añade filas con la información de cada alumno.
 *
 * @returns {void}
 */
function buscarRegistros() {
    const criterio = document.getElementById('criterioBusqueda').value;

    console.log(`Criterio de búsqueda: ${criterio}`);

    fetch('/../../controllers/buscarAlumno.php', {
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
                    <td>${registro.ID_Alumno}</td>
                    <td>${registro.Nombre}</td>
                    <td>${registro.Apellido1}</td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="seleccionarRegistro('${registro.ID_Alumno}')">
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
 * Selecciona un registro de alumno y rellena los campos del formulario principal.
 *
 * Realiza una petición fetch a la URL generada con el ID del alumno, procesa la respuesta
 * en formato JSON y actualiza los campos del formulario con los datos del alumno. Además,
 * actualiza el enlace para generar la ficha del alumno y cierra el modal de búsqueda.
 *
 * @param {string} id - El ID del alumno a seleccionar.
 * @returns {void}
 */
function seleccionarRegistro(id) {
    const url = `/../../controllers/getAlumnos.php?id=${id}`;
    console.log(`URL generada: ${url}`);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data);

            // Rellenar los campos del formulario principal con los datos del alumno
            document.getElementById('nombre').value = data.Nombre;
            document.getElementById('apellido1').value = data.Apellido1;
            document.getElementById('apellido2').value = data.Apellido2;
            document.getElementById('id_alumno').value = data.ID_Alumno;
            document.getElementById('direccion').value = data.Direccion;
            document.getElementById('poblacion').value = data.Poblacion;
            document.getElementById('provincia').value = data.Provincia;
            document.getElementById('cpostal').value = data.Codigo_Postal;
            document.getElementById('Phone').value = data.Telefono;
            document.getElementById('mail').value = data.Email;
            document.getElementById('fechanac').value = data.Fecha_Nacimiento;
            document.getElementById('fechalta').value = data.Fecha_Alta;
            document.getElementById('estudios').value = data.Nivel_Estudios;

            // Actualizar el enlace para generar la ficha del alumno
            const generarFichaAlumno = document.getElementById("generarFichaAlumno");
            if (generarFichaAlumno) {
                generarFichaAlumno.href = `listados/listadoDetalleAlumno.php?idAlumno=${data.ID_Alumno}`;
                generarFichaAlumno.style.display = "inline-block"; // Mostrar el botón
            }
            // Mostrar botón "Modificar Alumno"
            document.getElementById('modificarAlumnoLi').style.display = "block";
            console.log("✅ Alumno seleccionado. Botón Modificar ahora visible.");
            // Cerrar el modal de búsqueda
            const modalElement = document.getElementById('modalBuscar');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }

            // Eliminar el backdrop del modal y restablecer el scroll
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
        })
        .catch(error => console.error('Error:', error));
}

// Función para deshabilitar todos los campos del formulario
function disableFormFields() {
    const fields = document.querySelectorAll('#formAlumno input, #formAlumno select, #formAlumno textarea');
    fields.forEach(function (field) {
        field.disabled = true;
    });
}

// Función para habilitar todos los campos del formulario
function enableFormFields() {
    const fields = document.querySelectorAll('#formAlumno input, #formAlumno select, #formAlumno textarea');
    fields.forEach(function (field) {
        field.disabled = false;
    });
}

// Funciones para configurar el formulario y desplazar la vista hacia él
function mostrarInsertar() {
    // Reiniciamos el formulario
    document.getElementById('formAlumno').reset();
    // Habilitamos los campos
    enableFormFields();
    // Configuramos los botones: se muestra el de insertar y se oculta el de modificar
    document.getElementById('btnInsertar').style.display = "inline-block";
    document.getElementById('btnModificar').style.display = "none";
    // Actualizamos el título del formulario y el campo oculto "accion"
    document.getElementById('formTitulo').innerText = "Insertar Alumno";
    document.getElementById('accion').value = "insert";
    // Desplazamos la vista hacia el formulario
    document.getElementById('formAlumno').scrollIntoView({ behavior: 'smooth' });
}

function mostrarModificar() {
    // Habilitamos los campos para modificar
    enableFormFields();
    // Configuramos los botones: se muestra el de modificar y se oculta el de insertar
    document.getElementById('btnInsertar').style.display = "none";
    document.getElementById('btnModificar').style.display = "inline-block";
    // Actualizamos el título del formulario y el campo oculto "accion"
    //document.getElementById('formTitulo').innerText = "Modificar Alumno";
    document.getElementById('accion').value = "update";
    // Desplazamos la vista hacia el formulario
    document.getElementById('formAlumno').scrollIntoView({ behavior: 'smooth' });
}

// Al cargar la página, deshabilitamos los campos del formulario
document.addEventListener('DOMContentLoaded', function () {
    disableFormFields();
});
