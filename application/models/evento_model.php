<?php

class evento_model extends CI_Model {

    var $table = '';

    function __construct() {
        parent::__construct();
        $this->table = LIBRERIA . '.sga_citas_psicologica';
    }
 
    public function getCitas() {
        $this->db->select('p.idcita as id, a.nomcomp, p.titulo as title,s.nemodes as aula, p.color, p.feciniatencion as start, p.fecfinatencion as end,p.str_observacion');
        $this->db->from($this->table." as p");
        $this->db->join(LIBRERIA2.".alumno as a", "a.alucod=p.alucod");
        $this->db->join(LIBRERIA2.".salon_al as sa", "sa.alucod=a.alucod and sa.ano='".ANO_VIG."'");
        $this->db->join(LIBRERIA2.".salon as s", "sa.nemo=s.nemo and s.ano='".ANO_VIG."'");
        $this->db->where("flg_activo", 1);
       //  $this->db->where("p.idcita NOT IN  (152)");

        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        return $query->result();
    }
    
    public function add($arrData = array()) {
        try {
            $query = $this->db->insert($this->table, $arrData);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return TRUE;
        } catch (Exception $e) {
            $arrayError = array(
                'Problema' => $e->getMessage(),
                'Clase' => __CLASS__,
                'Metodo' => __FUNCTION__,
                'Archivo' => __FILE__,
                'Linea' => __LINE__,
                'Query' => print_r($this->db->last_query(), TRUE)
            );
            notificaError($arrayError);
            //return $e->getMessage();
            return false;
        }
    }

    public function edit($arrData = array(), $vId = '') {
        try {
            $this->db->where('idcita', $vId);
            $query = $this->db->update($this->table, $arrData);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return 1;
        } catch (Exception $e) {
            $arrayError = array(
                'Problema' => $e->getMessage(),
                'Clase' => __CLASS__,
                'Metodo' => __FUNCTION__,
                'Archivo' => __FILE__,
                'Linea' => __LINE__,
                'Query' => print_r($this->db->last_query(), TRUE)
            );
            notificaError($arrayError);
            return $e->getMessage();
        }
    }

    function delete($vId = '0') {
        /*  $this->db->where ('id', $vId);
          $query = $this->db->delete ($this->table); */
        $data = array('flg_activo' => 0);
        $this->db->where('idcita', $vId);
        $query = $this->db->update($this->table, $data);
        if (!$query)
            return false;
        else
            return true;
    }

}
