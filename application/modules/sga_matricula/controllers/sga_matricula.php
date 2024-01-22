<?php

/**
 * @package       modules/sga_matricula/controller
 * @name            sga_alumnos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - .10.2017
 */
class sga_matricula extends CI_Controller {

    public $token = '';
    public $modulo = 'MATRICULA';
    public $datasession = '';
    public $ano = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('seguridad_model');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('familia_model', 'objFamilia');
        $this->load->model('matricula_model', 'objMatricula');
        $this->load->model('cobros_model', 'objCobro');
        $this->datasession = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
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
        $flg_matricula = $this->objMatricula->verificaConfigMatricula();
        $data['token'] = $this->token();
        $data['vano'] = date("Y");
        $data['s_ano_vig'] = $this->nativesession->get('S_ANO_VIG');
        $data['flg_matricula'] = $flg_matricula;
        $data['lstAulas'] = $this->objSalon->getAllAulas();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('matricula-js');
        $this->load->view('lista-matricula-view', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function getpagos() {
        if ($this->input->post('token') && $this->input->post('token') == $this->nativesession->get('token')) {
            $vid = $this->input->post('vid');
            $arrDataPago = $this->objCobro->getPagoxAlumnoMatricula($vid, 1); // cambiar  por 1 al fin de año
            if (is_array($arrDataPago) && count($arrDataPago) > 0) {
                $arrayData = array('data' => $arrDataPago, 'msg' => 1);
            } else {
                $arrayData = array('data' => '', 'msg' => 2);
            }
        } else {
            $arrayData = array('data' => '', 'msg' => 2);
        }
        echo json_encode($arrayData);
    }

    public function FiltrarAlumno() {
        $arrAlumno = array();
        $vtipo = $this->input->post("vtipo");
        $vfiltro = $this->input->post("vfiltro");
        $vEstado = $this->input->post("vEstado");
        $dataAlumno = $this->objMatricula->FiltrarAlumno($vtipo, $vfiltro, $vEstado);
        if (count($dataAlumno) > 0) {
            $arrAlumno = $dataAlumno;
        }
        echo json_encode($arrAlumno);
    }

    public function getDatosAlumnoNuevo() {
        $arrAlumno = array();
        $vId = $this->input->post("vId");

        $dataAlumno = $this->objMatricula->getDataAlumnoNuevo($vId);
        if (count($dataAlumno) > 0) {
            $arrAlumno = $dataAlumno;
        }
        echo json_encode($arrAlumno);
    }

    public function verificaConducta() {
        $arrAlumno = array();
        $vDni = $this->input->post("vDni");
        $dataAlumno = $this->objMatricula->verificaConducta($vDni);
        if (count($dataAlumno) > 0) {
            $arrAlumno[] = $dataAlumno;
        }
        echo json_encode($arrAlumno);
    }

    public function getDatosAlumno() {
        $arrAlumno = array();
        $vId = $this->input->post("vId");
        $vflg = $this->input->post("vflg");

        $dataAlumno = $this->objMatricula->getDataAlumno($vId, $vflg);
        if (count($dataAlumno) > 0) {
            $arrAlumno = $dataAlumno;
        }
        echo json_encode($arrAlumno);
    }

    public function getDatosAlumnoMatriculado() {
        $arrAlumno = array();
        $vId = $this->input->post("vId");
        $vflg = $this->input->post("vflg");

        $dataAlumno = $this->objMatricula->getDataAlumnoMatriculado($vId, $vflg);
        if (count($dataAlumno) > 0) {
            $arrAlumno = $dataAlumno;
        }
        echo json_encode($arrAlumno);
    }

    public function verificaExistenciaAlumno() {
        $output = array();
        if ($this->input->is_ajax_request()) {
            $totalExiste = $this->objAlumno->existeAlumno($this->input->post("dni"));
            if ((int) $totalExiste > 0) {
                $output = array('flg' => 1, 'msg' => 'EL ALUMNO CON EL DNI INGRESADO YA EXISTE. REGISTRELO COMO ALUMNO ANTIGUO EN LA OPCIÓN [Buscar Alumnos] DESDE LA PANTALLA PRINCIPAL.', 'error' => '');
            } else {
                $output = array('flg' => 0, 'msg' => '', 'error' => '');
            }
        }
        echo json_encode($output);
    }

    public function test() {
        //  ================= Verificamos si existe la familia =========================
         $familia = "BUSTAMANTE QUIÑONES";
         $familiaExiste = $this->objFamilia->existeFamilia($familia);
         if($familiaExiste->TOTAL>0) {
             $codigoFamilia = $familiaExiste->FAMCOD;
         } else {
             $codigoFamilia ="GENERADA"; //$this->objFamilia->insertSimple($dataFam);
         }
         // =======================================================================
         echo "==>>>>>".$codigoFamilia;
    }
            
    public function saveMatricula2() {
        try {

            $output = array('flg' => '0', 'msg' => 'ACCION NO PERMITIDA', 'error' => 'ACCESO DENEGADO');
            if ($this->input->is_ajax_request()) {
                $this->db->trans_start();
                $data = $_POST;
                // ======== VARIABLES =============
                $vIdAula = $this->input->post("aulacod");
                $vAccion = (($this->input->post("accion") == 'N') ? 1 : 0);
                $vusu = $this->datasession['USUCOD'];
                // ========== PRECONSULTAS ==========        
                $vnemo = $this->objSalon->getNemoxAula($vIdAula, 0);
                $vidNemo = $vnemo->NEMO;
                $vseccion = $vnemo->SECCIONCOD;
                $vinstrucod = $vnemo->INSTRUCOD;
                $vgradocod = $vnemo->GRADOCOD;
                // =======================================
                //echo $this->input->post("accion"); exit;
                if ($this->input->post("accion") == 'N') {

                    if ($vinstrucod == 'I' || $vinstrucod == 'P') {
                        $vruc = RUC_PRIMARIA;
                        $tipoRazon = 'R01';
                    } else {
                        $vruc = RUC_SECUNDARIA;
                        $tipoRazon = 'R02';
                    }

                    $vAluGen = $this->objAlumno->generadorCodigo('ALUMNO');
                    $data['alucod'] = $vAluGen;
                    $vAlucod = $vAluGen;
                    $data['flgpadcorreo'] = (isset($data['chkpapa']) ? 'S' : 'N');
                    $data['flgmadcorreo'] = (isset($data['chkmama']) ? 'S' : 'N');
                    $data['flgapocorreo'] = (isset($data['chkapoderado']) ? 'S' : 'N');
                    $data['instrucod'] = $data['cb_nivel'];
                    $data['gradocod'] = $data['cb_grado'];
                    $data['seccioncod'] = $vseccion;
                    $data['estado'] = 'V';
                    // ============ Datos del Alumno =====================
                    $data['nombres'] = trim($data['nombres']);
                    $data['apepat'] = trim($data['apepat']);
                    $data['apemat'] = trim($data['apemat']);
                    $data['procede'] = trim($data['procede']);
                    $data['aluemail'] = trim($data['aluemail']);
                    $data['telefono'] = trim($data['telefono']);
                    $data['nomcomp'] = trim($data['apepat']) . ' ' . trim($data['apemat']) . ', ' . trim($data['nombres']);
                    $data['flg_matricula'] = 1;
                    $data['flgutiles'] = 0;
                    // ==================================================
                    $flgpapa = '0';
                    $flgmama = '0';
                    $flgapo = '0';
                    if ($data['cmbResponsablePago'] == 'P') {
                        $flgpapa = '1';
                    }
                    if ($data['cmbResponsablePago'] == 'M') {
                        $flgmama = '1';
                    }
                    if ($data['cmbResponsablePago'] == 'A') {
                        $flgapo = '1';
                    }
                    // Verificar en esta linea ya que hay veces hay recibos que salen con errores. al parewcer estan marcando BOLETAS
                    // podrimos poner el tipo por defecto 01
                    $tipocomp = (isset($data['chkTipoPago'][0]) ? $data['chkTipoPago'][0] : '');
                    $vDocumentos = (isset($data['chkDocumentos']) ? $data['chkDocumentos'] : '');
                    $flgExonera = (isset($data['chkExoneraPago']) ? '1' : '0');
                    $total = $data['totalcomp'];
                    $numcomp = $data['numcomprobante'];
                    $fecpago = $data['fecpago'];
                    $obs = $data['txtcomentarios'];
                    $obsdocus = $data['txtdsc'];
                    $obExonera = $data['txtobsExoneracion'];
                    $voucher = $data['voucher'];
                    $medioPago = $data['idmedio'];

                    unset($data['accion']);
                    unset($data['totalcomp']);
                    unset($data['numcomprobante']);
                    unset($data['flgerror']);
                    unset($data['fecpago']);
                    unset($data['chkpapa']);
                    unset($data['chkmama']);
                    unset($data['chkapoderado']);
                    unset($data['chkDocumentos']); // antes de borrar vaciar datos
                    unset($data['chkapoderado']);
                    unset($data['cmbResponsablePago']);
                    unset($data['nomcliente']);
                    unset($data['chkTipoPago']);
                    unset($data['aulaant']);
                    unset($data['cb_nivel']);
                    unset($data['cb_grado']);
                    unset($data['aulacod']);
                    unset($data['hdnemo']);
                    unset($data['txtcomentarios']);
                    unset($data['chkPrint']);
                    unset($data['chkPrint1']);
                    unset($data['chkJalar']);
                    unset($data['txtdsc']);
                    unset($data['chkExoneraPago']);
                    unset($data['txtobsExoneracion']);
                    unset($data['voucher']);
                    unset($data['idmedio']);

                    $dataFam = array(
                        'FAMDES' => trim($data['apepat']) . ' ' . trim($data['apemat']),
                        'PADAPEPAT' => trim($data['padpater']),
                        'MADAPEPAT' => trim($data['madpater']),
                        'PADMAIL' => trim($data['pademail']),
                        'MADMAIL' => trim($data['mademail']),
                        'PADDNI' => trim($data['dnipater']),
                        'PADAPEMAT' => trim($data['padmater']),
                        'PADNOMBRE' => trim($data['padnom']),
                        'MADDNI' => trim($data['dnimater']),
                        'MADAPEPAT' => trim($data['madpater']),
                        'MADAPEMAT' => trim($data['madmater']),
                        'MADNOMBRE' => trim($data['madnom']),
                        
                        'APONOMBRE' => trim($data['aponom']),
                         'APOAPEPAT' => trim($data['apopater']),
                        'APOAPEMAT' => trim($data['apomater']),
                        'APOMAIL' => trim($data['apoemail']),
                        'APODNI' => trim($data['dniapo']),
                        
                        
                        
                        'FLAG' => '1',
                        'FLGPADAPO' => $flgpapa,
                        'FLGMADAPO' => $flgmama,
                        'FLGAPO' => $flgapo
                    );
                    //  ================= Verificamos si existe la familia =========================
                    $familia = trim($data['apepat']) . ' ' . trim($data['apemat']);
                    $familiaExiste = $this->objFamilia->existeFamilia($familia);
                    if($familiaExiste->TOTAL>0) {
                        $codigoFamilia = $familiaExiste->FAMCOD;
                    } else {
                        $codigoFamilia =$this->objFamilia->insertSimple($dataFam);
                    }
                    // =======================================================================
                    //$respFam = $this->objFamilia->insertSimple($dataFam);
                    $data['famcod'] = $codigoFamilia; // $respFam; //$this->objFamilia->insertSimple($dataFam);
                    $data['discod'] = 'D';
                    $data['matricula'] = 'N';
                    //$data['tipopago'] = (($medioPago != '' && $medioPago == '1') ? 'C' : ($medioPago != '') ? 'B' : 'C');
                    $resp = $this->objAlumno->saveUpdate($data, $vAccion);

                    $vGenerado = $this->objMatricula->genCodigo();
                    $arrdata = array(
                        'aluant' => $vAlucod,
                        'alucod' => $vGenerado,
                        'observacion' => $obs,
                        'obsdocumentos' => $obsdocus,
                        'obsExoneracion' => $obExonera,
                        'flgexoMatricula'=>$flgExonera,
                        'dni' => $this->input->post("dni"),
                        'nemo' => $vidNemo,
                        'periodo' => '2024',
                        'fecmat' => date('Y-m-d H:i:s'),
                        'numlibro' => '0',
                        'estado' => 'M',
                        'usureg' => $vusu
                    );
                    $resp = $this->objMatricula->saveMatricula($arrdata);
                    $this->objMatricula->updateCodigo($vGenerado);

                    $this->objSalon->insertaAlumnoSalon($vAlucod, $vidNemo);
                    $this->objMatricula->generarPagosAlumno($vAlucod, $vinstrucod);
                    if ($vDocumentos != '') {
                        $this->objAlumno->grabaAluDocumentos($vAlucod, $vDocumentos);
                    }
                    // ENVIAMOS SOLE EL PAGO DE MATRICULA
                   // if ($flgExonera == '0') {
                        if (trim($medioPago) == '') {
                            $medioPago = 0;
                        }
                        if ($flgExonera == '1') {
                            $fecpago = trim($fecpago).' '.date("H:i:s");
                            $this->objCobro->grabarPensionExo($vAlucod, '03', '02', $numcomp, $total, 'C', $fecpago, $tipocomp, $tipoRazon, 1, $voucher, $medioPago);
                        } else {
                            $this->objCobro->grabarPension($vAlucod, '03', '02', $numcomp, $total, 'C', $fecpago, $tipocomp, $tipoRazon, 0, $voucher, $medioPago);
                        }
                        if ($vusu != 'SISTEMAS') {
                            $dataControl = array(
                                'usuret' => $vusu,
                                'fecharet' => date("Y-m-d H:i:s"),
                                'flgret' => 1
                            );
                            $this->objCobro->actualiza_control($dataControl, $numcomp);
                        }
                   // }
                    $output = array('flg' => '0', 'msg' => 'MATRICULA REGISTRADA CORRECTAMENTE.', 'error' => $codigoFamilia, 'vnemo' => $vidNemo, 'valucod' => $vAlucod, 'vtipocomp' => $tipocomp, 'vnum' => $numcomp);
                } else {

                    if ($vinstrucod == 'I' || $vinstrucod == 'P') {
                        $vruc = RUC_PRIMARIA;
                        $tipoRazon = 'R01';
                    } else {
                        $vruc = RUC_SECUNDARIA;
                        $tipoRazon = 'R02';
                    }

                    $data['flgpadcorreo'] = (isset($data['chkpapa']) ? 'S' : 'N');
                    $data['flgmadcorreo'] = (isset($data['chkmama']) ? 'S' : 'N');
                    $data['flgapocorreo'] = (isset($data['chkapoderado']) ? 'S' : 'N');
                    $data['instrucod'] = (isset($data['cb_nivel']) ? $data['cb_nivel'] : $vinstrucod);
                    $data['gradocod'] = (isset($data['cb_grado']) ? $data['cb_grado'] : $vgradocod);
                    $data['seccioncod'] = $vseccion;
                    $data['estado'] = 'V';
                    // ============ Datos del Alumno =====================
                    $data['nombres'] = trim($data['nombres']);
                    $data['apepat'] = trim($data['apepat']);
                    $data['apemat'] = trim($data['apemat']);
                    $data['procede'] = trim($data['procede']);
                    $data['aluemail'] = trim($data['aluemail']);
                    $data['telefono'] = trim($data['telefono']);
                    $data['nomcomp'] = trim($data['apepat']) . ' ' . trim($data['apemat']) . ', ' . trim($data['nombres']);
                    // ==================================================
                    $data['flg_matricula'] = 1;
                    $data['flgutiles'] = 0;
                    $flgpapa = '0';
                    $flgmama = '0';
                    $flgapo = '0';
                    if ($data['cmbResponsablePago'] == 'P') {
                        $flgpapa = '1';
                    }
                    if ($data['cmbResponsablePago'] == 'M') {
                        $flgmama = '1';
                    }
                    if ($data['cmbResponsablePago'] == 'A') {
                        $flgapo = '1';
                    }

                    $tipocomp = (isset($data['chkTipoPago'][0]) ? $data['chkTipoPago'][0] : '');
                    $vDocumentos = (isset($data['chkDocumentos']) ? $data['chkDocumentos'] : '');
                    $flgExonera = (isset($data['chkExoneraPago']) ? '1' : '0');
                    $total = $data['totalcomp'];
                    $numcomp = $data['numcomprobante'];
                    $fecpago = $data['fecpago'];
                    $vfamcod = $data['famcod'];
                    $vAlucod = $data['alucod'];
                    $vdni = $data['dni'];
                    $obs = $data['txtcomentarios'];
                    $obsdocus = $data['txtdsc'];
                    $acc = $data['accion'];
                    $obExonera = $data['txtobsExoneracion'];
                    $voucher = $data['voucher'];
                    $medioPago = $data['idmedio'];

                    unset($data['accion']);
                    unset($data['totalcomp']);
                    unset($data['numcomprobante']);
                    unset($data['flgerror']);
                    unset($data['fecpago']);
                    unset($data['chkpapa']);
                    unset($data['chkmama']);
                    unset($data['chkapoderado']);
                    unset($data['chkDocumentos']); // antes de borrar vaciar datos
                    unset($data['chkapoderado']);
                    unset($data['cmbResponsablePago']);
                    unset($data['nomcliente']);
                    unset($data['chkTipoPago']);
                    unset($data['aulaant']);
                    unset($data['cb_nivel']);
                    unset($data['cb_grado']);
                    unset($data['aulacod']);
                    unset($data['hdnemo']);
                    unset($data['txtcomentarios']);
                    unset($data['chkPrint']);
                    unset($data['chkPrint1']);
                    unset($data['chkJalar']);
                    unset($data['txtdsc']);
                    unset($data['chkExoneraPago']);
                    unset($data['txtobsExoneracion']);
                    unset($data['voucher']);
                    unset($data['idmedio']);

                    $dataFam = array(
                        'FAMDES' => trim($data['apepat']) . ' ' . trim($data['apemat']),
                        'PADAPEPAT' => trim($data['padpater']),
                        'MADAPEPAT' => trim($data['madpater']),
                        'PADMAIL' => trim($data['paddireccion']),
                        'MADMAIL' => trim($data['maddireccion']),
                        'PADDNI' => trim($data['dnipater']),
                        'PADAPEPAT' => trim($data['padpater']),
                        'PADAPEMAT' => trim($data['padmater']),
                        'PADNOMBRE' => trim($data['padnom']),
                        'MADDNI' => trim($data['dnimater']),
                        'MADAPEPAT' => trim($data['madpater']),
                        'MADAPEMAT' => trim($data['madmater']),
                        'MADNOMBRE' => trim($data['madnom']),
                        'FLAG' => '1',
                        'FLGPADAPO' => $flgpapa,
                        'FLGMADAPO' => $flgmama,
                        'FLGAPO' => $flgapo
                    );

                    if ($acc == 'E') { // Si es Edicion de los Datos 
                        $this->objFamilia->update($dataFam, $vfamcod);
                        $data['discod'] = 'D';
                        $data['matricula'] = 'N';
                        // $data['tipopago'] = (($medioPago != '' && $medioPago == '1') ? 'C' : ($medioPago != '') ? 'B' : 'C');
                        $resp = $this->objAlumno->saveUpdate($data, $vAccion);
                        $this->objSalon->updateAlumnoSalon($vAlucod, $vidNemo);
                        $arrdata = array(
                            'nemo' => $vidNemo,
                            'usumod' => $vusu,
                            'fecmod' => date('Y-m-d'),
                            'observacion' => $obs,
                            'obsdocumentos' => $obsdocus,
                            'obsExoneracion' => $obExonera
                        );
                        $this->objMatricula->updateMatricula($arrdata, $vdni);
                        if ($vDocumentos != '') {
                            $this->objAlumno->grabaAluDocumentos($vAlucod, $vDocumentos, '', 1);
                        }
                    } else {
                        $this->objFamilia->update($dataFam, $vfamcod);
                        $data['discod'] = 'D';
                        $data['matricula'] = 'N';
                        //$data['tipopago'] = (($medioPago != '' && $medioPago == '1') ? 'C' : ($medioPago != '') ? 'B' : 'C');
                        $resp = $this->objAlumno->saveUpdate($data, $vAccion);
                        $vGenerado = $this->objMatricula->genCodigo();
                        $arrdata = array(
                            'aluant' => $vAlucod,
                            'alucod' => $vGenerado,
                            'observacion' => $obs,
                            'obsdocumentos' => $obsdocus,
                            'flgexoMatricula'=>$flgExonera,
                            'dni' => $this->input->post("dni"),
                            'nemo' => $vidNemo,
                            'periodo' => '2024',
                            'fecmat' => date('Y-m-d H:i:s'),
                            'numlibro' => '0',
                            'estado' => 'M',
                            'usureg' => $vusu
                        );
                        $this->objMatricula->saveMatricula($arrdata);
                        $this->objMatricula->updateCodigo($vGenerado);

                        $this->objSalon->insertaAlumnoSalon($vAlucod, $vidNemo);
                        $this->objMatricula->generarPagosAlumno($vAlucod, $vinstrucod);

                        if ($vDocumentos != '') {
                            $this->objAlumno->grabaAluDocumentos($vAlucod, $vDocumentos, '', 1);
                        }
                        // ENVIAMOS SOLE EL PAGO DE MATRICULA
                      //  if ($flgExonera == '0') {
                            if (trim($medioPago) == '') {
                                $medioPago = 0;
                            }
                        if ($flgExonera == '1') {
                            $fecpago = trim($fecpago).' '.date("H:i:s");
                            $this->objCobro->grabarPensionExo($vAlucod, '03', '02', $numcomp, $total, 'C', $fecpago, $tipocomp, $tipoRazon, 1, $voucher, $medioPago);
                        } else {
                            $this->objCobro->grabarPension($vAlucod, '03', '02', $numcomp, $total, 'C', $fecpago, $tipocomp, $tipoRazon, 0, $voucher, $medioPago);
                        }
                            if ($vusu != 'SISTEMAS') {
                                $dataControl = array(
                                    'usuret' => $vusu,
                                    'fecharet' => date("Y-m-d H:i:s"),
                                    'flgret' => 1
                                );
                                $this->objCobro->actualiza_control($dataControl, $numcomp);
                            }
                      //  }
                    }
                    $output = array('flg' => '0', 'msg' => 'MATRICULA REGISTRADA CORRECTAMENTE.', 'error' => $resp, 'vnemo' => $vidNemo, 'valucod' => $vAlucod, 'vtipocomp' => $tipocomp, 'vnum' => $numcomp);
                }

                $this->db->trans_complete();
                $trans_status = $this->db->trans_status();
                if ($trans_status == FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }
            }
            echo json_encode($output);
        } catch (Exception $e) {
            $output = array('flg' => '0', 'msg' => 'OCURRIO UN ERROR AL EJECUTAR EL PROCESO DE MATRICULA\nEL SISTEMA YA INFORMO AL ADMINISTRADOR SOBRE EL ERROR OCURRIDO.', 'error' => $e->getMessage());
            //var_dump($e->getMessage());
            echo json_encode($output);
        }
    }

    public function saveMatricula() {

        $vAnio = $this->input->post("hanio");
        $vIdAula = $this->input->post("cb_aula");
        $vAccion = $this->input->post("haccion");
        $vnemo = $this->objSalon->getNemoxAula($vIdAula, 0);
        $vidNemo = $vnemo->NEMO;
        $vseccion = $vnemo->SECCIONCOD;
        $vinstrucod = $vnemo->INSTRUCOD;

        $vusu = $this->datasession['USUCOD'];
        $vDni = $this->input->post("lbldni"); //$this->input->post ("txtalucod");
        $vAlucod = $this->input->post("txtalucod");
        $vInstru = $this->input->post("hinstru");
        $vEstado = $this->input->post("hestado");
        $vDniTemp = trim($this->input->post("hdni"));
        $vLibro = $this->input->post("lbllibro");
        if ($vAccion == 'M' || $vEstado == 'N') { // M : Matricular / N: Nuevo
            // ================== 1. INSERTANDO EN LA TABLA MATRICULA ====================
            if ($vEstado == 'N') {
                $vGenerado = $vAlucod;
                $vInstru = $vinstrucod;
            } else {
                $vGenerado = $this->objMatricula->genCodigo();
            }
            $arrdata = array(
                'aluant' => $this->input->post("txtalucod"),
                'alucod' => $vGenerado,
                'dni' => $this->input->post("lbldni"),
                'nemo' => $vidNemo,
                'periodo' => $vAnio, //ANO_VIG,
                'fecmat' => date('Y-m-d H:i:s'),
                'numlibro' => $vLibro,
                'estado' => 'M',
                'usureg' => $vusu
            );
            /* if ($vEstado == 'N') {
              $arrdata['instrucod'] = $this->input->post ("cb_nivel");
              $arrdata['gradocod'] = $this->input->post ("cb_grado");
              $arrdata['seccioncod'] = $vseccion;
              } */
            if ($vDniTemp == '') { // Si el alumno biene con dni vacio, actualizamos la tabla de alumno
                $this->objAlumno->updateDatosAlumno($vAlucod, $vDni);
            }

            $this->objAlumno->updateNumLibroAlumno($vAlucod, $vLibro);

            $resp = $this->objMatricula->saveMatricula($arrdata);
            // ================== 2. INSERTANDO EN LA TABLA ALUMNO ====================
            // ================== 3. INSERTANDO EN LA TABLA SALON_AL ====================
            $this->objSalon->insertaAlumnoSalon($vAlucod, $vidNemo);
            // ================== 4. INSERTANDO LOS PAGOS  ====================
            $this->objMatricula->generarPagosAlumno($vAlucod, $vInstru);
            // ================== 5. REALIZAMOS EL PAGO DE LA MATRICULA ============
            // ================== Generando Codigo de Documento ============
            /* $dataSalon = $this->objSalon->getSalones($vIdnemo);
              $instru = $dataSalon[0]->INSTRUCOD;
              $vtipodocu = TP_COMP; // Boleta por defecto
              if ($instru == 'I' || $instru == 'P') {
              $vruc = RUC_PRIMARIA;
              } else {
              $vruc = RUC_SECUNDARIA;
              }
              $vusu = $this->_session['USUCOD'];
              $cadGenerado = $this->objCobros->getGeneraNumero($vruc, $vtipodocu);
              $genCod = $cadGenerado[0]->codigoGenerado;

              $arrDatoPago = array(
              'montocob' => 320,
              'montopen' => 0,
              'estado' => 'C',
              'usumod' => $vusu,
              'tipo_comp' => '',
              'tipo_razon' => '',
              'numrecibo' => $genCod
              );
              $this->objCobro->updatePensionCarga($arrDatoPago, $vAlucod, '03', '02', '2020'); */
            // ==================================
        } else {
            $this->objSalon->updateAlumnoSalon($vAlucod, $vidNemo);
            $vGenerado = $this->input->post("txtalucod");
            $arrWhere = $this->input->post("lbldni");
            $arrdata = array(
                'nemo' => $vidNemo,
                'usumod' => $vusu,
                'fecmod' => date('Y-m-d'),
                'numlibro' => $vLibro
            );
            $resp = $this->objMatricula->updateMatricula($arrdata, $arrWhere);
        }
        if ($resp) {

            $this->objSalon->updateSalonAlumno($vDni, $vidNemo);
            // ----- Grabando documentos del alumno -----------------------------------------------
            if ($this->input->post("chkDocumentos")) {
                $vDocumentos = $this->input->post("chkDocumentos");
                $vcomentario = $this->input->post("txtcomentarios");
                if ($vEstado == 'N') {
                    $this->objAlumno->grabaAluDocumentos($vAlucod, $vDocumentos, $vcomentario, 1);
                } else {
                    if ($vAccion == 'M') {
                        $this->objMatricula->updateCodigo($vGenerado);
                        $this->objAlumno->grabaAluDocumentos($vAlucod, $vDocumentos, $vcomentario);
                    } else {
                        $this->objAlumno->grabaAluDocumentos($vAlucod, $vDocumentos, $vcomentario, 1);
                    }
                }
            }
            if ($vAccion == 'M')
                $output = array('flg' => 0, 'msg' => 'MATRICULA REGISTRADA CORRECTAMENTE.', 'error' => '');
            else
                $output = array('flg' => 0, 'msg' => 'MATRICULA MODIFICADA CORRECTAMENTE.', 'error' => '');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN EL PROCESO\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error' => $resp);
        }
        echo json_encode($output);
    }

    public function getGrado($vNivel = '') {
        $arrDataGrado = array();
        $dataGrado = $this->objAlumno->getListaGrado($vNivel);
        foreach ($dataGrado as $grado) {
            $arrDataGrado[$grado->GRADOCOD] = $grado->GRADODES;
        }
        echo json_encode($arrDataGrado);
    }

    public function getSeccion($vNivel = '', $vGrado = '') {
        $arrDataSecc = array();
        $dataSeccion = $this->objAlumno->getListaAulas($vNivel, $vGrado);
        foreach ($dataSeccion as $lstSecc) {
            $arrDataSecc[$lstSecc->SECCIONCOD] = $lstSecc->AULADES;
        }
        echo json_encode($arrDataSecc);
    }

    public function eliminaMatricula() {
        $vId = $this->input->post("vId");
        $arrData = array(
            'estado' => 'P',
            'flg_matricula' => 0
        );
        $resp = $this->objMatricula->updateAlumno($arrData, $vId);

        $arrData = array(
            'estado' => 'E',
            'fecmod' => date("Y-m-d H:i:s"),
            'usumod' => $this->datasession['USUCOD']
        );
        $resp = $this->objMatricula->eliminaMatricula($arrData, $vId);
        if ($resp) {
            $arrData = array('estado' => 'E');
            $this->objMatricula->updateSalonAl($arrData, $vId);
            $arrData = array(
                'numrecibo' => '',
                'estado' => 'P',
                'montocob' => 0,
                'montopen' => 250,
                'idmedio' => '',
                'voucher' => '',
                'fecmod' => '0000-00-00 00:00:00',
                'usumod' => '',
                'tipopago' => 'C',
                'tipo_comp' => '00',
                'tipo_razon' => 'R00'
            );
            //$this->objMatricula->updatePagoMatricula($arrData, $vId);
            $output = array('flg' => 0, 'msg' => 'SE ELIMINO CORRECTAMENTE LA MATRICULA.', 'error' => '');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN EL PROCESO\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error' => $resp);
        }
        echo json_encode($output);
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            $vano = $this->nativesession->get('S_ANO_VIG');
            $output = array();
            $arrData = array();
            $data = $this->objMatricula->get_datatables();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $img = base_url() . '/images/bt_mini_delete.png';
                    $img2 = base_url() . '/images/bt_mini_edit.png';
                    $img3 = base_url() . '/images/barcode.png';
                    if ($vano >=2021 /* date("Y") */ && $fila->estado == 'M') {
                        $conf = "<img src='$img'  title='Eliminar Matricula' onclick=\"javascript:js_eliminar('$fila->alucod','$fila->nomcomp');\" style='cursor:pointer' />";
                        //if ($this->datasession['USUCOD'] == 'SISTEMAS') {
                        $conf .= "&nbsp;<img src='$img2'  title='Editar Matricula' onclick=\"javascript:js_editarMatricula('$fila->alucod');\" style='cursor:pointer' />";

                        $conf .= "&nbsp;<img src='$img3'  title='Imprimir Etiqueta' onclick=\"javascript:js_printEtiqueta('$fila->alucod');\" style='cursor:pointer;width:25px;height:25px'  />";
                        // }
                        //$conf = "&nbsp;&nbsp;<span style='font-size:15px; color:blue;cursor:pointer;' onclick=\"javascript:js_matricular('$fila->alucod','$fila->flg_matricula');\"  class='glyphicon glyphicon-pencil' data-toggle='tooltip' title='Editar'></span>";
                        //$conf = "-";
                    } else {
                         //$conf = "&nbsp;<img src='$img2'  title='Editar Matricula' onclick=\"javascript:js_editarMatricula('$fila->alucod');\" style='cursor:pointer' />";
                        $conf = "-";
                    }
                    $vestado = '';
                    if ($fila->estado == 'M') {
                        $vestado = 'Matriculado';
                    } elseif ($fila->estado == 'R') {
                        $vestado = '<b>Retirado</b>';
                    } elseif ($fila->estado == 'E') {
                        $vestado = '<b>Eliminado</b>';
                    }
                    $arrData [] = array(
                        "periodo" => $fila->periodo,
                        "codigo" => $fila->dni,
                        "nomcomp" => $fila->nomcomp,
                        "alucod" => $fila->alucod,
                        "ngs" => $fila->instrucod . $fila->gradocod . $fila->seccioncod,
                        "aula" => $fila->aulades,
                        "fecmat" => $fila->fecmat,
                        "estado" => $vestado,
                        "conf" => $conf
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objMatricula->count_all(),
                    "recordsFiltered" => $this->objMatricula->count_filtered(),
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

    public function printServicio() {
        /* echo "<pre>";
          print_r($_POST);
          echo "</pre>"; */
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }

        $apo = $_POST['hnomcomp'];
        $apodni = $_POST['hdnip'];
        $apodireccion = $_POST['hdireccion'];
        $nomhijo = $_POST['hnomcompa'];
        $grado = $_POST['hgrado'];
        $nivel = $_POST['hnivel'];
        $documentos = $_POST['hdocumentos'];
        $txtdsc = $_POST['htxtdsc'];
        $txtAula = $_POST['haula'];
        $tipoAlu = $_POST['htipoalu'];
        if ($tipoAlu == 'M') {
            $txtAula = ' del Aula: ' . $txtAula;
        } else {
            $txtAula = '';
        }

        if ($documentos != '') {
            $arrDocumentos = explode("*", $documentos);
        } else {
            $arrDocumentos = array();
        }

         if (trim($nivel) == 'INICIAL'){
             $matricula="370.00";
             $pension="400.00";
             $pension2="400.00";
         } else { // PRIMARIA Y SECUNDARIA
             $matricula="395.00";
             $pension="450.00";            
             $pension2="450.00";
         }
        if (trim($nivel) == 'INICIAL' || trim($nivel) == 'PRIMARIA') {            
            $VRUC = "20517718778";
            $DRELM = "02959";
            $TPGERENTE = "Promotor - Gerente General";
            $RAZON = "la Corporación Educativa Colegio Marianista S.A.C.";
        } else {
            $VRUC = "20556889237";
            $DRELM = "0245";
            $TPGERENTE = "Promotor - Director";
            $RAZON = "el Colegio Marianistas de V.M.T. S.A.C. ";
        }
        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P');
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle('CONTRATO DE SERVICIO EDUCATIVO - ' . $this->ano);
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/logo_header.jpg', 15, 5, 170, 17);
        $this->pdf->SetFont('Arial', 'U', 10);
        $this->pdf->SetXY(15, 25);
        $this->pdf->Cell(170, 10, utf8_decode('CONTRATO POR PRESTACIÓN DE SERVICIOS EDUCATIVOS AÑO ACADÉMICO ') . $this->ano, 0, 0, 'C');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 35);
        $this->pdf->MultiCell(170, 5, utf8_decode('Conste por el presente, que suscribimos de una parte ' . $RAZON . ' con RUC N°' . $VRUC . ', con autorización de funcionamiento mediante Resolución DRELM N°' . $DRELM . ', debidamente representado por su ' . $TPGERENTE . ' Domingo Huaytalla Ll. y de la otra parte: Don(ña): ' . $apo . ' con DNI N°: ' . $apodni . ' domiciliado en ' . $apodireccion . ' , EL PADRE DE FAMILIA O APODERADO del menor: ' . $nomhijo . ' Grado ' . str_replace("AñO", "Año", strtoupper($grado)) . ' Nivel: ' . $nivel . ' para el presente Año Escolar ' . $this->ano . '.'), 0, 'J'); //. ' Nivel: ' . $nivel . (($txtAula != "") ? $txtAula : "") .

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 70);
        $this->pdf->Cell(180, 5, 'PRIMERO: ', 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 75);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Colegio Marianista es una Institución Educativa de carácter privado que imparte educación escolarizada de Educación Básica Regular, de conformidad a lo establecido en la Ley General de Educación (N°28044) - y sus reglamentos -, Ley de Centros Educativos Privados (N° 26549), Reglamento de Instituciones Educativas Privadas de Educación Básica y Educación Técnico Productiva - DS N°009-2006-ED, Ley de Promoción de la inversión en la Educación - DL 882 -y sus reglamentos-, DU N°002-2020, DS Nº005-2021-MINEDU  -y su reglamento-, RM Nº531-2021-MINEDU y el Reglamento Interno del COLEGIO. Asimismo, el Colegio Marianista ha elaborado su Plan Curricular '.$this->ano.', en concordancia con el Diseño Curricular Nacional (DCN) y propuestas pedagógicas con reajustes permanentes de acuerdo con las nuevas tendencias educativas, sistemas de evaluación y control de los estudiantes.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 120);
        $this->pdf->Cell(180, 5, 'SEGUNDO: ', 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 125);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Colegio Marianista brinda una educación integral a favor del estudiante. Esto implica el cuidado de una formación espiritual, moral y socioafectiva. La promoción y el desarrollo de capacidades, a través de nuestros planes y programas educativos científico-humanista, conducirán a nuestros estudiantes a un exitoso desempeño personal. Asimismo, fomentaremos el desarrollo de sus habilidades, talentos en las áreas artísticas, literarias y deportivas; para lo cual incluimos dentro de nuestro servicio: actividades extracurriculares, talleres temporales y electivos de manera gratuita.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 155);
        $this->pdf->MultiCell(170, 5, utf8_decode('Nuestra institución educativa se empeña en formar líderes éticos, buscando el crecimiento personal de cada estudiante gracias a una sólida formación y desarrollo de virtudes que se irán plasmando en valores.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 165);
        $this->pdf->MultiCell(170, 5, utf8_decode('Es también parte de nuestro servicio brindar información sobre los resultados del proceso educativo y formativo de su hijo(a), dando las indicaciones y orientaciones destinadas a mejorar el rendimiento académico o el comportamiento del estudiante de acuerdo con el reglamento interno del COLEGIO.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 180);
        $this->pdf->Cell(180, 5, 'TERCERO: ', 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 185);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Padre de Familia o Apoderado del menor en su rol protagónico como parte de la comunidad educativa, asume los siguientes compromisos: 1) Participar activamente en el proceso educativo de su menor hijo(a) en actividades académicas, formativas, deportivas, recreativas, culturales, religiosas y sociales; 2) Cumplir de manera asertiva con lo establecido en el Reglamento Interno del Colegio Marianista '.$this->ano.', 3) Su menor hijo(a) debe contar con seguro médico, como: SIS, EsSalud, Seguro Policial, Seguro de Fuerzas Armadas, Seguro Privado, entre otros; siendo responsabilidad del padre de familia. 4) Enviar a su menor hijo(a) correctamente uniformado desde el primer día de clases. 5) Asistir a las citaciones, acompañamientos externos o terapias, en caso lo recomiende el Departamento Psicológico. 6) Consignar sus datos correctamente, tales como: número de celular vigente y operativo, correo electrónico, dirección actualizada y otros que la institución requiera. 7) En caso de que el firmante líneas abajo sea apoderado, debe acreditarse a través de una carta notarial y quien asumirá la responsabilidad sobre el menor en la institución educativa.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 240);
        $this->pdf->MultiCell(165, 5, utf8_decode('CUARTO: Modalidades de enseñanza'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 245);
        $this->pdf->MultiCell(170, 5, utf8_decode('Nuestra propuesta educativa implica una alta exigencia académica como colegio privado que somos. La modalidad de enseñanza vigente es la presencial y consiste en el dictado de clases de 5 días por semana con la asistencia de los estudiantes de manera presencial a las instalaciones del Colegio, correctamente uniformados y de acuerdo a un horario establecido por nivel y grado que el Colegio les brinda.'), 0, 'J');

        
        
         /*$this->pdf->SetFont('Arial', '', 9);   
        $this->pdf->SetXY(22, 240);
        $this->pdf->MultiCell(170, 5, utf8_decode('1.	Presencial: la modalidad presencial en el Colegio Marianista consiste en el dictado de clases de 5 días por semana con la asistencia de los estudiantes de manera presencial en las instalaciones del Colegio y, de acuerdo a un horario establecido por nivel y grado que el Colegio les brinda.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(22, 255);
        $this->pdf->MultiCell(170, 5, utf8_decode('2.	Semipresencial: la modalidad semipresencial consta del dictado de clases de 5 días por semana con la asistencia de los estudiantes de manera presencial 3 días a la semana y de manera virtual sincrónica 2 días a la semana. El horario de clase es de acuerdo a un horario establecido por nivel y grado que el Colegio les brinda.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(22, 270);
        $this->pdf->MultiCell(170, 5, utf8_decode('3.	Virtual: la modalidad virtual en el Colegio Marianista solo se desarrollaría en caso de que el Ministerio de Educación obligue el retorno a esta modalidad de enseñanza, de acuerdo al comportamiento de la pandemia. Constaría del dictado de clases virtuales sincrónicas a través de plataformas digitales.'), 0, 'J');
*/
        
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/logo_header.jpg', 15, 5, 170, 17);
       /*$this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 10);
        $this->pdf->MultiCell(165, 5, utf8_decode('QUINTO: De los posibles cambios en las modalidades de enseñanza'), 0, 'J');
        
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 15);
        $this->pdf->MultiCell(170, 5, utf8_decode('El colegio brinda el servicio educativo bajo el marco normativo vigente. Dado la situación cambiante respecto al COVID-19, se podrían presentar posibles cambios con normativas provenientes del MINEDU que modificarían la modalidad de enseñanza. El Colegio Marianista se adecuaría a dicha medida, con previo aviso a los padres de familia mediante resolución directoral, el mismo que debe ser acatado por los estudiantes.'), 0, 'J');
        */
        
       $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 30);
            $this->pdf->MultiCell(165, 5, utf8_decode('QUINTO: El monto, número y oportunidad de pago de la matrícula y de las pensiones'), 0, 'J');
        
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 35);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Padre de Familia o apoderado matricula de forma libre y voluntaria al menor, asumiendo el compromiso de pagar los siguientes conceptos oportunamente:'), 0, 'J');
        
        $this->pdf->SetXY(22, 45);
        $this->pdf->MultiCell(170, 5, utf8_decode('1.	Por concepto de matrícula: '.$nivel.' S/ '.$matricula), 0, 'J');

        $this->pdf->SetXY(22, 50);
        $this->pdf->MultiCell(170, 5, utf8_decode('2.	Pensiones: El costo de las pensiones es de S/ '.$pension.'. Cabe resaltar el pago por servicio educativo está calculado de manera anual y se divide en 11 cuotas: 1 matrícula y 10 pensiones. El pago de la matrícula y pensiones no son reembolsables.'), 0, 'J');                
        
        $this->pdf->SetXY(22, 65);
        $this->pdf->MultiCell(170, 5, utf8_decode('3.	Los costos detallados previamente corresponden al nivel inicial de  menores y los pagos se deberán efectuar de acuerdo con el siguiente cronograma:'), 0, 'J');        
        
        
        $this->pdf->SetFont('Arial', 'BU', 9);
        $this->pdf->SetXY(18, 55);
        $this->pdf->MultiCell(180, 53, 'CRONOGRAMA DE PAGOS POR SERVICIO EDUCATIVO '.$this->ano, 0, 'C');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(22, 85);
        $this->pdf->MultiCell(170, 5, utf8_decode('MATRÍCULA: Hasta el 31 de enero. '), 0, 'J');

        $this->pdf->Image(BASE_URL . '/images/matricula2022.jpg', 50, 90, 115, 23); // x y , w , h 
        
        

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 115);
        $this->pdf->MultiCell(165, 5, utf8_decode('SEXTO: Medidas que adopta el colegio frente al incumplimiento oportuno del pago de las pensiones escolares '), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 125);
        $this->pdf->MultiCell(170, 5, utf8_decode('Conforme a lo establecido en el párrafo 16.1 del artículo 16 de la Ley N°26549, se efectuará la retención del certificado de estudios por falta de pago de pensiones correspondientes a los grados no pagados; sin perjuicio del cobro de costas y costos incurridos en caso se inicien las acciones judiciales respectivas por el incumplimiento de pago y, asimismo, no se ratificará la matrícula para el año escolar 2024.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 145   );
        $this->pdf->MultiCell(170, 5, utf8_decode('A partir del segundo mes de endeudamiento, el Colegio Marianista aplicará una mora del 5% e informará a las centrales de riesgo: Equifax (INFOCORP), SICOM (Sistema Consolidado de Morosidad) y otros; según lo establecido por el inciso 1) del artículo 1219 del Código Civil Peruano, en concordancia con el literal e) del artículo 62 de la Ley Nº29571, Ley que aprobó el Código de Protección de Defensa del Consumidor, y la Ley Nº27489, modificada por la Ley Nº27863, Ley de las Centrales Privadas de Información de Riesgos y de Protección al Titular de Información.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 170);
        $this->pdf->MultiCell(165, 5, utf8_decode('SÉPTIMO: Documentación'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 175);
        $this->pdf->MultiCell(170, 5, utf8_decode('Los estudiantes que se incorporan el presente año académico '.$this->ano.' deberán presentar los documentos que se requieren en el Anexo 1: "Presentación de documentación para incorporación al Colegio Marianista - '.$this->ano.'". '), 0, 'J');

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 185);
        $this->pdf->MultiCell(165, 5, utf8_decode('OCTAVO:'), 0, 'J');
        
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 190);
        $this->pdf->MultiCell(170, 5, utf8_decode('El presente contrato tiene vigencia por el periodo del año escolar '.$this->ano.'. Podrá ser disuelto en caso de que los padres de familia o familiares de un estudiante incurran en actos que falten el respeto de manera verbal o física a las autoridades, docentes o trabajadores de la institución educativa. De igual manera, podrá ser disuelto en caso el estudiante cometa faltas graves según lo estipulado en el reglamento interno.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'UB', 9);
        $this->pdf->SetXY(18, 210);
        $this->pdf->MultiCell(165, 5, utf8_decode('NOVENO:'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 215);
        $this->pdf->MultiCell(170, 5, utf8_decode('Es obligación de los padres de familia (o apoderado) y estudiantes el de informarse acerca del reglamento interno vigente, plan curricular institucional y estar al tanto de las directivas y comunicados emitidos por las autoridades del colegio.'), 0, 'J');

        
      /*  $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY(50, 230);
        $this->pdf->Cell(70, 4, utf8_decode('DOCUMENTOS REQUERIDOS'), 1, 0, 'C');
        $this->pdf->SetXY(120, 230);
        $this->pdf->Cell(30, 4, utf8_decode('ESTADO'), 1, 0, 'C');

        $inifila = 234;
        $this->pdf->SetFont('Arial', '', 8);
        if (count($arrDocumentos) > 0) {
            foreach ($arrDocumentos as $valor) {
                $arrItem = explode("|", $valor);
                if ($arrItem[0] != 'Copia de Partida') {
                    $this->pdf->SetFont('Arial', '', 8);
                    $this->pdf->SetXY(50, $inifila);
                    $this->pdf->Cell(70, 4, utf8_decode($arrItem[0]), 1, 0, 'L');
                    $this->pdf->SetXY(120, $inifila);
                    if ($arrItem[1] == '1') {
                        $this->pdf->SetFont('Arial', 'B', 8);
                        $this->pdf->Cell(30, 4, utf8_decode('Entregado'), 1, 0, 'C');
                    } else {
                        $this->pdf->SetFont('Arial', '', 8);
                        $this->pdf->Cell(30, 4, utf8_decode('Pendiente'), 1, 0, 'C');
                    }
                    $inifila += 4;
                }
            }
        }
        $inifila += 3;*/
     
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 235);
        $this->pdf->MultiCell(170, 5, utf8_decode('Casos no contemplados en el presente documento, se resolverán de acuerdo con las normas vigentes, resoluciones directorales y reglamento interno. '), 0, 'J');

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, 245);
        $this->pdf->MultiCell(170, 5, utf8_decode('Teniendo pleno conocimiento de las condiciones y características del servicio que brinda el colegio, firmo sin dolo ni presión..   '), 0, 'J');
        
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(115, $this->pdf->getY() + 8);
        $this->pdf->Cell(90, 5, utf8_decode('Villa María del Triunfo, ' . date("d") . ' de ' . ucfirst(nombreMesesCompleto(date("m"))) . ' del ' . date("Y")), 0, 0, 'L');
        
        $this->pdf->Image(BASE_URL . '/images/firma_contrato.jpg', 120, 270, 60, 17);        

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(18, $this->pdf->getY()-5 );
        $this->pdf->Cell(180, 5, utf8_decode('PADRE:'), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, $this->pdf->getY()+5 );
        $this->pdf->Cell(180, 5, utf8_decode('FIRMA: ...................................................'), 0, 0, 'L');

        $this->pdf->SetXY(18, $this->pdf->getY() + 5);
        $this->pdf->Cell(180, 5, utf8_decode('DNI     : ...................................................'), 0, 0, 'L');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(18, $this->pdf->getY()+5 );
        $this->pdf->Cell(180, 5, utf8_decode('MADRE:'), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(18, $this->pdf->getY()+5 );
        $this->pdf->Cell(180, 5, utf8_decode('FIRMA: ...................................................'), 0, 0, 'L');

        $this->pdf->SetXY(18, $this->pdf->getY() + 5);
        $this->pdf->Cell(180, 5, utf8_decode('DNI     : ...................................................'), 0, 0, 'L');        
        //$this->pdf->AutoPrint();
        $this->pdf->Output("Contrato_de_Servicio_Educativo-" . $this->ano . ".pdf", "I");
    }

    public function printServicio_old() {
        /* echo "<pre>";
          print_r($_POST);
          echo "</pre>"; */


        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }

        $apo = $_POST['hnomcomp'];
        $apodni = $_POST['hdnip'];
        $apodireccion = $_POST['hdireccion'];
        $nomhijo = $_POST['hnomcompa'];
        $grado = $_POST['hgrado'];
        $nivel = $_POST['hnivel'];
        $documentos = $_POST['hdocumentos'];
        $txtdsc = $_POST['htxtdsc'];
        $txtAula = $_POST['haula'];
        $tipoAlu = $_POST['htipoalu'];
        if ($tipoAlu == 'M') {
            $txtAula = ' del Aula: ' . $txtAula;
        } else {
            $txtAula = '';
        }

        if ($documentos != '') {
            $arrDocumentos = explode("*", $documentos);
        } else {
            $arrDocumentos = array();
        }

        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P');
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle('CONTRATO DE SERVICIO EDUCATIVO - ' . $this->ano);
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 15, 8, 25, 25, 'PNG');
        $this->pdf->SetFont('Arial', 'U', 14);
        $this->pdf->SetXY(55, 15);
        $this->pdf->Cell(100, 10, 'COLEGIO MARIANISTA  - ' . $this->ano, 0, 0, 'C');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 40);
        $this->pdf->Cell(180, 5, utf8_decode('Estamos muy contentos de que pertenezcas a esta "Gran Familia Marianista".'), 0, 0, 'L');

        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(18, 50);
        $this->pdf->Cell(180, 5, 'Acerca de nosotros:', 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 11);

        $this->pdf->SetXY(18, 60);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Colegio Marianista es una Institución Educativa de carácter privado que imparte educación escolarizada en los niveles de: Inicial, Primaria y Secundaria de menores, de conformidad a lo establecido en la Ley General de Educación (N°28044) - y sus reglamentos -, Ley de Centros Educativos Privados (N° 26549), Reglamento de Instituciones Educativas Privadas de Educación Básica y Educación Técnico Productiva - aprobado por Decreto Supremo N°009-2006-ED, Ley de Promoción de la inversión en la Educación - Decreto Legislativo 882 y sus reglamentos- y el Reglamento Interno del COLEGIO. '), 0, 'J');

        $this->pdf->SetXY(18, 100);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Colegio Marianista tiene como base el Diseño Curricular Nacional (DCN) y propuestas pedagógicas con reajustes permanentes de acuerdo con las nuevas tendencias educativas, así como un plan de estudios, sistemas de evaluación y control de los estudiantes.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(18, 120);
        $this->pdf->Cell(180, 5, 'Nuestro Compromiso:', 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 130);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Colegio Marianista brinda una educación integral a favor del estudiante. Esto implica el cuidado de una formación espiritual, moral y socioafectiva. La promoción y el desarrollo de capacidades, a través de nuestros planes y programas educativos humanísticos y científicos, que conducirán a nuestros estudiantes a un exitoso desempeño personal. Asimismo, fomentaremos el desarrollo de sus habilidades, talentos en las áreas artísticas, literarias y deportivas; para lo cual incluimos dentro de nuestro servicio actividades extracurriculares (talleres) temporales y electivos de manera gratuita fuera del horario de la jornada escolar.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 170);
        $this->pdf->MultiCell(170, 5, utf8_decode('Nuestra institución educativa se empeña en formar líderes éticos, buscando el crecimiento personal de cada estudiante gracias a una sólida formación y desarrollo de virtudes que se irán plasmando en valores.'), 0, 'J');

        $this->pdf->SetXY(18, 190);
        $this->pdf->MultiCell(170, 5, utf8_decode('Es también parte de nuestro servicio brindar información sobre los resultados del proceso educativo y formativo de su hijo(a), dando las indicaciones y orientaciones destinadas a superar las deficiencias académicas o de comportamiento. Asimismo, la información que requiera el padre de familia o apoderado   respecto del proceso educativo de su menor hijo(a) de conformidad al reglamento interno del COLEGIO.'), 0, 'J');

        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(18, 220);
        $this->pdf->Cell(180, 5, 'Compromiso del Padre de Familia:', 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 230);
        $this->pdf->MultiCell(170, 5, utf8_decode('El Padre de Familia o Apoderado del menor, matrícula de forma libre y voluntaria a su menor hijo(a), asumiendo los siguientes compromisos:'), 0, 'J');

        $this->pdf->SetXY(18, 243);
        $this->pdf->Cell(180, 5, utf8_decode('- Participar activamente en el proceso educativo de su menor hijo(a) en actividades '), 0, 0, 'L');
        $this->pdf->SetXY(18, 248);
        $this->pdf->Cell(180, 5, utf8_decode('académicas, formativas, deportivas, recreativas, culturales, religiosas y sociales. '), 0, 0, 'L');
        $this->pdf->SetXY(18, 253);
        $this->pdf->Cell(180, 5, utf8_decode('- Cumplir de manera asertiva con lo establecido en el Reglamento Interno Marianista.  '), 0, 0, 'L');
        $this->pdf->SetXY(18, 258);
        $this->pdf->Cell(180, 5, utf8_decode('Asistir a las citaciones, acompañamientos externos o terapias, en caso lo recomiende el  '), 0, 0, 'L');
        $this->pdf->SetXY(18, 263);
        $this->pdf->Cell(180, 5, utf8_decode('Departamento Psicológico, entre otros. '), 0, 0, 'L');

        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 20);
        $this->pdf->Cell(180, 5, utf8_decode('- Asumir el pago por servicio educativo: '), 0, 0, 'L');
        $this->pdf->SetXY(18, 25);
        $this->pdf->Cell(180, 5, utf8_decode('      o Matrícula (pago único)                                                                                 S/250 soles '), 0, 0, 'L');
        $this->pdf->SetXY(18, 30);
        $this->pdf->Cell(180, 5, utf8_decode('      o Costo por servicio educativo mensual (10 meses)		                                    S/300 soles'), 0, 0, 'L');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 40);
        $this->pdf->MultiCell(170, 5, utf8_decode('Los costos detallados previamente corresponden a los niveles de Inicial, Primaria y Secundaria de menores, de acuerdo al siguiente cronograma:'), 0, 'J');

        $this->pdf->Image(BASE_URL . '/images/cronograma.png', 30, 55, 140, 50, 'PNG');

        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(18, 110);
        $this->pdf->MultiCell(170, 5, utf8_decode('Medidas que adopta el colegio frente al incumplimiento del pago de las pensiones escolares: '), 0, 'J');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, 120);
        $this->pdf->MultiCell(170, 5, utf8_decode('El incumplimiento del pago en la fecha establecida dará lugar a una mora de S/20.00, por mes de deuda. Asimismo, la retención de los certificados de estudios de los periodos no cancelados (Ley 27665, Art. 16), no ratificar la matrícula para el año siguiente si mantiene dicha deuda e informar a las centrales de riesgo (Infocorp, Equifax, Certicom, etc.)  '), 0, 'J');


        // Agregado en una ultima version
        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(18, 145);
        $this->pdf->Cell(40, 5, utf8_decode('Documentación:'), 0, 0, 'L');

        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetXY(50, 155);
        $this->pdf->Cell(70, 5, utf8_decode('DOCUMENTOS REQUERIDOS'), 1, 0, 'C');
        $this->pdf->SetXY(120, 155);
        $this->pdf->Cell(30, 5, utf8_decode('ESTADO'), 1, 0, 'C');

        $inifila = 160;
        if (count($arrDocumentos) > 0) {
            if (trim($nivel) == 'INICIAL' && utf8_decode(trim($grado)) == utf8_decode('3 Años')) {
                foreach ($arrDocumentos as $valor) {
                    $arrItem = explode("|", $valor);
                    if (trim($arrItem[0]) == 'Copia de DNI (Alumno)' || trim($arrItem[0]) == 'Copia de DNI (Padres)') {
                        $this->pdf->SetFont('Arial', '', 10);
                        $this->pdf->SetXY(50, $inifila);
                        $this->pdf->Cell(70, 5, utf8_decode($arrItem[0]), 1, 0, 'L');
                        $this->pdf->SetXY(120, $inifila);
                        if ($arrItem[1] == '1') {
                            $this->pdf->SetFont('Arial', 'B', 10);
                            $this->pdf->Cell(30, 5, utf8_decode('Entregado'), 1, 0, 'C');
                        } else {
                            $this->pdf->SetFont('Arial', '', 10);
                            $this->pdf->Cell(30, 5, utf8_decode('Pendiente'), 1, 0, 'C');
                        }
                        $inifila += 5;
                    }
                }
            } else {
                foreach ($arrDocumentos as $valor) {
                    $arrItem = explode("|", $valor);
                    if ($arrItem[0] != 'Copia de Partida') {
                        $this->pdf->SetFont('Arial', '', 10);
                        $this->pdf->SetXY(50, $inifila);
                        $this->pdf->Cell(70, 5, utf8_decode($arrItem[0]), 1, 0, 'L');
                        $this->pdf->SetXY(120, $inifila);
                        if ($arrItem[1] == '1') {
                            $this->pdf->SetFont('Arial', 'B', 10);
                            $this->pdf->Cell(30, 5, utf8_decode('Entregado'), 1, 0, 'C');
                        } else {
                            $this->pdf->SetFont('Arial', '', 10);
                            $this->pdf->Cell(30, 5, utf8_decode('Pendiente'), 1, 0, 'C');
                        }
                        $inifila += 5;
                    }
                }
            }
            if ($txtdsc != '') {
                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->SetXY(18, $this->pdf->getY() + 6);
                $this->pdf->Cell(30, 5, utf8_decode('Observacion : ' . ucfirst(strtolower($txtdsc))), 0, 0, 'L');
            }
        }

        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(18, $this->pdf->getY() + 10);
        $this->pdf->MultiCell(170, 5, utf8_decode('COMPROMISO POR PRESTACIÓN DE SERVICIOS EDUCATIVOS AÑO ACADÉMICO ' . $this->ano), 0, 'J');

        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, $this->pdf->getY() + 5);
        $this->pdf->MultiCell(170, 5, utf8_decode('Conste por el presente, que suscribimos de una parte la Corporación Educativa Colegio Marianista SAC, RUC N°20517718778, con autorización de funcionamiento mediante Resolución DRELM N°02959, debidamente representado por su Director - Promotor G.G. Domingo Huaytalla Ll. y de la otra parte: Don (ña): ' . $apo . ' con DNI N°: ' . $apodni . ' domiciliado en ' . $apodireccion . ' EL PADRE DE FAMILIA O APODERADO del menor: ' . $nomhijo . ' Edad o Grado ' . str_replace("AñO", "Año", strtoupper($grado)) . ' Nivel: ' . $nivel . (($txtAula != "") ? $txtAula : "") . '  para el presente Año Escolar 2020.'), 0, 'J');
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(18, $this->pdf->getY() + 5);
        $this->pdf->MultiCell(170, 5, utf8_decode('Teniendo pleno conocimiento de las condiciones y características del servicio que brinda el colegio, firmo sin dolo ni presión.  '), 0, 'J');

        $this->pdf->SetXY(100, $this->pdf->getY() + 10);
        $this->pdf->Cell(90, 5, utf8_decode('Villa María del Triunfo, ' . date("d") . ' de ' . nombreMesesCompleto(date("m")) . ' del ' . date("Y")), 0, 0, 'L');

        $this->pdf->SetXY(18, $this->pdf->getY() + 10);
        $this->pdf->Cell(180, 5, utf8_decode('FIRMA: ...................................................'), 0, 0, 'L');

        $this->pdf->SetXY(18, $this->pdf->getY() + 5);
        $this->pdf->Cell(180, 5, utf8_decode('DNI     : ...................................................'), 0, 0, 'L');

        //$this->pdf->AutoPrint();
        $this->pdf->Output("Contrato_de_Servicio_Educativo-" . $this->ano . ".pdf", "I");
    }

    public function printConstancia() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }

        $apo = $_POST['hnomcomp'];
        $apodni = $_POST['hdnip'];
        $apodireccion = $_POST['hdireccion'];
        $nomhijo = $_POST['hnomcompa'];
        $grado = trim($_POST['hgrado']);
        $nivel = trim($_POST['hnivel']);
        $vtipo = $_POST['htipo'];
        $documentos = $_POST['hdocumentos'];
        $txtdsc = $_POST['htxtdsc'];
        if ($documentos != '') {
            $arrDocumentos = explode("*", $documentos);
        } else {
            $arrDocumentos = array();
        }
        $datosDocumentos = array(
            'D001' => 'Libreta de Notas',
            'D002' => 'Certificado de Estudios',
            'D003' => 'Constancia de Matrícula (SIAGIE)',
            'D004' => 'Copia de DNI (Alumno)',
            'D005' => 'Copia de DNI (Padres)',
            'D006' => 'Copia de Partida',
            'D007' => 'Ficha de Matrícula (SIAGIE)'
        );

        if ($nivel == 'INICIAL')
            $vtextogrado = "años";
        else
            $vtextogrado = "grado";

        if ($vtipo == '1')
            $vtexto = "señor padre";
        if ($vtipo == '2')
            $vtexto = "de la interesada";
        if ($vtipo == '3')
            $vtexto = "del interesado(a)";

        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P');
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle('CONSTANCIA DE VACANTE - ' . $this->ano);
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/firmas/header.jpg', 15, 5, 180, 20, 'JPG');
        if ($nivel == 'INICIAL' || $nivel == 'PRIMARIA') {
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(55, 50);
            $this->pdf->Cell(100, 10, utf8_decode('LA DIRECCIÓN DEL COLEGIO MARIANISTA, UGEL '), 0, 0, 'C');
            $this->pdf->SetXY(55, 60);
            $this->pdf->Cell(100, 10, '01. C. S. EXPIDE: ', 0, 0, 'C');
        }
        if ($nivel == 'SECUNDARIA') {
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(55, 50);
            $this->pdf->Cell(100, 10, utf8_decode('LA DIRECCIÓN DEL COLEGIO MARIANISTAS, DE '), 0, 0, 'C');
            $this->pdf->SetXY(55, 60);
            $this->pdf->Cell(100, 10, 'V.M.T., UGEL 01. C. S. EXPIDE: ', 0, 0, 'C');
        }
        $this->pdf->SetFont('Arial', 'U', 14);
        $this->pdf->SetXY(55, 80);
        $this->pdf->Cell(100, 10, 'CONSTANCIA DE VACANTE ', 0, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->SetXY(55, 100);
        $this->pdf->Cell(100, 10, utf8_decode($nomhijo), 0, 0, 'C');

        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetXY(40, 115);
        $this->pdf->MultiCell(130, 5, utf8_decode('        El mencionado estudiante cuenta con una vacante para ' . $grado . '° ' . $vtextogrado . ' de educación ' . $nivel . ' en el año escolar ' . $this->ano . ', bajo la responsabilidad ' . $vtexto . ' ' . $apo . ' identificado con DNI N° ' . $apodni . '.'), 0, 'J');

        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetXY(40, 140);
        $this->pdf->MultiCell(130, 5, utf8_decode('        Se expide la presente a solicitud del interesado para las gestiones de entrega de documentos.'), 0, 'J');

        $vmes = date("m");
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->SetXY(70, 180);
        $this->pdf->MultiCell(100, 5, utf8_decode('Villa María del Triunfo, ' . date("d") . ' de ' . nombreMesesCompleto($vmes) . ' del ' . date("Y")), 0, 'R');


        $this->pdf->SetFont('Arial', 'U', 11);
        $this->pdf->SetXY(40, 220);
        $this->pdf->MultiCell(40, 5, utf8_decode('DOCUMENTOS:'), 0, 'L');
        $inifila = 225;
        $this->pdf->SetFont('Arial', '', 11);
        if (count($arrDocumentos) > 0) {
            if ($nivel == 'INICIAL' && $grado == '2') {
                $this->pdf->SetXY(40, $inifila);
                $this->pdf->Cell(30, 5, utf8_decode('No tiene ningún documento pendiente.'), 0, 0, 'L');
            } else {
                foreach ($arrDocumentos as $valor) {
                    if ($valor != 'Copia de Partida') {
                        $this->pdf->SetXY(40, $inifila);
                        $this->pdf->Cell(30, 5, utf8_decode('o ' . $valor), 0, 0, 'L');
                        $inifila += 5;
                    }
                }
            }
        } else {
            $this->pdf->SetXY(40, $inifila);
            $this->pdf->Cell(30, 5, utf8_decode('No tiene ningún documento pendiente.'), 0, 0, 'L');
        }

        // Bloque de Firmas     
        if ($nivel == 'INICIAL' || $nivel == 'PRIMARIA')
            $this->pdf->Image(BASE_URL . '/images/firmas/firma_directora.jpg', 120, 230, 60, 30, 'JPG');
        else
            $this->pdf->Image(BASE_URL . '/images/firmas/firma_director.jpg', 120, 230, 60, 30, 'JPG');

        $this->pdf->Image(BASE_URL . '/images/firmas/footer.jpg', 15, 265, 180, 20, 'JPG');

        $this->pdf->AutoPrint();
        $this->pdf->Output("Constancia_de_Vacante-" . $this->ano . ".pdf", "I");
    }

    public function printEtiqueta($vId = '') {
        $vDniAlumno = $this->objAlumno->getDniAlumno($vId);
        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
		$dataUsuario = $this->objSalon->getDatoUsuarioApp($dataAlumno[0]->FAMCOD);
		//print_r($dataUsuario); exit;
        $anio = (int) substr($vId, 0, 4);
        $valida = ($anio >= 2021 || ( $anio < 2021 && ($dataAlumno[0]->INSTRUCOD == 'P' || $dataAlumno[0]->INSTRUCOD == 'S') && $dataAlumno[0]->GRADOCOD == '1')) ? TRUE : FALSE;
        $usuarioParte = explode(' ', trim(str_replace('Á', 'A', str_replace('É', 'E', str_replace('Í', 'I', str_replace('Ó', 'O', str_replace('Ú', 'U', str_replace('ñ', 'n', str_replace('Ñ', 'N', str_replace('  ', ' ', $dataAlumno[0]->NOMBRES))))))))));
        $usuCampus = strtolower($usuarioParte[0] . '.' . substr($dataAlumno[0]->APEPAT, 0, 1) . substr($dataAlumno[0]->APEMAT, 0, 1));
        // ==================================================================
        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'L', $unit = 'mm', array(110, 60));
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle(' ETIQUETAS - ' . $this->ano);
        #Establecemos los mรกrgenes izquierda, arriba y derecha:
        $this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();

		if(count($dataUsuario)==0){
			$this->pdf->SetFont('Arial', 'B', 10);
			$this->pdf->SetXY(5, 15);
			$this->pdf->MultiCell(100, 3, utf8_decode('El alumno aun no le han generado sus accesos. Comuniquese con el administrador.'), 0, 'C');
		} else {
			$this->pdf->SetFont('Arial', '', 6);
			$this->pdf->SetXY(3, 22.5);
			$this->pdf->Cell(100, 1, '--------------------------------------------------------------------------------------------------------------------------------------------',0, 0, 'L');
		
			$this->pdf->SetFont('Arial', 'B', 10);
			$this->pdf->SetXY(5, 15);
			$this->pdf->Cell(100, 3, utf8_decode($dataAlumno[0]->NOMCOMP), 0, 0, 'C');
			$this->pdf->SetFont('Arial', 'B', 8);
			$this->pdf->SetXY(3, 25);
			$this->pdf->Cell(50, 3, 'Correo Institucional                         : ', 0, 0, 'L');
			$this->pdf->SetFont('Arial', '', 8);
			$this->pdf->SetXY(53, 25);
			$this->pdf->Cell(50, 3, $usuCampus . '@marianista.edu.pe', 0, 0, 'L');

			$this->pdf->SetFont('Arial', 'B', 8);
			$this->pdf->SetXY(3, 30);
			$this->pdf->Cell(50, 3, utf8_decode('Contraseña Correo                           :'), 0, 0, 'L');
			$this->pdf->SetFont('Arial', '', 8);
			$this->pdf->SetXY(53, 30);
			$this->pdf->Cell(50, 3, $dataAlumno[0]->DNI, 0, 0, 'L');

			$this->pdf->SetFont('Arial', '', 6);
			$this->pdf->SetXY(3, 33.5);
			$this->pdf->Cell(100, 1, '--------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

			$this->pdf->SetFont('Arial', 'B', 8);
			$this->pdf->SetXY(3, 35);
			$this->pdf->Cell(50, 3, utf8_decode('Usuario Campus Virtual Marianista  : '), 0, 0, 'L');
			$this->pdf->SetFont('Arial', '', 8);
			$this->pdf->SetXY(53, 35);
			$this->pdf->Cell(50, 3, $usuCampus, 0, 0, 'L');

			$this->pdf->SetFont('Arial', 'B', 8);
			$this->pdf->SetXY(3, 40);
			$this->pdf->Cell(50, 3, utf8_decode('Contraseña Campus                          : '), 0, 0, 'L');
			$this->pdf->SetFont('Arial', '', ($valida) ? 8 : 13);
			$this->pdf->SetXY(53, 40);
			$this->pdf->Cell(50, 3, ($valida) ? $dataAlumno[0]->DNI : '***************', 0, 0, 'L');

			$this->pdf->SetFont('Arial', '', 6);
			$this->pdf->SetXY(3, 43.5);
			$this->pdf->Cell(100, 1, '--------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

			$this->pdf->SetFont('Arial', 'B', 8);
			$this->pdf->SetXY(3, 45);
			$this->pdf->Cell(50, 3, utf8_decode('Usuario App Marianista GO              : '), 0, 0, 'L');
			$this->pdf->SetFont('Arial', '', 8);
			$this->pdf->SetXY(53, 45);
			$this->pdf->Cell(50, 3, $dataUsuario[0]->usucod, 0, 0, 'L');

			$this->pdf->SetFont('Arial', 'B', 8);
			$this->pdf->SetXY(3, 50);
			$this->pdf->Cell(50, 3, utf8_decode('Contraseña App                                 : '), 0, 0, 'L');
			$this->pdf->SetFont('Arial', '', 8);
			$this->pdf->SetXY(53, 50);
			$this->pdf->Cell(50, 3, $dataUsuario[0]->clave, 0, 0, 'L');


			$this->pdf->SetFont('Arial', '', 6);
			$this->pdf->SetXY(3, 53.5);
			$this->pdf->Cell(100, 1, '--------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

		}
		
        $this->pdf->AutoPrint();
        $this->pdf->output();
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

    function controlSesion() {
        $session = $this->seguridad_model->verificaSesion();
        if (!$session) {
            echo json_encode(array('flg' => 1, 'msg' => 'SESSION EXPIRADA.'));
        } else {
            echo json_encode(array('flg' => 0, 'msg' => ''));
        }
    }

}
