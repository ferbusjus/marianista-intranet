<?php

/**
 * @package       modules/sga_extractor/controller
 * @name            sga_extractor.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2019 SISTEMAS-DEV
 * @version         1.0 - 2019/01/07
 */
class sga_extractor extends CI_Controller {

    public $token = '';
    public $modulo = 'EXTRACTOR DE DATOS';
    public $S_ANO = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('asistencia_model', 'objAsistencia');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('matricula_model', 'objMatricula');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('observacion_model', 'objObservacion');
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
        $this->load->view('view_extractor', $data);
        $this->load->view('js_script');
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

    public function printExcel() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }

        $dataFiltro = array(
            //'tiporeporte' => $_POST["cbtiporeporte"],
            'instrucod' => (($_POST["cbnivel"] != '') ? $_POST["cbnivel"] : "Todos"),
            'gradocod' => (($_POST["cbgrado"] != '') ? $_POST["cbgrado"] : "Todos"),
            'seccioncod' => (($_POST["cbaula"] != '') ? $_POST["cbaula"] : "Todos")
        );

        $listaData = $this->objMatricula->getFiltroExtractor($_POST['chkCampos'], $dataFiltro);
        /* echo "<pre>";
          print_r($listaData);
          echo "</pre>";
          exit; */
        // Propiedades del documento
        $this->excel->getProperties()->setCreator("Sistemas-Dev")
                ->setLastModifiedBy("Sistemas-Dev")
                ->setTitle("Office 2010 XLSX Documento de prueba")
                ->setSubject("Office 2010 XLSX Documento de prueba")
                ->setDescription("Documento de prueba para Office 2010 XLSX, generado usando clases de PHP.")
                ->setKeywords("office 2010 openxml php")
                ->setCategory("Archivo con resultado de prueba");

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('DATA MATRICULAS - ' . $this->S_ANO);
        //$this->excel->setActiveSheetIndex(0)->mergeCells('A1:F1');

        $estiloTituloReporte = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 16
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'CCFFCC')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $styleArray = array('font' => array('name' => 'Arial', 'size' => 10, 'bold' => true),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF'))),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'CCFFCC')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $styleFilas = array('font' => array('name' => 'Arial', 'size' => 10,),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            )
        );
        if (is_array($listaData) && count($listaData) > 0) {
            // ======================= Campos de Matricula =================================
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 1] = 'FECMAT';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 2] = 'USUREG';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 3] = 'OBSERVACION';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 4] = 'OBSDOCUMENTOS';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 5] = 'DOCUMENTOS';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 6] = 'NUMRECIBO';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 7] = 'VOUCHER';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 8] = 'MEDIO_PAGO';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 9] = 'MONTO';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 10] = 'NIVEL';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 11] = 'GRADO';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 12] = 'AULA';
            $_POST['chkCampos'][sizeof($_POST['chkCampos']) + 13] = 'USUCAMPUS';
            // ============================================================================
            /* echo "<pre>";
              print_r($_POST['chkCampos']);
              echo "</pre>";
              exit; */
            $cel = 3;
            $posIniLetra = 2;
            $arrayLetras = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I',
                9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S',
                19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z', 26 => 'AA', 27 => 'AB',
                28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH', 34 => 'AI', 35 => 'AJ',
                36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP', 42 => 'AQ', 43 => 'AR',
                44 => 'AS', 45 => 'AT', 46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX', 50 => 'AY', 51 => 'AZ', 52 => 'BA');
            $cont = 0;
            foreach ($_POST['chkCampos'] as $campos) {
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrayLetras[$cont] . '2', $campos);

                $cont++;
            }

            /* foreach ($this->excel->getActiveSheet()->getColumnDimension() as $col) {
              $col->setAutoSize(true);
              }
              $this->excel->getActiveSheet()->calculateColumnWidths(); */

            // == autoajusta pero al tamaño de la columna de excel
            /* foreach (range('A', $this->excel->getActiveSheet()->getHighestDataColumn()) as $col) {
              $this->excel->getActiveSheet()
              ->getColumnDimension($col)
              ->setAutoSize(true);
              } */

            $rango = "A1:" . $arrayLetras[$cont - 1] . "1";
            $this->excel->setActiveSheetIndex(0)->mergeCells($rango);
            $this->excel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'LISTA DE ALUMNOS MATRICULADOS  - ' . $this->S_ANO);
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($estiloTituloReporte);
            $rango = "A2:" . $arrayLetras[$cont - 1] . "2";
            $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
        }

        $row = 3;
        foreach ($listaData as $lista) {
            $cont = 0;
            foreach ($_POST['chkCampos'] as $campos) {
                //echo $arrayLetras[$cont].$row."*".$lista[$campos]."<br>";
                if ($campos == 'USUCAMPUS') {
                    $lista[$campos] = str_replace("ñ", "n", str_replace("á", "a", str_replace("é", "e", str_replace("í", "i", str_replace("ó", "o", str_replace("ú", "u", $lista[$campos]))))));
                }
                $this->excel->setActiveSheetIndex(0)->setCellValue($arrayLetras[$cont] . $row, $lista[$campos]);
                if ($campos === 'DOCUMENTOS') {
                    $vancho = 170;
                } elseif ($campos === 'DIRECCION' || $campos === 'ALUEMAIL' || $campos === 'PADDIRECCION' || $campos === 'PADEMAIL' || $campos === 'MADDIRECCION' || $campos === 'MADEMAIL' || $campos === 'OBSERVACION') {
                    $vancho = 80;
                } elseif ($campos === 'DNI' || $campos === 'FECNAC' || $campos === 'SEXO' || $campos === 'TELEFONO' || $campos === 'TELEFONO2' || $campos === 'DNIPATER' || $campos === 'DNIMATER' || $campos === 'PADCELU' || $campos === 'MADCELU' || $campos === 'USUREG' || $campos === 'PADFECNAC' || $campos === 'MADFECNAC') {
                    $vancho = 15;
                } else {
                    $vancho = 25;
                }
                $this->excel->getActiveSheet()->getColumnDimension($arrayLetras[$cont])->setWidth($vancho);
                $cont++;
            }
            $row++;
        }
        $rango = "A3:" . $arrayLetras[$cont - 1] . ($row - 1);
        $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleFilas);
        // Forzamos a la descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="data_alumnos_matriculados_' . date("Y-m-d") . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

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
