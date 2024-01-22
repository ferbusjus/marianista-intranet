<?php

/**
 * @package       modules/sga_reporte_caja/controller
 * @name            sga_reporte_caja.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2017/09/03
 */
class sga_reporte_caja extends CI_Controller {

    public $S_ANO = '';
    public $token = '';
    public $modulo = 'REPORTE-CAJA';

    public function __construct() {
        parent::__construct();
        $this->load->model('asistencia_model', 'objAsistencia');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('empresa_model', 'objEmpresa');
        $this->load->model('observacion_model', 'objObservacion');
        $this->load->model('egresos_model', 'objEgresos');
        $this->load->model('seguridad_model');
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');
        $this->load->library('excel');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataEmpresa"] = $this->objEmpresa->getEmpresa();
        $data["dataNivel"] = $this->objSalon->getNivel();
        $data["dataUsuarios"] = $this->objCobros->getUsuarios();
        $data["rol_usuario"] = $this->_session['ROL'];
        $data["usucod"] = $this->_session['USUCOD'];
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_reporte_caja', $data);
        $this->load->view('js_reporte');
        $this->load->view('view_footer');
    }

    public function rptegresos() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_reporte_egresos');
        $this->load->view('js_reporte');
        $this->load->view('view_footer');
    }

    public function rptgeneral() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataEmpresa"] = $this->objEmpresa->getEmpresa();
        $data["dataUsuarios"] = $this->objCobros->getUsuarios();
        $data["rol_usuario"] = $this->_session['ROL'];
        $data["usucod"] = $this->_session['USUCOD'];        
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_reporte_resumen', $data);
        $this->load->view('js_resumen');
        $this->load->view('view_footer');
    }

    public function cierre() {
        $data['token'] = $this->token();
        $vfecha = date('Y-m-d');
        $vuser = $this->_session['USUCOD'];
        $data['token'] = $this->token();
        $dataPagos = $this->objEmpresa->listaPagosxUsuario($vfecha, $vuser);
        $data['dataPagos'] = $dataPagos;
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_cierre_caja', $data);
        //$this->load->view('js_reporte');
        $this->load->view('view_footer');
    }

    public function print_egresos() {
        /* echo "<pre>";
          print_r ($_POST);
          echo "</pre>"; */

        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vfecha = $this->input->post("finicial");

        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->SetAutoPageBreak(true, 5);

        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE DE EGRESOS   - ' . $this->S_ANO, 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Fecha : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, $vfecha, 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->SetFont('Arial', 'B', 10);
        $valorY = 50;
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(20, 5, utf8_decode('CÓDIGO'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('T-EGRESO'), 0, 0, 'C');
        $this->pdf->Cell(160, 5, utf8_decode('DESCRIPCIÓN DEL EGRESO'), 0, 0, 'L');
        $this->pdf->Cell(25, 5, utf8_decode('T-COMP.'), 0, 0, 'C');
        $this->pdf->Cell(20, 5, utf8_decode('NÚMERO'), 0, 0, 'C');
        $this->pdf->Cell(20, 5, utf8_decode('MONTO'), 0, 0, 'C');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $vnfecha = substr($vfecha, 6, 4) . '-' . substr($vfecha, 3, 2) . '-' . substr($vfecha, 0, 2);
        $dataEgresos = $this->objEgresos->listaEgresosxFecha($vnfecha);
        if (is_array($dataEgresos) && count($dataEgresos) > 0) {
            $this->pdf->SetFont('Arial', '', 10);
            $vtotal = 0;
            foreach ($dataEgresos as $egreso) {
                $this->pdf->Cell(20, 5, $egreso->id, 0, 0, 'C');
                $this->pdf->Cell(30, 5, $egreso->grupo, 0, 0, 'C');
                $this->pdf->Cell(160, 5, utf8_decode(strtoupper($egreso->descripcion)), 0, 0, 'L');
                $this->pdf->Cell(25, 5, $egreso->comprobante, 0, 0, 'C');
                $this->pdf->Cell(20, 5, $egreso->numcomp, 0, 0, 'C');
                $this->pdf->Cell(20, 5, $egreso->monto, 0, 0, 'R');
                $this->pdf->Ln(5);
                $vtotal += $egreso->monto;
            }

            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(255, 5, 'TOTAL =>', 0, 0, 'R');
            $this->pdf->Cell(20, 5, 'S/.' . number_format($vtotal, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
        }
        $this->pdf->Output('Reporte_de_egresos.pdf', 'I');
    }

    public function printResumen() {
        /* echo "<pre>";
          print_r ($_POST);
          echo "</pre>"; */

        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vrazon = $this->input->post("cbrazon");
        $vfecha = $this->input->post("finicial");
        $vffinal = $this->input->post("ffinal");
        $vusuario = $this->input->post("cbusuario");
        $vTipoComp = $this->input->post("cbcomprobante");
       if($this->_session['ROL'] == ''){ // No es usuario Administrativo
            $vusuario = $this->_session['USUCOD'];
        }        
        $vflg = (isset($_POST['chkReporte'])) ? 1 : 0;
        $vrazondes = "";
        switch ($vrazon) {
            case 'R01':
                $vrazondes = '20517718778 - MARIANISTA S.A.C';
                break;
            case 'R02':
                $vrazondes = '20556889237 - MARIANISTAS';
                break;
        }
        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->S_ANO);
        $this->pdf->SetTitle('RESUMEN GENERAL - ' . $this->S_ANO);
        $this->pdf->AliasNbPages(); // PARA QUE SALGA LAS PAGINAS 
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE - RESUMEN GENERAL  - ' . $this->S_ANO, 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Razón : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(60, 5, $vrazondes, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Desde : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, $vfecha, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Hasta : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, $vffinal, 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->SetFont('Arial', 'B', 10);
        $valorY = 50;
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        if ($vTipoComp == 'T') {
            $this->pdf->Cell(30, 5, utf8_decode('COMPROBANTE'), 0, 0, 'C');
        }
        if ($vTipoComp == '01') {
            $this->pdf->Cell(30, 5, utf8_decode('RECIBOS'), 0, 0, 'C');
        }
        if ($vTipoComp == '02') {
            $this->pdf->Cell(30, 5, utf8_decode('BOLETAS'), 0, 0, 'C');
        }
        if ($vTipoComp == '03') {
            $this->pdf->Cell(30, 5, utf8_decode('FACTURAS'), 0, 0, 'C');
        }
        $this->pdf->Cell(20, 5, utf8_decode('DNI'), 0, 0, 'C');
        $this->pdf->Cell(90, 5, utf8_decode('APELLIDOS Y NOMBRES'), 0, 0, 'C');
        $this->pdf->Cell(10, 5, utf8_decode('NGS'), 0, 0, 'C');
        $this->pdf->Cell(15, 5, utf8_decode('AÑO'), 0, 0, 'C');
        if ($vusuario == 'T') {
            $this->pdf->Cell(40, 5, utf8_decode('CONCEPTO'), 0, 0, 'C');
            $this->pdf->Cell(20, 5, utf8_decode('USUARIO'), 0, 0, 'C');
        } else {
            $this->pdf->Cell(60, 5, utf8_decode('CONCEPTO'), 0, 0, 'C');
        }
        $this->pdf->Cell(30, 5, utf8_decode('FECHA'), 0, 0, 'C');
        $this->pdf->Cell(20, 5, utf8_decode('IMPORTE'), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);

        $vtotalc = 0;
        $vtotalb = 0;
        $vnfecha = substr($vfecha, 6, 4) . '-' . substr($vfecha, 3, 2) . '-' . substr($vfecha, 0, 2);
        $vnfechaf = substr($vffinal, 6, 4) . '-' . substr($vffinal, 3, 2) . '-' . substr($vffinal, 0, 2);
        // ============= Listando registros de los pagos por Caja ===========================================
        if ($vflg == 0) {
            $dataPago = $this->objCobros->getPagoxRazon($vnfecha, $vnfechaf, $vrazon, $this->S_ANO, $vusuario, $vTipoComp);
            $this->pdf->SetFont('Arial', '', 10);
            if (is_array($dataPago) && count($dataPago) > 0) {
                foreach ($dataPago as $pagos) {
                    $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
                    $this->pdf->Cell(20, 5, $pagos->dni, 0, 0, 'C');
                    $this->pdf->Cell(90, 5, utf8_decode($pagos->nomcomp), 0, 0, 'L');
                    $this->pdf->Cell(10, 5, $pagos->ngs, 0, 0, 'C');
                    $this->pdf->Cell(15, 5, $pagos->anocob, 0, 0, 'C');
                    if($pagos->flgexonera==1) $exo = " - (EXO)"; else  $exo =""; // se agrega para las matriculas exoneradas
                    if ($vusuario == 'T') {
                        $this->pdf->Cell(40, 5, utf8_decode($pagos->condes . ' - ' . $pagos->mesdes.$exo), 0, 0, 'L');
                        $this->pdf->Cell(20, 5, utf8_decode($pagos->usumod), 0, 0, 'C');
                    } else {
                        $this->pdf->Cell(60, 5, utf8_decode($pagos->condes . ' - ' . $pagos->mesdes.$exo), 0, 0, 'L');
                    }
                    if ($pagos->tipopago == 'B') {
                        $this->pdf->Cell(30, 5, invierte_date(substr($pagos->fecmod, 0, 10)), 0, 0, 'C');
                        $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                        $vtotalb += $pagos->montocob;
                    } else {
                        $this->pdf->Cell(30, 5, invierte_date(substr($pagos->fecmod, 0, 10)), 0, 0, 'C');
                        $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                        $vtotalc += $pagos->montocob;
                    }
                    $this->pdf->Ln(5);
                }
            }
        } else {
            $dataPago = array();
        }
        // ============= Listando registros de los pagos Adicionales ===========================================
        $dataPagoAdicional = $this->objCobros->getPagoxRazonAdicional($vnfecha, $vnfechaf, $vrazon, $this->S_ANO, $vusuario, $vTipoComp, $vflg);
        $this->pdf->SetFont('Arial', '', 10);
        if (is_array($dataPagoAdicional) && count($dataPagoAdicional) > 0) {
            foreach ($dataPagoAdicional as $pagos) {
                $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
                $this->pdf->Cell(20, 5, $pagos->dni, 0, 0, 'C');
                $this->pdf->Cell(90, 5, utf8_decode(((strlen($pagos->nomcomp) < 8) ? 'SIN NOMBRE' : $pagos->nomcomp)), 0, 0, 'L');
                $this->pdf->Cell(10, 5, $pagos->ngs, 0, 0, 'C');
                $this->pdf->Cell(15, 5, $pagos->anocob, 0, 0, 'C');
                if ($vusuario == 'T') {
                    $this->pdf->Cell(40, 5, utf8_decode(ucwords(strtolower($pagos->condes))), 0, 0, 'L');
                    $this->pdf->Cell(20, 5, utf8_decode($pagos->usumod), 0, 0, 'C');
                } else {
                    $this->pdf->Cell(60, 5, utf8_decode(ucwords(strtolower($pagos->condes))), 0, 0, 'L');
                }
                if ($pagos->tipopago == 'B') {
                    $this->pdf->Cell(30, 5, invierte_date(substr($pagos->fecmod, 0, 10)), 0, 0, 'C');
                    $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                    $vtotalb += $pagos->montocob;
                } else {
                    $this->pdf->Cell(30, 5, invierte_date(substr($pagos->fecmod, 0, 10)), 0, 0, 'C');
                    $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                    $vtotalc += $pagos->montocob;
                }
                $this->pdf->Ln(5);
            }
        }

        if (count($dataPago) > 0 || count($dataPagoAdicional) > 0) {
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(225, 5, 'TOTAL', 0, 0, 'R');
            $this->pdf->Cell(20, 5, '', 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format(($vtotalc + $vtotalb), 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
        }

        $this->pdf->Output('Reporte_resumen_diario-' . date("Ymd") . '.pdf', 'I');
    }

    public function printExcelResumen() {
        $vrazon = $this->input->post("cbrazon");
        $vfecha = $this->input->post("finicial");
        $vffinal = $this->input->post("ffinal");
        $vusuario = $this->input->post("cbusuario");
        $vTipoComp = $this->input->post("cbcomprobante");
       if($this->_session['ROL'] == ''){ // No es usuario Administrativo
            $vusuario = $this->_session['USUCOD'];
        }        
        $vrazondes = "";
        switch ($vrazon) {
            case 'R01':
                $vrazondes = '20517718778 - MARIANISTA S.A.C';
                break;
            case 'R02':
                $vrazondes = '20556889237 - MARIANISTAS';
            case 'T':
                $vrazondes = 'MARIANISTA S.A.C / MARIANISTAS';
                break;
        }
        $vtotalc = 0;
        $vtotalb = 0;
        $vnfecha = substr($vfecha, 6, 4) . '-' . substr($vfecha, 3, 2) . '-' . substr($vfecha, 0, 2);
        $vnfechaf = substr($vffinal, 6, 4) . '-' . substr($vffinal, 3, 2) . '-' . substr($vffinal, 0, 2);

        // Propiedades del documento
        $this->excel->getProperties()->setCreator("SISTEMAS-DEV")
                ->setLastModifiedBy("SISTEMAS-DEV")
                ->setTitle("Reporte - Resumen General")
                ->setSubject("REPORTES SISTEMAS-DEV")
                ->setDescription("Reporte que Muestra lo pagos realizado por Razon Social")
                ->setKeywords("Reporte - Resumen General")
                ->setCategory("SISTEMAS-DEV");

        $vDesComp = "";
        if ($vTipoComp == 'T') {
            $vDesComp = "COMPROBANTE";
        }
        if ($vTipoComp == '01') {
            $vDesComp = "RECIBOS";
        }
        if ($vTipoComp == '02') {
            $vDesComp = "BOLETAS";
        }
        if ($vTipoComp == '03') {
            $vDesComp = "FACTURAS";
        }

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('RESUMEN CAJA DIARIO - ' . $this->S_ANO);
        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:J1');
        $this->excel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'REPORTE - RESUMEN CAJA DIARIO  - ' . $this->S_ANO)
                ->setCellValue('A2', 'RAZON SOCIAL')
                ->setCellValue('B2', $vDesComp)
                ->setCellValue('C2', 'DNI')
                ->setCellValue('D2', 'APELLIDOS Y NOMBRES')
                ->setCellValue('E2', 'NGS')
                ->setCellValue('F2', 'AÑO')
                ->setCellValue('G2', 'CONCEPTO DE PAGO')
                ->setCellValue('H2', 'USUARIO')
                ->setCellValue('I2', 'FECHA')
                ->setCellValue('J2', 'IMPORTE');
        // Fuente de la primera fila en negrita
        $boldArray = array('font' => array('bold' => true,), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $this->excel->getActiveSheet()->getStyle('A1:J2')->applyFromArray($boldArray);

        //Ancho de las columnas
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);

        $dataPago = $this->objCobros->getPagoxRazon($vnfecha, $vnfechaf, $vrazon, $this->S_ANO, $vusuario, $vTipoComp);
        if (is_array($dataPago) && count($dataPago) > 0) {
            $cel = 3;
            foreach ($dataPago as $pagos) {
                $razon = (($pagos->tipo_razon == 'R01') ? 'MARIANISTA S.A.C' : 'MARIANISTAS');
                $a = "A" . $cel;
                $b = "B" . $cel;
                $c = "C" . $cel;
                $d = "D" . $cel;
                $e = "E" . $cel;
                $f = "F" . $cel;
                $g = "G" . $cel;
                $h = "H" . $cel;
                $i = "I" . $cel;
                $j = "J" . $cel;
                // Agregar datos
                $this->excel->setActiveSheetIndex(0)
                        ->setCellValue($a, $razon)
                        ->setCellValue($b, $pagos->numrecibo)
                        ->setCellValue($c, $pagos->dni)
                        ->setCellValue($d, $pagos->nomcomp)
                        ->setCellValue($e, $pagos->ngs)
                        ->setCellValue($f, $pagos->anocob)
                        ->setCellValue($g, $pagos->condes . " - " . $pagos->mesdes)
                        ->setCellValue($h, $pagos->usumod)
                        ->setCellValue($i, $pagos->fecmod)
                        ->setCellValue($j, $pagos->montocob);
                $cel += 1;
            }
            $rango = "A2:$j";
            $styleArray = array('font' => array('name' => 'Arial', 'size' => 10),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
            );
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }
        $totalPagos = count($dataPago);
        // ============= Listando registros de los pagos Adicionales ===========================================
        $dataPagoAdicional = $this->objCobros->getPagoxRazonAdicional($vnfecha, $vnfechaf, $vrazon, $this->S_ANO, $vusuario, $vTipoComp);
        if (is_array($dataPagoAdicional) && count($dataPagoAdicional) > 0) {
            $cel = $totalPagos + 3;
            foreach ($dataPagoAdicional as $pagos) {
                $razon = (($pagos->tipo_razon == 'R01') ? 'MARIANISTA S.A.C' : 'MARIANISTAS');
                $a = "A" . $cel;
                $b = "B" . $cel;
                $c = "C" . $cel;
                $d = "D" . $cel;
                $e = "E" . $cel;
                $f = "F" . $cel;
                $g = "G" . $cel;
                $h = "H" . $cel;
                $i = "I" . $cel;
                $j = "J" . $cel;
                // Agregar datos
                $this->excel->setActiveSheetIndex(0)
                        ->setCellValue($a, $razon)
                        ->setCellValue($b, $pagos->numrecibo)
                        ->setCellValue($c, $pagos->dni)
                        ->setCellValue($d, ((strlen($pagos->nomcomp) < 8) ? 'SIN NOMBRE' : $pagos->nomcomp))
                        ->setCellValue($e, $pagos->ngs)
                        ->setCellValue($f, $pagos->anocob)
                        ->setCellValue($g, ucwords(strtolower($pagos->condes)))
                        ->setCellValue($h, $pagos->usumod)
                        ->setCellValue($i, $pagos->fecmod)
                        ->setCellValue($j, $pagos->montocob);
                $cel += 1;
            }
            $rango = "A2:$j";
            $styleArray = array('font' => array('name' => 'Arial', 'size' => 10),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
            );
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }

        // Forzamos a la descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Resumen_Caja_Diario_' . $this->S_ANO . '.xls"');
        header('Cache-Control: max-age=0');
        // Si usted está sirviendo a IE 9 , a continuación, puede ser necesaria la siguiente
        header('Cache-Control: max-age=1');


        // Si usted está sirviendo a IE a través de SSL , a continuación, puede ser necesaria la siguiente
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function printpagos() {
         /*echo "<pre>";
          print_r ($_POST);
          echo "</pre>";  
          exit;*/
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        //$vnivel = $this->input->post("cbnivel");
        $vrazon = $this->input->post("cbrazon");
        $vfecha = $this->input->post("finicial");
        $vusuario = $this->input->post("cbusuario");
        $vcomp = $this->input->post("cbcomprobante");
        //$usurol = $this->input->post("usurol");
        if($this->_session['ROL'] == ''){ // No es usuario Administrativo
            $vusuario = $this->_session['USUCOD'];
        }
        $vniveldes = '';
        /* switch ($vnivel) {
          case 'T':
          $vniveldes = 'TODOS';
          break;
          case 'I':
          $vniveldes = '  INICIAL';
          break;
          case 'P':
          $vniveldes = 'PRIMARIA';
          break;
          case 'S':
          $vniveldes = 'SECUNDARIA';
          break;
          } */
        switch ($vrazon) {
            case 'R01':
                $vniveldes = 'MARIANISTA S.A.C';
                break;
            case 'R02':
                $vniveldes = 'MARIANISTAS';
                break;
        }
        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE - CIERRE DE CAJA DIARIO   - ' . $this->S_ANO, 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Razón : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, $vniveldes, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Fecha : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, $vfecha, 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->SetFont('Arial', 'B', 10);
        $valorY = 50;
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(30, 5, utf8_decode('COMPROBANTE'), 0, 0, 'C');
        $this->pdf->Cell(80, 5, utf8_decode('APELLIDOS Y NOMBRES'), 0, 0, 'C');
        $this->pdf->Cell(10, 5, utf8_decode('NGS'), 0, 0, 'C');
        $this->pdf->Cell(10, 5, utf8_decode('AÑO'), 0, 0, 'C');
        if ($vusuario == 'T') {
            $this->pdf->Cell(50, 5, utf8_decode('CONCEPTO DE PAGO'), 0, 0, 'C');
            $this->pdf->Cell(30, 5, utf8_decode('USUARIO'), 0, 0, 'C');
        } else {
            $this->pdf->Cell(80, 5, utf8_decode('CONCEPTO DE PAGO'), 0, 0, 'C');
        }
        $this->pdf->Cell(30, 5, utf8_decode('BANCO'), 0, 0, 'R');
        $this->pdf->Cell(30, 5, utf8_decode('CAJA'), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        if ($vrazon == 'R01') {
            $arrayNivel = array(
                'I' => 'INICIAL',
                'P' => 'PRIMARIA'
            );
        } elseif ($vrazon == 'R02') {
            $arrayNivel = array(
                'S' => 'SECUNDARIA'
            );
        }
        $vtotalfb = 0;
        $vtotalfc = 0;
        $vnfecha = substr($vfecha, 6, 4) . '-' . substr($vfecha, 3, 2) . '-' . substr($vfecha, 0, 2);
        foreach ($arrayNivel as $key => $value) {
            //$vfecha = '2017-08-27'; //date ("Y-m-d", strtotime ($vfecha));
            /* if ($key == 'I' || $key == 'P') {
              $rz = "R01";
              } else {
              $rz = "R02";
              } */
            $vtotalc = 0;
            $vtotalb = 0;
            $dataPago = $this->objCobros->getPagoxNivel($key, $vnfecha, $this->S_ANO, $vusuario, $vcomp, $vrazon);
            $this->pdf->SetFont('Arial', '', 10);
            if (is_array($dataPago) && count($dataPago) > 0) {
                foreach ($dataPago as $pagos) {
                    $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
                    $this->pdf->Cell(80, 5, utf8_decode($pagos->nomcomp), 0, 0, 'L');
                    $this->pdf->Cell(10, 5, $pagos->ngs, 0, 0, 'C');
                    $this->pdf->Cell(10, 5, $pagos->anocob, 0, 0, 'C');
                    if($pagos->flgexonera==1) $exo = " - (EXO)"; else  $exo =""; // se agrega para las matriculas exoneradas
                    if ($vusuario == 'T') {
                        $this->pdf->Cell(50, 5, utf8_decode($pagos->condes . ' - ' . $pagos->mesdes.$exo), 0, 0, 'L');
                        $this->pdf->Cell(30, 5, utf8_decode($pagos->usumod), 0, 0, 'C');
                    } else {
                        $this->pdf->Cell(80, 5, utf8_decode($pagos->condes . ' - ' . $pagos->mesdes.$exo), 0, 0, 'L');
                    }
                    //$this->pdf->Cell (20, 5, (($pagos->tipopago == 'C') ? 'Caja' : 'Banco'), 0, 0, 'C');
                    if ($pagos->tipopago == 'B') {
                        $this->pdf->Cell(30, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                        $this->pdf->Cell(30, 5, '', 0, 0, 'C');
                        $vtotalb += $pagos->montocob;
                    } else {
                        $this->pdf->Cell(30, 5, '', 0, 0, 'C');
                        $this->pdf->Cell(30, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                        $vtotalc += $pagos->montocob;
                    }
                    $this->pdf->Ln(5);
                }
                $this->pdf->SetFont('Arial', 'B', 10);
                $this->pdf->Ln(5);
                $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
                $this->pdf->Ln(5);
                $this->pdf->Cell(220, 5, 'TOTAL ' . $value . ' =>', 0, 0, 'R');
                $this->pdf->Cell(20, 5, 'S/.' . number_format($vtotalb, 2, '.', ','), 0, 0, 'R');
                $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalc, 2, '.', ','), 0, 0, 'R');
                $this->pdf->Ln(5);
                $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
                $this->pdf->Ln(5);
                $vtotalfb += $vtotalb;
                $vtotalfc += $vtotalc;
            }
        }
        $this->pdf->Ln(5);
        $dataPagoOtros = $this->objCobros->getPagosOtros($vnfecha, $this->S_ANO, $vusuario, $vcomp, $vrazon);
        $vtotalAdicCaja = 0;
		$vtotalAdicBanco = 0;
        if (is_array($dataPagoOtros) && count($dataPagoOtros) > 0) {
            foreach ($dataPagoOtros as $pagos) {
                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
                $this->pdf->Cell(80, 5, utf8_decode(((strlen($pagos->nomcomp) < 8) ? 'SIN NOMBRE' : $pagos->nomcomp)), 0, 0, 'L');
                $this->pdf->Cell(10, 5, $pagos->ngs, 0, 0, 'C');
                $this->pdf->Cell(10, 5, $pagos->anocob, 0, 0, 'C');
                if ($vusuario == 'T') {
                    $this->pdf->Cell(50, 5, utf8_decode(ucwords(strtolower($pagos->condes))), 0, 0, 'L');
                    $this->pdf->Cell(30, 5, utf8_decode($pagos->usumod), 0, 0, 'C');
                } else {
                    $this->pdf->Cell(80, 5, utf8_decode(ucwords(strtolower($pagos->condes))), 0, 0, 'L');
                }
                if ($pagos->tipopago == 'B') {
                    $this->pdf->Cell(30, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                    $this->pdf->Cell(30, 5, '', 0, 0, 'C');
                    $vtotalAdicBanco += $pagos->montocob;
                } else {
                    $this->pdf->Cell(30, 5, '', 0, 0, 'C');
                    $this->pdf->Cell(30, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                    $vtotalAdicCaja += $pagos->montocob;
                }
                $this->pdf->Ln(5);
            }

            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(220, 5, 'TOTAL OTROS =>', 0, 0, 'R');
            $this->pdf->Cell(20, 5, 'S/.' . number_format($vtotalAdicBanco, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalAdicCaja, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
        }

        if ($vtotalfb > 0 || $vtotalfc > 0) {
			$totalBancos = ($vtotalfb+ $vtotalAdicBanco);
			$totalCajas   = ($vtotalfc + $vtotalAdicCaja);
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(240, 5, 'TOTAL BANCO =>', 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format($totalBancos, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            $this->pdf->Cell(240, 5, 'TOTAL CAJA =>', 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format($totalCajas, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            /*if ($vtotalAdicCaja > 0) {
                $this->pdf->Cell(240, 5, 'TOTAL OTROS =>', 0, 0, 'R');
                $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalAdicCaja, 2, '.', ','), 0, 0, 'R');
                $this->pdf->Ln(5);
            }*/

            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(240, 5, 'TOTAL =>', 0, 0, 'R');
            /*if ($vtotalAdicCaja > 0) {
                $this->pdf->Cell(30, 5, 'S/.' . number_format(($vtotalfb + $vtotalfc + $vtotalAdicCaja), 2, '.', ','), 0, 0, 'R');
            } else {
                $this->pdf->Cell(30, 5, 'S/.' . number_format(($vtotalfb + $vtotalfc), 2, '.', ','), 0, 0, 'R');
            }*/
			$this->pdf->Cell(30, 5, 'S/.' . number_format(($totalBancos + $totalCajas), 2, '.', ','), 0, 0, 'R');
			
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        }
        $this->pdf->Output('Reporte_de_cierre_caja_diario.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
