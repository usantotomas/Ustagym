
<?php
    // Variables de conexión
    $username = 'sinu'; 
    $password = 'Gf7pTeK8V3h'; 
    $host = '172.31.214.247'; // cadena de conexión a la base de datos
	$service_name = "sac";

	$sid="(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=".$host.")(PORT=1521)))
		   (CONNECT_DATA=(SERVICE_NAME=".$service_name.")))";

    // Utilizamos la función oci_connect que establece una conexión a una base de datos Oracle

    $conn = oci_connect($username, $password, $sid, 'AL32UTF8');  //AL32UTF8 significa que todas las cadenas de caracteres que envíes o recibas a través de esta conexión estarán en múltiples idiomas.
 
    // Verificar si la conexión fue exitosa
    if (!$conn) {
        // En caso de fallo en la conexión, recuperamos el mensaje de error
        $m = oci_error();
        trigger_error('No se pudo conectar a la base de datos: '. $m['message'], E_USER_ERROR);
    } 
?>

