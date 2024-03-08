 <?php
require('../conexion.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
    $fecha_ini   = $_POST['fecha_ini'];
    $fecha_hasta = $_POST['fecha_hasta'];
    
    $cod_sede = '';
    
    if ($_POST['sede'] != 5) {
        
        $cod_sede = 'AND d.COD_GYM = :cod_sede'; //se define la condicion si se selecicona una sede 
    }
    
    $fecha_ini_v   = '';
    $fecha_hasta_v = '';
    
    $patron = '/^\d{4}-\d{2}-\d{2}$/'; // Expresión regular para validar el formato YYYY-MM-DD
    
    if ((preg_match($patron, $fecha_ini)) && (preg_match($patron, $fecha_hasta))) {
        
        $fecha_ini_v   = date('Y-m-d', strtotime($fecha_ini));
        $fecha_hasta_v = date('Y-m-d', strtotime($fecha_hasta));
        
    } else {
        
        echo "La fecha no tiene el formato esperado";
    }
    
    
    $sql = "SELECT b.TIP_IDENTIFICACION , b.IDENTIFICACION, b.NOMBRES, b.PRI_APELLIDO, b.SEG_APELLIDO, b.SEXO, b.CORREO, b.TEL_CELULAR, b.DEPENDENCIA, b.UNIDAD, b.TIPO_USUARIO, b.EDAD, b.CLASIFICACION,  TO_CHAR(a.FECHA_ASISTENCIA, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_ASISTENCIA , d.NOMBRE_SEDE 
        FROM APLICACIONES.GYM_ASISTENCIA a
        JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG
        JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER
        JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM
        WHERE a.FECHA_ASISTENCIA >= TO_DATE(:fecha_ini, 'YYYY-MM-DD')
          AND a.FECHA_ASISTENCIA <= TO_DATE(:fecha_hasta, 'YYYY-MM-DD')
          $cod_sede
          ORDER BY a.FECHA_ASISTENCIA DESC";
    
    $stmt = oci_parse($conn, $sql); // Prepara la consulta SQL
    oci_bind_by_name($stmt, ':fecha_ini', $fecha_ini_v); // Vincula los parámetros    
    oci_bind_by_name($stmt, ':fecha_hasta', $fecha_hasta_v);
    
    if ($_POST['sede'] != 5) { //condicion para prevenir iyeccion SQL
        oci_bind_by_name($stmt, ':cod_sede', $_POST['sede']);
    }
    
    oci_execute($stmt); // Ejecuta la consulta    
    
    //------------------------------------------------------------------------//                        
    
    
    //consulta de total de registros, total asistencia
    
    $sql_total = "SELECT COUNT(*) as total
    FROM APLICACIONES.GYM_ASISTENCIA a
    JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG
    JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER
    JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM
    WHERE a.FECHA_ASISTENCIA >= TO_DATE(:fecha_ini, 'YYYY-MM-DD')
      AND a.FECHA_ASISTENCIA <= TO_DATE(:fecha_hasta, 'YYYY-MM-DD')
      $cod_sede
    ORDER BY a.FECHA_ASISTENCIA DESC";
    
    $stmt_total = oci_parse($conn, $sql_total);
    oci_bind_by_name($stmt_total, ':fecha_ini', $fecha_ini_v);
    oci_bind_by_name($stmt_total, ':fecha_hasta', $fecha_hasta_v);
    
    if ($_POST['sede'] != 5) {
		
    oci_bind_by_name($stmt_total, ':cod_sede', $_POST['sede']);
		
    }
    
    oci_execute($stmt_total);
    $total         = oci_fetch_assoc($stmt_total);
    $total_records = $total['TOTAL'];
    
    
    //calcula el promedio diario de asistencia
    
    $sql_average_day = "SELECT AVG(count) FROM
						(SELECT COUNT(*) as count
						FROM APLICACIONES.GYM_ASISTENCIA a
						JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG
						JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER
						JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM
						WHERE a.FECHA_ASISTENCIA BETWEEN TO_DATE(:fecha_ini, 'YYYY-MM-DD') 
						AND TO_DATE(:fecha_hasta, 'YYYY-MM-DD')
						$cod_sede
						GROUP BY TO_CHAR(FECHA_ASISTENCIA, 'YYYY-MM-DD'))";
    
    $stmt_avg_day = oci_parse($conn, $sql_average_day);
    oci_bind_by_name($stmt_avg_day, ':fecha_ini', $fecha_ini_v);
    oci_bind_by_name($stmt_avg_day, ':fecha_hasta', $fecha_hasta_v);
	
	if ($_POST['sede'] != 5) {
		
    oci_bind_by_name($stmt_avg_day, ':cod_sede', $_POST['sede']);
		
    }
	
    oci_execute($stmt_avg_day);
    $avg_day = oci_fetch_assoc($stmt_avg_day);
    
    //calcula el promedio de asistencia para la mañana (06:00 - 12:00)
    
    $sql_average_morning = "SELECT AVG(count) FROM
							(SELECT COUNT(*) as count
							   FROM APLICACIONES.GYM_ASISTENCIA a
								JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG
								JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER
								JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM
							WHERE a.FECHA_ASISTENCIA BETWEEN TO_DATE(:fecha_ini, 'YYYY-MM-DD') AND TO_DATE(:fecha_hasta, 'YYYY-MM-DD')
							AND TO_NUMBER(TO_CHAR(FECHA_ASISTENCIA, 'HH24')) BETWEEN 6 AND 12 
							$cod_sede
							GROUP BY TO_CHAR(FECHA_ASISTENCIA, 'YYYY-MM-DD'))";
    
    $stmt_avg_morning = oci_parse($conn, $sql_average_morning);
    oci_bind_by_name($stmt_avg_morning, ':fecha_ini', $fecha_ini_v);
    oci_bind_by_name($stmt_avg_morning, ':fecha_hasta', $fecha_hasta_v);
	
	if ($_POST['sede'] != 5) {
		
    oci_bind_by_name($stmt_avg_morning, ':cod_sede', $_POST['sede']);
		
    }
	
    oci_execute($stmt_avg_morning);
    $avg_morning = oci_fetch_assoc($stmt_avg_morning);
    
    
    //calcula el promedio de asistencia para la tarde/noche (12:00 - 22:00)
    
    $sql_average_evening = "SELECT AVG(count) FROM
							(SELECT COUNT(*) as count
							  FROM APLICACIONES.GYM_ASISTENCIA a
								 JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG
								 JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER
								 JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM
							WHERE a.FECHA_ASISTENCIA BETWEEN TO_DATE(:fecha_ini, 'YYYY-MM-DD') AND TO_DATE(:fecha_hasta, 'YYYY-MM-DD')
							AND TO_NUMBER(TO_CHAR(FECHA_ASISTENCIA, 'HH24')) BETWEEN 12 AND 22
							$cod_sede
							GROUP BY TO_CHAR(FECHA_ASISTENCIA, 'YYYY-MM-DD'))";
    
    $stmt_avg_evening = oci_parse($conn, $sql_average_evening);
    oci_bind_by_name($stmt_avg_evening, ':fecha_ini', $fecha_ini_v);
    oci_bind_by_name($stmt_avg_evening, ':fecha_hasta', $fecha_hasta_v);
	
	if ($_POST['sede'] != 5) {
		
    oci_bind_by_name($stmt_avg_evening, ':cod_sede', $_POST['sede']);
		
    }
	
    oci_execute($stmt_avg_evening);
    $avg_evening = oci_fetch_assoc($stmt_avg_evening);
    
    
    //Descripcion en la tabla
    
    echo '<div class="m-2 d-flex justify-content-end">
                <form method="POST" action="csv_reporte.php">
                    <input type="hidden" id="fecha_ini" name="fecha_ini" value="' . $fecha_ini_v . '">
                    <input type="hidden" id="fecha_fin" name="fecha_fin" value="' . $fecha_hasta_v . '">
                    <input type="hidden" id="sede" name="sede" value="' . $_POST['sede'] . '">
                    <button type="submit" class="btn btn-custom" id="excel" title="Descargar CSV"><i class="fa-solid fa-file-excel"></i></button>
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
                    <th>Fecha asistencia</th>
                    <th>Sede</th>
                </tr>
            </thead>
            <tbody>';
    
    //Itera a través de cada fila de resultados
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
        echo '<td>' . date('d-m-Y H:i:s', strtotime($row['FECHA_ASISTENCIA'])) . '</td>';
        echo '<td>' . $row['NOMBRE_SEDE'] . '</td>';
        echo '</tr>';
    }
    
	// descripcion segunda tabla
	
    echo '<table class="table-striped table-hover table" style="width: 60%;">
          <thead>
            <tr>
              <th scope="col">Tipo de dato</th>
              <th scope="col" class="text-center">Cantida personas asistidas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Total de registros:</td>
              <td class="text-center">' . round($total_records, 1) . '</td>
            </tr>
            <tr>
              <td>Promedio diario de asistencias:</td>
              <td class="text-center">' . round($avg_day['AVG(COUNT)'], 1) . '</td>
            </tr>
            <tr>
              <td>Promedio de asistencias (06:00 - 12:00):</td>
              <td class="text-center">' . round($avg_morning['AVG(COUNT)'], 1) . '</td>
            </tr>
            <tr>
              <td>Promedio de asistencias (12:00 - 22:00):</td>
              <td class="text-center">' . round($avg_evening['AVG(COUNT)'], 1) . '</td>
            </tr>
          </tbody>
        </table>';
    
}
?> 