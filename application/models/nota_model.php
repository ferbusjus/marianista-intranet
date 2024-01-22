<?php

/**
 * @package       modules/alumno_model/model
 * @name            alumno_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/14
 */
class nota_model extends CI_Model {

    public $tabla = '';
    public $ano = '';

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA2 . '.notas';
        $this->ano = $this->nativesession->get('S_ANO_VIG');
    }

    public function getNotasxBimestre($arrDatos = array()) {
        $vNemo = '';
        $vAlucod = '';
        $vBimestre = '';
        $vUnidad = '';

        if (!empty($arrDatos["NEMO"])) {
            $vNemo = $arrDatos["NEMO"];
        }
        if (!empty($arrDatos["ALUCOD"])) {
            $vAlucod = $arrDatos["ALUCOD"];
        }
        if (!empty($arrDatos["BIMESTRE"])) {
            $vBimestre = $arrDatos["BIMESTRE"];
        }
        if (!empty($arrDatos["UNIDAD"])) {
            $vUnidad = $arrDatos["UNIDAD"];
        }

        $vNotIn = '';
        if ($vNemo <= 2017009 || $vNemo == 2017013)
            $vNotIn = "'0'"; // INICIAL
        if ($vNemo >= 2017010 && $vNemo <= 2017037)
            $vNotIn = "'01','06'"; // PRIMARIA
        if ($vNemo > 2017038)
            $vNotIn = "'01','06','15'"; // SECUNDARIA



            
// Nota : Se debe modificar logica de salida ya que hay que agrupar por cursos oficiales
        //$sql = " SELECT cursocod,pb FROM ".$this->tabla." WHERE nemo='$vNemo' AND bimecod=$vBimestre AND unicod <=$vUnidad AND alucod='$vAlucod' ";
        $sql = " SELECT cursocod,bimecod, CEIL(SUM(pf)/2) AS prom FROM  " . $this->tabla . " 
            WHERE nemo='$vNemo'  AND  alucod='$vAlucod' AND bimecod<=$vBimestre 
            AND  cursocod NOT IN ($vNotIn)
            GROUP BY cursocod,bimecod ";

        $query = $this->db->query($sql);
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    public function getNotasxBimestreBoleta($alucod = '', $idcurso = 0, $bimestre = 0, $vunicod = 0) {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }  else {
              $DB ="fercmias_academico";
        }        
        $sql = "SELECT n1e1,n1e2,n1e3,n1e4,n1e5,pb,eu,pf,pfc FROM " . $DB . ".notas  WHERE alucod='$alucod' AND cursocod='$idcurso' AND bimecod<=$bimestre AND unicod<=$vunicod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }

    public function getNotasxBimestreBoletaInicialNew($alucod = '', $idcurso = 0, $bimestre = 0, $vunicod = 0) {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }  else {
              $DB ="fercmias_academico";
        }        
        $sql = "SELECT n1e1,n1e2,n1e3,n1e4,n1e5,pb,eu,pf,pfc FROM " . $DB . ".notas  WHERE alucod='$alucod' AND cursocod='$idcurso' AND bimecod=$bimestre AND unicod=$vunicod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }
	
        public function getCriteriosxUnidad($nemo = '', $idcurso = 0, $vunicod = 0) {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }  elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }   else {
              $DB ="fercmias_academico";
        }             
        $sql = "  SELECT * FROM " . $DB . ".critdesc  WHERE nemo='$nemo' AND cursocod='$idcurso' AND unicod=$vunicod AND critcod='CRI1'  ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }
    
    public function getNotasxBimestreBoletaInicial($alucod = '', $idcurso = 0, $bimestre = 0, $vunicod = 0) {
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }  elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }   else {
              $DB ="fercmias_academico";
        }             
        $sql = "SELECT * FROM " . $DB . ".notas  WHERE alucod='$alucod' AND cursocod='$idcurso' AND bimecod=$bimestre AND unicod=$vunicod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }
    
    public function getConductaxBimestreBoletaInicial($alucod = '', $bimestre = 0, $vunicod = 0){
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }  else {
              $DB ="fercmias_academico";
        }            
        $sql = "SELECT pb,obs,numinasjus,numinasinjus,numtar FROM " . $DB . ".conducta WHERE alucod='$alucod' AND bimecod =$bimestre AND unicod =$vunicod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;        
    }
    
    public function getEvaPadresBimestreBoletaInicial($alucod = '', $bimestre = 0, $vunicod = 0){
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }  else {
              $DB ="fercmias_academico";
        }                 
        $sql = "SELECT *  FROM " . $DB . ".nota_evapadre WHERE alucod='$alucod' AND bimecod =$bimestre AND unicod =$vunicod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;        
    }    
    
    public function getEvaEstudianteBimestreBoletaInicial($alucod = '', $bimestre = 0, $vunicod = 0){
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }  else {
              $DB ="fercmias_academico";
        }                 
        $sql = "SELECT *  FROM " . $DB . ".nota_registro_cualitativo WHERE alucod='$alucod' AND bimecod =$bimestre AND unicod =$vunicod ";
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;        
    } 
    
    public function getNotasConducta($alucod = '', $bimestre = 0, $vunicod = 0){
        if($this->ano == date("Y")){
            $DB = LIBRERIA2;
        } elseif($this->ano ==2020) {
            $DB = LIBRERIA_BD_2020; 
        }  elseif($this->ano ==2021) {
            $DB = LIBRERIA_BD_2021; 
        }   elseif($this->ano ==2022) {
            $DB = LIBRERIA_BD_2022; 
        }  else {
              $DB ="fercmias_academico";
        }               
        $sql = "SELECT pb,obs,numinasjus,numinasinjus,numtar FROM " . $DB . ".conducta WHERE alucod='$alucod' AND bimecod <=$bimestre AND unicod <=$vunicod ";
        
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;        
    }
    
    public function getNotasAlumno($arrDatos = array()) {
        $vNemo = '';
        $vAlucod = '';
        $vBimestre = '';
        $vUnidad = '';

        if (!empty($arrDatos["NEMO"])) {
            $vNemo = $arrDatos["NEMO"];
        }
        if (!empty($arrDatos["ALUCOD"])) {
            $vAlucod = $arrDatos["ALUCOD"];
        }
        if (!empty($arrDatos["BIMESTRE"])) {
            $vBimestre = $arrDatos["BIMESTRE"];
        }
        if (!empty($arrDatos["UNIDAD"])) {
            $vUnidad = $arrDatos["UNIDAD"];
        }

        $vNotIn = '';
        if ($vNemo <= 2019013)
            $vNotIn = "'0'"; // INICIAL
        if ($vNemo >= 2019014 && $vNemo <= 2019042)
            $vNotIn = "'01','06'"; // PRIMARIA
        if ($vNemo > 2019043)
            $vNotIn = "'01',06,'15'"; // SECUNDARIA

        $sql = "SELECT n.cursocod,cg.reporte,c.cursonom,p.nomcomp,s.instrucod,n.pf,n.pb,(CASE n.pf WHEN n.pf='' THEN '-' ELSE n.pf END) AS pf2,
                    (SELECT  (CASE pb WHEN pb='' THEN '-' ELSE pb END)
                            FROM " . LIBRERIA2 . ".conducta 
                            WHERE nemo=n.nemo AND alucod=n.alucod AND bimecod=n.bimecod AND unicod=n.unicod
                    ) AS conducta 
                    FROM " . LIBRERIA2 . ".notas  AS n
                    INNER JOIN " . LIBRERIA2 . ".asignapr AS pr ON pr.nemo=n.nemo AND pr.cursocod=n.cursocod 
                     INNER JOIN " . LIBRERIA2 . ".curso AS c ON c.cursocod=pr.cursocod
                     INNER JOIN " . LIBRERIA2 . ".salon AS s ON s.nemo=pr.nemo 
                     INNER JOIN " . LIBRERIA2 . ".profe AS p ON  p.profcod=pr.profcod 
                     INNER JOIN " . LIBRERIA2 . ".cur_gra AS cg ON cg.instrucod=s.instrucod AND cg.gradocod=s.gradocod AND cg.cursocod=pr.cursocod AND cg.cursocod=n.cursocod
                    WHERE n.nemo='$vNemo' AND n.alucod='$vAlucod' AND n.bimecod=$vBimestre AND n.unicod=$vUnidad  AND n.cursocod NOT IN ($vNotIn)
                    ORDER BY cg.reporte";
        $query = $this->db->query($sql);
        if (!$query) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    /*
      public function getAulasControl() {
      $sql = "SELECT
      n.nemo,s.nemodes,p.nomcomp AS tutor,
      COUNT(*) AS total1,
      (SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas WHERE bimecod=2 AND unicod=4 AND cursocod NOT IN ('01','06')  AND nemo=n.nemo AND pb>0 ) AS total2,
      ROUND(((SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas WHERE bimecod=2 AND unicod=4 AND cursocod NOT IN ('01','06')  AND nemo=n.nemo AND pb>0 ) * 100)/(
      SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas AS nt
      INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON nt.alucod=sa.alucod AND sa.ano='2018' AND sa.estado='V'
      WHERE nt.bimecod=2 AND nt.unicod=4 AND nt.nemo=n.nemo AND nt.cursocod NOT IN ('01','06')
      )) AS total3
      FROM " . LIBRERIA2 . ".notas AS n
      INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON n.alucod=sa.alucod AND sa.ano='2018' AND sa.estado='V'
      INNER JOIN " . LIBRERIA2 . ".salon AS s ON sa.nemo=s.nemo AND s.ano='2018'
      INNER JOIN " . LIBRERIA2 . ".profe AS p ON s.profcod=p.profcod
      WHERE n.bimecod=2 AND n.unicod=4
      AND n.nemo IN (SELECT nemo FROM  " . LIBRERIA2 . ".salon WHERE instrucod='P' AND ano='2018' AND blkcal='1')
      AND n.cursocod NOT IN ('01','06')
      GROUP BY n.nemo";
      $query = $this->db->query($sql);
      return $query->result();
      }
     */

    public function getDataTables($vnivel = '', $vbimestre = 0, $vunidad = 0) {
        $vNotIn = '';
        $prom = '';
        if ($vnivel == 'I') {
            $vNotIn = "'0'"; // INICIAL
            $prom = "pf>0";
        }
        if ($vnivel == 'P') {
            $vNotIn = "'01','06'"; // PRIMARIA
            $prom = "pb>0";
        }
        if ($vnivel == 'S') {
            $vNotIn = "'01',06,'15'"; // SECUNDARIA   
            $prom = "pb>0";
        }
        
        $sql = "SELECT 
                    n.nemo,s.nemodes,p.nomcomp AS tutor,
                    COUNT(*) AS total1,
                    (SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas nt1 INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON nt1.alucod=sa.alucod AND sa.ano='" . $this->ano . "' AND sa.estado='V' 
                    WHERE nt1.bimecod=$vbimestre AND nt1.unicod=$vunidad AND nt1.cursocod NOT IN (" . $vNotIn . ")  AND nt1.nemo=n.nemo AND $prom 
                    ) AS total2,
                    ROUND(((SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas nt2 INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON nt2.alucod=sa.alucod AND sa.ano='" . $this->ano . "' AND sa.estado='V' 
                    WHERE nt2.bimecod=$vbimestre AND nt2.unicod=$vunidad AND nt2.cursocod NOT IN (" . $vNotIn . ")  AND nt2.nemo=n.nemo AND $prom) * 100)/(
                    SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas AS nt
                    INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON nt.alucod=sa.alucod AND sa.ano='" . $this->ano . "' AND sa.estado='V'
                    WHERE nt.bimecod=$vbimestre AND nt.unicod=$vunidad AND nt.nemo=n.nemo AND nt.cursocod NOT IN (" . $vNotIn . ")
                    )) AS total3
                    FROM " . LIBRERIA2 . ".notas AS n
                    INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON n.alucod=sa.alucod AND sa.ano='" . $this->ano . "' AND sa.estado='V'
                    INNER JOIN " . LIBRERIA2 . ".salon AS s ON sa.nemo=s.nemo AND s.ano='" . $this->ano . "' 
                    INNER JOIN " . LIBRERIA2 . ".profe AS p ON s.profcod=p.profcod
                    WHERE n.bimecod=$vbimestre AND n.unicod=$vunidad 
                    AND n.nemo IN (SELECT nemo FROM  " . LIBRERIA2 . ".salon WHERE instrucod='$vnivel' AND ano='" . $this->ano . "' AND blkcal='1') 
                    AND n.cursocod NOT IN (" . $vNotIn . ")  
                    GROUP BY n.nemo ";

        $sql .= " ORDER BY n.nemo ";
        if ($_POST['length'] != -1)
            $sql .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'];

        $query = $this->db->query($sql);
       // echo $this->db->last_query(); exit;
        return $query->result();
    }

    public function getAll($vnivel = '') {
        if ($vnivel == 'I')
            $total = 10;
        if ($vnivel == 'P')
            $total = 27;
        if ($vnivel == 'S')
            $total = 23;
        return $total;
    }

    public function getDataTablesCurso($vnivel = '', $vnemo = '', $vbimestre = 0, $vunidad = 0) {
        $vNotIn = '';
        $prom = '';
        if ($vnivel == 'I') {
            $vNotIn = "'0'"; // INICIAL
            $prom = "ns.pf>0";
        }
        if ($vnivel == 'P') {
            $vNotIn = "'01','06'"; // PRIMARIA
            $prom = "ns.pb>0";
        }
        if ($vnivel == 'S') {
            $vNotIn = "'01',06,'15'"; // SECUNDARIA   
            $prom = "ns.pb>0";
        }
        $sql = "SELECT 
                    n.nemo,c.cursonom,p.nomcomp AS profe,
                    (
                    SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas AS nt
                    INNER JOIN " . LIBRERIA2 . ".salon_al AS sa1 ON nt.alucod=sa1.alucod AND sa1.ano='" . $this->ano . "' AND sa1.estado='V'
                    WHERE nt.bimecod=$vbimestre AND nt.unicod=$vunidad AND nt.nemo=n.nemo AND nt.cursocod=n.cursocod
                    ) AS total_alumno,
                    (SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas AS ns
                    WHERE ns.bimecod=$vbimestre AND ns.unicod=$vunidad AND ns.nemo=n.nemo AND ns.cursocod=n.cursocod
                     AND $prom ) AS llenado,
                     (COUNT(*)  - (SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas AS ns
                    WHERE ns.bimecod=$vbimestre AND ns.unicod=$vunidad AND ns.nemo=n.nemo AND ns.cursocod=n.cursocod
                     AND $prom )) AS falta,
                    ROUND(
                    ((
                    SELECT COUNT(*) FROM " . LIBRERIA2 . ".notas AS ns
                    WHERE ns.bimecod=$vbimestre AND ns.unicod=$vunidad AND ns.nemo=n.nemo AND ns.cursocod=n.cursocod
                     AND $prom
                    ) * 100 )/COUNT(*)) AS total3 
                    FROM " . LIBRERIA2 . ".notas AS n
                    INNER JOIN " . LIBRERIA2 . ".salon_al AS sa ON n.alucod=sa.alucod AND sa.ano='" . $this->ano . "' AND sa.estado='V'
                    INNER JOIN " . LIBRERIA2 . ".curso AS c ON n.cursocod=c.cursocod
                     INNER JOIN " . LIBRERIA2 . ".asignapr AS pr ON n.nemo=pr.nemo and n.cursocod=pr.cursocod
                     INNER JOIN " . LIBRERIA2 . ".profe AS p ON pr.profcod=p.profcod
                    WHERE n.bimecod=$vbimestre AND n.unicod=$vunidad
                    AND n.nemo='$vnemo'
                         AND n.cursocod NOT IN (" . $vNotIn . ")
                    GROUP BY n.cursocod ";

        $sql .= " ORDER BY n.nemo ";
        //if ($_POST['length'] != -1)
        //$sql .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'];

        $query = $this->db->query($sql);
        return $query->result();
    }

    function getListaNivel($vnivel = '') {
        $sql = "SELECT instrucod,instrudes FROM " . LIBRERIA2 . ".instru";
        if (!empty($vnivel))
            $sql .= " WHERE instrucod in ('" . $vnivel . "')";
        //echo $sql; exit;
        $query = $this->db->query($sql);
        $query = $query->result();
        return $query;
    }

    /*
      public function getAllCurso($vnemo = '') {
      $sql = "select count(*) as total from  " . LIBRERIA2 . ".asigna where nemo='$vnemo'";
      $query = $query->row();
      return $query->total;
      }
     */
	 
    function getNotaPRP($vnemo='', $vAlucod='',$vIdcurso='') {
        $sql =" SELECT alucod,pb  FROM   " . LIBRERIA2 . ".notas_recuperacion WHERE alucod='$vAlucod' AND nemo='$vnemo' AND cursocod='$vIdcurso'";
        //echo $sql; exit;
        $query = $this->db->query($sql);
        $query = $query->result();
		//print_r($query); exit;
        return $query;
    }
	
}
