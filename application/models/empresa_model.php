<?php

class empresa_model extends CI_Model {

    var $column_order = array('ruz', 'razon_social');
    var $column_search = array('idrazon');
    var $order = array('c.numrecibo' => 'desc');
    public $tabla = '';
    public $ano = '';

    function __construct() {
        parent::__construct();
        $this->table = LIBRERIA . '.sga_sunat';
        $this->ano = $this->nativesession->get('S_ANO_VIG');
    }

    public function _get_datatables_query() {
        /* $this->db->select("c.numrecibo, f.famdes AS familia,a.nomcomp AS alumno,CONCAT(cc.condes,' - ',m.mesdes,' - ',YEAR(NOW())) AS concepto,
          DATE(c.fecmod)  AS fechapago,SUM(c.montocob) AS montocobrado,'SOLES' AS moneda,s.nemodes, c.flgenvio"); */
        $this->db->select("c.numrecibo, f.famdes,a.nomcomp,cc.condes,m.mesdes,c.fecmod,c.montocob,c.flgenvio"); ///*,s.nemodes*/
        $this->db->from(LIBRERIA . ".wp_cobro as c");
        $this->db->join(LIBRERIA . '.wp_concobro as cc', ' cc.concob=c.concob');
        $this->db->join(LIBRERIA . '.wp_meses as m', ' m.mescod=c.mescob');
        $this->db->join(LIBRERIA2 . '.alumno as a', ' a.alucod=c.alucod');
        $this->db->join(LIBRERIA2 . '.familia as f', '  a.famcod=f.famcod ');
        //$this->db->join(LIBRERIA2 . '.salon_al as sa', ' sa.alucod=a.alucod AND sa.ano=' . $this->ano);
        //$this->db->join(LIBRERIA2 . '.salon as s', ' s.nemo=sa.nemo AND s.ano=' . $this->ano);
       // $this->db->where("c.anocob", $this->ano);
        $this->db->where("c.tipo_comp", "02");
       //$this->db->where("c.flgenvio", "0"); // Descomentar cuando se quiera enviar boletas pasadas  con secuencia 02
        //$this->db->where("c.flgsunat", "2");
        $this->db->where("c.estado", "C");
        $this->db->group_by("c.numrecibo,c.alucod,c.concob,c.mescob");

        if ($_POST['vfecha'] != '') {
            $txtFecha = $_POST['vfecha'];
            $this->db->where("DATE(c.fecmod)", $txtFecha);
        }

        if ($_POST['vrazon'] != '') {
            $txtRazon = $_POST['vrazon'];
            $this->db->where("c.tipo_razon", $txtRazon);
        }

        $i = 0;
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
        //   echo $this->db->last_query(); exit;
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($vFecha = '', $vRazon = '') {
        $this->db->select("c.numrecibo, f.famdes,a.nomcomp,cc.condes,m.mesdes,c.fecmod,c.montocob,c.flgenvio"); ///*,s.nemodes*/
        $this->db->from(LIBRERIA . ".wp_cobro as c");
        $this->db->join(LIBRERIA . '.wp_concobro as cc', ' cc.concob=c.concob');
        $this->db->join(LIBRERIA . '.wp_meses as m', ' m.mescod=c.mescob');
        $this->db->join(LIBRERIA2 . '.alumno as a', ' a.alucod=c.alucod');
        $this->db->join(LIBRERIA2 . '.familia as f', '  a.famcod=f.famcod ');
        //$this->db->join(LIBRERIA2 . '.salon_al as sa', ' sa.alucod=a.alucod AND sa.ano=' . $this->ano);
        //$this->db->join(LIBRERIA2 . '.salon as s', ' s.nemo=sa.nemo AND s.ano=' . $this->ano);
        //$this->db->where("c.anocob", $this->ano);
        $this->db->where("c.tipo_comp", "02");
        //$this->db->where("c.flgenvio", "0"); // Descomentar cuando se quiera enviar boletas pasadas  con secuencia 02
        // $this->db->where("c.flgsunat", "2");
        $this->db->where("c.estado", "C");
        $this->db->group_by("c.numrecibo,c.alucod,c.concob,c.mescob");

        if ($vFecha != '') {
            $this->db->where("DATE(c.fecmod)", $vFecha);
        }
        if ($vRazon != '') {
            $this->db->where("c.tipo_razon", $vRazon);
        }

        return $this->db->count_all_results();
    }

    public function totalVenta($vFecha = '', $vUsuario= ''){
            $sql = "SELECT FORMAT(SUM(montocob) ,2) AS acumulado
                        FROM wp_cobro WHERE  DATE(fecmod) = '$vFecha' AND usumod = '$vUsuario' AND estado='C' GROUP BY usumod";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();        
    }
    public function listaPagosxUsuario($vFecha = '', $vUsuario= '') {
        $this->db->select("c.numrecibo, f.famdes,a.nomcomp,cc.condes,m.mesdes,c.fecmod,c.montocob,s.nemodes,c.flgenvio");
        $this->db->from(LIBRERIA . ".wp_cobro as c");
        $this->db->join(LIBRERIA . '.wp_concobro as cc', ' cc.concob=c.concob');
        $this->db->join(LIBRERIA . '.wp_meses as m', ' m.mescod=c.mescob');
        $this->db->join(LIBRERIA2 . '.alumno as a', ' a.alucod=c.alucod');
        $this->db->join(LIBRERIA2 . '.familia as f', '  a.famcod=f.famcod ');
        $this->db->join(LIBRERIA2 . '.salon_al as sa', ' sa.alucod=a.alucod AND sa.ano=' . $this->ano);
        $this->db->join(LIBRERIA2 . '.salon as s', ' s.nemo=sa.nemo AND s.ano=' . $this->ano);
        $this->db->where("c.anocob", $this->ano);
        //$this->db->where("c.tipo_comp", "02");
        //  $this->db->where("c.flgenvio", "0");
        $this->db->where("c.estado", "C");
        $this->db->group_by("c.numrecibo,c.alucod,c.concob,c.mescob");

        if ($vFecha != '') {
            $this->db->where("DATE(c.fecmod)", $vFecha);
        }
        if ($vUsuario != '') {
            $this->db->where("c.usumod", $vUsuario);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function updateEnvios($vFecha = '', $vRazon = '') {
        try {
            $sql = "UPDATE  wp_cobro SET flgenvio=1 WHERE /*anocob='" . $this->ano . "' AND*/ estado='C'  AND tipo_comp='02' "
                    . " AND tipo_razon='" . $vRazon . "' AND flgenvio=0 AND DATE(fecmod)='" . $vFecha . "' --  AND anocob='2019' ";
            //echo  $sql; exit;
            $query = $this->db->query($sql); 
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
            return $e->getMessage();
        }
    }

    public function getDatosSunat($vFecha = '', $vtipoComp = '') {
        try {
            $sql = " SELECT 
                        c.numrecibo,
                        (
                        CASE 
                               WHEN f.paddni !='' THEN 
                                       f.paddni	
                               ELSE 
                                        CASE 
                                               WHEN f.maddni !='' THEN 
                                                       f.maddni
                                               ELSE 
                                                       '00000000' 
                                        END	
                        END
                        )  AS dni, SUM(c.montocob) AS monto
                       FROM  fercmias_sistemasdev.wp_cobro AS c
                        INNER JOIN fercmias_academico.alumno AS a  ON a.alucod=c.alucod
                         INNER JOIN fercmias_academico.familia AS f  ON  a.famcod=f.famcod 
                       WHERE /*c.anocob='" . $this->ano . "'  AND*/ c.tipo_comp='02' 
                       AND c.tipo_razon='" . $vtipoComp . "'  AND c.estado='C'               /* AND c.flgenvio=0  */       AND DATE(c.fecmod)='" . $vFecha . "'   
                        GROUP BY c.numrecibo ";         
            
           $sql .= " UNION ALL ";
             
             $sql .= "SELECT c.numrecibo, '00000000', c.monto 
                        FROM fercmias_sistemasdev.wp_cobro_adicional AS c 
                         WHERE DATE(c.fecreg)='" . $vFecha . "' AND c.tipo_razon='" . $vtipoComp . "'
                        AND c.tipo_comp='02' 
                          GROUP BY c.numrecibo ";      
             $sql .= "  ORDER BY numrecibo ";
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

    public function getEmpresa() {
        try {
            $this->db->select("idrazon,ruc,razon_social");
            $this->db->from($this->table);
            $this->db->where("estado", 1);
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

    public function getDatosxEmpresa($idRazon = '') {
        try {
            $this->db->select("*");
            $this->db->from($this->table);
            $this->db->where("estado", 1);
            $this->db->where("idrazon", $idRazon);
            $query = $this->db->get();
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->row();
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
