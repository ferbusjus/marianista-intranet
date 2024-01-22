<?php

/**
 * @package       modules/matricula_model/model
 * @name            matricula_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2017/12/03
 */
class matricula_model extends CI_Model {

    public $tabla = '';
    public $ano = '';
    var $column_order = array('a.dni', 'a.nomcomp', 'a.alucod', 'm.fecmat', 'm.estado');
    var $column_search = array('a.nomcomp');
    var $order = array('m.fecmat' => 'desc');

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA . '.sga_matricula';
        $this->ano = $this->nativesession->get('S_ANO_VIG');
    }

    function verificaConducta($dni = 0) {
        $this->db->select("*");
        $this->db->from(LIBRERIA2 . ".carga_comportamiento");
        $this->db->where("dni", $dni);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function getDataAlumnoNuevo($idAlumno = 0) {
        $this->db->select("A.ALUCOD,A.DNI,A.APEPAT,A.APEMAT,A.NOMBRES,A.NOMCOMP,A.MATRICULA,A.ESTADO,A.IDALUMNO,A.NUMLIBRO");
        $this->db->from(LIBRERIA2 . ".alumno AS A");
        $this->db->where("A.ALUCOD", $idAlumno);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function getDataAlumno($idAlumno = 0, $vflg = 0) {

        if ($vflg == 1)
            $vAno = $this->ano; //$ANO_VIG;
        else
            $vAno = ($this->ano - 1); //$ANO_VIG - 1;
        $this->db->select(""
                . "A.*,"
                . "S.NEMODES,S.NEMO,S.AULACOD,GN.INSTRUDES,GN.GRADODES,X.AULADES,"
                . "GN.INSTRUCOD, GN.GRADOCOD,GN.INSTRUDESP,GN.GRADODESP,GN.INSTRUCODP,GN.GRADOCODP"
        );
        $this->db->from(LIBRERIA2 . ".alumno AS A");
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD -- AND SA.ANO=' . $vAno);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO -- AND S.ANO=' . $vAno);
        $this->db->join(LIBRERIA2 . '.aula AS X', 'X.AULACOD=S.AULACOD -- AND X.ANO=' . $vAno);
        $this->db->join(LIBRERIA2 . '.nivelgrado_mov AS GN', 'GN.INSTRUCOD=S.INSTRUCOD AND GN.GRADOCOD=S.GRADOCOD');
        $this->db->where("A.ALUCOD", $idAlumno);
        $this->db->where("S.NEMO", " (SELECT NEMO FROM " . LIBRERIA2 . ".salon_al WHERE alucod= A.ALUCOD ORDER BY nemo DESC LIMIT 1) ", FALSE);
        $query = $this->db->get();
        $query = $query->row();
        //echo $this->db->last_query(); exit;
        return $query;
    }

    function getDataAlumnoMatriculado($idAlumno = 0, $vflg = 0) {

        if ($vflg == 1)
            $vAno = $this->ano; //$ANO_VIG;
        else
            $vAno = ($this->ano - 1); //$ANO_VIG - 1;

        $this->db->select(""
                . "A.*,M.observacion,M.obsdocumentos,X.AULADES,"
                . "S.NEMODES,S.NEMO,S.AULACOD"
        );
        $this->db->from(LIBRERIA2 . ".alumno AS A");
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $vAno);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $vAno);
        $this->db->join(LIBRERIA2 . '.aula AS X', 'X.AULACOD=S.AULACOD AND X.ANO=' . $vAno);
        $this->db->join(LIBRERIA . '.sga_matricula AS M', 'M.aluant=A.ALUCOD AND M.periodo=' . $vAno);
        $this->db->where("A.ALUCOD", $idAlumno);
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        $query = $query->row();
        return $query;
    }

    public function verificaConfigMatricula() {
        $query = $this->db->query("SELECT COUNT(*) AS TOTAL FROM " . LIBRERIA . ".sga_config_matricula WHERE estado='V' AND DATE(NOW()) BETWEEN  fec_ini  AND  fec_fin");
        $row = $query->row();
        return $row->TOTAL;
    }

    public function FiltrarAlumno($vtipo = 0, $vfiltro = '', $vestados = '') {
        /* $this->db->select ("a.dni,a.alucod,a.nomcomp,s.instrucod,s.gradocod,s.seccioncod,au.aulades as aula, a.flg_matricula, a.matricula");
          $this->db->from (LIBRERIA2 . '.alumno as a');
          $this->db->join (LIBRERIA2 . '.salon_al as sa', 'sa.alucod=a.alucod');
          $this->db->join (LIBRERIA2 . '.salon as s', 's.nemo=sa.nemo');
          $this->db->join (LIBRERIA2 . '.aula as au', 'au.aulacod=s.aulacod');
          $this->db->where("a.flg_matricula", 0);
          if ($vtipo == 1) {
          $this->db->like ("a.dni", $vfiltro);
          }
          if ($vtipo == 2) {
          $this->db->like ("a.nomcomp", $vfiltro);
          }
          $this->db->order_by ("s.instrucod", "asc");
          $this->db->order_by ("s.gradocod", "asc");
          $this->db->order_by ("s.seccioncod", "asc");
          $this->db->order_by ("a.nomcomp", "asc");
          $query = $this->db->get (); */
        $vestados = $vestados.",*V*";
        $sql = "CALL " . LIBRERIA2 . ".SP_S_FILTRAR_ALUMNOS($vtipo,'$vfiltro','$vestados') ";
        $query = $this->db->query($sql);
        /* echo $this->db->last_query(); exit; */
        return $query->result();
    }

    public function _get_datatables_query() {
        $this->db->select("m.periodo,m.numlibro,a.alucod,a.dni,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod,au.aulades,m.estado,m.fecmat ,a.flg_matricula");
        $this->db->from(LIBRERIA . ".sga_matricula as m");
        // $this->db->join (LIBRERIA2 . '.alumno as a', 'a.dni=m.dni');
        $this->db->join(LIBRERIA2 . '.alumno as a', 'a.alucod=m.aluant');
        $this->db->join(LIBRERIA2 . '.salon as s', 's.nemo=m.nemo');
        $this->db->join(LIBRERIA2 . '.aula as au', 'au.aulacod=s.aulacod');
        // $this->db->order_by("s.instrucod", "asc");
        // $this->db->order_by("s.gradocod", "asc");
        // $this->db->order_by("s.seccioncod", "asc");
        // $this->db->order_by("a.nomcomp", "asc");

        $i = 0;

        if ($_POST['idfiltro'] != '') {
            $idfiltro = $_POST['idfiltro'];
            $cadena = trim($_POST['txtsearch']);
            if ($idfiltro == 1)  // DNI
                $this->db->or_like("a.dni", "$cadena");
            if ($idfiltro == 2)  // APELLIDOS
                $this->db->or_like("a.nomcomp", "$cadena");
        }

        if ($_POST['vanio'] != '') {
            $vanio = $_POST['vanio'];
            $this->db->where("m.periodo", $vanio);
        }

        if ($_POST['vaula'] && $_POST['vaula'] != '') {
            $vaula = $_POST['vaula'];
            $this->db->where("s.nemo", $vaula);
        }        
        //$this->db->where("m.estado", 'M');

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
        //echo "<pre>"; print_r($query->result ()); echo "</pre>"; exit;
        /* echo "Query:" . $this->db->last_query ();
          exit; */
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->select("m.periodo,m.numlibro,a.alucod,a.dni,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod,au.aulades,m.estado,m.fecmat ");
        $this->db->from(LIBRERIA . ".sga_matricula as m");
        // $this->db->join (LIBRERIA2 . '.alumno as a', 'a.dni=m.dni');
        $this->db->join(LIBRERIA2 . '.alumno as a', 'a.alucod=m.aluant');
        $this->db->join(LIBRERIA2 . '.salon as s', 's.nemo=m.nemo');
        $this->db->join(LIBRERIA2 . '.aula as au', 'au.aulacod=s.aulacod');
        $this->db->where("m.periodo", $this->ano);
        $this->db->order_by("s.instrucod", "asc");
        $this->db->order_by("s.gradocod", "asc");
        $this->db->order_by("s.seccioncod", "asc");
        $this->db->order_by("a.nomcomp", "asc");
        return $this->db->count_all_results();
    }

    public function getlistaFiltro($dataFiltro = array(), $vtipo = 1) {

        $whereInstrucod = (($dataFiltro["instrucod"] != 'Todos') ? (" AND s.instrucod='" . $dataFiltro["instrucod"] . "'") : "");
        $whereGradocod = (($dataFiltro["gradocod"] != 'Todos') ? (" AND s.gradocod='" . $dataFiltro["gradocod"] . "'") : "");
        $whereSeccioncod = (($dataFiltro["seccioncod"] != 'Todos') ? (" AND s.aulacod='" . $dataFiltro["seccioncod"] . "'") : "");
        if ($vtipo === 1) {
            $query = $this->db->query("SELECT a.alucod,a.dni,a.nomcomp,CONCAT(a.instrucod,a.gradocod,a.seccioncod) AS ngs, au.aulades,date(m.fecmat) as fechamat,s.instrucod,s.gradocod,m.observacion,a.sexo
                                                        FROM " . LIBRERIA2 . ".alumno AS a 
                                                        INNER JOIN " . LIBRERIA . ".sga_matricula AS m ON m.aluant=a.alucod
                                                        INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON sa.alucod=a.alucod AND sa.ano='$this->ano'
                                                        INNER JOIN " . LIBRERIA2 . ".salon AS s ON s.nemo=sa.nemo AND s.ano='$this->ano'
                                                        INNER JOIN " . LIBRERIA2 . ".aula AS au ON au.aulacod=s.aulacod AND au.ano='$this->ano'
                                                        WHERE a.flg_matricula=1 AND m.estado='M' 
                                                        " . $whereInstrucod . " " . $whereGradocod . " " . $whereSeccioncod .
                    " AND m.periodo='$this->ano' " .
                    "ORDER BY a.instrucod,a.gradocod,a.seccioncod");
        }
        if ($vtipo === 2) {
            $query = $this->db->query("SELECT s.nemo,s.nemodes,au.limite
                                                        ,(SELECT COUNT(*) FROM " . LIBRERIA2 . ".alumno WHERE instrucod=s.instrucod AND gradocod=s.gradocod AND seccioncod=s.seccioncod AND flg_matricula=1 ) AS matriculados
                                                        , CONCAT(ROUND((((SELECT COUNT(*) FROM " . LIBRERIA2 . ".alumno WHERE instrucod=s.instrucod AND gradocod=s.gradocod AND seccioncod=s.seccioncod AND flg_matricula=1 )  * 100) /au.limite )),'%') AS porcentaje
                                                       FROM  " . LIBRERIA2 . ".salon AS s
                                                       INNER JOIN " . LIBRERIA2 . ".aula AS au ON au.aulacod=s.aulacod AND au.ano='$this->ano'
                                                       WHERE s.ano='$this->ano'
                                                        " . $whereInstrucod . " " . $whereGradocod . " " . $whereSeccioncod .
                    "ORDER BY s.instrucod,s.gradocod,s.seccioncod");
        }
        if ($vtipo === 3) {
            $query = $this->db->query("SELECT a.alucod,a.dni,a.nomcomp,CONCAT(a.instrucod,a.gradocod,a.seccioncod) AS ngs, au.aulades,date(m.fecmat) as fechamat,s.instrucod,m.observacion,m.obsdocumentos
                                                        FROM " . LIBRERIA2 . ".alumno AS a 
                                                        INNER JOIN " . LIBRERIA . ".sga_matricula AS m ON m.aluant=a.alucod
                                                        INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON sa.alucod=a.alucod AND sa.ano='$this->ano'
                                                        INNER JOIN " . LIBRERIA2 . ".salon AS s ON s.nemo=sa.nemo AND s.ano='$this->ano'
                                                        INNER JOIN " . LIBRERIA2 . ".aula AS au ON au.aulacod=s.aulacod AND au.ano='$this->ano'
                                                        WHERE a.flg_matricula=1 AND m.estado='M' 
                                                        " . $whereInstrucod . " " . $whereGradocod . " " . $whereSeccioncod .
                    " AND m.periodo='$this->ano' " .
                    "ORDER BY a.instrucod,a.gradocod,a.seccioncod");
        }
        // echo "Query:" . $this->db->last_query (); exit;
        $query = $query->result();

        return $query;
    }

    public function getFiltroExtractor($data = array(), $dataFiltro = array()) {

        $whereInstrucod = (($dataFiltro["instrucod"] != 'Todos') ? (" AND a.instrucod='" . $dataFiltro["instrucod"] . "'") : "");
        $whereGradocod = (($dataFiltro["gradocod"] != 'Todos') ? (" AND a.gradocod='" . $dataFiltro["gradocod"] . "'") : "");
        $whereSeccioncod = (($dataFiltro["seccioncod"] != 'Todos') ? (" AND a.aulacod='" . $dataFiltro["seccioncod"] . "'") : "");
        
        if (sizeof($data) > 0) {
            $cadena = "";
            foreach ($data as $campos) {
                
                $cadena .= "a." . $campos . ",";
            }
            // ================ Pensiones ==============
            $cadena .= "m.FECMAT,";
            $cadena .= "m.USUREG,";
            $cadena .= "m.OBSERVACION,";
            $cadena .= "m.OBSDOCUMENTOS,";
            $cadena .= LIBRERIA2 . ".fu_get_documentos(a.alucod) as DOCUMENTOS,";
            $cadena .= "c.numrecibo as NUMRECIBO,";
            $cadena .= "c.voucher as VOUCHER,";
            $cadena .= "(select descripcion from  wp_medio_pago where idmedio=c.idmedio) as MEDIO_PAGO,";
            $cadena .= "c.montocob as MONTO,";
            $cadena .= "s.instrucod as NIVEL,";
            $cadena .= "s.gradocod as GRADO,";
            $cadena .= "au.aulades as AULA,";
            $cadena .= "LCASE(CONCAT(SUBSTRING_INDEX(a.nombres, ' ', 1),'.',SUBSTR(a.apepat,1,1),SUBSTR(a.apemat,1,1))) AS USUCAMPUS,";
            // ========================================
            if ($cadena != "") {
                $cadena = substr($cadena, 0, strlen($cadena) - 1);
            }
        }

        $query = $this->db->query("SELECT " . $cadena . " FROM " . LIBRERIA2 . ".alumno AS a "
                . " INNER JOIN " . LIBRERIA2 . ".salon_al as sa on sa.alucod=a.alucod and sa.ano=".$this->ano
                . " INNER JOIN " . LIBRERIA2 . ".salon as s on s.nemo=sa.nemo and s.ano=".$this->ano
                . " INNER JOIN " . LIBRERIA2 . ".aula as au on au.aulacod=s.aulacod and au.ano=".$this->ano
                . " INNER JOIN " . LIBRERIA . ".sga_matricula as m on m.aluant=a.alucod "
                . " INNER JOIN " . LIBRERIA . ".wp_cobro as c on c.alucod=a.alucod and c.anocob='$this->ano' and c.concob='02' and c.mescob='03'"
                . " WHERE m.estado='M' AND m.periodo='$this->ano'  " . $whereInstrucod . " " . $whereGradocod . " " . $whereSeccioncod . "  "
                . " ORDER BY m.FECMAT desc");
        //echo  $this->db->last_query();
        //EXIT;
        $query = $query->result_array();
        return $query;
    }

    public function getMatriculasAll() {
        $this->db->select("m.periodo,m.numlibro,a.alucod,a.dni,a.nomcomp,a.instrucod,a.gradocod,a.seccioncod,au.aulades,m.estado,m.fecmat ");
        $this->db->from(LIBRERIA . ".sga_matricula as m");
        // $this->db->join (LIBRERIA2 . '.alumno as a', 'a.dni=m.dni');
        $this->db->join(LIBRERIA2 . '.alumno as a', 'a.alucod=m.aluant');
        $this->db->join(LIBRERIA2 . '.salon as s', 's.nemo=m.nemo');
        $this->db->join(LIBRERIA2 . '.aula as au', 'au.aulacod=s.aulacod');
        $this->db->order_by("s.instrucod", "asc");
        $this->db->order_by("s.gradocod", "asc");
        $this->db->order_by("s.seccioncod", "asc");
        $this->db->order_by("a.nomcomp", "asc");
        $query = $this->db->get();

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    /* function getDataAlumno ($idAlumno = 0)
      {
      $this->db->select (""
      . "A.ALUCOD,A.DNI,A.APEPAT,A.APEMAT,A.NOMBRES,A.NOMCOMP,A.SEXO,A.DISCOD,"
      . "A.TELEFONO,A.DIRECCION,A.INSTRUCOD,A.GRADOCOD,A.SECCIONCOD,A.FAMCOD,F.FAMDES,"
      . "A.MATRICULA,SA.ESTADO,S.NEMODES,S.NEMO,A.DIRECCION,A.FECNAC,A.TELEFONO,A.EMAIL,"
      . "A.PADNOM,A.PADPATER,A.PADMATER,A.PADCELU,A.PADEMAIL,A.MADNOM,A.MADPATER,"
      . "A.MADMATER,A.MADCELU,A.MADEMAIL,A.FLG_BAUTIZO,A.FLG_COMUNI,A.FLG_CONFIR,"
      . "A.DNIPATER,A.DNIMATER,A.IDALUMNO"
      );
      $this->db->from (LIBRERIA2 . ".alumno AS A");
      $this->db->join (LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD');
      $this->db->join (LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO');
      $this->db->join (LIBRERIA2 . '.familia AS F', 'F.FAMCOD=A.FAMCOD', 'left');
      $this->db->where ("A.ALUCOD", $idAlumno);
      $query = $this->db->get ();
      $query = $query->row ();
      return $query;
      } */

    function saveUpdate($arrAlumno = array()) {
        try {
            $arrdata = array(
                'DNI' => $arrAlumno['dni'],
                'APEPAT' => $arrAlumno['apepat'],
                'APEMAT' => $arrAlumno['apemat'],
                'NOMBRES' => $arrAlumno['nombres'],
                'NOMCOMP' => $arrAlumno['nomcomp'],
                'DIRECCION' => $arrAlumno['direccion'],
                'ESTADO' => $arrAlumno['estado'],
                'FAMCOD' => $arrAlumno['famcod']
            );
            $arrwhere = $arrAlumno['alucod'];
            // 1: Actualizando datos de la tabla Alumnos
            $this->db->where('ALUCOD', $arrwhere);
            $query = $this->db->update(LIBRERIA2 . ".alumno", $arrdata);
            if ($query) {
                // 2: Actualizando datos de la tabla Salon_al
                $this->db->where('ALUCOD', $arrwhere);
                $query = $this->db->update(LIBRERIA2 . ".salon_al", array('ESTADO' => $arrdata['ESTADO']));
                if (!$query)
                    throw new Exception($this->db->_error_message());
                else
                    return TRUE;
            } else {
                throw new Exception($this->db->_error_message());
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function getListaGrado($idNivel = '') {
        $query = $this->db->query("SELECT * FROM " . LIBRERIA2 . ".gradonivel WHERE INSTRUCOD='$idNivel'");
        $query = $query->result();
        return $query;
    }

    function saveMatricula($arrMatricula = array()) {
        try {
            /* $vinstru = $arrMatricula['instrucod'];
              $vgrado = $arrMatricula['gradocod'];
              $vseccion = $arrMatricula['seccioncod'];
              unset ($arrMatricula['instrucod']);
              unset ($arrMatricula['gradocod']);
              unset ($arrMatricula['seccioncod']); */
            $query = $this->db->insert(LIBRERIA . ".sga_matricula", $arrMatricula);
            //  echo "QUERY :".$this->db->last_query ();

            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                //$this->db->where('DNI', $arrMatricula['dni']);
                //$this->db->update(LIBRERIA2 . ".alumno", array('FLG_MATRICULA' => 1, 'ESTADO' => 'V'));
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function updateAlumno($arrdata = array(), $key = '0') {
        try {
            $this->db->where('alucod', $key);
            //$this->db->where('ano', $this->ano);
            $query = $this->db->update(LIBRERIA2 . ".alumno", $arrdata);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function updateSalonAl($arrdata = array(), $key = '0') {
        try {
            $this->db->where('alucod', $key);
            $this->db->where('ano', $this->ano);
            $query = $this->db->update(LIBRERIA2 . ".salon_al", $arrdata);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
        function updatePagoMatricula($arrdata = array(), $alucod='') {
        try {
            $this->db->where('alucod', $alucod);
            $this->db->where('anocob', $this->ano);
            $this->db->where('concob', '02');
            $this->db->where('mescob', '03');
            $query = $this->db->update(LIBRERIA . ".wp_cobro", $arrdata);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    

    function eliminaMatricula($arrdata = array(), $key = '0') {
        try {
            $this->db->where('aluant', $key);
            $this->db->where('periodo', $this->ano);
            $query = $this->db->update(LIBRERIA . ".sga_matricula", $arrdata);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function updateMatricula($arrdata = array(), $arrWhere = array()) {
        try {
            $this->db->where('dni', $arrWhere);
            $this->db->where('periodo', $this->ano);
            $query = $this->db->update(LIBRERIA . ".sga_matricula", $arrdata);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function generarPagosAlumno($valucod = '0', $vnivel = '0') {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_GENERA_PAGOS_X_ALUMNO('$valucod','$vnivel') ";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function genCodigo() {
        $ano_vig = $this->ano;
        $query = $this->db->query("SELECT (MAX(codigo) + 1) AS CODGEN "
                . "FROM " . LIBRERIA2 . ".configuracion "
                . "WHERE tabla ='MATRICULA' AND ano_vig='2024' AND flg_activo=1"); //$ano_vig
        $row = $query->row();
        return $row->CODGEN;
    }

    function updateCodigo($vCodigo = 0) {
        $ano_vig = $this->ano;
        $this->db->query("UPDATE " . LIBRERIA2 . ".configuracion  SET codigo='$vCodigo', numero='$vCodigo' "
                . "WHERE tabla ='MATRICULA' AND ano_vig='2024' AND flg_activo=1"); //$ano_vig
    }

}
