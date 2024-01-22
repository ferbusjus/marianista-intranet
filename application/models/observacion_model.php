<?php

/**
 * @package       modules/alumno_model/model
 * @name            observacion_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/02
 */
class observacion_model extends CI_Model
{

    public $tabla = '';

    function __construct()
    {
        parent::__construct();
        $this->tabla = LIBRERIA . '.sga_tipo_obs';
        $this->tabla2 = LIBRERIA . '.sga_alumno_obs';
    }

    public function lstObservaciones()
    {

        $this->db->select("*");
        $this->db->from($this->tabla);
        $this->db->where('flg_activo', 1);
        $query = $this->db->get();

        if (!$query)
        {
            return FALSE;
        } else
        {
            return $query->result();
        }
    }

    public function get_obervacion_alumno($codAlu = '', $fecha='0000-00-00')
    {
        if (!empty($codAlu))
        {
            $this->db->select("id,id_conducta,fecha,otros,fecreg");
            $this->db->from($this->tabla2);
            $this->db->where('alucod', $codAlu);
            $this->db->where('fecha', $fecha);
            $query = $this->db->get();
            return $query->result();
        }
    }

    public function saveUpdate($data = array())
    {
       // print_r($data); exit;
        if($data['acc']=='1') {            
            unset($data['acc']);
            $id=$data['id'];
            $this->db->where('id', $id);
            unset($data['id']);
            $query = $this->db->update($this->tabla2, $data);
        } else {
             unset($data['acc']); 
            $query = $this->db->insert($this->tabla2, $data);
        }
        return $query;
    }


}
