<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class proveedores extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('seguridad_model');
		$this->load->model('proveedores_model');
	}
	public function index(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/
          $this->load->view('constant');
          $this->load->view('view_header');
          $data['proveedores'] = $this->proveedores_model->ListarProveedores();
          $this->load->view('proveedores/view_proveedores', $data);
          $this->load->view('view_footer');
          
	}
     public function nuevo(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $this->load->view('constant');
          $this->load->view('view_header');
          $data['titulo'] = "Nuevo Proveedor";
          $this->load->view('proveedores/view_nuevo_proveedores', $data);
          $this->load->view('view_footer');
     }
     public function editarProveedor($id){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $id =  base64_decode($id);
          $this->load->view('constant');
          $this->load->view('view_header');
          $data['proveedor'] = $this->proveedores_model->BuscaProveedores($id);
          $data['titulo'] = "Editar Proveedor";
          $this->load->view('proveedores/view_nuevo_proveedores', $data);
          $this->load->view('view_footer');
     }
     public function SaveProveedor(){
        $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $Proveedores           = json_decode($this->input->post('ProveedoresPost'));
          $response = array (
                    "estatus"   => false,
                    "campo"     => "",
                 "error_msg" => ""
         );
         if($Proveedores->Nombre==""){
               $response["campo"]     = "Nombre";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Nombre del Proveedor es Obligatorio</div>";
               echo json_encode($response);
          }else if($Proveedores->Direccion==""){
               $response["campo"]     = "direccion";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Dirección es obligatorio</div>";
               echo json_encode($response);
          }else if($Proveedores->Telefono==""){
                    $response["campo"]       = "telefono";
                    $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Telefono es obligatorio</div>";
                    echo json_encode($response);
          }else if($Proveedores->CP==""){
               $response["campo"]       = "cp";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Codigo Postal es Obligatorio</div>";
               echo json_encode($response);
          }else{
               if($Proveedores->Id==""){
                     $RegistraProveedor    = array(
                     'nombre_proveedor'    => $Proveedores->Nombre,
                     'direccion'           => $Proveedores->Direccion,
                     'telefono'            => $Proveedores->Telefono,
                     'CP'                  => $Proveedores->CP,
                     'fecha_registro'      => date('Y-m-j H:i:s')
                     );
                     $this->proveedores_model->SaveProveedores($RegistraProveedor);
                     $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Informacion Guardada Correctamente</div>";
                     echo json_encode($response);
               }
               if($Proveedores->Id!=""){
                    $UpdateProveedor    = array(
                     'nombre_proveedor'    => $Proveedores->Nombre,
                     'direccion'           => $Proveedores->Direccion,
                     'telefono'            => $Proveedores->Telefono,
                     'CP'                  => $Proveedores->CP,
                     'fecha_registro'      => date('Y-m-j H:i:s')
                     );
                    $this->proveedores_model->UpdateProveedores($UpdateProveedor, $Proveedores->Id);
                    $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
                    echo json_encode($response);
               }
          }
     }
}