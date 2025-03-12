function toggleEstado() {
    const estadoCheckbox = document.getElementById('estado');
    const estadoLabel = document.getElementById('estado-label');
    const fechaBajaField = document.getElementById('fechabaja');

    if (estadoCheckbox.checked) {
        // Si está marcado, el estado es "Baja"
        estadoLabel.textContent = "Baja";
        estadoCheckbox.value = "baja";

        // Obtener la fecha actual y asignarla al campo oculto
        const fechaActual = new Date();
        const fechaFormateada = fechaActual.toISOString().split('T')[0]; // Formato: YYYY-MM-DD
        fechaBajaField.value = fechaFormateada;
    } else {
        // Si no está marcado, el estado es "Alta"
        estadoLabel.textContent = "Alta";
        estadoCheckbox.value = "alta";

        // Limpiar la fecha de baja
        //fechaBajaField.value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    toggleEstado(); // Inicializa el estado del checkbox y la fecha de baja al cargar la página
});
