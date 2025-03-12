// Script para manejar la selección del usuario y la apertura del modal 
   
        $(document).ready(function () {
            $(".open-modal").click(function () {
                var dni = $(this).data("dni");

                $.post("/router.php?route=searchUser", { dni: dni }, function (response) {
                    if (response) {
                        $("#modalDni").val(response.DNI_NIE);
                        $("#modalDniDelete").val(response.DNI_NIE);
                        $("#userModal").modal("show");
                    } else {
                        alert("Error al cargar usuario.");
                    }
                }, "json");
            });


            // Manejar la actualización de la contraseña
            $("#updatePasswordForm").submit(function (event) {
                event.preventDefault();

                $.post("/router.php?route=updatePassword", $(this).serialize(), function (response) {
                    if (response.success) {
                        alert(response.message); // Mostrar mensaje de éxito
                        $("#userModal").modal("hide"); // Cerrar modal
                    } else {
                        alert("Error: " + response.message); // Mostrar error si ocurre
                    }
                }, "json");
            });


            // Manejar la eliminación de la contraseña
            $("#deletePasswordForm").submit(function (event) {
                event.preventDefault();

                $.post("/router.php?route=deletePassword", $(this).serialize(), function (response) {

                    if (response.success) {
                        alert(response.message);
                        $("#userModal").modal("hide");
                    } else {
                        alert("Error: " + response.message); // Mostrar error si ocurre
                    }
                }, "json");
            });

        });
