<?php
include ('header.php');

require('../conexion.php');


// Verificar si el formulario ha sido enviado


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
	$identificacion = $_POST['identificacion'];
	
		if (!preg_match("/^\d+$/", $identificacion)) {  //Esta condicion protege contra las entradas no numéricas.
			
			echo "Error 02"; //Identificación no válida.
			
		} else {
			
			include('consulta_usuarioWS.php');  // en este archivo realiza el proceso para consultar los datos correspondientes del usuario ingresado al dar click en "obtener datos"  
		}	
}


if (isset($_GET['showModal']) && $_GET['showModal'] == 'true') {  //esta condicion muestra el modal de la respuesta de la consulta  en registro_usuario.php y 'limpia' la URL sin ningún parámetro GET 
	
    echo "<script type='text/javascript'>   
	
            document.addEventListener('DOMContentLoaded', function() { 
                var myModal = new bootstrap.Modal(document.getElementById('res_query'), {});
                myModal.show();

                if (history.replaceState) {
                    var clean_url = window.location.href.split('?')[0];
                    history.replaceState(null, null, clean_url);
                }
            });
          </script>";
}

if(isset($_GET['user']) && $_GET['user'] == 'false') { // esta condicion muestra el mensaje en caso de que el usuario ya se encuentra registrado (validacion en registro_usuario.php) y 'limpia' la URL sin ningún parámetro GET 
	
    echo "<div class='container'>
        <div class='row'>
            <div class='col-md-6 offset-md-3 mt-4'>
                <div class='alert alert-danger text-center' role='alert'>
                    El usuario ya se encuentra registrado
                </div>
            </div>
        </div>
    </div>";
	
    echo "<script>
        
        var clean_url = window.location.protocol + '//' + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, clean_url);
		
    </script>";
	
}


?>
<div class="container mt-4">
<h3 class="fw-light">Registro usuario</h3>
 	<div class="">
			<div class="forms">
			  <div class="row">
				 <form method="post" action="" class="row form1" id="myForm"> 
					<label for="identificacion" class="m-2" >Número de documento</label> <br>
					  <div class="col-md-3">
						 <input class="m-2 form-control" type="number" id="identificacion" name="identificacion" required>
					  </div>
					  <div class="col-md-3">
						 <input type="submit" class=" m-2 btn btn-custom" value="Obtener datos">
					 </div>
						<?php if (isset($aviso)) {echo $aviso ? $aviso : ''; } ?>  <!--Esta valiable muestra el aviso de numero de identificacion no encontrado -->
				  </form> 
				  <form class="form2" method="post" action="registro_usuario_db.php" enctype="multipart/form-data">
					  <div class="my-4 row">
						 <div class="col-md-3">
							<label for="identificacion" class="m-2">Tipo identificacion</label> <br>
							<input class="m-1 form-control" type="text" id="t_identificiacion" name="t_identificiacion" required value="<?php if(isset($t_identificiacion)) echo htmlspecialchars($t_identificiacion); ?>">
						  </div>
						  <div class="col-md-3">
							<label for="identificacion" class="m-2">No. identificacion</label> <br>
							<input class="m-1 form-control" type="number" id="res_identificacion" name="res_identificacion" required value="<?php if(isset($res_identificacion)) echo htmlspecialchars($res_identificacion); ?>">
						  </div>
						  <div class="col-md-3">
							<label for="nombres" class="m-2">Nombres</label> <br>
							<input class="m-1 form-control" type="text" id="nobres" name="nombres" required value="<?php if(isset($nobres)) echo htmlspecialchars($nobres); ?>">
						  </div>
						  <div class="col-md-3">
							<label for="p_apellido" class="m-2">Primer apellido</label> <br>
							<input class="m-1 form-control" type="text" id="p_apellido" name="p_apellido" required value="<?php if(isset($p_apellido)) echo htmlspecialchars($p_apellido); ?>">
						  </div>
						  <div class="col-md-3">
							<label for="s_apellido" class="m-2">Segundo apellido</label> <br>
							<input class="m-1 form-control" type="text" id="s_apellido" name="s_apellido" required value="<?php if(isset($s_apellido)) echo htmlspecialchars($s_apellido); ?>">
						  </div>
						 <div class="col-md-3">
							<label for="sexo" class="m-2">Sexo</label> <br>
							<input class="m-1 form-control" type="text" id="sexo" name="sexo" required value="<?php if(isset($sexo)) echo htmlspecialchars($sexo); ?>">
						 </div>
						 <div class="col-md-3">
							<label for="mail" class="m-2">Correo</label> <br>
							<input class="m-1 form-control" type="text" id="mail" name="mail" value="<?php if(isset($mail)) echo htmlspecialchars($mail); ?>">
						 </div> 
						 <div class="col-md-3">
							<label for="t_celular" class="m-2">Telefono Celular</label> <br>
							<input class="m-1 form-control" type="number" id="t_celular" name="t_celular" value="<?php if(isset($t_celular)) echo htmlspecialchars($t_celular); ?>">
						 </div> 
						 <div class="col-md-3">
							<label for="dependencia" class="m-2">Dependencia</label> <br>
							<input class="m-2 form-control" type="text" id="dependencia" name="dependencia" value="<?php if(isset($dependencia)) echo htmlspecialchars($dependencia); ?>">
						 </div> 
						 <div class="col-md-3">
							<label for="unidad" class="m-2">Unidad</label> <br>
							<input class="m-1 form-control" type="text" id="unidad" name="unidad" value="<?php if(isset($unidad)) echo htmlspecialchars($unidad); ?>">
						 </div> 
						 <div class="col-md-3">
							<label for="tipo_usuario" class="m-2">Tipo usuario</label> <br>
							<input class="m-1 form-control" type="text" id="tipo_usuario" name="tipo_usuario" required value="<?php if(isset($tipo_usuario)) echo htmlspecialchars($tipo_usuario); ?>">
						 </div>  
						 <div class="col-md-3">
							<label for="edad" class="m-2">Edad</label> <br>
							<input class="m-1 form-control" type="number" id="edad" name="edad" value="<?php if(isset($edad)) echo htmlspecialchars($edad); ?>">
						 </div>  
					  </div>
					  <hr>
					  <div>
						  <h3 class="fw-light my-3">Clasificacion</h3>
						<div class="form-check">
						  <input class="form-check-input" type="checkbox" value="A1" id="A1" name="clasificacion[]">
						  <label class="form-check-label" for="A1">
							<strong>A1:</strong> No presenta contraindicación para realizar ejercicio físico
						  </label>
						</div>

						<div class="form-check">
						  <input class="form-check-input" type="checkbox" value="A2" id="A2" name="clasificacion[]">
						  <label class="form-check-label" for="A2">
							<strong>A2:</strong> Ataque cardiaco, cateterización cardiaca coronaria, arritmia cardiaca, enfermedad cardiaca coronaria y toma alguna medicación
						  </label>
						</div>

						<div class="form-check">
						  <input class="form-check-input" type="checkbox" value="A3" id="A3" name="clasificacion[]">
						  <label class="form-check-label" for="A3">
							<strong>A3:</strong> Cirugía de corazón, enfermedad valvular del corazón, falla cardiaca y trasplante cardiaco
						  </label>
						</div>

						<div class="form-check">
						  <input class="form-check-input" type="checkbox" value="B" id="B" name="clasificacion[]">
						  <label class="form-check-label" for="B">
							<strong>B:</strong> Restriccion total
						  </label>
						</div>
					  </div>
						<div class="mb-3 mt-3">
							<label for="file_archivo" class="form-label">Archivo condicion fisica</label>
							<div class="input-group">
							   <input class="form-control form-control-md" type="file" name="file_archivo" id="file_archivo" accept=".pdf">
							   <button type="button" id="cancel_file" class="btn btn-secondary">X</button> <?php  //logica en registro_usuario_form.js ?>
							</div>
							<input type="submit" class="mt-4 btn btn-custom" id="submit-button" style="" value="Registrar"> <!-- logica de habilitacion boton en registro_usuario_form.js -->
						</div>
				</form>
			  </div>
			</div>

			<?php // Modal - Este modal se mostrara al adjuntar el archivo cuando supere los 800KB / logica en registro_usuario.js ?>
		
						<div class="modal fade" id="myModal">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">Advertencia</h4>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body">
										El documento es muy pesado, maximo 800KB.
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-danger"  data-bs-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div>
		  	<!-- --------------------------------------------------------------------------- -->

		<?php // Modal de carga - spinner, estilo personalizado en registro_usuario.css ?>
		
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
		
		
<?php // Modal respuesta de la consulta (al registrar usuario) ?>
		
					<div class="modal fade" id="res_query">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title"></h4>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body d-flex justify-content-center align-items-center flex-column mt-3">
									<i class="fa-solid fa-circle-check fa-beat-fade fa-2xl" style="color: #26af1d; width: 40px; height: 30px;" ></i> 
									<p class="text-center">Usuario registrado</p>
								</div>

							</div>
						</div>
					</div>

		  <!-- --------------------------------------------------------------------------- -->

		<script>
	
			
			
		// ejecucion spinner
			
		document.getElementById('myForm').addEventListener('submit', function(event) {
		  // Llama a la función que muestra el modal
		  showModal();
		});

		// Define la función para mostrar el modal
		function showModal() {
		  // Crea una instancia del modal de Bootstrap
		  var myModal = new bootstrap.Modal(document.getElementById('loadingModal'), {});
		  // Muestra el modal
		  myModal.show();
		}
		
		</script>
		
		

		
		<script src="js/registro_usuario.js"> </script>  <!-- Este script realiza las funciones de limitar el tamaño del archivo adjunto/ seleccionde checkbox  -->
		<script src="js/registro_usuario_form.js"> </script> <!-- Logica para deshabilitar el boton de "Registrar" hasta completar el formulario -->
		
 	</div>
</div>


<?php
include('footer.php');	
?>
