<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class seguridad_model extends CI_Model {

    public $_session = '';

    function __construct() {
        parent::__construct();
        $this->_session = $this->nativesession->get('arrDataSesion');
    }

    public function SessionActivo($url) {
        if ($this->_session['is_logged_in']) {
            return true;
        } else {
            redirect(base_url());
        }
    }

    public function verificaPermisoModulo($usuario = '') {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $nomModulo = pathinfo($url, PATHINFO_FILENAME);
        $sql = "SELECT COUNT(*) AS total FROM usuario_modulo WHERE usuario ='$usuario' AND  modulo ='$nomModulo'";
        $query = $this->db->query($sql);
        $query = $query->row();
        $total = $query->total;
        if ($total > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function verificaSesion() {
        if ($this->_session['is_logged_in']) {
            return true;
        } else {
            return false;
        }
    }

    function registraAcceso($vusucod = '') {
        $data = array(
            'usucod' => $vusucod,
            'ip' => $this->obtenerDireccionIP(),
            'navegador' => '');

        $this->db->insert('wp_accesos', $data);
    }

    function registraNavegacion($vmodulo = '') {
        $data = array(
            'usucod' => $this->_session ['USUCOD'],
            'modulo' => $vmodulo);
        $this->db->insert('wp_navegacion', $data);
    }

    function obtenerDireccionIP() {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    function verificarHorario() {
        if (date("H:i:s") > "07:00:00" AND date("H:i:s") < "20:10:00") {
            return true;
        } else {
            return false;
        }
    }

    function restringirIp() {
        $arrIp = array();
        $ipCliente = $this->obtenerDireccionIP();
        $sql = "select * from wp_control ";
        $query = $this->db->query($sql);
        $arrData = $query->result();
        foreach ($arrData as $row) {
            $arrIp[] = $row->ip;
        }
        /* print_r($arrIp)."<br>";
          echo $ipCliente;
          exit; */
        if (in_array($ipCliente, $arrIp)) {
            return true;
        } else {
            //redirect (base_url ());
            return false;
            // echo "<CENTER>ACCESO NO PERMITIDO</CENTER>";
            // exit;
        }
    }
    
    function getEstadistica($anio = ''){
        $sql = "SELECT COUNT(*) AS totalRazon1
                    FROM fercmias_sistemasdev.sga_matricula m
                    INNER JOIN fercmias_academico.salon s ON s.nemo=m.nemo 
                    WHERE m.periodo = '$anio' AND m.estado='M' 
                    AND s.instrucod IN ('I','P')
                    UNION ALL
                    SELECT COUNT(*) AS totalRazon2
                    FROM fercmias_sistemasdev.sga_matricula m
                    INNER JOIN fercmias_academico.salon s ON s.nemo=m.nemo 
                    WHERE m.periodo = '$anio' AND m.estado='M' 
                    AND s.instrucod IN ('S') ";
        $query = $this->db->query($sql);
        return $query->result();        
         
    }

}
