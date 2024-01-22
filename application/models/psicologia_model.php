<?php

class psicologia_model extends CI_Model {

    var $table = '';
    var $column_order = array('aucod', 'fecha', 'alumno', 'motivo', 'ngs');
    var $column_search = array('a.nomcomp');
    var $order = array('p.fecreg' => 'desc');

    function __construct() {
        parent::__construct();
        $this->table = LIBRERIA . '.sga_citas_psicologica';
    }

    public function getCitas() {
        $this->db->select('id, title, color, start, end');
        $this->db->from(LIBRERIA . '.sga_citas');
        $this->db->where("flg_activo", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function _get_datatables_query($flgview = 1) {
        $this->db->select("p.idcita,p.hora,p.titulo,p.flg_asiste,p.color,p.alucod,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod,p.feciniatencion,p.fecfinatencion,p.str_observacion");
        $this->db->from($this->table . " as p");
        $this->db->join(LIBRERIA2 . ".alumno AS a", "a.alucod=p.alucod", "INNER");
        // $this->db->join(LIBRERIA.".wp_motivo AS m", "m.idmotivo=p.idmotivo", "INNER");
        $this->db->where("p.flg_activo", 1);

        $i = 0;
        if ($flgview == 1) {
            //if ($_POST['idestado']) { // -- Buscamos por Estado : ASISTIO / PENDIENTE
                $idEstado = $_POST['idestado'];
                $this->db->where("p.flg_asiste", $idEstado);
            //}
            foreach ($this->column_search as $item) { // loop column 
                if ($_POST['search']['value']) { // if datatable send POST for search
                    //if ($i === 0) { // first loop
                    //$this->db->group_start (); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                    //} else {  
                    // $this->db->or_like ($item, $_POST['search']['value']);
                    // }
                    // if (count ($this->column_search) - 1 == $i) //last loop
                    //  $this->db->group_end (); //close bracket
                }
                $i++;
            }

            if (isset($_POST['order'])) { // here order processing
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset($this->order)) {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
        } else {
            $query = $this->db->get();
            return $query->result();
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        // echo $this->db->last_query(); exit;
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->select("p.idcita,p.hora,p.titulo,p.flg_asiste, p.color,p.alucod,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod,p.feciniatencion,p.fecfinatencion,p.str_observacion");
        $this->db->from($this->table . " as p");
        $this->db->join(LIBRERIA2 . ".alumno AS a", "a.alucod=p.alucod", "INNER");
        //$this->db->join(LIBRERIA.".wp_motivo AS m", "m.idmotivo=p.idmotivo", "INNER");
        $this->db->where("p.flg_activo", 1);
        return $this->db->count_all_results();
    }

    public function grabarMotivos($arrData = array()) {
        try {
            $query = $this->db->insert(LIBRERIA . ".sga_citas_motivos", $arrData);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return true;
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
            return false;
        }
    }

    public function grabar($arrData = array()) {
        try {
            $query = $this->db->insert($this->table, $arrData);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return true;
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
            return false;
        }
    }

    public function update($arrData = array(), $vidcita = 0) {
        try {
            $this->db->where('idcita', $vidcita);
            $query = $this->db->update($this->table, $arrData);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return true;
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
            return false;
        }
    }

    function eliminaEgreso($vId = 0) {
        /*  $this->db->where ('id', $vId);
          $query = $this->db->delete ('wp_psicologia'); */
        $data = array('flg_activo' => 0);
        $this->db->where('id', $vId);
        $query = $this->db->update($this->table, $data);
        if (!$query)
            return false;
        else
            return true;
    }

    public function desactivaMotivosCita($idcita = 0) {
        $data = array('flg_activo' => 0);
        $this->db->where('idcita', $idcita, FALSE);
        $query = $this->db->update(LIBRERIA . ".sga_citas_motivos", $data);
        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }        
    }

    public function verificaRegistro($idcita = 0, $idmotivo = 0) {
        $query = $this->db->query("select count(*) as total  from  " . LIBRERIA . ".sga_citas_motivos where idcita=$idcita and idmotivo=$idmotivo");
        $query = $query->row();
        if ($query->total > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function modificaMotivos($idcita = 0, $idmotivo = 0) {
        try {
            $data = array('flg_activo' => 1);
            $this->db->where('idcita', $idcita, FALSE);
            $this->db->where('idmotivo', $idmotivo, FALSE);
            $query = $this->db->update(LIBRERIA . ".sga_citas_motivos", $data);
            //echo "Sql : ".$this->db->last_query(); exit;
            if ($query)
                return TRUE;
            else
                throw new Exception($this->db->_error_message());
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
            return FALSE;
        }
    }

    public function getMotivos() {
        try {
            $sql = "select idmotivo,descripcion from wp_motivo where flg_activo=1";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();
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

    public function getDataCita($idcita = 0) {
        try {
            $this->db->select("p.idcita,p.alucod,p.nemo,a.nomcomp,s.nemodes,p.feciniatencion,p.fecfinatencion,hora,str_acudieron,str_inteligencia,str_emocional,str_recomendacion");
            $this->db->from($this->table . " as p");
            $this->db->join(LIBRERIA2 . ".alumno AS a", "a.alucod=p.alucod", "INNER");
            $this->db->join(LIBRERIA2 . ".salon_al AS sa", "a.alucod=sa.alucod and sa.ano=".ANO_VIG, "INNER");
            $this->db->join(LIBRERIA2 . ".salon AS s", "sa.nemo=s.nemo and s.ano=".ANO_VIG, "INNER");
            $this->db->where("p.idcita", $idcita);
            $query = $this->db->get();

            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();
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

    public function getMotivoxCita($idcita = 0) {
        try {
            $sql = "select m.idmotivo,m.descripcion FROM " . LIBRERIA . ".sga_citas_motivos as cm  "
                    . "INNER JOIN " . LIBRERIA . ".wp_motivo AS m on m.idmotivo=cm.idmotivo "
                    . "WHERE cm.idcita=" . $idcita . " AND cm.flg_activo=1";
            $query = $this->db->query($sql);
            //echo "query : ".$this->db->last_query(); exit;
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();
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

    public function listaEgresos() {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_S_EGRESOS_ALL() ";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();
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

}
