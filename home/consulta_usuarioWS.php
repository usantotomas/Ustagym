<?php


// Primero consulta en la base de datos que el usuario no haya estado registrado.
//Si no esta registrado primero consulta en el web service si es administrativo o docente, en el caso de que no lo sea, consulta en SAC si es estudiente.

$sql = "SELECT IDENTIFICACION FROM APLICACIONES.GYM_USUARIOS_REG WHERE IDENTIFICACION = :identificacion";
    
    
    // Prepara la consulta SQL
$stmt = oci_parse($conn, $sql);
    
    // Vincula los parámetros
oci_bind_by_name($stmt, ':identificacion', $identificacion);
    
    
    // Ejecuta la consulta
oci_execute($stmt);
    
    // Toma el dato de la consulta y la asigna a la variable
$user_data = oci_fetch_assoc($stmt);

if ($user_data['IDENTIFICACION'] == $identificacion) {
	$aviso = '<div class="col-md-4 align-items-center d-flex">
                       <h6 style="color: brown">Este usuario ya se encuentra registrado!</h6>
                </div>';
} else { 


//web service- proceso de conexion
	class ConsultarContratoLaboralCARq
	{
		var $InfoPeticion;
		var $numIdentificacion;
	}

	class InfoPeticion
	{
		var $IDPeticion;
		var $Origen;
	}

	class ConsultarContratoLaboralCARs
	{
		var $InfoRespuesta;
		var $InfoContrato;
	}

	class InfoRespuesta
	{
		var $Resultado;
		var $ErrorCode;
		var $ErrorDescription;
	}

	class InfoContrato
	{
		var $tipoIdentificacion;
		var $numIdentificacion;
		var $primerNombre;
		var $segundoNombre;
		var $primerApellido;
		var $segundoApellido;
		var $sexo;
		var $grupoSanguineo;
		var $telefono;
		var $celular;
		var $codigoVinculacion;
		var $fechaDeNacimiento;
		var $tipoDeVinculacion;
		var $codigoCentoCostos;
		var $correoInstitucional;
		var $codigoCargo;
		var $cargo;
		var $estado;
		var $fechaVencimientoContrato;
		var $fechaRetiro;
		var $sede;
		var $centroCostos;
	}

	class WsseAuthHeader extends SoapHeader
	{
		private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
		private $wsu_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

		function __construct($user, $pass)
		{
			$created    = gmdate('Y-m-d\TH:i:s\Z');
			$nonce      = mt_rand();
			$passdigest = base64_encode(pack('H*', sha1(pack('H*', $nonce) . pack('a*', $created) . pack('a*', $pass))));

			$auth           = new stdClass();
			$auth->Username = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
			$auth->Password = new SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
			$auth->Nonce    = new SoapVar($passdigest, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
			$auth->Created  = new SoapVar($created, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wsu_ns);

			$username_token                = new stdClass();
			$username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);

			$security_sv = new SoapVar(
				new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),
				SOAP_ENC_OBJECT,
				NULL,
				$this->wss_ns,
				'Security',
				$this->wss_ns
			);
			parent::__construct($this->wss_ns, 'Security', $security_sv, true);
		}
	}

	class clase
	{
		var $soapClient;

		function __construct(
			$url = 'https://integracionustaprod-usantotomas.integration.ocp.oraclecloud.com/ic/ws/integration/v1/flows/soap/CONSULTARCONTRATOCA/1.0/?wsdl'
		)
		{
			$options          = array(
				'soap_version' => SOAP_1_1,
				'exceptions' => true,
				'trace' => 1,
				'cache_wsdl' => WSDL_CACHE_NONE
			);
			$this->soapClient = new SoapClient($url, $options);
			$this->soapClient->__setSoapHeaders(
				Array(
					new WsseAuthHeader("OICProduccion", "jbyA8YI7DqQO")
				)
			);
		}

		function ConsultarContratoLaboralCA($ConsultarContratoLaboralCARq)
		{
			$ConsultarContratoLaboralCARs = $this->soapClient->ConsultarContratoLaboralCA($ConsultarContratoLaboralCARq);
			return $ConsultarContratoLaboralCARs;

		}
	}

// proceso de envio de datos al web service

	$valores                  = new ConsultarContratoLaboralCARq();
	$InfoPeticion             = new InfoPeticion();
	$InfoPeticion->IDPeticion = '3';
	$InfoPeticion->Origen     = 'CAA';

	$valores->InfoPeticion      = $InfoPeticion;
	$valores->numIdentificacion = $identificacion; // parametro que se envia desde registro_usuario.php /numero de documento

	$clase = new clase();

	$ConsultarContratoLaboralCARs = $clase->ConsultarContratoLaboralCA($valores);

//Validar la respuesta en el web service

	if ($ConsultarContratoLaboralCARs->InfoRespuesta->ErrorCode == 'OK') { // si la respuesta del web service es 'Ok' procede a asignar los datos a las variables 

		$t_identificiacion  = $ConsultarContratoLaboralCARs->InfoContrato->tipoIdentificacion;
		$res_identificacion = $ConsultarContratoLaboralCARs->InfoContrato->numIdentificacion;
		$nobres             = $ConsultarContratoLaboralCARs->InfoContrato->primerNombre . ' ' . $ConsultarContratoLaboralCARs->InfoContrato->segundoNombre;
		$p_apellido         = $ConsultarContratoLaboralCARs->InfoContrato->primerApellido;
		$s_apellido         = $ConsultarContratoLaboralCARs->InfoContrato->segundoApellido;
		$sexo               = $ConsultarContratoLaboralCARs->InfoContrato->sexo;
		$mail               = $ConsultarContratoLaboralCARs->InfoContrato->correoInstitucional;
		$t_celular          = $ConsultarContratoLaboralCARs->InfoContrato->celular;
		$dependencia        = $ConsultarContratoLaboralCARs->InfoContrato->centroCostos;
		$unidad             = 'n/a';
		$tipo_usuario       = $ConsultarContratoLaboralCARs->InfoContrato->tipoDeVinculacion;
		$fec_nacimiento     = $ConsultarContratoLaboralCARs->InfoContrato->fechaDeNacimiento;

		
		//calculo de edad
		
		if ($fec_nacimiento) {
			$fechaNacimiento = DateTime::createFromFormat('d/m/Y', $fec_nacimiento); // convierte la cadena de fecha a un objeto DateTime

			$fechaActual = new DateTime(); // obtén la fecha actual

			$intervalo = $fechaActual->diff($fechaNacimiento); // calcula la diferencia entre las fechas

			$edad = $intervalo->y; // obtén el número de años de la diferencia

		} else {
			$edad = 'Desconocido';
		}
	} else if ($ConsultarContratoLaboralCARs->InfoRespuesta->ErrorCode == 'ERR') { //si no encuentra ningun dato en el web service , realiza la busqueda en SAC 
    
    
    // consulta en SAC 
    
    
    //Esta consulta trae los datos de los estudiantes con cuenta al SAC donde sea estudiante/egresado/educacion continua  que haya estado inscrito y recopila información adicional sobre el periodo, la unidad académica y la dependencia en la que está inscrito.

		$sql = "SELECT DISTINCT a.TIP_IDENTIFICACION, a.NUM_IDENTIFICACION, b.COD_TABLA , a.NOM_TERCERO, a.PRI_APELLIDO, a.SEG_APELLIDO, a.GEN_TERCERO, a.DIR_EMAIL, a.TEL_CECULAR, f.NOM_DEPENDENCIA, e.NOM_UNIDAD, a.FEC_NACIMIENTO, c.ID_INSCRIPCION
            FROM SINU.BAS_TERCERO a
            JOIN SINU.BAS_TIP_TERCERO b ON a.ID_TERCERO = b.ID_TERCERO
            JOIN SINU.SRC_INSCRITO c ON a.ID_TERCERO = c.ID_TERCERO
            JOIN SINU.SRC_PERIODO d ON c.COD_PROG_OPC_UNO = d.COD_UNIDAD
            JOIN SINU.SRC_UNI_ACADEMICA e ON d.COD_UNIDAD = e.COD_UNIDAD
            JOIN SINU.BAS_DEPENDENCIA f ON e.ID_DEPENDENCIA = f.ID_DEPENDENCIA
            WHERE a.NUM_IDENTIFICACION = :identificacion
            AND b.COD_TABLA IN ('1','8','08','09')  
            ORDER BY c.ID_INSCRIPCION DESC";
    
    
    // Prepara la consulta SQL
		$stmt = oci_parse($conn, $sql);
    
    // Vincula los parámetros
		oci_bind_by_name($stmt, ':identificacion', $identificacion);
    
    
    // Ejecuta la consulta
		oci_execute($stmt);
    
    // Toma el dato de la consulta y la asigna a la variable
		$user_data = oci_fetch_assoc($stmt);

    
    
    // Inializacion variables
		$t_identificiacion = $res_identificacion = $nobres = $p_apellido = $s_apellido = $sexo = $mail = $t_celular = $dependencia = $unidad = $tipo_usuario = $edad = "";
    
    
    
    // Si existe el array user_data, establece las variables

		if (!empty($user_data)) {
        
        // Se comprueba si existe cada elemento antes de acceder a él
			$t_identificiacion  = isset($user_data['TIP_IDENTIFICACION']) ? $user_data['TIP_IDENTIFICACION'] : '';
			$res_identificacion = isset($user_data['NUM_IDENTIFICACION']) ? $user_data['NUM_IDENTIFICACION'] : '';
			$nobres             = isset($user_data['NOM_TERCERO']) ? $user_data['NOM_TERCERO'] : '';
			$p_apellido         = isset($user_data['PRI_APELLIDO']) ? $user_data['PRI_APELLIDO'] : '';
			$s_apellido         = isset($user_data['SEG_APELLIDO']) ? $user_data['SEG_APELLIDO'] : '';
			$sexo               = isset($user_data['GEN_TERCERO']) ? $user_data['GEN_TERCERO'] : '';
			$mail               = isset($user_data['DIR_EMAIL']) ? $user_data['DIR_EMAIL'] : '';
			$t_celular          = isset($user_data['TEL_CECULAR']) ? $user_data['TEL_CECULAR'] : '';
			$dependencia        = isset($user_data['NOM_DEPENDENCIA']) ? $user_data['NOM_DEPENDENCIA'] : '';
			$unidad             = isset($user_data['NOM_UNIDAD']) ? $user_data['NOM_UNIDAD'] : '';
			$tipo_usuario       = isset($user_data['COD_TABLA']) ? $user_data['COD_TABLA'] : '';
			$fec_nacimiento     = isset($user_data['FEC_NACIMIENTO']) ? $user_data['FEC_NACIMIENTO'] : '';
        
        //calculo de edad

			if ($fec_nacimiento) {
				$fechaNacimiento = DateTime::createFromFormat('d-M-y', $fec_nacimiento); // convierte la cadena de fecha a un objeto DateTime

				$fechaActual = new DateTime(); // obtén la fecha actual

				$intervalo = $fechaActual->diff($fechaNacimiento); // calcula la diferencia entre las fechas

				$edad = $intervalo->y; // obtén el número de años de la diferencia

			} else {
				$edad = 'Desconocido';
			}

			if (($tipo_usuario == '1') || ($tipo_usuario == '09')) {
				$tipo_usuario = 'Estudiante';
			} else if (($tipo_usuario == '8') || ($tipo_usuario == '08')) {
				$tipo_usuario = 'Egresado';
			}
		} else {
			$aviso = '<div class="col-md-4 align-items-center d-flex">
                         <h6 style="color: brown">No se encuentra el usuario !</h6>
                     </div>';
		}
	} else {
		echo 'Error 01'; // no se recibe respuesta en web service  
	}

}