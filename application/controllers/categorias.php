<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class categorias extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('seguridad_model');
		$this->load->model('categorias_model');
	}
	public function index(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/
          $this->load->view('constant');
          $this->load->view('view_header');
          $data['categorias'] = $this->categorias_model->ListarCategorias();
          $this->load->view('categorias/view_categorias', $data);
          $this->load->view('view_footer');
          
	}
     public function nuevosubcategoria($id,$descr){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $id                   = base64_decode($id);
          $descr                = base64_decode($descr);
          $data["titulo"] = "Nuevo Sub Categoria";
          $data["id"]     = $id;
          $data["desc"]   = $descr;   
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('categorias/view_nuevo_subcategoria',$data);
          $this->load->view('view_footer');
     }
     public function editarsubcategoria($id,$descr,$idsubcategoria){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $id                   = base64_decode($id);
          $descr                = base64_decode($descr);
          $idsubcategoria       = base64_decode($idsubcategoria);
          $data["titulo"]       = "Editar Sub Categoria";
          $data["id"]           = $id;
          $data["desc"]         = $descr;   
          $data["subcategoria"] = $this->categorias_model->BuscaSubCategoriaOne($idsubcategoria);
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('categorias/view_nuevo_subcategoria',$data);
          $this->load->view('view_footer');
     }
     public function SaveSubcategoria(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $SubCategoria           = json_decode($this->input->post('SubCategoriasPost'));
          $response = array (
                    "estatus"   => false,
                    "campo"     => "",
                 "error_msg" => ""
         );
           if($SubCategoria->IdCategoria=="0"){
               $response["campo"]     = "categoria";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>La Categoria es Obligatorio</div>";
               echo json_encode($response);
           }else if($SubCategoria->Descripcion==""){
               $response["campo"]     = "descripcion";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>La SubCategoria es Obligatorio</div>";
               echo json_encode($response);
          }else if($SubCategoria->Estatus=="0"){
               $response["campo"]     = "estatus";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Estatus es obligatorio</div>";
               echo json_encode($response);
          }else{
                    $arrayCategoria    = array(
                         "id_categoria"=> $SubCategoria->IdCategoria,
                         "descripcion" => $SubCategoria->Descripcion,
                         "estatus"     => $SubCategoria->Estatus
                         );
               if($SubCategoria->Id==""){
                    $this->categorias_model->SaveSubCategoria($arrayCategoria);
                    $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Informacion Guardada Correctamente</div>";
                    echo json_encode($response);
               }
               if($SubCategoria->Id != ""){
                    $this->categorias_model->UpdateSubCategoria($arrayCategoria,$SubCategoria->Id);
                    $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
                    echo json_encode($response);
               }
          }
     }
     public function nuevo(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $data["titulo"] = "Nuevo Categoria";
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('categorias/view_nuevo_categoria',$data);
          $this->load->view('view_footer');
     }
     public function Editar($id){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $id                = base64_decode($id);
          $data["categoria"] = $this->categorias_model->BuscaCategorias($id);
          $data["titulo"]    = "Editar Categoria";
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('categorias/view_nuevo_categoria',$data);
          $this->load->view('view_footer');
     }
     public function DeleteCategoria(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $Categoria          = json_decode($this->input->post('MiCategoria'));
          $id                 = base64_decode($Categoria->Id);
          /*Array de response*/
           $response = array (
                    "estatus"   => false,
                 "error_msg" => ""
         );
          $this->categorias_model->EliminarCategoria($id);
          $this->categorias_model->EliminarSubcategorias($id);
          $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Categoria Eliminado Correctamente, La Información de Actualizara en 5 Segundos <meta http-equiv='refresh' content='5'></div>";
          echo json_encode($response);
     }
     public function DeleteSubcategoria(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $SubCategoria          = json_decode($this->input->post('MiSubCategoria'));
          $id                    = base64_decode($SubCategoria->Id);
           /*Array de response*/
           $response = array (
                    "estatus"   => false,
                 "error_msg" => ""
         );
          $this->categorias_model->EliminaSubcategoria($id);
          $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>SubCategoria Eliminado Correctamente, La Información de Actualizara en 5 Segundos <meta http-equiv='refresh' content='5'></div>";
          echo json_encode($response);
     }
     public function Subcategoria($id,$descr){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $id                   = base64_decode($id);
          $descr                = base64_decode($descr);
          $data["subcategoria"] = $this->categorias_model->BuscaSubCategorias($id);
          $data["descripcionS"] = $descr;
          $data["idcategoria"]  = $id;
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('categorias/view_subcategoria',$data);
          $this->load->view('view_footer');
     }
     public function SaveCategoria(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $Categoria           = json_decode($this->input->post('CategoriasPost'));
          $response = array (
                    "estatus"   => false,
                    "campo"     => "",
                 "error_msg" => ""
         );
           if($Categoria->Descripcion==""){
               $response["campo"]     = "descripcion";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>La Categoria es Obligatorio</div>";
               echo json_encode($response);
          }else if($Categoria->Estatus=="0"){
               $response["campo"]     = "estatus";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Estatus es obligatorio</div>";
               echo json_encode($response);
          }else{
                    $arrayCategoria    = array(
                         "descripcion" => $Categoria->Descripcion,
                         "estatus"     => $Categoria->Estatus
                         );
               if($Categoria->Id==""){
                    $this->categorias_model->SaveCategoria($arrayCategoria);
                    $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Informacion Guardada Correctamente</div>";
                    echo json_encode($response);
               }
               if($Categoria->Id != ""){
                    $this->categorias_model->UpdateCategoria($arrayCategoria,$Categoria->Id);
                    $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
                    echo json_encode($response);
               }
          }
     }
}