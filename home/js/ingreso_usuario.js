//Proceso cuando se da click en el boton 'Registrar ingreso'
	
function num_documento(valorCaja1) {
  var parametros = {
    ducumento: valorCaja1,
  };

  // Muestra el spinner
  $("#loadingModal").show();

  $.ajax({
    data: parametros,
    url: "datos_user.php",
    type: "post",
    success: function (response) {
      $("#modal-datos").html(response);
      $("#ingreso-Backdrop").modal("show");
    },
    complete: function () {
      
      $("#loadingModal").hide(); // Oculta el spinner cuando se completa la solicitud
    },
  });
}

$(document).ready(function () {
  $("#submit-ingreso").on("click", function () {
    event.preventDefault();
    var ducumento = $("#documento").val();
    num_documento(ducumento);
  });
});	

// ------------------------------------------------------------------------------------//

// proceso al dar click en 'omitir ingreso denegado' dentro del modal ingreso-Backdrop / query construido en funciones_query.php
	
$(document).ready(function () {
  $("#omitir_ing_de").click(function () {
    var estadoUser = $("#estado_user").text().trim();  //variable proveniente de datos_use.php
   
    var documento = $("#documento").val();
    $("#omitir_ing_de").text("Actualizando...");

    // Verificar el valor de estadoUser  // si es diferente a estos caracteres , se ejecura el ajax
    if (
      !estadoUser.includes("Active") &&
      !estadoUser.includes("Activo") &&
      !estadoUser.includes("Egresado") &&
      !estadoUser.includes("Graduado")
    )  {
      // Realizar la llamada AJAX
      $.ajax({
        url: "funciones_query.php",
        type: "POST",
        data: {
          action: "omitir_ing_d",
          documento: documento,
        },
        success: function (result) {  //respuesta
          result = result.trim();
          if (result == "ingresado") {   
            setTimeout(function () {
              $("#omitir_ing_de").html(
                'Permitido <i class="fa-sharp fa-solid fa-check" style="color: #ffffff;"></i>'
              );
              $("#estado_entrada").html(
                '<strong style="color: #25ad2e;">Permitido </strong><i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>'
              );
              $("#omitir_ing_de").prop("disabled", true);
            }, 2000);
          } else {
			alert("Error: " + result); // Mostrar el error
            $("#omitir_ing_de").text("Error");
          }
        },
      });
    } else {
      // No realizar la llamada AJAX y mostrar un mensaje de error
  		
      $("#omitir_ing_de").text("Error: Estado de usuario valido");
      return;
    }
  });
});


// proceso al dar click en 'Renovar inscripcion' dentro del modal ingreso-Backdrop / query construido en funciones_query.php

$(document).ready(function () {
  $("#btn_renovar").click(function () {
    var actualizarVigencia = $("#actualizar_vig").text(); // Obtener el texto de #actualizar_vig
    var fechaActual = new Date(); // Obtener la fecha actual

    // Convertir la fecha actual al formato deseado (23-dec-23)
    var options = { year: '2-digit', month: 'short', day: 'numeric' };
    var fechaActualTexto = fechaActual.toLocaleDateString('en-US', options).toLowerCase();

    // Extraer la fecha de actualizarVigencia en formato (dd-mmm-yy)
    var fechaActualizarVigencia = actualizarVigencia.split(':')[1].trim().split(' ')[0];

    // Comparar las fechas
    if (new Date(fechaActualizarVigencia) < fechaActual) {
      // Si la fecha de actualizarVigencia es menor a la fecha actual
      var documento = $("#documento").val(); // Obtén el valor del campo de entrada con id 'documento'
      $("#btn_renovar").text("Actualizando...");
      $.ajax({
        url: "funciones_query.php",
        type: "POST",
        data: {
          action: "renovar",
          documento: documento,
        },
        success: function (result) {            //respuesta
          result = result.trim();               //elimina espacios en blanco
          if (result == "actualizado") {
            setTimeout(function () {
              // Esperar 2 segundos

              $("#btn_renovar").html(
                'Vigente <i class="fa-sharp fa-solid fa-check" style="color: #ffffff;"></i>'
              ); // Cambiar texto del botón
              $("#actualizar_vig").html(
                '<strong>Vigencia inscripcion:</strong> Renovado <i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>'
              ); // Cambia texto de vigencia
              $("#btn_renovar").prop("disabled", true); // Deshabilitar botón
            }, 2000);
          } else {
			alert("Error: " + result); // Mostrar el error
            $("#btn_renovar").text("Error");
			 
          }
        },
      });
    } else {
      // Si la fecha de actualizarVigencia es mayor o igual a la fecha actual
      alert("No puedes renovar en este momento. La fecha se encuentra vigente.");
    }
  });
});


// ------------------------------------------------------------------------------------//	
	
// Logica para recargar pagina al cerrar el modal
var closeButton = document.querySelector(".modal-header .btn-close");
closeButton.addEventListener("click", function () {
  location.reload();
});

// Logica para Enfocar automáticamente el campo documento al cargar la página

var inputField = document.getElementById("documento");
window.addEventListener("DOMContentLoaded", function () {
  inputField.focus();
});
