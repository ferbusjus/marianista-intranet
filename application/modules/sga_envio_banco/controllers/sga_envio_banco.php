<?php

/**
 * @package       modules/sga_envio_banco/controller
 * @name            sga_envio_banco.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2018 SISTEMAS-DEV
 * @version         1.0 - 2018/01/20
 */
class sga_envio_banco extends CI_Controller {

    public $token = '';
    public $modulo = 'ENVIO-BANCO';

    public function __construct() {
        parent::__construct();
        $this->load->model('seguridad_model');
        $this->load->model('cobros_model', 'objCobros');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_envio_banco', $data);
        $this->load->view('js_envio');
        $this->load->view('view_footer');
    }

    public function procesarFile() {
        $inputRazon = $this->input->post("vIdRazon");
        $vIdTipo = $this->input->post("vIdTipo");
        $vIdBanco = $this->input->post("vIdBanco");
        $vIdPeriodo = $this->input->post("vIdPeriodo");
        $vfini = $this->input->post("vfini");
        $vffin = $this->input->post("vffin");

        if (trim($inputRazon) == '') {
            $vIdRazon = "20517718778"; // Por defecto Primaria
            $vtipo = '01';
        } else {
            $datosRazon = explode("-", $inputRazon);
            $vtipo = $datosRazon[0];
            $vIdRazon = $datosRazon[1];
        }

        if ($vIdBanco == "B1") {
            // ===== Obtenemos los datos de los Pagos ================
            $dataPagos = $this->objCobros->getPagoEnvioBanco($vtipo, $vIdTipo);
            $vcont = count($dataPagos);
            // =====================================================            
            $vletra = (($vIdTipo == '01') ? 'E' : 'T');
            // El primer envió del DÍA comenzará con “000”, si se hicieran mas de una vez el mismo día se colocar el consecutivo “001”,etc. 
            // En caso de hacer un envío por día se colocará “000”.
            $numEnvio = "000";
            // Numero de pago
            $numclase = "000";
            $fecbloq = "20301231";
            $vtotal = 0;
            // RUC
            // PRIMARIA : 20517718778
            // SECUNDARIA : 20556889237
            $blancoCabcera = str_pad('', 322, ' ', STR_PAD_RIGHT);
            $vcadenaHead = trim("01" . $vIdRazon . $numclase . "PEN") . date("Ymd") . $numEnvio . "       " . $vletra . $blancoCabcera . "o_o";
            $vcadenaBody = "";
            foreach ($dataPagos as $row) {
                $vcadenaBody .= "02" . str_pad(substr($this->cleanCharacterSpecials(trim($row->nomcomp)), 0, 30), 30) . $row->dni . str_pad($row->periodo, 40) . $row->fechaven . $fecbloq . str_pad($row->monto, 15, '0', STR_PAD_LEFT) . str_pad($row->monto, 15, '0', STR_PAD_LEFT) . str_pad('', 182, '0') . "L" . str_pad('', 15, '0') . str_pad('', 36, ' ') . "o_o";
                $vtotal += $row->monto;
            }
            $vcadenaFood = "03" . str_pad($vcont, 9, '0', STR_PAD_LEFT) . str_pad($vtotal . '00', 18, '0', STR_PAD_LEFT) . str_pad($vtotal . '00', 18, '0', STR_PAD_LEFT) . str_pad('', 18, '0') . str_pad('', 295, ' ') . "o_o";
            $vcadenaComp = $vcadenaHead . $vcadenaBody . $vcadenaFood;
        } elseif ($vIdBanco == "B2") {
            // ===== Obtenemos los datos de los Pagos ================
            $dataPagos = $this->objCobros->getPagoEnvioBancoBCP($vIdTipo, $vtipo, $vIdPeriodo, $vfini, $vffin);
            $vcont = count($dataPagos);
            // =====================================================            
            $blancoCabcera = str_pad('', 157, ' ', STR_PAD_RIGHT);
            if($vtipo=='01'){ // primaria
                $cuentaEA = 2555188;
                $iniCuenta = 1930;
                $razon = "CCORPORACION EDUCATIVA COLEGIO MARIANISTA";
            } else {
                $razon = "COLEGIO MARIANISTAS DE V.M.T. S.A.C.     ";
                $cuentaEA = 2358217;
                $iniCuenta = 1940;
            }
            
            $vtotal = 0;
            $vletraCC = "A"; //Tipo de Archivo (“ ” o R = Archivo de Reemplazo, A = Archivo de Actualización)
            $vletraDD = ($vIdTipo=='01') ?"E" :"A"; //Tipo de registro de actualización (A = Registro a Agregar, M = Registro a Modificar, E = Registro a Eliminar)
            $vcadenaBody = "";
            $dnipago = "42397366"; //Nro. Documento de Pago
            foreach ($dataPagos as $row) {
                $vcadenaBody .= "DD".$iniCuenta . $cuentaEA . str_pad($row->dni, 14, '0', STR_PAD_LEFT) . str_pad(substr($this->cleanCharacterSpecials(trim($row->nomcomp)), 0, 40), 40) . str_pad($row->periodo, 30, ' ', STR_PAD_RIGHT) . date("Ymd") . $row->fechaven . str_pad($row->monto . "00", 15, '0', STR_PAD_LEFT) . str_pad("", 15, '0', STR_PAD_LEFT) . str_pad("", 9, '0', STR_PAD_LEFT) . $vletraDD . str_pad($row->dnipago, 20, '0', STR_PAD_LEFT) . str_pad('', 77, ' ') . "o_o";
                $vtotal += $row->monto;
            }
            $vcadenaHead = trim("CC".$iniCuenta . $cuentaEA . $razon . date('Ymd') . str_pad($vcont, 9, '0', STR_PAD_LEFT) . str_pad($vtotal . "00", 15, '0', STR_PAD_LEFT) . $vletraCC . "000000") . $blancoCabcera . "o_o";
            $vcadenaComp = $vcadenaHead . $vcadenaBody;
        }

        $output = array(
            "flg" => 1,
            "msg" => "EL ARCHIVO SE GENERO CORRECTAMENTE.\nPRESIONE EL BOTON DESCARGAR.",
            "cadena" => $vcadenaComp
        );
        echo json_encode($output);
    }

    public function generarFile() {
        if (isset($_POST['vCadena'])) {
            $vCadena = $this->input->post("vCadena");
            $vIbanco = $this->input->post("vBanco");
            $vIdTipo = $this->input->post("vIdTipo");
            $vIdPeriodo = $this->input->post("vIdPeriodo");

            $nombank = "";
            $nomArchi = "";
            $nomdate = "";
            switch ($vIbanco) {
                case 'B1':
                    $nombank = "BBVA";
                    break;
                case 'B2':
                    $nombank = "BCP";
                    if ($vIdTipo == '01') { // Cobros de Pensiones
                        $nomArchi = "CREP2358217P";
                        $nomdate = date("Ymd");
                    } elseif ($vIdTipo == '03') { // Cobros mensuales
                        $nomArchi = "CREP2358217M";
                        $nomdate = nombreMesesCompleto($vIdPeriodo);
                    }
                    break;
                default :
                    $nombank = "NN";
                    $nomArchi = "NINGUNO";
                    $nomdate = "DATE";
                    break;
            }

            $uploaddir = 'Bancos/' . $nombank . '/Envios';
            if (!file_exists('Bancos/' . $nombank)) {
                if (!mkdir('Bancos/' . $nombank, 0777, true)) {
                    die('Fallo al crear carpetas...');
                    exit;
                }
            }

            if (!file_exists($uploaddir)) {
                if (!mkdir($uploaddir, 0777, true)) {
                    die('Fallo al crear carpetas...');
                    exit;
                }
            }

            $nameFile = $nomArchi . "_" . $nomdate . ".txt";
            $directorio = 'Bancos/' . $nombank . '/Envios/' . $nameFile;
            $fichero = fopen($directorio, "w+") or die("No se puede abrir el archivo");
            fwrite($fichero, str_replace("o_o", chr(13) . chr(10), $vCadena));
            fclose($fichero);
            $msg = "";
            if (is_readable($directorio)) {
                echo "<script> location.href = '" . BASE_URL . "sga_envio_banco/downloadFile/" . $nameFile . "/" . $nombank . "'; </script>";
                //$msg = 'ARCHIVO DESCARGADO.';
            } else {
                $msg = 'EL ARCHIVO NO SE PUEDE LEER.';
            }
        } else {
            $msg = 'ARCHIVO NO GENERADO.';
        }
        echo $msg;
        /* $output = array(
          "flg" => 1,
          "msg" => $msg,
          );
          echo json_encode ($output); */
    }

    public function downloadFile($file = '', $banco = '') {
        if (!isset($file) || empty($file)) {
            echo 'EL ARCHIVO NO EXISTE';
            exit();
        }
        $dirfile = BASE_URL . 'Bancos/' . $banco . '/Envios/' . $file;

        //var_dump($dirfile); exit;
        header("Content-type: application/force-download");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=$file");
        //$fp=fopen("$dirfile", "r");
        readfile($dirfile);
    }

    public function cleanCharacterSpecials($texto) {
        $a = array("á", "é", "í", "ó", "ú", "à", "è", "ì", "ò", "ù", "ä", "ë", "ï", "ö", "ü", "â", "ê", "î", "ô", "û", "ñ", "ç", "Á", "É", "Í", "Ó", "Ú", "À", "È", "Ì", "Ò", "Ù", "Ä", "Ë", "Ï", "Ö", "Ü", "Â", "Ê", "Î", "Ô", "Û", "Ñ", "Ç","´");
        $b = array("a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "n", "c", "A", "E", "I", "O", "U", "A", "E", "I", "O", "U", "A", "E", "I", "O", "U", "A", "E", "I", "O", "U", "N", "C","");
        $texto = str_replace($a, $b, $texto);
        return $texto;
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
