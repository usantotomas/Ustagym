<?php
include ('header.php');
require('../conexion.php');

?>

<div class="container-fluid mt-4 px-5 mb-3">
   <h3 class="fw-light">Reporte Asistencia</h3>
   <div class="mt-3">
      <form class="form-group row">
         <div class="col-sm-2">
            <label for="fecha">Desde:</label>
            <input type="date" id="fec_ini" class="form-control" id="fecha" required>
         </div>
         <div class="col-sm-2">
            <label for="fecha">Hasta:</label>
            <input type="date" id="fec_hasta" class="form-control" id="fecha" required>
         </div>
         <div class="col-sm-1">
            <div class="col-sm-2">
               <label for="sede">Sede:</label>
               <select class="form-control" id="sede" name="sede" style="width: 150px" required>
                  <option value="5">Todos</option>
                  <?php
                     $sql = "SELECT * FROM APLICACIONES.GYM_GYMNASIOS "; 
                     	
                     $stmt = oci_parse($conn, $sql); 
                     oci_execute($stmt);	               					// Ejecuta la consulta	
                     
                     //Recorre los datos devueltos
                     	while($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) {
                     			echo '<option value="'.htmlentities($row['COD_GYM']).'">'.htmlentities($row['NOMBRE_SEDE']).'</option>';
                     		}
                     			
                     ?>
               </select>
            </div>
         </div>
         <div class="col-sm-2 mt-4 ms-2">
            <input class="btn btn-custom" type="submit" value="Generar reporte" id="generar_repor">
         </div>
      </form>
      <hr>
      <?php //Ejecucion spinner ?>
      <div class="d-flex justify-content-center">
         <div class="lds-roller" id="spinner_table" style="display:none">
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
      <div class="" id="result_report"></div>
      <hr>
	   
      <button class="btn btn-custom" id="generarGrafico">Generar gráfico</button>
      <div class="d-flex justify-content-center align-items-center">
         <div class="col-md-6 grafica-view" style="height: 400px; display: none;" id="grafica-div">
			 
			 <?php //Vista de la grafica ?>
            <canvas id="myChart"></canvas>
			 
            <button id="descargarGrafico" class="btn btn-light mt-4 mb-5" style="display: none;">Descargar gráfico</button>
         </div>
      </div>
   </div>
</div>

<script src="js/reporte_per.js"></script> <?php //scripts donde esta construida la logica al generar la tabla y grafica. ?>


	
<?php
include('footer.php');	
?>
