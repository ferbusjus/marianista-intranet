<?php

class becas_model extends CI_Model {

    var $column_order = array('B.ANOBEC', 'B.ALUCOD', 'B.DNI', 'B.MESINIBECA', 'B.MESFINBECA', 'B.BECACOD', 'B.FECREG');
    var $column_search = array('a.nomcomp');
    var $order = array('b.fecreg' => 'desc');
    public $tabla = '';
    public $ano = '';

    function __construct() {
        parent::__construct();
        $this->table = LIBRERIA . '.wp_beca_otorgada';
        $this->ano = $this->nativesession->get('S_ANO_VIG');
    }

    public function _get_datatables_query() {
        $this->db->select('b.anobec,b.alucod,b.dni,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod, b.becacod,t.becades,b.mesinibec,
                                        mi.mesdes AS mesini,b.mesfinbec,mf.mesdes AS mesfin,b.fecreg,b.usureg,b.fecmod,b.usumod');
        $this->db->from($this->table . " as b");
        $this->db->join(LIBRERIA2 . ".alumno as a", "a.alucod=b.alucod", "INNER");
        $this->db->join(LIBRERIA . ".wp_meses as mi", "mi.mescod=b.mesinibec", "INNER");
        $this->db->join(LIBRERIA . ".wp_meses as mf", "mf.mescod=b.mesfinbec", "INNER");
        $this->db->join(LIBRERIA . ".wp_beca_tipo as t", "t.becacod=b.becacod and t.anobec=" . $this->ano, "INNER");
        $this->db->where("b.anobec", 'YEAR(NOW())', false);
        $i = 0;

        if ($_POST['idbeca'] != '' && $_POST['idbeca'] != 0) {
            $idbeca = $_POST['idbeca'];
            $this->db->where("b.becacod", $idbeca);
        }
        foreach ($this->column_search as $item) { // loop column 
            if ($_POST['search']['value']) { // if datatable send POST for search
                $this->db->like($item, $_POST['search']['value']);
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->select('b.anobec,b.alucod,b.dni,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod, b.becacod,t.becades,b.mesinibec,
                                        mi.mesdes AS mesini,b.mesfinbec,mf.mesdes AS mesfin,b.fecreg,b.usureg,b.fecmod,b.usumod');
        $this->db->from($this->table . " as b");
        $this->db->join(LIBRERIA2 . ".alumno as a", "a.alucod=b.alucod", "INNER");
        $this->db->join(LIBRERIA . ".wp_meses as mi", "mi.mescod=b.mesinibec", "INNER");
        $this->db->join(LIBRERIA . ".wp_meses as mf", "mf.mescod=b.mesfinbec", "INNER");
        $this->db->join(LIBRERIA . ".wp_beca_tipo as t", "t.becacod=b.becacod and t.anobec=" . $this->ano, "INNER");
        $this->db->where("b.anobec", 'YEAR(NOW())', false);
        return $this->db->count_all_results();
    }

    public function grabaBecaAlumno($arrData = array()) {
        try {
            $query = $this->db->insert('wp_beca_otorgada', $arrData);
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

    function updateBecaAlumno($arrData = array(), $vId = 0, $vano = '') {
        /*  $this->db->where ('id', $vId);
          $query = $this->db->delete ('wp_egresos'); */
        $this->db->where('ALUCOD', $vId);
        $this->db->where('ANOBEC', $vano);
        $query = $this->db->update('wp_beca_otorgada', $arrData);
        if (!$query)
            return false;
        else
            return true;
    }

    function eliminaBeca($vIdAlumno = '', $vIdBeca = '', $vano = '') {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_ELIMINA_BECA('$vIdAlumno','$vIdBeca', $vano) ";
            $query = $this->db->query($sql);
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

    function updatePagosxBecas($vIdAlumno = '', $vIdBeca = '', $vano = '') {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_ACTUALIZAR_PAGOS_X_BECA('$vIdAlumno','$vIdBeca',$vano) ";
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

    public function getTipoBecas() {
        try {
            $sql = "select * from  wp_beca_tipo where flgactivo=1 and anobec=" . $this->ano;
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

    public function getMotivoBecas() {
        try {
            $sql = "select * from  wp_beca_motivo where flgactivo=1";
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
