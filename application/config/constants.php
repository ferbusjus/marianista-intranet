<?php

if (!defined ('BASEPATH'))
    exit ('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define ('FILE_READ_MODE', 0644);
define ('FILE_WRITE_MODE', 0666);
define ('DIR_READ_MODE', 0755);
define ('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define ('FOPEN_READ', 'rb');
define ('FOPEN_READ_WRITE', 'r+b');
define ('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define ('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define ('FOPEN_WRITE_CREATE', 'ab');
define ('FOPEN_READ_WRITE_CREATE', 'a+b');
define ('FOPEN_WRITE_CREATE_STRICT', 'xb');
define ('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');



/* Url */
$base_url = ((isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://" . $_SERVER['HTTP_HOST'];
$base_url .= str_replace (basename ($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
/* Constantes del Sistema */
define ('BASE_URL', $base_url);

define ('INCLUDES_JS', BASE_URL . "include/js/");
define ('INCLUDES_CSS', BASE_URL . "include/css/");
define ('INCLUDES_IMAGES', BASE_URL . "include/images/");
define ('URL_FOTO_SOCIO', "app.segurossura.pe/fotos/");
define ('PLANTILLA_RESET_CLAVE', "http://sistemas-dev.com/plantillas/plantilla_clave.html");
define ('LIBRERIA', "fercmias_sistemasdev");
define ('LIBRERIA2', "fercmias_academico");
define ('LIBRERIA_BD_2020', "fercmias_academico_2020");
define ('LIBRERIA_BD_2021', "fercmias_academico_2021");
define ('LIBRERIA_BD_2022', "fercmias_academico_2022");
define ('LIBRERIA_BD_2023', "fercmias_academico_2023");
define ('SISINTRA', 3);
define ('SISEXTRA', 4);
define ('ANO_VIG', 2019); // año vigente (ya no se usa esta variable global)
define ('ANO_INICIO_PEN', 2018); // año que inicio pensiones en Marianista
define ('RUC_PRIMARIA', '20517718778');  
define ('RUC_SECUNDARIA', '20556889237');
define ('WEB', 'www.marianista.pe');
define ('TP_COMP', '02'); // Boletas
define ('ADMIN', 'SISTEMAS'); // Usuario de Pruebas
define ('API_RESUMEN_BOLETAS', 'http://sistemas-dev.com/sis_facturacion/api_facturacion/resumen_boletas.php'); 
/* End of file constants.php */
/* Location: ./application/config/constants.php */