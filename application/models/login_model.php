<?php

if (!defined ('BASEPATH'))
    exit ('No direct script access allowed');

class login_model extends CI_Model
{

    function __construct ()
    {
        parent::__construct ();
    }

    public function CreaMenu ($idPerfil = 0)
    {
        $sql = "SELECT m.tipo, m.id_menu,m.dsc_menu,m.url_menu FROM s_menu AS m
                        INNER JOIN s_menu_perfil AS mp ON mp.id_menu=m.id_menu
                        INNER JOIN s_perfil AS p ON p.id_perfil=mp.id_perfil 
                    WHERE p.id_perfil=$idPerfil AND m.flg_activo=1  AND mp.id_sistema=" . SISEXTRA . "
                        ORDER BY mp.orden ";

        $query = $this->db->query ($sql);
        return $query->result ();
    }
    
    public function menuPadre ($idPerfil = 0)
    {
        $sql = "SELECT m.tipo, m.id_menu,m.dsc_menu,m.url_menu,mp.orden FROM s_menu AS m
                        INNER JOIN s_menu_perfil AS mp ON mp.id_menu=m.id_menu
                        INNER JOIN s_perfil AS p ON p.id_perfil=mp.id_perfil 
                    WHERE p.id_perfil=$idPerfil AND m.flg_activo=1   AND m.tipo='M' AND mp.id_sistema=" . SISEXTRA . "
                        ORDER BY mp.orden ";
        $query = $this->db->query ($sql);
        return $query->result ();
    }
    public function menuHijo ($idPerfil = 0,$idPadre=0)
    {
        $sql = "SELECT m.tipo, m.id_menu,m.dsc_menu,m.url_menu,mp.orden FROM s_menu AS m
                        INNER JOIN s_menu_perfil AS mp ON mp.id_menu=m.id_menu
                        INNER JOIN s_perfil AS p ON p.id_perfil=mp.id_perfil 
                    WHERE p.id_perfil=$idPerfil AND m.flg_activo=1   AND m.tipo='O' AND m.menupad=$idPadre AND mp.id_sistema=" . SISEXTRA . "
                        ORDER BY mp.orden ";
        $query = $this->db->query ($sql);
        return $query->result ();
    }    

    function LoginBD ($username = '',$vperfil=0)
    {
        if($vperfil==0){
            $vperfil = $this->getPerfilxUsuario($username);
        }
        $this->db->where ('usucod', strtoupper (trim($username)));
        $this->db->where('id_sistema', SISEXTRA);
        $this->db->where('id_perfil', $vperfil);        
         $this->db->where('ESTATUS', 1);        
        $query = $this->db->get ('usuarios')->row ();
        //echo "Cadena :".$this->db->last_query(); exit;
        return $query;
    }

    function getPerfil ($idPerfil = 0)
    {
        $this->db->select ("dsc_perfil");
        $this->db->where ('id_perfil', $idPerfil);
        $row = $this->db->get ('s_perfil')->row ();
        return $row->dsc_perfil;
    }

    function getPerfilxUsuario ($idUsuario = '')
    {
        $this->db->select ("id_perfil");
        $this->db->where ('usucod', strtoupper(trim($idUsuario)));
        $row = $this->db->get ('usuarios')->row ();
        return $row->id_perfil;
    }
    
}
