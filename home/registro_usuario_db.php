<?php
/*echo "<pre>";
    print_r($_POST);
    echo "</pre>";*/


/*echo "<pre>";
    print_r($_FILES);
    echo "</pre>"; */

require('../conexion.php');
require('funciones_query.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

	 //Esta procesose se realiza para proteger las entradas no numéricas o prevenir ataques de inyección SQL. 

	$t_identificiacion  = filter_var($_POST['t_identificiacion'], FILTER_SANITIZE_STRING);
	$res_identificacion = $_POST['res_identificacion'];
	$nobres             = filter_var($_POST['nombres'], FILTER_SANITIZE_STRING);
	$p_apellido         = filter_var($_POST['p_apellido'], FILTER_SANITIZE_STRING);
	$s_apellido         = filter_var($_POST['s_apellido'], FILTER_SANITIZE_STRING);
	$sexo               = filter_var($_POST['sexo'], FILTER_SANITIZE_STRING);
	$mail               = filter_var($_POST['mail'], FILTER_SANITIZE_STRING);
	$t_celular          = filter_var($_POST['t_celular'], FILTER_VALIDATE_INT);
	$dependencia        = filter_var($_POST['dependencia'], FILTER_SANITIZE_STRING);
	$unidad             = filter_var($_POST['unidad'], FILTER_SANITIZE_STRING);
	$tipo_usuario       = filter_var($_POST['tipo_usuario'], FILTER_SANITIZE_STRING);
	$fec_nacimiento     = filter_var($_POST['edad'], FILTER_VALIDATE_INT);
	$clasificacion = filter_var($_POST['clasificacion'][0], FILTER_SANITIZE_STRING);
	
	
	
   $sql = "SELECT IDENTIFICACION FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :identificacion"; // se verifica primero si el numero de documento no ha sido registrado
   
	
   $stmt = oci_parse($conn, $sql);
    // Vincula los parámetros
	oci_bind_by_name($stmt, ':identificacion', $res_identificacion);
    // Ejecuta la consulta
	oci_execute($stmt);
    // Toma el dato de la consulta y la asigna a la variable
	$user_data = oci_fetch_assoc($stmt);

	if ($user_data['IDENTIFICACION'] == $res_identificacion) {  
		
		header('Location: registro_usuario.php?user=false'); 
		;
		
	}else{
	

	if ($t_identificiacion === false || $res_identificacion === false || $nobres === false || $p_apellido === false || $s_apellido === false || $sexo === false || $dependencia === false || $unidad === false || $tipo_usuario === false || $clasificacion === false) {
		echo 'Valor no permitido'; //este caso se presenta en caso de que el tipo de valor en los campos del formulario sea manipulado en el codigo fuente

	} else {
		if (empty($t_identificiacion) || empty($res_identificacion) || empty($nobres) || empty($p_apellido) || empty($s_apellido) || empty($sexo) || empty($dependencia) || empty($tipo_usuario) || empty($clasificacion)) {
			echo 'faltan datos por ingresar';
		} else {
			
			// proceso si el usuario adjunto archivo 

			if (isset($_FILES["file_archivo"]) && $_FILES["file_archivo"]["error"] != UPLOAD_ERR_NO_FILE) {
				$archivo = $_FILES["file_archivo"];
				
					// Verificar si hay algún error durante la carga del archivo
				if ($archivo["error"] > 0) {
					echo "Error al cargar el archivo: " . $archivo["error"];
				} else {
						// Obtener información del archivo
					$nombreArchivo = $archivo["name"];
					$tipoArchivo = $archivo["type"];
					$tamanioArchivo = $archivo["size"];
					$rutaArchivoTemporal = $archivo["tmp_name"];

						// Especificar la ubicación y el nombre deseado del archivo en el servidor
					$directorioDestino = "file/";
					$rutaArchivoDestino = $directorioDestino . $res_identificacion . '_CF.pdf';

						// Mover el archivo a la ubicación deseada en el servidor
					if (move_uploaded_file($rutaArchivoTemporal, $rutaArchivoDestino)) {
							
								// Esta funcion ejecuta la consulta para insertar los datos en la base da datos - funcion construida en funciones_query.php 

						$resultado = insertar_datos(
							$conn,
							$res_identificacion,
							$nobres,
							$p_apellido,
							$s_apellido,
							$sexo,
							$mail,
							$t_celular,
							$dependencia,
							$unidad,
							$tipo_usuario,
							$clasificacion,
							$rutaArchivoDestino,
							$fec_nacimiento,
							$t_identificiacion
						);

						if ($resultado === true) { // Si el query es exitoso , redirecciona a registro.php y pasa un parámetro GET para mostrar el modal de 'usuario registrado'

							header('Location: registro_usuario.php?showModal=true');
							exit;
						} else {
							echo $resultado;  // en caso de error , mostrara el error correspondiente
						}
					} else {
						echo "Error al mover el archivo a la ubicación deseada.";
					}
				}
			} else {
				
			  // No hay archivo adjunto, así que continuamos con la inserción sin archivo
				
			  // Esta funcion ejecuta la consulta para insertar los datos en la base da datos - funcion construida en funciones_query.php
				$resultado = insertar_datos(
					$conn,
					$res_identificacion,
					$nobres,
					$p_apellido,
					$s_apellido,
					$sexo,
					$mail,
					$t_celular,
					$dependencia,
					$unidad,
					$tipo_usuario,
					$clasificacion,
					$rutaArchivoDestino = '',
					$fec_nacimiento,
					$t_identificiacion
				);

				if ($resultado === true) { // Si el query es exitoso , redirecciona a registro.php y pasa un parámetro GET para mostrar el modal de 'usuario registrado'

					header('Location: registro_usuario.php?showModal=true');
					exit;
				} else {
					echo $resultado;  // en caso de error , mostrara el error correspondiente
				}
			}
		}
	}
 }

}