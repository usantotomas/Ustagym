<?php
require('funciones.php');
require('password.php');

// Verificar si se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sede = $_POST['sede'];

    // Encriptar la contraseña con BCRYPT
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Preparar la consulta SQL para insertar el usuario y la contraseña encriptada
    $query = 'INSERT INTO APLICACIONES.GYM_USER (COD_GYM, NOMBRE_USER, PASSWORD) VALUES (:sede, :username, :password)';

    $statement = oci_parse($conn, $query);

    // Asociar las variables a los marcadores de posición en la consulta
    oci_bind_by_name($statement, ':sede', $sede);
    oci_bind_by_name($statement, ':username', $username);
    oci_bind_by_name($statement, ':password', $hashedPassword);

    // Ejecutar la consulta
    if (oci_execute($statement)) {
        echo 'Cuenta creada con éxito';
    } else {
        echo 'Error al crear la cuenta';
    }

    // Cerrar el statement
    oci_free_statement($statement);
    
    // Cerrar la conexión
    oci_close($conn);

    // Detener la ejecución del script para evitar mostrar el formulario nuevamente
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- framework bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" 
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form action="" method="post" class="mt-2">
			<div class='alert alert-warning custom-alert mt-2' role='alert'>
				Este metodo es solo para uso del personal de TIC 
			</div>
		 <h2>Nueva cuenta</h2>
          <div class="form-group mt-3">
            <label for="username">Nombre de usuario:</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="sede">Sede:</label>
            <select class="form-select" name="sede" id="sede" required>
              <option value="1">Sede Principal</option>
              <option value="2">Sede Angelico</option>
              <option value="3">Sede Aquinate</option>
              <option value="4">Sede Campus</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary mt-4">Crear cuenta</button>
        </form>
      </div>
    </div>
  </div>
  
</body>
	<script src="bootstrap/js/bootstrap.min.js"></script>
</html>
