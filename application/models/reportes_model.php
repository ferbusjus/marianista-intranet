<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reportes_model extends CI_Model {
	function __construct()
     {
          parent::__construct();
     }
     public function buscarcliente($filtro){
		$sql="SELECT concat(CODIGO_CLIENTE,' - ', NOMBRE, ' ', APELLIDOS) AS label, CODIGO_CLIENTE, NOMBRE, APELLIDOS FROM clientes   WHERE (CODIGO_CLIENTE LIKE  '%".$filtro."%' or NOMBRE LIKE '%".$filtro."%')";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function UpdateExistenciasProducto($codigo,$cantidad){
		$sql="update productos set cantidad= cantidad - '".$cantidad."' where codigo='".$codigo."'";
		$query=$this->db->query($sql);
		return True;
	}
	public function reportesGenera($FInicial, $FFinal, $Documento){
		$sql="SELECT * FROM documentos   WHERE FECHA between '".$FInicial."' AND '".$FFinal."' AND TIPO='".$Documento."'";
		//echo $sql;
		$query=$this->db->query($sql);
		return $query->result();
	}

}