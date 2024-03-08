<?php

// Iniciar la sesión
session_start();

// Verificar si la clave de la sesión está presente
if (!isset($_SESSION['COD_GYM'])) {
    // Redirigir al usuario a la página de inicio
    header('Location: http://172.16.1.60/ustagym_desa/index.php');
    exit;
}


?>


<!DOCTYPE html>
<html lang="es"> <!-- etiqueta de cierre en footer.php --> 
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>USTAGYM</title>
	
  <!-- Enlace CSS de Bootstrap -->
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../bootstrap/icons-1.10.5/font/bootstrap-icons.css">
	
  <!-- fuentes  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
	
  <!-- jQuery -->
   <script src="../jQuery/jquery-3.7.0.min.js"></script>
	
	
  <!-- Estilo personalizado css -->
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/registro_usuario.css">
  <link rel="stylesheet" href="css/ingreso_usuario.css">
  <link rel="stylesheet" href="css/rutina_usuario.css">
  <link rel="stylesheet" href="css/reporte_per.css">
	
   <!-- Font Awesome iconos -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<!--Datatables-->
	<link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet"/>
 	<script src="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.js"></script>
	
	<!--jsdelivr-->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body> <!-- etiqueta de cierre en footer.php -->
  <header>
    <nav class="navbar navbar-expand-lg ">
      <div class="container-fluid">
        <?php // Logo y título en la parte izquierda del header ?>
        <a class="navbar-brand p-1 color" href="#">
          <img  src="../img/logo_usta.png" alt="Logo_usta" width="40" height="40">
           &nbsp; USTAGYM
        </a>

        <?php // Opciones del menú en la parte derecha del header ?>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
          <ul class="navbar-nav text-center">
            <li class="nav-item">
              <a class="nav-link color mx-3" href="ingreso_usuario.php"><i class="bi bi-card-text"></i>&nbsp; Ingreso Usuario</a>
			  <span class="line"></span>
            </li>
            <li class="nav-item">
              <a class="nav-link color mx-3" href="registro_usuario.php"><i class="bi bi-pencil-square"></i>&nbsp; Registro Usuario</a>
			  <span class="line"></span>
            </li>
			<li class="nav-item">
              <a class="nav-link color mx-3" href="rutina_usuario.php"><i class="fas fa-dumbbell"></i>&nbsp; Rutina de usuario</a>
			  <span class="line"></span>
            </li>
			 <li class="nav-item dropdown">
				  <a class="nav-link color mx-3 dropdown-toggle"  id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				  <i class="bi bi-file-bar-graph"></i>&nbsp; Reporte
				  </a>
				  <span class="line"></span>
					  <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
						<li><a class="dropdown-item" href="reporte_per.php">Reporte Asistencia</a></li>
						<li><a class="dropdown-item" href="reporte_user.php">Reporte Usuarios</a></li>
					  </ul>
				</li>
			 <li class="nav-item">
              <a class="nav-link color mx-3" href="https://usta.gitbook.io/ustagym/" title="Manual de usuario" target="_blank"><i class="fa-solid fa-book-open"></i></a>
			  <span class="line"></span>
            </li>
            <li class="nav-item">
              <a class="nav-link color mx-3" href="cerrar_sesion.php"><i class="bi bi-lock-fill"></i>&nbsp; Cerrar Sesión</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

	

