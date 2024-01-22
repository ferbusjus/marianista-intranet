<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class familia_model extends CI_Model {

    public $table = 'familia';
    public $id = 'famcod';
    public $column_order = array('f.famcod', 'f.famdes');
    public $column_search = array('f.famdes');
    public $order = array('f.famcod' => 'desc');

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA2 . '.familia';
    }

    public function _get_datatables_query() {
        $this->db->select("f.famcod,f.famdes,(SELECT COUNT(*) FROM " . LIBRERIA2 . ".alumno WHERE famcod=f.famcod AND  estado='V') as total, f.padmail,f.madmail");
        $this->db->from($this->tabla . " as f");
        $this->db->where("f.flag", "1");
        $this->db->order_by("f.famdes", "asc");

        if ($_POST['cbhijo'] != '') {
            $nhijo = $_POST['cbhijo'];
            if ($nhijo == 0)
                $this->db->having("total", $nhijo);
            elseif ($nhijo == 1)
                $this->db->having("total", $nhijo);
            elseif ($nhijo == 2)
                $this->db->having("total", $nhijo);
            elseif ($nhijo == 3)
                $this->db->having("total >=", $nhijo);
        }

        if ($_POST['txtsearch'] != '') {
            $txtcadena = $_POST['txtsearch'];
            $this->db->like("f.famdes", $txtcadena);
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
        //  echo $this->db->last_query(); exit;
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->select("f.famcod,f.famdes,(SELECT COUNT(*) FROM " . LIBRERIA2 . ".alumno WHERE famcod=f.famcod) as total, f.padmail,f.madmail");
        $this->db->from($this->tabla . " as f");
        $this->db->where("f.flag", "1");
        return $this->db->count_all_results();
    }

    function updateToken($vFamcod = '', $vClavegen = '') {

        $arrData = array(
            'PASSWORD' => $vClavegen,
            'fecha_modifica' => date("Y-m-d H:i:s"),
            'flg_cambio' => 0,
            'ESTATUS' => 1
        );

        $this->db->where('usucod', $vFamcod);
        $query = $this->db->update(LIBRERIA . ".usuarios", $arrData);
        return $query;
    }

    function insertToken($vFamcod = '', $vFamdesc = '', $vClavegen = '') {

        $arrData = array(
            'NOMBRE' => 'FAMILIA',
            'APELLIDOS' => $vFamdesc,
            'EMAIL' => '',
            'FECHA_REGISTRO' => date("Y-m-d H:i:s"),
            'ESTATUS' => 1,
            'TIPO' => 2,
            'PASSWORD' => $vClavegen,
            'id_sistema' => 4,
            'id_perfil' => 4,
            'usucod' => $vFamcod,
            'famcod' => $vFamcod,
            'fecha_creacion' => date("Y-m-d H:i:s")
        );

        $query = $this->db->insert(LIBRERIA . ".usuarios", $arrData);
        return $query;
    }

    function getFamilia($vIdFamcod = '') {

        if ($vIdFamcod != '') {
            $this->db->select("*");
        } else {
            $this->db->select("FAMCOD,FAMDES");
        }
        $this->db->from(LIBRERIA2 . ".familia");
        if ($vIdFamcod != '') {
            $this->db->where("FAMCOD", $vIdFamcod);
        }
        $this->db->where("ESTADO", 'V');
        $this->db->order_by("FAMDES");
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function insertSimple($arrDatos = array()) {

        try {

            $vfamdes = $arrDatos['FAMDES'];

            $vpadApepat = $arrDatos['PADAPEPAT'];

            $vpadApemat = $arrDatos['PADAPEMAT'];

            $vpadNombre = $arrDatos['PADNOMBRE'];
            
            $vpadDni = $arrDatos['PADDNI'];

            $vpadDir =  '';

            $vpadMail = $arrDatos['PADMAIL'];

            $vpadTel = '';

            $vmadApepat = $arrDatos['MADAPEPAT'];

            $vmadApemat = $arrDatos['MADAPEMAT'];

            $vmadNombre = $arrDatos['MADNOMBRE'];
            
            $vmadDni = $arrDatos['MADDNI'];

            $vmadDir = '';

            $vmadMail = $arrDatos['MADMAIL'];

            $vmadTel = '';
            
            if($arrDatos['FLGAPO']=='1'){
                
                $vpadApepat = $arrDatos['APOAPEPAT'];

                $vpadApemat = $arrDatos['APOAPEMAT'];

                $vpadNombre = $arrDatos['APONOMBRE'];

                $vpadDni = $arrDatos['APODNI'];

                $vpadMail = $arrDatos['APOMAIL'];
            
            }
            
            $vflgpadapo = $arrDatos['FLGPADAPO'];
            
            $vflgmadapo = $arrDatos['FLGPADAPO'];
            
            $vflgapo = $arrDatos['FLGAPO'];

            $query = $this->db->query("CALL " . LIBRERIA2 . ".SP_I_FAMILIA("
                    . "'$vfamdes',"
                    . "'$vpadApepat',"
                    . "'$vpadApemat',"
                    . "'$vpadNombre',"
                    . "'$vpadDni',"
                    . "'$vpadMail',"
                    . "'$vpadTel',"
                    . "'$vmadApepat',"
                    . "'$vmadApemat',"
                    . "'$vmadNombre',"
                    . "'$vmadDni',"
                    . "'$vmadMail',"
                    . "'$vmadTel','$vflgpadapo','$vflgmadapo','$vflgapo',@codigo)");

            if ($query) {

                $query = $this->db->query("select @codigo as codigo ");

                $query = $query->row();

                return $query->codigo;
            } else {

                throw new Exception($this->db->_error_message());
            }
        } catch (Exception $e) {
           return $this->db->last_query();
            //return $e->getMessage();
        }
    }

    function insert($arrDatos = array()) {

        try {

            $vfamdes = $arrDatos['FAMDES'];

            $vpadApepat = $arrDatos['PADAPEPAT'];

            $vpadApemat = $arrDatos['PADAPEMAT'];

            $vpadNombre = $arrDatos['PADNOMBRE'];

            $vpadDir = $arrDatos['PADDIR'];

            $vpadMail = $arrDatos['PADMAIL'];

            $vpadTel = $arrDatos['PADTEL'];

            $vmadApepat = $arrDatos['MADAPEPAT'];

            $vmadApemat = $arrDatos['MADAPEMAT'];

            $vmadNombre = $arrDatos['MADNOMBRE'];

            $vmadDir = $arrDatos['MADDIR'];

            $vmadMail = $arrDatos['MADMAIL'];

            $vmadTel = $arrDatos['MADTEL'];



            $query = $this->db->query("CALL " . LIBRERIA2 . ".SP_I_FAMILIA("
                    . "'$vfamdes',"
                    . "'$vpadApepat',"
                    . "'$vpadApemat',"
                    . "'$vpadNombre',"
                    . "'$vpadDir',"
                    . "'$vpadMail',"
                    . "'$vpadTel',"
                    . "'$vmadApepat',"
                    . "'$vmadApemat',"
                    . "'$vmadNombre',"
                    . "'$vmadDir',"
                    . "'$vmadMail',"
                    . "'$vmadTel',@codigo)");

            //$stored = $this->db->query ("CALL " . LIBRERIA2 . ".SP_GENERA_CODIGO('FAMILIA','FM')");
            //$row = $stored->row ();
            //$vcod = $row->codgen;
            //$arrDatos['FAMCOD'] = $vcod;
            //$stored->free_result();
            //$query = $this->db->insert ($this->tabla, $arrDatos); 

            if ($query) {

                return true;
            } else {

                throw new Exception($this->db->_error_message());
            }
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    function updateEstadoFamilia($vFamcod = '', $arrDatos = array()) {

        try {

            $this->db->where('FAMCOD', $vFamcod);

            $query = $this->db->update(LIBRERIA2 . ".familia", $arrDatos);

            if ($query) {

                return true;
            } else {

                throw new Exception($this->db->_error_message());
            }
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    function update($arrDatos = array(), $vFamcod = '') {

        try {

            $this->db->where('FAMCOD', $vFamcod);

            $query = $this->db->update(LIBRERIA2 . ".familia", $arrDatos);

            if ($query) {

                return true;
            } else {

                throw new Exception($this->db->_error_message());
            }
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    function getDatosHijos($vIdFamcod = '') {

        $this->db->select("ALUCOD,DNI,NOMCOMP,ESTADO,INSTRUCOD,GRADOCOD,SECCIONCOD");

        $this->db->from(LIBRERIA2 . ".alumno");

        $this->db->where("FAMCOD", $vIdFamcod);
        $this->db->where("ESTADO", "V");
        $query = $this->db->get();

        $query = $query->result();

        return $query;
    }

    function existeUsuario($vFamcod = '') {

        $query = $this->db->query("SELECT  COUNT(*)  AS TOTAL FROM " . LIBRERIA . ".usuarios  WHERE usucod='$vFamcod' ");

        $row = $query->row();

        $total = $row->TOTAL;

        if ($total == 0)
            return false;
        else
            return true;
    }
 
        function existeFamilia($vFamdes = '') {
        $query = $this->db->query("SELECT  famcod as FAMCOD, COUNT(*)  AS TOTAL FROM " . LIBRERIA2 . ".familia  WHERE trim(famdes)='$vFamdes' ");
        $row = $query->row();
        /*$total = $row->TOTAL;
        if ($total == 0)
            return FALSE;
        else
            return TRUE;*/
        return  $row;
    }
    
    function ActualizaMarcaApoderado($vfamcod = '', $vdni = '', $valor = 0) {
        if ($valor == 1) {// papa
            $sql = " UPDATE " . LIBRERIA2 . ".familia set FLGMADAPO=0,FLGPADAPO=1, PADDNI='$vdni' WHERE FAMCOD='$vfamcod'";
            $sqlSelect = "SELECT  famcod,paddni,padapepat,padapemat,padnombre,maddni,madapepat,madapemat,madnombre "
                    . "FROM " . LIBRERIA2 . ".familia WHERE PADDNI='$vdni' AND FAMCOD='$vfamcod'";                    
        }
        if ($valor == 2) { //mama
            $sql = " UPDATE " . LIBRERIA2 . ".familia set FLGMADAPO=1,FLGPADAPO=0, MADDNI='$vdni' WHERE FAMCOD='$vfamcod'";
            $sqlSelect = "SELECT  famcod,paddni,padapepat,padapemat,padnombre,maddni,madapepat,madapemat,madnombre "
                    . "FROM " . LIBRERIA2 . ".familia WHERE MADDNI='$vdni' AND FAMCOD='$vfamcod'";                    
        }

        $this->db->query($sql);
        $query = $this->db->query($sqlSelect);
        return $query->result();
    }

    function ActualizaDatosApoderado($vfamcod = '', $vtipo = 0, $vdni = '', $vpaterno = '', $vmaterno = '', $vnombre = '') {
        if ($vtipo == 1) {// papa
            $sql = " UPDATE " . LIBRERIA2 . ".familia set PADDNI='$vdni',PADAPEPAT='$vpaterno', PADAPEMAT='$vmaterno', PADNOMBRE='$vnombre'  WHERE FAMCOD='$vfamcod'";
        }
        if ($vtipo == 2) { //mama
            $sql = " UPDATE " . LIBRERIA2 . ".familia set MADDNI='$vdni',MADAPEPAT='$vpaterno', MADAPEMAT='$vmaterno', MADNOMBRE='$vnombre'   WHERE FAMCOD='$vfamcod'";
        }
        $query = $this->db->query($sql);
        return $query;
    }

    function obtienePadresxAlumno($famcod = '') {
        $sql = "SELECT 
                    PADDNI AS DNI,PADAPEPAT AS PATERNO,PADAPEMAT AS MATERNO,PADNOMBRE  AS NOMBRE,FLGPADAPO AS ESAPO
                    FROM " . LIBRERIA2 . ".familia WHERE famcod='$famcod'
                    UNION ALL
                    SELECT 
                    MADDNI AS DNI,MADAPEPAT AS PATERNO,MADAPEMAT AS MATERNO,MADNOMBRE AS NOMBRE,FLGMADAPO AS ESAPO
                    FROM " . LIBRERIA2 . ".familia WHERE famcod='$famcod'";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }

}
