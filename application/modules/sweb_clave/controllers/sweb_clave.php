<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');

class sweb_clave extends CI_Controller {

    public $datasession = '';

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios_model');
        $this->datasession = $this->nativesession->get('arrDataSesion');
    }

    public function form() {
        $vUsuCod = $this->datasession['USUCOD'];
        $vPerfil = '';
        $data["usuarios"] = $this->usuarios_model->buscarUsuarioxPerfil($vUsuCod, $vPerfil);
        $data["titulo"] = "Cambiar Clave";
        $this->load->view('constant');
        $this->load->view('js_usuario');
        $this->load->view('view_clave', $data);
    }

    public function index() {
        $vUsuCod = $this->datasession['FAMCOD'];
        $vPerfil = '';
        $data["usuarios"] = $this->usuarios_model->buscarUsuarioxPerfil($vUsuCod, $vPerfil);
        $data["titulo"] = "Cambiar Clave";
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('js_usuario');
        $this->load->view('view_cambio_clave', $data);
        $this->load->view('view_footer');
    }

    public function save() {
        sleep(1);
        $Usuarios = json_decode($this->input->post('UsuariosPost'));
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
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Correo es Obligatorio para que le llege las credenciales</div>";
        } else if (trim($Usuarios->Password1) == "") {
            $response["campo"] = "password1";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Contraseña Es Obligatorio</div>";
        } else if (trim($Usuarios->Password2) == "") {
            $response["campo"] = "password2";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Nueva Contraseña Es Obligatorio</div>";
        } else if (trim($Usuarios->Password1) == trim($Usuarios->Password2)) {
            $response["campo"] = "password2";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>No Puede colocar la misma Contraseña</div>";
        }
        /*  else if ($Usuarios->Password1 != $Usuarios->Password2) {
          $response["campo"] = "password2";
          $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La confirmación de Contraseña Es Incorrecta</div>";
          } */ else {
            $newPassword = $Usuarios->Password2;
            /*
              $newPassword = $Usuarios->Password1;
              $newPassword = strlen ($newPassword);
              if ($newPassword >= 20) {
              $newPassword = $Usuarios->Password1;
              } else {
              $newPassword = crypt ($Usuarios->Password1);
              }
             */
            $UpdateUser = array(
                'NOMBRE' => ucwords($Usuarios->Nombre),
                'APELLIDOS' => ucwords($Usuarios->Apellidos),
                'EMAIL' => $Usuarios->Email,
                'PASSWORD' => $newPassword,
                'CLAVE' => $Usuarios->Password1,
                'FECHA_MODIFICA' => date('Y-m-d H:i:s'),
                'FLG_CAMBIO' => 1
            );
            $vFamcod = $this->datasession['USUCOD'];
            $this->usuarios_model->UpdateUsers($UpdateUser, $Usuarios->Id);
            $res = $this->envioNotificacion($Usuarios->Apellidos, $vFamcod, $newPassword, $Usuarios->Email);
            /* $session = array(
              'FLG_CAMBIO' => 1
              ); */
            //$this->session->set_userdata ($session);
            $this->nativesession->set('FLG_CAMBIO_PASS', 1);
            //$this->nativesession->get ('arrDataSesion')['FLG_CAMBIO']=1;
            $response["campo"] = "SUCCES";
            $response["mail"] = $res;
            $response["error_msg"] = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
        }
        echo json_encode($response);
    }

    public function envioMasivoComunicado() {
        $message = '<p>&nbsp;</p>
                            <h3 style="text-align: center; color: #3f7320;"><span style="border-bottom: 4px solid #c82828;">COMUNICADO </span></h3>
                            <!-- Este comentario es visible solo en el editor fuente -->
                            <p><strong> Buenas Tardes Estimad@,</strong></p>
                            <p>El presente es para comunicarle que se ha vuelto habilitar la Intranet de Padres en donde podr&aacute; consultar de forma Online lo siguiente:</p>
                            <p></p>
                            <div style="margin-left: 10px;"><lu>
                            <li>Asistencia de su Hijos (Solo para el Nivel Secundaria)</li>
                            <li>Los Silabos cargados por los Profesores</li>
                            <li>Los pagos realizado (Boletas electr&oacute;nicas)</li>
                            <li>Boleta de Notas</li>
                            </lu></div>
                            <p></p>
                            <p>Para poder acceder a la Intranet deber&aacute; de solicitar su usuario y clave en Administraci&oacute;n e ingresar en la siguiente URL: <a target="_blank" rel="nofollow noopener" href="http://sistemas-dev.com/intranet">http://sistemas-dev.com/intranet</a></p>
                            <p>&nbsp;</p>
                            <center><img src="http://sistemas-dev.com/intranet/images/fondo.png" width="120px" alt="MARIANISTA" />
                            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; M&aacute;s que un colegio, Una gran Familia Marianista.</p>
                            </center>';
        $vmail = "";
        $dataEMail = $this->usuarios_model->getMailComunicado();
        $vMensajeEnvio = array();
        foreach ($dataEMail as $row) {

            $mail = new Mailer();
            $mail->CharSet = 'UTF-8';

            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'gator4201.hostgator.com';
            $mail->Port = 465;

            $mail->Username = 'info@sistemas-dev.com';
            $mail->Password = '965713193';
            $mail->SetLanguage('es');
            
            $mail->FromName = "Colegio Marianista";
            $mail->From = "info@sistemas-dev.com";
            $mail->Subject = "COMUNICADO - INTRANET DE PADRES";
            $mail->AddAddress($row->email);
            $mail->AddReplyTo('info@sistemas-dev.com', 'COLEGIO MARIANISTA');
            //$mail->addCC ("fercimas@gmail.com");
            $mail->msgHTML($message);
            $mail->IsHTML(true);
            if (!$mail->send()) {
                $vMensajeEnvio[] = array('email' => $row->email, 'msg' => $mail->ErrorInfo);
            } else {
                $vMensajeEnvio[] = array('email' => $row->email, 'msg' => 'El mensaje se envio Correctamente');
            }
            $mail->ClearAddresses();
        }
        echo "<lu>";
        foreach ($vMensajeEnvio as $msg) {
            echo "<li>Correo : " . $msg['email'] . " | Mensaje : " . $msg['msg'] . "</li>";
        }
        echo "</lu>";
        exit;
    }

    public function envioNotificacion($vfamilia = '', $vusuario = '', $vclave = '', $vmail = '') {
        $vResp = '';
        $message = file_get_contents(PLANTILLA_RESET_CLAVE);
        // Replace the % with the actual information
        $message = str_replace('%v_familia%', $vfamilia, $message);
        $message = str_replace('%v_usuario%', $vusuario, $message);
        $message = str_replace('%v_clave%', $vclave, $message);

        $mail = new Mailer();
        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'gator4201.hostgator.com';
        $mail->Port = 465;
        //$mail->SMTPDebug = 3; // para depurar el phpMailer

        $mail->Username = 'info@sistemas-dev.com';
        $mail->Password = '965713193';
        $mail->SetLanguage('es');
        $mail->FromName = "Colegio Marianista";
        $mail->From = "info@sistemas-dev.com";
        $mail->Subject = "SOLICITUD - CAMBIO DE CLAVE";
        $mail->AddAddress($vmail);
        $mail->AddReplyTo('info@sistemas-dev.com', 'SISTEMAS-DEV');
        $mail->addCC("info@sistemas-dev.com");
        $mail->msgHTML($message);
        $mail->IsHTML(true);
        if (!$mail->send()) {
            $vResp = 'Error: ' . $mail->ErrorInfo;
        } else {
            $vResp = 1;
        }
        return $vResp;
    }

}
