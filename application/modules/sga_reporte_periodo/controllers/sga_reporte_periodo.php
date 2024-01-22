<?php

/**
 * @package       modules/sga_reporte_mes_completo/controller
 * @name            sga_reporte_mes_completo.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2018/04/01
 */
class sga_reporte_periodo extends CI_Controller {

    public $S_ANO = '';
    public $token = '';
    public $modulo = 'REPORTE-PAGO-PERIODO';

    public function __construct() {
        parent::__construct();
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('seguridad_model');
        $this->load->library('excel');
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataNivel"] = $this->objSalon->getNivel();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_reporte_periodo', $data);
        $this->load->view('js_reporte_periodo');
        $this->load->view('view_footer');
    }

    public function printexcel() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vnivel = $this->input->post("cbnivel");
        $vMes = $this->input->post("cbmes");

// Propiedades del documento
        $this->excel->getProperties()->setCreator("Sistemas-Dev")
                ->setLastModifiedBy("Sistemas-Dev")
                ->setTitle("Office 2010 XLSX Documento de prueba")
                ->setSubject("Office 2010 XLSX Documento de prueba")
                ->setDescription("Documento de prueba para Office 2010 XLSX, generado usando clases de PHP.")
                ->setKeywords("office 2010 openxml php")
                ->setCategory("Archivo con resultado de prueba");

        $arrLetra = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M');

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('REPORTE-PAGOS POR PERIODO');
        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:' . $arrLetra[(int) $vMes] . '1');
        $this->excel->setActiveSheetIndex(0)->setCellValue('A1', 'REPORTE - PAGOS POR PERIODO  - ' . $this->S_ANO);
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[0] . '2', 'DNI');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[1] . '2', 'APELLIDOS Y NOMBRES');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[2] . '2', 'AULA');

        for ($x = 3; $x <= (int) $vMes; $x++) {
            $strmes = (($x < 10) ? '0' . $x : $x);
            $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[$x] . '2', nombreMeses($strmes));
        }

        $boldArray = array('font' => array('bold' => true,), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $this->excel->getActiveSheet()->getStyle('A1:' . $arrLetra[(int) $vMes] . '2')->applyFromArray($boldArray);

        $dataPago = $this->objCobros->getPagoxperiodo($vnivel, $vMes, $this->S_ANO);
        // echo "<pre>"; print_r($dataPago); echo "</pre>"; 
        // exit;
        if (is_array($dataPago) && count($dataPago) > 0) {

            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $cel = 3;
            foreach ($dataPago as $pagos) {

                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[0] . $cel, $pagos['dni']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[1] . $cel, $pagos['nomcomp']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[2] . $cel, $pagos['nemodes']);

                for ($x = 3; $x <= (int) $vMes; $x++) {
                    $this->excel->getActiveSheet()->getColumnDimension($arrLetra[$x])->setWidth(10);
                    $strmes = (($x < 10) ? '0' . $x : $x);
                    $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[$x] . $cel, $pagos['mes_' . $x]);
                }
                $cel += 1;
            }
            $rango = "A2:" . $arrLetra[(int) $vMes] . $cel;
            $styleArray = array('font' => array('name' => 'Arial', 'size' => 10),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
            );
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }

// Forzamos a la descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporte_pagos_periodo.xls"');
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

    public function printcartascobro() {

        $this->load->library('pdf');
        $this->pdf = new Pdf ();

        $vnivel = $this->input->post("cbnivel");
        $vMes = $this->input->post("cbmes");

        $dataPago = $this->objCobros->getPagoxperiodo($vnivel, $vMes, $this->S_ANO);
        if (is_array($dataPago) && count($dataPago) > 0) {
            foreach ($dataPago as $pagos) {

                $this->pdf->AddPage();
                $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 10, 20, 20, 'PNG');
                $this->pdf->SetFont('Arial', 'B', 14);
                $this->pdf->SetXY(20, 15);
                $this->pdf->Cell(180, 10, 'CARTA DE COBRANZA DE SERVICIO   - ' . $this->S_ANO, 0, 0, 'C');
                $this->pdf->Ln();
                $this->pdf->Ln();
                $this->pdf->SetFont('Arial', 'B', 10);
                $this->pdf->SetXY(20, 35);
                $this->pdf->Cell(170, 5, 'Sr: Padre de familia o apoderado', 0, 0, 'L');
                $this->pdf->Ln();
                $this->pdf->Ln();
                $this->pdf->SetFont('Arial', '', 10);
                $dataNemo = explode("-", $pagos['nemodes']);
                $this->pdf->SetXY(20, 45);
                $this->pdf->Cell(170, 5, 'Del alumno(a): ' . utf8_decode($pagos['nomcomp']) . '    Grado: ' . $dataNemo[1] . '   Aula: ' . $dataNemo[2] . '   Nivel: ' . $dataNemo[0], 0, 0, 'L');
                $this->pdf->Ln();
                $this->pdf->SetXY(20, 50);
                $this->pdf->Cell(170, 5, utf8_decode('Ponemos en su conocimiento que hasta el momento usted mantiene una deuda contraida por el'), 0, 0, 'L');
                $this->pdf->Ln();
                $this->pdf->SetXY(20, 55);
                $this->pdf->Cell(170, 5, utf8_decode('servicio educativo de su menor hijo(a) con nuestra institución.'), 0, 0, 'L');
                $this->pdf->Ln();
                $this->pdf->Ln();
                $this->pdf->SetXY(20, 65);
                $this->pdf->Cell(170, 5, utf8_decode('Hemos cumplido con enviarle una carta el día miercoles 27 de Junio para que regularice ó suscriba'), 0, 0, 'L');
                $this->pdf->Ln();
                $this->pdf->SetXY(20, 70);
                $this->pdf->Cell(170, 5, utf8_decode('un acuerdo de pago como establece las normas del Ministerio de Educación.'), 0, 0, 'L');
                $this->pdf->Ln();
                $this->pdf->Ln();
                $this->pdf->Ln();



                $dataDeuda = $this->objCobros->getPendientesxAlumno($pagos['alucod'], '05');
                $this->pdf->SetFont('Arial', 'B', 10);
                $this->pdf->SetXY(35, 90);
                $this->pdf->Cell(25, 5, 'PERIODO', 1, 0, 'C');
                $this->pdf->Cell(25, 5, 'FEC-VENC', 1, 0, 'C');
                $this->pdf->Cell(25, 5, 'MONTO', 1, 0, 'C');
                $this->pdf->Cell(25, 5, 'MORA', 1, 0, 'C');
                $this->pdf->Cell(25, 5, 'TOTAL', 1, 0, 'C');
                $this->pdf->Ln();
                $filay = 95;
                $vtotal = 0;
                foreach ($dataDeuda as $deuda) {
                    $this->pdf->SetFont('Arial', '', 10);
                    $this->pdf->SetXY(35, $filay);
                    $this->pdf->Cell(25, 5, $deuda->periodo, 1, 0, 'C');
                    $this->pdf->Cell(25, 5, $deuda->fecven, 1, 0, 'C');
                    $this->pdf->Cell(25, 5, 'S/.' . $deuda->montopen, 1, 0, 'C');
                    $this->pdf->Cell(25, 5, 'S/.' . $deuda->mora, 1, 0, 'C');
                    $this->pdf->Cell(25, 5, 'S/.' . $deuda->total, 1, 0, 'C');
                    $filay += 5;
                    $vtotal += $deuda->total;
                }
                $this->pdf->SetFont('Arial', 'B', 10);
                $this->pdf->SetXY(35, ($filay));

                $this->pdf->Cell(100, 5, '', 1, 0, 'C');
                $this->pdf->Cell(25, 5, 'S/.' . number_format($vtotal, 2, '.', ','), 1, 0, 'C');



                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->SetXY(100, 220);
                $this->pdf->Cell(100, 5, 'Villa Maria del Triunfo  ' . date('d/m/Y'), 0, 0, 'L');


                $this->pdf->SetXY(100, 240);
                $this->pdf->Cell(50, 5, utf8_decode('La Dirección'), 0, 0, 'L');
            }
        }

        $this->pdf->Output('Carta_de_cobros_deudores.pdf', 'I');
    }

    public function printpagos() {
        /*  echo "<pre>";
          print_r ($_POST);
          echo "</pre>";
          exit; */

        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vnivel = $this->input->post("cbnivel");
        $vMes = $this->input->post("cbmes");
        $vniveldes = '';
        switch ($vnivel) {
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
        }

        $vmesdes = '';
        switch ($vMes) {
            case '03':
                $vmesdes = 'MARZO';
                break;
            case '04':
                $vmesdes = 'ABRIL';
                break;
            case '05':
                $vmesdes = 'MAYO';
                break;
            case '06':
                $vmesdes = 'JUNIO';
                break;
            case '07':
                $vmesdes = 'JULIO';
                break;
            case '08':
                $vmesdes = 'AGOSTO';
                break;
            case '09':
                $vmesdes = 'SETIEMBRE';
                break;
            case '10':
                $vmesdes = 'OCTUBRE';
                break;
            case '11':
                $vmesdes = 'NOVIEMBRE';
                break;
            case '12':
                $vmesdes = 'DICIEMBRE';
                break;
        }

        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 30, 30, 'PNG');
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Cell(270, 5, 'Fecha :' . date("Y-m-d"), 0, 0, 'R');
        $this->pdf->Ln();
        $this->pdf->Cell(270, 5, 'Hora :' . date("H:i:s"), 0, 0, 'R');
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE - PAGO POR PERIODO   - ' . $this->S_ANO, 0, 0, 'C');

        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Nivel : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, $vniveldes, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Desde : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(30, 5, 'MARZO', 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Hasta : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(35, 5, $vmesdes, 0, 0, 'L');
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
        $this->pdf->Cell(20, 5, utf8_decode('DNI'), 0, 0, 'C');
        $this->pdf->Cell(80, 5, utf8_decode('APELLIDOS Y NOMBRES'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('AULA'), 0, 0, 'C');
        for ($x = 3; $x <= (int) $vMes; $x++) {
            $strmes = (($x < 10) ? '0' . $x : $x);
            $this->pdf->Cell(15, 5, nombreMeses($strmes), 0, 0, 'C');
        }
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);

        $dataPago = $this->objCobros->getPagoxperiodo($vnivel, $vMes, $this->S_ANO);
        $this->pdf->SetFont('Arial', '', 10);
        if (is_array($dataPago) && count($dataPago) > 0) {
            foreach ($dataPago as $pagos) {

                $datoNemo = explode("-", $pagos['nemodes']);
                $this->pdf->Cell(20, 4, $pagos['dni'], 0, 0, 'C');
                $this->pdf->Cell(80, 4, utf8_decode($pagos['nomcomp']), 0, 0, 'L');
                $this->pdf->Cell(25, 4, utf8_decode(trim($datoNemo[2])), 0, 0, 'C');
                for ($x = 3; $x <= (int) $vMes; $x++) {
                    $this->pdf->Cell(15, 4, $pagos['mes_' . $x], 0, 0, 'C');
                }
                $this->pdf->Ln(3);
                $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
                $this->pdf->Ln(3);
            }
        }
        $this->pdf->Output('Reporte_mensual_pagos_periodo_' . $this->S_ANO . '.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
