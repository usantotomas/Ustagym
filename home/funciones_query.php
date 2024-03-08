<?php

require('../conexion.php');
session_start();
// funcion para insertar los datos al registrar un nuevo usuario, uso en registro_usuario_db.php

function insertar_datos($conn, $res_identificacion, $nobres, $p_apellido, $s_apellido, $sexo, $mail, $t_celular, $dependencia, $unidad, $tipo_usuario, $clasificacion, $rutaArchivoDestino, $fec_nacimiento, $t_identificiacion)
{
    
    // Creacion la consulta SQL
    $sql = "INSERT INTO APLICACIONES.GYM_USUARIOS_REG (IDENTIFICACION, NOMBRES, PRI_APELLIDO, SEG_APELLIDO, SEXO, CORREO, TEL_CELULAR, DEPENDENCIA, UNIDAD, TIPO_USUARIO, INICIO_INSCRIPCION, FINAL_INSCRIPCION, CLASIFICACION, FORMATO, EDAD, TIP_IDENTIFICACION
    ) VALUES (
        :res_identificacion, 
        :nobres, 
        :p_apellido, 
        :s_apellido, 
        :sexo, 
        :mail, 
        :t_celular, 
        :dependencia, 
        :unidad, 
        :tipo_usuario, 
        SYSDATE, 
        ADD_MONTHS(SYSDATE, 6), 
        :clasificacion, 
        :formato, 
        :fec_nacimiento, 
        :t_identificiacion
    )";
    
    // Prepara la consulta SQL
    $stmt = oci_parse($conn, $sql);
    
    if (!$stmt) {
        $m = oci_error($conn);
        trigger_error('No se pudo parsear la consulta. ' . $m['message'], E_USER_ERROR);
    }
    
    
    // Vincula los parámetros
    oci_bind_by_name($stmt, ':res_identificacion', $res_identificacion);
    oci_bind_by_name($stmt, ':nobres', $nobres);
    oci_bind_by_name($stmt, ':p_apellido', $p_apellido);
    oci_bind_by_name($stmt, ':s_apellido', $s_apellido);
    oci_bind_by_name($stmt, ':sexo', $sexo);
    oci_bind_by_name($stmt, ':mail', $mail);
    oci_bind_by_name($stmt, ':t_celular', $t_celular);
    oci_bind_by_name($stmt, ':dependencia', $dependencia);
    oci_bind_by_name($stmt, ':unidad', $unidad);
    oci_bind_by_name($stmt, ':tipo_usuario', $tipo_usuario);
    oci_bind_by_name($stmt, ':clasificacion', $clasificacion);
    oci_bind_by_name($stmt, ':formato', $rutaArchivoDestino);
    oci_bind_by_name($stmt, ':fec_nacimiento', $fec_nacimiento);
    oci_bind_by_name($stmt, ':t_identificiacion', $t_identificiacion);
    
    
    // Ejecuta la consulta
    $r = oci_execute($stmt);
    if (!$r) {
        $m = oci_error($stmt);
        
        // devuelve el mensaje de error específico
        return 'No se pudo ejecutar la consulta: ' . $m['message'];
    }
    
    // si todo fue bien, devuelve true
    return true;
    
    
}


//proceso para renovar registro / ingreso_usuario.php

if (isset($_POST['action']) && $_POST['action'] == 'renovar' && isset($_POST['documento']) && !empty($_POST['documento'])) {
		
		
		
		$documento = $_POST['documento'];    										// documento recibido 			
        $inicio_inscripcion = date('d-M-y');                                        // Fecha actual 
        $final_inscripcion = date('d-M-y', strtotime("+6 months")); 				// Fecha 6 meses después
        
	
		//actualizacion edad 
	
		$sql_edad = 'SELECT * FROM SINU.BAS_TERCERO WHERE NUM_IDENTIFICACION = :documento';
	
	
		$stmt = oci_parse($conn, $sql_edad);
	
		// Vincula el parámetro
		oci_bind_by_name($stmt, ':documento', $documento);
	
		// Ejecuta la consulta
		oci_execute($stmt);
		
			// Comprobar si existen datos utilizando oci_fetch
			if ($fila = oci_fetch_assoc($stmt)) {
				
				$fec_nacimiento = $fila['FEC_NACIMIENTO'];
				
				if ($fec_nacimiento) {
					
				$fechaNacimiento = DateTime::createFromFormat('d-M-y', $fec_nacimiento); // convierte la cadena de fecha a un objeto DateTime

				$fechaActual = new DateTime(); // obtén la fecha actual

				$intervalo = $fechaActual->diff($fechaNacimiento); // calcula la diferencia entre las fechas

				$edad = $intervalo->y; // obtén el número de años de la diferencia

					} else {

					$edad = 'Desconocido';

					}
				
				
				$sql_ubdate = "UPDATE APLICACIONES.GYM_USUARIOS_REG SET EDAD = :edad WHERE IDENTIFICACION = :documento"; //actualiza la edad


				$stmt = oci_parse($conn, $sql_ubdate);

				// Vincula el parámetro
				oci_bind_by_name($stmt, ':edad', $edad);
				oci_bind_by_name($stmt, ':documento', $documento);

				// Ejecuta la sentencia
				$edad_ubdate = oci_execute($stmt); 
				
				
			}
	

        // Actualiza los datos inscripcion
        $sql = "UPDATE APLICACIONES.GYM_USUARIOS_REG SET INICIO_INSCRIPCION=TO_DATE(:inicio_inscripcion, 'dd-mon-yy'), FINAL_INSCRIPCION=TO_DATE(:final_inscripcion, 'dd-mon-yy') WHERE IDENTIFICACION = :documento";

        // Preparar la sentencia
        $stmt = oci_parse($conn, $sql);
        if (!$stmt) {
            echo 'error';
            exit;
        }

        // Vincula los parámetros
        oci_bind_by_name($stmt, ':inicio_inscripcion', $inicio_inscripcion);
        oci_bind_by_name($stmt, ':final_inscripcion', $final_inscripcion);
		oci_bind_by_name($stmt, ':documento', $documento);
	
        // Ejecuta la sentencia
        if (oci_execute($stmt)) {
			
			        if (oci_num_rows($stmt) == 0) {   // no se actualizo ningun dato
						echo 'Error documento';
						exit;
                    }
            echo 'actualizado';
        } else {
			error_log($e['message']);
            echo 'error';
        }
}


//proceso para omitir el acceso denegado / ingreso_usuario.php

if (isset($_POST['action']) && $_POST['action'] == 'omitir_ing_d' && isset($_POST['documento']) && !empty($_POST['documento'])) {
	
		$documento = $_POST['documento'];
		
		$sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :documento";
				
		
		$stmt = oci_parse($conn, $sql);                     											// Prepara la consulta SQL
		oci_bind_by_name($stmt, ':documento', $documento);  											// Vincula los parámetros
		oci_execute($stmt);                                 											// Ejecuta la consulta
		$user_data = oci_fetch_assoc($stmt); 															// Toma el dato de la consulta y la asigna a la variable
		
		if ($user_data['COD_USUARIO_REG'] == ''){
			die;
		} 	
	
		$cod_user  = $_SESSION['COD_USER']; //es la sede donde realiza el ingreso
	
		$sql_t = "INSERT INTO APLICACIONES.GYM_ASISTENCIA  (COD_USER, COD_USUARIO_REG , FECHA_ASISTENCIA)
					VALUES (:cod_user, :cod_usuario_reg, SYSDATE)";

		$stmt_t = oci_parse($conn, $sql_t);                    							    // Prepara la consulta SQL
		oci_bind_by_name($stmt_t, ':cod_usuario_reg', $user_data['COD_USUARIO_REG']);       // Vincula los parámetros
		oci_bind_by_name($stmt_t, ':cod_user', $cod_user);  				 				// Vincula los parámetros
		$execute_result = oci_execute($stmt_t);                                					            // Ejecuta la consulta
	
	
		if ($execute_result) {
        	// La ejecución fue exitosa
			echo "ingresado";
    	} else {
        	// Hubo un error en la ejecución
        $error = oci_error($stmt_t);
        	echo "Error en oci_execute: " . $error['message'];
    }
}

// proceso al dar click en 'informacion de usuario' en 'rutina_usuario.php'

if (isset($_GET['action']) && $_GET['action'] == 'infoUsuario' && isset($_GET['documento']) && !empty($_GET['documento'])) {
	
		$documento = $_GET['documento'];
		
		$sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :documento";
				
		
		$stmt = oci_parse($conn, $sql);                     											// Prepara la consulta SQL
		oci_bind_by_name($stmt, ':documento', $documento);  											// Vincula los parámetros
		oci_execute($stmt);                                 											// Ejecuta la consulta
		$user_data = oci_fetch_assoc($stmt); 	
	
		echo '
			  <p class="mt-2 mb-0">Nombre: </p><input class="form-control" type="text" value="'.$user_data['NOMBRES'] .' '.$user_data['PRI_APELLIDO'].' '.$user_data['SEG_APELLIDO'].'" disabled>
			  <p class="mt-2 mb-0">Identificacion: </p><input class="form-control " type="text" value="'.$user_data['TIP_IDENTIFICACION'] .' '.$user_data['IDENTIFICACION'].'" disabled>
			  <p class="mt-2 mb-0">Sexo: </p><input class="form-control " type="text" value="'.$user_data['SEXO'] .'" disabled>
			  <p class="mt-2 mb-0">Correo: </p><input class="form-control " type="text" value="'.$user_data['CORREO'] .'" disabled>
			  <p class="mt-2 mb-0">Telefono celular: </p><input class="form-control" type="text" value="'.$user_data['TEL_CELULAR'] .'" disabled>
			  <p class="mt-2 mb-0"">Dependencia: </p> <input class="form-control " type="text" value="'.$user_data['DEPENDENCIA'] .'" disabled>
			  <p class="mt-2 mb-0"">Unidad: </p><input class="form-control " type="text" value="'.$user_data['UNIDAD'] .'" disabled>
			  <p class="mt-2 mb-0"">Tipo Uusario: </p><input class="form-control " type="text" value="'.$user_data['TIPO_USUARIO'] .'" disabled>
			  <p class="mt-2 mb-0"">Inscripcion: </p><input class="form-control " type="text" value="'.$user_data['INICIO_INSCRIPCION'] .' / '.$user_data['FINAL_INSCRIPCION'].'" disabled>
			  <p class="mt-2 mb-0"">Clasificacion: </p><input class="form-control " type="text" value="'.$user_data['CLASIFICACION'] .'" disabled>
			  <p class="mt-2 mb-0"">Edad: </p><input class="form-control " type="text" value="'.$user_data['EDAD'] .'" disabled>
 			  <button class="btn btn-custom mt-2" '.(!$user_data['FORMATO'] ? 'disabled' : '').'><a href="'.($user_data['FORMATO'] ? $user_data['FORMATO'] : '').'"target="_blank" style="color: inherit; text-decoration: none; outline: none;"><i class="fa-solid fa-file-arrow-down"></i> Formato condicion fisica</a></button>';
		
		$identificaccion = $user_data['IDENTIFICACION'];
		
		if($user_data['FORMATO'] == ''){
				
			echo '<form id="form_adj" enctype="multipart/form-data">
					<div class="mb-3 mt-2">
				  		<label for="formFile" class="form-label">Adjuntar Condicion fisica</label>
						<input class="form-control" type="hidden" id="identificacion" value="'.$identificaccion.'">
				  		<input class="form-control" type="file" id="adj_archivo" accept=".pdf">
				  		<input class="btn btn-custom mt-2" type="submit" id="submit_button" value="Enviar">
					</div>
				</form>';
			
		}
	
}


//proceso al dar click en 'enviar' dentro del modal 'informacion usuario' en en 'rutina_usuario.php'


if (isset($_POST['action']) && $_POST['action'] == 'upload_file') {
    if (isset($_FILES['file']) && isset($_POST['identificacion'])) {
        // Extraer los datos del archivo
        $file = $_FILES['file'];
        $identificacion = $_POST['identificacion'];

        // Validar que el archivo es un PDF
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($fileType == 'pdf') {
            // Validar el tamaño del archivo
            if ($file['size'] <= 819200) {  // 800KB en bytes
                // Ruta de destino del archivo
                $destPath = 'file/';
                $destFile = $destPath . $identificacion . '_CF.pdf';

                // Mover el archivo a la ruta de destino
                if (move_uploaded_file($file['tmp_name'], $destFile)) {

                    // Preparar la consulta SQL
                    $query = oci_parse($conn, "UPDATE APLICACIONES.GYM_USUARIOS_REG SET FORMATO = :destFile WHERE IDENTIFICACION = :identificacion");

                    // Enlazar las variables de PHP a los marcadores de posición en la consulta
                    oci_bind_by_name($query, ":destFile", $destFile);
                    oci_bind_by_name($query, ":identificacion", $identificacion);

                    // Ejecutar la consulta
                    $result = oci_execute($query);

                    // Verificar si la consulta fue exitosa
                    if ($result) {
                        echo "ingresado";
                    } else {
                        $e = oci_error($query);  // Para errores de oci_execute, se pasa el recurso de la declaración
                        echo htmlentities($e['message']);
                    }
                } else {
                    echo "Ocurrió un error al subir el archivo.";
                }
            } else {
                echo "El archivo es muy pesado, maximo 800kb.";
            }
        } else {
            echo "El archivo no es un PDF.";
        }
    } else {
        echo "No se ha recibido ningún archivo o identificación.";
    }
} 






// proceso al dar click en 'Observaciones' en 'rutina_usuario.php' , muestra la observaciones realizadas (si existe).

if (isset($_GET['action']) && $_GET['action'] == 'obserUsuario' && isset($_GET['documento']) && !empty($_GET['documento'])) {

    $documento = $_GET['documento'];
    $observacion = '';

    $sql = "SELECT * FROM APLICACIONES.GYM_OBSERVACION a 
            JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG 
            WHERE b.IDENTIFICACION  = :documento ";
            
    
    $stmt = oci_parse($conn, $sql);                                                    // Prepara la consulta SQL
    oci_bind_by_name($stmt, ':documento', $documento);                                  // Vincula los parámetros
    oci_execute($stmt);                                                                 // Ejecuta la consulta
        
    $row = oci_fetch_assoc($stmt);

    if ($row) {                                                                        // Existen datos en la consulta

        do {
            $observacion = $row['DES_OBSERVACION'];

            echo "<div class='card p-2 mb-3'>
                        <div class='card-header'>".$row['FECHA_OBSERVACION']."</div>
                            <div class='card-body'>
                                <div class='card-text'>
                                    <h6>".$row['NOMBRE_INSTRUCTOR']."</h6>
                                    <p>".$row['DES_OBSERVACION']."</p>
                                </div>
                            </div>
                       </div>";
        } while ($row = oci_fetch_assoc($stmt));
    } else {

        echo "No se encontraron registros.";
    }
    
}


//proceso al añadir una observacion en rutina_usuario.php al dar click en 'Añadir observacion' en  observaciones.

if (isset($_POST['action']) && $_POST['action'] == 'añadir_observacion' && 
	isset($_POST['documento']) && !empty($_POST['documento']) && 
	isset($_POST['cod_user']) && !empty($_POST['cod_user']) &&
    isset($_POST['instructor']) && !empty($_POST['instructor']) &&
    isset($_POST['observacion']) && !empty($_POST['observacion'])) {
	
		$documento = $_POST['documento'];
		$cod_user = $_POST['cod_user'];
		$instructor = $_POST['instructor'];
		$observacion = $_POST['observacion'];
			
		$sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :documento";
		
		$stmt = oci_parse($conn, $sql);                     											// Prepara la consulta SQL
		oci_bind_by_name($stmt, ':documento', $documento);  											// Vincula los parámetros
		oci_execute($stmt);                                 											// Ejecuta la consulta
		$user_data = oci_fetch_assoc($stmt); 	
		
		if(!$user_data) {
			echo "Error: No se encontró el usuario.";
			return; // Termina la ejecución del código 
		}
	
	
		$cod_usuario_reg = $user_data['COD_USUARIO_REG'];
	
		
		$sql_insert = "INSERT INTO APLICACIONES.GYM_OBSERVACION(COD_USUARIO_REG, COD_USER, FECHA_OBSERVACION, NOMBRE_INSTRUCTOR, DES_OBSERVACION) VALUES (:cod_usuario_reg, :cod_user, SYSDATE, :instructor, :observacion)";
	
		$stmt_insert = oci_parse($conn, $sql_insert);												// Prepara la consulta SQL
		oci_bind_by_name($stmt_insert, ':cod_usuario_reg', $cod_usuario_reg);						// Vincula los parámetros
		oci_bind_by_name($stmt_insert, ':cod_user', $cod_user);
		oci_bind_by_name($stmt_insert, ':instructor', $instructor);
		oci_bind_by_name($stmt_insert, ':observacion', $observacion);
	
		$execute = oci_execute($stmt_insert);																  // Ejecuta la consulta
		
		if($execute) {
			echo "insertado";
		} else {
			echo "Error al insertar el registro.";
		}
	
		
}


//proceso al añadir la rutina del usuario en rutina_usuario.php al dar click en 'Añadir rutina' en  rutina.

if (
	isset($_GET['action']) && $_GET['action'] == 'agreRutina' && 
	isset($_GET['documento']) && !empty($_GET['documento']) && 
	isset($_GET['lunes']) &&
    isset($_GET['martes']) &&
    isset($_GET['miercoles']) &&
    isset($_GET['jueves']) &&
    isset($_GET['viernes']) &&
    isset($_GET['sabado'])
) {
	
		$documento = $_GET['documento'];
		$lunes     = $_GET['lunes'];
		$martes    = $_GET['martes'];
		$miercoles = $_GET['miercoles'];
		$jueves    = $_GET['jueves'];
		$viernes   = $_GET['viernes'];
		$sabado    = $_GET['sabado'];
		
	
		//consulta el codigo del usuario
	
		$sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :documento";
		
		$stmt = oci_parse($conn, $sql);                     											// Prepara la consulta SQL
		oci_bind_by_name($stmt, ':documento', $documento);  											// Vincula los parámetros
		oci_execute($stmt);                                 											// Ejecuta la consulta
		$user_data = oci_fetch_assoc($stmt); 	
		
		if(!$user_data) {
			echo "Error: No se encontró el usuario.";
			return; // Termina la ejecución del código 
		}
	
		$cod_usuario_reg = $user_data['COD_USUARIO_REG'];					
	
	
	
		//verifica si ya existe una rutina , si existe, se realiza un update. 
	
		$sql_update ="SELECT * FROM APLICACIONES.GYM_RUTINA WHERE COD_USUARIO_REG = :cod_user";
		
		$stmt_update = oci_parse($conn, $sql_update);                     									// Prepara la consulta SQL
		oci_bind_by_name($stmt_update, ':cod_user', $cod_usuario_reg);  											// Vincula los parámetros
		oci_execute($stmt_update); 
	
		$row = oci_fetch_assoc($stmt_update);
	
		if ($row) {
			
				$query_update = "UPDATE APLICACIONES.GYM_RUTINA 
								SET LUNES = :lunes, 
								MARTES = :martes,
								MIERCOLES = :miercoles, 
                   				JUEVES = :jueves,
                   				VIERNES = :viernes, 
                   				SABADO = :sabado
               					WHERE COD_USUARIO_REG = :cod_user";

			$stmt_update_q = oci_parse($conn, $query_update);												// Prepara la consulta SQL
			oci_bind_by_name($stmt_update_q, ':cod_user', $cod_usuario_reg);						// Vincula los parámetros
			oci_bind_by_name($stmt_update_q, ':lunes', $lunes);
			oci_bind_by_name($stmt_update_q, ':martes', $martes);
			oci_bind_by_name($stmt_update_q, ':miercoles', $miercoles);
			oci_bind_by_name($stmt_update_q, ':jueves', $jueves);
			oci_bind_by_name($stmt_update_q, ':viernes', $viernes);
			oci_bind_by_name($stmt_update_q, ':sabado', $sabado);

			$execute = oci_execute($stmt_update_q);															// Ejecuta la consulta
			
			if($execute) {
				echo "insertado";
			} else {
				echo "Error al insertar el registro.";
			}
			
		} else {
				
			$sql_insert = "INSERT INTO APLICACIONES.GYM_RUTINA  (COD_USUARIO_REG, LUNES, MARTES, MIERCOLES, JUEVES, VIERNES, SABADO)VALUES (:cod_usuario_reg, :lunes, :martes,:miercoles, :jueves,:viernes,:sabado)";

			$stmt_insert = oci_parse($conn, $sql_insert);												// Prepara la consulta SQL
			oci_bind_by_name($stmt_insert, ':cod_usuario_reg', $cod_usuario_reg);						// Vincula los parámetros
			oci_bind_by_name($stmt_insert, ':lunes', $lunes);
			oci_bind_by_name($stmt_insert, ':martes', $martes);
			oci_bind_by_name($stmt_insert, ':miercoles', $miercoles);
			oci_bind_by_name($stmt_insert, ':jueves', $jueves);
			oci_bind_by_name($stmt_insert, ':viernes', $viernes);
			oci_bind_by_name($stmt_insert, ':sabado', $sabado);

			$execute = oci_execute($stmt_insert);															// Ejecuta la consulta

			if($execute) {
				echo "insertado";
			} else {
				echo "Error al insertar el registro.";
			}

		}
	
}


//proceso al para ver la rutina del usuario

if(isset($_GET['action']) && $_GET['action'] == 'rutina_user' && isset($_GET['documento']) && !empty($_GET['documento'])){
	
	$documento = $_GET['documento'];
	
		$sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :documento";
		
		$stmt = oci_parse($conn, $sql);                     											// Prepara la consulta SQL
		oci_bind_by_name($stmt, ':documento', $documento);  											// Vincula los parámetros
		oci_execute($stmt);                                 											// Ejecuta la consulta
		$user_data = oci_fetch_assoc($stmt); 
	
		if(!$user_data) {
			echo "Error: No se encontró el usuario.";
			return; // Termina la ejecución del código 
		}
		
		$cod_usuario_reg = $user_data['COD_USUARIO_REG'];
		
		$sql_selec = "SELECT * FROM APLICACIONES.GYM_RUTINA WHERE COD_USUARIO_REG = :cod_user";
	
		$stmt_select = oci_parse($conn, $sql_selec);                     									// Prepara la consulta SQL
		oci_bind_by_name($stmt_select, ':cod_user', $cod_usuario_reg);  									// Vincula los parámetros
		oci_execute($stmt_select);                                 											// Ejecuta la consulta
		$user = oci_fetch_assoc($stmt_select); 
		
		echo json_encode($user);
		
	
}
