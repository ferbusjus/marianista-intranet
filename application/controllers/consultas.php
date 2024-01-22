<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class consultas extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('seguridad_model');
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');        
    }

    public function index() {
        $this->load->view('constant');
        $arr['anio']=$this->S_ANO;
        $this->load->view('consultas/consultas-view',  $arr);
    }

    public function material(){
        $this->load->view('constant');
        $this->load->view('consultas/default-view');        
    }
    
    public function filtro() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $vopcion = $this->input->post("opcion");
        $vfiltro = $this->input->post("txtfiltro");
        $data = $this->objAlumno->filtroAlumno($vopcion, $vfiltro);
        if (sizeof($data) > 0) {
            $output = array(
                'total' => count($data),
                'data' => $data
            );
        } else {
            $output = array(
                'total' => 0,
                'data' => ''
            );
        }
        echo json_encode($output);
    }

    public function getDatos() {
        $valucod = $this->input->post("valucod");
        $data = $this->objAlumno->_getDatoAlumno($valucod);
        if (sizeof($data) > 0) {
            $output = array(
                'total' => count($data),
                'data' => $data
            );
        } else {
            $output = array(
                'total' => 0,
                'data' => ''
            );
        }
        echo json_encode($output);
    }

}
