<?php
include ('header.php');
include ('../conexion.php');

//consulta para saber la sede conrrespondiente segun la cuenta logeada

$cod_user = $_SESSION['COD_USER'];

$sql = "SELECT b.NOMBRE_SEDE FROM APLICACIONES.GYM_USER a JOIN APLICACIONES.GYM_GYMNASIOS b ON a.COD_GYM = b.COD_GYM WHERE a.COD_USER = :cod_user"; 
   
	
$stmt = oci_parse($conn, $sql);													// Vincula los parámetros
oci_bind_by_name($stmt, ':cod_user', $cod_user);
oci_execute($stmt);    															// Ejecuta la consulta
$user_data = oci_fetch_assoc($stmt);											// Toma el dato de la consulta y la asigna a la variable
    																			
	

?>

<h3 class="fw-light m-4">Ingreso usuario - <?php echo $user_data['NOMBRE_SEDE']?></h3>

<div class="mt-3 container d-flex align-items-center justify-content-center" style="height: 400px"> 
	<form class="ingreso-div p-4"> 
		<div class="form-group">
			<p>Numero de documento:</p>
			<input type="number" class="form-control" id="documento" required>
		</div>
		<div class="form-group mt-3 d-flex align-items-center justify-content-center">
			<input type="submit" class="btn btn-custom" id="submit-ingreso" value="Registrar ingreso">
		</div>
	</form>
</div>

		<!-- Modal informacion de usuario , validacion en datos_user.php-->

		<div class="modal fade" id="ingreso-Backdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content modal-ingreso">
			  <div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			   <div class="modal-body" id='modal-datos'>

					<!-- informacion del usuario  -->
			  </div>
			  <div class="modal-footer">
				<button id="omitir_ing_de" class="ms-4 btn btn-custom">Omitir ingreso denegado</button>
				<button id="btn_renovar" class="btn btn-custom">Renovar inscripcion</button>
			  </div>
			</div>
		  </div>
		</div>


		<!-- Modal de carga - spinner, estilo personalizado en registro_usuario.css -->  
		
			<div class="modal" tabindex="-1" role="dialog" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" >
			  <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content" id="modal-spinner">
				  <div class="modal-body text-center">
					<div class="text-center text-primary d-flex justify-content-center align-items-center">
					  <div class="spinner">
						  <div class="double-bounce1"></div>
						  <div class="double-bounce2"></div>
						</div>
					</div>
				  </div>
				</div>
			  </div>
			</div>

		  <!-- --------------------------------------------------------------------------- -->
		
<script src="js/ingreso_usuario.js"> </script> <?php //scripts donde esta construida la logica de ingreso de usuario e interaccion con la base de datos ?>


<?php
include('footer.php');	
?>

