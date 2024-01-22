<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class clientes extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('seguridad_model');
		$this->load->model('clientes_model');
	}
	public function index(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/
          $this->load->view('constant');
          $this->load->view('view_header');
          $data['clientes'] = $this->clientes_model->ListarClientes();
          $this->load->view('clientes/view_clientes', $data);
          $this->load->view('view_footer');
          
	}
	public function EditarCliente($idCliente,$codigoCliente){
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
		$idCliente 		= base64_decode($idCliente);
		$codigoCliente  = base64_decode($codigoCliente);
		$data["cliente"]= $this->clientes_model->BuscaCliente($idCliente);
		$data["titulo"] = "Editar Cliente";
		$this->load->view('constant');
		$this->load->view('view_header');
		$this->load->view('clientes/view_nuevo_cliente',$data);
		$this->load->view('view_footer');
	}
	public function deletecliente(){
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$Clientes 		= json_decode($this->input->post('MiCliente'));
		$id             = base64_decode($Clientes->Id);
		$codigo 		= base64_decode($Clientes->Codigo);
		/*Array de response*/
		 $response = array (
				"estatus"   => false,
	            "error_msg" => ""
	    );
		 $this->clientes_model->EliminarCliente($id);
		 $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Cliente Eliminado Correctamente Codigo: <strong>".$codigo."</strong>, La Información de Actualizara en 5 Segundos <meta http-equiv='refresh' content='5'></div>";
		 echo json_encode($response);
	}
	public function nuevo(){
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$data["titulo"] = "Nuevo Cliente";
		$this->load->view('constant');
		$this->load->view('view_header');
		$this->load->view('clientes/view_nuevo_cliente',$data);
		$this->load->view('view_footer');
	}
	public function DirEnvio($codigoCliente,$idCliente)
	{
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$idCliente 		= base64_decode($idCliente);
		$codigoCliente  = base64_decode($codigoCliente);
		$data["idCliente"] = $idCliente;
		$data["codigoClie"]= $codigoCliente;
		$this->load->view('constant');
		$this->load->view('view_header');
		$this->load->view('clientes/view_dir_clientes',$data);
		$this->load->view('view_footer');
	}
	public function BuscaCP(){
		$cp = $this->input->get('cp');
		echo json_encode($this->clientes_model->BuscaCP($cp));
	}
	public function GuardaDireccion(){
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
		$Clientes 		= json_decode($this->input->post('ClientesPost'));
		$response = array (
				"estatus"   => false,
				"campo"     => "",
	            "error_msg" => ""
	    );
	    if($Clientes->Direccion==""){
			$response["campo"]     = "Direccion";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>La Dirección es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->nExterior==""){
			$response["campo"]     = "nExterior";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El nExterior es obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->CP==""){
			$response["campo"]       = "cp";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Codigo Postal es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Estado==""){
				$response["campo"]       = "estado";
				$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Estado es Obligatorio</div>";
				echo json_encode($response);
		}else if($Clientes->Municipio==""){
			$response["campo"]       = "municipio";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Municipio es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Ciudad==""){
			$response["campo"]       = "ciudad";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El campo Ciudad es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Colonia=="0"){
			$response["campo"]       = "colonia";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Colonia es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Telefono==""){
			$response["campo"]       = "telefono";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Telefono Es Obligatorio</div>";
			echo json_encode($response);
		}else{
			$RegistrarDirEnvio 	= array(
					'ID_CLIENTE'        => $Clientes->idcliente,
					'DIRECCION'		    => $Clientes->Direccion,
					'N_EXTERIOR'		=> $Clientes->nExterior,
					'N_INTERIOR'		=> $Clientes->nInterior,
					'CP'				=> $Clientes->CP,
					'COLONIA'			=> $Clientes->Colonia,
					'CIUDAD'			=> $Clientes->Ciudad,
					'MUNICIPIO'			=> $Clientes->Municipio,
					'ESTADO'			=> $Clientes->Estado,
					'TELEFONO'	    	=> $Clientes->Telefono,
					'REFERENCIAS'		=> $Clientes->Referencias,
					'FECHA_REGISTRO'	=> date('Y-m-j H:i:s')
					);
			$this->clientes_model->SaveDireccion($RegistrarDirEnvio);
			$response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Guardada Correctamente</div>";
			echo json_encode($response);
		}

	}
	public function GuardaClientes(){
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$Clientes 		= json_decode($this->input->post('ClientesPost'));
		$response = array (
				"estatus"   => false,
				"campo"     => "",
	            "error_msg" => ""
	    );
	    if($Clientes->Nombre==""){
			$response["campo"]     = "Nombre";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Nombre es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Apellidos==""){
			$response["campo"]     = "apellidos";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Apellido es obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->CP==""){
			$response["campo"]       = "cp";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Codigo Postal es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Pais==""){
				$response["campo"]       = "pais";
				$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Pais es obligatorio</div>";
				echo json_encode($response);
		}else if($Clientes->Estado==""){
				$response["campo"]       = "estado";
				$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Estado es Obligatorio</div>";
				echo json_encode($response);
		}else if($Clientes->Municipio==""){
			$response["campo"]       = "municipio";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Municipio es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Ciudad==""){
			$response["campo"]       = "ciudad";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El campo Ciudad es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Colonia=="0"){
			$response["campo"]       = "colonia";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Colonia es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Calle==""){
			$response["campo"]       = "Calle";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Calle Es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Email==""){
			$response["campo"]       = "email";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Correo Es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->RFC==""){
			$response["campo"]       = "rfc";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo RFC Es Obligatorio</div>";
			echo json_encode($response);
		}else if($Clientes->Telefono==""){
			$response["campo"]       = "telefono";
			$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Telefono Es Obligatorio</div>";
			echo json_encode($response);
		}else{

				if($Clientes->Id==""){
					$ExisteRFC         = $this->clientes_model->ExisteRFC($Clientes->RFC);
					$ExisteEmail       = $this->clientes_model->ExisteEmail($Clientes->Email);
					if($ExisteRFC==true){
						$response["campo"]     = "rfc";
						$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El RFC Ya esta en Uso</div>";
						echo json_encode($response);
					}else if($ExisteEmail==true){
						$response["campo"]     = "email";
						$response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Email Ya esta en Uso</div>";
						echo json_encode($response);
					}else{
						
						$codigoCliente      = $this->clientes_model->UltimoCliente();
						foreach ($codigoCliente as $value) {
							$codigoCliente = $value->CODIGO_CLIENTE;
						}
						$codigoCliente =  (int)$codigoCliente;
						$codigoCliente = $codigoCliente + 1;
						$codigoCliente = str_pad($codigoCliente, 5, '0',STR_PAD_LEFT);
						$RegistraCliente 	= array(
						'CODIGO_CLIENTE'    => $codigoCliente,
						'NOMBRE'		    => $Clientes->Nombre,
						'APELLIDOS'			=> $Clientes->Apellidos,
						'CP'				=> $Clientes->CP,
						'CALLE'				=> $Clientes->Calle,
						'COLONIA'			=> $Clientes->Colonia,
						'LOCALIDAD'			=> $Clientes->Ciudad,
						'MUNICIPIO'			=> $Clientes->Municipio,
						'ESTADO'			=> $Clientes->Estado,
						'PAIS'				=> $Clientes->Pais,
						'EMAIL'				=> $Clientes->Email,
						'RFC'	    		=> $Clientes->RFC,
						'TELEFONO'	    	=> $Clientes->Telefono,
						'ID_DIR_ENVIO'      => '0',
						'FECHA_REGISTRO'	=> date('Y-m-j H:i:s')
						);
						$this->clientes_model->SaveClientes($RegistraCliente);
						$response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Cliente Nº: ".$codigoCliente."</strong> Informacion Guardada Correctamente</div>";
						echo json_encode($response);
					}
				}
				if($Clientes->Id!=""){
						$UpdateClientes 	= array(
						'NOMBRE'		    => $Clientes->Nombre,
						'APELLIDOS'			=> $Clientes->Apellidos,
						'CP'				=> $Clientes->CP,
						'CALLE'				=> $Clientes->Calle,
						'COLONIA'			=> $Clientes->Colonia,
						'LOCALIDAD'			=> $Clientes->Ciudad,
						'MUNICIPIO'			=> $Clientes->Municipio,
						'ESTADO'			=> $Clientes->Estado,
						'PAIS'				=> $Clientes->Pais,
						'EMAIL'				=> $Clientes->Email,
						'RFC'	    		=> $Clientes->RFC,
						'TELEFONO'	    	=> $Clientes->Telefono,
						'ID_DIR_ENVIO'      => '0',
						'FECHA_EDICION'	    => date('Y-m-j H:i:s')
						);
						$this->clientes_model->UpdateClientes($UpdateClientes,$Clientes->Id);
						$response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
						echo json_encode($response);
				}
		}
	}

}