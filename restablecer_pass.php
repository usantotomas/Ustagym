<?php

require('funciones.php');
require('password.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    
    // Consulta para verificar si el usuario existe
    $query = 'SELECT * FROM APLICACIONES.GYM_USER WHERE NOMBRE_USER = :username';
    $statement = oci_parse($conn, $query);
    oci_bind_by_name($statement, ':username', $username);
    oci_execute($statement);
    $result = oci_fetch_array($statement, OCI_ASSOC);

    if ($result) {
        // El usuario existe, por lo que puedes actualizar la contraseña
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        
        // Consulta para actualizar la contraseña del usuario
        $query = 'UPDATE APLICACIONES.GYM_USER SET PASSWORD = :password WHERE NOMBRE_USER = :username';
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ':username', $username);
        oci_bind_by_name($statement, ':password', $hashedPassword);
        
        // Ejecuta la consulta
        oci_execute($statement);

        echo 'Contraseña actualizada con éxito';
    } else {
        // El usuario no existe
        echo 'El usuario no existe';
    }
    
    oci_close($conn);
}
?>





<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- framework bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form class="mt-4" action="" method="post">
			<div class='alert alert-warning custom-alert mt-2' role='alert'>
				Este metodo es solo para uso del personal de TIC 
			</div>
		<h2>Restablecimiento de contraseña</h2>
          <div class="form-group mt-3">
            <label for="username">Nombre de usuario:</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="form-group">
            <label for="new_password">Nueva Contraseña:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
          </div>
          <button type="submit" class="btn btn-primary mt-2">Actualizar Contraseña</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
