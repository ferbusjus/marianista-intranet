<?php

/**
 * @package       modules/sga_asistencia/controller
 * @name            sga_asistencia.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/13
 */
class swe_nota extends CI_Controller
{

    public $token = '';
    public $modulo = 'NOTAS';
    public $datasession = '';

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('asistencia_model', 'objAsistencia');
        $this->load->model ('salon_model', 'objSalon');
        $this->load->model ('nota_model', 'objNota');
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('observacion_model', 'objObservacion');
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

    public function verBoleta ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        sleep (1);
        $datasession = $this->nativesession->get ('sesionNota');
        $valucod = $datasession['ALUCOD'];
        $vnemo = $datasession['NEMO'];
        $vbimestre = $datasession['BIMESTRE'];
        $vunidad = $datasession['UNIDAD'];
        $this->nativesession->delete ('sesionNota');
        //$data['archivo'] = BASE_URL . 'boletas/'.ANO_VIG.'/' . $vnemo . '/boletas_' . $vnemo . '_' . $vbimestre . '_' . $vunidad . '_' . $valucod . '.pdf';
        $data['archivo'] = BASE_URL . 'boletas/'.ANO_VIG.'/' . $vnemo . '/boletas_' . $vnemo .  '_' . $valucod . '.pdf';
        $this->load->view ('view_boleta', $data);
    }

    public function listar ()
    {
        $dataNota = array('data' => '', 'msg' => 0);
        
        if ($this->input->post ('token') && $this->input->post ('token') == $this->nativesession->get ('token')) {
            sleep (1);
            $dataForm = json_decode ($this->input->post ('dataForm'));
            $arrData = array(
                'ALUCOD' => $dataForm->valumno,
                'NEMO' => $dataForm->vsalon,
                'BIMESTRE' => $dataForm->vbimestre,
                'UNIDAD' => $dataForm->vunidad
            );
            $this->nativesession->set ('sesionNota', $arrData);
            $arrNotas = $this->objNota->getNotasAlumno ($arrData);
            $dataNota = array('data' => $arrNotas, 'msg' => 1);
        } else {
            $dataNota = array('data' => '', 'msg' => 2);
        }
        echo json_encode ($dataNota);
        //redirect('login', 'refresh');    	      
    }

    public function getNotasBimestre ()
    {
        $dataNota = array('data' => '', 'msg' => 0);
        // if ($this->input->post ('token') && $this->input->post ('token') == $this->session->userdata ('token')) {
        sleep (1);
        $arrData = array(
            'ALUCOD' => $this->input->post ('vIdAlumno'),
            'NEMO' => $this->input->post ('vIdNemo'),
            'BIMESTRE' => $this->input->post ('vIdBimestre'),
            'UNIDAD' => $this->input->post ('vIdUnidad')
        );
        $arrNotas = $this->objNota->getNotasxBimestre ($arrData);
        $dataNota = array('dataNotas' => $arrNotas, 'msg' => 1);
        // } else {
        //     $dataNota = array('dataNotas' => '', 'msg' => 2);
        // }
        echo json_encode ($dataNota);
    }

    public function lstUnidad ()
    {
        $bimecod = $this->input->post ('bimecod');
        $data = array();
        switch ($bimecod) {
            case 1:
                $data[0] = array('id' => 1, 'valor' => 'UNIDAD 1');
                $data[1] = array('id' => 2, 'valor' => 'UNIDAD 2');
                break;
            case 2:
                $data[0] = array('id' => 3, 'valor' => 'UNIDAD 3');
                $data[1] = array('id' => 4, 'valor' => 'UNIDAD 4');
                break;
            case 3:
                $data[0] = array('id' => 5, 'valor' => 'UNIDAD 5');
                $data[1] = array('id' => 6, 'valor' => 'UNIDAD 6');
                break;
            case 4:
                $data[0] = array('id' => 7, 'valor' => 'UNIDAD 7');
                $data[1] = array('id' => 8, 'valor' => 'UNIDAD 8');
                break;
        }

        echo json_encode ($data);
    }

    public function token ()
    {
        $this->token = md5 (uniqid (rand (), true));
        $this->nativesession->set ('token', $this->token);
        return $this->token;
    }

}
