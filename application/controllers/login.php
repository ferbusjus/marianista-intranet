<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
date_default_timezone_set('America/Lima');

class login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('login_model', 'objLogin');
        $this->load->model('seguridad_model', 'objSeguridad');
    }

    public function index() {
        if ($this->nativesession->get('arrDataSesion')['is_logged_in']) {
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('view_home');
            $this->load->view('view_footer');
        } else {
            $this->load->view('constant');
            $this->load->view('view_login');
        }
    }

    function CerrarSesion() {
        /* destrozamos la sesion activay nos vamos al login de nuevo */
        //if ($this->session->userdata('is_logged_in'))
        //{
        $this->nativesession->delete('token');
        $this->nativesession->delete('arrDataSesion');
        $this->nativesession->delete('S_ANO_VIG');
        redirect('login', 'refresh');
        //}
    }

    public function ValidaAcceso() {
        //session_start ();
        // sleep (1);
        $Login = json_decode($this->input->post('LoginPost'));
        $response = array(
            "success" => 0,
            "campo" => "",
            "error" => ""
        );
        if ($Login->UserName == "") {
            $response["campo"] = "usuario";
            //$response["error"] = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Usuario es Obligatorio</div>";
            $response["error"] = "<div style='text-align:center;font-weight:bold;'>El Usuario es Obligatorio</div>";
        } else if ($Login->Password == "") {
            $response["campo"] = "password";
            $response["error"] = "<div style='text-align:center;font-weight:bold;'>La Contrase&ntilde;a es obligatorio</div>";
        } else {
            $user = $this->objLogin->LoginBD($Login->UserName, $Login->Perfil);
            //print_r($user); exit;
            if (is_object($user)) {
                //$crypt = crypt ($Login->Password, $user->PASSWORD);
                $crypt = trim($Login->Password);
                if ($user->PASSWORD == $crypt) {
                    $vPerfil = $this->objLogin->getPerfil($user->id_perfil);
                    $vDataEstadistica = $this->objSeguridad->getEstadistica('2024');
                    
                    $session = array(
                        'ID' => $user->ID,
                        'NOMBRE' => $user->NOMBRE,
                        'APELLIDOS' => $user->APELLIDOS,
                        'PROFCOD' => $user->profcod,
                        'FAMCOD' => $user->famcod,
                        'EMAIL' => $Login->UserName,
                        'USUCOD' => $user->usucod,
                        'PERFIL' => $vPerfil,
                        'IDPERFIL' => $user->id_perfil,
                        'FLG_CAMBIO' => $user->flg_cambio,
                        'is_logged_in' => TRUE,
                        'NIVEL' => $user->nivel,
                        'ESTADISTICA' => $vDataEstadistica
                    );

                    $vDataMenu = $this->objLogin->menuPadre($user->id_perfil);
                    $arrDataMenu = array();
                    /*  echo "<pre>";
                      print_r($vDataMenu );
                      echo "</pre>"; exit; */

                    foreach ($vDataMenu as $itemPadre) {
                        $dataHijo = $this->objLogin->menuHijo($user->id_perfil, $itemPadre->id_menu);
                        /*     echo "<pre>";
                          print_r($dataHijo );
                          echo "</pre>"; */
                        $arrDataMenu[] = array(
                            'arrpadre' => $itemPadre,
                            'arrhijo' => $dataHijo
                        );
                    }
                    /* echo "<pre>";
                      print_r ($arrDataMenu);
                      echo "</pre>";
                      exit; */
                    // $this->session->sess_expiration = 60; // 1 Hora de Sesion
                    $this->objSeguridad->registraAcceso($user->usucod); // registramos el acceso
                    $session['vMenu'] = $arrDataMenu; //cargamos la sesion del menu de acuerdo a los permisos                   
                    $this->nativesession->set('arrDataSesion', $session); //Cargamos la sesion de datos del usuario logeado
                    $this->nativesession->set('S_ANO_VIG', 2024); // Cargamos en la variable el año actual
                    $_SESSION["demo"] = $session;
                    /* echo "Logged : ".$this->nativesession->get ('arrDataSesion')['is_logged_in'];
                      exit; */
                    /* echo "Logged : ".$this->nativesession->get ('arrDataSesion')['is_logged_in'];
                      exit;
                     */
                    //$datasession = $this->nativesession->get ('arrDataSesion');
                    /*
                      echo "<pre>";
                      print_r($datasession);
                      echo "</pre><br>";
                      echo "Perfil : ".$datasession['IDPERFIL'];
                      exit;
                     */

                    $response["success"] = 1;
                    $response["error"] = "<div style='text-align:center;font-weight:bold;'>Credenciales Correcta.! </div>";
                } else {
                    $response["error"] = "<div style='text-align:center;font-weight:bold;'>La Contrase&ntilde;a  es Invalida  </div>";
                }
            } else {
                $response["error"] = "<div style='text-align:center;font-weight:bold;'>El Usuario y/o Clave es Incorrecta. </div>";
            }
        }
        echo json_encode($response);
    }

}
