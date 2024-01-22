<?php

/**
 * @package       modules/sga_reporte_caja/controller
 * @name            sga_resumen_sunat.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2019 SISTEMAS-DEV
 * @version         1.0 - 2019/03/24
 */
class sga_resumen_sunat extends CI_Controller {

    public $S_ANO = '';
    public $token = '';
    public $modulo = 'RESUMEN-SUNAT';

    public function __construct() {
        parent::__construct();
        $this->load->model('empresa_model', 'objEmpresa');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('seguridad_model');
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataEmpresa"] = $this->objEmpresa->getEmpresa();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_resumen_sunat', $data);
        $this->load->view('js_resumen');
        $this->load->view('view_footer');
    }

    public function generaDataSunat() {
        if ($this->input->is_ajax_request()) {
            $vFecha = $_POST['vfecha'];
            $vRazon = $_POST['vrazon'];
            if ($vFecha != "") {
                $cadFecha = explode("-", $vFecha);
                $vFechaCadena = $cadFecha[0] . $cadFecha[1] . $cadFecha[2];
            } else {
                $vFechaCadena = date("Ymd");
            }
            $dataEmpresa = $this->objEmpresa->getDatosxEmpresa($vRazon);
           /* print_r($dataEmpresa);
              exit; */
            $ruta = API_RESUMEN_BOLETAS;
            // ========= 1. Creamos el bloque de la Emisor ========
            $arrEmisor = array(
                "ruc" => $dataEmpresa->ruc,
                "tipo_doc" => "6",
                "nom_comercial" => $dataEmpresa->nombre_comercial,
                "razon_social" => $dataEmpresa->razon_social,
                "codigo_ubigeo" => "140132", // Villa Maria
                "direccion" => $dataEmpresa->direccion,
                "direccion_departamento" => $dataEmpresa->departamento,
                "direccion_provincia" => $dataEmpresa->provincia,
                "direccion_distrito" => $dataEmpresa->distrito,
                "direccion_codigopais" => $dataEmpresa->codigo_pais,
                "usuariosol" => $dataEmpresa->usuario_sol,
                "clavesol" => $dataEmpresa->clave_sol,
                "pass_firma" => $dataEmpresa->certificado_key
            );
            // ======== 2. Creamos el Bloque Detalles ============
            $dataPagos = $this->objEmpresa->getDatosSunat($vFecha, $vRazon);
            //print_r($dataPagos); exit;
            $item = 1;
            $arrDetalle = array();
            foreach ($dataPagos as $pagos) {
                $arrDetalle[] = array(
                    "ITEM" => $item,
                    "TIPO_COMPROBANTE" => "03", // 01: Factura ,  03: Boletas
                    "NRO_COMPROBANTE" => $pagos->numrecibo,
                    "NRO_DOCUMENTO" => $pagos->dni,
                    "TIPO_DOCUMENTO" => "1",
                    "NRO_COMPROBANTE_REF" => "0",
                    "TIPO_COMPROBANTE_REF" => "0",
                    "STATUS" => "1", // 1 : OK -  3 : ANULADA
                    "COD_MONEDA" => "PEN",
                    "TOTAL" => $pagos->monto,
                    "GRAVADA" => "0",
                    "EXONERADO" => "0",
                    "INAFECTO" => "0",
                    "EXPORTACION" => "0",
                    "GRATUITAS" => "0",
                    "MONTO_CARGO_X_ASIG" => "0",
                    "CARGO_X_ASIGNACION" => "0",
                    "ISC" => "0",
                    "IGV" => "0",
                    "OTROS" => "0"
                );   
                $item++;
            }
            // =====3. Generando Trama de SUNAT=======================
            $data = array(
                //Cabecera del documento
                "codigo" => "RC",
                "serie" => $vFechaCadena,
                "secuencia" => '01', // valor por defecto 01. Si se desea enviar mas resumenes por dia ..colocar aqui el numero de secuencia
                "fecha_referencia" => $vFecha,
                "fecha_documento" => $vFecha,
                //data de la empresa emisora o contribuyente que entrega el documento electrónico.
                "emisor" => $arrEmisor,  
                //items
                "detalle" => $arrDetalle
            );
           /*echo "<pre>";
              print_r($data);
              echo "</pre>";
              exit; */
            //Invocamos l servicio
            $token = ''; //en caso quieras utilizar algún token generado desde tu sistema
            //codificamos la data
            $data_json = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
                    )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta = curl_exec($ch);
            curl_close($ch);

            $resp = $this->objEmpresa->updateEnvios($vFecha, $vRazon);
            echo $respuesta;
            exit();

            /* $response = json_decode($respuesta, true);  
              echo "=========== DATA RETORNO =============== ";
              echo "<br /><br />respuesta	: " . $response['respuesta'];
              echo "<br /><br />url_xml	: " . $response['url_xml'];
              echo "<br /><br />hash_cpe	: " . $response['hash_cpe'];
              echo "<br /><br />hash_cdr	: " . $response['hash_cdr'];
              echo "<br /><br />msj_sunat	: " . $response['msj_sunat'];
              echo "<br /><br />ruta_cdr	: http://sistemadefacturacionelectronicasunat.com/sis_facturacion/" . $response['ruta_cdr'];
              echo "<br /><br />ruta_pdf	: " . $response['ruta_pdf']; */
        }
    }

    public function generarNumero($longitud) {
        $key = '';
        $pattern = '1234567890';
        $max = strlen($pattern) - 1;
        for ($i = 0; $i < $longitud; $i++)
            $key .= $pattern{mt_rand(0, $max)};
        return $key;
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            $txtFecha = $_POST['vfecha'];
            $txtRazon = $_POST['vrazon'];
            $output = array();
            $arrData = array();
            $data = $this->objEmpresa->get_datatables();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    if ($fila->flgenvio == '1') {
                        $vimg = base_url() . "img/pagado.png";
                    } else {
                        $vimg = base_url() . "img/Pendientes.png";
                    }
                    //$conf .= "&nbsp;<span style='font-size:15px; color:red;cursor:pointer;' onclick=cursor:pointer; class='glyphicon glyphicon-ban-circle'  data-toggle='tooltip' title='Bloquear'></span>";
                    $img = '<img src="' . $vimg . '" width="20px" heigth="20px"  />';
                    $arrData [] = array(
                        "numrecibo" => $fila->numrecibo,
                        "familia" => $fila->famdes,
                        "alumno" => $fila->nomcomp,
                        "concepto" => ($fila->condes . ' - ' . $fila->mesdes . ' - ' . $this->S_ANO),
                        "fecreg" => invierte_date(substr($fila->fecmod, 0, 10)),
                        "cobrado" => $fila->montocob,
                        //  "moneda" => 'SOLES',
                       // "aula" => $fila->nemodes,
                        "envio" => $img
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objEmpresa->count_all($txtFecha, $txtRazon),
                    "recordsFiltered" => $this->objEmpresa->count_filtered(),
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

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
