<?php

/**
 * @package       modules/alumno_model/model
 * @name            alumno_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/14
 */
class silabo_model extends CI_Model
{

    public $tabla = '';

    function __construct ()
    {
        parent::__construct ();
        $this->tabla = LIBRERIA2 . '.registro_silabo';
    }

    public function getSilabos ($arrDatos = array())
    {
        $vNemo = '';
        $vSemana = '';

        if (!empty ($arrDatos["NEMO"])) {
            $vNemo = $arrDatos["NEMO"];
        }
        if (!empty ($arrDatos["UNICOD"])) {
            $vUnicod = $arrDatos["UNICOD"];
        }
        if (!empty ($arrDatos["BIMECOD"])) {
            $vBimecod = $arrDatos["BIMECOD"];
        }
        
        $this->db->select ("l.idsilabo,l.idcurso,c.cursonom,u.usunom,l.txtdia1,l.txtdia2,l.txtdia3,l.txtdia4,l.txtdia5");
        $this->db->from ($this->tabla . ' as l');
        $this->db->join (LIBRERIA2 . '.curso as c', 'cursocod=l.idcurso');
        $this->db->join (LIBRERIA2 . '.usuario as u', 'u.usucod=l.usureg');
        $this->db->where ('l.nemo', $vNemo);
        $this->db->where ('l.unicod', $vUnicod);
        $this->db->where ('l.bimecod', $vBimecod);
        $query = $this->db->get ();

        if (!$query) {
            return FALSE;
        } else {
            return $query->result ();
        }
    }

    public function getSilaboDetalle ($idSilabo = 0, $campo = '')
    {
        $this->db->select ($campo . ' as texto ');
        $this->db->from ($this->tabla);
        $this->db->where ('idsilabo', $idSilabo);
        $query = $this->db->get ();

        if (!$query) {
            return FALSE;
        } else {
            return $query->row ();
        }
    }

}
