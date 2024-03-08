<?php
require('../conexion.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
  //Esta procesose se realiza para proteger las entradas no numéricas o prevenir ataques de inyección SQL. 

	$documento = $_POST['documento'];

	$sql = "SELECT b.NOMBRES, b.PRI_APELLIDO, b.SEG_APELLIDO, b.IDENTIFICACION, a.FECHA_ASISTENCIA, d.NOMBRE_SEDE 
			FROM APLICACIONES.GYM_ASISTENCIA a 
			JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG  = b.COD_USUARIO_REG 
			JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER 
			JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM 
			WHERE b.IDENTIFICACION = :identificacion";

	$stmt = oci_parse($conn, $sql); 	                                    // Prepara la consulta SQL
	oci_bind_by_name($stmt, ':identificacion', $documento);		            // Vincula los parámetros														    
	oci_execute($stmt);													    // Ejecuta la consulta				
    													
	
	
	//Descripcion en la tabla

	echo '<div class="m-3 d-flex justify-content-between">
				<div>
					<button type="submit" class="btn btn-custom" id="inf_user" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> Informacion de usuario </button>
					<button type="submit" class="btn btn-custom" id="obs_user" data-bs-toggle="modal" data-bs-target="#modal_observa"> Observaciones </button>
					<button type="submit" class="btn btn-custom" id="rutina_user" data-bs-toggle="modal" data-bs-target="#rutina"> Rutina </button>
				</div>
			    <form method="POST" action="csv_rutina_usuario.php">
					<button type="submit" class="btn btn-custom" id="excel" title="Descargar CSV"><i class="fa-solid fa-file-excel"></i><input type="hidden" id="documento" name="documento" value="'.$documento.'"></button>
				</form>	
			</div>
	 		<table class="table table-striped" id="Table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Fecha de Asistencia (D-M-A)</th>
                    <th>Sede</th>
                </tr>
            </thead>
            <tbody>';

    //Itera a través de cada fila de resultados
	while ($row = oci_fetch_assoc($stmt)) {
		echo '<tr>';
		echo '<td>' . $row['NOMBRES'] . ' ' . $row['PRI_APELLIDO'] . ' ' . $row['SEG_APELLIDO'] . '</td>';   
		echo '<td>' . $row['IDENTIFICACION'] . '</td>';
		echo '<td>' . date('d-m-Y', strtotime($row['FECHA_ASISTENCIA'])) . '</td>'; 
		echo '<td>' . $row['NOMBRE_SEDE'] . '</td>'; 
		echo '</tr>';
	}

	echo '</tbody>
        </table>';
}
?>

