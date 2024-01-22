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
    var $column_order = array('p.id', 'p.nomcomp');
    public $order = array('p.id' => 'desc'); 
    public $column_search = array('p.nomcomp');

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA . '.wp_cobro';
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    function deletePagoAdicional($vid = '') {
        try {
            $sql = " DELETE FROM " . LIBRERIA . ".wp_cobro_adicional WHERE id='" . $vid . "' ";
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

    public function graba_control($arrData = array()) {
        $query = $this->db->insert("wp_cobro_control", $arrData);
        if (!$query)
            throw new Exception($this->db->_error_message());
        return true;
    }

    public function actualiza_control($arrdata = array(), $vIdrecibo = 0) {
        $this->db->where('numrecibo', $vIdrecibo);
        $query = $this->db->update("wp_cobro_control", $arrdata);
        if (!$query)
            throw new Exception($this->db->_error_message());
        return TRUE;
    }

    public function getGeneraNumero($vruc = '', $vtipo = '01', $tipoRazon = '') {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_GENERA_CODPAGO('$vruc','$vtipo','$tipoRazon') ";
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

    public function getConceptos($idtipo = 0) {
        try {
            $sql = "CALL " . LIBRERIA . ".SP_S_CONCEPTOS_ALL($idtipo) ";
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

    public function getConceptosxComprobante($idcomprobante = 0, $vrazon = '') {
        try {
            $sql = "SELECT 
                                p.id,
                                p.apepat,
                                p.apemat,
                                p.nombres,
                                p.nomcomp,
                                p.fecemi,
                                n.monsig, 
                                p.monto,
                                c.desconcepto,
                                p.moncod,
                                n.monnom,
                                n.monsig,
                                p.estado,
                                p.numrecibo,
                                p.fecreg,
                                p.usumod,
                                p.tipopago,
                                p.tipo_comp,
                                p.tipo_razon,
                                p.usureg,
                                p.fecreg
                        FROM 
                                wp_cobro_adicional AS p
                        INNER JOIN 
                                wp_concepto AS c ON  c.idtipo=p.idtipo AND c.idconcepto=p.idconcepto
                        INNER JOIN 
                                wp_moneda AS n ON n.moncod = p.moncod
                        WHERE p.numrecibo='$idcomprobante' and p.tipo_razon='$vrazon'";

            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            $query = $query->result();
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

    public function getConceptosxId($idconcepto = 0) {
        try {
            $sql = " select desconcepto from wp_concepto where idconcepto=" . $idconcepto;
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            $query = $query->row();
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

    function getBoletasxAlumno($alucod = '') {
        $query = $this->db->query("SELECT c.alucod,c.numrecibo,c.fecmod,m.nemo,tipo_comp,sum(montocob) as monto
                                                        FROM   wp_cobro AS c
                                                        INNER JOIN sga_matricula AS m ON m.aluant=c.alucod AND m.periodo='" . $this->ano . "'
                                                        WHERE c.alucod='$alucod' AND c.estado='C' AND c.anocob='" . $this->ano . "'  GROUP BY c.numrecibo
                                                        ORDER BY c.orden, c.numrecibo");
        $query = $query->result();
        return $query;
    }

    function filtroAlumno($txtFiltro = '') {
        $vAno = $this->ano;
        //echo $vAno."-".date("Y");
        //  exit;
        $where = (($vAno == '2020') ? " /*A.FLG_MATRICULA=1 AND*/ SA.ESTADO IN ('V','E','R')  AND " : " SA.ESTADO IN('V','P','E') AND "); //(SA.ESTADO='V'  OR SA.ESTADO='E')
        $query = $this->db->query("SELECT A.ALUCOD, A.NOMCOMP,S.NEMODES,S.NEMO,SA.ESTADO"
                . " FROM  " . LIBRERIA2 . ".alumno as A "
                . " INNER JOIN " . LIBRERIA2 . ".salon_al as SA on SA.ALUCOD=A.ALUCOD and SA.ANO='$vAno' "
                . "INNER JOIN " . LIBRERIA2 . ".salon as S on S.NEMO=SA.NEMO and S.ANO='$vAno'"
                . " WHERE  " . $where . " TRIM(A.NOMCOMP) LIKE '%" . trim($txtFiltro) . "%' LIMIT 0 ,20");
     //   echo $this->db->last_query(); exit;
        $query = $query->result();
        return $query;
    }

    function filtroAlumnoAll($txtFiltro = '') {
        $vAno = "2019";
        $query = $this->db->query("SELECT A.ALUCOD,A.DNI, A.APEPAT,A.APEMAT,A.NOMBRES, A.NOMCOMP, S.NEMODES, S.NEMO"
                . " FROM  " . LIBRERIA2 . ".alumno as A "
                . " INNER JOIN " . LIBRERIA2 . ".salon_al as SA on SA.ALUCOD=A.ALUCOD  "
                . " INNER JOIN " . LIBRERIA2 . ".salon as S on S.NEMO=SA.NEMO "
                . " WHERE  A.ESTADO IN('V','P','R','A') AND TRIM(A.NOMCOMP) LIKE '%" . trim($txtFiltro) . "%' "
                . "  AND S.NEMO=(SELECT NEMO FROM " . LIBRERIA2 . ".salon_al WHERE alucod= A.ALUCOD ORDER BY nemo DESC LIMIT 1) ORDER BY A.NOMCOMP LIMIT 0 ,20");
        //echo $this->db->last_query(); exit;
        $query = $query->result();
        return $query;
    }

    function getUsuarios() {
        $query = $this->db->query("SELECT  usucod,apellidos FROM usuarios WHERE  id_perfil in (2,7,8) -- and estatus=1");
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

    public function getPagosOtros($vfecha = '0000-00-00', $ano = '', $vusuario = '', $vcomp = '', $razon='') {
        try {
            $sql = "SELECT 
                                c.alucod,
                                c.alucod as dni,
                                c.nomcomp,
                                'ADI' AS ngs,
                                '01' AS concob,
                                cc.desconcepto AS condes,
                                c.tipopago,
                                c.monto AS montocob,
                                'S/.' AS monsig,
                                c.fecreg AS fecmod,
                                c.usureg as usumod,
                                c.numrecibo,
                                c.anocob
                        FROM fercmias_sistemasdev.wp_cobro_adicional AS c
                       INNER JOIN  fercmias_sistemasdev.wp_concepto AS cc ON cc.idconcepto=c.idconcepto AND cc.idtipo=c.idtipo
                       WHERE  DATE(c.fecreg) ='$vfecha' /*and c.anocob='$ano'*/ and tipo_razon='$razon' ";

            if ($vusuario != 'T') {
                $sql .= " AND c.usureg='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }
            $sql .= " ORDER BY  c.numrecibo ";

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

        public function getPagoDeudores($vnivel = 'T', $vmes = '', $ano = '') {
        try {
            $sql = "SELECT a.dni,a.nomcomp as alumno ,s.nemodes as salon "
                    . " ,CONCAT(a.padpater,' ',a.padmater,', ',a.padnom) as padre "
                    . " ,IF(LENGTH(TRIM(a.padcelu))=0,a.padcelu2,IF(LENGTH(a.padcelu2)>0,IF(a.padcelu=a.padcelu2,a.padcelu,CONCAT(a.padcelu,' - ',a.padcelu2)),a.padcelu)) as padcelu "
                    . ", CONCAT(a.madpater,' ',a.madmater,', ',a.madnom) as madre"
                    . " , IF(LENGTH(TRIM(a.madcelu))=0,a.madcelu2,IF(LENGTH(a.madcelu2)>0,IF(a.madcelu=a.madcelu2,a.madcelu,CONCAT(a.madcelu,' - ',a.madcelu2)),a.madcelu)) as madcelu"
                    . " , CONCAT(a.apopater,' ',a.apomater,', ',a.aponom)  as apoderado "
                    . ",IF(LENGTH(TRIM(a.apocelu))=0,a.apocelu2,IF(LENGTH(a.apocelu2)>0,CONCAT(a.apocelu,' - ',a.apocelu2),a.apocelu)) as apocelu";
            for ($x = 3; $x <= (int) $vmes; $x++) {
                $strmes = (($x < 10) ? '0' . $x : $x);
                //if($x==3)
                //  $sql .= ", '0.00' AS mes_" . $x; // Se agrega bloque temporal para evitar mostrar el monto de marzo
                //else
                $sql .= " ,(SELECT montopen FROM fercmias_sistemasdev.wp_cobro WHERE alucod=a.alucod AND concob='01' AND flgexonera=0 AND anocob='$ano' AND mescob='" . $strmes . "') AS mes_" . $x;
            }
            $cadIn ="";
            for ($x = 3; $x <= (int) $vmes; $x++) {
                $cadIn .="'". (($x < 10) ? '0' . $x : $x) ."',";
            }
            if($cadIn!="") $cadIn = substr ($cadIn, 0, -1);
            $sql .= ",(SELECT SUM(montopen) FROM fercmias_sistemasdev.wp_cobro  WHERE alucod =a.alucod AND anocob='$ano'  AND concob='01' AND mescob IN ($cadIn) AND flgexonera=0  ) AS total ";
            $sql .= " FROM fercmias_academico.alumno AS a
                    INNER JOIN fercmias_academico.salon_al AS sa ON sa.alucod=a.alucod AND sa.ano='$ano'
                    INNER JOIN fercmias_academico.salon AS s ON s.nemo=sa.nemo and s.ano='$ano'
                    WHERE 1=1 ";
            if ($vnivel == 'T') {
                $sql .= " and s.instrucod in ('I','P','S')";
            } else {
                $sql .= " and s.instrucod='" . $vnivel . "'";
            }
            $sql .= " and sa.estado='V' ";
                  
             $sql .= " HAVING total>0
                    ORDER BY a.instrucod,a.gradocod,a.seccioncod,a.nomcomp";
            // Se agrega AND c.mescob >'03' para que contabilice mayor a marzo
            /* $sql .= " AND c.mescob <='" . $vmes . "'  AND concob='01'
              GROUP BY a.alucod
              HAVING SUM(c.montopen) >0
              ORDER BY a.instrucod,a.gradocod,a.seccioncod,a.nomcomp"; */
            //echo $sql; exit;
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
    
    public function getPagoxperiodo($vnivel = 'T', $vmes = '', $ano = '') {
        try {
            $sql = "SELECT a.alucod,a.dni,a.nomcomp,a.gradocod,a.instrucod ";
            for ($x = 3; $x <= (int) $vmes; $x++) {
                $strmes = (($x < 10) ? '0' . $x : $x);
                //if($x==3)
                //  $sql .= ", '0.00' AS mes_" . $x; // Se agrega bloque temporal para evitar mostrar el monto de marzo
                //else
                $sql .= " ,(SELECT IF(flgexonera=1,0,montopen) as montopen FROM fercmias_sistemasdev.wp_cobro WHERE alucod=a.alucod AND concob='01' AND anocob='$ano' AND mescob='" . $strmes . "') AS mes_" . $x;
            }
            $sql .= ", s.nemodes ";
            $sql .= " FROM fercmias_academico.alumno AS a
                    INNER JOIN fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod AND c.anocob='$ano'
                    INNER JOIN fercmias_sistemasdev.sga_matricula AS m ON m.aluant=c.alucod AND m.periodo='$ano'
                    INNER JOIN fercmias_academico.salon_al AS sa ON sa.alucod=a.alucod AND sa.ano='$ano'
                    INNER JOIN fercmias_academico.salon AS s ON s.nemo=sa.nemo and s.ano='$ano'
                    WHERE  sa.estado='V' and  a.alucod!='20170859'  /*m.estado='M' and sa.estado='V' AND*/ AND m.flgbeca=0 AND c.anocob='$ano'   ";
            if ($vnivel == 'T') {
                $sql .= " and s.instrucod in ('I','P','S')";
            } else {
                $sql .= " and s.instrucod='" . $vnivel . "'";
            }
            $sql .= " AND c.mescob >='03' AND c.mescob <='" . $vmes . "'  AND concob='01' AND c.flgexonera=0
                    GROUP BY a.alucod
                    HAVING SUM(c.montopen) >0
                    ORDER BY a.instrucod,a.gradocod,a.seccioncod,a.nomcomp";
            // Se agrega AND c.mescob >'03' para que contabilice mayor a marzo
            /* $sql .= " AND c.mescob <='" . $vmes . "'  AND concob='01'
              GROUP BY a.alucod
              HAVING SUM(c.montopen) >0
              ORDER BY a.instrucod,a.gradocod,a.seccioncod,a.nomcomp"; */
            //echo $sql; exit;
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

    public function getPagoxRazon($vfecha = '0000-00-00', $vfechaf = '0000-00-00', $vrazon = '', $vano = '', $vusuario = '', $vTipoComp = '') {
        try {
            $sql = "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2018'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2018'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                                /*AND c.tipo_razon= '$vrazon'*/
                                /*AND sa.estado='V'*/ ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }

            $sql .= " UNION ALL  ";

            $sql .= "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2019'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2019'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                               /*AND c.tipo_razon= '$vrazon'*/
                               /* AND sa.estado='V'*/ ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }

            $sql .= " UNION ALL  ";

            $sql .= "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2020'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2020'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                                /*AND c.tipo_razon= '$vrazon'*/
                              /*  AND sa.estado='V' */ ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }

            $sql .= " UNION ALL  ";

            $sql .= "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2021'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2021'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                                /*AND c.tipo_razon= '$vrazon'*/
                                AND sa.estado='V' ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }
            
            $sql .= " UNION ALL  ";

            $sql .= "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2022'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2022'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                                /*AND c.tipo_razon= '$vrazon'*/
                                AND sa.estado='V' ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }
         
            $sql .= " UNION ALL  ";

            $sql .= "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2023'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2023'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                                /*AND c.tipo_razon= '$vrazon'*/
                                AND sa.estado='V' ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }
            
           $sql .= " UNION ALL  ";

            $sql .= "SELECT 
                                a.alucod,
                                a.dni,
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
                                c.numrecibo,
                                c.anocob,
                                c.tipo_razon,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod  and sa.ano='2024'
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod  and c.anocob='2024'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(fecmod) between '$vfecha' and '$vfechaf' 
                                /*AND c.tipo_razon= '$vrazon'*/
                                AND sa.estado='V' ";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }
            
            $sql .= " ORDER BY  tipo_razon, numrecibo, nomcomp ";
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

    public function getPagoxRazonAdicional($vfecha = '0000-00-00', $vfechaf = '0000-00-00', $vrazon = '', $vano = '', $vusuario = '', $vTipoComp = '', $vflg = 0) {
        try { //IF(c.externo=1, c.alucod, c.dni)
            $sql = "SELECT 
                                c.alucod,
                                c.alucod as dni,
                                c.nomcomp,
                                'NNN' AS ngs,
                                '01' AS concob,
                                cc.desconcepto AS condes,
                                c.tipopago,
                                c.monto AS montocob,
                                'S/.' AS monsig,
                                c.fecreg AS fecmod,
                                c.usureg as usumod,
                                c.numrecibo,
                                c.anocob,
                                c.idconcepto,
                                c.tipo_razon
                        FROM fercmias_sistemasdev.wp_cobro_adicional AS c
                       INNER JOIN  fercmias_sistemasdev.wp_concepto AS cc ON cc.idconcepto=c.idconcepto AND cc.idtipo=c.idtipo
                       WHERE  DATE(c.fecreg) BETWEEN '$vfecha' and '$vfechaf' /*AND c.tipo_razon= '$vrazon'*/";

            if ($vrazon != 'T') {
                $sql .= " AND c.tipo_razon='" . $vrazon . "'";
            }
            if ($vusuario != 'T') {
                $sql .= " AND c.usureg='" . $vusuario . "'";
            }
            if ($vTipoComp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vTipoComp . "'";
            }
            if ($vflg == '1') {
                $sql .= " AND c.idconcepto=30 ";
            }
            //$sql .= "  AND c.anocob='$vano'";
            $sql .= " ORDER BY  c.tipo_razon,c.numrecibo ";

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

    public function getPagoxNivel($vnivel = 'T', $vfecha = '0000-00-00', $ano = '', $vusuario = '', $vcomp = '', $razon = '') {
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2018'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2018'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                /*AND sa.estado='V'*/ ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }

            $sql .= " UNION ALL ";

            $sql .= "SELECT 
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2019'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2019'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                /*AND sa.estado='V'*/ ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }

            $sql .= " UNION ALL ";

            $sql .= "SELECT 
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2020'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2020'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                /*AND sa.estado='V'*/ ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }
            
           $sql .= " UNION ALL ";

            $sql .= "SELECT 
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2021'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2021'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                AND sa.estado='V' ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }            
            
           $sql .= " UNION ALL ";

            $sql .= "SELECT 
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2022'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2022'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                AND sa.estado='V' ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }         
            
           $sql .= " UNION ALL ";

            $sql .= "SELECT 
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2023'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2023'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                AND sa.estado='V' ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }           
            
           $sql .= " UNION ALL ";

            $sql .= "SELECT 
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
                                c.numrecibo,
                                c.anocob,
                                c.flgexonera
                        FROM 
                                fercmias_academico.alumno AS a
                         INNER JOIN 
                                fercmias_academico.salon_al as sa on sa.alucod=a.alucod and sa.ano='2024'
                         INNER JOIN 
                                fercmias_academico.salon AS sl ON sl.nemo=sa.nemo                                  
                        INNER JOIN 	
                                fercmias_sistemasdev.wp_cobro AS c ON c.alucod=a.alucod and c.anocob='2024'
                        INNER JOIN 
                                fercmias_sistemasdev.wp_concobro AS cc ON cc.concob = c.concob
                        INNER JOIN 
                                fercmias_sistemasdev.wp_moneda AS m ON m.moncod=c.moncod
                        INNER JOIN 
                                fercmias_sistemasdev.wp_meses AS s ON s.mescod=c.mescob
                        WHERE 
                                c.estado='C'  
                                AND date(c.fecmod) ='$vfecha' 
                                AND sl.instrucod = '$vnivel'
                                AND c.tipo_razon='$razon'
                                AND sa.estado='V' ";
            if ($vusuario != 'T') {
                $sql .= " AND c.usumod='" . $vusuario . "'";
            }
            if ($vcomp != 'T') {
                $sql .= " AND c.tipo_comp='" . $vcomp . "'";
            }              
            $sql .= " ORDER BY  numrecibo,nomcomp ";
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
            $data   = array();
            $vano = $this->ano;
            $sql = "CALL " . LIBRERIA . ".SP_S_COBROS_ADICIONAL_ALL('$vano') ";
            $query = $this->db->query($sql);
            if (!$query)
                throw new Exception($this->db->_error_message());
            $data = $query->result();
            $query->free_result();
            $query->next_result();
            return $data;
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

    public function _get_datatables_query() {
        $vano = $this->ano;
        $this->db->select("p.id,
                                    p.nomcomp,
                                    p.fecemi,
                                    n.monsig, 
                                    p.monto,
                                    c.desconcepto,
                                    p.moncod,
                                    n.monnom,
                                    n.monsig,
                                    p.estado,
                                    p.numrecibo,
                                    p.fecreg,
                                    p.usumod,
                                    p.tipopago,
                                    p.tipo_razon,
                                    p.eliminado");
        $this->db->from(LIBRERIA . ".wp_cobro_adicional as p");
        $this->db->join(LIBRERIA . '.wp_concepto as c', 'c.idtipo=p.idtipo AND c.idconcepto=p.idconcepto');
        $this->db->join(LIBRERIA . '.wp_moneda as n', 'n.moncod = p.moncod');
        $this->db->where("p.anocob", $vano);
        
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
        //echo "<pre>"; print_r($query->result ()); echo "</pre>"; exit;
        /* echo "Query:" . $this->db->last_query ();
          exit; */
        return $query->result();
    }
    
    public function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        try{
        $vano = $this->ano;
        $this->db->select("p.id,
                                    p.nomcomp,
                                    p.fecemi,
                                    n.monsig, 
                                    p.monto,
                                    c.desconcepto,
                                    p.moncod,
                                    n.monnom,
                                    n.monsig,
                                    p.estado,
                                    p.numrecibo,
                                    p.fecreg,
                                    p.usumod,
                                    p.tipopago,
                                    p.tipo_razon,
                                    p.eliminado");
        $this->db->from(LIBRERIA . ".wp_cobro_adicional as p");
        $this->db->join(LIBRERIA . '.wp_concepto as c', 'c.idtipo=p.idtipo AND c.idconcepto=p.idconcepto');
        $this->db->join(LIBRERIA . '.wp_moneda as n', 'n.moncod = p.moncod');
        $this->db->where("p.anocob", $vano);
      //    echo $this->db->last_query(); exit;
         if (!$this->db->count_all_results())
                throw new Exception($this->db->_error_message());          
         return $this->db->count_all_results();
          } catch (Exception $e) {
             echo  "Error encontrado : ".$e->getMessage();    
          }
    }

    public function updatePensionCarga($varrData = array(), $valucod = '', $vmescob = '', $idconcepto = '01', $ano = '') {
        try {
            if ($ano == '') {
                $vano = $this->ano;
            } else {
                $vano = $ano;
            }
            $this->db->where('concob', $idconcepto);
            $this->db->where('mescob', $vmescob);
            $this->db->where('alucod', $valucod);
            $this->db->where('anocob', $vano);
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
            return false;
        }
    }

    public function getPagoxAlumnoxMes($vIdAlumno = '', $vMes = 0) {
        try {
            $vMes = (($vMes < 10) ? '0' . $vMes : $vMes);
            $sql = "select count(*) as total from " . LIBRERIA . ".wp_cobro where alucod='$vIdAlumno' and mescob='$vMes' and concob='01' and estado='C'  and anocob=" . ANO_VIG;
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

    public function getPagoxAlumnoMatricula($vIdAlumno = 0, $vFlag = 0) {
        try {
            if ($vFlag == 1) {
                $vano = ($this->ano - 1);
            } else {
                $vano = $this->ano;
            }
            $sql = "CALL " . LIBRERIA . ".SP_S_COBROS_X_ALUMNO_MAT('$vIdAlumno','$vano')";
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
            $sql = " SELECT apellidos AS nomcomp,usucod FROM  " . LIBRERIA . ".usuarios where usucod='$vUsuario'";
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

    public function getDatoUsuarioPago($vBoleta = '0', $vTipoRazon = '') {
        try {
            $sql = " SELECT usumod FROM  " . LIBRERIA . ".wp_cobro where numrecibo='$vBoleta' and anocob=" . $this->ano . " and tipo_razon='$vTipoRazon'";
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

    public function getDatoUsuarioPagoApp($vBoleta = '0', $vTipoRazon = '',$anio=2020) {
        try {
            $sql = " SELECT usumod FROM  " . LIBRERIA . ".wp_cobro where numrecibo='$vBoleta' and anocob=$anio and tipo_razon='$vTipoRazon'";
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

    public function getPagoxAlumnoRecApp($vRecibo = '', $valucod = '',$anio=2020) {
        try {
            $sql = " SELECT * FROM  " . LIBRERIA . ".wp_cobro where numrecibo='$vRecibo' and alucod='$valucod' and anocob=$anio";
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

    public function getPagoEnvioBancoBCP($vtipoPago = '01',  $razon ='', $periodo = '', $fini = '', $ffin = '') {
        try {
             $vano = $this->ano;
            $sql = "CALL " . LIBRERIA . ".SP_S_GENERACION_ENVIO_BCP('$vtipoPago','R$razon','$periodo','$fini','$ffin','$vano') ";
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

    public function grabarPension($vIdAlu = 0, $vIdMes = '', $vIdCobro = '', $vnumrec = '', $vnmonto = 0, $vcbtipo = 'C', $vfecha = '', $vcomp = '', $vruc = '', $flag = 0, $voucher='', $medioPago=0) {
        try {
            $vusu = $this->_session['USUCOD'];
            //$vusu = $this->session->userdata ('USUCOD');
            $ano = $this->ano;
            $vfecreg = (($vfecha == '') ? '' : $vfecha); //date ("Y-m-d");
            $sql = "CALL " . LIBRERIA . ".SP_U_PAGO_CAJA('$vIdAlu','$vIdMes','$vIdCobro','$vnumrec','$vusu','$vfecreg', '$vnmonto','$vcbtipo','$vcomp','$vruc','$ano',$flag,'$voucher',$medioPago) ";
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

    public function grabarPensionExo($vIdAlu = 0, $vIdMes = '', $vIdCobro = '', $vnumrec = '', $vnmonto = 0, $vcbtipo = 'C', $vfecha = '', $vcomp = '', $vruc = '', $flag = 0, $voucher='', $medioPago=0) {
        try {
            $vusu = $this->_session['USUCOD'];
            //$vusu = $this->session->userdata ('USUCOD');
            $ano = $this->ano;
            $vfecreg = (($vfecha == '') ? '' : $vfecha); //date ("Y-m-d");
            $sql = "CALL " . LIBRERIA . ".SP_U_PAGO_CAJA_EXO('$vIdAlu','$vIdMes','$vIdCobro','$vnumrec','$vusu','$vfecreg', '$vnmonto','$vcbtipo','$vcomp','$vruc','$ano',$flag,'$voucher',$medioPago) ";
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
    
    function getpagoAlumno($alucod = '', $vmes = '', $anio=0) { // considerar los exonerados
        $sql = "SELECT count(*) as total from " . LIBRERIA . ".wp_cobro where alucod='$alucod' and anocob='$anio' and mescob='$vmes' and estado='P' and concob='01'";
        $query = $this->db->query($sql);
        if (!$query) {
            false;
            //throw new Exception($this->db->_error_message());
        } else {
            $query = $query->row();
            return ($query->total > 0) ? true : false;
        }
    }

    function generadorCodigo($vtable = '', $caracter = '', $numceros = 4) {
        try {
            $sql = "SELECT " . LIBRERIA2 . ".FU_GENERA_CODIGO('$vtable','$caracter',$numceros) AS CODIGO";
            $query = $this->db->query($sql);
            if (!$query) {
                throw new Exception($this->db->_error_message());
            } else {
                $query = $query->row();
                return $query->CODIGO;
            }
        } catch (Exception $e) {
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

    public function getPagosviewBoleta($alucod = '', $ano = 2020) {
        $sql = "SELECT mescob,montopen FROM wp_cobro WHERE alucod='$alucod' AND anocob=$ano AND concob='01' AND mescob IN ('03','04','05','06','07') AND flgexonera=0 AND estado='P'";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTotalPagos($alucod = '', $ano = 2020) {
        $sql = "SELECT 
                    (COUNT(*) + (SELECT COUNT(*) FROM wp_cobro WHERE alucod='$alucod' AND anocob=$ano AND concob='01' AND flgexonera=1  )) AS total
                    FROM wp_cobro WHERE alucod='$alucod' AND anocob=$ano AND concob='01' AND estado='C'";
        $query = $this->db->query($sql);
        $data = $query->row();
        return $data->total;
    }
    
    public function getTieneBecaAlumno($alucod = '', $ano = 2020) {
        $sql = "SELECT flgbeca FROM sga_matricula  WHERE aluant='$alucod' AND periodo='$ano'";
        $query = $this->db->query($sql);
        $data = $query->row();
        return $data->flgbeca;
    }
    
}
