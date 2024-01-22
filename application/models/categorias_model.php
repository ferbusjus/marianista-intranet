<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class categorias_model extends CI_Model {
	function __construct()
     {
          parent::__construct();
     }
	public function ListarCategorias(){
		$sql="SELECT  * from categorias ORDER BY  descripcion ASC ";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function EliminarCategoria($id){
		$this->db->where('id',$id);
		return $this->db->delete('categorias');
	}
	public function EliminarSubcategorias($id){
		$this->db->where('id_categoria',$id);
		return $this->db->delete('subcategoria');
	}
	public function EliminaSubcategoria($id){
		$this->db->where('id',$id);
		return $this->db->delete('subcategoria');
	}
	public function BuscaCategorias($id){
		$sql="SELECT  * from categorias where id='".$id."' limit 1";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function BuscaSubCategorias($id){
		$sql="SELECT  * from subcategoria where id_categoria='".$id."'";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function BuscaSubCategoriaOne($id){
		$sql="SELECT  * from subcategoria where id='".$id."' limit 1";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function SaveCategoria($array){
		$this->db->trans_start();
     	$this->db->insert('categorias', $array);
     	$this->db->trans_complete();
	}
	public function UpdateCategoria($array,$id){
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('categorias', $array); 
		$this->db->trans_complete();
	}
	public function SaveSubCategoria($array){
		$this->db->trans_start();
     	$this->db->insert('subcategoria', $array);
     	$this->db->trans_complete();
	}
	public function UpdateSubCategoria($array,$id){
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('subcategoria', $array); 
		$this->db->trans_complete();
	}
	 
}