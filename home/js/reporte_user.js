
//proceso al generar el reporte, al dar click en 'generar reporte' de reporte_user.php
	
$(document).ready(function() {
    $("#form_user").submit(function(event) {
        event.preventDefault();

        var t_user = $("#t_user").val();

        // Mostrar el spinner.
        $("#spinner_table_two").show();

        $.ajax({
            type: "POST",
            url: "reporte_user_db.php",
            data: {

                t_user: t_user

            },
            success: function(response) {
                // Ocultar el spinner.
                $("#spinner_table_two").hide();

                $("#result_report_user").html(response); //muestra la respuesta en del div

                // DataTables se inicializa solo después de que se haya cargado la tabla en el DOM.

                setTimeout(function() {
                    var table = $("#Table").DataTable({
                        pageLength: 10, // Establece por defecto la cantidad de registros por página
                        lengthMenu: [
                            [10, 50, 100, 200, 300, 400, -1],
                            [10, 50, 100, 200, 300, 400, "Todos"]
                        ],
                        language: {
                            lengthMenu: "Mostrar _MENU_ registros por página",
                            zeroRecords: "No se encontraron resultados",
                            info: "Mostrando página _PAGE_ de _PAGES_",
                            infoEmpty: "No hay registros disponibles",
                            infoFiltered: "(filtrado de _MAX_ registros totales)",
                            search: "Buscar:",
                            paginate: {
                                first: "Primero",
                                last: "Último",
                                next: "Siguiente",
                                previous: "Anterior",
                            },
                        },
                    });
                    $(table.table().container()).find('.dt-row').addClass('table-responsive').css({
                        'max-height': '500px',
                        'overflow': 'auto'
                    });
                    //permite incluir barra de desplazamiento en la tabla 
                });
            },
            error: function(response) {
                $("#spinner_table_two").hide();
                console.log("Error: ", response);
            },
        });
    });
});			