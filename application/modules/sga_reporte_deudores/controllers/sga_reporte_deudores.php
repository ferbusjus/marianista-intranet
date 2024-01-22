<?php

/**
 * @package       modules/sga_reporte_mes_completo/controller
 * @name            sga_reporte_mes_completo.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2018/04/01
 */
class sga_reporte_deudores extends CI_Controller {

    public $S_ANO = '';
    public $token = '';
    public $modulo = 'REPORTE-DEUDORES';

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
        $this->load->view('view_reporte_deudores', $data);
        $this->load->view('js_reporte_deudores');
        $this->load->view('view_footer');
    }

    public function printexceldeudores() {
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

        $arrLetra = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T');

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('REPORTE-DEUDORES');
        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:' . $arrLetra[(int) $vMes+6] . '1');
        $this->excel->setActiveSheetIndex(0)->setCellValue('A1', 'REPORTE - DEUDORES POR PERIODO  - ' . $this->S_ANO);
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[0] . '2', 'DNI');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[1] . '2', 'APELLIDOS Y NOMBRES');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[2] . '2', 'AULA');
        // DATOS DE LOS PADRES
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[3] . '2', 'PADRE');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[4] . '2', 'PADRE-CELU');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[5] . '2', 'MADRE');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[6] . '2', 'MADRE-CELU');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[7] . '2', 'APODERADO');
        $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[8] . '2', 'APODERADO-CELU');
        $iniCabecera=9;
        for ($x = $iniCabecera; $x <= (int) $vMes+6; $x++) {
            $strmes = (($x-6 < 10) ? '0' . $x-6 : $x-6);
            $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[$x] . '2', nombreMeses($strmes));
        }
        
        $boldArray = array('font' => array('bold' => true,), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $this->excel->getActiveSheet()->getStyle('A1:' . $arrLetra[(int) $vMes+6] . '2')->applyFromArray($boldArray);

        $dataPago = $this->objCobros->getPagoDeudores($vnivel, $vMes, $this->S_ANO);
        // echo "<pre>"; print_r($dataPago); echo "</pre>"; 
        // exit;
        if (is_array($dataPago) && count($dataPago) > 0) {

            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(45);
            $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
            $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(45);
            
            $inifila = 3;
            foreach ($dataPago as $pagos) {

                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[0] . $inifila, $pagos['dni']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[1] . $inifila, $pagos['alumno']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[2] . $inifila, $pagos['salon']);

                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[3] . $inifila, $pagos['padre']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[4] . $inifila, $pagos['padcelu']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[5] . $inifila, $pagos['madre']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[6] . $inifila, $pagos['madcelu']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[7] . $inifila, $pagos['apoderado']);
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[8] . $inifila, $pagos['apocelu']);
                
                for ($x = 3; $x <= (int) $vMes; $x++) {
                    $this->excel->getActiveSheet()->getColumnDimension($arrLetra[$x+6])->setWidth(10);
                    $strmes = (($x < 10) ? '0' . $x : $x);
                    $this->excel->setActiveSheetIndex(0)->setCellValue($arrLetra[$x+6] . $inifila, $pagos['mes_' . $x]);
                }
                $inifila += 1;
            }
            $rango = "A2:" . $arrLetra[(int) $vMes+6] . $inifila;
            $styleArray = array('font' => array('name' => 'Arial', 'size' => 10),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
            );
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }

// Forzamos a la descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporte_deudores_periodo_'.nombreMeses($vMes).'.xls"');
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
