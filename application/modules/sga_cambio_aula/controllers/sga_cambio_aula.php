<?php

/**
 * @package       modules/sga_cambio_aula/controller
 * @name            sga_cambio_aula.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2020 SISTEMAS-DEV
 * @version         1.0 - 2020/01/08
 */
class sga_cambio_aula extends CI_Controller {

    public $token = '';
    public $modulo = 'CAMBIO-AULA';

    public function __construct() {
        parent::__construct();
        $this->load->model('asistencia_model', 'objAsistencia');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('observacion_model', 'objObservacion');
        $this->load->model('seguridad_model');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataSalones"] = $this->objSalon->getSalones();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('cambio-aula-js');
        $this->load->view('cambio-aula-view', $data);
        $this->load->view('view_footer');
    }

    public function lstalumno($nemo = '') {
        $dataAlumno = $this->objAlumno->getAlumnosMatriculados($nemo);
        echo json_encode($dataAlumno);
    }

    public function processCambioAula() {
        $valucod = $this->input->post("valucod");
        $vnemo = $this->input->post("vnemo");
        $vDataNemo = $this->objSalon->getAulaxNemo($vnemo, 0);
        $vinstrucod = $vDataNemo->INSTRUCOD;
        $vgradocod = $vDataNemo->GRADOCOD;
        $vseccion = $vDataNemo->SECCIONCOD;

        $resp = $this->objSalon->updateNemoMatricula($valucod, $vnemo);
        $resp = $this->objSalon->updateNemoSalonal($valucod, $vnemo);
        $resp = $this->objSalon->updateNemoAlumno($valucod, $vinstrucod, $vgradocod, $vseccion);
        if ($resp) {
            $arrData = array(
                'msg' => 'SE CAMBIO CORRECTAMENTE DE AULA ',
                'estado' => 'PROCESADO ',
                'flg' => 1
            );
        } else {
            $arrData = array(
                'msg' => 'HUBO UN PROBLEMA EN EL PROCESO',
                'estado' => 'ERROR ',
                'flg' => 0
            );
        }
        echo json_encode($arrData);
    }

    public function cargaAulaMigra() {
        $vnivel = $this->input->post("vnivel");
        $vgrado = $this->input->post("vgrado");
        $dataAlumno = $this->objSalon->getAulasMigrar($vnivel, $vgrado);
        echo json_encode($dataAlumno);
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
