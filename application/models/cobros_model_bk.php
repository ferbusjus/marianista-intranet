<?php

/**
 * @package       modules/alumno_model/model
 * @name            cobros_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 2017/08/30
 */
class cobros_model extends CI_Model {

    public $tabla = '';
    public $_session = '';
    public $ano = '';

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA . '.wp_cobro';
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    function deletePagoxAlumno($valucod = '', $vconcob = '', $vmescob = '') {
        try {
            $sql = " UPDATE " . $this->tabla . " "
                    . " SET montopen=montocob, montocob=0, estado='P', "
                    . " numrecibo=NULL , fecmod=NULL, usumod=NULL, "
                    . " tipo_comp='00', tipo_razon='R00'  "
                    . " WHERE alucod='" . $valucod . "' and concob='" . $vconcob . "' "
                    . " and mescob='" . $vmescob . "' and anocob='$this->ano'  ";
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

    public function getGeneraNumero($vruc = '', $vtipo = '01') {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_GENERA_CODPAGO('$vruc','$vtipo') ";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            $query = $query->result();
            $this->db->free_db_resource();
            return $query;
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

    public function getConceptos() {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_S_CONCEPTOS_ALL() ";
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

    function filtroAlumno($txtFiltro = '') {
        $vAno = $this->ano;
        $where = (($vAno == date("Y")) ? " A.FLG_MATRICULA=1 AND SA.ESTADO='V' AND " : " SA.ESTADO='V' AND ");
        $query = $this->db->query("SELECT A.ALUCOD, A.NOMCOMP,S.NEMODES,S.NEMO"
                . " FROM  " . LIBRERIA2 . ".alumno as A "
                . " INNER JOIN " . LIBRERIA2 . ".salon_al as SA on SA.ALUCOD=A.ALUCOD and SA.ANO='$vAno' "
                . "INNER JOIN " . LIBRERIA2 . ".salon as S on S.NEMO=SA.NEMO and S.ANO='$vAno'"
                . " WHERE  " . $where . " TRIM(A.NOMCOMP) LIKE '%" . trim($txtFiltro) . "%' LIMIT 0 ,20");
// echo $this->db->last_query(); exit;
        $query = $query->result();
        return $query;
    }

    function getUsuarios() {
        $query = $this->db->query("SELECT  usucod,apellidos FROM usuarios WHERE  id_perfil=2");
        $query = $query->result();
        return $query;
    }

    public function ValidaConceptoxAlumno($vidAlumno = '0', $vidConcepto = '0') {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_S_VALIDACONCEPT_X_ALUMNO('$vidAlumno', '$vidConcepto') ";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            $fila = $query->row();
            return $fila->total;
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

    public function grabaNuevoConcepto($arrData = array()) {
        try {
            /* $this->db->insert(LIBRERIA . '.wp_cobro', $arrData);
              echo $this->db->last_query(); exit;
              if ($this->db->affected_rows() != 1) {
              throw new Exception($this->db->_error_message());
              } else {
              return true;
              } */
            $anocob = $arrData['anocob'];
            $alucod = $arrData['alucod'];
            $concob = $arrData['concob'];
            $mescob = $arrData['mescob'];
            $montoini = $arrData['montoini'];
            $fecemi = $arrData['fecemi'];
            $fecven = $arrData['fecven'];
            $montocob = $arrData['montocob'];
            $moncod = $arrData['moncod'];
            $estado = $arrData['estado'];
            $montopen = $arrData['montopen'];
            $fecreg = $arrData['fecreg'];
            $usureg = $arrData['usureg'];
            $orden = $arrData['orden'];
            $tipopago = $arrData['tipopago'];

            $sql = "INSERT INTO fercmias_sistemasdev.wp_cobro (anocob, alucod, concob, mescob, montoini, fecemi, fecven, "
                    . "montocob, moncod, estado, montopen, fecreg, usureg, orden, tipopago) VALUES ('$anocob','$alucod','$concob',"
                    . "'$mescob',$montoini,'$fecemi','$fecven',$montocob,'$moncod','$estado',$montopen,'$fecreg','$usureg',"
                    . "$orden,'$tipopago')";
            //echo $sql; exit;
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

    public function grabaNuevoConceptoAdicional($arrData = array()) {
        try {
            $query = $this->db->insert('wp_cobro_adicional', $arrData);
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

    public function getPagosOtros($vfecha = '0000-00-00', $ano = '', $vusuario = '', $vcomp = '') {
        try {
            $sql = "SELECT 
	c.id AS alucod,
	c.nomcomp,
	'ADI' AS ngs,
	cc.concob,
	cc.condes,
	c.tipopago,
	c.montocob,
	m.monsig,
	c.fecmod,
	c.usumod,
	s.mesdes,
                       c.numrecibo
                FROM                                
                        wp_cobro_adicional AS c 
                INNER JOIN 
                        wp_concobro AS cc ON cc.concob = c.concob
                INNER JOIN 
                        wp_moneda AS m ON m.moncod=c.moncod
                INNER JOIN 
                        wp_meses AS s ON s.mescod=c.mescob
                WHERE 
	fecreg='$vfecha' and c.anocob='$ano' ";
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

    public function getPagoxMesCompleto($vnivel = 'T', $vmes = '', $ano = '') {
        try {

            $sql = "SELECT 
                                a.dni,
                                a.alucod,
                                a.nomcomp,
                                CONCAT(a.instrucod, a.gradocod, a.seccioncod) AS ngs,
                                cc.concob,
                                cc.condes,
                                c.tipopago,
                                date(c.fecmod) as fecha,
                                c.usumod as usuario,
                                s.mesdes,
                                sl.nemodes
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='" . $ano . "'
                         INNER JOIN 
                                fercmias_academico.salon as sl on sl.nemo=sa.nemo and sl.ano='" . $ano . "'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='" . $ano . "'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob                                
                        WHERE 
                                c.estado='C'  
                                AND c.concob='01'                                
                                AND c.mescob ='$vmes' 
                                AND sa.estado='V' ";
            if ($vnivel == 'T') {
                $sql .= " AND a.instrucod IN ('I','P','S') ";
            } else {
                $sql .= " AND a.instrucod = '$vnivel' ";
            }
            $sql .= " ORDER BY a.instrucod, a.gradocod, a.seccioncod,a.nomcomp ";
            //           echo "Query : ".$sql."<br>";
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

    public function getPendientesxAlumno($valucod = 'T', $vmes = '') {
        try {
            $sql = "SELECT  UPPER(m.mesdes) AS periodo ,c.fecven,c.montopen,20.00 AS mora,(c.montopen+20) AS total
                        FROM fercmias_sistemasdev.wp_cobro AS c JOIN fercmias_sistemasdev.wp_meses AS m ON m.mescod=c.mescob
                        WHERE c.alucod='" . $valucod . "' AND  c.concob='01' AND c.mescob <='" . $vmes . "'  AND c.montopen>0 ";
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

    public function getPagoxperiodo($vnivel = 'T', $vmes = '', $ano = '') {
        try {
            $sql = "SELECT a.alucod,a.dni,a.nomcomp,a.gradocod,a.instrucod ";
            for ($x = 3; $x <= (int) $vmes; $x++) {
                $strmes = (($x < 10) ? '0' . $x : $x);
                $sql .= " ,(SELECT montopen FROM fercmias_sistemasdev.wp_cobro WHERE alucod=a.alucod AND concob='01' AND anocob='$ano' AND mescob='" . $strmes . "') AS mes_" . $x;
            }
            $sql .= ", s.nemodes ";
            $sql .= " FROM fercmias_academico.alumno AS a
                    INNER JOIN fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod
                    INNER JOIN fercmias_sistemasdev.sga_matricula AS m ON m.aluant=c.alucod
                    INNER JOIN fercmias_academico.salon_al AS sa ON sa.alucod=a.alucod AND sa.ano='$ano'
                    INNER JOIN fercmias_academico.salon AS s ON s.nemo=sa.nemo and s.ano='$ano'
                    WHERE  m.estado='M' AND m.flgbeca=0 AND c.anocob='$ano'   ";
            if ($vnivel == 'T') {
                $sql .= " and s.instrucod in ('I','P','S')";
            } else {
                $sql .= " and s.instrucod='" . $vnivel . "'";
            }
            $sql .= " AND c.mescob <='" . $vmes . "'  AND concob='01'
                    GROUP BY a.alucod
                    HAVING SUM(c.montopen) >0
                    ORDER BY a.instrucod,a.gradocod,a.seccioncod,a.nomcomp";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            return $query->result_array();
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

    public function getPagoxNivel($vnivel = 'T', $vfecha = '0000-00-00', $ano = '', $vusuario = '', $vcomp = '') {
        try {
            //$sql = "CALL " . LIBRERIA . ".SP_S_REPORTE_CAJA_DIARIO('$vnivel', '$vfecha') ";
            $sql = "SELECT 
                                a.alucod,
                                a.nomcomp,
                                CONCAT(a.instrucod, a.gradocod, a.seccioncod) AS ngs,
                                cc.concob,
                                cc.condes,
                                c.tipopago,
                                c.montocob,
                                m.monsig,
                                c.fecmod,
                                c.usumod,
                                s.mesdes,
                                c.numrecibo
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='" . $ano . "'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='" . $ano . "'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) ='$vfecha' 
                                AND a.instrucod = '$vnivel'
                                AND sa.estado='V' ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }
            //echo "Query : ".$sql."<br>";
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

    public function getPagoAllAdicionales() {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_S_COBROS_ADICIONAL_ALL() ";
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

    public function updatePensionCarga($varrData = array(), $valucod = '', $vmescob = '') {
        try {
            $this->db->where('concob', '01');
            $this->db->where('mescob', $vmescob);
            $this->db->where('alucod', $valucod);
            $query = $this->db->update(LIBRERIA . ".wp_cobro", $varrData);
            //echo $this->db->last_query(); exit;
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

    public function getPagoxAlumnoxMes($vIdAlumno = '', $vMes = 0) {
        try {
            $vMes = (($vMes < 10) ? '0' . $vMes : $vMes);
            $sql = "select count(*) as total from " . LIBRERIA . ".wp_cobro where alucod='$vIdAlumno' and mescob='$vMes' and concob='01' and estado='C' ";
            //echo $sql; exit; 
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

    public function getPagoxAlumno($vIdAlumno = 0, $vFlag = 0) {
        try {
            if ($vFlag == 1) {
                $vano = ($this->ano - 1);
            } else {
                $vano = $this->ano;
            }
            $sql = "CALL " . LIBRERIA . ".SP_S_COBROS_X_ALUMNO('$vIdAlumno','$vano') ";
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

    public function getDatoUsuario($vUsuario = '') {
        try {
            $sql = " SELECT apellidos AS nomcomp,usucod FROM  " . LIBRERIA . ".usuarios where usucod='$vUsuario' ";
            $query = $this->db->query($sql);
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

    public function getPagoxAlumnoRec($vRecibo = '', $valucod = '') {
        try {
            $sql = " SELECT * FROM  " . LIBRERIA . ".wp_cobro where numrecibo='$vRecibo' and alucod='$valucod' and anocob='" . $this->ano . "'";
            //echo $sql;  exit;
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

    public function getPagoEnvioBanco($vtipo = '01', $vtipoPago = '01') {
        try {
            $sql = "CALL " . LIBRERIA2 . ".SP_S_PAGOS_REALIZADOS('$vtipo','$vtipoPago') ";
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

    public function getPagoxId($vIdAlumno = 0, $vsqlMescobIn = '', $vsqlConcobIn = '') {
        try {
            $vano = $this->ano;
            $sql = "CALL " . LIBRERIA . ".SP_S_COBROS_X_IDS('$vIdAlumno','$vsqlMescobIn','$vsqlConcobIn',$vano) ";
            $query = $this->db->query($sql);
            //  echo $this->db->last_query(); exit;
            if (!$query)
                throw new Exception($this->db->_error_message());
            $query = $query->result();
            $this->db->free_db_resource();
            return $query;
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

    public function grabarPension($vIdAlu = 0, $vIdMes = '', $vIdCobro = '', $vnumrec = '', $vnmonto = 0, $vcbtipo = 'C', $vfecha = '', $vcomp = '', $vruc = '', $flag = 0) {
        try {
            $vusu = $this->_session['USUCOD'];
            //$vusu = $this->session->userdata ('USUCOD');
            $ano = $this->ano;
            $vfecreg = (($vfecha == '') ? '' : $vfecha); //date ("Y-m-d");
            $sql = "CALL " . LIBRERIA . ".SP_U_PAGO_CAJA('$vIdAlu','$vIdMes','$vIdCobro','$vnumrec','$vusu','$vfecreg', '$vnmonto','$vcbtipo','$vcomp','$vruc','$ano',$flag) ";
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

    public function getDatoReporte($vtipo = 0, $vfecha = '0000-00-00', $ano = '') {
        if ($vtipo == 1) { //Obtengo el numero minimo y maximo de recibos en ese dia
            $sql = "SELECT MIN(numrecibo) as minimo , MAX(numrecibo) as maximo FROM wp_cobro WHERE estado='C'  AND date(fecmod)='$vfecha' and anocob='$ano' ";
        } elseif ($vtipo == 2) { //Obtengo monto total cobrado por caja en ese dia
            $sql = "SELECT SUM(montocob) as monto FROM wp_cobro WHERE estado='C' AND tipopago='C' AND date(fecmod)='$vfecha' and anocob='$ano' ";
        } elseif ($vtipo == 3) { //Obtengo monto total cobrado por banco en ese dia
            $sql = "SELECT SUM(montocob) as monto FROM wp_cobro WHERE estado='C' AND tipopago='B' AND date(fecmod)='$vfecha' and anocob='$ano' ";
        } elseif ($vtipo == 4) { //Obtengo monto total de los egresos en ese dia
            $sql = "SELECT SUM(monto) as monto FROM wp_egresos WHERE DATE(fecreg)='$vfecha' AND flgactivo=1 ";
        }
        $query = $this->db->query($sql);
        return $query->result();
    }

}
