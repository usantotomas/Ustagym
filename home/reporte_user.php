<?php
include ('header.php');

?>

<div class="container-fluid mt-4 px-5 mb-3">
   <h3 class="fw-light">Reporte Usuarios</h3>
	<div>
		<form class="form-group row" id="form_user">
         <div class="col-sm-1">
            <div class="col-sm-2">
               <label for="sede">Usuario:</label>
               <select class="form-control" id="t_user" name="t_user" style="width: 150px" required>
                  <option value="Todos">Todos</option>
				  <option value="Activo">Activos</option>
				  <option value="Inactivo">Inactivos</option>
               </select>
            </div>
         </div>
         <div class="col-sm-2 mt-4 ms-2">
            <input class="btn btn-custom" type="submit" value="Generar reporte" id="generar_repor_user" data-tippy-content="Tooltip en la parte superior">
         </div>
      </form>
	</div>
	<hr>
	      <?php //Ejecucion spinner ?>
      <div class="d-flex justify-content-center">
         <div class="lds-roller" id="spinner_table_two" style="display: none">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
         </div>
      </div>
	
	
	  <?php //Respuesta de la tabla ?>
      <div class="" id="result_report_user"></div>
      <hr>
		
</div>


<script src="js/reporte_user.js"> </script> <?php //scripts donde esta construida la logica al generar el reporte , iteraccion con la base de datos ?>
	
<?php
include('footer.php');	
?>