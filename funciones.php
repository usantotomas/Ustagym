<?php
require ('conexion.php');
require ('password.php'); 

// Función para autenticar al usuario
function authenticate($username, $password, $conn) {
    // Crea la consulta SQL
    $sql = "SELECT * FROM APLICACIONES.GYM_USER  WHERE NOMBRE_USER = :username";

    // Prepara la consulta SQL
    $stmt = oci_parse($conn, $sql);

    // Vincula los parámetros
    oci_bind_by_name($stmt, ':username', $username);
    //oci_bind_by_name($stmt, ':password', $password);

    // Ejecuta la consulta
    oci_execute($stmt);
	
	// Toma el dato de la consulta y la asigna a la variable
	$user_data = oci_fetch_assoc($stmt);
	
	// Verificar si el usuario existe y la contraseña coincide
    if ($user_data && password_verify($password, $user_data['PASSWORD'])) {
        return $user_data; // Devolver credenciales validas , datos del usuario
    } else {
        return false; // Credenciales inválidas
    }
}


?>