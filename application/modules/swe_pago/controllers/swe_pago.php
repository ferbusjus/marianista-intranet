<?php

/**
 * @package       modules/sga_asistencia/controller
 * @name            sga_asistencia.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/13
 */
class swe_pago extends CI_Controller
{

    public $token = '';
    public $modulo = 'PENSION';
    public $datasession = '';

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('cobros_model', 'objCobro');
        
        $this->load->model ('seguridad_model');
        $this->datasession = $this->nativesession->get ('arrDataSesion');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        $this->seguridad_model->registraNavegacion ($this->modulo);
        $data['token'] = $this->token ();
        $data["dataHijos"] = $this->objAlumno->getHijos ($this->datasession['FAMCOD']);
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('js_asistencia');
        $this->load->view ('view_asistencia', $data);
        $this->load->view ('view_footer');
    }

    public function getpagos ()
    {
        if ($this->input->post ('token') && $this->input->post ('token') == $this->nativesession->get ('token')) {            
            sleep (1);
            $vid = $this->input->post ('vid');
            $vMes = $this->input->post ('vMes');
            $vAcc = $this->input->post ('vAcc');
            if($vAcc=='1'){
                $arrDataPago  = $this->objCobro->getPagoxAlumno($vid);
            } else {
                $arrDataPago  = $this->objCobro->getPagoxAlumnoxMes($vid,$vMes);
            }
            $arrayData = array('data' => $arrDataPago, 'msg' => 1);
        } else {
            $arrayData = array('data' => '', 'msg' => 2);
        }
        echo json_encode ($arrayData);
    }
    
    public function token ()
    {
        $this->token = md5 (uniqid (rand (), true));
        $this->nativesession->set ('token', $this->token);
        return $this->token;
    }

}
