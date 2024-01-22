<?php

/**
 * @package       modules/salon_model/model
 * @name            salon_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/14
 */
class salon_model extends CI_Model {

    public $tabla = '';
    public $ano = '';

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA2 . '.salon';
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG'); //ANO_VIG; //date ("Y");
    }

    function getAllAulas($nivel = '') { // Para inicio de Año (Matroculas) quitar el filtro por estado 'V'
        $sql = "SELECT s.nemo,s.nemodes,s.instrucod,a.aulades, "
                . "(SELECT COUNT(*) FROM " . LIBRERIA2 . ".salon_al where nemo=s.nemo and ano='$this->ano' and estado='V' ) " //and estado='V'
                . "AS total FROM " . $this->tabla . " AS s INNER JOIN " . LIBRERIA2 . ".aula as a ON  a.aulacod=s.aulacod WHERE  s.ano='$this->ano'  and s.blkcal=1 ";
        if ($nivel != "") {
            $sql .= " and s.instrucod='$nivel' ";
        }
        $sql .= " order by s.instrucod, s.gradocod,s.seccioncod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }

    function getAlumnosAnteriores($pnemo = '') {
        $anoActual = date("Y");
        $query = $this->db->query("SELECT a.alucod,a.dni, a.nomcomp, IF((SELECT COUNT(*) FROM  ". LIBRERIA .".sga_matricula  WHERE dni=a.dni AND periodo='".$anoActual."')>0,'MATRICULADO','PENDIENTE') AS estado "
                . " FROM " . LIBRERIA2 . ".alumno  AS a INNER JOIN " . LIBRERIA2 . ".salon_al as sa on sa.alucod=a.alucod and sa.ano='$this->ano' "
                . "INNER JOIN " . $this->tabla . " AS s ON  s.nemo=sa.nemo and s.ano='$this->ano' "
                . "WHERE s.nemo='$pnemo' and sa.estado='V' GROUP BY a.dni  order by a.nomcomp ");
        $query = $query->result();
        //echo $this->db->last_query(); exit; 
        return $query;
    }

    function getCursosSubAreas($vinstru = '', $vgrado = '') {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }    else {
              $DB ="fercmias_academico";
        }             
        $query = $this->db->query("SELECT g.cursocod,g.cursonat,c.cursonom,c.cursocor, cursopre
                                                            FROM " . $DB . ".cur_gra AS g 
                                                            INNER JOIN " . $DB . ".curso AS c ON c.cursocod=g.cursocod
                                                    WHERE g.ano=" . $this->ano . " AND g.instrucod='" . $vinstru . "' AND g.gradocod=" . $vgrado . " AND g.cursonat IN ('O','I') 
                                                    ORDER BY orden  ");
        $query = $query->result();   
        //echo $this->db->last_query(); exit;
        return $query;
    }
    
    
    function getCompetenciaPorUnidadCurso($vcurso = '', $vunidad = '', $vnemo = '') {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }    else {
              $DB ="fercmias_academico";
        }             
        $query = $this->db->query("SELECT * FROM " . $DB . ".critdesc WHERE nemo='$vnemo' AND unicod=$vunidad AND cursocod='$vcurso' AND critcod='CRI1'");
        $query = $query->result();   
		//echo $this->db->last_query(); exit;
        return $query;
    }
	
    function getResumenPorPeriodo($vinstru = '', $vgrado = '', $vbimestre = '') { 
        $query = $this->db->query("select * from ".LIBRERIA.".sga_resumen_notas where nivel='$vinstru' and gradocod='$vgrado' and bimecod= '$vbimestre'");
        $query = $query->result();   
        return $query;
    }
    

    function getCursosAreas($vinstru = '', $vgrado = '') {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }    else {
              $DB ="fercmias_academico";
        }             

        $query = $this->db->query("SELECT g.cursocod,g.cursonat,c.cursonom,c.cursocor,cursoabr,cursopre
					FROM " . $DB . ".cur_gra AS g 
					INNER JOIN " . $DB . ".curso AS c ON c.cursocod=g.cursocod
				WHERE g.ano=" . $this->ano . " AND g.instrucod='" . $vinstru . "' AND g.gradocod=" . $vgrado . " AND g.cursonat IN ('D','O') 
				ORDER BY orden   ");
      //  echo $this->db->last_query(); exit;
        $query = $query->result();
        return $query;
    }

    function getNumInternoXCursoPadre($vinstru = '', $vgrado = '', $cursopad='') {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }    else {
              $DB ="fercmias_academico";
        }             

        $query = $this->db->query("SELECT COUNT(*) total 
                                    FROM " . $DB . ".cur_gra AS g 
                                    INNER JOIN " . $DB . ".curso AS c ON c.cursocod=g.cursocod
                            WHERE g.ano=" . $this->ano . " AND g.instrucod='" . $vinstru . "' AND g.gradocod=" . $vgrado . " AND g.cursopad='$cursopad' ");
        $query = $query->row();
        return $query;
    }
    
    function getNumInternoXNivelGrado($vinstru = '', $vgrado = '') {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }    else {
              $DB ="fercmias_academico";
        }             

        $query = $this->db->query("SELECT COUNT(*) total 
                                    FROM " . $DB . ".cur_gra AS g 
                                    INNER JOIN " . $DB . ".curso AS c ON c.cursocod=g.cursocod
                            WHERE g.ano=" . $this->ano . " AND g.instrucod='" . $vinstru . "' AND g.gradocod=" . $vgrado . " AND g.cursonat IN ('I','O') ");
        $query = $query->row();
        return $query;
    }
    
    function getAlumnosNemo($pnemo = '') { // Para inicio de Año (Matroculas) quitar el filtro por estado 'V'
        $anoCompara1 = ($this->ano - 1) ; // 2023
        $anoCompara2 = ($this->ano - 2) ; // 2022
        $query = $this->db->query("SELECT a.alucod,a.dni, a.nomcomp, "
                . " IF((SELECT COUNT(*) FROM " . LIBRERIA . ".sga_matricula  WHERE dni=a.dni AND periodo=$anoCompara1)>0,'RATIFICADO', "
                . " IF((SELECT COUNT(*) FROM " . LIBRERIA . ".sga_matricula  WHERE dni=a.dni AND periodo<=$anoCompara2)>0,'REINGRESO','NUEVO')) as estado "
                . " FROM " . LIBRERIA2 . ".alumno  AS a INNER JOIN " . LIBRERIA2 . ".salon_al as sa on sa.alucod=a.alucod and sa.ano='$this->ano' "
                . "INNER JOIN " . $this->tabla . " AS s ON  s.nemo=sa.nemo and s.ano='$this->ano' "
                . "WHERE s.nemo='$pnemo' and sa.estado='V' order by a.nomcomp"); // and a.estado='V' (para inicio de año)
        /* echo "SELECT a.alucod,a.dni, a.nomcomp, IF(substring(a.alucod,1,4) ='$this->ano','NUEVO','ANTIGUO') as estado "
          . " FROM " . LIBRERIA2 . ".alumno  AS a INNER JOIN " . LIBRERIA2 . ".salon_al as sa on sa.alucod=a.alucod and sa.ano='$this->ano' "
          . "INNER JOIN " . $this->tabla . " AS s ON  s.nemo=sa.nemo and s.ano='$this->ano' "
          . "WHERE s.nemo='$pnemo' and a.estado='V' order by a.nomcomp"; exit; */
        $query = $query->result(); 
        return $query;
    }

    function getDatosNemo($pnemo = '') {
        $query = $this->db->query("SELECT  s.nemo,s.nemodes,p.nomcomp,s.instrucod,gradocod FROM " . $this->tabla . " AS s "
                . " LEFT JOIN " . LIBRERIA2 . ".profe AS p ON p.profcod=s.profcod "
                . "WHERE s.nemo='$pnemo'");
        $query = $query->row();
        return $query;
    }

    public function getDatosEmpresa($vruc = '') {
        if (!empty($vruc)) {
            $this->db->select("*");
            $query = $this->db->get_where(LIBRERIA . ".sga_sunat", array('ruc' => $vruc));
        } else {
            $this->db->select("*");
            $this->db->where("ano", $this->ano);
            $this->db->order_by("id");
            $query = $this->db->get($this->tabla);
        }

        //echo $this->db->last_query(); exit;
        // print_r($query->result());
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getSalones($nemo = 0, $flag = 0) {

        if (!empty($nemo) && $nemo > 0) {
            if ($flag == '1') {
                $anio = (int) $this->ano + 1;
            } else {
                $anio = $this->ano;
            }
            $this->db->select("NEMO,NEMODES,ANO,INSTRUCOD,GRADOCOD,SECCIONCOD");
            $query = $this->db->get_where($this->tabla, array('nemo' => $nemo, 'ano' => $this->ano));
        } else {
            $this->db->select("NEMO,NEMODES,INSTRUCOD,GRADOCOD,SECCIONCOD");
            $this->db->where("ano", $this->ano);
            $this->db->order_by("INSTRUCOD");
            $this->db->order_by("GRADOCOD");
            $this->db->order_by("SECCIONCOD");
            $query = $this->db->get($this->tabla);
        }

        //echo $this->db->last_query(); exit;
        // print_r($query->result());
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getDataAlumnoApp($alucod = 0, $anio = 2020) {
$query = $this->db->query("SELECT  s.NEMO,s.NEMODES,s.ANO,s.INSTRUCOD,s.GRADOCOD,s.SECCIONCOD,a.DNI,a.NOMCOMP 
                                                FROM " . LIBRERIA2 . ".alumno AS a
                                                INNER JOIN " . LIBRERIA2 . ".salon_al sa ON sa.alucod=a.alucod 
                                                INNER JOIN " . LIBRERIA2 . ".salon AS s ON s.nemo=sa.nemo
                                                WHERE a.alucod='$alucod' AND sa.ano=$anio");
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }
    
    public function getAulasMigrar($vnivel = '', $vgrado = '') {
        $this->db->select("NEMO,NEMODES");
        $this->db->where("INSTRUCOD", $vnivel);
        $this->db->where("GRADOCOD", $vgrado);
        $this->db->where("ano", $this->ano);
        $query = $this->db->get($this->tabla);
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function cambiaSalonAlumno($vnemOrg = 0, $vnemDes = 0, $valucod = '') {
        try {
            $sql = "CALL " . LIBRERIA2 . ".sp_cambio_salon('$valucod','$vnemDes','$vnemOrg') ";
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
            return FALSE;
        }
    }

    public function getNivel() {
        $this->db->select("INSTRUCOD,INSTRUDES");
        $this->db->group_by("INSTRUCOD");
        $query = $this->db->get(LIBRERIA2 . ".gradonivel");
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getGrados($nivel = 0) {
        $this->db->select("GRADOCOD,GRADODES");
        $this->db->where("INSTRUCOD", $nivel);
        $query = $this->db->get(LIBRERIA2 . ".gradonivel");
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getSalonesxNivel($nivel = 0, $grado = 0) {
        $this->db->select("NEMO,NEMODES");
        $this->db->where("INSTRUCOD", $nivel);
        $this->db->where("GRADOCOD", $grado);
        $this->db->where("ANO", $this->ano);
        $query = $this->db->get(LIBRERIA2 . ".salon");
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getDeudasXPeriodo($vmes = '0', $vnivel = '0', $vgrado = 0, $vaula = '0') {
        try {
            $vano = $this->nativesession->get('S_ANO_VIG');
            $sql = "CALL " . LIBRERIA . ".SP_S_DEUDAS_X_PERIODO('$vmes','$vnivel',$vgrado, '$vaula',$vano) ";
            //echo  $sql; exit;
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateAlumnoSalon($vAlucod = '', $vnemo = '') {
        try {
            $this->db->where('alucod', $vAlucod);
            $this->db->where('ano', $this->ano);
            $this->db->update(LIBRERIA2 . ".salon_al", array('nemo' => $vnemo));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function insertaAlumnoSalon($vAlucod = '', $vnemo = '') {
        try {
            $arrData = array(
                'nemo' => $vnemo,
                'alucod' => $vAlucod,
                'estado' => 'V',
                'flag' => 'U',
                'ano' => '2024' //$this->ano
            );
            $query = $this->db->insert(LIBRERIA2 . ".salon_al", $arrData);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                return TRUE;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAulasxId($aula = 0, $flgReturn = 0) {
        $this->db->select("AU.AULACOD,AU.AULADES");
        $this->db->from(LIBRERIA2 . ".aula as AU");
        $this->db->where("AU.AULACOD", $aula);
        $this->db->where("AU.ANO", $this->ano);
        $query = $this->db->get();
        if (!$query) {
            return FALSE;
        } else {
            if ($flgReturn == 0) {
                return $query->result();
            } else {
                $datos = $query->row();
                return $datos->AULADES;
            }
        }
    }

    public function getAulasxNivel($nivel = 0, $grado = 0) {
        $this->db->select("AU.AULACOD,AU.AULADES");
        $this->db->from(LIBRERIA2 . ".aula as AU");
        $this->db->join(LIBRERIA2 . ".salon as S", "S.AULACOD=AU.AULACOD");
        $this->db->where("S.INSTRUCOD", $nivel);
        $this->db->where("S.GRADOCOD", $grado);
        $this->db->where("AU.ANO", $this->ano);
        $this->db->where("S.ANO", $this->ano);
        $query = $this->db->get();
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getDatoAlumno($alucod = 0) {
        if (!empty($alucod)) {
            $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.ALUCOD,A.DNI,A.APEPAT,A.APEMAT,A.NOMBRES,A.NOMCOMP,A.CORREO_INSTITUCIONAL,A.FAMCOD");
            $this->db->from($this->tabla . ' AS S');
            $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.NEMO=S.NEMO AND SA.ANO=' . $this->ano);
            $this->db->join(LIBRERIA2 . '.alumno AS A', 'A.ALUCOD=SA.ALUCOD');
            $this->db->where('A.DNI', $alucod);
            $this->db->where('S.ANO', $this->ano);
            $query = $this->db->get();
            //echo $this->db->last_query(); exit;
        }

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }
    
        public function getDatoAlumnoxCodigo($alucod = 0) {
        if (!empty($alucod)) {
            $this->db->select("S.NEMO,S.NEMODES,S.ANO,S.INSTRUCOD,S.GRADOCOD,S.SECCIONCOD,A.ALUCOD,A.DNI,A.APEPAT,A.APEMAT,A.NOMBRES,A.NOMCOMP,A.FAMCOD");
            $this->db->from($this->tabla . ' AS S');
            $this->db->join(LIBRERIA2 . '.salon_al AS SA', 'SA.NEMO=S.NEMO AND SA.ANO=' . $this->ano);
            $this->db->join(LIBRERIA2 . '.alumno AS A', 'A.ALUCOD=SA.ALUCOD');
            $this->db->where('A.ALUCOD', $alucod);
            $this->db->where('S.ANO', $this->ano);
            $query = $this->db->get();
            //echo $this->db->last_query(); exit;
        }

        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

        public function getDatoUsuarioApp($famcod = '') {
        $query = $this->db->query("SELECT usucod,clave from usuarios where famcod='$famcod'"); 
        $query = $query->result();
		//echo $this->db->last_query(); exit;
        return $query;
    }
	
    public function updateSalonAlumno($vAlucod = '', $vnemo = '') {
        if (!empty($vAlucod) && !empty($vnemo)) {
            $this->db->select("NEMO,NEMODES,INSTRUCOD,GRADOCOD,SECCIONCOD");
            $this->db->from(LIBRERIA2 . '.salon AS S');
            $this->db->where('NEMO', $vnemo);
            $query = $this->db->get();
            $row = $query->row();

            $arrData = array(
                'INSTRUCOD' => $row->INSTRUCOD,
                'GRADOCOD' => $row->GRADOCOD,
                'SECCIONCOD' => $row->SECCIONCOD
            );
            $this->db->where('DNI', $vAlucod);
            $query = $this->db->update(LIBRERIA2 . ".alumno", $arrData);
        }
    }

    public function getNemoxAula($vIdAula = 0, $vtipo = 0) {
        if (!empty($vIdAula)) {
            $this->db->select("NEMO,NEMODES,INSTRUCOD,GRADOCOD,SECCIONCOD");
            $this->db->from(LIBRERIA2 . '.salon AS S');
            $this->db->where('AULACOD', $vIdAula);
            $query = $this->db->get();
        }
        if (!$query) {
            return FALSE;
        } else {
            if ($vtipo == 1) {
                $row = $query->row();
                return $row->NEMO;
            } else {
                return $query->row();
            }
        }
    }

    public function getAulaxNemo($vNemo = 0, $vtipo = 0) {
        if (!empty($vNemo)) {
            $this->db->select("NEMODES,INSTRUCOD,GRADOCOD,SECCIONCOD");
            $this->db->from(LIBRERIA2 . '.salon AS S');
            $this->db->where('NEMO', $vNemo);
            $query = $this->db->get();
        }
        if (!$query) {
            return FALSE;
        } else {
            if ($vtipo == 1) {
                $row = $query->row();
                return $row->NEMODES;
            } else {
                return $query->row();
            }
        }
    }

    public function updateNemoMatricula($valucod = '', $vnemo = '') {
        $arrData = array(
            'nemo' => $vnemo
        );
        $this->db->where('aluant', $valucod);
        $this->db->where('periodo', $this->ano);
        $query = $this->db->update(LIBRERIA . ".sga_matricula", $arrData);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function updateNemoSalonal($valucod = '', $vnemo = '') {
        $arrData = array(
            'nemo' => $vnemo
        );
        $this->db->where('alucod', $valucod);
        $this->db->where('ano', $this->ano);
        $query = $this->db->update(LIBRERIA2 . ".salon_al", $arrData);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function updateNemoAlumno($valucod = '', $vinstrucod = '', $vgradocod = '', $vseccion = '') {
        $arrData = array(
            'instrucod' => $vinstrucod,
            'gradocod' => $vgradocod,
            'seccioncod' => $vseccion
        );
        $this->db->where('alucod', $valucod);
        $this->db->where('flg_matricula', 1);
        $query = $this->db->update(LIBRERIA2 . ".alumno", $arrData);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function getPromedioOficial($alucod = '', $bimecod='', $cursocod='') {
        $query = $this->db->query("SELECT ".LIBRERIA2.".fu_get_prom_area_oficial('$alucod', '$bimecod', '$cursocod') prom"); 
       return $query->row();         
    }
    
    public function getPromedioDependiente($alucod = '', $bimecod='', $instrucod='', $gradocod='', $cursocod='', $numInterno=0) {        
        $query = $this->db->query("SELECT ".LIBRERIA2.".fu_get_prom_area_dependiente('$alucod', '$bimecod', '$instrucod', '$gradocod', '$cursocod', $numInterno) prom"); 
        //echo $this->db->last_query(); exit;
       return $query->row();         
    }
    
    public function getPromedioGA($alucod = '', $bimecod='', $instrucod='', $gradocod='', $numInterno=0) {        
        $query = $this->db->query("SELECT ".LIBRERIA2.".fu_get_prom_ga('$alucod', '$bimecod', '$instrucod', '$gradocod', $numInterno) prom"); 
        //echo $this->db->last_query(); exit;
       return $query->row();         
    }    
    
}
