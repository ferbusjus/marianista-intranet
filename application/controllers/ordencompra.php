<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class ordencompra extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('seguridad_model');
		$this->load->model('ordencompra_model');
	}
	public function index(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('ordencompra/orden_compra');
          $this->load->view('view_footer');
          
	}
	public function buscarproductos(){
		$filtro    = $this->input->get("term");
		$productos = $this->ordencompra_model->listarproducto($filtro);
		echo json_encode($productos);
	}
	public function addcarrito(){
		session_start();
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$CarritoOrdenCompra   = json_decode($this->input->post('MiCarrito'));
		if(isset($_SESSION['CarritoOrdencompra'.$CarritoOrdenCompra->IdSession])){
				$carrito_orderventa=$_SESSION['CarritoOrdencompra'.$CarritoOrdenCompra->IdSession];
				if(isset($CarritoOrdenCompra->Codigo)){
					$txtCodigo = $CarritoOrdenCompra->Codigo;
					$precio    = $CarritoOrdenCompra->Pcompra;
					$cantidad  = $CarritoOrdenCompra->Cantidad;
					$descripcio= $CarritoOrdenCompra->Descripcion;
					$Proveedor = $CarritoOrdenCompra->Proveedor;
					$Costo     = $CarritoOrdenCompra->Costo;
					$donde     = -1;
					for($i=0;$i<=count($carrito_orderventa)-1;$i ++){
					if($txtCodigo==$carrito_orderventa[$i]['txtCodigo']){
						$donde=$i;
					}
					}
					if($donde != -1){
						$cuanto=$carrito_orderventa[$donde]['cantidad'] + $cantidad;
						$carrito_orderventa[$donde]=array("txtCodigo"=>$txtCodigo,"precio"=>$precio,"cantidad"=>$cuanto,"descripcion"=>$descripcio,"proveedor"=>$Proveedor,"costo"=>$Costo);
					}else{
						$carrito_orderventa[]=array("txtCodigo"=>$txtCodigo,"precio"=>$precio,"cantidad"=>$cantidad,"descripcion"=>$descripcio,"proveedor"=>$Proveedor,"costo"=>$Costo);
						
					}
				}
		}else{
				$txtCodigo = $CarritoOrdenCompra->Codigo;
				$precio    = $CarritoOrdenCompra->Pcompra;
				$cantidad  = $CarritoOrdenCompra->Cantidad;
				$descripcio= $CarritoOrdenCompra->Descripcion;
				$Proveedor = $CarritoOrdenCompra->Proveedor;
				$Costo     = $CarritoOrdenCompra->Costo;
				$carrito_orderventa[]=array("txtCodigo"=>$txtCodigo,"precio"=>$precio,"cantidad"=>$cantidad,"descripcion"=>$descripcio,"proveedor"=>$Proveedor,"costo"=>$Costo);	
		}
		$_SESSION['CarritoOrdencompra'.$CarritoOrdenCompra->IdSession]=$carrito_orderventa;
		echo json_encode($_SESSION['CarritoOrdencompra'.$CarritoOrdenCompra->IdSession]);
	}
	public function saveOrder(){
		session_start();
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$arrayResponse = array("NumOrden"=>"0","Msg"=>"Error: Ocurrio Un Error Intente de Nuevo", "TipoMsg"=>"Error");
		$OrderCompra   = json_decode($this->input->post('MiCarrito'));
		$RecuperaOrder = $_SESSION["CarritoOrdencompra".$OrderCompra->IdSession];
		$impuesto      = 16;
		$arrayDocumento= array(
				"TIPO"				=>"Entrada", 
				"FECHA"				=>date('Y-m-j H:i:s'),
				"BASEIMPUESTO"		=>$impuesto,
				"TOTAL_IMPUESTO"	=>$OrderCompra->IVA,
				"BRUTO"				=>$OrderCompra->Subtotal,
				"TOTAL"				=>$OrderCompra->Total,
				"USUARIO"			=>$this->session->userdata('ID')
				);
		$saveOrderDocument = $this->ordencompra_model->saveOrderDocumento($arrayDocumento);
		if($saveOrderDocument!=0){
			foreach ($RecuperaOrder as $key => $value) {
				$arrayPartidas = array(
					"ID_LINK"			=> $saveOrderDocument,
					"TIPO"				=> "Entrada",
					"FECHA"				=> date('Y-m-j H:i:s'),
					"PROVEEDOR"			=> $value["proveedor"],
					"CLAVE"				=> $value["txtCodigo"],
					"COSTO"				=> $value["costo"],
					"PRECIO"			=> $value["precio"],
					"DESCRIPCION"		=> $value["descripcion"],
					"ENTRADAS"			=> $value["cantidad"]
					);
				# code...
				$this->ordencompra_model->saveOrderPartidas($arrayPartidas);
				$this->ordencompra_model->UpdateExistenciasProducto($value["txtCodigo"],$value["cantidad"]);

			}
			$arrayResponse = array("NumOrden"=>$saveOrderDocument,"Msg"=>"<strong>Folio: ".$saveOrderDocument."</strong>, La Orden de Compra se Guardado Correctamente", "TipoMsg"=>"Sucefull");
		}
		echo json_encode($arrayResponse);
		
	}
	

}