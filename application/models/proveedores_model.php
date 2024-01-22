<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class proveedores_model extends CI_Model {
	function __construct()
     {
          parent::__construct();
     }
	public function ListarProveedores(){
		$sql="SELECT * from proveedores order by nombre_proveedor asc";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function BuscaProveedores($id){
		$sql="SELECT * from proveedores where id='".$id."' limit 1";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function SaveProveedores($RegistraProveedor){
		$this->db->trans_start();
     	$this->db->insert('proveedores', $RegistraProveedor);
     	$this->db->trans_complete();
	}
	public function UpdateProveedores($UpdateProveedor,$id){
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('proveedores', $UpdateProveedor); 
		$this->db->trans_complete();
	}
}