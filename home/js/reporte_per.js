
// proceso para generar el reporte 

$(document).ready(function() {
    $("form").submit(function(event) {
        event.preventDefault();

        var fecha_ini = $("#fec_ini").val();
        var fecha_hasta = $("#fec_hasta").val();
        var sede = $("#sede").val();

        // Mostrar el spinner.
        $("#spinner_table").show();

        $.ajax({
            type: "POST",
            url: "reporte_per_db.php",
            data: {

                fecha_ini: fecha_ini,
                fecha_hasta: fecha_hasta,
                sede: sede,

            },
            success: function(response) {
                // Ocultar el spinner.
                $("#spinner_table").hide();

                $("#result_report").html(response); //muestra la respuesta en del div

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
                $("#spinner_table").hide();
                console.log("Error: ", response);
            },
        });
    });
});

// Proceso al dar click en generar grafica 

function obtenerDatosTabla(idTabla) {
    let tabla = document.getElementById(idTabla);
    let datos = [];

    for (let i = 1; i < tabla.rows.length; i++) {
        let filaActual = tabla.rows[i];
        let filaDatos = [];

        for (let j = 0; j < filaActual.cells.length; j++) {
            filaDatos.push(filaActual.cells[j].innerText);
        }

        datos.push(filaDatos);
    }

    return datos;
}

function contarAsistenciasPorDia(datosTabla) {
    let asistenciasPorDia = {};

    datosTabla.forEach(fila => {
        // Extrae la fecha de asistencia y la convierte en formato YYYY-MM-DD
        let fechaAsistencia = fila[11].split(' ')[0];
        asistenciasPorDia[fechaAsistencia] = (asistenciasPorDia[fechaAsistencia] || 0) + 1;
    });

    return asistenciasPorDia;
}

let miGrafico; // Variable para almacenar la instancia del gráfico	


document.getElementById('generarGrafico').addEventListener('click', () => {
    // Obtiene los datos de la tabla
    let datosTabla = obtenerDatosTabla('Table');

    // Cuenta las asistencias por día
    let asistenciasPorDia = contarAsistenciasPorDia(datosTabla);

    // Separa los datos en etiquetas y valores
    let etiquetas = Object.keys(asistenciasPorDia);
    let valores = Object.values(asistenciasPorDia);

    // Crea un gráfico 
    let ctx = document.getElementById('myChart').getContext('2d');




    // Si la gráfica ya existe, destrúyela
    if (miGrafico) {
        miGrafico.destroy();
    }

    miGrafico = new Chart(ctx, {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Asistencias por día',
                data: valores,
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de fondo
                borderColor: 'rgba(75, 192, 192, 1)', // Color del borde
                borderWidth: 2 // Ancho del borde
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    //muestra el boton de descarga
    document.getElementById('grafica-div').style.display = 'inline-block';
    document.getElementById('descargarGrafico').style.display = 'inline-block';
});



//proceso al dar click en 'descargar grafica'

document.getElementById('descargarGrafico').addEventListener('click', () => {
    // Obtiene el canvas del gráfico
    let canvas = document.getElementById('myChart');

    // Crea un elemento de anclaje ('a') para la descarga
    let link = document.createElement('a');
    link.download = 'grafico.png';

    // Genera la imagen en formato Base64 y la asigna como href al enlace
    link.href = canvas.toDataURL('image/png');

    // Simula un click en el enlace para iniciar la descarga
    link.click();
});
