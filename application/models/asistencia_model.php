<?php

/**
 * @package       modules/sg_asistencia/model
 * @name            asistencia_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/07
 */
class asistencia_model extends CI_Model
{

    public $tabla = '';

    function __construct ()
    {
        parent::__construct ();
        $this->tabla = LIBRERIA . '.sga_asisalumno';
    }

    public function get_rango_ingreso ($nivel = '')
    {
        if (!empty ($nivel)) {
            $query = $this->db->get_where (LIBRERIA . '.sga_marcacion', array('nivel' => $nivel));
            if (!$query) {
                return FALSE;
            } else {
                return $query->result ();
            }
        } else {
            return FALSE;
        }
    }

    public function getAsistenciaxDia($fecha='', $alucod='')
    {
            $sql = "SELECT t_asist FROM sga_asisproc WHERE id_alumno='$alucod' AND evento='I' AND fecha  ='$fecha' ";

            $query=$this->db->query($sql);
            return $query->result();
    }
    
    public function getAsistenciaHoy($fecha='', $alucod='')
    {
            $sql = "SELECT t_asist FROM sga_asisalumno WHERE id_alumno='$alucod' AND evento='I' AND fecha  ='$fecha' ";

            $query=$this->db->query($sql);
            return $query->result();
    }    
    
        
    public function verificaAsistencia ($codAlu = '', $est = 0)
    {
        if (!empty ($codAlu)) {
            $this->db->select ('*')
                    ->where ('id_alumno', $codAlu)
                    ->where ('est', $est)
                    ->where ('fecha', 'CURDATE()', FALSE);
            $query = $this->db->get ($this->tabla);
            //$query = $this->db->get_where($this->tabla, array('id_alumno' => $codAlu, 'est' => 0, 'fecha' => curdate()));            
            if (!$query) {
                return FALSE;
            } else {
                return $query->result ();
            }
        } else {
            return FALSE;
        }
    }

    public function actualizaMarca ($array = array())
    {
        if (!empty ($array) && is_array ($array)) {
            $data = array(
                'hora' => $array[1],
                't_asist' => $array[2],
                'est' => $array[3]
            );
            $this->db->where ('id_alumno', $array[0]);
            $this->db->where ('fecha', 'CURDATE()', FALSE);
            $query = $this->db->update ($this->tabla, $data);
            if (!$query) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    public function updateAsis ($vTipo = '', $vHora = '', $vIdalu = '')
    {
        if (!empty ($vTipo) && !empty ($vHora) && !empty ($vIdalu)) {
            $data = array(
                'hora' => $vHora,
                't_asist' => $vTipo,
                'est' => 1
            );
            $this->db->where ('id_alumno', $vIdalu);
            $this->db->where ('est', 0);
            $this->db->where ('fecha', 'curdate()', FALSE);
            $query = $this->db->update ($this->tabla, $data);
            if (!$query) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    public function insertaAsistencia ($array = array())
    {
        if (!empty ($array) && is_array ($array)) {
            $data = array(
                'id_asistencia' => $array[0],
                'id_alumno' => $array[1],
                'nomcomp' => $array[2],
                'instrucod' => $array[3],
                'fecha' => $array[4],
                'hora' => $array[5],
                'evento' => $array[6],
                't_asist' => $array[7],
                't_tipo' => $array[8],
                'observacion' => $array[9],
                'est    ' => $array[10]
            );
            $query = $this->db->insert ($this->tabla, $data);
            if (!$query) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    public function getAsistenciasAll ($arrDatos = array(), $return = FALSE)
    {

        if (!empty ($arrDatos) && is_array ($arrDatos)) {
            $vTipo = '';
            $vAlucod = '';
            $vMes = 0;
            if (!empty ($arrDatos["ALUCOD"])) {
                $vAlucod = $arrDatos["ALUCOD"];
            }
            if (!empty ($arrDatos["TIPO"]) && $arrDatos["TIPO"] != '0') {
                $vTipo = $arrDatos["TIPO"];
            }
            if (!empty ($arrDatos["MES"]) && $arrDatos["MES"] != '0') {
                $vMes = intval ($arrDatos["MES"]);
            }
            //echo "CALL sp_get_asistencia_alumnos('$vAlucod','$vTipo',$vMes)"; exit;
            //$query = $this->db->query ("CALL sp_get_asistencia_alumnos('$vAlucod','$vTipo',$vMes)");

                $query = $this->db->query("SELECT
                                                            alucod,fecha,
                                                            UPPER(DATE_FORMAT(fecha,'%a-%d-%b')) AS fecha_formato
                                                            ,hora,t_asist AS tipo,observacion,evento
                                                            FROM sga_asisalumno
                                                            WHERE alucod='$vAlucod' AND evento='$vTipo' AND month(fecha)='$vMes'
                                                            UNION ALL
                                                            SELECT
                                                            alucod,fecha,
                                                            UPPER(DATE_FORMAT(fecha,'%a-%d-%b')) AS fecha_formato
                                                            ,hora,t_asist AS tipo,observacion,evento
                                                            FROM sga_asisproc 
                                                            WHERE alucod='$vAlucod' AND evento='$vTipo' AND month(fecha)='$vMes'
                                                            ORDER BY fecha DESC");
                
               // echo  $this->db->last_query(); exit;
                return $query->result();        
    
        } else {
            return FALSE;
        }
    }
    public function verificaNivelxFamilia ($famcod=''){
        if($famcod!=''){
            $query = $this->db->query ("SELECT COUNT(*) AS total FROM ".LIBRERIA2.".alumno WHERE famcod='$famcod' AND flg_matricula=1 AND instrucod='S'");
            $query = $query->row ();
            if ($query->total > 0){
                return TRUE;
            } else {
                return FALSE;
            }                
        }
    }
    public function getAsistencias ($arrDatos = array(), $return = FALSE)
    {
        if (!empty ($arrDatos) && is_array ($arrDatos)) {
            $this->db->select ("*");
            $this->db->from (LIBRERIA . '.sga_asisproc');
            if (!empty ($arrDatos["ALUCOD"])) {
                $this->db->where ("id_alumno", $arrDatos["ALUCOD"]);
            }
            if (!empty ($arrDatos["TIPO"]) && $arrDatos["TIPO"] != '0') {
                $this->db->where ("evento", $arrDatos["TIPO"]);
            }
            if (!empty ($arrDatos["MES"]) && $arrDatos["MES"] != '0') {
                $this->db->where ("MONTH(fecha)", intval ($arrDatos["MES"]));
            }
            $query = $this->db->get ();
            //echo  $this->db->last_query(); exit;
            if (!$query) {
                return FALSE;
                //throw new Exception($this->db->_error_message());
            } else {
                if ($return)
                    return $query->row ();
                else
                    return $query->result ();
            }
        } else {
            return FALSE;
        }
    }

    public function getAsistenciaDiaria ($arrDatos = array(), $return = FALSE)
    {
        if (!empty ($arrDatos) && is_array ($arrDatos)) {
            $this->db->select ("*");
            $this->db->from ($this->tabla);
            if (!empty ($arrDatos["ALUCOD"])) {
                $this->db->where ("id_alumno", $arrDatos["ALUCOD"]);
            }
            if (!empty ($arrDatos["TIPO"]) && $arrDatos["TIPO"] != '0') {
                $this->db->where ("evento", $arrDatos["TIPO"]);
            }
            if (!empty ($arrDatos["MES"]) && $arrDatos["MES"] != '0') {
                $this->db->where ("MONTH(fecha)", intval ($arrDatos["MES"]));
            }
            $query = $this->db->get ();

            if (!$query) {
                return FALSE;
                //throw new Exception($this->db->_error_message());
            } else {
                if ($return)
                    return $query->row ();
                else
                    return $query->result ();
            }
        } else {
            return FALSE;
        }
    }

    public function getDataClaves ()
    {
        $this->db->select ("*");
        $this->db->from ("fercmias_academico.usuarios_claves");
        $this->db->order_by("instucod");
        $this->db->order_by("gradocod");
        $this->db->order_by("seccioncod");
        $this->db->order_by("nomcomp");
        $query = $this->db->get ();
        return $query->result ();
    }

    public function get_alumno ($codAlu = '', $return = FALSE)
    {
        if (!empty ($codAlu)) {
            /* $this->db->select('a.ALUCOD,a.ALUCOD, a.APEPAT,a.APEMAT,a.INSTRUCOD');
              $this->db->from($this->tabla ,' as s ');
              $this->db->join(
              LIBRERIA.'.ALUMNO as a',
              'a.ALUCOD=s.id_alumno',
              'INNER'
              );
              $this->db->where('s.id_alumno', $codAlu);
              $query = $this->db->get(); */
            $query = $this->db->get_where ($this->tabla, array('id_alumno' => $codAlu));
            if (!$query) {
                return FALSE;
                //throw new Exception($this->db->_error_message());
            } else {
                if ($return)
                    return $query->row ();
                else
                    return $query->result ();
            }
        } else {
            return FALSE;
        }
    }

}
