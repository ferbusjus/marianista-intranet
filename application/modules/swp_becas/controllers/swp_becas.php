<?php

/**
 * @package       modules/swp_pagos/controller
 * @name            swp_pagos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2018 SISTEMAS-DEV
 * @version         1.0 - 11.02.2018
 */
class swp_becas extends CI_Controller {

    public $token = '';
    public $modulo = 'BECAS';
    public $datasession = '';
    public $ano = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('becas_model', 'objBecas');
        $this->load->model('seguridad_model');
        $this->datasession = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['vano'] = date("Y");
        $data['s_ano_vig'] = $this->ano;
        $data['becas'] = $this->objBecas->getTipoBecas();
        $data['token'] = $this->token();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('becas-js');
        $this->load->view('lista-becas-view', $data);
        $this->load->view('view_footer');
    }

    public function eliminaBeca() {
        $vId = $this->input->post("vId");
        $vIdBeca = $this->input->post("vIdBeca");
        $resp = $this->objBecas->eliminaBeca($vId, $vIdBeca, $this->ano);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE ELIMINO CORRECTAMENTE LA BECA.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL ELIMINAR LA BECA\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $data = $this->objBecas->get_datatables();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $img = base_url() . '/images/bt_mini_delete.png';
                    $conf = "<img src='$img'  title='Eliminar' onclick=\"javascript:js_eliminar('$fila->alucod','$fila->becacod');\" style='cursor:pointer' />";
                    $arrData [] = array(
                        "dni" => $fila->dni,
                        "nomcomp" => $fila->nomcomp,
                        "ngs" => $fila->instrucod . $fila->gradocod . $fila->seccioncod,
                        "mesini" => $fila->mesini,
                        "mesfin" => $fila->mesfin,
                        "beca" => $fila->becades,
                        "conf" => $conf
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objBecas->count_all(),
                    "recordsFiltered" => $this->objBecas->count_filtered(),
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

    public function getdatos() {
        $output = array();
        $arrDataTipoBeca = array();
        $arrDataAlumno = array();
        $arrDataMotBeca = array();

        $dataBeca = $this->objBecas->getTipoBecas();
        if (is_array($dataBeca) && count($dataBeca) > 0) {
            foreach ($dataBeca as $beca) {
                $arrDataTipoBeca [] = array(
                    'id' => $beca->BECACOD . '*' . $beca->BECAPOR,
                    'value' => $beca->BECADES
                );
            }
        }

        $dataMotBeca = $this->objBecas->getMotivoBecas();
        if (is_array($dataMotBeca) && count($dataMotBeca) > 0) {
            foreach ($dataMotBeca as $mot) {
                $arrDataMotBeca [] = array(
                    'id' => $mot->MOTBECCOD,
                    'value' => $mot->MOTBECDES
                );
            }
        }

        $dataAlumno = $this->objAlumno->getAlumnosMatriculadosSimple();
        if (is_array($dataAlumno) && count($dataAlumno) > 0) {
            foreach ($dataAlumno as $alumno) {
                $arrDataAlumno [] = array(
                    'id' => $alumno->ALUCOD . '*' . $alumno->DNI,
                    'value' => $alumno->ALUCOD . ' - ' . $alumno->NOMCOMP . '  (' . $alumno->NEMODES . ')'
                );
            }

            $output = array(
                'alumno' => $arrDataAlumno,
                'tipobeca' => $arrDataTipoBeca,
                'motbeca' => $arrDataMotBeca
            );
        }
        echo json_encode($output);
    }

    public function saveUpdate() {

        $output = array();
        $vidAlumno = explode("*", $this->input->post("vidAlumno"));
        $vmesini = $this->input->post("vmesini");
        $vmesfin = $this->input->post("vmesfin");
        $vmotbeca = $this->input->post("vmotbeca");
        $vtipbeca = $this->input->post("vtipbeca");
        // $vmonto = $this->input->post("vmonto");
        $vaccion = $this->input->post("vaccion");
        $vusu = $this->datasession['USUCOD'];

        if ($vaccion == 1) { // insertar
            $dataPost = array(
                'ANOBEC' => $this->ano,
                'ALUCOD' => $vidAlumno[0],
                'DNI' => $vidAlumno[1],
                'MESINIBEC' => $vmesini,
                'MESFINBEC' => $vmesfin,
                'BECACOD' => $vtipbeca,
                'MOTBECCOD' => $vmotbeca,
                'USUREG' => $vusu
            );
            $resp = $this->objBecas->grabaBecaAlumno($dataPost);
        } else { // actualizar
            $dataPost = array(
                'MESINIBEC' => $vmesini,
                'MESFINBEC' => $vmesfin,
                'BECACOD' => $vtipbeca,
                'MOTBECCOD' => $vmotbeca,
                'FECMOD' => date("Y-m-d H:i:s"),
                'USUMOD' => $vusu
            );
            $resp = $this->objBecas->updateBecaAlumno($dataPost, $vidAlumno[0], $this->ano);
        }
        if ($resp) {
            $this->objBecas->updatePagosxBecas($vidAlumno[0], $vtipbeca, $this->ano);
            if ($vaccion == 1)
                $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE LA BECA.');
            else
                $output = array('flg' => 0, 'msg' => 'SE MODIFICO CORRECTAMENTE LA BECA.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN LA TRANSACCION\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
