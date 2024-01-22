<?php

if (!defined ('BASEPATH'))
    exit ('No direct script access allowed');
date_default_timezone_set ('America/Mexico_City');

class sweb_datos_familia extends CI_Controller
{

    public $datasession = '';
    
    function __construct ()
    {
        parent::__construct ();
        $this->load->model ('usuarios_model');
        $this->datasession = $this->nativesession->get ('arrDataSesion');
    }

    public function index ()
    {
        $vUsuCod = $this->datasession ['FAMCOD'];
        $vPerfil = '';
        $data["usuarios"] = $this->usuarios_model->buscarUsuarioxPerfil ($vUsuCod, $vPerfil);
        $data["titulo"] = "Modificar Datos Personales";
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('js_datos_familia');
        $this->load->view ('view_datos_familia', $data);
        $this->load->view ('view_footer');
    }

    public function save ()
    {
        sleep(1);
        $Usuarios = json_decode ($this->input->post ('UsuariosPost'));
        $response = array(
            "campo" => "",
            "error_msg" => ""
        );
        if ($Usuarios->Nombre == "") {
            $response["campo"] = "Nombre";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Nombre es Obligatorio</div>";
        } else if ($Usuarios->Apellidos == "") {
            $response["campo"] = "Apellidos";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Apellido es obligatorio</div>";
        } else if ($Usuarios->Email == "") {
            $response["campo"] = "email";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Correo Es Obligatorio</div>";
        }  else if ($Usuarios->Password1 == "") {
            $response["campo"] = "password1";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Contraseña Es Obligatorio</div>";
        } else if ($Usuarios->Password2 == "") {
            $response["campo"] = "password2";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La confirmación de Contraseña Es Obligatorio</div>";
        }  else if ($Usuarios->Password1 != $Usuarios->Password2) {
            $response["campo"] = "password2";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La confirmación de Contraseña Es Incorrecta</div>";
        } else {
                $newPassword = $Usuarios->Password1;
                $newPassword = strlen ($newPassword);
                if ($newPassword >= 20) {
                    $newPassword = $Usuarios->Password1;
                } else {
                    $newPassword = crypt ($Usuarios->Password1);
                }
                $UpdateUser = array(
                    'NOMBRE' => ucwords ($Usuarios->Nombre),
                    'APELLIDOS' => ucwords ($Usuarios->Apellidos),
                    'EMAIL' => $Usuarios->Email,
                    'PASSWORD' => $newPassword,
                    'CLAVE' => $Usuarios->Password1,
                    'FECHA_MODIFICA' => date('Y-m-d H:i:s')
                );
                $this->usuarios_model->UpdateUsers ($UpdateUser, $Usuarios->Id);
                $response["error_msg"] = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
            
        }
        echo json_encode ($response);
    }

}
