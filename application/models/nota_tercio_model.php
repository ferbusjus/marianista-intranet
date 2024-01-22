<?php

/**
 * @package       modules/notas_tercio_model/model
 * @name            notas_tercio_model.php
 * @category      Model
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2018 SISTEMAS-DEV
 * @version         1.0 - 17.10.2018
 */
class nota_tercio_model extends CI_Model {

    public $tabla = '';

    function __construct() {
        parent::__construct();
        $this->tabla = LIBRERIA2 . '.notas_tercio_carga_2018';
    }

    public function getListaAlumnos($vtxtalu = '') { //@row_number:=@row_number+1 AS row_number,
        $this->db->select("codigo,nomcomp,COUNT(*) as anios, SUM(pt) AS pt , ROUND(SUM(pb)/5) AS pb, numord");
        $this->db->from($this->tabla); // ", (SELECT @row_number:=0) AS t "        
        if (trim($vtxtalu) != '') {
            $this->db->like("nomcomp", trim($vtxtalu));
        }
        $this->db->group_by("codigo");
        $this->db->order_by("pt", "desc");
        if ($_POST['length'] != -1)
        //$sql .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'];
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        if (!$query) {
            return NULL;
        } else {
            return $query->result();
        }
    }

    public function getDatoAlumno($idAlumno = '') {
        $this->db->select("a.dni,a.alucod,a.nomcomp,s.nemodes as aula");
        $this->db->from(LIBRERIA2 . ".alumno as a");
        $this->db->join(LIBRERIA2 . ".salon_al as sa", "a.alucod=sa.alucod and sa.ano=" . ANO_VIG, "INNER");
        $this->db->join(LIBRERIA2 . ".salon as s", "s.nemo=sa.nemo and s.ano=" . ANO_VIG, "INNER");
        $this->db->where("a.dni", $idAlumno);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        }
    }

    public function getNotasxAnio($idAlumno = '') {
        $this->db->select("codigo, nomcomp, pt as punt, pb as prom, gs as aula");
        $this->db->from($this->tabla);
        $this->db->where("codigo", $idAlumno);
        $this->db->order_by("gs", "desc");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function getAll() {
        $this->db->select("COUNT(DISTINCT codigo) AS TOTAL");
        $this->db->from($this->tabla);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $total = $row->TOTAL;
            return $total;
        } else {
            return 0;
        }
    }

}
