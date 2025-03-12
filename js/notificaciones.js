/**
 * Muestra la pregunta "¿Todos o algunos?" al seleccionar una figura.
 *
 * Esta función muestra el elemento con id "preguntaTodos" configurándolo para que sea visible.
 *
 * @returns {void}
 */
function mostrarPregunta() {
    const pregunta = document.getElementById('preguntaTodos');
    if (pregunta) {
        pregunta.style.display = 'block';
    }
}

/**
 * Envía la notificación a todos los usuarios.
 *
 * Valida que se haya seleccionado una figura (tipo) y se haya ingresado un mensaje.
 * Luego, añade un campo oculto "todos" al formulario si aún no existe y envía el formulario.
 *
 * @returns {void}
 */
function enviarATodos() {
    const tipo = document.getElementById('opciones').value;
    const mensaje = document.getElementById('notificacion').value;

    if (!tipo) {
        alert("Selecciona una figura antes de continuar.");
        return;
    }

    if (!mensaje) {
        alert("Por favor, escribe el mensaje antes de enviarlo.");
        return;
    }

    const formulario = document.querySelector('form');

    // Evitar duplicados: si el campo oculto "todos" no existe, lo crea.
    if (!formulario.querySelector('input[name="todos"]')) {
        const inputOculto = document.createElement('input');
        inputOculto.type = 'hidden';
        inputOculto.name = 'todos';
        inputOculto.value = '1';
        formulario.appendChild(inputOculto);
    }

    formulario.submit();
}

/**
 * Muestra el modal para seleccionar usuarios específicos.
 *
 * Valida que se haya seleccionado una figura (tipo) y luego realiza una llamada AJAX
 * para obtener los usuarios activos del tipo seleccionado. Si se reciben usuarios, los muestra en el modal.
 *
 * @returns {void}
 */
function mostrarModalSeleccion() {
    const tipo = document.getElementById('opciones').value;
    if (!tipo) {
        alert("Selecciona una figura antes de continuar.");
        return;
    }

    // Realiza una llamada AJAX para obtener usuarios activos por tipo
    fetch(`/router.php?route=getUsuariosPorTipo&type=${tipo}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Usuarios recibidos:", data);
            const listaUsuarios = document.getElementById('lista-usuarios');
            listaUsuarios.innerHTML = '';

            if (data.length === 0) {
                alert("No se encontraron usuarios activos.");
                return;
            }

            data.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.ID_Usuario;
                option.textContent = `${usuario.ID_Usuario} - ${usuario.Nombre ?? ''}`;
                listaUsuarios.appendChild(option);
            });

            const modal = new bootstrap.Modal(document.getElementById('modalSeleccionUsuarios'));
            modal.show();
        })
        .catch(error => {
            console.error('Error al cargar usuarios:', error);
            alert(`Ocurrió un error al cargar los usuarios: ${error.message}`);
        });
}

/**
 * Confirma la selección de usuarios y los agrega al formulario.
 *
 * Obtiene los usuarios seleccionados del elemento select con id "lista-usuarios",
 * elimina cualquier entrada oculta previa para evitar duplicados y añade nuevos campos ocultos
 * al formulario. Finalmente, cierra el modal y envía el formulario.
 *
 * @returns {void}
 */
function confirmarSeleccion() {
    const listaUsuarios = document.getElementById('lista-usuarios');
    const usuariosSeleccionados = Array.from(listaUsuarios.selectedOptions).map(option => option.value);

    if (usuariosSeleccionados.length === 0) {
        alert("Por favor, selecciona al menos un usuario.");
        return;
    }

    const formulario = document.querySelector('form');

    // Elimina entradas ocultas existentes para evitar duplicados
    formulario.querySelectorAll('input[name="usuarios[]"]').forEach(input => input.remove());

    usuariosSeleccionados.forEach(idUsuario => {
        const inputOculto = document.createElement('input');
        inputOculto.type = 'hidden';
        inputOculto.name = 'usuarios[]';
        inputOculto.value = idUsuario;
        formulario.appendChild(inputOculto);
    });

    // Cierra el modal y envía el formulario
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSeleccionUsuarios'));
    if (modal) {
        modal.hide();
    }

    formulario.submit();
}
