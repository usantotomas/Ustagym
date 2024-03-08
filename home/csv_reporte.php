<?php

require('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$fecha_ini = $_POST['fecha_ini'];
	$fecha_fin = $_POST['fecha_fin'];
	
	$cod_sede = '';
	
	if ($_POST['sede'] != 5) {
		
		$cod_sede = 'AND d.COD_GYM = :cod_sede'; //se define la condicion si se selecicona una sede 
	}
	
		$query = "SELECT b.TIP_IDENTIFICACION , b.IDENTIFICACION, b.NOMBRES, b.PRI_APELLIDO, b.SEG_APELLIDO, b.SEXO, b.CORREO, b.TEL_CELULAR, b.DEPENDENCIA, b.UNIDAD, b.TIPO_USUARIO, b.EDAD, b.CLASIFICACION,  TO_CHAR(a.FECHA_ASISTENCIA, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_ASISTENCIA , d.NOMBRE_SEDE 
				FROM APLICACIONES.GYM_ASISTENCIA a
				JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG = b.COD_USUARIO_REG
				JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER
				JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM
				WHERE a.FECHA_ASISTENCIA >= TO_DATE(:fecha_ini, 'YYYY-MM-DD')
				  AND a.FECHA_ASISTENCIA <= TO_DATE(:fecha_fin, 'YYYY-MM-DD')
				  $cod_sede
				  ORDER BY a.FECHA_ASISTENCIA DESC";
	

	$stmt = oci_parse($conn, $query); 
	
	// Prepara la consulta SQL
	oci_bind_by_name($stmt, ':fecha_ini', $fecha_ini);		            // Vincula los parámetros	
	oci_bind_by_name($stmt, ':fecha_fin', $fecha_fin);	
	
    if ($_POST['sede'] != 5) {												//condicion para prevenir iyeccion SQL
    oci_bind_by_name($stmt, ':cod_sede', $_POST['sede']);
		
	}	
												    
	
	oci_execute($stmt); // Ejecuta la consulta

	
	
    // Prepara los headers para forzar la descarga del archivo
   	header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="archivo.csv"');

    // Abre el stream de PHP
    $fp = fopen('php://output', 'w');
	
	fwrite($fp, "\xFF\xFE");
	
	// Títulos de las columnas al archivo CSV
	fputcsv($fp, array('Tipo identificacion', 'Identificacion', 'Nombre', 'Sexo', 'Correo', 'Telefono C.', 'Dependencia', 'Unidad', 'Tipo usuario', 'Edad', 'Clasificacion', 'Fecha_asistencia', 'Sede'), ';');


    
    while ($row = oci_fetch_assoc($stmt)) {
		
    // Aquí transformamos la fila en un formato adecuado para CSV
    $fila = array(
        $row['TIP_IDENTIFICACION'] ,
        $row['IDENTIFICACION'],
		$row['NOMBRES'] . ' ' . $row['PRI_APELLIDO'] . ' ' . $row['SEG_APELLIDO'],
		$row['SEXO'],
		$row['CORREO'],
		$row['TEL_CELULAR'],
		$row['DEPENDENCIA'] = mb_convert_encoding($row['DEPENDENCIA'], 'UTF-16LE', 'UTF-8'),
		$row['UNIDAD'] = mb_convert_encoding($row['UNIDAD'], 'UTF-16LE', 'UTF-8'),
		$row['TIPO_USUARIO'],
		$row['EDAD'],
		$row['CLASIFICACION'],
        date('d-m-Y', strtotime($row['FECHA_ASISTENCIA'])),
        $row['NOMBRE_SEDE']
    );
    // Escribe la fila al archivo CSV utilizando el punto y coma como delimitador
    fputcsv($fp, $fila, ';');
    }

    // Cierra el stream
    fclose($fp);

}

?>