<?php

require('funciones.php');
//validacion de usuario 

$error = ''; // Variable para almacenar el mensaje de error

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
	$user = $_POST['user'];
    $pass = $_POST['password'];
	
	$user_data = authenticate($user, $pass, $conn);  //esta funcion esta construida en funciones.php
	
    if ($user_data) {
		
		session_start();
		
			$_SESSION['COD_USER'] =  $user_data['COD_USER'];
			$_SESSION['COD_GYM'] =  $user_data['COD_GYM'];
			$_SESSION['NOMBRE_USER'] =  $user_data['NOMBRE_USER'];
			
        // Si el usuario es válido, redirigir a home.php
        header('Location: home/ingreso_usuario.php');
        exit;
    } else {
        // Si las credenciales no son válidas, mostrar un mensaje de error
        $error = "<div class='alert alert-warning custom-alert mt-2' role='alert'>
					Usuario o contraseña incorrecto
				  </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- framework bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<!-- fuentes  -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
	<!-- estilo personalizado css -->
	<link rel="stylesheet" href="css_index.css">
</head>
<body> 
	  <div class="container d-flex align-items-center justify-content-center">
		<div class="login-box p-4">
		  <div class="text-center mb-3">
			 <div class=" mx-auto">
				 <img class="img-fluid" src="img/logo_usta.png" alt="Logo" class="mb-3" />
			 </div>
			<h2 class="m-1">USTAGYM</h4> 
		  </div>
		  <form action="" method="post">
			<div class="form-group m-4 ">
			  <input class="input" type="text" id="user" name="user" autocomplete="off" required="">
			  <label class="user-label">Usuario</label>
			</div>
			<div class="form-group m-4">
			  <input class="input" type="password" id="password" name="password" autocomplete="off" required="">
				<label  class="user-label">Contraseña</label>
			</div>
			 <div class="text-center"> 
				<button type="submit " class="btn btn-light">Iniciar sesión</button>
				 
				<?php if($error != ''): echo $error; endif; ?>  <!-- mensaje de error de contraseña en caso de ser erronea --> 
				 
			  </div>
		  </form>
		</div>
	  </div>
</body>
	<script src="bootstrap/js/bootstrap.min.js"></script>
</html>

