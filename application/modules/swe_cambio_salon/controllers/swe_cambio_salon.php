<?php

/**
 * @package       modules/sga_asistencia/controller
 * @name            sga_cambio_salon.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/10/10
 */
class swe_cambio_salon extends CI_Controller
{

    public $token = '';
    public $modulo = 'CAMBIO-SALON';

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('asistencia_model', 'objAsistencia');
        $this->load->model ('salon_model', 'objSalon');
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('observacion_model', 'objObservacion');
        $this->load->model ('seguridad_model');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        $this->seguridad_model->registraNavegacion ($this->modulo);
        $data['token'] = $this->token ();
        $data["dataSalones"] = $this->objSalon->getSalones ();
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('cambio-salon-js');
        $this->load->view ('cambio-salon-view', $data);
        $this->load->view ('view_footer');
    }

    public function lstalumno ($nemo = '')
    {
        $dataAlumno = $this->objAlumno->getAlumnos ($nemo);
        echo json_encode ($dataAlumno);
    }

    public function cargaAulaMigra ()
    {
        $vnivel = $this->input->post ("vnivel");
        $vgrado = $this->input->post ("vgrado");
        $dataAlumno = $this->objSalon->getAulasMigrar ($vnivel, $vgrado);
        echo json_encode ($dataAlumno);
    }

    public function cambioAula ()
    {
        $vnemOrg = $this->input->post ("vnemOrg"); // Hay que dividir el value porque trae : NEMO / INTRUCOD / GRADOCOD
        $vnemDes = $this->input->post ("vnemDes");
        $valucod = $this->input->post ("valucod");
        $resp = $this->objSalon->cambiaSalonAlumno ($vnemOrg, $vnemDes, $valucod);
        if ($resp) {
            $arrData = array(
                'msg' => 'EL ALUMNO FUE MIGRADO CORRECTAMENTE AL AULA : ',
                'flg' => 1
            );
        } else {
            $arrData = array(
                'msg' => 'HUBO UN PROBLEMA EN EL PROCESO. VUELVA A INTENTARLO O COMUNIQUESE CON EL ADMINISTRADOR. ',
                'flg' => 0
            );
        }
        echo json_encode ($arrData);
    }

    public function token ()
    {
        $this->token = md5 (uniqid (rand (), true));
        $this->nativesession->set ('token', $this->token);
        return $this->token;
    }

}
