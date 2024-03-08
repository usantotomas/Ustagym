<?php
require('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
    $t_user = $_POST['t_user'];
	
    $ins_vigente = '';
	
	$ins_vigente = ($t_user == 'Activo') ? 'Vigente' : (($t_user == 'Inactivo') ? 'Vencido' : '');  //esto se realiza para crear una columna del estado de incripcion 

	
    $sql = "SELECT * FROM APLICACIONES.GYM_USUARIOS_REG";
    
    if ($t_user == 'Activo') {
		
        $sql .= " WHERE TRUNC(inicio_inscripcion) <= TRUNC(SYSDATE) AND TRUNC(final_inscripcion) >= TRUNC(SYSDATE)"; 
		
    } else if ($t_user == 'Inactivo') {
		
        $sql .= " WHERE TRUNC(inicio_inscripcion) > TRUNC(SYSDATE) OR TRUNC(final_inscripcion) < TRUNC(SYSDATE)";
		
    } else if ($t_user == 'Todos'){
		
		$sql = "SELECT  TIP_IDENTIFICACION, 
				IDENTIFICACION,
				NOMBRES ,
				PRI_APELLIDO,
				SEG_APELLIDO,
				SEXO,
				CORREO,
				TEL_CELULAR,
				DEPENDENCIA,
				UNIDAD,
				TIPO_USUARIO,
				EDAD,
				CLASIFICACION,
				inicio_inscripcion, 
			   final_inscripcion,
			   CASE 
				   WHEN TRUNC(SYSDATE) BETWEEN TRUNC(inicio_inscripcion) AND TRUNC(final_inscripcion) THEN 'Vigente'
				   ELSE 'Vencido'
			   END as INSCRIPCION
				FROM APLICACIONES.GYM_USUARIOS_REG";
	}
    
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
	
	
	
	// cuenta el numero de usuarios dependiendo si es activo o inactivos. 
	
	if ($t_user == 'Activo'){
			
		$sql_two = "SELECT COUNT(*) as total
					FROM (
					  SELECT CASE 
							   WHEN TRUNC(SYSDATE) BETWEEN TRUNC(inicio_inscripcion) AND TRUNC(final_inscripcion) THEN 'Activos'
							   ELSE 'Inactivos'
							 END as estado_suscripcion
					  FROM APLICACIONES.GYM_USUARIOS_REG
					) 
					WHERE estado_suscripcion = 'Activos'";	
		
	}
	
	else if($t_user == 'Inactivo'){
		
		$sql_two = "SELECT COUNT(*) as total
					FROM (
					  SELECT CASE 
							   WHEN TRUNC(SYSDATE) BETWEEN TRUNC(inicio_inscripcion) AND TRUNC(final_inscripcion) THEN 'Activos'
							   ELSE 'Inactivos'
							 END as estado_suscripcion
					  FROM APLICACIONES.GYM_USUARIOS_REG
					) 
					WHERE estado_suscripcion = 'Inactivos'";	
		
	}else {
		
		$sql_two = "SELECT COUNT(*) as total FROM APLICACIONES.GYM_USUARIOS_REG";
	}
	
	
	$stmt_total = oci_parse($conn, $sql_two);
	oci_execute($stmt_total);
    $total         = oci_fetch_assoc($stmt_total);
    $total_records = $total['TOTAL'];
		
	
	

    echo '<div class="m-2 d-flex justify-content-end">
                <form method="post" action="csv_reporte_user.php">
                    <input type="hidden" id="estado" name="estado" value="' . $t_user . '">
                    <button type="submit" class="btn btn-custom" id="excel" title="Descargar CSV" data-tippy-content="Tooltip en la parte superior"><i class="fa-solid fa-file-excel"></i></button>
                </form>    
            </div>
			<table class="table table-striped table-sm small" id="Table">
            <thead>
                <tr>
                    <th>Tipo de identificacion</th>
                    <th>Identificacion</th>
                    <th>Nombre</th>
                    <th>Sexo</th>
                    <th>Correo</th>
                    <th>Telefono C.</th>
                    <th>Dependencia</th>
                    <th>Unidad</th>
                    <th>Tipo usuario</th>
                    <th>Edad</th>
                    <th>Clasificacion</th>
                    <th>Inscripcion</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($row = oci_fetch_assoc($stmt)) {
        echo '<tr>';
        echo '<td>' . $row['TIP_IDENTIFICACION'] . '</td>';
        echo '<td>' . $row['IDENTIFICACION'] . '</td>';
        echo '<td>' . $row['NOMBRES'] . ' ' . $row['PRI_APELLIDO'] . ' ' . $row['SEG_APELLIDO'] . '</td>';
        echo '<td>' . $row['SEXO'] . '</td>';
        echo '<td>' . $row['CORREO'] . '</td>';
        echo '<td>' . $row['TEL_CELULAR'] . '</td>';
        echo '<td>' . $row['DEPENDENCIA'] . '</td>';
        echo '<td>' . $row['UNIDAD'] . '</td>';
        echo '<td>' . $row['TIPO_USUARIO'] . '</td>';
        echo '<td>' . $row['EDAD'] . '</td>';
        echo '<td>' . $row['CLASIFICACION'] . '</td>';
        echo '<td>' . (($ins_vigente == '') ? $row['INSCRIPCION'] : $ins_vigente) . '</td>';
        echo '</tr>';
    }

	// descripcion segunda tabla
	
    echo '<table class="table-striped table-hover table" style="width: 60%;">
          <thead>
            <tr>
              <th scope="col">Tipo de dato</th>
              <th scope="col" class="text-center">Cantida personas registradas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Total de usuarios:</td>
              <td class="text-center">' . $total_records . '</td>
            </tr>
          </tbody>
        </table>';
}




?>