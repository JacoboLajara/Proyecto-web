/**
 * @file horarios.js
 * @description Gestión de horarios: recuperación, modificación y envío masivo de cambios.
 */

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formAsignarHorario");
    const selectAula = document.getElementById("aula");
    const selectCurso = document.getElementById("curso");
    const filasTabla = document.querySelectorAll("tbody tr");
    let horariosOriginales = {}; // 📌 Guardará los horarios al cargar la tabla

    /**
     * Cargar los horarios cuando se selecciona un curso y un aula.
     */
    selectCurso.addEventListener("change", cargarHorarios);
    selectAula.addEventListener("change", cargarHorarios);

    function cargarHorarios() {
        let cursoId = selectCurso.value;
        let aulaId = selectAula.value;

        if (!cursoId || !aulaId) {
            console.warn("⚠️ No se ha seleccionado curso y/o aula.");
            limpiarHorarios();
            return;
        }

        console.log("🔎 Enviando solicitud de horarios...");

        fetch(`mainpage.php?route=getHorariosPorCurso&aula=${aulaId}&curso=${cursoId}`)
            .then(response => response.json())
            .then(data => {
                console.log("📌 Respuesta del servidor:", data);
                console.log("📌 Horarios recibidos para llenar tabla:", data.horarios);


                if (data.success && data.horarios.length > 0) {
                    guardarHorariosOriginales(data.horarios); // 📌 Guardamos copia de los horarios
                    actualizarTablaHorarios(data.horarios);
                } else {
                    console.warn("⚠️ No hay horarios para este curso y aula.");
                    limpiarHorarios();
                }
            })
            .catch(error => {
                console.error("❌ Error al obtener horarios:", error);
                limpiarHorarios();
            });
    }


    /**
     * Guarda los horarios originales para compararlos después
     * @param {Array} horarios - Lista de horarios obtenidos de la base de datos
     */
    function guardarHorariosOriginales(horarios) {
        horariosOriginales = {};
        horarios.forEach(horario => {
            if (horario.Hora_Inicio && horario.Hora_Fin) {
                horariosOriginales[`${horario.Dia}_mañana`] = {
                    dia: horario.Dia,
                    hora_inicio: horario.Hora_Inicio,
                    hora_fin: horario.Hora_Fin,
                    tipo: "mañana"
                };
            }
            if (horario.Tarde_Inicio && horario.Tarde_Fin) {
                horariosOriginales[`${horario.Dia}_tarde`] = {
                    dia: horario.Dia,
                    hora_inicio: horario.Tarde_Inicio,
                    hora_fin: horario.Tarde_Fin,
                    tipo: "tarde"
                };
            }
        });
        console.log("💾 Horarios originales guardados:", horariosOriginales);
    }


    /**
     * Actualiza la tabla con los horarios recuperados.
     */
    function actualizarTablaHorarios(horarios) {
        console.log("📌 Datos recibidos en actualizarTablaHorarios:", horarios);
        limpiarHorarios();

        horarios.forEach(horario => {
            let dia = horario.Dia;
            let fila = Array.from(filasTabla).find(tr => tr.cells[0].textContent.trim() === dia);

            console.log(`📌 Procesando día: ${dia}, Mañana: ${horario.Hora_Inicio} - ${horario.Hora_Fin}, Tarde: ${horario.Tarde_Inicio} - ${horario.Tarde_Fin}`);

            if (fila) {
                if (horario.Hora_Inicio) {
                    fila.cells[1].querySelector("input").value = horario.Hora_Inicio;
                    fila.cells[2].querySelector("input").value = horario.Hora_Fin;
                }
                if (horario.Tarde_Inicio) {
                    fila.cells[3].querySelector("input").value = horario.Tarde_Inicio;
                    fila.cells[4].querySelector("input").value = horario.Tarde_Fin;
                }
            }
        });

        console.log("✅ Tabla actualizada con horarios de Curso:", selectCurso.value, "Aula:", selectAula.value);
    }

    /**
     * Manejador del botón "Asignar Horario".
     * Se envían solo los días con horarios registrados y se eliminan los que se borraron.
     */
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let aula = selectAula.value;
        let curso = selectCurso.value;
        let horarios = [];
        let horariosEliminar = [];

        filasTabla.forEach((fila) => {
            let dia = fila.cells[0].textContent.trim();
            let mananaInicio = fila.cells[1].querySelector("input").value.trim() || null;
            let mananaFin = fila.cells[2].querySelector("input").value.trim() || null;
            let tardeInicio = fila.cells[3].querySelector("input").value.trim() || null;
            let tardeFin = fila.cells[4].querySelector("input").value.trim() || null;

            // 📌 Si el horario estaba antes y ahora está vacío, lo añadimos a horariosEliminar
            if (horariosOriginales[`${dia}_mañana`] && !mananaInicio && !mananaFin) {
                horariosEliminar.push({ dia, tipo: "mañana" });
            }
            if (horariosOriginales[`${dia}_tarde`] && !tardeInicio && !tardeFin) {
                horariosEliminar.push({ dia, tipo: "tarde" });
            }

            // 📌 Si hay horarios nuevos o editados, los añadimos
            if (mananaInicio && mananaFin) {
                horarios.push({ dia, hora_inicio: mananaInicio, hora_fin: mananaFin, tipo: "mañana" });
            }
            if (tardeInicio && tardeFin) {
                horarios.push({ dia, hora_inicio: tardeInicio, hora_fin: tardeFin, tipo: "tarde" });
            }
        });


        console.log("📤 Datos enviados al servidor:", JSON.stringify({ aula, curso, horarios, horariosEliminar }, null, 2));

        if (!aula || !curso || (horarios.length === 0 && horariosEliminar.length === 0)) {
            alert("⚠️ No hay cambios para guardar.");
            return;
        }

        fetch("mainpage.php?route=storeHorario", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ aula, curso, horarios, horariosEliminar }),
        })
            .then(response => response.json())
            .then(data => {
                console.log("📩 Respuesta del servidor:", data);
                if (data.success) {
                    alert("✅ Horarios guardados correctamente.");
                    cargarHorarios(); // Recargar la tabla después de guardar
                } else {
                    // 📌 **Mostrar mensaje de error en la interfaz**
                    if (data.message.includes("El aula ya está ocupada")) {
                        alert("⚠️ " + data.message);
                    } else {
                        alert("❌ Error: " + data.message);
                    }
                }
            })
            .catch(error => {
                console.error("❌ Error en la solicitud:", error);
                alert("❌ Se ha producido un error al guardar los horarios.");
            });

    });

    /**
     * Limpia la tabla de horarios reseteando los valores de los inputs.
     */
    function limpiarHorarios() {
        filasTabla.forEach(fila => {
            fila.cells[1].querySelector("input").value = "";
            fila.cells[2].querySelector("input").value = "";
            fila.cells[3].querySelector("input").value = "";
            fila.cells[4].querySelector("input").value = "";
        });
    }

    /**
 * Manejador del botón "Ver Horarios Ocupados".
 * Permite obtener y mostrar los horarios ocupados en la tabla.
 */
btnMostrarHorarios.addEventListener("click", function () {
    const aulaId = selectAula.value;

    if (!aulaId) {
        alert("⚠️ Selecciona un aula para ver sus horarios ocupados.");
        return;
    }

    fetch(`mainpage.php?route=getHorariosOcupados&aula=${aulaId}`)
        .then(response => response.json())
        .then(data => {
            console.log("📌 Respuesta del servidor (Horarios Ocupados):", data);

            if (data.success && data.horarios.length > 0) {
                horariosOcupadosBody.innerHTML = "";

                data.horarios.forEach(horario => {
                    let dia = horario.Dia;
                    let mananaInicio = horario.Hora_Inicio ? horario.Hora_Inicio : "—";
                    let mananaFin = horario.Hora_Fin ? horario.Hora_Fin : "—";
                    let tardeInicio = horario.Tarde_Inicio ? horario.Tarde_Inicio : "—";
                    let tardeFin = horario.Tarde_Fin ? horario.Tarde_Fin : "—";

                    // 🟢 DEBUG: Ver qué valores estamos insertando en la tabla
                    console.log(`📌 Día: ${dia}, Mañana: ${mananaInicio} - ${mananaFin}, Tarde: ${tardeInicio} - ${tardeFin}`);

                    let row = `
                            <tr>
                                <td>${dia}</td>
                                <td>${mananaInicio}</td>
                                <td>${mananaFin}</td>
                                <td>${tardeInicio}</td>
                                <td>${tardeFin}</td>
                                <td>${horario.Curso}</td>
                            </tr>
                        `;
                    horariosOcupadosBody.innerHTML += row;
                });

                document.getElementById("modalHorarios").addEventListener("hidden.bs.modal", function () {
                    horariosOcupadosBody.innerHTML = "";
                });

            } else {
                horariosOcupadosBody.innerHTML = "<tr><td colspan='6' class='text-center'>❌ No hay horarios ocupados.</td></tr>";
            }
        })
        .catch(error => {
            console.error("❌ Error al obtener horarios ocupados:", error);
            horariosOcupadosBody.innerHTML = "<tr><td colspan='6' class='text-center'>❌ Error al cargar los horarios.</td></tr>";
        });
});

});






