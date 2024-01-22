<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    // Incluimos el archivo fpdf
    require_once APPPATH."/third_party/PHPMailer/class.phpmailer.php";
    require_once  APPPATH."/third_party/PHPMailer/PHPMailerAutoload.php";
    //Extendemos la clase Pdf de la clase fpdf para que herede todas sus variables y funciones
    class Mailer extends PHPMailer {
        public function __construct() {
            parent::__construct();
        }
    }