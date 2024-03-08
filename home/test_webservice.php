<?php

class ConsultarContratoLaboralCARq{
				var $InfoPeticion;
				var $numIdentificacion;
				}

class InfoPeticion{
				var $IDPeticion;
				var $Origen;
			}

class ConsultarContratoLaboralCARs{
				var $InfoRespuesta;
				var	$InfoContrato;
				}
		
class InfoRespuesta{
				var $Resultado;
				var $ErrorCode;
				var $ErrorDescription;
				}
	
class InfoContrato{
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



class WsseAuthHeader extends SoapHeader {

		private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
		private $wsu_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

		function __construct($user, $pass) {

			$created = gmdate('Y-m-d\TH:i:s\Z');
			$nonce = mt_rand();
			$passdigest = base64_encode( pack('H*', sha1( pack('H*', $nonce) . pack('a*',$created).  pack('a*',$pass))));

			$auth = new stdClass();
			$auth->Username = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
			$auth->Password = new SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
			$auth->Nonce = new SoapVar($passdigest, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
			$auth->Created = new SoapVar($created, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wsu_ns);

			$username_token = new stdClass();
			$username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);

			$security_sv = new SoapVar(
				new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),
				SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);
			parent::__construct($this->wss_ns, 'Security', $security_sv, true);
		}
		}

		class clase 
		 {
		 var $soapClient;

		 function __construct($url='https://integracionustaprod-usantotomas.integration.ocp.oraclecloud.com/ic/ws/integration/v1/flows/soap/CONSULTARCONTRATOCA/1.0/?wsdl')
		 {                      
			 $options = array(
                'soap_version'=>SOAP_1_1,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE);
			 $this->soapClient= new SoapClient($url, $options);        
			 $this->soapClient->__setSoapHeaders(Array(new WsseAuthHeader("OICProduccion", "jbyA8YI7DqQO")));     
		 }

		function ConsultarContratoLaboralCA($ConsultarContratoLaboralCARq)
		{

		$ConsultarContratoLaboralCARs = $this->soapClient->ConsultarContratoLaboralCA($ConsultarContratoLaboralCARq);
		return $ConsultarContratoLaboralCARs;

		}}

		$valores=new ConsultarContratoLaboralCARq();
		$InfoPeticion = new InfoPeticion();
		$InfoPeticion->IDPeticion = '3';
		$InfoPeticion->Origen = 'CAA';
			
		$valores->InfoPeticion = $InfoPeticion;
		$valores->numIdentificacion = '1071166905';


		$clase=new clase();
		
		$ConsultarContratoLaboralCARs=$clase->ConsultarContratoLaboralCA($valores);
		
			//Validar una respuesta positiva
			if($ConsultarContratoLaboralCARs->InfoRespuesta->ErrorCode == 'ERR'){
				echo "Usuario no existe";
				die("");
			}

echo('<pre>');
var_dump($ConsultarContratoLaboralCARs);
echo('</pre>');

