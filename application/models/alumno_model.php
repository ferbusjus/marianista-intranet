<?php

/**
 * @package       modules/alumno_model/model
 * @name            alumno_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/14
 */
class alumno_model extends CI_Model {

    public $S_ANO = '';
    public $tabla = '';
    var $column_order = array('a.alucod', 'a.nomcomp', 's.instrucod', 's.gradocod', 'al.aulades');
    var $column_search = array('a.nomcomp');
    var $order = array('A.ALUCOD' => 'desc');

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA2 . '.alumno';
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function getAlumnosMatriculadosSimple() {
        $this->db->select("A.ALUCOD,A.DNI,A.NOMCOMP,A.INSTRUCOD,A.GRADOCOD,A.SECCIONCOD,S.NEMODES,A.FLGUTILES");
        $this->db->from($this->tabla . ' AS A');
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);
        $this->db->where('A.ESTADO', 'V');
        $this->db->where('A.FLG_MATRICULA', 1);
        $this->db->order_by("S.INSTRUCOD", "asc");
        $this->db->order_by("S.GRADOCOD", "asc");
        $this->db->order_by("S.SECCIONCOD", "asc");
        $this->db->order_by("A.NOMCOMP", "asc");
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function marcarRecojo($vid = '0') {
        $query = $this->db->query("UPDATE " . LIBRERIA2 . ".alumno SET flgutiles=1 WHERE alucod='$vid'");
        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getFiltroAlumno($vfiltro = '') {
        $this->db->select("A.ALUCOD,A.DNI,A.NOMCOMP,A.INSTRUCOD,A.GRADOCOD,A.SECCIONCOD,S.NEMODES,A.FLGUTILES");
        $this->db->from($this->tabla . ' AS A');
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);

        //$this->db->where('A.ESTADO', 'V');
        //$this->db->where('A.FLG_MATRICULA', 1);
        $this->db->like("A.NOMCOMP", $vfiltro);
        $this->db->order_by("S.INSTRUCOD", "asc");
        $this->db->order_by("S.GRADOCOD", "asc");
        $this->db->order_by("S.SECCIONCOD", "asc");
        $this->db->order_by("A.NOMCOMP", "asc");
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getCorreosxAlumno($alucod = '') {
        $sql = "SELECT pademail, padnom,mademail,madnom,apoemail, aponom FROM " . LIBRERIA2 . ".alumno WHERE alucod='$alucod'";
        $query = $this->db->query($sql);
        if (!$query) {
            return FALSE;
        } else {
            return $query->row();
        }
    }

    public function getAlumnosBoletas() {
        $sql = "SELECT * FROM " . LIBRERIA2 . ".boletas";
        $query = $this->db->query($sql);
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function updateRuta($alucod = '', $url = "") {
        if (!empty($alucod)) {
            $arrData = array(
                'url' => $url,
            );
            $this->db->where('alucod', $alucod);
            $query = $this->db->update(LIBRERIA2 . ".boletas", $arrData);
        }
    }

    public function getAlumnosMatriculados($nemo = 0) {
        if (!empty($nemo) && $nemo > 0) {
            $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.ALUCOD,A.DNI,A.NOMCOMP,SA.NUMORD,M.OBSERVACION");
            $this->db->from($this->tabla . ' AS A');
            $this->db->join(LIBRERIA . '.sga_matricula AS M', 'M.ALUANT=A.ALUCOD AND M.PERIODO=' . $this->S_ANO);
            $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $this->S_ANO);
            $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);
            $this->db->where('S.NEMO', $nemo);
            $this->db->where('SA.ESTADO', 'V');
            $this->db->order_by("A.NOMCOMP", "asc");
            $query = $this->db->get();
        }

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getAlumnos($nemo = 0) {
        if (!empty($nemo) && $nemo > 0) {
            $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.ALUCOD,A.DNI,A.NOMCOMP,SA.NUMORD");
            $this->db->from($this->tabla . ' AS A');
            $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $this->S_ANO);
            $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);
            $this->db->where('S.NEMO', $nemo);
            $this->db->where('SA.ESTADO', 'V');
            $this->db->order_by("A.NOMCOMP", "asc");
            $query = $this->db->get();
            //echo $this->db->last_query(); exit;
        }

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getAlumnosxSalon($nemo = 0, $alucod = 0, $flgEliminado=0) {
        if($this->S_ANO == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->S_ANO ==2020) {
            $DB = LIBRERIA_BD_2020;
        } else {
            $DB ="fercmias_academico";
        }
        if (!empty($nemo) && $nemo > 0) {
            $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.ALUCOD,A.DNI,A.NOMCOMP,SA.NUMORD,P.NOMCOMP AS PROFE, SA.REPITE");
            $this->db->from($this->tabla . ' AS A');
            $this->db->join($DB  . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $this->S_ANO);
            $this->db->join($DB  . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO); 
            $this->db->join($DB  . '.profe AS P', 'P.PROFCOD=S.PROFCOD');
            $this->db->where('S.NEMO', $nemo);
            if ($alucod != "T") {
                $this->db->where('A.ALUCOD', $alucod);
            }
            if($flgEliminado==0)
                $this->db->where('SA.ESTADO', 'V');
            $this->db->order_by("A.NOMCOMP", "asc");
            $query = $this->db->get();
            //echo $this->db->last_query(); exit;
        }

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function updateDatosAlumno($vAlucod = '', $vDni = '') {
        if (!empty($vAlucod)) {
            $arrData = array(
                'DNI' => $vDni,
            );
            $this->db->where('ALUCOD', $vAlucod);
            $query = $this->db->update(LIBRERIA2 . ".alumno", $arrData);
        }
    }

    public function updateNumLibroAlumno($vAlucod = '', $vLibro = '') {
        if (!empty($vAlucod)) {
            $arrData = array(
                'NUMLIBRO' => $vLibro,
            );
            $this->db->where('ALUCOD', $vAlucod);
            $query = $this->db->update(LIBRERIA2 . ".alumno", $arrData);
        }
    }

    public function _get_datatables_query() {
        $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.DNI,A.FLG_MATRICULA,A.ALUCOD,A.NOMCOMP,AL.AULADES,SA.ESTADO,SA.NUMORD");
        $this->db->from(LIBRERIA2 . '.alumno AS A');
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO =' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.aula AS AL', 'AL.AULACOD=S.AULACOD');
        $this->db->order_by("S.INSTRUCOD", "asc");
        $this->db->order_by("S.GRADOCOD", "asc");
        $this->db->order_by("S.SECCIONCOD", "asc");
        $this->db->order_by("A.NOMCOMP", "asc");

        $i = 0;
        // idnivel: idnivel, idgrado: idgrado, idfiltro: idfiltro, txtsearch: txtsearch
        if ($_POST['idnivel'] != '') {
            $idnivel = $_POST['idnivel'];
            $this->db->where("S.INSTRUCOD", $idnivel);
            if ($_POST['idgrado'] != '') {
                $idgrado = $_POST['idgrado'];
                $this->db->where("S.GRADOCOD", $idgrado);
            }
        }

        if ($_POST['idfiltro'] != '') {
            $idfiltro = $_POST['idfiltro'];
            $cadena = trim($_POST['txtsearch']);
            if ($idfiltro == 1)  // DNI
                $this->db->or_like("A.DNI", "$cadena");
            if ($idfiltro == 2)  // ALUCOD
                $this->db->or_like("A.ALUCOD", "$cadena");
            if ($idfiltro == 3)  // APELLIDOS
                $this->db->or_like("A.NOMCOMP", "$cadena");
            if ($idfiltro == 4)  // NUMLIBRO
                $this->db->or_like("A.NUMLIBRO", "$cadena");
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
        $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.DNI,A.FLG_MATRICULA,A.ALUCOD,A.NOMCOMP,AL.AULADES,SA.ESTADO,SA.NUMORD");
        $this->db->from(LIBRERIA2 . '.alumno AS A');
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO =' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.aula AS AL', 'AL.AULACOD=S.AULACOD');
        $this->db->order_by("S.INSTRUCOD", "asc");
        $this->db->order_by("S.GRADOCOD", "asc");
        $this->db->order_by("S.SECCIONCOD", "asc");
        $this->db->order_by("A.NOMCOMP", "asc");
        return $this->db->count_all_results();
    }

    public function getAlumnosAll() {

        $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.DNI,A.FLG_MATRICULA,A.ALUCOD,A.NOMCOMP,AL.AULADES,SA.ESTADO,SA.NUMORD");
        $this->db->from(LIBRERIA2 . '.alumno AS A');
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD');
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO');
        $this->db->join(LIBRERIA2 . '.aula AS AL', 'AL.AULACOD=S.AULACOD');
        $this->db->order_by("A.NOMCOMP", "asc");
        $this->db->order_by("S.INSTRUCOD", "asc");
        $this->db->order_by("S.GRADOCOD", "asc");
        $query = $this->db->get();

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    function moverAlumno($pcodGen = 0, $pAlucod = '', $pinstrucod = '', $pgradocod = '', $pseccioncod = '') {
        try {
            $query = $this->db->query("INSERT INTO alumno
                                                        (alucod,alucodant,dni,apepat,apemat,nombres,nomcomp,
                                                        sexo,fecnac,direccion,discod,telefono,padnom,padpater,padmater,padcelu,
                                                        pademail,madnom,madpater,madmater,madcelu,mademail,flg_bautizo,flg_comuni,
                                                        flg_confir,instrucod,gradocod,seccioncod,matricula,estado,famcod,idalumno,dnipater,dnimater)
                                                    SELECT '$pcodGen' AS codigo,alucod AS codant,dni,apepat,apemat,nombres,nomcomp,sexo,
                                                        fecnac,direccion,discod,telefono,padnom,padpater,padmater,padcelu,pademail
                                                        ,madnom,madpater,madmater,madcelu,mademail,flg_bautizo,flg_comuni,flg_confir,
                                                        '$pinstrucod','$pgradocod','$pseccioncod','S',estado,famcod,idalumno,dnipater,dnimater
                                                     FROM alumno_temp WHERE alucod='$pAlucod'");

            if (!$query) {
                //throw new Exception ($this->db->_error_message ());
                throw Exception($this->db->error());
                //print_r($this->db->error_message ());
            } else {
                $this->db->query("UPDATE alumno SET FLG_MATRICULA=1, MATRICULA='S' WHERE ALUCOD='$pAlucod'");
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function filtroAlumno($opcion = 0, $filtro = '') {
        $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.ALUCOD,A.DNI,A.NOMCOMP,SA.NUMORD,A.ESTADO,AL.AULADES");
        $this->db->from($this->tabla . ' AS A');
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO=' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO=' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.aula AS AL', 'AL.AULACOD=S.AULACOD');
        //$this->db->where('SA.ESTADO', 'V');
        //$this->db->where('A.ESTADO', 'V');
        //$this->db->where('A.FLG_MATRICULA', 1);

        if ($opcion == 1)  // apellidos
            $this->db->or_like("A.NOMCOMP", "$filtro");
        if ($opcion == 2)  // NOMBRES
            $this->db->or_like("A.NOMBRES", "$filtro");
        if ($opcion == 3)  // DNI
            $this->db->or_like("A.DNI", "$filtro");

        $this->db->order_by("A.NOMCOMP", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    function getDataAlumno($idAlumno = 0) {
        $this->db->select(""
                . "A.ALUCOD,A.DNI,A.APEPAT,A.APEMAT,A.NOMBRES,A.NOMCOMP,A.SEXO,A.DISCOD,"
                . "A.TELEFONO,A.PROCEDE,A.DIRECCION,A.INSTRUCOD,A.GRADOCOD,A.SECCIONCOD,A.FAMCOD,F.FAMDES,"
                . "A.MATRICULA,SA.ESTADO,S.NEMODES,S.NEMO,A.DIRECCION,A.FECNAC,A.TELEFONO2,A.EMAIL,"
                . "A.PADNOM,A.PADPATER,A.PADMATER,A.PADCELU,A.PADEMAIL,A.MADNOM,A.MADPATER,"
                . "A.MADMATER,A.MADCELU,A.MADEMAIL,A.FLG_BAUTIZO,A.FLG_COMUNI,A.FLG_CONFIR,"
                . "A.DNIPATER,A.DNIMATER,A.IDALUMNO,AU.AULACOD,AU.AULADES,NUMLIBRO"
        );
        $this->db->from(LIBRERIA2 . ".alumno AS A");
        $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.ALUCOD=A.ALUCOD AND SA.ANO =' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.salon AS S', 'S.NEMO=SA.NEMO AND S.ANO =' . $this->S_ANO);
        $this->db->join(LIBRERIA2 . '.familia AS F', 'F.FAMCOD=A.FAMCOD', 'left');
        $this->db->join(LIBRERIA2 . '.aula AS AU', 'AU.AULACOD=S.AULACOD');
        // $this->db->join (LIBRERIA2 . '.nivelgrado_mov AS GN', 'GN.INSTRUCOD=S.INSTRUCOD AND GN.GRADOCOD=S.GRADOCOD');
        $this->db->where("A.ALUCOD", $idAlumno);
        $query = $this->db->get();
        // echo $this->db->last_query(); exit;
        $query = $query->row();
        return $query;
    }

    function getDatosDocumentos($vAlucod = '') {
        $query = $this->db->query("SELECT * FROM " . LIBRERIA2 . ".alumno_docu WHERE ALUCOD='$vAlucod'");
        $query = $query->result();
        return $query;
    }

    function existeAlumno($vDni = '') {
        $total = 0;
        $query = $this->db->query("SELECT count(*) as total FROM " . LIBRERIA2 . ".alumno WHERE dni='$vDni'");
        $query = $query->row();
        $total = $query->total;
        return $total;
    }

    function validaFamiliaExistente($vpat = '', $vmat = '') {
        $query = $this->db->query("SELECT count(*) as total FROM " . LIBRERIA2 . ".alumno WHERE apepat='$vpat' AND apemat='$vmat'");
        $query = $query->row();
        $total = $query->total;
        return $total;
    }

    function getCursoCargo($vidAlumno = 0) {
        $this->db->select("*");
        $this->db->from(LIBRERIA2 . ".notas_consolidado_anual");
        $this->db->where("codigo", $vidAlumno);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function grabaAluDocumentos($vAlucod = ''/* , $vAlucodAnt = '' */, $vDocumentos = array(), $vcomentario = '', $vflg = 0) {
        if (is_array($vDocumentos) && !empty($vDocumentos)) {
            if ($vflg == 1) { // Cuando esta editando la matricula
                $this->db->query("DELETE FROM " . LIBRERIA2 . ".alumno_docu WHERE ALUCOD='$vAlucod'");
                foreach ($vDocumentos as $indDoc) {
                    $this->db->query("INSERT INTO  " . LIBRERIA2 . ".alumno_docu (IDDOCU,ALUCOD,OBSERVACION) VALUES ('$indDoc','$vAlucod','$vcomentario')");
                }
            } elseif ($vflg == 0) { // Cuando es una matricula nueva
                //$this->db->query ("DELETE FROM  " . LIBRERIA2 . ".alumno_docu WHERE ALUCOD='$vAlucodAnt'");
                foreach ($vDocumentos as $indDoc) {
                    $this->db->query("INSERT INTO  " . LIBRERIA2 . ".alumno_docu (IDDOCU,ALUCOD,OBSERVACION) VALUES ('$indDoc','$vAlucod','$vcomentario')");
                }
            }
        }
    }

    function generadorCodigo($vtable = '') {
        try {
            $sql = "CALL " . LIBRERIA2 . ".SP_GENERA_CODIGO('$vtable',@codigo) ";
            $query = $this->db->query($sql);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                $query = $this->db->query("select @codigo as codigo ");
                $query = $query->row();
                return $query->codigo;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function saveUpdate($arrAlumno = array(), $vflag = 0) {
        try {
            /*  $arrdata = array(
              'DNI' => $arrAlumno['dni'],
              'APEPAT' => $arrAlumno['apepat'],
              'APEMAT' => $arrAlumno['apemat'],
              'NOMBRES' => $arrAlumno['nombres'],
              'NOMCOMP' => $arrAlumno['nomcomp'],
              'DIRECCION' => $arrAlumno['direccion'],
              'ESTADO' => $arrAlumno['estado'],
              'FAMCOD' => $arrAlumno['famcod']
              ); */
            if ($vflag == 0) {
                $arrwhere = $arrAlumno['alucod'];
                $this->db->where('ALUCOD', $arrwhere);
                $query = $this->db->update(LIBRERIA2 . ".alumno", $arrAlumno);
            } else {
                $query = $this->db->insert(LIBRERIA2 . ".alumno", $arrAlumno);
            }

            // if ($query) {
            // 2: Actualizando datos de la tabla Salon_al
            /* $this->db->where ('ALUCOD', $arrwhere);
              $query = $this->db->update (LIBRERIA2 . ".salon_al", array('ESTADO' => $arrdata['ESTADO'])); */
            if (!$query)
                throw new Exception($this->db->_error_message());
            else
                return TRUE; //$this->db->last_query();









                
// } else {
            //  throw new Exception ($this->db->_error_message ());
            // }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /* function getListaGrado ($idNivel = '')
      {
      $query = $this->db->query ("SELECT * FROM " . LIBRERIA2 . ".gradonivel WHERE INSTRUCOD='$idNivel'");
      $query = $query->result ();
      return $query;
      } */

    function getListaGrado($idNivel = '') {
        $query = $this->db->query("SELECT * FROM " . LIBRERIA2 . ".gradonivel WHERE INSTRUCOD='$idNivel'");
        $query = $query->result();
        return $query;
    }

    function getCodigoAlumno($vdni = '') {
        $query = $this->db->query("SELECT a.alucod FROM " . LIBRERIA2 . ".alumno a "
                . " INNER JOIN  " . LIBRERIA2 . ".salon_al s ON s.alucod=a.alucod"
                . " WHERE a.dni='$vdni' and s.ano=".$this->S_ANO);
        $query = $query->row();
        if($query)
            $alucod = $query->alucod;
        else 
            $alucod =null;
        return $alucod;
    }

    function getDatosAlumno($vdni = '') {
        $query = $this->db->query("SELECT a.alucod,a.nomcomp,a.instrucod,a.gradocod FROM " . LIBRERIA2 . ".alumno a "
                . " INNER JOIN  " . LIBRERIA2 . ".salon_al s ON s.alucod=a.alucod"
                . " WHERE a.dni='$vdni' and s.ano=".$this->S_ANO);
        return $query->row();
    }

    function getFamiliaAlumno($vdni = '') {
        $query = $this->db->query("SELECT f.famcod, f.famdes, f.paddni,f.padapepat,f.padapemat,f.padnombre, concat(f.padapepat,' ',f.padapemat,', ',f.padnombre) as padre,flgpadapo, "
                . "f.maddni,f.madapepat,f.madapemat,f.madnombre, concat(f.madapepat,' ',f.madapemat,', ',f.madnombre) as madre,flgmadapo FROM " . LIBRERIA2 . ".familia as f "
                . "INNER JOIN  " . LIBRERIA2 . ".alumno as a on a.FAMCOD=f.FAMCOD WHERE a.dni='$vdni' AND (a.flg_matricula=1  OR a.flg_matricula=0 OR a.idalumno=5000) GROUP BY a.dni ");
        $query = $query->row();
        return $query;
    }

    function geUsuarioCampus($vdni = '') {
        $query = $this->db->query("SELECT * FROM " . LIBRERIA2 . ".usuarios_campus WHERE dni='$vdni'");
        $query = $query->row();
        return $query;
    }

    function _getDatoAlumno($alucod = '') {
        $query = $this->db->query("SELECT 
                                            a.alucod,a.dni,a.nomcomp,correo_institucional,
                                            , CONCAT(a.padpater,' ',a.padmater,', ',a.padnom) AS padre
                                            , a.padcelu,paddireccion, a.padcelu2,a.pademail
                                            , CONCAT(a.madpater,' ',a.madmater,', ',a.madnom) AS madre
                                            , a.madcelu,maddireccion , a.madcelu2,a.mademail
                                            , CONCAT(a.apopater,' ',a.apomater,', ',a.aponom) AS apoderado
                                            , a.apocelu,apodireccion, a.apocelu2
                                            FROM  " . LIBRERIA2 . ".alumno AS a
                                            WHERE a.alucod='$alucod'
                                            ORDER BY a.instrucod,a.gradocod,a.nomcomp");
        $query = $query->row();
        return $query;
    }

    function getDniAlumno($vAlucod = '') {
        $query = $this->db->query("SELECT dni FROM " . LIBRERIA2 . ".alumno WHERE alucod='$vAlucod'");
        $query = $query->row();
        return $query->dni;
    }

    function getListaAulas($idNivel = '', $idGrado = '', $flg = 0) {
        // --- Verificando la Vigencia de la Matricula
        $query = $this->db->query("SELECT COUNT(*) AS TOTAL FROM " . LIBRERIA . ".sga_config_matricula WHERE estado='V' AND DATE(NOW()) BETWEEN  fec_ini  AND  fec_fin");
        $row = $query->row();
        $row->TOTAL;
        // ---------------------------------------------
        if ($row->TOTAL == 1) {
            $vanio = 2024;
        } elseif ($flg == 1) {
            $vanio = $this->S_ANO;
        } else {
            $vanio = date("Y");
            if ($vanio == $this->S_ANO)
                $vanio = $this->S_ANO - 1;
            else
                $vanio = $vanio;
        }
        $query = $this->db->query("SELECT a.AULACOD,a.SECCIONCOD,a.AULADES,s.NEMO,s.NEMODES, a.LIMITE, (select count(*) from " . LIBRERIA2 . ".salon_al where nemo=s.nemo and ano='" . $vanio . "' and estado='V') as total FROM  " . LIBRERIA2 . ".gradoseccion AS g "
                . "INNER JOIN  " . LIBRERIA2 . ".aula AS a ON a.aulacod=g.aulacod "
                . "INNER JOIN  " . LIBRERIA2 . ".salon AS s ON s.aulacod=g.aulacod "
                . "WHERE g.INSTRUCOD='$idNivel' AND g.GRADOCOD='$idGrado' AND g.ANO='$vanio' and s.blkcal=1");
        $query = $query->result();
        //echo $this->db->last_query (); exit;
        return $query;
    }

    function activar($vId = '', $vNemo = '') {
        try {
            $sql = "CALL " . LIBRERIA2 . ".SP_HABILITA_ALUMNO('$vId','$vNemo') ";
            $query = $this->db->query($sql);
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
            return $e->getMessage();
        }
    }

    function eliminaAlumno($vId = '', $vNemo = '') {
        try {
            $sql = "CALL " . LIBRERIA2 . ".sp_deshabilita_alumno('$vId','$vNemo') ";
            $query = $this->db->query($sql);
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
            return $e->getMessage();
        }
    }

    public function getHijos($idFamilia = '') {
        if (!empty($idFamilia)) {
            $this->db->select("a.dni,a.alucod,a.nombres,s.nemo, s.nemodes,a.idalumno,s.instrucod");
            $this->db->from($this->tabla . ' as a');
            $this->db->join(LIBRERIA2 . '.salon_al as sa', 'sa.alucod=a.alucod and sa.ano=' . $this->S_ANO);
            $this->db->join(LIBRERIA2 . '.salon as s', 's.nemo=sa.nemo and s.ano=' . $this->S_ANO);
            $this->db->where('a.famcod', $idFamilia);
            //  $this->db->where ('a.matricula', 'S');
            $this->db->where('a.estado', 'V');
            $query = $this->db->get();
            // echo $this->db->last_query ();
            //exit;
        }

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

}
