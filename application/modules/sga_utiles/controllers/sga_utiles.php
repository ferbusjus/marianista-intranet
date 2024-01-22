<?php

/**
 * @package       modules/sga_utiles/controller
 * @name            sga_utiles.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2020 SISTEMAS-DEV
 * @version         1.0 - 01/02/2020
 */
class sga_utiles extends CI_Controller {

    public $token = '';
    public $modulo = 'UTILES';
    public $_session = '';
    public $ano = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('familia_model', 'objFamilia');
        $this->load->model('seguridad_model');
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data['usuario'] = $this->_session['USUCOD'];
        $data["dataAlumnos"] = $this->objAlumno->getAlumnosMatriculadosSimple();
        $data["ano"] = $this->ano;
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('js_utiles');
        $this->load->view('view_lista', $data);
        $this->load->view('view_footer');
    }

    public function mostrarAll() {
        $html = '';
        if ($this->input->is_ajax_request()) {
            $data = $this->objAlumno->getAlumnosMatriculadosSimple();
            if (is_array($data) && sizeof($data)) {
                foreach ($data as $lista) {
                    if ($lista->FLGUTILES === '0') {
                        $class = "glyphicon glyphicon-unchecked optChek";
                        $title = "PENDIENTE";
                    } else {
                        $class = "glyphicon glyphicon-check";
                        $title = "RECIBIDO";
                    }
                    $html .= ' <tr>';
                    $html .= '<td style="width: 10%;text-align: center">' . $lista->DNI . '</td>';
                    $html .= '<td style="width: 10%;text-align: center">' . $lista->ALUCOD . '</td>';
                    $html .= ' <td style="width: 40%;text-align: left">' . $lista->NOMCOMP . '</td>';
                    $html .= ' <td style="width: 30%;text-align: left">' . $lista->NEMODES . '</td>';
                    $html .= ' <td style="width: 10%;text-align: center"><i  value="' . $lista->ALUCOD . '" style="cursor: pointer" title="' . $title . '" data-toggle="tooltip"  class="' . $class . '"></i></td>';
                    $html .= '</tr> ';
                }
            } else {
                $html .= ' <tr>';
                $html .= '<td colspan="5" style="width: 100%;text-align: center">NO SE ENCONTRARON RESULTADOS.</td>';
                $html .= '</tr> ';
            }
        }
        echo $html;
    }

    public function marca() {
        $output = array();
        if ($this->input->is_ajax_request()) {
            $vid = $this->input->post("vid");
            $resp = $this->objAlumno->marcarRecojo($vid);
            if ($resp) {
                $output = array("flg" => 0, "msg" => "PROCESO REALIZADO CORRECTAMENTE.");
            } else {
                $output = array("flg" => 1, "msg" => "ERROR INTERNO EN EL PROCESO.");
            }
        }
        echo json_encode($output);
    }

    public function filtro() {
        $html = '';
        if ($this->input->is_ajax_request()) {
            $vfiltro = $this->input->post("vfiltro");
            $data = $this->objAlumno->getFiltroAlumno($vfiltro);
            if (is_array($data) && sizeof($data)) {
                foreach ($data as $lista) {
                    if ($lista->FLGUTILES === '0') {
                        $class = "glyphicon glyphicon-unchecked optChek";
                        $title = "PENDIENTE";
                    } else {
                        $class = "glyphicon glyphicon-check";
                        $title = "RECIBIDO";
                    }
                    $html .= ' <tr>';
                    $html .= '<td style="width: 10%;text-align: center">' . $lista->DNI . '</td>';
                    $html .= '<td style="width: 10%;text-align: center">' . $lista->ALUCOD . '</td>';
                    $html .= ' <td style="width: 40%;text-align: left">' . $lista->NOMCOMP . '</td>';
                    $html .= ' <td style="width: 30%;text-align: left">' . $lista->NEMODES . '</td>';
                    $html .= ' <td style="width: 10%;text-align: center"><i  value="' . $lista->ALUCOD . '" onclick="js_marca(' . $lista->ALUCOD . ',' . $lista->FLGUTILES . ');"  style="cursor: pointer" title="' . $title . '" data-toggle="tooltip"  class="' . $class . '"></i></td>';
                    $html .= '</tr> ';
                }
            } else {
                $html .= ' <tr>';
                $html .= '<td colspan="5" style="width: 100%;text-align: center">NO SE ENCONTRARON RESULTADOS.</td>';
                $html .= '</tr> ';
            }
        }
        echo $html;
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
