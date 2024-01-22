<?php

/**
 * @package       modules/sga_reporte_pagos/controller
 * @name            sga_reporte_pagos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2017/09/03
 */
class sga_reporte_pago extends CI_Controller {

    public $token = '';
    public $modulo = 'REPORTE-PAGO';
    public $S_ANO = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('asistencia_model', 'objAsistencia');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('observacion_model', 'objObservacion');
        $this->load->model('seguridad_model');
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');
        $this->load->library('excel');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataNivel"] = $this->objSalon->getNivel();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_reporte_pago', $data);
        $this->load->view('js_reporte');
        $this->load->view('view_footer');
    }

    public function lstgrado($nivel = 0) {
        $arrData = array();
        $dataGrado = $this->objSalon->getGrados($nivel);
        foreach ($dataGrado as $gr)
            $arrData[] = array(
                'id' => $gr->GRADOCOD,
                'value' => $gr->GRADODES
            );
        echo json_encode($arrData);
    }

    public function lstaula($nivel = 0, $grado = 0) {
        $arrData = array();
        $dataSalon = $this->objSalon->getAulasxNivel($nivel, $grado);
        foreach ($dataSalon as $sal)
            $arrData[] = array(
                'id' => $sal->AULACOD,
                'value' => $sal->AULADES
            );
        echo json_encode($arrData);
    }

    public function printpagos() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vmes = $_POST["cbmes"];
        $vgrado = $_POST["cbgrado"];
        $vnivel = $_POST["cbnivel"];
        $vaula = $_POST["cbaula"];

        if ($vaula != '0')
            $dataAula = $this->objSalon->getAulasxId($vaula, 1);
        else
            $dataAula = "Todos";

        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE DE DEUDORES POR MES   - ' . $this->S_ANO, 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Nivel : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, nombreNivel($vnivel), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Grado : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(50, 5, utf8_decode(nombreGrado($vnivel, $vgrado)), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Aula : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, utf8_decode($dataAula), 0, 0, 'L');
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, 'Mes : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, nombremes($vmes), 0, 0, 'L');

        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        // ================ Imprimiendo las deudas por alumno ==================
        $arrDataPagos = $this->objSalon->getDeudasXPeriodo($vmes, $vnivel, $vgrado, $vaula);
        /* echo "<pre>"; 
          print_r($arrDataPagos);
          echo "</pre>";
          exit; */

        $this->pdf->SetFont('Arial', 'B', 10);
        $valorY = 50;
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(20, 5, utf8_decode('CÓDIGO'), 0, 0, 'C');
        // --- Si selecciona Aula ya no mostrar la columna Aula
        if ($vaula != '0') {
            $this->pdf->Cell(150, 5, utf8_decode('APELLIDOS  Y NOMBRES'), 0, 0, 'L');
        } else {
            $this->pdf->Cell(120, 5, utf8_decode('APELLIDOS  Y NOMBRES'), 0, 0, 'L');
            $this->pdf->Cell(30, 5, utf8_decode('AULA'), 0, 0, 'C');
        }
        $this->pdf->Cell(20, 5, utf8_decode('NGS'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('MES'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('FEC-VEN'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('MONTO'), 0, 0, 'C');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);

        if (is_array($arrDataPagos) && count($arrDataPagos) > 0) {
            $this->pdf->SetFont('Arial', '', 10);
            $vtotal = 0;
            foreach ($arrDataPagos as $pago) {
                $this->pdf->Cell(20, 5, utf8_decode($pago->alucod), 0, 0, 'C');
                if ($vaula != '0') {
                    $this->pdf->Cell(150, 5, utf8_decode($pago->nomcomp), 0, 0, 'L');
                } else {
                    $this->pdf->Cell(120, 5, utf8_decode($pago->nomcomp), 0, 0, 'L');
                    $this->pdf->Cell(30, 5, utf8_decode($pago->aulades), 0, 0, 'C');
                }
                $this->pdf->Cell(20, 5, utf8_decode($pago->ngs), 0, 0, 'C');
                $this->pdf->Cell(30, 5, utf8_decode($pago->mesdes), 0, 0, 'C');
                $this->pdf->Cell(30, 5, utf8_decode($pago->fecven), 0, 0, 'C');
                $this->pdf->Cell(30, 5, utf8_decode($pago->montopen), 0, 0, 'C');
                $this->pdf->Ln(5);
                $vtotal += $pago->montopen;
            }
        }
        $this->pdf->Output('Reporte_de_Deudores_por_Nivel.pdf', 'I');
    }

    public function printExcel() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vmes = $_POST["cbmes"];
        $vgrado = $_POST["cbgrado"];
        $vnivel = $_POST["cbnivel"];
        $vaula = $_POST["cbaula"];

        if ($vaula != '0')
            $dataAula = $this->objSalon->getAulasxId($vaula, 1);
        else
            $dataAula = "Todos";
        // Propiedades del documento
        $this->excel->getProperties()->setCreator("SISTEMAS-DEV")
                ->setLastModifiedBy("SISTEMAS-DEV")
                ->setTitle("Reporte - Resumen General")
                ->setSubject("REPORTES SISTEMAS-DEV")
                ->setDescription("Reporte que Muestra lo pagos realizado por Razon Social")
                ->setKeywords("Reporte - Resumen General")
                ->setCategory("SISTEMAS-DEV");

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('RESUMEN CAJA DIARIO - ' . $this->S_ANO);
        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:G1');
        $this->excel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'REPORTE DE DEUDORES POR MES - ' . $this->S_ANO)
                ->setCellValue('A2', 'CODIGO')
                ->setCellValue('B2', 'APELLIDOS Y NOMBRES')
                ->setCellValue('C2', 'AULA')
                ->setCellValue('D2', 'NGS')
                ->setCellValue('E2', 'MES')
                ->setCellValue('F2', 'FEC-VENC')
                ->setCellValue('G2', 'MONTO');
        // Fuente de la primera fila en negrita
        $boldArray = array('font' => array('bold' => true,), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $this->excel->getActiveSheet()->getStyle('A1:G2')->applyFromArray($boldArray);

        //Ancho de las columnas
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

        $arrDataPagos = $this->objSalon->getDeudasXPeriodo($vmes, $vnivel, $vgrado, $vaula);
        if (is_array($arrDataPagos) && count($arrDataPagos) > 0) {
            $cel = 3;
            foreach ($arrDataPagos as $pagos) {
                $a = "A" . $cel;
                $b = "B" . $cel;
                $c = "C" . $cel;
                $d = "D" . $cel;
                $e = "E" . $cel;
                $f = "F" . $cel;
                $g = "G" . $cel;
                // Agregar datos
                $this->excel->setActiveSheetIndex(0)
                        ->setCellValue($a, $pagos->alucod)
                        ->setCellValue($b, $pagos->nomcomp)
                        ->setCellValue($c, $pagos->aulades)
                        ->setCellValue($d, $pagos->ngs)
                        ->setCellValue($e, $pagos->mesdes)
                        ->setCellValue($f, $pagos->fecven)
                        ->setCellValue($g, $pagos->montopen);
                $cel += 1;
            }
            $rango = "A2:$g";
            $styleArray = array('font' => array('name' => 'Arial', 'size' => 10),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
            );
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }

        // Forzamos a la descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Deudores_x_Mes_' . $this->S_ANO . '.xls"');
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

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
