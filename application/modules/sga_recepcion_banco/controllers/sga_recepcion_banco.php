<?php

/**
 * @package       modules/sga_recepcion_banco/controller
 * @name            sga_recepcion_banco.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2018 SISTEMAS-DEV
 * @version         1.0 - 2018/03/20
 */
class sga_recepcion_banco extends CI_Controller {

    public $token = '';
    public $_session = '';
    public $modulo = 'RECEPCION-BANCO';
    public $ano = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('seguridad_model');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('alumno_model', 'objAlumno');
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {

        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $valucod = $this->objAlumno->getCodigoAlumno("77399047");
        //print_r($valucod);  exit;
        $data['token'] = $this->token();
        $data['ano'] = $this->ano;
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_recepcion_banco', $data);
        $this->load->view('js_recepcion');
        $this->load->view('view_footer');
    }

    public function cargarFile() {
        sleep(5);
        $flag = 0;
        $nombre_archivo = $_FILES['txtfile']['name'];
        $vBanco = $_POST['cbBanco']; // tipo de banco
        $vRazon = $_POST['cbRazon']; // tipo de razon social
        $numCtaEmp = "2358217";
        $nombank = "";
        switch ($vBanco) {
            case 'B1':
                $nombank = "BBVA";
                break;
            case 'B2':
                $nombank = "BCP";
                break;
            default :
                $nombank = "";
                break;
        }

        $vparte = explode(".", $nombre_archivo);
        $vparteRazon = explode("-", $vRazon);
        $fechaReg = date('Ymd-H:i:s');
        $vtpcomp = '';
        $vtprazon = '';
        $vcadErrors = '';
        $arrAuditoria = array('logAuditoria' => '');
        $vtpnivel = (($vparteRazon[0] == '01') ? 'P' : 'S');
        if ($vparteRazon[0] == '01') { // primaria
            $vtpcomp = '01';
            $vtprazon = 'R01';
        } else { // secundaria
            $vtpcomp = '02';
            $vtprazon = 'R02';
        }

        $vFileNew = $vparte[0] . '_' . $vtpnivel . '_' . $fechaReg . '.txt';
        $directorio = 'Bancos/' . $nombank . '/Recepcion/';
        $vusu = $this->_session['USUCOD'];
        /* echo "Directorio : " . $directorio . "<br>";
          echo "Archivo : " . $vFileNew . "<br>";
          exit; */
        if (move_uploaded_file($_FILES['txtfile']['tmp_name'], $directorio . $vFileNew)) {
            $vfileRed = $directorio . $vFileNew;
            $archivo = fopen($vfileRed, "r") or die("Problema al abrir el archivo.txt");
            $filas = 0;
            while (!feof($archivo)) {
                $filas++;
                $traer = fgets($archivo);
                $vLinea = nl2br($traer);
                if ($vLinea == 1) { // la primera linea 
                    if ($vBanco === "B1") {  //BBVA
                        $vruc = substr($vLinea, 2, 11);
                        if ($vparteRazon[1] != $vruc) {
                            $flag = 2;
                            break;
                        }
                    } elseif ($vBanco == 'B2') { // BCP
                        $vcuentaEmpresa = substr($vLinea, 6, 7);
                        if ($numCtaEmp != $vcuentaEmpresa) {
                            $flag = 2;
                            break;
                        }
                    }
                } else { // las demas lineas
                    if ($vBanco === "B1") {  //BBVA
                        if (substr($vLinea, 0, 2) == '02') { // si son registros de pagos
                            $vdni = substr($vLinea, 32, 8);
                            $vmes = trim(substr($vLinea, 40, 10));
                            $vmes = $this->getIdMes($vmes);
                            $vBoucher = substr($vLinea, 129, 6);
                            $vcadFecha = substr($vLinea, 135, 8);
                            $vmonto = substr($vLinea, 90, 3);
                            $vFecha = substr($vcadFecha, 0, 4) . '-' . substr($vcadFecha, 4, 2) . '-' . substr($vcadFecha, 6, 2);
                            $arrDataUp = array(
                                'montocob' => $vmonto,
                                'estado' => 'C',
                                'montopen' => 0,
                                'numrecibo' => $vBoucher,
                                'fecmod' => $vFecha . date("H:i:s"),
                                'usumod' => $vusu,
                                'tipopago' => 'B',
                                'tipo_comp' => $vtpcomp,
                                'tipo_razon' => $vtprazon
                            );
                            $valucod = $this->objAlumno->getCodigoAlumno($vdni);
                            $vres = $this->objCobros->updatePensionCarga($arrDataUp, $valucod, $vmes);
                        }
                    } elseif ($vBanco == 'B2') { // BCP                        
                        if (substr($vLinea, 0, 2) == 'DD') { // si son registros de pagos
                            $vdni = substr($vLinea, 19, 8);
                            $vmesdes = trim(substr($vLinea, 28, 10));
                            $vmes = $this->getIdMes($vmesdes);
                            $vBoucher = substr($vLinea, 124, 6);
                            $vcadFecha = substr($vLinea, 57, 8);
                            $vmonto = (int) substr($vLinea, 113, 3); // ver la forma cuando trae monto de 2 digitos como 85
                            $vFecha = substr($vcadFecha, 0, 4) . '-' . substr($vcadFecha, 4, 2) . '-' . substr($vcadFecha, 6, 2);
                            // ==================== Generamos en numero del Comprobante ===========================
                            $datoAlumno = $this->objAlumno->getDatosAlumno($vdni);
                            if ($datoAlumno) {
                                $instru = $datoAlumno->instrucod;
                                if ($instru == 'I' || $instru == 'P') {
                                    $vruc = RUC_PRIMARIA;
                                    $tipoRazon = '01';
                                    $vtpcomp = '02';
                                    $vtprazon = 'R01';
                                } else {
                                    $vruc = RUC_SECUNDARIA;
                                    $tipoRazon = '02';
                                    $vtpcomp = '02';
                                    $vtprazon = 'R02';
                                }
                                $vNumrecibo = "";
                                $valucod = $this->objAlumno->getCodigoAlumno($vdni);
                                $cobrado = $this->objCobros->getpagoAlumno($valucod, $vmes, $this->ano);
                                $vusu = 'USER'; // Por defecto USER. Si entras como SISTEMAS generara DEMO-999999999
                                //=====================================================================================                          
                                if ($cobrado) {
                                    if ($vusu == ADMIN) {
                                        $vNumrecibo = "DEMO-99999999";
                                    } else {
                                        $cadGenerado = $this->objCobros->getGeneraNumero($vruc, "02", $tipoRazon);
                                        $vNumrecibo = $cadGenerado[0]->codigoGenerado;
                                    }
                                    $arrDataUp = array(
                                        'montocob' => $vmonto,
                                        'estado' => 'C',
                                        'montopen' => 0,
                                        'numrecibo' => $vNumrecibo,
                                        'voucher' => $vBoucher,
                                        'fecmod' => $vFecha.' '.date('H:i:s'),
                                        'usumod' => $vusu,
                                        'tipopago' => 'B', // Origen de Cobro
                                        'idmedio' => 5, // 5 (RECAUDO BCP)
                                        'tipo_comp' => $vtpcomp,
                                        'tipo_razon' => $vtprazon
                                    );

                                    if ($vusu == ADMIN) {
                                        $mensaje = "Pago procesado correctamente (DEMO).";
                                    } else {
                                        $mensaje = "Pago procesado correctamente.";
                                        $vres = $this->objCobros->updatePensionCarga($arrDataUp, $valucod, $vmes);
                                        if ($vres) {
                                            $dataControl = array(
                                                'usuret' => $vusu,
                                                'fecharet' => date("Y-m-d H:i:s"),
                                                'flgret' => 1
                                            );
                                            $this->objCobros->actualiza_control($dataControl, $vNumrecibo);
                                        }
                                    }
                                    $arrAuditoria['logAuditoria'][] = array(
                                        'alumno' => $datoAlumno->nomcomp,
                                        'fecha' => $vFecha,
                                        'voucher' => $vBoucher,
                                        'periodo' => $vmesdes,
                                        'recibo' => $vNumrecibo,
                                        'mensaje' => $mensaje,
                                        'status' => 'PROCESADO'
                                    );
                                } else {
                                    //$vcadErrors .= "No registra pago pendiente en el mes [" . $vmes . "] del año " . $this->ano . " . Linea " . ($filas + 1) . "*";
                                    $arrAuditoria['logAuditoria'][] = array(
                                        'alumno' => $datoAlumno->nomcomp,
                                        'fecha' => $vFecha,
                                        'voucher' => $vBoucher,
                                        'periodo' => $vmesdes,
                                        'recibo' => '0000000000',
                                        'mensaje' => 'No registra pago pendiente en el mes ' . $vmesdes . ' del año ' . $this->ano . ' Verificar linea ' . $filas,
                                        'status' => 'ERROR'
                                    );
                                }
                            } else {
                                //$vcadErrors .= "Error en numero de DNI no existe en la BD. Linea " . ($filas + 1) . "*";
                                $arrAuditoria['logAuditoria'][] = array(
                                    'alumno' => 'Alumno no existente en la BD',
                                    'fecha' => $vFecha,
                                    'voucher' => '00000000',
                                    'periodo' => 'NINGUNO',
                                    'recibo' => '00000000',
                                    'mensaje' => 'Error en número de DNI no existe en la BD.  Verificar linea ' . $filas,
                                    'status' => 'ERROR'
                                );
                            }
                        }
                    }
                }
            }
            $flag = 1;
        }
        echo '<script>window.top.window.terminoCarga(' . $flag . ',' . json_encode($arrAuditoria) . '); </script> ';
    }

    public function getIdMes($vdesmes = '') {
        $vidmes = '';
        $vdesmes = trim($vdesmes);
        switch ($vdesmes) {
            case 'MARZO':
                $vidmes = '03';
                break;
            case 'ABRIL':
                $vidmes = '04';
                break;
            case 'MAYO':
                $vidmes = '05';
                break;
            case 'JUNIO':
                $vidmes = '06';
                break;
            case 'JULIO':
                $vidmes = '07';
                break;
            case 'AGOSTO':
                $vidmes = '08';
                break;
            case 'SETIEMBRE':
                $vidmes = '09';
                break;
            case 'OCTUBRE':
                $vidmes = '10';
                break;
            case 'NOVIEMBRE':
                $vidmes = '11';
                break;
            case 'DICIEMBRE':
                $vidmes = '12';
                break;
        }
        return $vidmes;
    }

    public function procesarFile() {
        
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
