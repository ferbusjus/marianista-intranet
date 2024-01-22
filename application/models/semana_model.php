<?php

/**
 * @package       modules/alumno_model/model
 * @name            alumno_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/14
 */
class semana_model extends CI_Model
{

    public $tabla = '';

    function __construct ()
    {
        parent::__construct ();
        $this->tabla = LIBRERIA2 . '.semana_conf';
    }

    public function getSemana ()
    {
        $this->db->select ("idsemana,semana,dsc_semana,bimecod,unicod,flg_actual");
        $this->db->from ($this->tabla);
        $this->db->where ('flg_activo', 1);
        $this->db->where ('flg_web', 1);
        $this->db->where ('anio', 2016);
        $query = $this->db->get ();

        if (!$query) {
            return FALSE;
        } else {
            return $query->result ();
        }
    }

}
