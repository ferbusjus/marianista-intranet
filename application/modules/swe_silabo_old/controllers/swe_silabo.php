<?php

/**
 * @package       modules/sga_asistencia/controller
 * @name            sga_asistencia.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/13
 */
class swe_silabo extends CI_Controller
{

    public $token = '';
    public $datasession = '';

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('semana_model', 'objSemana');
        $this->load->model ('salon_model', 'objSalon');
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('silabo_model', 'objSilabo');
        $this->datasession = $this->nativesession->get ('arrDataSesion');
    }

    public function index ()
    {
        $data['token'] = $this->token ();
        $data["dataHijos"] = $this->objAlumno->getHijos ($this->datasession['FAMCOD']);
        $data["dataSemana"] = $this->objSemana->getSemana ();
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('js_asistencia');
        $this->load->view ('view_asistencia', $data);
        $this->load->view ('view_footer');
    }

    public function listar ()
    {
        $dataSilabo = array('data' => '', 'msg' => 0);
        if ($this->input->post ('token') && $this->input->post ('token') == $this->datasession['token']) {
            sleep (1);
            $dataForm = json_decode ($this->input->post ('dataForm'));
            $arrData = array(
                'NEMO' => $dataForm->vnemo,
                'ALUCOD' => $dataForm->valumno,
                'SEMANA' => $dataForm->vsemana
            );

            $arrSilabos = $this->objSilabo->getSilabos ($arrData);
            $dataSilabo = array('data' => $arrSilabos, 'msg' => 1);
        } else {
            $dataSilabo = array('data' => '', 'msg' => 2);
        }
        echo json_encode ($dataSilabo);
    }

    public function verDetalle ()
    {
        $vIdsilabo = $this->input->post ('vIdsilabo');
        $vCampo = $this->input->post ('vCampo');
        $data = $this->objSilabo->getSilaboDetalle ($vIdsilabo, $vCampo);
        echo $data->texto;
    }

    public function token ()
    {
        $this->token = md5 (uniqid (rand (), true));
        $this->nativesession->set ('token', $this->token);
        return $this->token;
    }

}
