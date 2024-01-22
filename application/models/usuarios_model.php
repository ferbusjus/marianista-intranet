<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class usuarios_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function ListarUsuarios()
    {
        $sql = "SELECT  * from usuarios ORDER BY  NOMBRE ASC ";
        $query = $this->db->query($sql);
        return $query->result();
    }

    
   public function buscarUsuarioxPerfil($usucod, $perfil=0)
    {
        $sql = "SELECT  * from usuarios where usucod='" . $usucod . "' ";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    public function getMailComunicado(){
        $sql = "SELECT  LOWER(REPLACE(email,' ','')) AS email FROM usuarios WHERE id_perfil=4 AND email !='' AND LOCATE('@', email)>0  "; //AND id=7
        $query = $this->db->query($sql);
        return $query->result();        
    }
    
    public function BuscarUsuario($id)
    {
        $sql = "SELECT  * from usuarios where ID='" . $id . "' limit 1 ";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function SaveUsuarios($array)
    {
        $id = 0;
        $this->db->trans_start();
        $this->db->insert('usuarios', $array);
        $id = $this->db->insert_id();
        $this->db->trans_complete();
        return $id;
    }

    public function UpdateUsers($array, $id)
    {
        $this->db->trans_start();
        $this->db->where('ID', $id);
        $this->db->update('usuarios', $array);
        $this->db->trans_complete();
    }

    public function ExisteEmail($email)
    {
        $this->db->where("EMAIL", $email);
        $check_exists = $this->db->get("usuarios");
        if ($check_exists->num_rows() == 0)
        {
            return false;
        } else
        {
            return true;
        }
    }

    public function EliminarUsuario($id)
    {
        $this->db->where('ID', $id);
        return $this->db->delete('usuarios');
    }

    public function AsignaPermisosAdmin($array)
    {
        $this->db->trans_start();
        $this->db->insert('accesosusuarios', $array);
        $this->db->trans_complete();
    }

}