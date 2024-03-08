<?php
include ('header.php');

$cod_user = $_SESSION['COD_USER'];

?>
<div class="container mt-4">
<h3 class="fw-light">Rutina usuario</h3>
	<div class="mt-4">
		<div class="2">
			<form class="col-md-3 row">
				<p class="">Numero de documento:</p>
			    <input class="form-control" type="number" id="documento" name="documento" required style="width: 200px;" >
				<input type="submit" class="btn btn-custom ms-1" id="submit-button" value="Buscar" style="width: 70px;"> 
			</form>
			<hr>
			
			<?php //Ejecucion spinner ?>
				  <div class="d-flex justify-content-center" >
					<div class="lds-roller" id="spinner_table" style="display: none;">
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
			</div>
			
			<?php //Respuesta de la tabla ?>
			<div id="divResult"> <hr> </div>
	
			<?php// Modal informacion de usuario ?>
			
				<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="staticBackdropLabel">Informacion del usuario </h5>&nbsp;&nbsp; <i class="fa-solid fa-magnifying-glass fa-xl"></i>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					  </div>
					  <div class="modal-body">
						<?php// Aca se muestra el contenido ejecutado en funciones_query ?>
					  </div>
					</div>
				  </div>
				</div>
			
				<?php// Modal Observaciones ?>
			
				<div class="modal fade" id="modal_observa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="obser_usuario" aria-hidden="true">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="obser_usuario">Observaciones </h5> &nbsp;&nbsp; <i class="fa-solid fa-pen-to-square fa-xl"></i>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					  </div>
						<div class="p-3">
							<div class="mb-3">
							  <label for="instructor" class="form-label">Instructor:</label>
							  <input type="email" class="form-control" id="instructor" placeholder="nombre instructor" required>
							</div>
							<div class="mb-3">
							  <label for="descripcion_obs" class="form-label">Descripcion:</label>
							  <textarea class="form-control" id="descripcion_obs" rows="3" required></textarea>
							</div>
							<input type="hidden" id="cod_user" name="cod_user" value="<?php echo $cod_user; ?>"> 
							<button type="submit" class="btn btn-custom" id="añadir_obs"><i class="fa-solid fa-circle-plus"></i> Añadir observacion </button>
							<p class="m-2" id="text"></p>
						</div>
						<hr>

					  <div class="modal-body-two p-3">
						
						<?php// Aca se muestra la listas de observaciones, el contenido ejecutado en funciones_query.php ?>
					  </div>
					</div>
				  </div>
				</div>
			
			
			<?php// Modal Agregar rutina ?>
			
			<!-- Modal -->
			<div class="modal fade" id="rutina" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="rutina_user" aria-hidden="true">
			  <div class="modal-dialog modal-xl">
				<div class="modal-content">
				  <div class="modal-header">
					<h1 class="modal-title fs-5" id="rutina_user">Rutina</h1> &nbsp;&nbsp; <i class="fa-solid fa-dumbbell fa-xl"></i>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body-twe row justify-content-center">
						<div class="mb-3 col-12 col-sm-4 m-1">
						  <label for="exampleFormControlTextarea1" class="form-label">Lunes</label>
						  <textarea class="form-control" id="lunes" rows="3"></textarea>
						</div>
						<div class="mb-3 col-12 col-sm-4 m-1">
						  <label for="exampleFormControlTextarea1" class="form-label">Martes</label>
						  <textarea class="form-control" id="martes" rows="3"></textarea>
						</div>
						<div class="mb-3 col-12 col-sm-4 m-1">
						  <label for="exampleFormControlTextarea1" class="form-label">Miercoles</label>
						  <textarea class="form-control" id="miercoles" rows="3"></textarea>
						</div>
						<div class="mb-3 col-12 col-sm-4 m-1">
						  <label for="exampleFormControlTextarea1" class="form-label">Jueves</label>
						  <textarea class="form-control" id="jueves" rows="3"></textarea>
						</div>
						<div class="mb-3 col-12 col-sm-4 m-1">
						  <label for="exampleFormControlTextarea1" class="form-label">Viernes</label>
						  <textarea class="form-control" id="viernes" rows="3"></textarea>
						</div>
					  	<div class="mb-3 col-12 col-sm-4 m-1">
						  <label for="exampleFormControlTextarea1" class="form-label">Sabado</label>
						  <textarea class="form-control" id="sabado" rows="3"></textarea>
						</div>
				  </div>
				  <div class="modal-footer">
					 <p id="text_rt"></p>
					<button type="button" class="btn btn-custom" id="agr_rutina">Agregar rutina</button>
				  </div>
				</div>
			  </div>
			</div>
			
			
		</div>
	</div>



<script src="js/rutina_usuario.js"> </script> <?php //scripts donde esta construida la logica de la rutina de usuario /tabla lista de asistencia /modals / botones/ iteraccion con la base de datos ?>
<?php
include('footer.php');	
?>
