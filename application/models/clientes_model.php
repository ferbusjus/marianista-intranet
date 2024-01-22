<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class clientes_model extends CI_Model {
	function __construct()
     {
          parent::__construct();
     }
	public function ListarClientes(){
		$sql="SELECT  * from clientes ORDER BY  CODIGO_CLIENTE ASC ";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function UltimoCliente()
	{
		$sql = "SELECT CODIGO_CLIENTE FROM clientes ORDER BY CODIGO_CLIENTE DESC limit 1";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function BuscaCP($cp){
		$sql="SELECT  * from codigospostales where CodigoPostal='".$cp."' limit 1";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function BuscaCliente($id){
		$sql="SELECT  * from clientes where ID='".$id."' limit 1";
		echo $sql;
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function EliminarCliente($id)
	{
		# code...
		$this->db->where('ID',$id);
		return $this->db->delete('clientes');
	}
	public function ExisteEmail($email){
		$this->db->where("EMAIL",$email);
        $check_exists = $this->db->get("clientes");
        if($check_exists->num_rows() == 0){
            return false;
        }else{
            return true;
        }
	}
	public function ExisteRFC($rfc){
		$this->db->where("RFC",$rfc);
        $check_exists = $this->db->get("clientes");
        if($check_exists->num_rows() == 0){
            return false;
        }else{
            return true;
        }
	}
	public function SaveClientes($arrayClientes){
		$this->db->trans_start();
     	$this->db->insert('clientes', $arrayClientes);
     	$this->db->trans_complete();
	}
	public function UpdateClientes($arrayClientes,$id){
		$this->db->trans_start();
		$this->db->where('ID', $id);
		$this->db->update('clientes', $arrayClientes); 
		$this->db->trans_complete();
	}
	public function SaveDireccion($arrayDir){
		$this->db->trans_start();
     	$this->db->insert('direcciones_envio', $arrayDir);
     	$this->db->trans_complete();
	}
}