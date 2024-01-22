<?php

if (!defined ('BASEPATH'))
    exit ('No direct script access allowed');

class sga_consultas extends CI_Controller
{

    public $token = '';
    public $modulo = 'CONSULTAS';    
    function __construct ()
    {
        parent::__construct ();
        $this->load->model ('seguridad_model');
        $this->load->helper ('funciones_helper');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        $this->seguridad_model->registraNavegacion ($this->modulo);
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('consultas-view' );
        $this->load->view ('view_footer');
    }
    
}
