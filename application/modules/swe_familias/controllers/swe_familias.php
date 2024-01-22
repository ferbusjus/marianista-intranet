<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class swe_familias extends CI_Controller {

    public $token = '';
    public $modulo = 'FAMILIAS';

    function __construct() {
        parent::__construct();
        $this->load->model('familia_model', 'objfamilia');
        $this->load->model('seguridad_model');
        $this->load->library('form_validation');
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
        $data['token'] = $this->token();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('familia-js');
        $this->load->view('lista-familia-view', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $data = $this->objfamilia->get_datatables();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $link = "<a href='javascript:void();' style='cursor:pointer;' onclick=\"javascript:js_verhijos('$fila->famcod');\" >" . $fila->total . "</a>";
                    $conf = "<span style='font-size:15px; color:blue;cursor:pointer;' onclick=\"javascript:js_editar('$fila->famcod');\"  class='glyphicon glyphicon-pencil' data-toggle='tooltip' title='Editar'></span>";
                    $conf .= "&nbsp;<span style='font-size:15px; color:red;cursor:pointer;' onclick=cursor:pointer; class='glyphicon glyphicon-ban-circle'  data-toggle='tooltip' title='Bloquear'></span>";
                    $conf .= "&nbsp;<span style='font-size:15px; color:blue;cursor:pointer;' onclick=\"js_generar('$fila->famcod','$fila->famdes');\" class='glyphicon glyphicon-random'  data-toggle='tooltip' title='Generar Clave'></span>";
                    $conf .= "&nbsp;<span style='font-size:15px; color:blue;cursor:pointer;' onclick=\"js_mail('$fila->famcod');\" class='glyphicon glyphicon-envelope'  data-toggle='tooltip' title='Enviar correo'></span>";
                    $arrData [] = array(
                        "famcod" => $fila->famcod,
                        "famdes" => $fila->famdes,
                        "email" => $fila->padmail,
                        "total" => $link,
                        "conf" => $conf
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objfamilia->count_all(),
                    "recordsFiltered" => $this->objfamilia->count_filtered(),
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

    public function getDatosHijos() {
        $arrHijos = array();
        $vId = $this->input->post("vId");
        $dataHijos = $this->objfamilia->getDatosHijos($vId);
        if (count($dataHijos) > 0) {
            $arrHijos = $dataHijos;
        }
        echo json_encode($arrHijos);
    }

    public function getDatosFamilia() {
        $arrFamilia = array();
        $vId = $this->input->post("vId");
        $dataFam = $this->objfamilia->getFamilia($vId);
        if (count($dataFam) > 0) {
            $arrFamilia = $dataFam;
        }
        echo json_encode($arrFamilia);
    }

    public function saveUpdate() {
        $accion = $this->input->post("accion");
        $famcod = $this->input->post("txtcodigo");
        $arrdata = array(
            //'FAMCOD' => $this->input->post ("txtcodigo"),
            'FAMDES' => $this->input->post("txtpaterno_p") . " " . $this->input->post("txtpaterno_m"),
            'PADAPEPAT' => $this->input->post("txtpaterno_p"),
            'PADAPEMAT' => $this->input->post("txtmaterno_p"),
            'PADNOMBRE' => $this->input->post("txtnombre_p"),
            'PADDIR' => $this->input->post("txtdireccion_p"),
            'PADMAIL' => $this->input->post("txtemail_p"),
            'PADTEL' => $this->input->post("txtcelular_p"),
            'MADAPEPAT' => $this->input->post("txtpaterno_m"),
            'MADAPEMAT' => $this->input->post("txtmaterno_m"),
            'MADNOMBRE' => $this->input->post("txtnombre_m"),
            'MADDIR' => $this->input->post("txtdireccion_m"),
            'MADMAIL' => $this->input->post("txtemail_m"),
            'MADTEL' => $this->input->post("txtcelular_m"),
            'PADDNI' => $this->input->post("txtdni_p"),
            'MADDNI' => $this->input->post("txtdni_m")
        );

        if ($accion == 'update') {
            $resp = $this->objfamilia->update($arrdata, $famcod);
            $txt = 'MODIFICO';
        } else {
            $resp = $this->objfamilia->insert($arrdata);
            $txt = 'REGISTRO';
        }
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE ' . $txt . ' CORRECTAMENTE LOS DATOS DE LA FAMILIA.', 'error' => '');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN EL PROCESO\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error' => $resp);
        }
        echo json_encode($output);
    }

    function getVerifica() {
        $vFamcod = $this->input->post("vFamcod");
        $existe = $this->objfamilia->existeUsuario($vFamcod);
        if ($existe)
            $arrJson = array('estado' => 'Ya tiene clave Generada.', 'flag' => 1);
        else
            $arrJson = array('estado' => '', 'flag' => 0);
        echo json_encode($arrJson);
    }

    public function grabaToken($vFamcod = '', $vFamdesc = '', $vClavegen = '') {
        $existe = $this->objfamilia->existeUsuario($vFamcod);
        if ($existe)
            $resp = $this->objfamilia->updateToken($vFamcod, $vClavegen);
        else
            $resp = $this->objfamilia->insertToken($vFamcod, $vFamdesc, $vClavegen);

        return $resp;
    }

    public function getGeneraClave() {
        $vFamdesc = $this->input->post("vFamdesc");
        $vFamcod = $this->input->post("vFamcod");
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $password = "";
        //Reconstruimos la contrase�a segun la longitud que se quiera
        for ($i = 0; $i < 8; $i++) {
            //obtenemos un caracter aleatorio escogido de la cadena de caracteres
            $password .= substr($str, rand(0, 36), 1);
        }
        $resp = $this->grabaToken($vFamcod, $vFamdesc, $password);
        if ($resp) {
            $arrJson = array(
                'msg' => 'SU CLAVE TOKEN GENERO CORRECTAMENTE.',
                'token' => $password,
                'error' => ''
            );
        } else {
            $arrJson = array(
                'msg' => 'HUBO UN PROBLEMA AL GENERAR EL TOKEN.',
                'token' => $password,
                'error' => $resp
            );
        }
        echo json_encode($arrJson);
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
