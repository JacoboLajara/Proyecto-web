/**
 * Valida el formulario comprobando que los campos cumplen ciertas condiciones.
 * Se verifica que:
 * - El nombre tenga más de dos caracteres.
 * - La dirección tenga más de 10 caracteres.
 * - El teléfono tenga 9 caracteres.
 * - Se proporcione una población (aunque solo sea recomendable).
 * - Se hayan aceptado las condiciones.
 *
 * @returns {boolean} Devuelve `true` si el formulario es válido; en caso contrario, `false`.
 */
function Validar() { // Función para validar el formulario una vez esté relleno
    var comprobado;

    if (nombre.value.length < 3) { // Valida que el campo nombre tenga más de dos caracteres
        alert("Escriba mas de dos caracteres para su nombre");
        comprobado = false;
    }

    if (direccion.value.length < 9) { // Valida que el campo dirección tenga más de 10 caracteres
        alert("La dirección  no puede estar vacia\n y ha de tener más de 10 caracteres");
        comprobado = false;
    }

    if (Phone.value.length < 9) { // Valida que el campo teléfono tenga 9 caracteres
        alert("El Telfeono no puede estar vacio\n y ha de tener 9 caracteres");
        comprobado = false;
    }

    if (poblacion.value.length < 2) { // Recomienda introducir una población para tener más datos
        alert("Sería recomendable que introdujese una población \n para poder tener más datos");
        comprobado = false;
    }

    if (!condiciones.checked) { // Valida que se hayan aceptado las condiciones
        alert("Debe de aceptar las condiciones")
        comprobado = false;
    }

    alert("El formulario ha sido validado"); // Comprobación a posteriori
    return comprobado;
}


/**
 * Valida el email comprobando que cumple con el formato estándar.
 * Se utiliza una expresión regular para asegurar que el email contiene al menos un "@" y un ".".
 *
 * @param {HTMLInputElement} mail - Elemento input que contiene el correo a validar.
 */
function ValidarMail(mail) { // Valida el mail comprobando que lleve "." y "@"
    var emailRegex;

    emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;

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
 * Comprueba si un campo de formulario está vacío y cambia su color de fondo.
 * Si el campo está vacío, se resalta en rojo; si no, se muestra en blanco.
 *
 * @param {HTMLInputElement} obj - Elemento input a comprobar.
 */
function comprueba(obj) { // Función que comprueba que el objeto esté o no vacío y lo cambia de color
    if (obj.value == '') {
        if ((document.getElementById)) {
            obj.style.backgroundColor = "red";
        }
    } else {
        if ((document.getElementById)) {
            obj.style.backgroundColor = "white";
        }
    }
}


/**
 * Prepara el modal para realizar una nueva búsqueda.
 * Limpia el campo de búsqueda, elimina resultados anteriores y cierra cualquier backdrop o modal que pudiera interferir.
 */
function prellenarModal() {
    // Limpiar el campo de búsqueda
    document.getElementById('criterioBusqueda').value = '';

    // Limpiar resultados anteriores
    const tabla = document.getElementById('tablaResultados');
    tabla.innerHTML = '';

    // ✅ Asegurar que se eliminen posibles backdrop anteriores
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
}


/**
 * Busca registros de profesores según el criterio de búsqueda ingresado.
 * Realiza una solicitud POST al servidor y muestra los resultados en una tabla.
 */
function buscarRegistros() {
    const criterio = document.getElementById('criterioBusqueda').value.trim();

    console.log(`Criterio de búsqueda: ${criterio}`);

    fetch('/../../controllers/buscarProfesor.php', {
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

            if (data.length === 0) {
                alert("No se encontraron registros de profesores."); // Muestra alerta si no hay registros
            } else {
                data.forEach(registro => {
                    console.log('Registro:', registro);
                    const fila = `
                <tr>
                    <td>${registro.ID_Profesor}</td>
                    <td>${registro.Nombre}</td>
                    <td>${registro.Apellido1}</td>
                    <td>${registro.Apellido2}</td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="seleccionarRegistro('${registro.ID_Profesor}')">
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
 * Selecciona un registro de profesor y rellena el formulario principal con sus datos.
 * También actualiza el enlace para ver la ficha completa del profesor y cierra el modal de búsqueda.
 *
 * @param {string} id - Identificador del profesor a seleccionar.
 */
function seleccionarRegistro(id) {
    console.log(`🔍 Buscando profesor con ID: ${id}`);

    const url = `/../../controllers/getProfesores.php?id=${id}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('🟢 Datos recibidos:', data);

            if (!data.ID_Profesor) {
                console.error("❌ Error: No se encontró el ID_Profesor en la respuesta.");
                return;
            }

            // Rellenar los campos con los datos del profesor
            document.getElementById('nombre').value = data.Nombre;
            document.getElementById('apellido1').value = data.Apellido1;
            document.getElementById('apellido2').value = data.Apellido2;
            document.getElementById('id_profesor').value = data.ID_Profesor;
            document.getElementById('direccion').value = data.Direccion;
            document.getElementById('poblacion').value = data.Poblacion;
            document.getElementById('provincia').value = data.Provincia;
            document.getElementById('cpostal').value = data.Codigo_Postal;
            document.getElementById('Phone').value = data.Telefono;
            document.getElementById('mail').value = data.Email;
            document.getElementById('fechanac').value = data.Fecha_Nacimiento;
            document.getElementById('fechalta').value = data.Fecha_Alta;
            document.getElementById('estudios').value = data.Nivel_Estudios;

            // Habilitar los campos del formulario
            const fields = document.querySelectorAll('#formProfesor input, #formProfesor select, #formProfesor textarea');
            fields.forEach(field => {
                field.disabled = false;
            });

            // Mostrar botón "Modificar Profesor"
            document.getElementById('modificarProfesorLi').style.display = "block";
            console.log("✅ Profesor seleccionado. Botón Modificar ahora visible.");

            // 📌 Mostrar la opción "Ficha Detalle Profesor"
            const enlaceFicha = document.getElementById('verFichaProfesor');
            if (enlaceFicha) {
                enlaceFicha.href = `/listados/listadoDetalleProfesores.php?id=${data.ID_Profesor}`;
                enlaceFicha.style.display = "block"; // Asegurar que se muestra
                console.log(`✅ Enlace actualizado: ${enlaceFicha.href}`);
            } else {
                console.error("❌ Error: No se encontró el elemento 'verFichaProfesor'.");
            }
            // Cerrar el modal de búsqueda
            const modalElement = document.getElementById('modalBuscar');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
        })
        .catch(error => console.error('❌ Error en la solicitud:', error));
}


/**
 * Deshabilita todos los campos del formulario de profesores.
 * Selecciona todos los inputs, selects y textareas dentro del fieldset del formulario y los desactiva.
 */
function disableFormFieldsProf() {
    console.log("🔒 Deshabilitando todos los campos y ocultando botón Modificar");

    const fields = document.querySelectorAll('#formProfesor fieldset input, #formProfesor fieldset select, #formProfesor fieldset textarea');
    fields.forEach(field => {
        field.disabled = true;
    });

    // Ocultar botón "Modificar Profesor"
    document.getElementById('modificarProfesorLi').style.display = 'none';
}





/**
 * Habilita todos los campos del formulario de profesores.
 * Permite que el usuario edite los inputs, selects y textareas del formulario.
 */
function enableFormFieldsProf() {
    const fields = document.querySelectorAll('#formProfesor input, #formProfesor select, #formProfesor textarea');
    fields.forEach(function (field) {
        field.disabled = false;
    });
}


/**
 * Configura el formulario para insertar un nuevo profesor.
 * Realiza lo siguiente:
 * - Reinicia el formulario.
 * - Habilita los campos para poder editar.
 * - Muestra el botón de insertar y oculta el de modificar.
 * - Actualiza el título del formulario y el valor oculto "accion".
 * - Desplaza la vista hacia el formulario.
 */
function mostrarInsertarProf() {
    console.log("🟢 Modo Insertar: Habilitando campos");

    // Habilitamos los campos para permitir inserción
    const fields = document.querySelectorAll('#formProfesor input, #formProfesor select, #formProfesor textarea');
    fields.forEach(field => {
        field.disabled = false;
    });

    // Configurar botones: mostrar "Insertar", ocultar "Modificar"
    document.getElementById('btnInsertar').style.display = "inline-block";
    document.getElementById('btnModificar').style.display = "none";

    // Asegurar que "Modificar Profesor" sigue oculto
    document.getElementById('modificarProfesorLi').style.display = "none";

    // Actualizar título del formulario y el campo oculto "accion"
    document.getElementById('formTitulo').innerText = "Insertar Profesor";
    document.getElementById('accion').value = "insert";
}



/**
 * Configura el formulario para modificar un profesor existente.
 * Realiza lo siguiente:
 * - Habilita los campos para que sean editables.
 * - Muestra el botón de modificar y oculta el de insertar.
 * - Actualiza el título del formulario y el valor oculto "accion".
 * - Desplaza la vista hacia el formulario.
 */
function mostrarModificarProf() {
    // Habilitamos los campos para modificar
    enableFormFieldsProf();
    // Configuramos los botones: se muestra el de modificar y se oculta el de insertar
    document.getElementById('btnInsertar').style.display = "none";
    document.getElementById('btnModificar').style.display = "inline-block";
    // Actualizamos el título del formulario y el campo oculto "accion"
    document.getElementById('formTitulo').innerText = "Modificar Profesor";
    document.getElementById('accion').value = "update";
    // Desplazamos la vista hacia el formulario
    document.getElementById('formProfesor').scrollIntoView({ behavior: 'smooth' });
}


// Al cargar la página, deshabilitamos los campos del formulario de profesores.
document.addEventListener('DOMContentLoaded', function () {
    disableFormFieldsProf();
});
