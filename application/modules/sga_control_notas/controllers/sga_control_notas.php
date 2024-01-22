<?php

/**
 * @package       modules/sga_control_notas/controller
 * @name            sga_control_notas.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2018 SISTEMAS-DEV
 * @version         1.0 - 26.08.2018
 */
class sga_control_notas extends CI_Controller {

    public $token = '';
    public $modulo = 'CONTROL-NOTAS';
    public $datasession = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('nota_model', 'objNota');
        $this->load->model('seguridad_model');
        $this->datasession = $this->nativesession->get('arrDataSesion');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $vnivel = $this->datasession['NIVEL'];
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['lstNivel'] = $this->objNota->getListaNivel($vnivel);
        $data['token'] = $this->token();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('control-js');
        $this->load->view('lista-control-view', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            // ======== Recepcion de variables POST =============
            $vnivel = $this->input->post("idnivel");
            $vbimestre = $this->input->post("idbimestre");
            $vunidad = $this->input->post("idunidad");
            // ===============================================
            $output = array();
            $arrData = array();
            $data = $this->objNota->getDataTables($vnivel, $vbimestre, $vunidad);
            if (is_array($data) && count($data) > 0) {
                $row = 1;
                foreach ($data as $fila) {
                    $img = base_url() . '/images/bt_mini_buscar.png';
                    $conf = "<img src='$img'  title='Ver Detalle' onclick=\"javascript:js_detalle_salon('$fila->nemo','$fila->nemodes');\" style='cursor:pointer' />";
                    $vprogress = '<div class="skill-name"><b>' . $fila->total3 . '%</b></div> 
                            <div class="progress progress-striped active progress-adjust">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $fila->total3 . '" style="width: ' . $fila->total3 . '%">                                
                                </div>
                            </div>';
                    $arrData [] = array(
                        "fila" => $row,
                        "aula" => $fila->nemodes,
                        "tutor" => $fila->tutor,
                        "treg" => $fila->total1,
                        "tcarga" => $fila->total2,
                        "avance" => $vprogress,
                        "conf" => $conf
                    );
                    $row++;
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objNota->getAll($vnivel),
                    "recordsFiltered" => $this->objNota->getAll($vnivel),
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
            echo json_encode($output);
        }
    }

    public function getdetallenemo() {
        if ($this->input->is_ajax_request()) {
            // ======== Recepcion de variables POST =============
            $vnemo = $this->input->post("vnemo");
            $vnivel = $this->input->post("idnivel");
            $vbimestre = $this->input->post("idbimestre");
            $vunidad = $this->input->post("idunidad");
            // ===============================================
            $output = array();
            $arrData = array();
            $data = $this->objNota->getDataTablesCurso($vnivel,$vnemo, $vbimestre, $vunidad);
            if (is_array($data) && count($data) > 0) {
                $row = 1;
                foreach ($data as $fila) {
                    $vprogress = '<div class="skill-name"><b>' . $fila->total3 . '%</b></div> 
                            <div class="progress progress-striped active progress-adjust">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $fila->total3 . '" style="width: ' . $fila->total3 . '%">                                
                                </div>
                            </div>'; 
                    $arrData [] = array(
                        "fila" => $row,
                        "curso" => $fila->cursonom,
                        "profe" => $fila->profe,
                        "talum" => $fila->total_alumno,
                        "llenado" => $fila->llenado,
                        "falta" => $fila->falta,
                        "avance" => $vprogress
                    );
                    $row++;
                }
                $output = array(
                    "data" => $arrData
                );
            } else {
                $output = array(
                    "data" => NULL
                );
            }
            echo json_encode($output);
        } else {
            echo json_encode($output);
        }
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
