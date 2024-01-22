<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class ventas extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('seguridad_model');
		$this->load->model('ventas_model');
		$this->load->model('ordencompra_model');
	}
	public function index(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/   
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('ventas/view_ventas');
          $this->load->view('view_footer');
          
	}
	public function BuscarCliente(){
		$filtro    = $this->input->get("term");
		$clientes = $this->ventas_model->buscarcliente($filtro);
		echo json_encode($clientes);
	}
	public function buscarproductos(){
		$filtro    = $this->input->get("term");
		$productos = $this->ordencompra_model->listarproducto($filtro);
		echo json_encode($productos);
	}
	public function ImprimeVenta($numOrder){
		$numOrder         = base64_decode($numOrder);
		$data["NumOrder"] = $numOrder;
		$data["ListOrder"]= $this->ventas_model->TraeVenta($numOrder);
		$data["DocOrder"] = $this->ventas_model->TraeDoc($numOrder);
		 $this->load->view('constant');
        $this->load->view('ventas/view_print_venta',$data);
	}
	public function saveOrder(){
		session_start();
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$arrayResponse = array("NumOrden"=>"0","Msg"=>"Error: Ocurrio Un Error Intente de Nuevo", "TipoMsg"=>"Error");

		$OrderVenta    = json_decode($this->input->post('MiCarrito'));
		$RecuperaOrder = $_SESSION["CarritoVenta".$OrderVenta->IdSession];
		$RecuperaOrder = $_SESSION["CarritoVenta".$OrderVenta->IdSession];
		$impuesto      = 16;
		$arrayDocumento= array(
				"TIPO"				=>"Venta", 
				"FECHA"				=>date('Y-m-j H:i:s'),
				"CLIENTE"			=>$OrderVenta->Cliente,
				"BASEIMPUESTO"		=>$impuesto,
				"TOTAL_IMPUESTO"	=>$OrderVenta->IVA,
				"BRUTO"				=>$OrderVenta->Subtotal,
				"TOTAL"				=>$OrderVenta->Total,
				"USUARIO"			=>$this->session->userdata('ID')
				);
		$saveOrderDocument = $this->ordencompra_model->saveOrderDocumento($arrayDocumento);
		if($saveOrderDocument!=0){
			foreach ($RecuperaOrder as $key => $value) {
				$arrayPartidas = array(
					"ID_LINK"			=> $saveOrderDocument,
					"TIPO"				=> "Venta",
					"FECHA"				=> date('Y-m-j H:i:s'),
					"CLIENTE"			=> $OrderVenta->Cliente,
					"PROVEEDOR"			=> $value["IdProveedor"],
					"CLAVE"				=> $value["txtCodigo"],
					"COSTO"				=> $value["costo"],
					"PRECIO"			=> $value["precio"],
					"DESCRIPCION"		=> $value["descripcion"],
					"SALIDAS"			=> $value["cantidad"]
					);
				# code...
				$this->ordencompra_model->saveOrderPartidas($arrayPartidas);
				$this->ventas_model->UpdateExistenciasProducto($value["txtCodigo"],$value["cantidad"]);

			}
			$arrayResponse = array("NumOrden"=>base64_encode($saveOrderDocument),"Msg"=>"<strong>Folio: ".$saveOrderDocument."</strong>, La Venta se Guardado Correctamente", "TipoMsg"=>"Sucefull");
		}
		echo json_encode($arrayResponse);
		
	}
	public function addcarrito(){
		session_start();
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
		$CarritoNewVenta   = json_decode($this->input->post('MiCarrito'));
		if(isset($_SESSION['CarritoVenta'.$CarritoNewVenta->IdSession])){
				$carrito_orderventa=$_SESSION['CarritoVenta'.$CarritoNewVenta->IdSession];
				if(isset($CarritoNewVenta->Codigo)){
					$txtCodigo = $CarritoNewVenta->Codigo;
					$precio    = $CarritoNewVenta->Pcompra;
					$cantidad  = $CarritoNewVenta->Cantidad;
					$descripcio= $CarritoNewVenta->Descripcion;
					$Proveedor = $CarritoNewVenta->Proveedor;
					$Costo     = $CarritoNewVenta->Costo;
					$IdProveedo= $CarritoNewVenta->IdProveedor;
					$donde     = -1;
					for($i=0;$i<=count($carrito_orderventa)-1;$i ++){
					if($txtCodigo==$carrito_orderventa[$i]['txtCodigo']){
						$donde=$i;
					}
					}
					if($donde != -1){
						$cuanto=$carrito_orderventa[$donde]['cantidad'] + $cantidad;
						$carrito_orderventa[$donde]=array("txtCodigo"=>$txtCodigo,"precio"=>$precio,"cantidad"=>$cuanto,"descripcion"=>$descripcio,"proveedor"=>$Proveedor,"costo"=>$Costo,"IdProveedor"=>$IdProveedo);
					}else{
						$carrito_orderventa[]=array("txtCodigo"=>$txtCodigo,"precio"=>$precio,"cantidad"=>$cantidad,"descripcion"=>$descripcio,"proveedor"=>$Proveedor,"costo"=>$Costo,"IdProveedor"=>$IdProveedo);
						
					}
				}
		}else{
				$txtCodigo = $CarritoNewVenta->Codigo;
				$precio    = $CarritoNewVenta->Pcompra;
				$cantidad  = $CarritoNewVenta->Cantidad;
				$descripcio= $CarritoNewVenta->Descripcion;
				$Proveedor = $CarritoNewVenta->Proveedor;
				$Costo     = $CarritoNewVenta->Costo;
				$IdProveedo= $CarritoNewVenta->IdProveedor;
				$carrito_orderventa[]=array("txtCodigo"=>$txtCodigo,"precio"=>$precio,"cantidad"=>$cantidad,"descripcion"=>$descripcio,"proveedor"=>$Proveedor,"costo"=>$Costo,"IdProveedor"=>$IdProveedo);	
		}
		$_SESSION['CarritoVenta'.$CarritoNewVenta->IdSession]=$carrito_orderventa;
		echo json_encode($_SESSION['CarritoVenta'.$CarritoNewVenta->IdSession]);
	}

}