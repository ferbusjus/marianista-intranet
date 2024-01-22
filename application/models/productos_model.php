<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class productos_model extends CI_Model {
	function __construct()
     {
          parent::__construct();
     }
	public function ListarProductos(){
		$sql="SELECT P.id, P.codigo, P.descripcion, P.precio_compra, P.precio_venta, P.cantidad, C.descripcion AS DesCategoria, Pro.nombre_proveedor FROM productos AS P INNER JOIN categorias AS C ON P.id_categoria = C.id INNER JOIN proveedores AS Pro ON P.id_proveedor = Pro.id order by P.descripcion asc";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function BuscarProducto($id){
		$sql="SELECT * FROM productos  where id='".$id."'";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function EliminarProducto($id)
	{
		# code...
		$this->db->where('id',$id);
		return $this->db->delete('productos');
	}
	public function Categorias(){
		$sql="select * from categorias";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function Subcategorias($id){
		$sql="select * from subcategoria where id_categoria='".$id."'";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function Proveedores(){
		$sql="SELECT  * FROM proveedores where estatus=1";
		$query=$this->db->query($sql);
		return $query->result();
	}
	public function ExisteCodigo($codigo){
		$this->db->where("codigo",$codigo);
        $check_exists = $this->db->get("productos");
        if($check_exists->num_rows() == 0){
            return false;
        }else{
            return true;
        }
	}
	public function SaveProductos($arrayProductos){
		$this->db->trans_start();
     	$this->db->insert('productos', $arrayProductos);
     	$this->db->trans_complete();
	}
	public function UpdateProductos($arrayProductos, $id){
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('productos', $arrayProductos); 
		$this->db->trans_complete();
	}
}