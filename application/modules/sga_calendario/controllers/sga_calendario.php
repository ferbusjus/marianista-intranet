<?php



/**

 * @package       modules/sga_calendario/controller

 * @name            sga_calendario.php

 * @category      Controller

 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>

 * @copyright     2018 SISTEMAS-DEV

 * @version         1.0 - 11.06.2018

 */

class sga_calendario extends CI_Controller {



    public $token = '';

    public $modulo = 'CALENDARIO';

    public $datasession = '';



    public function __construct() {

        parent::__construct();

        $this->load->model('alumno_model', 'objAlumno');

        $this->load->model('psicologia_model', 'objPsicologia');

        $this->load->model('evento_model', 'objEventos');

        $this->load->model('seguridad_model');

        $this->datasession = $this->nativesession->get('arrDataSesion');

    }



    public function index() {

        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $this->seguridad_model->SessionActivo($url);

        /* if ($this->seguridad_model->restringirIp () == FALSE) {

          $this->load->view ('constant');

          $this->load->view ('view_header');

          $this->load->view ('view_default');

          $this->load->view ('view_footer');

          } else { */

        $this->seguridad_model->registraNavegacion($this->modulo);

        $data['events'] = $this->objEventos->getCitas();

        $data['lstmotivo'] = $this->objPsicologia->getMotivos();
		//echo "<pre>"; print_r($data['lstmotivo']); echo "</pre>"; exit;
        //  print_r($data['events']); exit;

        $data['token'] = $this->token();

        $this->load->view('constant');

        $this->load->view('view_header');

        $this->load->view('lista-calendario-view', $data);

        $this->load->view('calendario-js');

        $this->load->view('view_footer');

        /* } */

    }



    public function addEvent() {

        $title = $this->input->post('titulo');

        $start = $this->input->post('start');

        $end = $this->input->post('end');

        $color = $this->input->post('color');

        $txtnemo = $this->input->post('txtnemo');

        $txtalucod = $this->input->post('txtalucod');

        $txtacude = $this->input->post('txtacude');

        $txtobs = $this->input->post('txtobs');

        $vtipomotivo = $this->input->post('idmotivos');

        $vhora = $this->input->post('hora');

        //$idmotivos = substr($idmotivos, 0, -1);

        $arrMotivos = explode(",", $vtipomotivo);

        $vusu = $this->datasession['USUCOD'];



        $arrData = array(

            'nemo' => $txtnemo,

            'alucod' => $txtalucod,

            'str_acudieron' => $txtacude,

            'str_observacion' => $txtobs,

            'titulo' => $title,

            'feciniatencion' => $start,

            'fecfinatencion' => $end,

            'color' => $color,

            'usureg' => $vusu,

            'hora'=>$vhora

        ); 

        $resp = $this->objEventos->add($arrData);

        if ($resp) {

            $idcita = $this->db->insert_id();

            foreach ($arrMotivos as $idmotivo) {

                $arrDato = array(

                    'idcita' => $idcita,

                    'idmotivo' => $idmotivo,

                    'usureg' => $vusu

                );

                $this->objPsicologia->grabarMotivos($arrDato);

            }

            $output = array(

                'flg' => 0,

                'msg' => 'CITA REGISTRADA CORRECTAMENTE!.',

                'errorlog' => ''

            );

        } else {

            $output = array(

                'flg' => 1,

                'msg' => 'OCURRIO UN ERROR AL REGISTRAR LA CITA COMUNIQUESE CON EL ADMINISTRADOR.',

                'errorlog' => 'Error enviado por Correo.' //$resp

            );

        }

        echo json_encode($output);

    }



    public function deleteEvento() {

        $id = $this->input->post('id');

        $resp = $this->objEventos->delete($id);

        if ($resp) {

            $output = array('flg' => 0, 'msg' => 'EVENTO ELIMINADO CORRECTAMENTE!.');

        } else {

            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL ELIMINAR EVENTO\n COMUNIQUESE CON EL ADMINISTRADOR.');

        }

        echo json_encode($output);

    }



    public function editEvento() {



        $event = $this->input->post('Event');

        if (is_array($event) && count($event > 0)) {

            $id = $event[0];

            $start = $event[1];

            $end = $event[2];

            $arrData = array(

                'title' => $title,

                'start' => $start,

                'end' => $end,

                'color' => $color

            );

            $resp = $this->objEventos->edit($arrData, $id);

            if ($resp) {

                $output = array('flg' => 0, 'msg' => 'EVENTO MODIFICADO CORRECTAMENTE!.');

            } else {

                $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL MODIFICAR EVENTO\n COMUNIQUESE CON EL ADMINISTRADOR.');

            }

        } else {

            $output = array('flg' => 2, 'msg' => 'PROBLEMAS INTERNOS EN EL SERVIDOR.');

        }

        echo json_encode($output);

    }



    public function token() {

        $this->token = md5(uniqid(rand(), true));

        $this->nativesession->set('token', $this->token);

        return $this->token;

    }



}

