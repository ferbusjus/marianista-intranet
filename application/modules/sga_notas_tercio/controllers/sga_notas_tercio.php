<?php

/**
 * @package       modules/sga_notas_tercio/controller
 * @name            sga_notas_tercio.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2018 SISTEMAS-DEV
 * @version         1.0 - 17.10.2018
 */
class sga_notas_tercio extends CI_Controller {

    public $token = '';
    public $modulo = 'NOTAS-TERCIO';
    public $datasession = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('nota_tercio_model', 'objNotaTercio');
        $this->load->model('seguridad_model');
        $this->datasession = $this->nativesession->get('arrDataSesion');
    }

    public function index() {
        try {
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $this->seguridad_model->SessionActivo($url);
            //$vnivel = $this->datasession['NIVEL'];
            $this->seguridad_model->registraNavegacion($this->modulo);
            //$data['lstAlumnos'] = $this->objNotaTercio->getListaAlumnos();
            $data['token'] = $this->token();
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('notas-tercio-js');
            $this->load->view('lista-tercio-view', $data);
            $this->load->view('view_footer');
        } catch (Exception $err) {
            show_error($err->getMessage(), 500, "Error Interno");
        }
    }

    public function getanios() {
        $output = array();
        $vidAlumno = $this->input->post("idAlumno");
        $datoAlumno = $this->objNotaTercio->getDatoAlumno($vidAlumno);
        $arrNotasAnio = $this->objNotaTercio->getNotasxAnio($vidAlumno);
        $output = array(
            'dataAlumno' => $datoAlumno,
            'dataNotas' => $arrNotasAnio
        );
        echo json_encode($output);
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            // ======== Recepcion de variables POST =============
            $vtxtalu = $this->input->post("txtalu");
            // ===============================================
            $output = array();
            $arrData = array();
            $data = $this->objNotaTercio->getListaAlumnos($vtxtalu);
            if (is_array($data) && count($data) > 0) {
                $row = 1;
                foreach ($data as $fila) {
                    $img = base_url() . '/images/bt_mini_buscar.png';
                    $conf = "<img src='$img'  title='Ver Detalle' onclick=\"javascript:js_detalle('$fila->codigo');\" style='cursor:pointer' />";
                    $arrData [] = array(
                        "fila" => $fila->numord,
                        "codigo" => $fila->codigo,
                        "nomcomp" => $fila->nomcomp,
                        "anios" => $fila->anios,
                        "punt" => $fila->pt,
                        "prom" => $fila->pb,
                        "conf" => $conf
                    );
                    $row++;
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objNotaTercio->getAll(),
                    "recordsFiltered" => $this->objNotaTercio->getAll(),
                    "data" => $arrData
                );
            } else {
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => $arrData
                );
            }
            echo json_encode($output);
        } else {
            show_error("Accion no Permitida.", 500, "Error Interno");
        }
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
