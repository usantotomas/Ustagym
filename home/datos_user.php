<?php
require ('../conexion.php');
session_start();

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$documento = $_POST['ducumento'];

	if (!preg_match('/^\d+$/', $documento)) {
		echo 'El documento no es un número entero válido';
	} else {
		$sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :documento";

		$stmt = oci_parse($conn, $sql);                     // Prepara la consulta SQL
		oci_bind_by_name($stmt, ':documento', $documento);  // Vincula los parámetros
		oci_execute($stmt);                                 // Ejecuta la consulta
		$user_data = oci_fetch_assoc($stmt); 				// Toma el dato de la consulta y la asigna a la variable

		if (!empty($user_data)) {
		
	
		
		//si es estudiante o egresado consulta en SAC para verificar el estado actual 

			if ((strtolower($user_data['TIPO_USUARIO']) === 'estudiante') || (strtolower($user_data['TIPO_USUARIO']) === 'egresado')) {
				$sql_two = "SELECT  
						a.NUM_IDENTIFICACION, 
						CASE d.EST_ALUMNO
							WHEN '0' THEN 'Retirado'
							WHEN '1' THEN 'Activo'
							WHEN '2' THEN 'Egresado'
							WHEN '3' THEN 'Graduado'
							WHEN '4' THEN 'Traslado'
							WHEN '5' THEN 'Cancelado'
							WHEN '6' THEN 'Borrado financieramente'
							WHEN '7' THEN 'Condicional'
							WHEN '8' THEN 'Inactivo'
							WHEN '9' THEN 'Sanción Disciplinaria'
							WHEN '10' THEN 'Anulado'
							WHEN '11' THEN 'Estado Asignado Manualmente'
							WHEN '12' THEN 'Deserción Precoz'
							WHEN '13' THEN 'En Opción Grado'
							WHEN '14' THEN 'Inactivo por Continuidad'
							ELSE 'Estado Desconocido'
						END AS EST_ALUMNO
					FROM 
						SINU.BAS_TERCERO a
						JOIN SINU.BAS_TIP_TERCERO b ON a.ID_TERCERO = b.ID_TERCERO
						JOIN SINU.SRC_ALUM_PROGRAMA d ON a.ID_TERCERO = d.ID_TERCERO 
					WHERE 
						a.NUM_IDENTIFICACION = :documento 
						AND b.COD_TABLA IN ('1','8','08','09') 
					ORDER BY 
						d.COD_PERIODO  DESC";

				$stmt_tow = oci_parse($conn, $sql_two);                                      // Prepara la consulta SQL
				oci_bind_by_name($stmt_tow, ':documento', $documento);             		     // Vincula los parámetros
				oci_execute($stmt_tow);                                                      // Ejecuta la consulta
				$est_alumno = oci_fetch_assoc($stmt_tow);                                    // Toma el dato de la consulta y la asigna a la variable
		
			
					
				// Proceso para mostrar texto primera letra en mayuscula y demas en minuscula	
				$datos = array(
					$user_data['IDENTIFICACION'],
					$user_data['NOMBRES'] . ' ' . $user_data['PRI_APELLIDO'] . ' ' . $user_data['SEG_APELLIDO'],
					$user_data['TIPO_USUARIO'],
					$user_data['DEPENDENCIA'],
					$user_data['CLASIFICACION'],
					$user_data['FINAL_INSCRIPCION'],
					$est_alumno['EST_ALUMNO'] //= 'inactivo'
				); 
					
				if($datos[6] == '') {$datos[6] = 'No se identifica';}
				
				// Convertir todos los elementos del array a minúsculas
				$datos = array_map('strtolower', $datos);

				// Convertir la primera letra de cada elemento del array a mayúscula
				$datos = array_map('ucfirst', $datos);
				
				//variables para iconos
				$vigencia = '';
				$est_alumno = '';
				
				// Convertimos la fecha a un formato que PHP pueda entender
				$fechaFormateada = DateTime::createFromFormat('d-M-y', $datos[5]);

				// Obtenemos la fecha actual
				$fechaActual = new DateTime();

				$est_alumno = ($datos[6] == 'Activo' || $datos[6] == 'Egresado' || $datos[6] == 'Graduado' || $datos[6] == 'Condicional') ? '<i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>' : '<i class="fa-solid fa-circle-exclamation" style="color: #bf0d0d;"></i>';

				$vigencia = ($fechaFormateada < $fechaActual) ? '<i class="fa-solid fa-circle-exclamation" style="color: #bf0d0d;"></i>' : '<i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>';

				//estas variables definen el acceso 
				$entrada = '';
				$query_entrada = '';
				
				$omitir_ing_de = ''; 
				
				if ($est_alumno == '<i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>') {
					$entrada = "<p class='mb-2 ms-4' id='estado_entrada' style='font-size: 14pt; color: #31af3a;'><i class='fa-solid fa-user-check' style='color: #31af3a;  font-size: 14pt;'></i> Entrada valida</p>";
					$query_entrada = true;
					
					
				} else {
					$entrada = "<p class='mb-2 ms-4' id='estado_entrada' style='font-size: 14pt; color: #de0202;'><i class='fa-solid fa-user-xmark' style='color: #de0202;'></i> Acceso denegado</p>";
					$query_entrada = false;
					//$omitir_ing_de = '<button id="omitir_ing_de" class="ms-4 btn btn-custom">Omitir ingreso denegado</button>';
				}
				
				// creacion ruta completa de la foto, 
				
				$etiqueta_imagen = '';
				
				$ruta_imagen = '../../Identidad_N/fotos/' . $documento . '.jpg'; // ajusta la extensión del archivo si es necesario
								
					 
				// verificar si el archivo de la imagen existe
				if (file_exists($ruta_imagen)) {
					// mostrar la imagen
					 $etiqueta_imagen = '<img src="' . $ruta_imagen . '" alt="Imagen de la persona" width="130">';
					
				} else {
					// manejar el caso cuando la imagen no existe
					$etiqueta_imagen = '<img src="img/user.png" alt="user_logo" style="opacity: 0.6" class="img-fluid">';
				}
				
				//consulta para añadir las observaciones
				$sql_observaciones = 'SELECT * FROM APLICACIONES.GYM_OBSERVACION a 
            							JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG 
										WHERE b.IDENTIFICACION  = :documento ';
				
					    $stmt_obser = oci_parse($conn, $sql_observaciones);                                       // Prepara la consulta SQL
						oci_bind_by_name($stmt_obser, ':documento', $documento);                                  // Vincula los parámetros
						oci_execute($stmt_obser);                                                                 // Ejecuta la consulta
				
				$row_ob = oci_fetch_assoc($stmt_obser);
				
				
				
				// Muestra los datos correspondientes de la persona y su estado actual  				    	
				echo "<div class='row'>
							<div class='col-md-8'>
								" . $entrada . "
								<ul>
							  	<li class='list-group-item'><strong> N° Documento: </strong>" . $datos[0] . "</li>
							  	<li class='list-group-item'><strong> Nombre: </strong>" . $datos[1] . "</li>
							  	<li class='list-group-item'><strong> Tipo usuario: </strong>" . $datos[2] . "</li>
								<li class='list-group-item'><strong> Dependencia: </strong>" . $datos[3] . "</li>
								<li class='list-group-item'> <strong> Clasificacion: </strong>" . $datos[4] . "</li>
								<li class='list-group-item' id='actualizar_vig'> <strong> Vigencia inscripcion: </strong>" . $datos[5] . ' ' . $vigencia . "</li>
							  	<li class='list-group-item' id='estado_user'> <strong> Estado Usuario: </strong>" . $datos[6] . ' ' . $est_alumno . "</li>
								</ul>
							</div>
							<div class='col-md-4 mt-3'>
								".$etiqueta_imagen."
							</div>
					  </div>";
				
				//muestra las observaciones si se han realizado
				if ($row_ob) {
					
						echo "<hr>
							  <p><strong><i class='fa-solid fa-triangle-exclamation'></i> Observaciones:</strong></p>";
						
					do {
						
						echo "<div class='row'>
							<div class='col-md-8'>
								 <ul>
									<li>".$row_ob['FECHA_OBSERVACION']."</li>
									<li class='list-group-item'>Instructor: ".$row_ob['NOMBRE_INSTRUCTOR']." </li>
									<li class='list-group-item'>Descripcion: ".$row_ob['DES_OBSERVACION']." </li>
								 </ul>
							</div>
						</div>";
						
					} while ($row_ob = oci_fetch_assoc($stmt_obser));
				 }

				//si el estudiante es activo, egresado o graduado inserta los datos en la base de datos de su ingreso. 

				if ($query_entrada == true) {
					$cod_user = $_SESSION['COD_USER']; //es la sede donde realiza el ingreso

					if (empty($cod_user)) { //se agrega esta condicion en el caso de que se haya cerrado la sesion 

						echo "No se registró el ingreso, inicia nuevamente sesion.";
						die;
					} else {
						$sql_t = "INSERT INTO APLICACIONES.GYM_ASISTENCIA  (COD_USER, COD_USUARIO_REG , FECHA_ASISTENCIA)
									VALUES (:cod_user, :cod_usuario_reg, SYSDATE)";

						$stmt_t = oci_parse($conn, $sql_t);                    							    // Prepara la consulta SQL
						oci_bind_by_name($stmt_t, ':cod_usuario_reg', $user_data['COD_USUARIO_REG']);       // Vincula los parámetros
						oci_bind_by_name($stmt_t, ':cod_user', $cod_user);  				 				// Vincula los parámetros
						oci_execute($stmt_t);                                					            // Ejecuta la consulta

					}
				}
			}
		//si es funcionario o docente realiza la consulta en el web service
			else if (($user_data['TIPO_USUARIO'] != '') || ($user_data['TIPO_USUARIO'] == 'DOCENTE') || ($user_data['TIPO_USUARIO'] == 'DIR ADMINISTRATIVO') || ($user_data['TIPO_USUARIO'] == 'DOC MEDIO TIEMPO') || ($user_data['TIPO_USUARIO'] == 'DOC TIEMPO COMPLETO') || ($user_data['TIPO_USUARIO'] == 'FRAILES DOMINICOS')) {
			
			require_once('consulta_estadoWS.php'); //consulta en el web service el estado actual del usuario y se define la variable $estado_user
			
		
				
		// Proceso para mostrar texto primera letra en mayuscula y demas en minuscula	
				$datos = array(
					$user_data['IDENTIFICACION'],
					$user_data['NOMBRES'] . ' ' . $user_data['PRI_APELLIDO'] . ' ' . $user_data['SEG_APELLIDO'],
					$user_data['TIPO_USUARIO'],
					$user_data['DEPENDENCIA'],
					$user_data['CLASIFICACION'],
					$user_data['FINAL_INSCRIPCION'],
					$estado_user// 'inactivo'
				);  
			
	
				// Convertir todos los elementos del array a minúsculas
				$datos = array_map('strtolower', $datos);

				// Convertir la primera letra de cada elemento del array a mayúscula
				$datos = array_map('ucfirst', $datos);
				
				//variables para iconos
				$vigencia = '';
				$est_user = '';
				
				// Convertimos la fecha a un formato que PHP pueda entender
				$fechaFormateada = DateTime::createFromFormat('d-M-y', $datos[5]);

				// Obtenemos la fecha actual
				$fechaActual = new DateTime();

				$est_user = ($datos[6] == 'Active') ? '<i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>' : '<i class="fa-solid fa-circle-exclamation" style="color: #bf0d0d;"></i>';

				$vigencia = ($fechaFormateada < $fechaActual) ? '<i class="fa-solid fa-circle-exclamation" style="color: #bf0d0d;"></i>' : '<i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>';
				
			
				//estas variables definen el acceso 
				$entrada = '';
				$query_entrada = '';
				

				if ($est_user == '<i class="fa-solid fa-circle-check" style="color: #25ad2e;"></i>') {
					$entrada = "<p class='mb-2 ms-4' id='estado_entrada' style='font-size: 14pt; color: #31af3a;'><i class='fa-solid fa-user-check' style='color: #31af3a;  font-size: 14pt;'></i> Entrada valida</p>";
					$query_entrada = true;
				} else {
					$entrada = "<p class='mb-2 ms-4' id='estado_entrada' style='font-size: 14pt; color: #de0202;'><i class='fa-solid fa-user-xmark' style='color: #de0202;'></i> Acceso denegado</p>";
					$query_entrada = false;
				}
			
				
				// crear la ruta completa a la imagen
				
				$etiqueta_imagen = '';
				
				$ruta_imagen = '../../Identidad_N/fotos/' . $documento . '.jpg'; 
				
				
				// verificar si el archivo de la imagen existe
				if (file_exists($ruta_imagen)) {
					
					// mostrar la imagen
					$etiqueta_imagen = '<img src="' . $ruta_imagen . '" alt="Imagen de la persona" width="130">';
					
				} else {
					// manejar el caso cuando la imagen no existe
					$etiqueta_imagen = '<img src="img/user.png" alt="user_logo" style="opacity: 0.6" class="img-fluid">';
				}
				
				
				//consulta para añadir las observaciones
				$sql_observaciones = 'SELECT * FROM APLICACIONES.GYM_OBSERVACION a 
            							JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG 
										WHERE b.IDENTIFICACION  = :documento ';
				
					    $stmt_obser = oci_parse($conn, $sql_observaciones);                                       // Prepara la consulta SQL
						oci_bind_by_name($stmt_obser, ':documento', $documento);                                  // Vincula los parámetros
						oci_execute($stmt_obser);                                                                 // Ejecuta la consulta
				
				$row_ob = oci_fetch_assoc($stmt_obser);
							
				
				
				// Muestra los datos correspondientes de la persona y su estado actual  	
								    	
				echo "<div class='row'>
							<div class='col-md-8'>
								" . $entrada . "
								<ul>
							  	<li class='list-group-item'><strong> N° Documento: </strong>" . $datos[0] . "</li>
							  	<li class='list-group-item'><strong> Nombre: </strong>" . $datos[1] . "</li>
							  	<li class='list-group-item'><strong> Tipo usuario: </strong>" . $datos[2] . "</li>
								<li class='list-group-item'><strong> Dependencia: </strong>" . $datos[3] . "</li>
								<li class='list-group-item'> <strong> Clasificacion: </strong>" . $datos[4] . "</li>
								<li class='list-group-item' id='actualizar_vig'> <strong> Vigencia inscripcion: </strong>" . $datos[5] . ' ' . $vigencia . "</li>
							  	<li class='list-group-item' id='estado_user'> <strong> Estado Usuario: </strong>" . $datos[6] . ' ' . $est_user . "</li>
								</ul>
							</div>
						 <div class='col-md-4 mt-4'>
							".$etiqueta_imagen."
						 </div>
					  </div>";
				
				
				//muestra las observaciones si se han realizado
				if ($row_ob) {
					
						echo "<hr>
							  <p><strong><i class='fa-solid fa-triangle-exclamation'></i> Observaciones:</strong></p>";
						
					do {
						
						echo "<div class='row'>
							<div class='col-md-8'>
								 <ul>
									<li>".$row_ob['FECHA_OBSERVACION']."</li>
									<li class='list-group-item'>Instructor: ".$row_ob['NOMBRE_INSTRUCTOR']." </li>
									<li class='list-group-item'>Descripcion: ".$row_ob['DES_OBSERVACION']." </li>
								 </ul>
							</div>
						</div>";
						
					} while ($row_ob = oci_fetch_assoc($stmt_obser));
				 }
				
				
				//si es funcionario o docente es activo inserta los datos en la base de datos de su ingreso. 

				if ($query_entrada == true) {
					$cod_user = $_SESSION['COD_USER']; //es la sede donde realiza el ingreso

					if (empty($cod_user)) { //se agrega esta condicion en el caso de que se haya cerrado la sesion 

						echo "No se registró el ingreso, inicia nuevamente sesion.";
						die;
					} else {
						$sql_t = "INSERT INTO APLICACIONES.GYM_ASISTENCIA  (COD_USER, COD_USUARIO_REG , FECHA_ASISTENCIA)
									      VALUES (:cod_user, :cod_usuario_reg, SYSDATE)";

						$stmt_t = oci_parse($conn, $sql_t);                    							    // Prepara la consulta SQL
						oci_bind_by_name($stmt_t, ':cod_usuario_reg', $user_data['COD_USUARIO_REG']);       // Vincula los parámetros
						oci_bind_by_name($stmt_t, ':cod_user', $cod_user);  				 				// Vincula los parámetros
						oci_execute($stmt_t);                                					            // Ejecuta la consulta

					}
				}
			}else {
				
				//consulta para añadir las observaciones
				$sql_observaciones = 'SELECT * FROM APLICACIONES.GYM_OBSERVACION a 
            							JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG 
										WHERE b.IDENTIFICACION  = :documento ';
				
					    $stmt_obser = oci_parse($conn, $sql_observaciones);                                       // Prepara la consulta SQL
						oci_bind_by_name($stmt_obser, ':documento', $documento);                                  // Vincula los parámetros
						oci_execute($stmt_obser);                                                                 // Ejecuta la consulta
				
				$row_ob = oci_fetch_assoc($stmt_obser);
				
				
				//esta condicion solo se cumple si se ha registrado el usuario manualmente y no aparece en sac ni en el web service o no esta especificado el tipo de usuario como estudiante/docente/administrativo. 
				echo "<div class='row'>
							<div class='col-md-10'>
								<p class='mb-2 ms-4' id='estado_entrada' style='font-size: 14pt; color: #de0202;'><i class='fa-solid fa-user-xmark' style='color: #de0202;'></i> Acceso denegado</p>
								<ul>
							  	<li class='list-group-item'><strong> N° Documento: </strong>" . $user_data['IDENTIFICACION'] . "</li>
							  	<li class='list-group-item'><strong> Nombre: </strong>" . $user_data['NOMBRES'] . ' ' . $user_data['PRI_APELLIDO'] . ' ' . $user_data['SEG_APELLIDO'] . "</li>
							  	<li class='list-group-item'><strong> Tipo usuario: </strong>" . $user_data['TIPO_USUARIO'] . "</li>
								<li class='list-group-item'><strong> Dependencia: </strong>" . $user_data['DEPENDENCIA'] . "</li>
								<li class='list-group-item'> <strong> Clasificacion: </strong>" . $user_data['CLASIFICACION'] . "</li>
								<li class='list-group-item' id='actualizar_vig'> <strong> Vigencia inscripcion: </strong> " . $user_data['FINAL_INSCRIPCION'] . "</li>
							  	<li class='list-group-item' id='estado_user'> <strong> Estado Usuario: </strong> Informacion desconocida </li>
								</ul>
							</div>
					  </div>";
				
				
					// se comenta este codigo en caso de que se requiera registrar la entrada de la persona en caso de que se cumpla la condicion anterior 
				
					/*$cod_user = $_SESSION['COD_USER']; //es la sede donde realiza el ingreso
				
					if (empty($cod_user)) { //se agrega esta condicion en el caso de que se haya cerrado la sesion 

						echo "No se registró el ingreso, inicia nuevamente sesion.";
						die;
					} else {
						$sql_t = "INSERT INTO APLICACIONES.GYM_ASISTENCIA  (COD_USER, COD_USUARIO_REG , FECHA_ASISTENCIA)
									      VALUES (:cod_user, :cod_usuario_reg, SYSDATE)";

						$stmt_t = oci_parse($conn, $sql_t);                    							    // Prepara la consulta SQL
						oci_bind_by_name($stmt_t, ':cod_usuario_reg', $user_data['COD_USUARIO_REG']);       // Vincula los parámetros
						oci_bind_by_name($stmt_t, ':cod_user', $cod_user);  				 				// Vincula los parámetros
						oci_execute($stmt_t);                                					            // Ejecuta la consulta

					}*/
				
				//muestra las observaciones si se han realizado
				if ($row_ob) {
					
						echo "<hr>
							  <p><strong><i class='fa-solid fa-triangle-exclamation'></i> Observaciones:</strong></p>";
						
					do {
						
						echo "<div class='row'>
							<div class='col-md-8'>
								 <ul>
									<li>".$row_ob['FECHA_OBSERVACION']."</li>
									<li class='list-group-item'>Instructor: ".$row_ob['NOMBRE_INSTRUCTOR']." </li>
									<li class='list-group-item'>Descripcion: ".$row_ob['DES_OBSERVACION']." </li>
								 </ul>
							</div>
						</div>";
						
					} while ($row_ob = oci_fetch_assoc($stmt_obser));
				 }
			
			}
		} else {
			echo "No detectamos un registro con este usuario. Si es un nuevo usuario, por favor procede a <a href='registro_usuario.php'>registrar</a> el usuario para habilitar el ingreso.";
		}
	}
}
