<?php

/**
 * @package       modules/sga_reporte_mes_completo/controller
 * @name            sga_reporte_mes_completo.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2018/04/01
 */
class sga_reporte_completo extends CI_Controller {

    public $S_ANO = '';
    public $token = '';
    public $modulo = 'REPORTE-MES-COMPLETO';

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
        $this->load->view('view_reporte_completo', $data);
        $this->load->view('js_reporte_completo');
        $this->load->view('view_footer');
    }

    public function printexcel() {
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

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('REPORTE-PAGOS AL DIA');
        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:F1');
        $this->excel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'REPORTE - PAGOS AL DIA   - ' . $this->S_ANO)
                ->setCellValue('A2', 'DNI')
                ->setCellValue('B2', 'APELLIDOS Y NOMBRES')
                ->setCellValue('C2', 'AULA')
                ->setCellValue('D2', 'CONCEPTO DE PAGO')
                ->setCellValue('E2', 'MODO')
                ->setCellValue('F2', 'FECHA');
        // Fuente de la primera fila en negrita
        $boldArray = array('font' => array('bold' => true,), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $this->excel->getActiveSheet()->getStyle('A1:F2')->applyFromArray($boldArray);

        //Ancho de las columnas
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

        $dataPago = $this->objCobros->getPagoxMesCompleto($vnivel, $vMes, $this->S_ANO);
        if (is_array($dataPago) && count($dataPago) > 0) {
            $cel = 3;
            foreach ($dataPago as $pagos) {
                $a = "A" . $cel;
                $b = "B" . $cel;
                $c = "C" . $cel;
                $d = "D" . $cel;
                $e = "E" . $cel;
                $f = "F" . $cel;
                // Agregar datos
                $this->excel->setActiveSheetIndex(0)
                        ->setCellValue($a, $pagos->dni)
                        ->setCellValue($b, $pagos->nomcomp)
                        ->setCellValue($c, $pagos->nemodes)
                        ->setCellValue($d, $pagos->condes . " - " . $pagos->mesdes)
                        ->setCellValue($e, (($pagos->tipopago == 'C') ? 'Caja' : 'Banco'))
                        ->setCellValue($f, $pagos->fecha);
                $cel += 1;
            }
            $rango = "A2:$f";
            $styleArray = array('font' => array('name' => 'Arial', 'size' => 10),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
            );
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }

        // Forzamos a la descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporte_pagos_completo_' . $this->S_ANO . '.xls"');
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
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE - PAGOS AL DIA   - ' . $this->S_ANO, 0, 0, 'C');
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
        $this->pdf->Cell(35, 5, 'Mes : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, $vmesdes, 0, 0, 'L');
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
        $this->pdf->Cell(30, 5, utf8_decode('DNI'), 0, 0, 'C');
        $this->pdf->Cell(100, 5, utf8_decode('APELLIDOS Y NOMBRES'), 0, 0, 'C');
        $this->pdf->Cell(60, 5, utf8_decode('AULA'), 0, 0, 'C');
        $this->pdf->Cell(40, 5, utf8_decode('CONCEPTO DE PAGO'), 0, 0, 'C');
        $this->pdf->Cell(20, 5, utf8_decode('MODO'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('FECHA'), 0, 0, 'C');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);

        $dataPago = $this->objCobros->getPagoxMesCompleto($vnivel, $vMes, $this->S_ANO);
        $this->pdf->SetFont('Arial', '', 10);
        if (is_array($dataPago) && count($dataPago) > 0) {
            foreach ($dataPago as $pagos) {
                $this->pdf->Cell(30, 5, $pagos->dni, 1, 0, 'C');
                $this->pdf->Cell(100, 5, utf8_decode($pagos->nomcomp), 1, 0, 'L');
                $this->pdf->Cell(60, 5, utf8_decode($pagos->nemodes), 1, 0, 'L');
                $this->pdf->Cell(40, 5, utf8_decode($pagos->condes . " - " . $pagos->mesdes), 1, 0, 'L');
                $this->pdf->Cell(20, 5, (($pagos->tipopago == 'C') ? 'Caja' : 'Banco'), 1, 0, 'C');
                $this->pdf->Cell(30, 5, $pagos->fecha, 1, 0, 'C');
                $this->pdf->Ln(5);
            }
            /*
              $this->pdf->SetFont ('Arial', 'B', 10);
              $this->pdf->Ln (5);
              $this->pdf->Cell (275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
              $this->pdf->Ln (5);
              $this->pdf->Cell (220, 5, 'TOTAL ' . $value . ' =>', 0, 0, 'R');
              $this->pdf->Cell (20, 5, 'S/.' . number_format ($vtotalb, 2, '.', ','), 0, 0, 'R');
              $this->pdf->Cell (30, 5, 'S/.' . number_format ($vtotalc, 2, '.', ','), 0, 0, 'R');
              $this->pdf->Ln (5);
              $this->pdf->Cell (275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
              $this->pdf->Ln (5);
             */
        }
        $this->pdf->Output('Reporte_mensual_pagos_completo_' . $this->S_ANO . '.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
