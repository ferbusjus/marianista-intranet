<?php

/**
 * @package       modules/sga_pagos/controller
 * @name            sga_pagos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 2017/07/09
 */
class swp_pagos_test extends CI_Controller {

    public $token = '';
    public $modulo = 'PAGOS';
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
        /* if ($this->seguridad_model->restringirIp() == FALSE) {
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('view_default');
          $this->load->view('view_footer');
          } else { */
        /* if (!$this->seguridad_model->verificarHorario()) {
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('view_bloqueo');
          $this->load->view('view_footer');
          } else { */
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data['usuario'] = $this->_session['USUCOD'];
        $data["dataSalones"] = $this->objSalon->getSalones();
        $data["ano"] = $this->ano;
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('js_pagos');
        $this->load->view('view_lista', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function adicional() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $data['token'] = $this->token();
        //$data["dataSalones"] = $this->objSalon->getSalones ();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('js_adicional');
        $this->load->view('view_lista_adiconal');
        $this->load->view('view_footer');
    }

    public function getDocumento() {
        $output = array();
        $flgArr = 0;
        if ($this->input->is_ajax_request()) {
            $vTipo = $this->input->post("vTipo");
            $vIdnemo = $this->input->post("vidnemo");
            $vflag = (isset($_POST['flag']) ? $_POST['flag'] : '0');
            $dataSalon = $this->objSalon->getSalones($vIdnemo, $vflag);
            $instru = $dataSalon[0]->INSTRUCOD;
            if ($instru == 'I' || $instru == 'P') {
                $vruc = RUC_PRIMARIA;
                $tipoRazon = '01';
            } else {
                $vruc = RUC_SECUNDARIA;
                $tipoRazon = '02';
            }
            $vusu = $this->_session['USUCOD'];
            if ($vusu == ADMIN /* || $this->ano == 2018 */) {
                if ($vTipo == '01')
                    $genCod = "R001-99999999";
                if ($vTipo == '02')
                    $genCod = "B001-99999999";
                if ($vTipo == '03')
                    $genCod = "F001-99999999";
            } else {
                $cadGenerado = $this->objCobros->getGeneraNumero($vruc, $vTipo, $tipoRazon);
                $genCod = $cadGenerado[0]->codigoGenerado;
                /* $dataControl = array(
                  'id' => '',
                  'flgreg' => 1,
                  'idrazon' => $tipoRazon,
                  'tipo_comp' => $vTipo,
                  'numrecibo' => $genCod,
                  'usureg' => $vusu
                  );
                  $this->objCobros->graba_control($dataControl); */
                //$genCod ="0001-99999999";
            }
            $output = array(
                "gencod" => $genCod,
            );
        }
        echo json_encode($output);
    }

    public function wsreniec() {
        $url = "http://181.65.245.150/ProyectoPcrWeb/ConsultaAfiliacionControlador?TipoDocumento=0&numeroDocumento=44737462&tipoConsulta=2";
        $archivo_XML = file_get_contents($url);
        $dataws = array();

        if (empty($archivo_XML)) {
            $dataws = array("error" => "1", "mensaje" => "No se pudo conectar al Servicio Reniec.", "data" => array());
        } else {

            preg_match_all("|<DatosReniec>(.*)</DatosReniec>|sU", $archivo_XML, $items);
            $listar_nodos = array();
            if (!empty($items)) {

                foreach ($items[1] as $key => $item) {

                    preg_match("|<ApellidoPaterno>(.*)</ApellidoPaterno>|s", $item, $paterno);
                    preg_match("|<ApellidoMaterno>(.*)</ApellidoMaterno>|s", $item, $materno);
                    preg_match("|<PrimerNombre>(.*)</PrimerNombre>|s", $item, $nombre1);
                    preg_match("|<SegundoNombre>(.*)</SegundoNombre>|s", $item, $nombre2);

                    preg_match("|<Direccion>(.*)</Direccion>|sU", $archivo_XML, $itemdirec);
                    preg_match("|<Ubigeo>(.*)</Ubigeo>|sU", $archivo_XML, $ubigueo);
                    echo "aqui";
                    exit;
                    $listar_nodos[$key]['paterno'] = $paterno[1];
                    $listar_nodos[$key]['materno'] = $materno[1];
                    $listar_nodos[$key]['nombre1'] = $nombre1[1];
                    $listar_nodos[$key]['nombre2'] = (($nombre2) ? $nombre2[1] : "");
                    $cadena1 = str_replace("<Direccion>", "", $itemdirec[0]);
                    $cadena1 = str_replace("</Direccion>", "", $cadena1);
                    $cadena1 = str_replace("<PrefijoDireccion/>", "", $cadena1);
                    $cadena2 = str_replace("<Ubigeo>", "", $ubigueo[0]);
                    $cadena2 = str_replace("</Ubigeo>", "", $cadena2);
                    $listar_nodos[0]['direccion'] = trim($cadena1);
                    $listar_nodos[0]['ubigueo'] = trim($cadena2);
                }
                $dataws = array("error" => "0", "mensaje" => "Datos devueltos correctamente.", "data" => $listar_nodos);
            } else {
                $dataws = array("error" => "2", "mensaje" => "No se encontro respuesta para el DNI", "data" => array());
            }
        }
        header('Content-Type: application/json');
        echo json_encode($dataws);
    }

    public function srvreniec() {
        $vnumdocu = $this->input->post("num_documento");
        $vtipo = $this->input->post("tipo");
        // El servico es de 3 Meses  | Inicio el 13.12.2021 Hasta 14/04/2022
        $password = 'fergus_aLKJHLKJ876987ADSFGASDF';
        if ($vtipo == 'dni') {
            $ruta = "https://facturalahoy.com/api/persona/" . $vnumdocu . '/' . $password;
        } elseif ($vtipo == 'ruc') {
            $ruta = "https://facturalahoy.com/api/empresa/" . $vnumdocu . '/' . $password;
        } else {
            $resp['repsuesta'] = 'error';
            echo json_encode();
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $ruta,
            CURLOPT_USERAGENT => 'Consulta Datos'
        ));

        $resp = curl_exec($curl);
        echo $resp;
        curl_close($curl);
        exit();
    }

    public function getComprobantes() {
        $output = array();
        $flgArr = 0;
        if ($this->input->is_ajax_request()) {
            $vIdAlumno = $this->input->post("vIdAlumno");
            $arrData = $this->objCobros->getBoletasxAlumno($vIdAlumno);
            $output = array(
                "data" => $arrData);
        } else {
            $output = array(
                "data" => '');
        }
        echo json_encode($output);
    }

    public function updateDatosApoderado() {
        $output = array();
        $vdni = $this->input->post("dni");
        $vtipo = $this->input->post("tipo");
        $vpaterno = $this->input->post("paterno");
        $vmaterno = $this->input->post("materno");
        $vnombre = $this->input->post("nombres");
        $vfamcod = $this->input->post("vfamcod");
        $resp = $this->objFamilia->ActualizaDatosApoderado($vfamcod, $vtipo, $vdni, $vpaterno, $vmaterno, $vnombre);
        if ($resp) {
            $output = array("msg" => "OK, DATOS MODIFICADOS CORRECTAMENTE.");
        } else {
            $output = array("msg" => "ERROR, NO SE MODIFICO CORRECTAMENTE EL APODERADO.");
        }
        echo json_encode($output);
    }

    public function marcarApoderados() {
        $output = array();
        $vfamcod = $this->input->post("vfamcod");
        $vdni = $this->input->post("vdni");
        $valor = $this->input->post("valor");
        $resp = $this->objFamilia->ActualizaMarcaApoderado($vfamcod, $vdni, $valor);
        // echo "Total :".count($resp);
        if (count($resp) > 0) {
            $output = array("msg" => "OK, APODERADO ASIGNADO CORRECTAMENTE.", "data" => $resp, "flgapo" => $valor);
        } else {
            $output = array("msg" => "ERROR, NO SE ASIGNO CORRECTAMENTE EL APODERADO.", "data" => "", "flgapo" => 0);
        }
        echo json_encode($output);
    }

    public function getApoderados() {
        $output = array();
        $vfamcod = $this->input->post("vfamcod");
        $arrData = $this->objFamilia->obtienePadresxAlumno($vfamcod);
        //  print_r($arrData);
        if (count($arrData) > 0) {
            $output = array(
                "data" => $arrData);
        } else {
            $output = array(
                "data" => '');
        }
        echo json_encode($output);
    }

    public function getPago() {
        $output = array();
        $flgArr = 0;
        if ($this->input->is_ajax_request()) {
            $vIdAlumno = $this->input->post("vIdAlumno");
            $varrCheck = $this->input->post("varrCheck");
            $vIdnemo = $this->input->post("vidnemo");
            // if ($varrCheck != "" && strlen ($varrCheck) > 5) {
            $varrCheck = substr($varrCheck, 0, -1);
            $vsqlMescobIn = "";
            $vsqlConcobIn = "";
            // if (strlen($varrCheck) > 5) {
            $flgArr = 1;
            $varrCheck = explode("*", $varrCheck);
            foreach ($varrCheck as $row) {
                $vParte = explode("|", $row);
                $vsqlMescobIn .= $vParte[1] . ",";
                $vsqlConcobIn .= $vParte[0] . ",";
            }
            //} else {
            ///   $vParte = explode("|", $varrCheck);
            //  $vsqlMescobIn .= $vParte[1];
            // $vsqlConcobIn .= $vParte[0];
            //}
            if ($vsqlMescobIn != "" && $vsqlConcobIn != "" /* && $flgArr == 1 */) {
                $vsqlMescobIn = substr($vsqlMescobIn, 0, -1);
                $vsqlConcobIn = substr($vsqlConcobIn, 0, -1);
            }
            /* echo "vsqlMescobIn : ".$vsqlMescobIn."<br>";
              echo "vsqlConcobIn : ".$vsqlConcobIn."<br>";
              exit; */
            $vDniAlumno = $this->objAlumno->getDniAlumno($vIdAlumno);
            //echo $vDniAlumno; exit;
            $dataFamilia = $this->objAlumno->getFamiliaAlumno($vDniAlumno);
            $flgDni = 1;
            $flgApo = "";
            if (trim($dataFamilia->flgpadapo) == '1') {
                //  if (trim($dataFamilia->paddni)!='') {
                $dataDni = $dataFamilia->paddni;
                $dataApo = $dataFamilia->padre;
                $dataPaterno = $dataFamilia->padapepat;
                $dataMaterno = $dataFamilia->padapemat;
                $dataNombre = $dataFamilia->padnombre;
                $flgApo = "P";
                $famcod = $dataFamilia->famcod;
            } elseif (trim($dataFamilia->flgmadapo) == '1') {
                //} elseif (trim($dataFamilia->maddni)!='') {
                $dataDni = $dataFamilia->maddni;
                $dataApo = $dataFamilia->madre;
                $dataPaterno = $dataFamilia->madapepat;
                $dataMaterno = $dataFamilia->madapemat;
                $dataNombre = $dataFamilia->madnombre;
                $flgApo = "M";
                $famcod = $dataFamilia->famcod;
            } else {
                $dataDni = "";
                $dataApo = "";
                $dataPaterno = "";
                $dataMaterno = "";
                $dataNombre = "";
                $flgDni = 0;
                $famcod = $dataFamilia->famcod;
            }

            $data = $this->objCobros->getPagoxId($vIdAlumno, $vsqlMescobIn, $vsqlConcobIn);
            // ================== Generando Codigo de Documento ============
            $dataSalon = $this->objSalon->getSalones($vIdnemo);
            $instru = $dataSalon[0]->INSTRUCOD;
            $vtipodocu = TP_COMP; // Boleta por defecto
            if ($instru == 'I' || $instru == 'P') {
                $vruc = RUC_PRIMARIA;
            } else {
                $vruc = RUC_SECUNDARIA;
            }
            $vusu = $this->_session['USUCOD'];
            if ($vusu == ADMIN || $this->ano == 2018) {
                $genCod = "B001-99999999";
            } else {
                //$cadGenerado = $this->objCobros->getGeneraNumero($vruc, $vtipodocu);
                //$genCod = $cadGenerado[0]->codigoGenerado;
                $genCod = "0001-99999999";
            }
            // ==================================

            $arrData = array();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $arrData [] = $fila;
                }
            }
            $output = array(
                "data" => $arrData,
                "arrMescodId" => $vsqlMescobIn,
                "arrConcodId" => $vsqlConcobIn,
                "gencod" => $genCod,
                "dni" => $dataDni,
                "familia" => $dataApo,
                "flgDni" => $flgDni,
                "flgApo" => $flgApo,
                "famcod" => $famcod,
                "dataPaterno" => $dataPaterno,
                "dataMaterno" => $dataMaterno,
                "dataNombre" => $dataNombre
            );
        }
        echo json_encode($output);
    }

    public function savePago() {
        $output = array();
        if ($this->input->is_ajax_request()) {
            $vIdAlu = $this->input->post("vIdAlu");
            $vIdsMes = $this->input->post("vIdsMes");
            $vIdsCobro = $this->input->post("vIdsCobro");
            $vnumrec = $this->input->post("vnumrec");
            $vfecha = $this->input->post("vfecha") . ' ' . date("H:i:s");
            $varrPagos = $this->input->post("varrPagos");
            $vcbtipo = $this->input->post("vcbtipo");
            $vvoucher = $this->input->post("voucher");
            $vcomp = $this->input->post("vcomp");
            $vIdnemo = $this->input->post("vidnemo");
            $vchkflag = $this->input->post("vchkflag");
            // Datos del Apoderado a Guardar
            $vFlgDni = $this->input->post("vFlgDni");
            $vTipoApo = $this->input->post("vTipoApo");
            $vDNI = $this->input->post("vDNI");
            $vFamCod = $this->input->post("vFamCod");
            $vPaterno = $this->input->post("vPaterno");
            $vMaterno = $this->input->post("vMaterno");
            $vNombres = $this->input->post("vNombres");
            // ------ Obtenemos el instrucod por Codigo de Salon
            $dataSalon = $this->objSalon->getSalones($vIdnemo);
            $instru = $dataSalon[0]->INSTRUCOD;
            $vusu = $this->_session['USUCOD'];
            if ($instru == 'I' || $instru == 'P') {
                $vruc = 'R01';
            } else {
                $vruc = 'R02';
            }
            // Si no tiene DNI lo grabamos
            // if ($vFlgDni == "0") {
            if ($vcomp == "02") { // Boletas
                /* if ($vTipoApo == "P") {
                  $arrdatos = array(
                  'PADDNI' => $vDNI,
                  'PADAPEPAT' => $vPaterno,
                  'PADAPEMAT' => $vMaterno,
                  'PADNOMBRE' => $vNombres
                  );
                  $this->objFamilia->update($arrdatos, $vFamCod);
                  } elseif ($vTipoApo == "M") {
                  $arrdatos = array(
                  'MADDNI' => $vDNI,
                  'MADAPEPAT' => $vPaterno,
                  'MADAPEMAT' => $vMaterno,
                  'MADNOMBRE' => $vNombres
                  );
                  $this->objFamilia->update($arrdatos, $vFamCod);
                  } */
            } elseif ($vcomp == "03") { // Facturas
                $arrdatos = array(
                    'RUC' => $vDNI
                );
                $this->objFamilia->update($arrdatos, $vFamCod);
            }
            // --------------------------------------------------------            
            /* if (strlen($vIdsMes) == 2) { // == Cuando solo se envia un solo Registro de Pago 
              if (($vIdsCobro == '02') || ($vIdsCobro == '01' && $vchkflag == '1')) { //==== Cuando es Matricula o Pension y este Activo el Check Modificable
              //$varrPagos = substr ($varrPagos, 0, -1);
              $varrPagos = trim($varrPagos);
              $data = $this->objCobros->grabarPension($vIdAlu, $vIdsMes, $vIdsCobro, $vnumrec, $varrPagos, $vcbtipo, $vfecha, $vcomp, $vruc, $vchkflag);
              } else {
              $data = $this->objCobros->grabarPension($vIdAlu, $vIdsMes, $vIdsCobro, $vnumrec, 0, $vcbtipo, $vfecha, $vcomp, $vruc, $vchkflag);
              }
              } else { */ // == Cuando solo se envia varios Registros de Pago 
            $arrDataMes = explode(",", $vIdsMes);
            $arrDataCob = explode(",", $vIdsCobro);
            $arrDataPago = explode("|", $varrPagos);
            $i = 0;
            for ($x = 0; $x < count($arrDataMes); $x++) {
                if (($arrDataCob[$x] == '02') || ($arrDataCob[$x] == '01' && $vchkflag == '1')) {
                    $data = $this->objCobros->grabarPension($vIdAlu, $arrDataMes[$x], $arrDataCob[$x], $vnumrec, $arrDataPago[$i], $vcbtipo, $vfecha, $vcomp, $vruc, $vchkflag,$vvoucher,$vcbtipo );
                    $i++;
                } else {
                    $data = $this->objCobros->grabarPension($vIdAlu, $arrDataMes[$x], $arrDataCob[$x], $vnumrec, 0, $vcbtipo, $vfecha, $vcomp, $vruc, $vchkflag,$vvoucher,$vcbtipo);
                }
            }
            /* } */
            if ($data) {
                $dataControl = array(
                    'usuret' => $vusu,
                    'fecharet' => date("Y-m-d H:i:s"),
                    'flgret' => 1
                );
                $this->objCobros->actualiza_control($dataControl, $vnumrec);
                $output = array(
                    "msg" => "PAGO REALIZADO CON EXITO."
                );
            } else {
                $output = array(
                    "msg" => "Hubo un error en la Transaccion. Vuelva a intentarlo."
                );
            }
        }
        echo json_encode($output);
    }

    public function filtroAlumno() {
        $returnArr = array();
        $txtFiltro = $this->input->get("term");
        $dataFiltro = $this->objCobros->filtroAlumno($txtFiltro);
        foreach ($dataFiltro as $arrdata) {
            $alucod = $arrdata->ALUCOD;
            $rowArray['value'] = $alucod . " | " . $arrdata->NOMCOMP . " | " . $arrdata->NEMODES;
            $rowArray['alucod'] = $alucod;
            $rowArray['nomcomp'] = $arrdata->NOMCOMP;
            $rowArray['nemo'] = $arrdata->NEMO;
            $rowArray['nemodes'] = $arrdata->NEMODES;
            $rowArray['estado'] = $arrdata->ESTADO;
            array_push($returnArr, $rowArray);
        }
        echo json_encode($returnArr);
    }

    public function lstPagosAdicional() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $data = $this->objCobros->getPagoAllAdicionales();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $arrData [] = array(
                        "id" => $fila->id,
                        "fecreg" => $fila->fecreg,
                        "nomcomp" => $fila->nomcomp,
                        "concepto" => $fila->condes,
                        "recibo" => $fila->numrecibo,
                        "monto" => 'S/' . $fila->montocob
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => count($data),
                    "recordsFiltered" => count($data),
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

    public function lstPagos() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $vIdAlumno = $this->input->post("idAlumno");
            $data = $this->objCobros->getPagoxAlumno($vIdAlumno);
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $vimg = base_url() . "img/pagado.png";
                    if ($fila->estado == 'C') {
                        if ($fila->tipo_comp == '01' && str_replace("-", "", substr($fila->fecmod, 0, 10)) < date("Ymd")) // Recibos y Matriculas
                            $vimgDel = "<img src='" . base_url() . "img/recibo.png' width='33px' heigth='33px' title='Recibo Procesado'/>";
                        elseif ($fila->flgenvio == '1' && ($fila->tipo_comp == '02' || $fila->tipo_comp == '03')) // Boletasy Facturas
                            $vimgDel = "<img src='" . base_url() . "img/enviado.png' width='25px' heigth='25px' title='Enviado a SUNAT'/>";
                        else
                            $vimgDel = "<img src='" . base_url() . "img/delete.png' width='25px' heigth='25px' title='Eliminar Pago' onclick=\"javascript:js_delpago('" . $fila->concob . "','" . $fila->mescob . "','" . $vIdAlumno . "','" . $fila->numrecibo . "');\" />";
                    } else {
                        $vimgDel = "";
                    }
                    if ($fila->tipopago == 'C') {
                        $vtitle = "Pago en Caja";
                        $vimgPago = base_url() . "img/efectivo.png";
                    } else {
                        $vtitle = "Pago en Banco";
                        $vimgPago = base_url() . "img/banco.png";
                    }
                    // ========== Se agrega para exoneracion de Pagos de Marzo 2020 =======
                    if ($fila->flgexonera == '1' && $this->ano >=2020) {
                        $bloq = "disabled";
                        $txtbloq = "<b style='color:red;'>Exonerado</b>";
                    } else {
                        $bloq = "";
                        $txtbloq = "<b>Pendiente</b>";
                    }
                    // ==================================================================
                    $arrData [] = array(
                        "chk" => (($fila->estado == 'P') ? '<input type="checkbox" ' . $bloq . ' class="chk-box" name="chkPagos[]" value="' . $fila->concob . '|' . $fila->mescob . '" />' : '<img src="' . $vimg . '" width="20px" heigth="20px"  />'),
                        "estado" => (($fila->estado == 'P') ? $txtbloq : '<b>Pagado</b>&nbsp;<img src="' . $vimgPago . '" title="' . $vtitle . '" width="25px" heigth="25px" />'),
                        "fecven" => invierte_date($fila->fecven),
                        "concepto" => $fila->condes . (($fila->concob == '01') ? (' - ' . $fila->mesdes) : ''),
                        "fecreg" => invierte_date_time($fila->fecmod),
                        "motno" => 'S/' . $fila->montopen,
                        "mora" => "S/0.00",
                        "total" => 'S/' . (($fila->estado == 'C') ? $fila->montocob : $fila->montopen),
                        "config" => $vimgDel
                    ); // $fila->concob == '02' || $fila->concob == '04' && 
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => count($data),
                    "recordsFiltered" => count($data),
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

    public function deletePago() {
        $vconcob = $this->input->post("vconcob");
        $vmescob = $this->input->post("vmescob");
        $valucod = $this->input->post("valucod");
        $vnumrec = $this->input->post("vnumrecibo");
        $resp = $this->objCobros->deletePagoxAlumno($valucod, $vconcob, $vmescob);
        // ========= eLIMINANDO DOCUMENTO ===============
        // 1.Hay que recibir el documento
        // 2.Buscar la cantidad de registros por documento 
        // 3.Eliminar documento si la cantida de documento es 1
        // ==================================================
        if ($resp) {
            $dataControl = array(
                'usuret' => '',
                'fecharet' => '',
                'flgreg' => 1,
                'flgret' => 0
            );
            $this->objCobros->actualiza_control($dataControl, $vnumrec);
            $output = array('flg' => 0, 'msg' => 'REGISTRO ELIMINADO CORRECTAMENTE!.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL ELIMINAR EL PAGO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function getAddConcepto() {
        $output = array();
        $dataConceptos = $this->objCobros->getConceptos();
        if (is_array($dataConceptos) && count($dataConceptos) > 0) {
            foreach ($dataConceptos as $concep) {
                $arrData [] = array(
                    'id' => $concep->concob,
                    'value' => strtoupper($concep->condes)
                );
            }
            $output = array('data' => $arrData);
        }
        echo json_encode($output);
    }

    public function grabarConceptoAdic() {
        $output = array();
        $vfecha = $this->input->post("vfecha");
        $vapepat = $this->input->post("vapepat");
        $vapemat = $this->input->post("vapemat");
        $vnom = $this->input->post("vnom");
        $vmonto = $this->input->post("vmonto");
        $vnumrecibo = $this->input->post("vnumrecibo");
        $vconcepto = $this->input->post("vconcepto");

        $dataPost = array(
            'anocob' => date('Y'),
            'apepat' => $vapepat,
            'apemat' => $vapemat,
            'nombres' => $vnom,
            'nomcomp' => ($vapepat . " " . $vapemat . ", " . $vnom),
            'concob' => $vconcepto,
            'mescob' => date('m'),
            'montoini' => $vmonto,
            'fecemi' => $vfecha,
            'fecven' => $vfecha,
            'montocob' => $vmonto,
            'moncod' => '001',
            'estado' => 'C',
            'montopen' => 0,
            'numrecibo' => $vnumrecibo,
            'fecreg' => date('Y-m-d'),
            'usureg' => 'SISTEMAS',
            'orden' => 0,
            'tipopago' => 'C'
        );
        $resp = $this->objCobros->grabaNuevoConceptoAdicional($dataPost);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE EL CONCEPTO DE COBRO AL ALUMNO.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL REGISTRAR EL CONCEPTO DE PAGO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function grabarConcepto() {
        $output = array();
        $vIdAlumno = $this->input->post("vIdAlumno");
        $vmonto = $this->input->post("vmonto");
        $vnumrecibo = $this->input->post("vnumrecibo");
        $vconcepto = $this->input->post("vconcepto");
        $vusu = $this->_session['USUCOD']; // Codigo del Usuario                 
        $valida = $this->objCobros->ValidaConceptoxAlumno($vIdAlumno, $vconcepto);
        if ($valida > 0) { // Ya existe el concepto
            $output = array('flg' => 1, 'msg' => 'YA EXISTE EL CONCEPTO DE COBRO ASIGNADO AL ALUMNO');
        } else {
            $dataPost = array(
                'anocob' => date('Y'),
                'alucod' => $vIdAlumno,
                'concob' => $vconcepto,
                'mescob' => date('m'),
                'montoini' => $vmonto,
                'fecemi' => date('Y-m-d'),
                'fecven' => date('Y-m-d'),
                'montocob' => 0,
                'moncod' => '001',
                'estado' => 'P',
                'montopen' => $vmonto,
                'fecreg' => date('Y-m-d'),
                'usureg' => $vusu,
                'orden' => 0,
                'tipopago' => 'C'
            );
            $res = $this->objCobros->grabaNuevoConcepto($dataPost);
            if ($res) {
                $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE EL CONCEPTO DE COBRO AL ALUMNO.');
            } else {
                $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL REGISTRAR EL CONCEPTO DE COBRO.');
            }
        }
        echo json_encode($output);
    }

    public function printModelo() {
        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P', $unit = 'mm', array(45, 100));
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 8);    //Letra Arial, negrita (Bold), tam. 20
        $textypos = 5;
        $this->pdf->setY(2);
        $this->pdf->setX(2);
        $this->pdf->Cell(5, $textypos, "NOMBRE DE LA EMPRESA");
        $this->pdf->SetFont('Arial', '', 5);    //Letra Arial, negrita (Bold), tam. 20
        $textypos += 6;
        $this->pdf->setX(2);
        $this->pdf->Cell(5, $textypos, '-------------------------------------------------------------------');
        $textypos += 6;
        $this->pdf->setX(2);
        $this->pdf->Cell(5, $textypos, 'CANT.  ARTICULO       PRECIO               TOTAL');
        $total = 0;
        $off = $textypos + 6;
        $producto = array(
            "q" => 1,
            "name" => "Computadora Lenovo i5",
            "price" => 100
        );
        $productos = array($producto, $producto, $producto, $producto, $producto);
        foreach ($productos as $pro) {
            $this->pdf->setX(2);
            $this->pdf->Cell(5, $off, $pro["q"]);
            $this->pdf->setX(6);
            $this->pdf->Cell(35, $off, strtoupper(substr($pro["name"], 0, 12)));
            $this->pdf->setX(20);
            $this->pdf->Cell(11, $off, "$" . number_format($pro["price"], 2, ".", ","), 0, 0, "R");
            $this->pdf->setX(32);
            $this->pdf->Cell(11, $off, "$ " . number_format($pro["q"] * $pro["price"], 2, ".", ","), 0, 0, "R");
            $total += $pro["q"] * $pro["price"];
            $off += 6;
        }
        $textypos = $off + 6;
        $this->pdf->setX(2);
        $this->pdf->Cell(5, $textypos, "TOTAL: ");
        $this->pdf->setX(38);
        $this->pdf->Cell(5, $textypos, "$ " . number_format($total, 2, ".", ","), 0, 0, "R");
        $this->pdf->setX(2);
        $this->pdf->Cell(5, $textypos + 6, 'GRACIAS POR TU COMPRA ');
        $this->pdf->AutoPrint();
        $this->pdf->output();
    }

    public function printTicketV2() {
        /* echo "<pre>";
          print_r($_POST);
          echo "</pre>";
          exit; */
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vtipo = $_POST["rbdTipo"];  // (tipo de documento)
        $vNemo = $_POST["htxtsalon"]; // "2019034"; // (Codigo del Salon)
        $vAlumno = $_POST["htxtalumno"];  // "20170859"; // (Codigo del Alumno)
        $vcomp = $_POST["htxtnumrecibo"]; // "B001-00000101"; (Numero de comprobante)
        //$vusu = $this->_session['USUCOD']; // Codigo del Usuario
        // ================= Obteniendo informacion  ===========================
        $dataSalon = $this->objSalon->getSalones($vNemo);
        $instru = $dataSalon[0]->INSTRUCOD;
        $nemodes = $dataSalon[0]->NEMODES;
        if ($instru == 'I' || $instru == 'P') {
            $vruc = RUC_PRIMARIA;
            $vTipoRazon = "R01";
        } else {
            $vruc = RUC_SECUNDARIA;
            $vTipoRazon = "R02";
        }
        if ($vtipo == '01') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
            /* $vTipoDesc = "RECIBO";
              $vTipoElectro = "ELECTRÓNICO"; */
        } elseif ($vtipo == '02') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
        } elseif ($vtipo == '03') {
            $vTipoDesc = "FACTURA";
            $vTipoElectro = "ELECTRÓNICA";
        }
        $dataEmpresa = $this->objSalon->getDatosEmpresa($vruc);
        $vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);

        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataFamilia = $this->objAlumno->getFamiliaAlumno($vDniAlumno);
        /*$dataCampus = $this->objAlumno->geUsuarioCampus($vDniAlumno);
        if (count($dataCampus) > 0) {
            $vusucampus = $dataCampus->usuario;
            $vclavecampus = "********";
        } else {
            $vusucampus = strtolower($dataAlumno[0]->APEPAT . "." . $dataAlumno[0]->APEMAT . "." . substr($dataAlumno[0]->NOMBRES, 0, 1));
            $vclavecampus = $dataCampus->dni;
        }        */
        $dataApoderado = "";
        if ($dataFamilia->flgpadapo == '1') {
            $dataApoderado = $dataFamilia->paddni . ' - ' . $dataFamilia->padre;
        } else {
            $dataApoderado = $dataFamilia->maddni . ' - ' . $dataFamilia->madre;
        }
        $dataPago = $this->objCobros->getPagoxAlumnoRec($vcomp, $vAlumno);

        $numregs = count($dataPago);
        // ============ Obteniendo la altura del Ticket =====================
        $limite = 0;
        if ($numregs > 0) {
            $limite = ($numregs * 3);
        }
        if ($vtipo == '03') {
            $limite += 25;
        }
        $limite = 125 + $limite;
        $vusuPago = $this->objCobros->getDatoUsuarioPago($vcomp, $vTipoRazon);
        $datoUsuario = $this->objCobros->getDatoUsuario($vusuPago->usumod);

        // ==================================================================
        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P', $unit = 'mm', array(45, $limite));
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle($vTipoDesc . ' ELECTRONICA - ' . $this->ano);
        #Establecemos los mรกrgenes izquierda, arriba y derecha:
        $this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 13, 2, 18, 16, 'PNG');
        // ==================== BLOQUE DEL COMPROBANTE ==============
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY(8, 20);
        $this->pdf->Cell(30, 3, $vTipoDesc . ' ' . utf8_decode($vTipoElectro), 0, 0, 'C');
        $this->pdf->SetXY(8, 23);
        $this->pdf->Cell(30, 3, 'R.U.C : ' . $dataEmpresa[0]->ruc, 0, 0, 'C');
        $this->pdf->SetXY(8, 26);
        $this->pdf->Cell(30, 3, $vcomp, 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(3, 29);
        $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');
        // ================BLOQUE RAZON SOCIAL =====================
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(2, 33);
        $this->pdf->Cell(42, 3, utf8_decode('"' . $dataEmpresa[0]->nombre_comercial . '"'), 0, 0, 'C');
        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->SetXY(2, 35);
        $this->pdf->Cell(42, 3, utf8_decode('"' . $dataEmpresa[0]->razon_social . '"'), 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(2, 40);
        $this->pdf->Cell(42, 3, utf8_decode($dataEmpresa[0]->direccion), 0, 0, 'C');
        $this->pdf->SetXY(2, 42);
        $this->pdf->Cell(42, 3, utf8_decode('Teléfono : ' . $dataEmpresa[0]->telefono), 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(3, 45);
        $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');
        // ================BLOQUE DATOS DEL CLIENTE =====================
        $data = explode("-", $dataApoderado);
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(3, 48);
        if ($vtipo == '03') {
            $this->pdf->Cell(13, 2, utf8_decode("Razón Social: "), 0, 0, 'L');
        } else {
            $this->pdf->Cell(10, 2, utf8_decode("Señor(es): "), 0, 0, 'L');
        }
        $this->pdf->SetFont('Arial', '', 5);

        if ($vtipo == '03') { // esta en duro el ruc
            $this->pdf->SetXY(16, 48);
            $this->pdf->Cell(15, 2, '10' . trim($data[0]) . '2', 0, 0, 'L');
            $this->pdf->SetXY(3, 50);
            $this->pdf->Cell(40, 2, utf8_decode(trim($data[1])), 0, 0, 'L');
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(3, 52);
            $this->pdf->Cell(13, 2, utf8_decode("Alumno(a): "), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(16, 52);
            $this->pdf->Cell(15, 2, utf8_decode($dataAlumno[0]->DNI), 0, 0, 'L');
        } else {
            $this->pdf->SetXY(13, 48);
            $this->pdf->Cell(10, 2, $data[0], 0, 0, 'L');
            $this->pdf->SetXY(3, 50);
            $this->pdf->Cell(40, 2, utf8_decode(trim($data[1])), 0, 0, 'L');
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(3, 52);
            $this->pdf->Cell(10, 2, utf8_decode("Alumno(a): "), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(13, 52);
            $this->pdf->Cell(10, 2, utf8_decode($dataAlumno[0]->DNI), 0, 0, 'L');
        }


        $this->pdf->SetXY(3, 54);
        $this->pdf->Cell(40, 2, utf8_decode(substr($dataAlumno[0]->NOMCOMP, 0, 38)), 0, 0, 'L');
        /*$this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(3, 56);
        $this->pdf->Cell(10, 2, utf8_decode("Aula: "), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(13, 56);
        $this->pdf->Cell(10, 2, utf8_decode($nemodes), 0, 0, 'L');*/
        // ================BLOQUE CABECERA BOLETA =====================
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(4, 60);
        $this->pdf->Cell(4, 3, utf8_decode("#"), 'TB', 0, 'C');
        $this->pdf->SetXY(8, 60);
        $this->pdf->Cell(27, 3, utf8_decode("CONCEPTO"), 'TB', 0, 'C');
        /* $this->pdf->Cell(21, 3, utf8_decode("CONCEPTO"), 'TB', 0, 'C');
          $this->pdf->SetXY(29, 60);
          $this->pdf->Cell(6, 3, utf8_decode("MORA"), 'TB', 0, 'C'); */
        $this->pdf->SetXY(35, 60);
        $this->pdf->Cell(7, 3, utf8_decode("TOTAL"), 'TB', 0, 'C');
        // ================BLOQUE DETALLE BOLETA =====================
        $iniFila = 63;
        $filas = 1;
        $total = 0;
        $fechapago = "";
        foreach ($dataPago as $pago) {
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(4, $iniFila);
            $this->pdf->Cell(4, 3, utf8_decode($filas), 'B', 0, 'C');
            $this->pdf->SetXY(8, $iniFila);
            if($pago->concob=='02'){
                $exo = ($pago->flgexonera=='1')? ' - (EXO)' : '';
                 $descPago ='MATRICULA - ' . $this->ano.$exo; 
            } else {
                 $descPago =nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob) . ' - ' . $this->ano;
            }
            $this->pdf->Cell(27, 3, $descPago, 'B', 0, 'L'); //. ' - ' . $this->ano
            /* $this->pdf->Cell(21, 3, utf8_decode(nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob)), 'B', 0, 'L');
              $this->pdf->SetXY(29, $iniFila);
              $this->pdf->Cell(6, 3, utf8_decode('0.00'), 'B', 0, 'R'); */
            $this->pdf->SetXY(35, $iniFila);
            $this->pdf->Cell(7, 3, $pago->montocob, 'B', 0, 'R');
            $iniFila += 3;
            $total += $pago->montocob;
            $fechapago = $pago->fecmod;
        }
		
		    /*$this->pdf->SetXY(4, $iniFila);
            $this->pdf->Cell(4, 3, '2', 'B', 0, 'C');
            $this->pdf->SetXY(8, $iniFila);
            $this->pdf->Cell(27, 3, 'MORA AGOSTO', 'B', 0, 'L'); 
            $this->pdf->SetXY(35, $iniFila);
            $this->pdf->Cell(7, 3, '19.00', 'B', 0, 'R');
            $iniFila += 3;
            //$total += 19;
            $fechapago = $pago->fecmod;
			
			$iniFila += 3;*/

        if ($vtipo == '03') { // Solo para Facturas
            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Gratuitas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Exoneradas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Gravadas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Inafecta: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("IGV (18%): "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Otros Cargos: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 3;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(3, $iniFila);
            $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Importe Total: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');
        } else {

            $iniFila += 5;
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(22, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Importe Total: "), 0, 0, 'C');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');
        }
        $iniFila += 4;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode("SON : " . convertir_a_letras($total) . ' Y 00/100 SOLES.'), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Fecha de Emisión : ' . $fechapago), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Atendido por         : ' . $datoUsuario->nomcomp), 0, 0, 'L');
        
       /* $iniFila += 4;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Usuario Campus : ' . $vusucampus), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Clave Campus    : ' . $vclavecampus), 0, 0, 'L');    */
        $iniFila += 5;        
        
        // ===================Creacion de Codigo QR =========================
        $this->load->library('qrcodephp');   
        /* RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE | */
        //$textqr = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . date("d/m/Y") . '|' . $vtipo . '|';
        $algorithm = MCRYPT_BLOWFISH;
        $key = 'keyappweb';
        $data = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . $vtipo . '|';
        /* $mode = MCRYPT_MODE_CBC;
          $iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, $mode),MCRYPT_DEV_URANDOM);
          $encrypted = mcrypt_encrypt($algorithm, $key, $data, $mode, $iv); */
        $plainText = base64_encode($data);
        /* $encrypted_data = base64_decode($plain_text);
          $decoded = mcrypt_decrypt($algorithm, $key, $encrypted_data, $mode, $iv); */
        $textqr = "http://www.sistemas-dev.com/verboletas.php?id=" . $plainText;
        // echo $textqr; exit;
        $rutaqr = $_SERVER['DOCUMENT_ROOT'] . "/intranet/img/qr/" . $vcomp . ".png";
        $rutaticket = base_url() . "img/qr/" . $vcomp . ".png";
        QRcode::png($textqr, $rutaqr, 'Q', 15, 0); 
        // =================================================================
        $this->pdf->Image($rutaticket, 13, $iniFila, 18, 16, 'PNG');
        $iniFila += 18;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(4, $iniFila);
        //$this->pdf->MultiCell(40, 2, utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica, Para consultar el documento ingrese a:"), 0, 'L');
        $this->pdf->MultiCell(40, 2, utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica."), 0, 'L');
        /* $iniFila += 6;
          $this->pdf->SetFont('Arial', 'B', 5);
          $this->pdf->SetXY(4, $iniFila);
          $this->pdf->MultiCell(40, 2, utf8_decode("www.marianista.pe"), 0, 'C'); */
        if ($vtipo != '01') { // diferentes de Recibo
            $iniFila += 4;
            $this->pdf->SetFont('Arial', '', 4);
            $this->pdf->SetXY(4, $iniFila);
            $this->pdf->MultiCell(40, 2, utf8_decode("Estimado Cliente, Conserve su Ticket de compra, Por regulación de SUNAT es indispensable presentarlo para solicitar cambios o devoluciones."), 0, 'L');
        }
        $this->pdf->AutoPrint();
        $this->pdf->output($vAlumno . '-' . $vcomp . '.pdf', 'I');
    }
    
    public function printTicketEmail() {
        /* echo "<pre>";
          print_r($_POST);
          echo "</pre>";
          exit; */
       /* if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }*/
        $vtipo = $_POST["rbdTipo"];  // (tipo de documento)
        $vNemo = $_POST["htxtsalon"]; // "2019034"; // (Codigo del Salon)
        $vAlumno = $_POST["htxtalumno"];  // "20170859"; // (Codigo del Alumno)
        $vcomp = $_POST["htxtnumrecibo"]; // "B001-00000101"; (Numero de comprobante)
        //$vusu = $this->_session['USUCOD']; // Codigo del Usuario
        // ================= Obteniendo informacion  ===========================
        $dataSalon = $this->objSalon->getSalones($vNemo);
        $instru = $dataSalon[0]->INSTRUCOD;
        $nemodes = $dataSalon[0]->NEMODES;
        if ($instru == 'I' || $instru == 'P') {
            $vruc = RUC_PRIMARIA;
            $vTipoRazon = "R01";
        } else {
            $vruc = RUC_SECUNDARIA;
            $vTipoRazon = "R02";
        }
        if ($vtipo == '01') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
            /* $vTipoDesc = "RECIBO";
              $vTipoElectro = "ELECTRÓNICO"; */
        } elseif ($vtipo == '02') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
        } elseif ($vtipo == '03') {
            $vTipoDesc = "FACTURA";
            $vTipoElectro = "ELECTRÓNICA";
        }
        $dataEmpresa = $this->objSalon->getDatosEmpresa($vruc);
        $vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);

        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataFamilia = $this->objAlumno->getFamiliaAlumno($vDniAlumno);
        /*$dataCampus = $this->objAlumno->geUsuarioCampus($vDniAlumno);
        if (count($dataCampus) > 0) {
            $vusucampus = $dataCampus->usuario;
            $vclavecampus = "********";
        } else {
            $vusucampus = strtolower($dataAlumno[0]->APEPAT . "." . $dataAlumno[0]->APEMAT . "." . substr($dataAlumno[0]->NOMBRES, 0, 1));
            $vclavecampus = $dataCampus->dni;
        }        */
        $dataApoderado = "";
        if ($dataFamilia->flgpadapo == '1') {
            $dataApoderado = $dataFamilia->paddni . ' - ' . $dataFamilia->padre;
        } else {
            $dataApoderado = $dataFamilia->maddni . ' - ' . $dataFamilia->madre;
        }
        $dataPago = $this->objCobros->getPagoxAlumnoRec($vcomp, $vAlumno);

        $numregs = count($dataPago);
        // ============ Obteniendo la altura del Ticket =====================
        $limite = 0;
        if ($numregs > 0) {
            $limite = ($numregs * 3);
        }
        if ($vtipo == '03') {
            $limite += 25;
        }
        $limite = 125 + $limite;
        $vusuPago = $this->objCobros->getDatoUsuarioPago($vcomp, $vTipoRazon);
        $datoUsuario = $this->objCobros->getDatoUsuario($vusuPago->usumod);

        // ==================================================================
        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P', $unit = 'mm', array(45, $limite));
        $this->pdf->SetAuthor('.SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle($vTipoDesc . ' ELECTRONICA - ' . $this->ano);
        #Establecemos los mรกrgenes izquierda, arriba y derecha:
        $this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 13, 2, 18, 16, 'PNG');
        // ==================== BLOQUE DEL COMPROBANTE ==============
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY(8, 20);
        $this->pdf->Cell(30, 3, $vTipoDesc . ' ' . utf8_decode($vTipoElectro), 0, 0, 'C');
        $this->pdf->SetXY(8, 23);
        $this->pdf->Cell(30, 3, 'R.U.C : ' . $dataEmpresa[0]->ruc, 0, 0, 'C');
        $this->pdf->SetXY(8, 26);
        $this->pdf->Cell(30, 3, $vcomp, 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(3, 29);
        $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');
        // ================BLOQUE RAZON SOCIAL =====================
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(2, 33);
        $this->pdf->Cell(42, 3, utf8_decode('"' . $dataEmpresa[0]->nombre_comercial . '"'), 0, 0, 'C');
        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->SetXY(2, 35);
        $this->pdf->Cell(42, 3, utf8_decode('"' . $dataEmpresa[0]->razon_social . '"'), 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(2, 40);
        $this->pdf->Cell(42, 3, utf8_decode($dataEmpresa[0]->direccion), 0, 0, 'C');
        $this->pdf->SetXY(2, 42);
        $this->pdf->Cell(42, 3, utf8_decode('Teléfono : ' . $dataEmpresa[0]->telefono), 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(3, 45);
        $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');
        // ================BLOQUE DATOS DEL CLIENTE =====================
        $data = explode("-", $dataApoderado);
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(3, 48);
        if ($vtipo == '03') {
            $this->pdf->Cell(13, 2, utf8_decode("Razón Social: "), 0, 0, 'L');
        } else {
            $this->pdf->Cell(10, 2, utf8_decode("Señor(es): "), 0, 0, 'L');
        }
        $this->pdf->SetFont('Arial', '', 5);

        if ($vtipo == '03') { // esta en duro el ruc
            $this->pdf->SetXY(16, 48);
            $this->pdf->Cell(15, 2, '10' . trim($data[0]) . '2', 0, 0, 'L');
            $this->pdf->SetXY(3, 50);
            $this->pdf->Cell(40, 2, utf8_decode(trim($data[1])), 0, 0, 'L');
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(3, 52);
            $this->pdf->Cell(13, 2, utf8_decode("Alumno(a): "), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(16, 52);
            $this->pdf->Cell(15, 2, utf8_decode($dataAlumno[0]->DNI), 0, 0, 'L');
        } else {
            $this->pdf->SetXY(13, 48);
            $this->pdf->Cell(10, 2, $data[0], 0, 0, 'L');
            $this->pdf->SetXY(3, 50);
            $this->pdf->Cell(40, 2, utf8_decode(trim($data[1])), 0, 0, 'L');
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(3, 52);
            $this->pdf->Cell(10, 2, utf8_decode("Alumno(a): "), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(13, 52);
            $this->pdf->Cell(10, 2, utf8_decode($dataAlumno[0]->DNI), 0, 0, 'L');
        }


        $this->pdf->SetXY(3, 54);
        $this->pdf->Cell(40, 2, utf8_decode(substr($dataAlumno[0]->NOMCOMP, 0, 38)), 0, 0, 'L');
        /*$this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(3, 56);
        $this->pdf->Cell(10, 2, utf8_decode("Aula: "), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(13, 56);
        $this->pdf->Cell(10, 2, utf8_decode($nemodes), 0, 0, 'L');*/
        // ================BLOQUE CABECERA BOLETA =====================
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(4, 60);
        $this->pdf->Cell(4, 3, utf8_decode("#"), 'TB', 0, 'C');
        $this->pdf->SetXY(8, 60);
        $this->pdf->Cell(27, 3, utf8_decode("CONCEPTO"), 'TB', 0, 'C');
        /* $this->pdf->Cell(21, 3, utf8_decode("CONCEPTO"), 'TB', 0, 'C');
          $this->pdf->SetXY(29, 60);
          $this->pdf->Cell(6, 3, utf8_decode("MORA"), 'TB', 0, 'C'); */
        $this->pdf->SetXY(35, 60);
        $this->pdf->Cell(7, 3, utf8_decode("TOTAL"), 'TB', 0, 'C');
        // ================BLOQUE DETALLE BOLETA =====================
        $iniFila = 63;
        $filas = 1;
        $total = 0;
        $fechapago = "";
        foreach ($dataPago as $pago) {
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(4, $iniFila);
            $this->pdf->Cell(4, 3, utf8_decode($filas), 'B', 0, 'C');
            $this->pdf->SetXY(8, $iniFila);
            if($pago->concob=='02'){
                $exo = ($pago->flgexonera=='1')? ' - (EXO)' : '';
                 $descPago ='MATRICULA - ' . $this->ano.$exo; 
            } else {
                 $descPago =nombreConcepto($pago->concob)  . ' - ' . nombreMesesCompleto($pago->mescob). ' - ' . $this->ano;
            }
            $this->pdf->Cell(27, 3, $descPago, 'B', 0, 'L'); //. ' - ' . $this->ano
            /* $this->pdf->Cell(21, 3, utf8_decode(nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob)), 'B', 0, 'L');
              $this->pdf->SetXY(29, $iniFila);
              $this->pdf->Cell(6, 3, utf8_decode('0.00'), 'B', 0, 'R'); */
            $this->pdf->SetXY(35, $iniFila);
            $this->pdf->Cell(7, 3, utf8_decode($pago->montocob), 'B', 0, 'R');
            $iniFila += 3;
            $total += $pago->montocob;
            $fechapago = $pago->fecmod;
        }

        if ($vtipo == '03') { // Solo para Facturas
            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Gratuitas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Exoneradas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Gravadas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Inafecta: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("IGV (18%): "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Otros Cargos: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 3;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(3, $iniFila);
            $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Importe Total: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');
        } else {

            $iniFila += 5;
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(22, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Importe Total: "), 0, 0, 'C');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');
        }
        $iniFila += 4;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode("SON : " . convertir_a_letras($total) . ' Y 00/100 SOLES.'), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Fecha de Emisión : ' . $fechapago), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Atendido por         : ' . $datoUsuario->nomcomp), 0, 0, 'L');
        
       /* $iniFila += 4;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Usuario Campus : ' . $vusucampus), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Clave Campus    : ' . $vclavecampus), 0, 0, 'L');    */
        $iniFila += 5;        
        
        // ===================Creacion de Codigo QR =========================
        $this->load->library('qrcodephp');   
        /* RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE | */
        //$textqr = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . date("d/m/Y") . '|' . $vtipo . '|';
        $algorithm = MCRYPT_BLOWFISH;
        $key = 'keyappweb';
        $data = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . $vtipo . '|';
        /* $mode = MCRYPT_MODE_CBC;
          $iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, $mode),MCRYPT_DEV_URANDOM);
          $encrypted = mcrypt_encrypt($algorithm, $key, $data, $mode, $iv); */
        $plainText = base64_encode($data);
        /* $encrypted_data = base64_decode($plain_text);
          $decoded = mcrypt_decrypt($algorithm, $key, $encrypted_data, $mode, $iv); */
        $textqr = "http://www.sistemas-dev.com/verboletas.php?id=" . $plainText;
        // echo $textqr; exit;
        $rutaqr = $_SERVER['DOCUMENT_ROOT'] . "/intranet/img/qr/" . $vcomp . ".png";
        $rutaticket = base_url() . "img/qr/" . $vcomp . ".png";
        QRcode::png($textqr, $rutaqr, 'Q', 15, 0); 
        // =================================================================
        $this->pdf->Image($rutaticket, 13, $iniFila, 18, 16, 'PNG');
        $iniFila += 18;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(4, $iniFila);
        //$this->pdf->MultiCell(40, 2, utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica, Para consultar el documento ingrese a:"), 0, 'L');
        $this->pdf->MultiCell(40, 2, utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica."), 0, 'L');
        /* $iniFila += 6;
          $this->pdf->SetFont('Arial', 'B', 5);
          $this->pdf->SetXY(4, $iniFila);
          $this->pdf->MultiCell(40, 2, utf8_decode("www.marianista.pe"), 0, 'C'); */
        if ($vtipo != '01') { // diferentes de Recibo
            $iniFila += 4;
            $this->pdf->SetFont('Arial', '', 4);
            $this->pdf->SetXY(4, $iniFila);
            $this->pdf->MultiCell(40, 2, utf8_decode("Estimado Cliente, Conserve su Ticket de compra, Por regulación de SUNAT es indispensable presentarlo para solicitar cambios o devoluciones."), 0, 'L');
        }
        $nombreArchivo = $vAlumno . '-' . $vcomp . '.pdf';
        $this->pdf->output($nombreArchivo, 'F');
        $email[]= array('email'=>$dataAlumno[0]->CORREO_INSTITUCIONAL, 'nombre'=>$dataAlumno[0]->NOMBRES);
        //$email[]= array('email'=>'ffernandox@hotmail.com', 'nombre'=>'FERNANDO');
        echo EnviarMailAdjuntos($email, $nombreArchivo, $nombreArchivo);
    }    

    public function printTicket() {
        $vtipo = $_POST["rbdTipo"];  // "02";  // (tipo de documento)
        $vNemo = $_POST["htxtsalon"]; // "2019034"; // (Codigo del Salon)
        $vAlumno = $_POST["htxtalumno"];  // "20170859"; // (Codigo del Alumno)
        $vcomp = $_POST["htxtnumrecibo"]; // "B001-00000101"; (Numero de comprobante)
        $vusu = $this->_session['USUCOD']; // Codigo del Usuario
        // ================= Obteniendo informacion  ===========================
        $dataSalon = $this->objSalon->getSalones($vNemo);

        $instru = $dataSalon[0]->INSTRUCOD;
        $nemodes = $dataSalon[0]->NEMODES;
        if ($instru == 'I' || $instru == 'P') {
            $vruc = RUC_PRIMARIA;
        } else {
            $vruc = RUC_SECUNDARIA;
        }
        if ($vtipo == '01') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
            /* $vTipoDesc = "RECIBO";
              $vTipoElectro ="ELECTRÓNICO"; */
        } elseif ($vtipo == '02') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
        } elseif ($vtipo == '03') {
            $vTipoDesc = "FACTURA";
            $vTipoElectro = "ELECTRÓNICA";
        }
        $dataEmpresa = $this->objSalon->getDatosEmpresa($vruc);
        $vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);
        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataFamilia = $this->objAlumno->getFamiliaAlumno($vDniAlumno);
        //$dataCampus = $this->objAlumno->geUsuarioCampus($vDniAlumno);
        /*if (count($dataCampus) > 0) {
            $vusucampus = $dataCampus->usuario;
            $vclavecampus = "********";
        } else {
            $vusucampus = strtolower($dataAlumno[0]->APEPAT . "." . $dataAlumno[0]->APEMAT . "." . substr($dataAlumno[0]->NOMBRES, 0, 1));
            $vclavecampus = $dataCampus->dni;
        }*/
        $dataApoderado = "";
        if ($dataFamilia->flgpadapo == '1') {
            $dataApoderado = $dataFamilia->paddni . ' - ' . $dataFamilia->padre;
        } else {
            $dataApoderado = $dataFamilia->maddni . ' - ' . $dataFamilia->madre;
        }
        $dataPago = $this->objCobros->getPagoxAlumnoRec($vcomp, $vAlumno);
        $datoUsuario = $this->objCobros->getDatoUsuario($vusu);
        // ==================================================================
//        $this->load->library('pdf');
//        $this->pdf = new Pdf('P', 'mm', 'A4');
//        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
//        $this->pdf->SetTitle('COMPROBANTE ELECTRONICO - ' . $this->ano);
//        #Establecemos los mรกrgenes izquierda, arriba y derecha:
//        $this->pdf->SetMargins(5, 5, 5);
//        #Establecemos el margen inferior:
//        $this->pdf->SetAutoPageBreak(true, 5);
//        $this->pdf->AddPage();
//        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 9, 12, 30, 28, 'PNG');
//
//        $this->pdf->SetFont('Arial', 'B', 7);
//        $this->pdf->SetXY(55, 8);
//        $this->pdf->Cell(60, 5, utf8_decode('"' . $dataEmpresa[0]->nombre_comercial . '"'), 0, 0, 'C');
//
//        $this->pdf->SetFont('Arial', 'B', 12);
//        $this->pdf->SetXY(55, 12);
//        $this->pdf->Cell(60, 5, utf8_decode('"' . $dataEmpresa[0]->razon_social . '"'), 0, 0, 'C');
//
//        $this->pdf->SetFont('Arial', 'B', 7);
//        $this->pdf->SetXY(55, 16);
//        $this->pdf->Cell(60, 5, utf8_decode($dataEmpresa[0]->rd), 0, 0, 'C');
//
//
//        $this->pdf->SetFont('Arial', '', 9);
//
//        $this->pdf->SetXY(60, 25);
//        $this->pdf->Cell(50, 5, utf8_decode($dataEmpresa[0]->direccion), 0, 0, 'C');
//
//        $this->pdf->SetXY(60, 30);
//        $this->pdf->Cell(50, 5, utf8_decode('Telรฉfono : ' . $dataEmpresa[0]->telefono), 0, 0, 'C');
//
//        $this->pdf->SetXY(60, 35);
//        $this->pdf->Cell(50, 5, 'Web : ' . WEB, 0, 0, 'C');
//
//        $this->pdf->Rect(135, 10, 60, 25);
//        $this->pdf->SetFont('Arial', 'B', 12);
//        $this->pdf->SetXY(140, 15);
//        $this->pdf->Cell(50, 5, 'R.U.C : ' . $dataEmpresa[0]->ruc, 0, 0, 'C');
//        $this->pdf->SetXY(140, 20);
//        $this->pdf->Cell(50, 5, utf8_decode($vTipoDesc . ' ELECTRรNICA'), 0, 0, 'C');
//        $this->pdf->SetXY(140, 25);
//        $this->pdf->Cell(50, 5, $vcomp, 0, 0, 'C');
//
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(10, 41);
//        $this->pdf->Cell(150, 3, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
//
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(15, 45);
//        $this->pdf->Cell(20, 5, utf8_decode("Seรฑor(es):"), 0, 0, 'L');
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(35, 45);
//        $this->pdf->Cell(115, 5, utf8_decode($dataApoderado), 0, 0, 'L');
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(150, 45);
//        $this->pdf->Cell(25, 5, utf8_decode("Fecha Emisiรณn:"), 0, 0, 'L');
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(175, 45);
//        $this->pdf->Cell(20, 5, date("d/m/Y"), 0, 0, 'C');
//
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(15, 50);
//        $this->pdf->Cell(20, 5, "Alumno(a):", 0, 0, 'L');
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(35, 50);
//        $this->pdf->Cell(115, 5, utf8_decode($dataAlumno[0]->DNI . ' - ' . $dataAlumno[0]->NOMCOMP), 0, 0, 'L');
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(150, 50);
//        $this->pdf->Cell(25, 5, utf8_decode("Moneda:"), 0, 0, 'L');
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(175, 50);
//        $this->pdf->Cell(20, 5, "SOLES", 0, 0, 'C');
//
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(15, 55);
//        $this->pdf->Cell(20, 5, utf8_decode("Aula:"), 0, 0, 'L');
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(35, 55);
//        $this->pdf->Cell(100, 5, utf8_decode($nemodes), 0, 0, 'L');
//
//        $this->pdf->SetFont('Arial', '', 9);
//        $this->pdf->SetXY(10, 60);
//        $this->pdf->Cell(150, 3, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
//
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(15, 65);
//        $this->pdf->Cell(10, 5, utf8_decode("ITEM"), 1, 0, 'C');
//        $this->pdf->SetXY(25, 65);
//        $this->pdf->Cell(15, 5, utf8_decode("TIPO"), 1, 0, 'C');
//        $this->pdf->SetXY(40, 65);
//        $this->pdf->Cell(90, 5, utf8_decode("CONCEPTO DE PAGO"), 1, 0, 'C');
//        $this->pdf->SetXY(130, 65);
//        $this->pdf->Cell(20, 5, utf8_decode("SUB TOTAL"), 1, 0, 'C');
//        $this->pdf->SetXY(150, 65);
//        $this->pdf->Cell(20, 5, utf8_decode("IGV (18%)"), 1, 0, 'C');
//        $this->pdf->SetXY(170, 65);
//        $this->pdf->Cell(20, 5, utf8_decode("IMPORTE"), 1, 0, 'C');

        $iniFila = 70;
        $filas = 1;
        $total = 0;
        foreach ($dataPago as $pago) {
//            $this->pdf->SetFont('Arial', '', 9);
//            $this->pdf->SetXY(15, $iniFila);
//            $this->pdf->Cell(10, 5, utf8_decode($filas), 1, 0, 'C');
//            $this->pdf->SetXY(25, $iniFila);
//            $this->pdf->Cell(15, 5, utf8_decode($pago->concob), 1, 0, 'C');
//            $this->pdf->SetXY(40, $iniFila);
//            $this->pdf->Cell(90, 5, utf8_decode(nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob) . ' - ' . $this->ano), 1, 0, 'L');
//            $this->pdf->SetXY(130, $iniFila);
//            $this->pdf->Cell(20, 5, utf8_decode('S/.' . $pago->montocob), 1, 0, 'R');
//            $this->pdf->SetXY(150, $iniFila);
//            $this->pdf->Cell(20, 5, utf8_decode("S/.0.00"), 1, 0, 'R');
//            $this->pdf->SetXY(170, $iniFila);
//            $this->pdf->Cell(20, 5, utf8_decode('S/.' . $pago->montocob), 1, 0, 'R');
//            $iniFila += 5;
            $total += $pago->montocob;
        }
//        if ($total == 0) {
//            echo "<center>OCURRIO UN ERROR INTERNO.</center>";
//            exit;
//        }
//        $this->pdf->SetFont('Arial', 'B', 9);
//        $this->pdf->SetXY(150, $iniFila);
//        $this->pdf->Cell(20, 5, utf8_decode("TOTAL S/."), 1, 0, 'C');
//        $this->pdf->SetXY(170, $iniFila);
//        $this->pdf->Cell(20, 5, utf8_decode("S/." . number_format($total, 2, '.', ',')), 1, 0, 'R');
//
//        $this->pdf->SetFont('Arial', 'I', 9);
//        $this->pdf->SetXY(15, $iniFila + 10);
//        $this->pdf->Cell(120, 5, utf8_decode("SON : " . convertir_a_letras($total) . ' NUEVOS SOLES.'), 0, 0, 'L');
//
//        $this->pdf->SetFont('Arial', '', 7);
//        $this->pdf->SetXY(140, $iniFila + 16);
//        $this->pdf->Cell(120, 5, utf8_decode("Hora             : " . date('H:i:s')), 0, 0, 'L');
//        $this->pdf->SetXY(140, $iniFila + 20);
//        $this->pdf->Cell(120, 5, utf8_decode("Atendido por : " . $datoUsuario->nomcomp), 0, 0, 'L');
        // ===================Creacion de Codigo QR =========================
        $this->load->library('qrcodephp');
        /* RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE | */
        $textqr = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . date("d/m/Y") . '|' . $vtipo . '|';
        $rutaqr = $_SERVER['DOCUMENT_ROOT'] . "/intranet/img/qr/" . $vcomp . ".png";
        $rutaticket = base_url() . "img/qr/" . $vcomp . ".png";
        QRcode::png($textqr, $rutaqr, 'Q', 15, 0);
        // =============== Creacion de directorio por Alumno ==================
//        if (!is_dir('../intranet/recibos/' . $this->ano))
//            mkdir('../intranet/recibos/' . $this->ano, 0755);
//        if (!is_dir('../intranet/recibos/' . $this->ano . '/' . $vAlumno))
//            mkdir('../intranet/recibos/' . $this->ano . '/' . $vAlumno, 0755);
//        $path = '../intranet/recibos/' . $this->ano . '/' . $vAlumno . '/';
//        $filename = $vcomp . ".pdf";
//        $this->pdf->Output($path . $filename, "F");
        // ================================================================

        $data['tipodesc'] = $vTipoDesc;
        $data['tipoElectro'] = $vTipoElectro;
        $data['empresa'] = $dataEmpresa;
        $data['alumno'] = $dataAlumno;
        $data['apoderado'] = $dataApoderado;
        $data['dataPago'] = $dataPago;
        $data['dataUsuario'] = $datoUsuario;
        $data['nemodes'] = $nemodes;
        $data['numero'] = $vcomp;
        $data['rutaqr'] = $rutaticket;
        $data['tipoDoc'] = $vtipo;
        $this->load->view('constant');
        $this->load->view('view_print_ticket', $data);
    }

    public function printcpv() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vtipo = $_POST["rbdTipo"];  // "02";  // (tipo de documento)
        $vNemo = $_POST["htxtsalon"]; // "2019034"; // (Codigo del Salon)
        $vAlumno = $_POST["htxtalumno"];  // "20170859"; // (Codigo del Alumno)
        $vcomp = $_POST["htxtnumrecibo"]; // "B001-00000101"; (Numero de comprobante)
        $vusu = $this->_session['USUCOD']; // Codigo del Usuario
        // ================= Obteniendo informacion  ===========================
        $dataSalon = $this->objSalon->getSalones($vNemo);

        $instru = $dataSalon[0]->INSTRUCOD;
        $nemodes = $dataSalon[0]->NEMODES;
        if ($instru == 'I' || $instru == 'P') {
            $vruc = RUC_PRIMARIA;
        } else {
            $vruc = RUC_SECUNDARIA;
        }
        if ($vtipo == '01') {
            $vTipoDesc = "RECIBO";
        } elseif ($vtipo == '02') {
            $vTipoDesc = "BOLETA";
        } elseif ($vtipo == '03') {
            $vTipoDesc = "FACTURA";
        }
        $dataEmpresa = $this->objSalon->getDatosEmpresa($vruc);
        $vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);
        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataFamilia = $this->objAlumno->getFamiliaAlumno($vDniAlumno);
        $dataApoderado = "";
        if ($dataFamilia->flgpadapo == '1') {
            $dataApoderado = $dataFamilia->paddni . ' - ' . $dataFamilia->padre;
        } else {
            $dataApoderado = $dataFamilia->maddni . ' - ' . $dataFamilia->madre;
        }
        $dataPago = $this->objCobros->getPagoxAlumnoRec($vcomp, $vAlumno);
        $datoUsuario = $this->objCobros->getDatoUsuario($vusu);
        /* echo "<pre>";
          print_r($dataPago);
          echo "</pre>";
          exit; */
        // ==================================================================
        $this->load->library('pdf');
        $this->pdf = new Pdf('P', 'mm', 'A4');
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle('COMPROBANTE ELECTRONICO - ' . $this->ano);
        #Establecemos los mรกrgenes izquierda, arriba y derecha:
        $this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 9, 12, 30, 28, 'PNG');

        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY(55, 8);
        $this->pdf->Cell(60, 5, utf8_decode('"' . $dataEmpresa[0]->nombre_comercial . '"'), 0, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetXY(55, 12);
        $this->pdf->Cell(60, 5, utf8_decode('"' . $dataEmpresa[0]->razon_social . '"'), 0, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY(55, 16);
        $this->pdf->Cell(60, 5, utf8_decode($dataEmpresa[0]->rd), 0, 0, 'C');


        $this->pdf->SetFont('Arial', '', 9);

        $this->pdf->SetXY(60, 25);
        $this->pdf->Cell(50, 5, utf8_decode($dataEmpresa[0]->direccion), 0, 0, 'C');

        $this->pdf->SetXY(60, 30);
        $this->pdf->Cell(50, 5, utf8_decode('Telefono : ' . $dataEmpresa[0]->telefono), 0, 0, 'C');

        $this->pdf->SetXY(60, 35);
        $this->pdf->Cell(50, 5, 'Web : ' . WEB, 0, 0, 'C');

        $this->pdf->Rect(135, 10, 60, 25);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetXY(140, 15);
        $this->pdf->Cell(50, 5, 'R.U.C : ' . $dataEmpresa[0]->ruc, 0, 0, 'C');
        $this->pdf->SetXY(140, 20);
        $this->pdf->Cell(50, 5, utf8_decode($vTipoDesc . ' ELECTRÓNICA'), 0, 0, 'C');
        $this->pdf->SetXY(140, 25);
        $this->pdf->Cell(50, 5, $vcomp, 0, 0, 'C');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(10, 41);
        $this->pdf->Cell(150, 3, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(15, 45);
        $this->pdf->Cell(20, 5, utf8_decode("Señor(es):"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(35, 45);
        $this->pdf->Cell(115, 5, utf8_decode($dataApoderado), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(150, 45);
        $this->pdf->Cell(25, 5, utf8_decode("Fecha Emisión:"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(175, 45);
        $this->pdf->Cell(20, 5, date("d/m/Y"), 0, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(15, 50);
        $this->pdf->Cell(20, 5, "Alumno(a):", 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(35, 50);
        $this->pdf->Cell(115, 5, utf8_decode($dataAlumno[0]->DNI . ' - ' . $dataAlumno[0]->NOMCOMP), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(150, 50);
        $this->pdf->Cell(25, 5, utf8_decode("Moneda:"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(175, 50);
        $this->pdf->Cell(20, 5, "SOLES", 0, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(15, 55);
        $this->pdf->Cell(20, 5, utf8_decode("Aula:"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(35, 55);
        $this->pdf->Cell(100, 5, utf8_decode($nemodes), 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(10, 60);
        $this->pdf->Cell(150, 3, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(15, 65);
        $this->pdf->Cell(10, 5, utf8_decode("ITEM"), 1, 0, 'C');
        $this->pdf->SetXY(25, 65);
        $this->pdf->Cell(15, 5, utf8_decode("TIPO"), 1, 0, 'C');
        $this->pdf->SetXY(40, 65);
        $this->pdf->Cell(90, 5, utf8_decode("CONCEPTO DE PAGO"), 1, 0, 'C');
        $this->pdf->SetXY(130, 65);
        $this->pdf->Cell(20, 5, utf8_decode("SUB TOTAL"), 1, 0, 'C');
        $this->pdf->SetXY(150, 65);
        $this->pdf->Cell(20, 5, utf8_decode("IGV (18%)"), 1, 0, 'C');
        $this->pdf->SetXY(170, 65);
        $this->pdf->Cell(20, 5, utf8_decode("IMPORTE"), 1, 0, 'C');

        $iniFila = 70;
        $filas = 1;
        $total = 0;
        foreach ($dataPago as $pago) {
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(15, $iniFila);
            $this->pdf->Cell(10, 5, utf8_decode($filas), 1, 0, 'C');
            $this->pdf->SetXY(25, $iniFila);
            $this->pdf->Cell(15, 5, utf8_decode($pago->concob), 1, 0, 'C');
            $this->pdf->SetXY(40, $iniFila);
            $this->pdf->Cell(90, 5, utf8_decode(nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob) . ' - ' . $this->ano), 1, 0, 'L');
            $this->pdf->SetXY(130, $iniFila);
            $this->pdf->Cell(20, 5, utf8_decode('S/.' . $pago->montocob), 1, 0, 'R');
            $this->pdf->SetXY(150, $iniFila);
            $this->pdf->Cell(20, 5, utf8_decode("S/.0.00"), 1, 0, 'R');
            $this->pdf->SetXY(170, $iniFila);
            $this->pdf->Cell(20, 5, utf8_decode('S/.' . $pago->montocob), 1, 0, 'R');
            $iniFila += 5;
            $total += $pago->montocob;
        }

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(150, $iniFila);
        $this->pdf->Cell(20, 5, utf8_decode("TOTAL S/."), 1, 0, 'C');
        $this->pdf->SetXY(170, $iniFila);
        $this->pdf->Cell(20, 5, utf8_decode("S/." . number_format($total, 2, '.', ',')), 1, 0, 'R');

        $this->pdf->SetFont('Arial', 'I', 9);
        $this->pdf->SetXY(15, $iniFila + 10);
        $this->pdf->Cell(120, 5, utf8_decode("SON : " . convertir_a_letras($total) . ' NUEVOS SOLES.'), 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->SetXY(140, $iniFila + 16);
        $this->pdf->Cell(120, 5, utf8_decode("Hora             : " . date('H:i:s')), 0, 0, 'L');
        $this->pdf->SetXY(140, $iniFila + 20);
        $this->pdf->Cell(120, 5, utf8_decode("Atendido por : " . $datoUsuario->nomcomp), 0, 0, 'L');
        // =============== Creacion de directorio por Alumno ==================
        if (!is_dir('../intranet/recibos/' . $this->ano))
            mkdir('../intranet/recibos/' . $this->ano, 0755);
        if (!is_dir('../intranet/recibos/' . $this->ano . '/' . $vAlumno))
            mkdir('../intranet/recibos/' . $this->ano . '/' . $vAlumno, 0755);
        $path = '../intranet/recibos/' . $this->ano . '/' . $vAlumno . '/';
        $filename = $vcomp . ".pdf";
        $this->pdf->Output($path . $filename, "F");
        // ================================================================
        //$this->pdf->Output('Comprobante_Electronico.pdf', 'I');
    }

    public function printeecc() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        // print_r($_POST); exit;
        $vNemo = $_POST["htxtsalon"]; //$_POST["cbsalon"];
        $vAlumno = $_POST["htxtalumno"]; //$_POST["cbalumno"];

        $dataSalon = $this->objSalon->getSalones($vNemo);
        $vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);
        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataPago = $this->objCobros->getPagoxAlumno($vAlumno);

        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        #Establecemos los mรกrgenes izquierda, arriba y derecha:
        //$this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE DE EE.CC  - ' . date("Y"), 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Código : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, $dataSalon[0]->NEMO, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Aula : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, utf8_decode($dataSalon[0]->NEMODES), 0, 0, 'L');
        //$this->pdf->Ln ();
        //$this->pdf->SetFont ('Arial', '', 10);
        // $this->pdf->Cell (275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        //$this->pdf->Line (10, 28, 200, 28);
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Alumno(a) : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(120, 5, utf8_decode($dataAlumno[0]->NOMCOMP), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->SetFont('Arial', 'B', 10);
        $valorY = 50;
        // $this->pdf->SetXY (11, $valorY);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(20, 5, utf8_decode('CÓDIGO'), 0, 0, 'C');
        $this->pdf->Cell(70, 5, utf8_decode('CONCEPTO DE CONCEPTO'), 0, 0, 'C');
        $this->pdf->Cell(20, 5, utf8_decode('PERIODO'), 0, 0, 'C');
        $this->pdf->Cell(15, 5, utf8_decode('MES'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('FECHA-VEN'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('FECHA-COBRO'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('RECIBO'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('PENDIENTE'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('COBRADO'), 0, 0, 'C');
        // $this->pdf->Cell (20, 10, utf8_decode ('T-PAGO'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('TOTAL'), 0, 0, 'C');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', '', 10);
        $vtotalp = 0;
        $vtotalc = 0;
        $vtotalt = 0;
        foreach ($dataPago as $pagos) {
            $this->pdf->Cell(20, 5, $pagos->concob, 0, 0, 'C');
            $this->pdf->Cell(70, 5, (($pagos->concob == '01') ? ( utf8_decode($pagos->condes . ' - ' . $pagos->mesdes)) : utf8_decode($pagos->condes)), 0, 0, 'L');
            $this->pdf->Cell(20, 5, $pagos->mescob, 0, 0, 'C');
            $this->pdf->Cell(15, 5, $pagos->mesdes, 0, 0, 'C');
            $this->pdf->Cell(25, 5, invierte_date($pagos->fecven), 0, 0, 'C');
            $this->pdf->Cell(25, 5, invierte_date($pagos->fecmod), 0, 0, 'C');
            $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
            $this->pdf->Cell(25, 5, $pagos->monsig . $pagos->montopen, 0, 0, 'R');
            $this->pdf->Cell(25, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
            $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
            $this->pdf->Ln(5);
            $vtotalp += $pagos->montopen;
            $vtotalc += $pagos->montocob;
            $vtotalt += $pagos->montocob;
        }

        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(205, 5, ' ', 0, 0, 'C');
        $this->pdf->Cell(25, 5, 'S/.' . number_format($vtotalp, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(25, 5, 'S/.' . number_format($vtotalc, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(20, 5, 'S/.' . number_format($vtotalt, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

        $this->pdf->Output('Reporte_de_EECC_por_Alumno.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
