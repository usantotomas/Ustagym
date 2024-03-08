<?php

require('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $estado_ins = $_POST['estado'];
	
	if (($estado_ins == 'Activo') || ($estado_ins == 'Inactivo')){
		
    //realiza la consulta teniendo encuenta el tipo de dato enviado en $estado_ins 
		
    $query = "SELECT  TIP_IDENTIFICACION,  
				IDENTIFICACION,
				NOMBRES,
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
					WHEN TRUNC(SYSDATE) BETWEEN TRUNC(inicio_inscripcion) AND TRUNC(final_inscripcion) THEN 'Activo'
					ELSE 'Inactivo'
				END as INSCRIPCION
		FROM APLICACIONES.GYM_USUARIOS_REG
		WHERE CASE 
				WHEN TRUNC(SYSDATE) BETWEEN TRUNC(inicio_inscripcion) AND TRUNC(final_inscripcion) THEN 'Activo'
				ELSE 'Inactivo'
			  END = :estado_ins";

    // Prepara el comando
    $stid = oci_parse($conn, $query);

    // Vincula la variable PHP al parámetro de la consulta SQL
    oci_bind_by_name($stid, ':estado_ins', $estado_ins);

    // Ejecuta la consulta
    oci_execute($stid);


		
	}else if ($estado_ins == 'Todos'){
		
		
		$query = "SELECT 
					TIP_IDENTIFICACION,
					IDENTIFICACION,
					NOMBRES,
					PRI_APELLIDO,
					SEG_APELLIDO,
					SEXO,
					CORREO,
					TEL_CELULAR,
					DEPENDENCIA,
					UNIDAD,
					TIPO_USUARIO,
					CLASIFICACION,
					EDAD,
					inicio_inscripcion, 
					final_inscripcion,

					CASE 
						WHEN TRUNC(SYSDATE) BETWEEN TRUNC(inicio_inscripcion) AND TRUNC(final_inscripcion) THEN 'Vigente'
						ELSE 'Vencido'
					END AS INSCRIPCION
				FROM APLICACIONES.GYM_USUARIOS_REG";

		// Prepara el comando
		$stid = oci_parse($conn, $query);

		// Ejecuta la consulta
		oci_execute($stid);


		
	}
	
	 // Prepara los headers para forzar la descarga del archivo
   	header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="archivo.csv"');

    // Abre el stream de PHP
    $fp = fopen('php://output', 'w');
	
	fwrite($fp, "\xFF\xFE");
	
	// Títulos de las columnas al archivo CSV
	fputcsv($fp, array('Tipo identificacion', 'Identificacion', 'Nombre', 'Sexo', 'Correo', 'Telefono C.', 'Dependencia', 'Unidad', 'Tipo usuario', 'Edad', 'Clasificacion', 'Inscripcion'), ';');


    
    while ($row = oci_fetch_assoc($stid)) {
		
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
        $row['INSCRIPCION']
    );
    // Escribe la fila al archivo CSV utilizando el punto y coma como delimitador
    fputcsv($fp, $fila, ';');
    }

    // Cierra el stream
    fclose($fp);

}
