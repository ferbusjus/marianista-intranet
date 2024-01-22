<?php

class egresos_model extends CI_Model
{

    var $table = 'persons';
    var $column_order = array('id', 'fecha', 'descripcion', 'grupo', 'comprobante', 'recibo');
    //var $column_search = array('grupo', 'descripcion', 'numcomp', 'comprobante');
    var $column_search = array('e.descripcion');
    var $order = array('fecreg' => 'desc');

    function __construct ()
    {
        parent::__construct ();
        $this->table = LIBRERIA . '.wp_egresos';
    }

    public function listaEgresosxFecha ($vfecha = '0000-00-00')
    {
        $this->db->select ('e.id,g.dscgrupo as grupo,e.descripcion,e.numcomp,e.fecha_gasto,e.fecha_pago,e.monto,c.descripcion as comprobante,e.flgactivo');
        $this->db->from ($this->table . " as e");
        $this->db->join ("wp_grupo as g", "g.idgrupo=e.idgrupo", "INNER");
        $this->db->join ("wp_comprobante as c", "c.idcomprobante=e.idcomprobante", "INNER");
        $this->db->where ("e.flgactivo", 1);
        $this->db->where ("e.fecha", $vfecha);
        $query = $this->db->get ();
        /* echo $this->db->last_query ();
          exit; */
        return $query->result ();
    }

    public function filtrarProveedor($txtFiltro='', $tipo=1){
        if($tipo == 1) 
            $campo = "razon_social";
        if($tipo == 2) 
            $campo = "ruc";        
        $query = $this->db->query("SELECT * FROM wp_gastos_proveedor WHERE TRIM($campo) LIKE '%" . trim($txtFiltro) . "%' LIMIT 0 ,20");
        //echo $this->db->last_query(); exit;
        $query = $query->result();
        return $query;        
    }
    
    public function consultarRuc($ruc=''){
        $query = $this->db->query("SELECT count(*) as total FROM wp_gastos_proveedor WHERE trim(ruc)='$ruc'");
        $row = $query->row(); 
        return $row->total;      
    }
    
    public function registrarRuc($arrData = array()){
            $query = $this->db->insert ('wp_gastos_proveedor', $arrData);
            if (!$query)
                throw new Exception ($this->db->_error_message ());        
            return true;
    }
    
    public function _get_datatables_query ($flgview = 1)
    {
        $this->db->select ("e.id
                                        ,r.razon_social AS razon
                                        ,rp.responsable
                                        ,c.descripcion AS comprobante
                                        ,e.id_tipo_gasto AS tipo_gasto
                                        ,i.descripcion AS inputacion
                                        ,e.fecha_gasto
										,e.fecha_pago
                                        ,e.descripcion
                                        ,e.num_comprobante
                                        ,e.ruc_proveedor AS proveedor
                                        ,e.monto
                                        ,e.archivo_1
                                        ,e.archivo_2
                                        ,e.fecreg,e.usureg,e.usumod,e.fecmod,e.flgactivo");
        $this->db->from ($this->table . " as e");
        $this->db->join ("wp_razon_social as r", "r.id_razon=e.id_razon", "INNER");
        $this->db->join ("wp_gastos_responsables as rp", "rp.id_responsable=e.id_responsable", "INNER");
        $this->db->join ("wp_comprobante as c", "c.idcomprobante = e.id_comprobante", "INNER");
        $this->db->join ("wp_gastos_inputaciones as i", "i.id_inputacion = e.id_inputacion", "INNER");
        
        if($_POST['flagBorrado']=="1"){
            $this->db->where_in ("e.flgactivo", array(0, 1));
        }   else {
            $this->db->where ("e.flgactivo", 1);
        }

        $i = 0;
        if ($flgview == 1) {
            if ($_POST['idrazon'] != '' || $_POST['idrazon'] != 0) {
                $idrazon = $_POST['idrazon'];
                $this->db->where ("r.id_razon", $idrazon);
            }
            if ($_POST['idcomp'] != '' || $_POST['idcomp'] != 0) {
                $idcomp = $_POST['idcomp'];
                $this->db->where ("c.idcomprobante", $idcomp);
            }
            if ($_POST['idresp'] != '' || $_POST['idresp'] != 0) {
                $idresp = $_POST['idresp'];
                $this->db->where ("rp.id_responsable", $idresp);
            }            
            
            foreach ($this->column_search as $item) { // loop column 
                if ($_POST['search']['value']) { // if datatable send POST for search
                    //if ($i === 0) { // first loop
                    //$this->db->group_start (); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like ($item, $_POST['search']['value']);
                    //} else {  
                    // $this->db->or_like ($item, $_POST['search']['value']);
                    // }
                    // if (count ($this->column_search) - 1 == $i) //last loop
                    //  $this->db->group_end (); //close bracket
                }
                $i++;
            }

            if (isset ($_POST['order'])) { // here order processing
                $this->db->order_by ($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset ($this->order)) {
                $order = $this->order;
                $this->db->order_by (key ($order), $order[key ($order)]);
            }             
        } else {
            $query = $this->db->get ();             
            return $query->result ();
        }
    }

    function get_datatables ()
    {
        $this->_get_datatables_query ();        
        if ($_POST['length'] != -1)
            $this->db->limit ($_POST['length'], $_POST['start']);
        $query = $this->db->get();       
        //echo $this->db->last_query(); exit;
        return $query->result();
    }

    function count_filtered ()
    {
        $this->_get_datatables_query ();
        $query = $this->db->get ();
        return $query->num_rows ();
    }

    public function count_all ()
    {
        $this->db->select ("e.id
                                        ,r.razon_social AS razon
                                        ,rp.responsable
                                        ,c.descripcion AS comprobante
                                        ,e.id_tipo_gasto AS tipo_gasto
                                        ,i.descripcion AS inputacion
                                        ,e.fecha_gasto,
										,e.fecha_pago
                                        ,e.descripcion
                                        ,e.num_comprobante
                                        ,e.ruc_proveedor AS proveedor
                                        ,e.monto
                                        ,e.archivo_1
                                        ,e.archivo_2
                                        ,e.fecreg");
        $this->db->from ($this->table . " as e");
        $this->db->join ("wp_razon_social as r", "r.id_razon=e.id_razon", "INNER");
        $this->db->join ("wp_gastos_responsables as rp", "rp.id_responsable=e.id_responsable", "INNER");
        $this->db->join ("wp_comprobante as c", "c.idcomprobante = e.id_comprobante", "INNER");
        $this->db->join ("wp_gastos_inputaciones as i", "i.id_inputacion = e.id_inputacion", "INNER");
        $this->db->where ("e.flgactivo", 1);
        $this->db->order_by ("e.fecreg","desc");    
        return $this->db->count_all_results ();
    }

    public function grabaNuevoEgreso ($arrData = array(), $flgSave=0, $vId="")
    {
        try {
            if($flgSave==0) {
                $query = $this->db->insert ('wp_egresos', $arrData);
            } else {
                $this->db->where('id', $vId);
                $query = $this->db->update('wp_egresos', $arrData);
            }
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $this->db->insert_id();
        } catch (Exception $e) {
            $arrayError = array(
                'Problema' => $e->getMessage (),
                'Clase' => __CLASS__,
                'Metodo' => __FUNCTION__,
                'Archivo' => __FILE__,
                'Linea' => __LINE__,
                'Query' => print_r ($this->db->last_query (), TRUE)
            );
            notificaError ($arrayError);
            return $e->getMessage ();
        }
    }

    function eliminaEgreso ($vId = 0)
    {
        /*  $this->db->where ('id', $vId);
          $query = $this->db->delete ('wp_egresos'); */
        $data = array('flgactivo' => 0);
        $this->db->where ('id', $vId);
        $query = $this->db->update ('wp_egresos', $data);
        if (!$query)
            return false;
        else
            return true;
    }
    
        function actualizarArchivos ($vId = 0,$data = array())
    {
        $this->db->where ('id', $vId);
        $query = $this->db->update ('wp_egresos', $data);
        if (!$query)
            return false;
        else
            return true;
    }
    
    public function getDatosGasto($vId=0){
            $sql = "select * from wp_egresos where id=".$vId;
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();
    }
    
    public function getGrupos ()
    {
        try {
            $sql = "select * from wp_grupo where flgactivo=1";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();
        } catch (Exception $e) {
            $arrayError = array(
                'Problema' => $e->getMessage (),
                'Clase' => __CLASS__,
                'Metodo' => __FUNCTION__,
                'Archivo' => __FILE__,
                'Linea' => __LINE__,
                'Query' => print_r ($this->db->last_query (), TRUE)
            );
            notificaError ($arrayError);
            return $e->getMessage ();
        }
    }

    public function getRazonSocial()
    {
            $sql = "select * from wp_razon_social";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();        
    }
    
    public function getResponsables()
    {
            $sql = "select * from wp_gastos_responsables where flg_estado=1";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();           
    }

   public function getCajas()
    {
            $sql = "select * from wp_gastos_caja where flg_activo=1";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();           
    }
    
    public function getInputaciones()
    {
            $sql = "select * from wp_gastos_inputaciones where flg_estado=1";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();           
    }
    public function getComprobantes ()
    {
        try {
            $sql = "select * from wp_comprobante where flgactivo=1";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();
        } catch (Exception $e) {
            $arrayError = array(
                'Problema' => $e->getMessage (),
                'Clase' => __CLASS__,
                'Metodo' => __FUNCTION__,
                'Archivo' => __FILE__,
                'Linea' => __LINE__,
                'Query' => print_r ($this->db->last_query (), TRUE)
            );
            notificaError ($arrayError);
            return $e->getMessage ();
        }
    }

    public function listaEgresos ()
    {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_S_EGRESOS_ALL() ";
            $query = $this->db->query ($sql);
            if (!$query)
                throw new Exception ($this->db->_error_message ());
            return $query->result ();
        } catch (Exception $e) {
            $arrayError = array(
                'Problema' => $e->getMessage (),
                'Clase' => __CLASS__,
                'Metodo' => __FUNCTION__,
                'Archivo' => __FILE__,
                'Linea' => __LINE__,
                'Query' => print_r ($this->db->last_query (), TRUE)
            );
            notificaError ($arrayError);
            return $e->getMessage ();
        }
    }

}
