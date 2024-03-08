//Proceso de mostrar el registro de dias asistidos del usuario al dar en 'buscar' /

$(document).ready(function () {
  $("form").submit(function (event) {
    event.preventDefault();
    var documento = $("#documento").val();

    // Mostrar el spinner.
    $("#spinner_table").show();

    $.ajax({
      type: "POST",
      url: "rutina_usuario_db.php",
      data: {
        documento: documento,
      },
      success: function (response) {
        // Ocultar el spinner.
        $("#spinner_table").hide();

        $("#divResult").html(response);

        // DataTables se inicializa solo después de que se haya cargado la tabla en el DOM.

        setTimeout(function () {
          $("#Table").DataTable({
            pageLength: 10, // Establece por defecto la cantidad de registros por página
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
        });
      },
      error: function (response) {
        $("#spinner_table").hide();
        console.log("Error: ", response);
      },
    });
  });
});

// Proceso al click en 'informacion de usuario'

$(document).ready(function () {
  $(document).on("click", "#inf_user", function (e) {
    //evento al realizar click
    e.preventDefault();

    var docNum = $("#documento").val();

    $.ajax({
      type: "GET",
      url: "funciones_query.php",
      data: { action: "infoUsuario", documento: docNum },
      async: false,
      success: function (data) {
        // 'data' contiene la respuesta de funciones_query.php

        $(".modal-body").html(data); // Establece el contenido del modal con la respuesta.

        $("#staticBackdrop").modal("show"); // Muestra el modal.
      },
      error: function () {
        // En caso de que la solicitud falle, se muestra el error.
        alert("Error al enviar la solicitud");
      },
    });
  });
});

// Proceso al click en 'Observaciones'

$(document).ready(function () {
  $(document).on("click", "#obs_user", function (e) {
    //evento al realizar click
    e.preventDefault();

    var docNum = $("#documento").val();

    $.ajax({
      type: "GET",
      url: "funciones_query.php",
      data: { action: "obserUsuario", documento: docNum },
      async: false,
      success: function (data) {
        // 'data' contiene la respuesta del archivo funciones_query.php.

        $(".modal-body-two").html(data); // Establece el contenido del modal con la respuesta.

        $("#modal_observa").modal("show"); // Muestra el modal.
      },
      error: function () {
        // En caso de que la solicitud falle, se muestra el error.
        alert("Error al enviar la solicitud");
      },
    });
  });
});

// Proceso al dar click en 'añadir observacion' dentro de 'obsertvaciones'

$(document).ready(function () {
  $("#añadir_obs").click(function () {
    var cod_user = $("#cod_user").val();
    var documento = $("#documento").val();
    var instructor = $("#instructor").val();
    var observacion = $("#descripcion_obs").val();

    $("#text").text("Actualizando...");

    // Realizar la llamada AJAX
    $.ajax({
      url: "funciones_query.php",
      type: "POST",
      data: {
        action: "añadir_observacion",
        documento: documento,
        cod_user: cod_user,
        instructor: instructor,
        observacion: observacion,
      },
      success: function (result) {
        //respuesta
        result = result.trim(); //elimina espacios en blanco de la respuesta
        if (result == "insertado") {
          setTimeout(function () {
            $("#text").html(
              '<strong style="color: #25ad2e;">Incluido </strong><i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>'
            );

            $("#instructor").val("");
            $("#descripcion_obs").val("");

            // Oculta el mensaje después de 3 segundos
            setTimeout(function () {
              $("#text").empty();
            }, 3000);
          }, 1000);
        } else {
          $("#text").html(
            "<strong style='color: red;'>Error al ingresar la observacion</strong>"
          );

          // Oculta el mensaje después de 3 segundos
          setTimeout(function () {
            $("#text").empty();
          }, 3000);
        }
      },
    });
  });
});


//mantener intup deshabilitado hasta recargar todo el dom
document.addEventListener("DOMContentLoaded", function () {
  var documentoInput = document.getElementById("documento");
  documentoInput.disabled = false;
});

// Proceso al click en 'Rutina'

$(document).ready(function () {
  $(document).on("click", "#rutina_user", function (e) {
    //evento al realizar click
    e.preventDefault();

    var docNum = $("#documento").val();

    $.ajax({
      type: "GET",
      url: "funciones_query.php",
      data: { action: "rutina_user", documento: docNum },
      dataType: "json", //tipo de dato a recibir
      async: false,
      success: function (data) {
        // 'data' contiene la respuesta del archivo funciones_query.php.

        //'data' es un objeto JSON.
        // Asigna cada día de la semana a los textareas correspondientes del modal.
        $("#lunes").val(data.LUNES);
        $("#martes").val(data.MARTES);
        $("#miercoles").val(data.MIERCOLES);
        $("#jueves").val(data.JUEVES);
        $("#viernes").val(data.VIERNES);
        $("#sabado").val(data.SABADO);

        $("#rutina").modal("show"); // Muestra el modal.
      },
      error: function () {
        // En caso de que la solicitud falle, se muestra el error.
        alert("Error al enviar la solicitud");
      },
    });
  });
});

// Proceso al click en 'agregar rutina'

$(document).ready(function () {
  $(document).on("click", "#agr_rutina", function (e) {
    //evento al realizar click
    e.preventDefault();

    var docNum = $("#documento").val();
    var lunes = $("#lunes").val();
    var martes = $("#martes").val();
    var miercoles = $("#miercoles").val();
    var jueves = $("#jueves").val();
    var viernes = $("#viernes").val();
    var sabado = $("#sabado").val();

    $("#text_rt").text("Actualizando...");

    $.ajax({
      type: "GET",
      url: "funciones_query.php",
      data: {
        action: "agreRutina",
        documento: docNum,
        lunes: lunes,
        martes: martes,
        miercoles: miercoles,
        jueves: jueves,
        viernes: viernes,
        sabado: sabado,
      },
      success: function (data) {
        data = data.trim();
        if (data == "insertado") {
          $("#text_rt").html(
            '<strong style="color: #25ad2e;"> Cambios guardados </strong><i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>'
          );

          setTimeout(function () {
            $("#text_rt").fadeOut(1000, function () {
              // opacidad del mensaje de forma gradual.
              $(this).empty().show();
            });
          }, 3000); //tiempo en mostrar el mensaje
        } else {
          $("#text_rt").html(
            "<strong style='color: red;'>Error al ingresar la observacion</strong>"
          );
			
          setTimeout(function () {
            $("#text_rt").fadeOut(1000, function () {
              // opacidad del mensaje de forma gradual.
              $(this).empty().show();
            });
          }, 3000); //tiempo en mostrar el mensaje
        }
      },
    });
  });
});


//proceso al realizar click en "enviar" dentro del modal 'informacion de usuario'


$(document).ready(function(){
    $(document).on('submit', '#form_adj', function(event){
        event.preventDefault();
        
        let identificacion = $('#identificacion').val();
        let file_data = $('#adj_archivo').prop('files')[0];   
        let form_data = new FormData();                  
        form_data.append('file', file_data);
        form_data.append('identificacion', identificacion);
        form_data.append('action', 'upload_file');
        
        $('#submit_button').val('Actualizando...').prop('disabled', true);
        
        $.ajax({
            url: 'funciones_query.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(response){
                if (response.trim() === 'ingresado') {
                    $('#submit_button').val('Actualizado').prop('disabled', true);
                    
                } else {
                    $('#submit_button').val('Enviar').prop('disabled', false);
                    alert('Error al enviar: ' + response);
                }
            },
            error: function(response){
                $('#submit_button').val('Enviar').prop('disabled', false);
                alert('Ha ocurrido un error al subir el archivo');
            }
        });
    });
});














