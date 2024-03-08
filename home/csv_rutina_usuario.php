<?php

require('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$documento = filter_var($_POST['documento'], FILTER_VALIDATE_INT);
	
    $query = 'SELECT b.NOMBRES, b.PRI_APELLIDO, b.SEG_APELLIDO, b.IDENTIFICACION, a.FECHA_ASISTENCIA, d.NOMBRE_SEDE 
			FROM APLICACIONES.GYM_ASISTENCIA a 
			JOIN APLICACIONES.GYM_USUARIOS_REG b ON a.COD_USUARIO_REG  = b.COD_USUARIO_REG 
			JOIN APLICACIONES.GYM_USER c ON a.COD_USER = c.COD_USER 
			JOIN APLICACIONES.GYM_GYMNASIOS d ON c.COD_GYM = d.COD_GYM 
			WHERE b.IDENTIFICACION = :identificacion';
	
	$stmt = oci_parse($conn, $query); 	                                    // Prepara la consulta SQL
	oci_bind_by_name($stmt, ':identificacion', $documento);		            // Vincula los parámetros														    
	oci_execute($stmt);													    // Ejecuta la consulta		
	
    // Prepara los headers para forzar la descarga del archivo
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="archivo.csv"');

    // Abre el stream de PHP
    $fp = fopen('php://output', 'w');
	
	fwrite($fp, "\xEF\xBB\xBF");
	// Títulos de las columnas al archivo CSV
	fputcsv($fp, array('Nombre', 'Documento', 'Fecha_Asistencia', 'Sede'), ';');


    
    while ($row = oci_fetch_assoc($stmt)) {
    // Aquí transformamos la fila en un formato adecuado para CSV
    $fila = array(
        $row['NOMBRES'] = mb_convert_encoding($row['NOMBRES'], 'UTF-16LE', 'UTF-8') . ' ' . $row['PRI_APELLIDO'] = mb_convert_encoding($row['PRI_APELLIDO'], 'UTF-16LE', 'UTF-8') . ' ' . $row['SEG_APELLIDO'] = mb_convert_encoding($row['SEG_APELLIDO'], 'UTF-16LE', 'UTF-8'),
        $row['IDENTIFICACION'],
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
