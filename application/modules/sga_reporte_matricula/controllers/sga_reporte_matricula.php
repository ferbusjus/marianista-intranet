<?php

/**
 * @package       modules/sga_reporte_matricula/controller
 * @name            sga_reporte_matricula.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2019 SISTEMAS-DEV
 * @version         1.0 - 2019/01/06
 */
class sga_reporte_matricula extends CI_Controller {

    public $token = '';
    public $modulo = 'REPORTE-MATRICULAS';
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
        $this->load->view('view_reporte_matricula', $data);
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

    public function printsPdf() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }

        $dataFiltro = array(
            'tiporeporte' => $_POST["cbtiporeporte"],
            'instrucod' => (($_POST["cbnivel"] != '') ? $_POST["cbnivel"] : "Todos"),
            'gradocod' => (($_POST["cbgrado"] != '') ? $_POST["cbgrado"] : "Todos"),
            'seccioncod' => (($_POST["cbaula"] != '') ? $_POST["cbaula"] : "Todos")
        );

        if ($_POST["cbtiporeporte"] === '1')
            $listaData = $this->objMatricula->getlistaFiltro($dataFiltro, 1);
        if ($_POST["cbtiporeporte"] === '2')
            $listaData = $this->objMatricula->getlistaFiltro($dataFiltro, 2);
        if ($_POST["cbtiporeporte"] === '3')
            $listaData = $this->objMatricula->getlistaFiltro($dataFiltro, 3);
        echo "REPORTE EN CONSTRUCCION.";
        exit;
    }

    public function printsExcel() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $dataFiltro = array(
            'tiporeporte' => $_POST["cbtiporeporte"],
            'instrucod' => (($_POST["cbnivel"] != '') ? $_POST["cbnivel"] : "Todos"),
            'gradocod' => (($_POST["cbgrado"] != '') ? $_POST["cbgrado"] : "Todos"),
            'seccioncod' => (($_POST["cbaula"] != '') ? $_POST["cbaula"] : "Todos")
        );

        if ($_POST["cbtiporeporte"] === '1')
            $listaData = $this->objMatricula->getlistaFiltro($dataFiltro, 1);
        if ($_POST["cbtiporeporte"] === '2')
            $listaData = $this->objMatricula->getlistaFiltro($dataFiltro, 2);
        if ($_POST["cbtiporeporte"] === '3')
            $listaData = $this->objMatricula->getlistaFiltro($dataFiltro, 3);
        /*  echo "<pre>";
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
        // URL : https://codigosdeprogramacion.com/2017/02/06/curso-de-php-y-mysql-13-reporte-en-excel/
        /* $gdImage = imagecreatefrompng('images/insigniachico.png'); //Logotipo
          $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
          $objDrawing->setName('Logotipo');
          $objDrawing->setDescription('Logotipo');
          $objDrawing->setImageResource($gdImage);
          $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
          $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
          $objDrawing->setHeight(100);
          $objDrawing->setCoordinates('A1');
          $objDrawing->setWorksheet($this->excel->getActiveSheet()); */

        if ($_POST["cbtiporeporte"] === '1') {

            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('LISTA MATRICULAS - ' . $this->S_ANO);
            $this->excel->setActiveSheetIndex(0)->mergeCells('A1:H1');

            $estiloTituloReporte = array(
                'font' => array(
                    'name' => 'Arial',
                    'bold' => true,
                    'italic' => false,
                    'strike' => false,
                    'size' => 14
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#ABF2FF')
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

            $this->excel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'LISTA DE ALUMNOS MATRICULADOS  - ' . $this->S_ANO)
                    ->setCellValue('A2', 'CÓDIGO')
                    ->setCellValue('B2', 'DNI')
                    ->setCellValue('C2', 'APELLIDOS Y NOMBRES')
                    ->setCellValue('D2', 'SEXO')
                    ->setCellValue('E2', 'NIVEL')
                    ->setCellValue('F2', 'GRADO')
                    ->setCellValue('G2', 'AULA')
                    ->setCellValue('H2', 'FECHA-MATRICULA')
                    ->setCellValue('I2', 'OBSERVACION');
            // Fuente de la primera fila en negrita
            $boldArray = array(
                'font' => array('bold' => true, 'name' => 'Arial', 'size' => 12),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '#ABF2FF'))
            );
            $this->excel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estiloTituloReporte);
            $this->excel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($boldArray);

            //Ancho de las columnas
            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(70);
            if (is_array($listaData) && count($listaData) > 0) {
                $cel = 3;
                foreach ($listaData as $lista) {
                    if ($lista->instrucod === 'I')
                        $lista->instrucod = "INICIAL";
                    if ($lista->instrucod === 'P')
                        $lista->instrucod = "PRIMARIA";
                    if ($lista->instrucod === 'S')
                        $lista->instrucod = "SECUNDARIA";
                    $a = "A" . $cel;
                    $b = "B" . $cel;
                    $c = "C" . $cel;
                    $d = "D" . $cel;
                    $e = "E" . $cel;
                    $f = "F" . $cel;
                    $g = "G" . $cel;
                    $h = "H" . $cel;
                    $i = "I" . $cel;
                    // Agregar datos
                    $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($a, $lista->alucod)
                            ->setCellValue($b, $lista->dni)
                            ->setCellValue($c, $lista->nomcomp)
                            ->setCellValue($d, $lista->sexo)
                            ->setCellValue($e, $lista->instrucod)
                            ->setCellValue($f, $lista->gradocod)
                            ->setCellValue($g, $lista->aulades)
                            ->setCellValue($h, $lista->fechamat)
                            ->setCellValue($i, $lista->observacion);
                    $cel += 1;
                }
                $rango = "A2:$i";
                $styleArray = array('font' => array('name' => 'Arial', 'size' => 10), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
                );
                $styleArray2 = array('font' => array('name' => 'Arial', 'size' => 10), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
                );

                $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
                // -- Para que las Columnas se alinien a la Izquierda
                $this->excel->getActiveSheet()->getStyle("C3:C" . ($cel - 1))->applyFromArray($styleArray2);
                $this->excel->getActiveSheet()->getStyle("I3:I" . ($cel - 1))->applyFromArray($styleArray2);
            }
            // Forzamos a la descarga
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="listado_alumnos_matriculados_' . date("Y-m-d") . '.xls"');
            header('Cache-Control: max-age=0');
            // Si usted está sirviendo a IE 9 , a continuación, puede ser necesaria la siguiente
            header('Cache-Control: max-age=1');
        } elseif ($_POST["cbtiporeporte"] === '2') {

            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('MATRICULAS AGRUPADAS - ' . $this->S_ANO);
            $this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
            $this->excel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'LISTA AGRUPADA DE MATRICULAS  - ' . $this->S_ANO)
                    ->setCellValue('A2', 'CÓDIGO')
                    ->setCellValue('B2', 'DESCRIPCION DE AULA')
                    ->setCellValue('C2', 'LIMITE')
                    ->setCellValue('D2', 'MATRICULADOS')
                    ->setCellValue('E2', '% AVANCE');
            // Fuente de la primera fila en negrita
            $boldArray = array(
                'font' => array('bold' => true, 'name' => 'Arial', 'size' => 16),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '#ABF2FF'))
            );
            $this->excel->getActiveSheet()->getStyle('A1:E2')->applyFromArray($boldArray);

            //Ancho de las columnas
            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);

            if (is_array($listaData) && count($listaData) > 0) {
                $cel = 3;
                foreach ($listaData as $lista) {
                    $a = "A" . $cel;
                    $b = "B" . $cel;
                    $c = "C" . $cel;
                    $d = "D" . $cel;
                    $e = "E" . $cel;
                    // Agregar datos
                    $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($a, $lista->nemo)
                            ->setCellValue($b, $lista->nemodes)
                            ->setCellValue($c, $lista->limite)
                            ->setCellValue($d, $lista->matriculados)
                            ->setCellValue($e, $lista->porcentaje);
                    $cel += 1;
                }
                $rango = "A2:$e";
                $styleArray = array('font' => array('name' => 'Arial', 'size' => 10), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
                );
                $styleArray2 = array('font' => array('name' => 'Arial', 'size' => 10), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FFF')))
                );

                $this->excel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
                $this->excel->getActiveSheet()->getStyle("B3:B" . ($cel - 1))->applyFromArray($styleArray2);
            }
            // Forzamos a la descarga
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="totales_alumnos_matriculados_' . date("Y-m-d") . '.xls"');
            header('Cache-Control: max-age=0');
            // Si usted está sirviendo a IE 9 , a continuación, puede ser necesaria la siguiente
            header('Cache-Control: max-age=1');
        }


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
