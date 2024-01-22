<?php

if (!defined ('BASEPATH'))
    exit ('No direct script access allowed');

class sga_ano extends CI_Controller
{

    public $token = '';
    public $modulo = 'AÑOS';    
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
        $data['titulo'] = "Cambio de Año";
        $data['vano'] = date("Y");
        $data['s_ano_vig'] = $this->nativesession->get ('S_ANO_VIG');
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('ano-script');
        $this->load->view ('ano-view', $data);
        $this->load->view ('view_footer');
    }

    public function cambio(){
        $vano = $this->input->post("cbano");
        $this->nativesession->set ('S_ANO_VIG',$vano); 
        redirect ('sga_ano', 'refresh');
    }
    
}
