<?php

/**
 * @package       modules/sga_utiles/controller
 * @name            sga_boletas.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2020 SISTEMAS-DEV
 * @version         1.0 - 25/05/2020
 */
class sga_boletas extends CI_Controller {

    public $token = '';
    public $modulo = 'BOLETAS';
    public $_session = '';
    public $ano = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('nota_model', 'objNota');
        $this->load->model('seguridad_model');
        $this->_session = $this->nativesession->get('arrDataSesion');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//$extension= pathinfo($url, PATHINFO_EXTENSION);
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
// ====== Verifico si tiene persimo al modulo =====        
        $flgvalida = $this->seguridad_model->verificaPermisoModulo($this->_session['USUCOD']);
        if ($flgvalida && $this->ano >= 2020) { // Año de Inicio de boletas por intranet
            $data['token'] = $this->token();
            $data['anio'] = $this->ano;
            $nivel = "";
            if ($this->_session['USUCOD'] == "GARISANCA") {
                $nivel = "S";
            } elseif ($this->_session['USUCOD'] == "WCORREA") {
                $nivel = "P";
            }
            $data['listaAulas'] = $this->objSalon->getAllAulas($nivel);
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('js_boletas');
            $this->load->view('view_lista', $data);
            $this->load->view('view_footer');
        } else {
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('view_default');
            $this->load->view('view_footer');
        }
    }

    public function resumen() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//$extension= pathinfo($url, PATHINFO_EXTENSION);
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
// ====== Verifico si tiene persimo al modulo =====        
        $flgvalida = true; // $this->seguridad_model->verificaPermisoModulo($this->_session['USUCOD']);
        if ($flgvalida) { // Año de Inicio de boletas por intranet
            $data['token'] = $this->token();
            $data['anio'] = $this->ano;
            //$nivel = "";
            if ($this->_session['USUCOD'] == "GARISANCA") {
                $nivel[0] = array('instrucod' => 'S', 'descripcion' => 'SECUNDARIA');
                //$nivel = "S";
            } elseif ($this->_session['USUCOD'] == "WCORREA") {
                //$nivel = "P";
                $nivel[0] = array('instrucod' => 'P', 'descripcion' => 'PRIMARIA');
            } else {
                $nivel[0] = array('instrucod' => 'P', 'descripcion' => 'PRIMARIA');
                $nivel[1] = array('instrucod' => 'S', 'descripcion' => 'SECUNDARIA');
            }

            $data['listaNivel'] = $nivel;
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('js_resumen');
            $this->load->view('view_resumen', $data);
            $this->load->view('view_footer');
        } else {
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('view_default');
            $this->load->view('view_footer');
        }
    }

    public function getGrado() {
        $nivel = $this->input->post("nivel");
        $output = array();
        if ($nivel == 'P') {
            $arrData [] = array('id' => 1, 'value' => '1 GRADO');
            $arrData [] = array('id' => 2, 'value' => '2 GRADO');
            $arrData [] = array('id' => 3, 'value' => '3 GRADO');
            $arrData [] = array('id' => 4, 'value' => '4 GRADO');
            $arrData [] = array('id' => 5, 'value' => '5 GRADO');
            $arrData [] = array('id' => 6, 'value' => '6 GRADO');
        } elseif ($nivel == 'S') {
            $arrData [] = array('id' => 1, 'value' => '1 AÑO');
            $arrData [] = array('id' => 2, 'value' => '2 AÑO');
            $arrData [] = array('id' => 3, 'value' => '3 AÑO');
            $arrData [] = array('id' => 4, 'value' => '4 AÑO');
            $arrData [] = array('id' => 5, 'value' => '5 AÑO');
        }
        $output = array('data' => $arrData);
        echo json_encode($output);
    }

    public function getUnidad() {
        sleep(1);
        $bim = $this->input->post("bimestre");
        $output = array();
        if ($bim == 1) {
            $arrData [] = array('id' => 1, 'value' => 'UNIDAD I');
            $arrData [] = array('id' => 2, 'value' => 'UNIDAD II');
        } elseif ($bim == 2) {
            $arrData [] = array('id' => 3, 'value' => 'UNIDAD III');
            $arrData [] = array('id' => 4, 'value' => 'UNIDAD IV');
        } elseif ($bim == 3) {
            $arrData [] = array('id' => 5, 'value' => 'UNIDAD V');
            $arrData [] = array('id' => 6, 'value' => 'UNIDAD VI');
        } elseif ($bim == 4) {
            $arrData [] = array('id' => 7, 'value' => 'UNIDAD VII');
            $arrData [] = array('id' => 8, 'value' => 'UNIDAD VIII');
        }
        $output = array('data' => $arrData);
        echo json_encode($output);
    }

    public function getAlumnos() {
        sleep(1);
        $nemo = $this->input->post("nemo");
        $output = array();
        $dataAlumnos = $this->objAlumno->getAlumnos($nemo);
        if (is_array($dataAlumnos) && count($dataAlumnos) > 0) {
            foreach ($dataAlumnos as $row) {
                $arrData [] = array(
                    'id' => $row->ALUCOD,
                    'value' => strtoupper($row->NOMCOMP)
                );
            }
            $output = array('data' => $arrData);
        }
        echo json_encode($output);
    }

    public function plantillaboletainicial() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
        $vflgGen = $this->input->post("flgGenerar");
// ==============================================
        //$vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        if ($vflgGen == '0') {
            $this->pdf = new Pdf ();
        }
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            if ($vflgGen == '1') {
                $this->pdf = new Pdf ();
            }
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);

            $dataCursoOficial = $this->objSalon->getCursosAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
            $filasPintadas = 1;
            $iniFilaArea = 50;
            foreach ($dataCursoOficial as $areas) {
                // Saltando el curso Computacion (21) para Inicial de 3 años
                if (($areas->cursocod != '21' && $alumno->GRADOCOD == 3 && $this->ano == '2021') || $alumno->GRADOCOD == 3 || $alumno->GRADOCOD == 4 || $alumno->GRADOCOD == 5) {
                    $totalm = ((/*$alumno->GRADOCOD == 4 &&*/ $this->ano == '2022') ? 20 : 28);
                    if ($filasPintadas > $totalm) {
                        $this->pdf->AddPage('P', 'A4');
                        $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                        $iniFilaArea = 20;
                        $filasPintadas = 1;
                    }
                    $this->pdf->SetFont('Arial', 'B', 10);
                    $this->pdf->SetFillColor(25, 21, 81);
                    $this->pdf->SetTextColor(255, 255, 255);
                    $this->pdf->SetXY(20, $iniFilaArea);
                    $this->pdf->Cell(160, 10, utf8_decode("AREA"), 'LT', 0, 'C', TRUE);
                    $this->pdf->SetXY(180, $iniFilaArea);
                    $this->pdf->Cell(15, 7.5, utf8_decode($vunidad . '°'), 'TR', 0, 'C', TRUE);
                    $this->pdf->SetXY(180, $iniFilaArea + 7.5);
                    $this->pdf->Cell(15, 7.5, 'UNIDAD', 'RB', 0, 'C', TRUE);

                    $this->pdf->SetFont('Arial', 'B', 8);
                    $this->pdf->SetFillColor(123, 237, 247);
                    $this->pdf->SetTextColor(0, 0, 0);
                    $this->pdf->SetXY(20, $iniFilaArea + 10);
                    $this->pdf->Cell(160, 5, utf8_decode($areas->cursonom), 'LRB', 0, 'C', TRUE);

                    // Pintando los indicadores con la nota
                    $dataCriterios = $this->objNota->getCriteriosxUnidad($alumno->NEMO, $areas->cursocod, $vunidad);
                    // Conducta
                    $dataNotaConducta = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
                    // Evaluacion de Padre
                    $dataNotaPadre = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
                    // Evaluacion de Alumno
                    $dataNotaEstudiante = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
                    // Notas del Alumno
                    $dataNota = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, $areas->cursocod, $vbimestre, $vunidad);

                    // $filaCriterio = $iniFilaArea + 5;
                    $iniFilaArea = $this->pdf->getY() + 5;
                    $sumNotas = 0;
                    $numIndicadores = 0;
                    for ($x = 1; $x <= 30; $x++) {
                        $this->pdf->SetFont('Arial', '', 8);
                        $this->pdf->SetFillColor(255, 255, 255);
                        $this->pdf->SetTextColor(0, 0, 0);

                        $campo = "EVAL" . $x . "DES";
                        $peso = "EVAL" . $x . "PESO";
                        if ((int) $dataCriterios[0]->$peso > 0) {
                            $this->pdf->SetXY(20, $iniFilaArea);
                            $this->pdf->Cell(160, 5, utf8_decode('- ' . $dataCriterios[0]->$campo), 'LRB', 0, 'L');

                            $campoNota = 'N1E' . $x;
                            $nota = $dataNota[0]->$campoNota;
                            $sumNotas += $this->getCuantativo($nota);
                            $numIndicadores ++;
                            if ($nota == 'A' || $nota == 'AD' || $nota == 'B')
                                $this->pdf->SetTextColor(0, 0, 204);
                            else
                                $this->pdf->SetTextColor(255, 0, 51);
                            $this->pdf->SetXY(180, $iniFilaArea);
                            $this->pdf->Cell(15, 5, $nota, 'RLBT', 0, 'C', TRUE);

                            $iniFilaArea += 5;
                            $filasPintadas++;
                        }
                    }
                    // Bloque Promedio
                    $this->pdf->SetFont('Arial', 'B', 8);
                    $this->pdf->SetFillColor(123, 237, 247);
                    $this->pdf->SetTextColor(0, 0, 0);
                    $this->pdf->SetXY(20, $iniFilaArea);
                    $this->pdf->Cell(160, 5, utf8_decode('PROMEDIO FINAL'), 'LRB', 0, 'C', TRUE);

                    $prom = round($sumNotas / $numIndicadores);
                    $prom = $this->getCualitativoInicial($prom); // (($this->ano == "2021") ? $this->getCualitativoInicial($prom) : $dataNota[0]->PB);
                    if ($nota == 'A' || $nota == 'AD' || $nota == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(180, $iniFilaArea);
                    $this->pdf->Cell(15, 5, $prom, 'RLB', 0, 'C', TRUE);
                    $filasPintadas++;
                    $iniFilaArea += 10;
                }
            }
            // Bloque Conducta            
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetFillColor(123, 237, 247);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetXY(20, $iniFilaArea);
            $this->pdf->Cell(160, 5, utf8_decode('COMPORTAMIENTO'), 'LTRB', 0, 'C', TRUE);
            $filasPintadas++;
            $nota = $dataNotaConducta[0]->pb;
            if ($nota == 'A' || $nota == 'AD' || $nota == 'B')
                $this->pdf->SetTextColor(0, 0, 204);
            else
                $this->pdf->SetTextColor(255, 0, 51);
            $this->pdf->SetXY(180, $iniFilaArea);
            $this->pdf->Cell(15, 5, $nota, 'RLTB', 0, 'C', TRUE);

            $this->pdf->AddPage('P', 'A4');
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
            $iniFilaArea = 20;

            // Bloque Evaluacion de Alumnos 
            // Bloque Valores de notas
            $iniFilaArea = 20;
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(25, 21, 81);
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->SetXY(30, $iniFilaArea);
            $this->pdf->Cell(75, 10, utf8_decode("ESCALA DE APRENDIZAJE"), 'LT', 0, 'C', TRUE);

            $iniFilaArea += 10;

            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetTextColor(0, 0, 0);
            if ($this->ano == "2021") {
                $arrayTextoLetra = array(
                    0 => 'A',
                    1 => 'B',
                    2 => 'C'
                );

                $arrayTexto = array(
                    0 => 'Cuando logró alcanzar un aprendizaje',
                    1 => 'Cuando estoy en proceso de aprendizaje',
                    2 => 'Cuando estoy en el inicio de un aprendizaje'
                );
                $iniX = 3;
            } else {
                $arrayTextoLetra = array(
                    0 => 'AD',
                    1 => 'A',
                    2 => 'B',
                    3 => 'C'
                );

                $arrayTexto = array(
                    0 => 'Excelente en su aprendizaje',
                    1 => 'Logró alcanzar el aprendizaje',
                    2 => 'Está en proceso de aprendizaje',
                    3 => 'Está en inicio de aprendizaje'
                );
                $iniX = 4;
            }
            for ($x = 1; $x <= $iniX; $x++) {
                $this->pdf->SetFont('Arial', 'B', 10);
                $this->pdf->SetXY(30, $iniFilaArea);
                $this->pdf->Cell(15, 5, utf8_decode($arrayTextoLetra[$x - 1]), 'LBT', 0, 'C', TRUE);
                $this->pdf->SetFont('Arial', '', 8);
                $this->pdf->SetXY(45, $iniFilaArea);
                $this->pdf->Cell(60, 5, utf8_decode($arrayTexto[$x - 1]), 'LTRB', 0, 'L', TRUE);
                $iniFilaArea += 5;
            }

            $iniFilaArea = 20;
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 140, $iniFilaArea, 50, 35, 'JPG', '');
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetXY(70, $iniFilaArea + 45);
            $this->pdf->Cell(75, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'C');

            // se comenta este bloque solo por la 1 unidad 
            /*
              $iniFilaArea += 10;
              $this->pdf->SetFont('Arial', 'B', 9);
              $this->pdf->SetFillColor(25, 21, 81);
              $this->pdf->SetTextColor(255, 255, 255);
              $this->pdf->SetXY(20, $iniFilaArea);
              $this->pdf->Cell(80, 10, utf8_decode("DESEMPEÑO DEL ESTUDIANTE"), 'LT', 0, 'C', TRUE);
              $this->pdf->SetXY(100, $iniFilaArea);
              $this->pdf->Cell(15, 5, utf8_decode($vunidad . '°'), 'TR', 0, 'C', TRUE);
              $this->pdf->SetXY(100, $iniFilaArea + 5);
              $this->pdf->Cell(15, 5, 'UNIDAD', 'RB', 0, 'C', TRUE);

              $iniFilaArea += 10;
              $this->pdf->SetFont('Arial', '', 8);
              $this->pdf->SetFillColor(255, 255, 255);
              if ($this->ano == "2021") {
              if ($vunidad >= 7) {
              $arrayTextoAlumno = array(
              0 => 'Es responsable en sus actividades diarias',
              1 => 'Colabora y participaen las actividades matinales',
              2 => 'Es ordenado y limpio en sus hojas de aplicación',
              3 => 'Cumple con las tareas diarias'
              );
              } else {
              $arrayTextoAlumno = array(
              0 => 'Es responsable en sus actividades diarias',
              1 => 'Colabora y participaen las actividades matinales',
              2 => 'Es ordenado y limpio en sus hojas de aplicación',
              3 => 'Cumple con las actividades diarias'
              );
              }
              $numIndAumno = 4;
              } else { // 2022
              $arrayTextoAlumno = array(
              0 => 'Es responsable en sus actividades diarias.',
              1 => 'Colabora y participa en las actividades matinales.',
              2 => 'Es ordenado y limpio en sus hojas de aplicación.',
              );
              $numIndAumno = 3;
              }
              for ($x = 1; $x <= $numIndAumno; $x++) {
              $campo = 'N' . $x;
              $this->pdf->SetTextColor(0, 0, 0);
              $this->pdf->SetXY(20, $iniFilaArea);
              $this->pdf->Cell(80, 5, utf8_decode($arrayTextoAlumno[$x - 1]), 'LTRB', 0, 'L', TRUE);

              $nota = $dataNotaEstudiante[0]->$campo;
              if ($nota == 'A' || $nota == 'B')
              $this->pdf->SetTextColor(0, 0, 204);
              else
              $this->pdf->SetTextColor(255, 0, 51);
              $this->pdf->SetXY(100, $iniFilaArea);
              $this->pdf->Cell(15, 5, $nota, 'RLTB', 0, 'C', TRUE);
              $iniFilaArea += 5;
              }

              // Bloque Valores de notas
              $iniFilaArea -= 30;
              $this->pdf->SetFont('Arial', 'B', 9);
              $this->pdf->SetFillColor(25, 21, 81);
              $this->pdf->SetTextColor(255, 255, 255);
              $this->pdf->SetXY(120, $iniFilaArea);
              $this->pdf->Cell(75, 10, utf8_decode("ESCALA DE APRENDIZAJE"), 'LT', 0, 'C', TRUE);

              $iniFilaArea += 10;

              $this->pdf->SetFillColor(255, 255, 255);
              $this->pdf->SetTextColor(0, 0, 0);
              if ($this->ano == "2021") {
              $arrayTextoLetra = array(
              0 => 'A',
              1 => 'B',
              2 => 'C'
              );

              $arrayTexto = array(
              0 => 'Cuando logró alcanzar un aprendizaje',
              1 => 'Cuando estoy en proceso de aprendizaje',
              2 => 'Cuando estoy en el inicio de un aprendizaje'
              );
              $iniX = 3;
              } else {
              $arrayTextoLetra = array(
              0 => 'AD',
              1 => 'A',
              2 => 'B',
              3 => 'C'
              );

              $arrayTexto = array(
              0 => 'Excelente en su aprendizaje',
              1 => 'Logró alcanzar el aprendizaje',
              2 => 'Está en proceso de aprendizaje',
              3 => 'Está en inicio de aprendizaje'
              );
              $iniX = 4;
              }
              for ($x = 1; $x <= $iniX; $x++) {
              $this->pdf->SetFont('Arial', 'B', 10);
              $this->pdf->SetXY(120, $iniFilaArea);
              $this->pdf->Cell(15, 5, utf8_decode($arrayTextoLetra[$x - 1]), 'LBT', 0, 'C', TRUE);
              $this->pdf->SetFont('Arial', '', 8);
              $this->pdf->SetXY(135, $iniFilaArea);
              $this->pdf->Cell(60, 5, utf8_decode($arrayTexto[$x - 1]), 'LTRB', 0, 'L', TRUE);
              $iniFilaArea += 5;
              }

              // Bloque Evaluacion de Padres
              $iniFilaArea += 10;
              $this->pdf->SetFont('Arial', 'B', 9);
              $this->pdf->SetFillColor(25, 21, 81);
              $this->pdf->SetTextColor(255, 255, 255);
              $this->pdf->SetXY(20, $iniFilaArea);
              $this->pdf->Cell(80, 10, utf8_decode("DESEMPEÑO DEL PADRE DE FAMILIA"), 'LT', 0, 'C', TRUE);
              $this->pdf->SetXY(100, $iniFilaArea);
              $this->pdf->Cell(15, 5, utf8_decode($vunidad . '°'), 'TR', 0, 'C', TRUE);
              $this->pdf->SetXY(100, $iniFilaArea + 5);
              $this->pdf->Cell(15, 5, 'UNIDAD', 'RB', 0, 'C', TRUE);

              $iniFilaArea += 10;
              $this->pdf->SetFont('Arial', '', 8);
              $this->pdf->SetFillColor(255, 255, 255);
              if ($this->ano == "2021") {
              if ($vunidad >= 7) {
              $arrayTextoPadre = array(
              0 => 'Cumple con la presentación de cuadernos',
              1 => 'Propicia un ambiente adecuado para recibir las clases.',
              2 => 'Hace ingresar puntualmente a su niño(a) a la clase zoom.',
              3 => 'Mantiene una relación cordial con la profesora. ',
              4 => 'Se identifica con el colegio usando su polo Marianista.',
              5 => 'Apoya en las tareas académicas.'
              );
              $totalInd = 6;
              } else {
              $arrayTextoPadre = array(
              0 => 'Apoya en las tareas académicas',
              1 => 'Propicia un ambiente adecuado para recibir las clases',
              2 => 'Hace ingresar puntualmente a su niño(a) a la clase zoom',
              3 => 'Mantiene una relación cordial con la profesora ',
              4 => 'Se identifica con el colegio usando su polo Marianista'
              );
              $totalInd = 5;
              }
              } else {
              $arrayTextoPadre = array(
              0 => 'Envía a su hijo puntualmente al colegio',
              1 => 'Recoge a su hijo  puntualmente al colegio,',
              2 => 'Se esmera en la presentación personal e higiene de su hijo(a)',
              3 => 'Cumple con la presentación de cuadernos. ',
              4 => 'Asiste puntualmente a reuniones del aula. ',
              5 => 'Revisa y firma diariamente la agenda marianista. ',
              6 => 'Se interesa por el rendimiento académico y conductual de su hijo(a) '
              );
              $totalInd = 7;
              }
              for ($x = 1; $x <= $totalInd; $x++) {
              $campo = 'N' . $x;
              $this->pdf->SetTextColor(0, 0, 0);
              $this->pdf->SetXY(20, $iniFilaArea);
              $this->pdf->Cell(80, 5, utf8_decode($arrayTextoPadre[$x - 1]), 'LTRB', 0, 'L', TRUE);

              $nota = $dataNotaPadre[0]->$campo;
              if ($nota == 'A' || $nota == 'B')
              $this->pdf->SetTextColor(0, 0, 204);
              else
              $this->pdf->SetTextColor(255, 0, 51);
              $this->pdf->SetXY(100, $iniFilaArea);
              $this->pdf->Cell(15, 5, $nota, 'RLTB', 0, 'C', TRUE);
              $iniFilaArea += 5;
              }

              $iniFilaArea -= 40;
              $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 140, $iniFilaArea, 50, 35, 'JPG', '');
              $this->pdf->SetFont('Arial', '', 7);
              $this->pdf->SetTextColor(0, 0, 0);
              $this->pdf->SetXY(120, $iniFilaArea + 35);
              $this->pdf->Cell(75, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'C');
             */



            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                //$pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        }

        if ($vflgGen == 0) {
            $this->pdf->Output('Boletas_Inicial_.pdf', 'I');
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboletainicial3() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
// ==============================================
        $vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            $this->pdf = new Pdf ();
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);
// =============== Obteniendo Notas por Cursos =================================
            $dataNota1 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '06', $vbimestre);
            $dataNota2 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '28', $vbimestre);
            $dataNota3 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '01', $vbimestre);
            $dataNota4 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '27', $vbimestre);
            $dataNota5 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '14', $vbimestre);
            $dataNota6 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '29', $vbimestre);
            $dataNota7 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '10', $vbimestre);
// Conducta
            $dataNota8 = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
// Evaluacion de Padre
            $dataNota9 = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
// Evaluacion de Alumno
            $dataNota10 = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);

# ================================ COMUNICACION ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/comunicacion3.jpg", 18, 53, 180, 84, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 70;
                $sumNotas = 0;
                $numIndicadores = 11;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 5)
                        $iniY += 10;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/comunicacion.jpg", 18, 53, 180, 95, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 67;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 5);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/comunicacion.jpg", 18, 53, 180, 80, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 68;
                $sumNotas = 0;
                $numIndicadores = 12;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/comunicacion.jpg", 18, 53, 180, 90, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 68;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }

# ================================ PERSONAL SOCIAL  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/personal3.jpg", 18, $iniY + 23, 180, 110, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 38;
                $sumNotas = 0;
                $numIndicadores = 16;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4)
                        $iniY += 10.5;
                    elseif ($x > 9)
                        $iniY += 5.5;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/personal.jpg", 18, $iniY + 23, 180, 110, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 38;
                $sumNotas = 0;
                $numIndicadores = 16;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 8 || $x == 14)
                        $iniY += 9;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/personal.jpg", 18, $iniY + 23, 180, 100, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 38;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 10.5;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 3);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/personalsocial.jpg", 18, $iniY + 23, 180, 85, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 42;
                $sumNotas = 0;
                $numIndicadores = 10;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4 || $x == 5)
                        $iniY += 10;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            $this->pdf->SetTextColor(0, 0, 0);

# ================================ MATEMATICA  ============================================
            $this->pdf->AddPage('P', 'A4');
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/matematica3.jpg", 18, 25, 180, 110, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 46.5;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 3)
                        $iniY += 9.5;
                    elseif ($x == 7)
                        $iniY += 9;
                    elseif ($x == 11)
                        $iniY += 9;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/matematicas.jpg", 18, 25, 180, 150, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 45.5;
                $sumNotas = 0;
                $numIndicadores = 20;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4)
                        $iniY += 10.5;
                    elseif ($x == 6)
                        $iniY += 10;
                    elseif ($x == 10 || $x == 14)
                        $iniY += 10.5;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/matematica.jpg", 18, 25, 180, 92, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 45.5;
                $sumNotas = 0;
                $numIndicadores = 12;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 10.5;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/matematicas.jpg", 18, 25, 180, 92, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 45.5;
                $sumNotas = 0;
                $numIndicadores = 10;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 3 || $x == 7)
                        $iniY += 10;
                    elseif ($x == 9)
                        $iniY += 9;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ CIENCIA Y AMBIENTE  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/ciencia3.jpg", 18, $iniY + 10, 180, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 24.5;
                $sumNotas = 0;
                $numIndicadores = 6;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/cienciayambiente.jpg", 18, $iniY + 10, 180, 110, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 24.5;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1 || $x == 6)
                        $iniY += 9;
                    elseif ($x == 9 || $x == 11)
                        $iniY += 10;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/ciencia.jpg", 18, $iniY + 10, 180, 90, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 33;
                $sumNotas = 0;
                $numIndicadores = 11;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 5 || $x == 6 || $x == 7 /* || $x == 8 */)
                        $iniY += 6;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/ciencias.jpg", 18, $iniY + 10, 180, 95, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 25;
                $sumNotas = 0;
                $numIndicadores = 12;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1 || $x == 5 || $x == 8)
                        $iniY += 9;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            $this->pdf->SetTextColor(0, 0, 0);
# ================================ RELIGION  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/religion3.jpg", 18, $iniY + 15, 180, 30, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 29;
                $sumNotas = 0;
                $numIndicadores = 2;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/religion.jpg", 18, 25, 180, 82, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 41;
                $sumNotas = 0;
                $numIndicadores = 11;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/religion.jpg", 18, 25, 180, 50, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 41;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/religion.jpg", 18, 25, 180, 100, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 41.5;
                $sumNotas = 0;
                $numIndicadores = 12;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4 || $x == 5)
                        $iniY += 10.5;
                    else
                        $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ PSICOMOTROCIDAD  ============================================     
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/psicomotrocidad3.jpg", 18, $iniY + 10, 180, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23;
                $sumNotas = 0;
                $numIndicadores = 3;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/psicomotrocidad.jpg", 18, $iniY + 10, 180, 88, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 22;
                $sumNotas = 0;
                $numIndicadores = 13;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/psicomotricidad.jpg", 18, $iniY + 10, 180, 65, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/psicomotricidad.jpg", 18, $iniY + 10, 180, 60, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23;
                $sumNotas = 0;
                $numIndicadores = 7;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 6;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            $this->pdf->SetTextColor(0, 0, 0);
# ================================ INGLES  ============================================
            $this->pdf->AddPage('P', 'A4');
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/ingles3.jpg", 18, 25, 180, 170, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 46;
                $sumNotas = 0;
                $numIndicadores = 20;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4) {
                        $iniY += 11.5;
                    } elseif ($x == 7 || $x == 9 || $x == 12 || $x == 14 || $x == 17) {
                        $iniY += 12;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/ingles.jpg", 18, 25, 180, 190, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 45;
                $sumNotas = 0;
                $numIndicadores = 26;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4) {
                        $iniY += 11;
                    } elseif ($x == 8) {
                        $iniY += 11;
                    } elseif ($x == 12 || $x == 15 || $x == 17) {
                        $iniY += 10;
                    } elseif ($x == 20) {
                        $iniY += 12;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/ingles.jpg", 18, 25, 180, 100, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 39.5;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
                $iniY += 2;
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/3/ingles.jpg", 18, 25, 180, 140, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 39;
                $sumNotas = 0;
                $numIndicadores = 20;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 10;
                    elseif ($x == 7 || $x == 10)
                        $iniY += 8.5;
                    else
                        $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
                $iniY += 2;
            }

// Comportamiento
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/comportamiento3.jpg", 18, $iniY + 8, 180, 8, 'JPG', '');
            $this->pdf->SetFont('Arial', '', 9);

            $campo = 'pb';
            $n_comu = $dataNota8[0]->$campo;
            if ($n_comu == 'A' || $n_comu == 'B')
                $this->pdf->SetTextColor(0, 0, 204);
            else
                $this->pdf->SetTextColor(255, 0, 51);
            $this->pdf->SetXY(183, $iniY + 10);
            $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

            $this->pdf->SetTextColor(0, 0, 0);
            if ($vbimestre == '1') {

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/estudiante.jpg", 18, $iniY + 18, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = $iniY + 30;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, $iniY + 18, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/padre.jpg", 18, $iniY + 59, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 10;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 140, $iniY + 59, 40, 25, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniY + 99);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            } elseif ($vbimestre == '2') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/estudiante.jpg", 18, 25, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 37;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, 25, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/padre.jpg", 18, $iniYE + 15, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 25;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 130, $iniYE + 15, 50, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniYE + 56);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            } elseif ($vbimestre == '3') {

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/desempeno_estudiante.jpg", 18, $iniY + 25, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = $iniY + 37;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, $iniYE - 40, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/3/desempeno_padre.jpg", 18, $iniYE + 5, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 15;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 130, $iniYE + 10, 50, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniYE + 45);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            } elseif ($vbimestre == '4') {

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/desempenioestudiante.jpg", 18, $iniY + 25, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = $iniY + 37;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, $iniYE - 40, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/desempeniopadre.jpg", 18, $iniYE + 5, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 15;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 130, $iniYE + 10, 50, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniYE + 45);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            }

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        }
        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
//$file_contents = $this->pdf->Output('Reporte_boletas.pdf','S');	
//echo $file_contents;
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboletainicial3unidad() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
// ==============================================
        $vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            $this->pdf = new Pdf ();
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);
// =============== Obteniendo Notas por Cursos =================================
            $dataNota1 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '06', $vbimestre, $vunidad); // comunicacion
            $dataNota2 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '28', $vbimestre, $vunidad); // personal social
            $dataNota3 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '01', $vbimestre, $vunidad); // matematicas
            $dataNota4 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '27', $vbimestre, $vunidad); // ciencia y ambiente
            $dataNota5 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '14', $vbimestre, $vunidad); // religion
            $dataNota6 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '29', $vbimestre, $vunidad); // psicomotricidad
            $dataNota7 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '10', $vbimestre, $vunidad); // ingles
// Conducta
            $dataNota8 = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
// Evaluacion de Padre
            $dataNota9 = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
// Evaluacion de Alumno
            $dataNota10 = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);

            // ================================ COMUNICACION ============================================
            if ($vunidad == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/comunicacion.jpg", 18, 53, 180, 65, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 67;
                $sumNotas = 0;
                $numIndicadores = 9;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
                //============================================= PERSONAL SOCIAL ===========================
                $getY = $this->pdf->getY() + 10;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/personal_social.jpg", 18, $getY, 180, 55, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 14;
                $sumNotas = 0;
                $numIndicadores = 7;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= MATEMATICAS ===========================

                $getY = $this->pdf->getY() + 10;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/matematicas.jpg", 18, $getY, 180, 70, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 19;
                $sumNotas = 0;
                $numIndicadores = 7;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2 || $x == 5) {
                        $iniY += 10;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

                //============================================= CIENCIA Y AMBIENTE ===========================
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/ciencia_ambiente.jpg", 18, 25, 180, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 42;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= RELIGION ===========================
                $getY = $this->pdf->getY() + 10;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/religion.jpg", 18, $getY, 180, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 17;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= PSICOMOTRICIDAD ===========================
                $getY = $this->pdf->getY() + 10;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/psicomotricidad.jpg", 18, $getY, 180, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 13;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                //============================================= INGLES ===========================
                $getY = $this->pdf->getY() + 10;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/ingles.jpg", 18, $getY, 180, 120, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 18;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 4 || $x == 7 || $x == 9 || $x == 11 || $x == 12) {
                        $iniY += 10;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');


                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/comportamiento3.jpg", 18, 20, 180, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);

                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, 22);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/desempenio_estudiante.jpg", 18, 35, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 46;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, $iniYE - 39, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/desempenio_padre.jpg", 18, $iniYE + 5, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 15;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 130, $iniYE + 10, 50, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniYE + 45);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            }

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                //$pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        } // Fin de FOR de Alumnos

        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboletainicial4unidad() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
// ==============================================
        $vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            $this->pdf = new Pdf ();
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);
// =============== Obteniendo Notas por Cursos =================================
            $dataNota1 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '06', $vbimestre, $vunidad); // comunicacion
            $dataNota2 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '28', $vbimestre, $vunidad); // personal social
            $dataNota3 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '01', $vbimestre, $vunidad); // matematicas
            $dataNota4 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '27', $vbimestre, $vunidad); // ciencia y ambiente
            $dataNota5 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '14', $vbimestre, $vunidad); // religion
            $dataNota6 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '29', $vbimestre, $vunidad); // psicomotricidad
            $dataNota7 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '10', $vbimestre, $vunidad); // ingles
            $dataNota11 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '21', $vbimestre, $vunidad); // computacion
// Conducta
            $dataNota8 = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
// Evaluacion de Padre
            $dataNota9 = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
// Evaluacion de Alumno
            $dataNota10 = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);

            // ================================ COMUNICACION ============================================
            if ($vunidad == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/comunicacion.jpg", 18, 53, 180, 88, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 68;
                $sumNotas = 0;
                $numIndicadores = 11;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2 || $x == 4 || $x == 6) {
                        $iniY += 9;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
                //============================================= PERSONAL SOCIAL ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/personal_social.jpg", 18, $getY, 180, 60, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 14;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= MATEMATICAS ===========================

                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/matematicas.jpg", 18, $getY, 180, 86, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 14;
                $sumNotas = 0;
                $numIndicadores = 12;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2 || $x == 5 || $x == 9) {
                        $iniY += 8.5;
                    } else {
                        $iniY += 4.5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

                //============================================= CIENCIA Y AMBIENTE ===========================
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/ciencia_ambiente.jpg", 18, 25, 180, 55, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 41;
                $sumNotas = 0;
                $numIndicadores = 6;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= RELIGION ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/religion.jpg", 18, $getY, 180, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 14;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= PSICOMOTRICIDAD ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/psicomotricidad.jpg", 18, $getY, 180, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 13;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                //============================================= INGLES ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/ingles.jpg", 18, $getY, 180, 132, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 18;
                $sumNotas = 0;
                $numIndicadores = 20;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 7 || $x == 10 || $x == 11 || $x == 17) {
                        $iniY += 8.6;
                    } else {
                        $iniY += 4.5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

                //============================================= COMPUTACION ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/4/I/computacion.jpg", 18, 20, 180, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 32;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota11[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/comportamiento3.jpg", 18, $iniYE + 10, 180, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 11;
                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/desempenio_estudiante.jpg", 18, $iniYE + 10, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 21;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, $iniYE - 38, 80, 38, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/desempenio_padre.jpg", 18, $iniYE + 5, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 16;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 130, $iniYE + 10, 50, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniYE + 45);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            }

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                //$pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        } // Fin de FOR de Alumnos

        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboletainicial5unidad() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
// ==============================================
        $vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            $this->pdf = new Pdf ();
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);
// =============== Obteniendo Notas por Cursos =================================
            $dataNota1 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '06', $vbimestre, $vunidad); // comunicacion
            $dataNota2 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '28', $vbimestre, $vunidad); // personal social
            $dataNota3 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '01', $vbimestre, $vunidad); // matematicas
            $dataNota4 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '27', $vbimestre, $vunidad); // ciencia y ambiente
            $dataNota5 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '14', $vbimestre, $vunidad); // religion
            $dataNota6 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '29', $vbimestre, $vunidad); // psicomotricidad
            $dataNota7 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '10', $vbimestre, $vunidad); // ingles
            $dataNota11 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '21', $vbimestre, $vunidad); // computacion
// Conducta
            $dataNota8 = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
// Evaluacion de Padre
            $dataNota9 = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);
// Evaluacion de Alumno
            $dataNota10 = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre, $vunidad);

            // ================================ COMUNICACION ============================================
            if ($vunidad == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/comunicacion.jpg", 18, 53, 180, 66, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 68;
                $sumNotas = 0;
                $numIndicadores = 9;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
                //============================================= PERSONAL SOCIAL ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/personal_social.jpg", 18, $getY, 180, 55, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 13;
                $sumNotas = 0;
                $numIndicadores = 6;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 10;
                    else
                        $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= MATEMATICAS ===========================

                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/matematicas.jpg", 18, $getY, 180, 118, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 13;
                $sumNotas = 0;
                $numIndicadores = 17;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 6 || $x == 9 || $x == 10 || $x == 14) {
                        $iniY += 10;
                    } else {
                        $iniY += 4.5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

                //============================================= CIENCIA Y AMBIENTE ===========================
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/ciencia_ambiente.jpg", 18, 20, 180, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 33;
                $sumNotas = 0;
                $numIndicadores = 6;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= RELIGION ===========================
                $getY = $this->pdf->getY() + 6;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/religion.jpg", 18, $getY, 180, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 14;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                //============================================= PSICOMOTRICIDAD ===========================
                $getY = $this->pdf->getY() + 6;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/psicomotricidad.jpg", 18, $getY, 180, 42, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 11;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                //============================================= INGLES ===========================
                $getY = $this->pdf->getY() + 6;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/ingles.jpg", 18, $getY, 180, 139, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = $getY + 16;
                $sumNotas = 0;
                $numIndicadores = 21;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 7 || $x == 8) {
                        $iniY += 9.5;
                    } elseif ($x == 17) {
                        $iniY += 9;
                    } else {
                        if ($x >= 17)
                            $iniY += 4.5;
                        else
                            $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');


                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');

                //============================================= COMPUTACION ===========================
                $getY = $this->pdf->getY() + 7;
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/5/I/computacion.jpg", 18, 20, 180, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 32;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota11[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/comportamiento3.jpg", 18, $iniYE + 10, 180, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 11;
                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);

// Evaluacion del Estudiante
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/desempenio_estudiante.jpg", 18, $iniYE + 10, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 21;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, $iniYE - 38, 80, 38, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2021/3/I/desempenio_padre.jpg", 18, $iniYE + 5, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 16;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 130, $iniYE + 10, 50, 35, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(125, $iniYE + 45);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            }

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                //$pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        } // Fin de FOR de Alumnos

        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboletainicial4() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
// ==============================================
        $vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
//if ($vflgGen == 0) {
//}

        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            $this->pdf = new Pdf ();
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);
// =============== Obteniendo Notas por Cursos =================================
            $dataNota1 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '06', $vbimestre);
            $dataNota2 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '28', $vbimestre);
            $dataNota3 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '01', $vbimestre);
            $dataNota4 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '27', $vbimestre);
            $dataNota5 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '14', $vbimestre);
            $dataNota6 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '29', $vbimestre);
            $dataNota7 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '10', $vbimestre);
// Conducta
            $dataNota8 = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
// Evaluacion de Padre
            $dataNota9 = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
// Evaluacion de Alumno
            $dataNota10 = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
# ================================ COMUNICACION ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comunicacion.jpg", 18, 53, 180, 84, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 70;
                $sumNotas = 0;
                $numIndicadores = 11;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2) {
                        $iniY += 5;
                    } elseif ($x >= 10) {
                        $iniY += 6.5;
                    } elseif ($x > 5) {
                        $iniY += 5;
                    } else {
                        $iniY += 6;
                    }
                }
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/comunicacion.jpg", 18, 53, 180, 105, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 70;
                $sumNotas = 0;
                $numIndicadores = 15;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    $iniY += 5.5;
                }
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/comunicacion.jpg", 18, 53, 180, 83, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 70;
                $sumNotas = 0;
                $numIndicadores = 11;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    $iniY += 5.5;
                }
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/comunicacion.jpg", 18, 53, 180, 115, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 69;
                $sumNotas = 0;
                $numIndicadores = 16;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1 || $x == 4 || $x == 11)
                        $iniY += 9.5;
                    else
                        $iniY += 5;
                }

                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ PERSONAL SOCIAL  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/personal.jpg", 18, $iniY + 23, 180, 78, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 40;
                $sumNotas = 0;
                $numIndicadores = 9;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2)
                        $iniY += 7;
                    elseif ($x > 2)
                        $iniY += 6;
                    else
                        $iniY += 5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/personal.jpg", 18, $iniY + 23, 184, 78, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 36.5;
                $sumNotas = 0;
                $numIndicadores = 10;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    $iniY += 6;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY - 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/personal.jpg", 18, $iniY + 23, 180, 100, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 44.5;
                $sumNotas = 0;
                $numIndicadores = 13;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/personalsocial.jpg", 18, $iniY + 23, 180, 100, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 40;
                $sumNotas = 0;
                $numIndicadores = 13;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 8)
                        $iniY += 10;
                    else
                        $iniY += 5.5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            $this->pdf->SetTextColor(0, 0, 0);
# ================================ MATEMATICA  ============================================
            $this->pdf->AddPage('P', 'A4');
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
            if ($vbimestre == '1') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/matematicas.jpg", 18, 25, 180, 165, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 42.5;
                $sumNotas = 0;
                $numIndicadores = 19;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2)
                        $iniY += 11;
                    elseif ($x == 6 || $x == 9 || $x == 11)
                        $iniY += 12;
                    elseif ($x == 13 || $x == 16 || $x == 18)
                        $iniY += 11;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/matematicas.jpg", 18, 25, 180, 165, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 42.5;
                $sumNotas = 0;
                $numIndicadores = 20;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 5)
                        $iniY += 12.5;
                    elseif ($x == 8)
                        $iniY += 11;
                    elseif ($x == 13)
                        $iniY += 12;
                    elseif ($x == 16)
                        $iniY += 11;
                    else
                        $iniY += 6;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/matematica.jpg", 18, 25, 180, 135, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 42.5;
                $sumNotas = 0;
                $numIndicadores = 17;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 5)
                        $iniY += 12.5;
                    elseif ($x == 7)
                        $iniY += 11;
                    elseif ($x == 9 || $x == 13)
                        $iniY += 5.5;
                    else
                        $iniY += 6;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY - 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/matematicas.jpg", 18, 25, 180, 160, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 42.5;
                $sumNotas = 0;
                $numIndicadores = 25;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    $iniY += 5.5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY - 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ CIENCIA Y AMBIENTE  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/ciencia.jpg", 18, $iniY + 10, 180, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 24.5;
                $sumNotas = 0;
                $numIndicadores = 6;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/ciencia.jpg", 18, $iniY + 5, 180, 100, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 19;
                $sumNotas = 0;
                $numIndicadores = 12;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 10;
                    elseif ($x == 4 || $x == 8)
                        $iniY += 12;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/ciencia.jpg", 18, $iniY + 5, 180, 108, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 27;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/ciencias.jpg", 18, $iniY + 5, 180, 108, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 27;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ RELIGION  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/religion3.jpg", 18, $iniY + 15, 180, 30, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 29;
                $sumNotas = 0;
                $numIndicadores = 2;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } else {
                $this->pdf->AddPage('P', 'A4');
                if ($vbimestre == '1')
                    $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                if ($vbimestre == '2')
                    $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/religion.jpg", 18, 20, 180, 62, 'JPG', '');
                if ($vbimestre == '3')
                    $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/religion.jpg", 18, 20, 180, 66, 'JPG', '');
                if ($vbimestre == '4')
                    $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/religion.jpg", 18, 20, 180, 60, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                if ($vbimestre == '3')
                    $iniY = 36;
                elseif ($vbimestre == '4')
                    $iniY = 39;
                else
                    $iniY = 32;
                $sumNotas = 0;
                if ($vbimestre == '4') {
                    $numIndicadores = 6;
                } else {
                    $numIndicadores = 8;
                }

                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            $this->pdf->SetTextColor(0, 0, 0);
            # ================================ PSICOMOTROCIDAD  ============================================    
            if ($vbimestre == '1') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/psicomotricidad.jpg", 18, 20, 180, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 34;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/psicomotrocidad.jpg", 18, $iniY + 10, 180, 52, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 21;
                $sumNotas = 0;
                $numIndicadores = 7;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/psicomotricidad.jpg", 18, $iniY + 10, 180, 58, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23.5;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 4.7;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/psicomotricidad.jpg", 18, $iniY + 10, 180, 63, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 22;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ INGLES  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/ingles.jpg", 18, $iniY + 18, 180, 160, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 40;
                $sumNotas = 0;
                $numIndicadores = 18;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4) {
                        $iniY += 11.5;
                    } elseif ($x == 7 || $x == 16) {
                        $iniY += 12;
                    } elseif ($x == 9 || $x == 11 || $x == 13) {
                        $iniY += 11;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/4/ingles.jpg", 18, $iniY + 8, 180, 150, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23;
                $sumNotas = 0;
                $numIndicadores = 22;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4) {
                        $iniY += 9;
                    } elseif ($x == 8 || $x == 12 || $x == 13 || $x == 15 || $x == 18) {
                        $iniY += 9.5;
                    } else {
                        $iniY += 4.5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/ingles.jpg", 18, $iniY + 8, 180, 95, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 24;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 6)
                        $iniY += 6;
                    else
                        $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/4/ingles.jpg", 18, $iniY + 8, 180, 95, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 24;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 6)
                        $iniY += 6;
                    else
                        $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }

            if ($vbimestre == '1') {
// Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, $iniY + 15, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);

                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 17);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);


// Evaluacion del Estudiante            
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/estudiante3.jpg", 18, 25, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 37;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, 25, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/padre3.jpg", 18, 70, 80, 46, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 15;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 125, 75, 60, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 8);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(80, 130);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');
            } elseif ($vbimestre == '2') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, 25, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 25;
                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE + 2);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');


                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/estudiante.jpg", 18, $iniYE + 15, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 27.5;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, 40, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/padre.jpg", 18, 90, 80, 52, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 20;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYP += 8.5;
                }


                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 125, 90, 60, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 8);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(120, 135);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '3') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, 25, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 25;
                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE + 2);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');


                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/desempeno_estudiante.jpg", 18, $iniYE + 15, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 27.5;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, 40, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/4/desempeno_padre.jpg", 18, 90, 80, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 22;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 3)
                        $iniYP += 6.5;
                    else
                        $iniYP += 8.5;
                }


                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 125, 90, 60, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 8);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(120, 135);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '4') {

                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, 25, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE = 25;
                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniYE + 2);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');


                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/desempenioestudiante.jpg", 18, $iniYE + 15, 80, 40, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYE += 27.5;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota10[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYE);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniYE += 7;
                }

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, 40, 80, 40, 'JPG', '');
// Evaluacion de Padres
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/desempeniopadre.jpg", 18, 90, 80, 50, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniYP = $iniYE + 22;
                $sumNotas = 0;
                $numIndicadores = 5;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N' . $x;
                    $n_comu = $dataNota9[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(85, $iniYP);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 3)
                        $iniYP += 6.5;
                    else
                        $iniYP += 8.5;
                }


                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 125, 90, 60, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 8);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(120, 135);
                $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');

                $this->pdf->SetTextColor(0, 0, 0);
            }
            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        }
        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboletainicial5() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
// ==============================================
        $vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        foreach ($arraAlumnos as $alumno) {
# INSTANCIAMOS OBJETO FPDF
            $this->pdf = new Pdf ();
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');

# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);
// =============== Obteniendo Notas por Cursos =================================
            $dataNota1 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '06', $vbimestre);
            $dataNota2 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '28', $vbimestre);
            $dataNota3 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '01', $vbimestre);
            $dataNota4 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '27', $vbimestre);
            $dataNota5 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '14', $vbimestre);
            $dataNota6 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '29', $vbimestre);
            $dataNota7 = $this->objNota->getNotasxBimestreBoletaInicial($alumno->ALUCOD, '10', $vbimestre);
// Conducta
            $dataNota8 = $this->objNota->getConductaxBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
// Evaluacion de Padre
            $dataNota9 = $this->objNota->getEvaPadresBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
// Evaluacion de Alumno
            $dataNota10 = $this->objNota->getEvaEstudianteBimestreBoletaInicial($alumno->ALUCOD, $vbimestre);
# ================================ COMUNICACION ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/comunicacion.jpg", 18, 53, 180, 129, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 70;
                $sumNotas = 0;
                $numIndicadores = 19;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1) {
                        $iniY += 5;
                    } elseif ($x == 10 || $x == 11) {
                        $iniY += 6.5;
                    } elseif ($x == 12) {
                        $iniY += 5;
                    } elseif ($x == 13 || $x == 14) {
                        $iniY += 6;
                    } elseif ($x >= 15) {
                        $iniY += 5;
                    } elseif ($x > 5) {
                        $iniY += 5;
                    } else {
                        $iniY += 6;
                    }
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/comunicacion.jpg", 18, 53, 180, 198, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 71;
                $sumNotas = 0;
                $numIndicadores = 29;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2) {
                        $iniY += 10;
                    } elseif ($x == 9 || $x == 27) {
                        $iniY += 10.5;
                    } /* elseif ($x == 12) {
                      $iniY += 5;
                      } elseif ($x == 13 || $x == 14) {
                      $iniY += 6;
                      } elseif ($x >= 15) {
                      $iniY += 5;
                      } elseif ($x > 5) {
                      $iniY += 5;
                      } */ else {
                        $iniY += 5.5;
                    }
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/comunicacion.jpg", 18, 53, 180, 160, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 71;
                $sumNotas = 0;
                $numIndicadores = 23;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1) {
                        $iniY += 10;
                    } elseif ($x == 9 || $x == 27) {
                        $iniY += 10.5;
                    } /* elseif ($x == 12) {
                      $iniY += 5;
                      } elseif ($x == 13 || $x == 14) {
                      $iniY += 6;
                      } elseif ($x >= 15) {
                      $iniY += 5;
                      } elseif ($x > 5) {
                      $iniY += 5;
                      } */ else {
                        $iniY += 5.5;
                    }
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/comunicacion.jpg", 18, 53, 180, 145, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 70;
                $sumNotas = 0;
                $numIndicadores = 20;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota1[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1) {
                        $iniY += 10.5;
                    } elseif ($x == 7) {
                        $iniY += 9.8;
                    } elseif ($x == 17) {
                        $iniY += 16.5;
                    } /* elseif ($x == 12) {
                      $iniY += 5;
                      } elseif ($x == 13 || $x == 14) {
                      $iniY += 6;
                      } elseif ($x >= 15) {
                      $iniY += 5;
                      } elseif ($x > 5) {
                      $iniY += 5;
                      } */ else {
                        $iniY += 5;
                    }
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ PERSONAL SOCIAL  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/personal.jpg", 18, $iniY + 23, 180, 80, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 44;
                $sumNotas = 0;
                $numIndicadores = 10;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 3)
                        $iniY += 7;
                    else
                        $iniY += 5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/personal.jpg", 18, 20, 180, 95, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 36;
                $sumNotas = 0;
                $numIndicadores = 13;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2 /* || $x == 4 || $x == 6 || $x == 7 || $x == 9 || $x == 10 || $x == 11 */)
                        $iniY += 6.5;
                    else
                        $iniY += 5.5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/personal.jpg", 18, 20, 180, 95, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 40;
                $sumNotas = 0;
                $numIndicadores = 13;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2)
                        $iniY += 5.5;
                    elseif ($x == 5 || $x == 7)
                        $iniY += 5;
                    else
                        $iniY += 5.5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/personal.jpg", 18, 20, 180, 150, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 35;
                $sumNotas = 0;
                $numIndicadores = 21;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota2[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1 || $x == 4 || $x == 14)
                        $iniY += 10;
                    else
                        $iniY += 5.5;
                }

// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            $this->pdf->SetTextColor(0, 0, 0);

            # ================================ MATEMATICA  ============================================
            if ($vbimestre == '1') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/matematicas.jpg", 18, 20, 180, 175, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 34;
                $sumNotas = 0;
                $numIndicadores = 26;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 6)
                        $iniY += 10;
                    elseif ($x == 9 || $x == 12 || $x == 20 || $x == 22)
                        $iniY += 9;
                    elseif ($x == 17)
                        $iniY += 10;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/matematicas.jpg", 18, $iniY + 7, 180, 175, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 22.5;
                $sumNotas = 0;
                $numIndicadores = 26;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 10 || $x == 13 || $x == 17 || $x == 23)
                        $iniY += 10.5;
                    else
                        $iniY += 5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/matematica.jpg", 18, $iniY + 7, 180, 150, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 26.5;
                $sumNotas = 0;
                $numIndicadores = 21;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                    if ($x == 10)
                        $iniY += 11.5;
                    elseif ($x == 13 || $x == 14 || $x == 15 || $x == 16)
                        $iniY += 6;
                    else
                        $iniY += 5.5;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/matematica.jpg", 18, $iniY + 7, 180, 110, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 26.5;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota3[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 6;
                }
// PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ CIENCIA Y AMBIENTE  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/ciencia.jpg", 18, $iniY + 15, 180, 58, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 30.5;
                $sumNotas = 0;
                $numIndicadores = 7;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '2') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/cienciayambiente.jpg", 18, 20, 180, 150, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 38;
                $sumNotas = 0;
                $numIndicadores = 19;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 3 || $x == 10 || $x == 13)
                        $iniY += 10;
                    elseif ($x == 7)
                        $iniY += 5;
                    elseif ($x == 15)
                        $iniY += 4;
                    else
                        $iniY += 6;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '3') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/ciencia.jpg", 18, 20, 180, 118, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 38;
                $sumNotas = 0;
                $numIndicadores = 15;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 11;
                    elseif ($x == 7 || $x == 12)
                        $iniY += 5;
                    else
                        $iniY += 6;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '4') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/cienciayambiente.jpg", 18, 20, 180, 135, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 37;
                $sumNotas = 0;
                $numIndicadores = 17;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota4[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 1)
                        $iniY += 11.5;
                    elseif ($x == 7 || $x == 12)
                        $iniY += 11;
                    /*  elseif ($x == 7 || $x == 12)
                      $iniY += 5; */
                    else
                        $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            }
# ================================ RELIGION  ============================================
            if ($vbimestre == '1') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/religion.jpg", 18, 20, 180, 30, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 34;
                $sumNotas = 0;
                $numIndicadores = 2;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/religion.jpg", 18, $iniY + 14, 180, 70, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 31;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2 || $x == 4 || $x == 6)
                        $iniY += 6;
                    elseif ($x == 7 || $x == 8)
                        $iniY += 7;
                    else
                        $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/religion.jpg", 18, $iniY + 14, 180, 56, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 31;
                $sumNotas = 0;
                $numIndicadores = 6;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 2 || $x == 4 || $x == 6)
                        $iniY += 6;
                    elseif ($x == 7 || $x == 8)
                        $iniY += 7;
                    else
                        $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/religion.jpg", 18, $iniY + 14, 180, 65, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 29;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota5[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5.5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
            # ================================ PSICOMOTROCIDAD  ============================================        
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/psicomotricidad.jpg", 18, $iniY + 10, 180, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 24;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/psicomotrocidad.jpg", 18, $iniY + 10, 180, 40, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23;
                $sumNotas = 0;
                $numIndicadores = 4;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/psicomotrocidad.jpg", 18, $iniY + 10, 180, 59, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 23;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/psicomotricidad.jpg", 18, $iniY + 10, 180, 59, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 22;
                $sumNotas = 0;
                $numIndicadores = 8;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota6[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    $iniY += 5;
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 1);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }
# ================================ INGLES  ============================================
            if ($vbimestre == '1') {
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/5/ingles.jpg", 18, $iniY + 18, 180, 164, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY += 41;
                $sumNotas = 0;
                $numIndicadores = 19;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4) {
                        $iniY += 11.5;
                    } elseif ($x == 7) {
                        $iniY += 12;
                    } elseif ($x == 10) {
                        $iniY += 12;
                    } elseif ($x == 14 || $x == 17) {
                        $iniY += 13;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '2') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/5/ingles.jpg", 18, 20, 180, 164, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 42.5;
                $sumNotas = 0;
                $numIndicadores = 19;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    if ($x == 4) {
                        $iniY += 11.5;
                    } elseif ($x == 7) {
                        $iniY += 13;
                    } elseif ($x == 10) {
                        $iniY += 12;
                    } elseif ($x == 14 || $x == 17) {
                        $iniY += 13;
                    } else {
                        $iniY += 5;
                    }
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '3') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/3B/5/ingles.jpg", 18, 20, 180, 100, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 36;
                $sumNotas = 0;
                $numIndicadores = 15;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    /* if ($x == 4) {
                      $iniY += 11.5;
                      } elseif ($x == 7) {
                      $iniY += 13;
                      } elseif ($x == 10) {
                      $iniY += 12;
                      } elseif ($x == 14 || $x == 17) {
                      $iniY += 13;
                      } else { */
                    $iniY += 5;
                    /*  } */
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 2);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            } elseif ($vbimestre == '4') {
                $this->pdf->AddPage('P', 'A4');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4B/5/ingles.jpg", 18, 20, 180, 100, 'JPG', '');

                $this->pdf->SetFont('Arial', '', 9);
                $iniY = 36;
                $sumNotas = 0;
                $numIndicadores = 14;
                for ($x = 1; $x <= $numIndicadores; $x++) {
                    $campo = 'N1E' . $x;
                    $n_comu = $dataNota7[0]->$campo;
                    $sumNotas += $this->getCuantativo($n_comu);
                    if ($n_comu == 'A' || $n_comu == 'B')
                        $this->pdf->SetTextColor(0, 0, 204);
                    else
                        $this->pdf->SetTextColor(255, 0, 51);
                    $this->pdf->SetXY(183, $iniY);
                    $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                    /* if ($x == 4) {
                      $iniY += 11.5;
                      } elseif ($x == 7) {
                      $iniY += 13;
                      } elseif ($x == 10) {
                      $iniY += 12;
                      } elseif ($x == 14 || $x == 17) {
                      $iniY += 13;
                      } else { */
                    $iniY += 5.5;
                    /*  } */
                }
                // PROMEDIOS
                $prom = round($sumNotas / $numIndicadores);
                $prom = $this->getCualitativoInicial($prom);
                if ($prom == 'A' || $prom == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY);
                $this->pdf->Cell(10, 5, $prom, 0, 0, 'C');
            }

            if ($vbimestre == '1') {
// Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, $iniY + 15, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);

                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 17);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '2') {
                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, $iniY + 15, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);

                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 17);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '3') {
                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, $iniY + 15, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);

                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 17);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            } elseif ($vbimestre == '4') {
                // Comportamiento
                $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/4/comportamiento.jpg", 18, $iniY + 15, 179, 8, 'JPG', '');
                $this->pdf->SetFont('Arial', '', 9);

                $campo = 'pb';
                $n_comu = $dataNota8[0]->$campo;
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(183, $iniY + 17);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');

                $this->pdf->SetTextColor(0, 0, 0);
            }
// Evaluacion del Estudiante            
            $this->pdf->AddPage('P', 'A4');
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/estudiante.jpg", 18, 25, 80, 40, 'JPG', '');
            $this->pdf->SetFont('Arial', '', 9);
            $iniYE = 37;
            $sumNotas = 0;
            $numIndicadores = 4;
            for ($x = 1; $x <= $numIndicadores; $x++) {
                $campo = 'N' . $x;
                $n_comu = $dataNota10[0]->$campo;
                $sumNotas += $this->getCuantativo($n_comu);
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(85, $iniYE);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                $iniYE += 7;
            }

            $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/escala3.jpg", 117, 25, 80, 40, 'JPG', '');
// Evaluacion de Padres
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/2B/3/padre.jpg", 18, 70, 80, 46, 'JPG', '');
            $this->pdf->SetFont('Arial', '', 9);
            $iniYP = $iniYE + 15;
            $sumNotas = 0;
            $numIndicadores = 5;
            for ($x = 1; $x <= $numIndicadores; $x++) {
                $campo = 'N' . $x;
                $n_comu = $dataNota9[0]->$campo;
                $sumNotas += $this->getCuantativo($n_comu);
                if ($n_comu == 'A' || $n_comu == 'B')
                    $this->pdf->SetTextColor(0, 0, 204);
                else
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetXY(85, $iniYP);
                $this->pdf->Cell(10, 5, $n_comu, 0, 0, 'C');
                $iniYP += 7;
            }

            $this->pdf->Image("http://sistemas-dev.com/intranet/images/inicial/dibujo3.jpg", 125, 70, 60, 40, 'JPG', '');

            $this->pdf->SetFont('Arial', '', 8);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetXY(120, 112);
            $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');

            $this->pdf->SetTextColor(0, 0, 0);

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
            }
        }
        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
        }
    }

    public function generarboleta() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
        $vflgGen = $this->input->post("flgGenerar");
// ==============================================
        //$vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        if ($vflgGen == '0') {
            $this->pdf = new Pdf ();
        }
        foreach ($arraAlumnos as $alumno) {
            if ($vflgGen == '1') {
                $this->pdf = new Pdf ();
            }
            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);

            $vFilaIni = 55;
            $vFilaIni2 = 60;

# BLOQUE : LISTADO DE SUB-AREAS
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetXY(20, $vFilaIni);
            $this->pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(30, $vFilaIni);
            $this->pdf->Cell(50, 10, utf8_decode('SUB-ÁREAS'), 1, 0, 'C', TRUE);

            $this->pdf->SetXY(80, $vFilaIni);
            $this->pdf->Cell(22, 5, 'PROM. I B', 1, 0, 'C', TRUE);
            // Validar para agrupar para Secundaria 3, 4 y 5
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                $this->pdf->SetXY(80, $vFilaIni2);
                $this->pdf->Cell(22, 5, 'CUANTITATIVO', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(80, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUANT', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(91, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUAL', 1, 0, 'C', TRUE);
            }

            $this->pdf->SetXY(102, $vFilaIni);
            $this->pdf->Cell(22, 5, 'PROM. II B', 1, 0, 'C', TRUE);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                $this->pdf->SetXY(102, $vFilaIni2);
                $this->pdf->Cell(22, 5, 'CUANTITATIVO', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(102, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUANT', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(113, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUAL', 1, 0, 'C', TRUE);
            }

            $this->pdf->SetXY(124, $vFilaIni);
            $this->pdf->Cell(22, 5, 'PROM. III B', 1, 0, 'C', TRUE);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                $this->pdf->SetXY(124, $vFilaIni2);
                $this->pdf->Cell(22, 5, 'CUANTITATIVO', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(124, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUANT', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(135, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUAL', 1, 0, 'C', TRUE);
            }

            $this->pdf->SetXY(146, $vFilaIni);
            $this->pdf->Cell(22, 5, 'PROM. IV B', 1, 0, 'C', TRUE);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                $this->pdf->SetXY(146, $vFilaIni2);
                $this->pdf->Cell(22, 5, 'CUANTITATIVO', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(146, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUANT', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(157, $vFilaIni2);
                $this->pdf->Cell(11, 5, 'CUAL', 1, 0, 'C', TRUE);
            }
//$this->pdf->Rect(168, $vFilaIni-5, 10, 20, 'DF');
            $this->pdf->SetXY(168, $vFilaIni);
            $this->pdf->Cell(26, 5, 'PROM. FINAL', 1, 0, 'C', TRUE);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                $this->pdf->SetXY(168, $vFilaIni2);
                $this->pdf->Cell(26, 5, 'CUANTITATIVO', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(168, $vFilaIni2);
                $this->pdf->Cell(13, 5, 'CUANT', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(181, $vFilaIni2);
                $this->pdf->Cell(13, 5, 'CUAL', 1, 0, 'C', TRUE);
            }
// ========================================================
            $dataCursos = $this->objSalon->getCursosSubAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
            $dataCursoOficial = $this->objSalon->getCursosAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
// ========================================================                        
            $this->pdf->SetFillColor(208, 222, 240);
            $yCurso = 65;
            $filaCurso = 1;
            $filaCursollenados = 0;
            $filaCursollenados2 = 0;
            $filaCursollenados3 = 0;
            $filaCursollenados4 = 0;
            $vPuntaje = 0;
            $vPuntaje2 = 0;
            $vPuntaje3 = 0;
            $vPuntaje4 = 0;
            $arrCurso = array();
            $arrCurso2 = array();
            $arrCurso3 = array();
            $arrCurso4 = array();
            $notaTIC1cuan = "";
            $notaTIC1cual = "";
            $notaTIC2cuan = "";
            $notaTIC2cual = "";
            $notaTIC3cuan = "";
            $notaTIC3cual = "";
            $notaTIC4cuan = "";
            $notaTIC4cual = "";
            foreach ($dataCursos as $rowcur) {
                $vidcurso = $rowcur->cursocod;
                $dataNota = $this->objNota->getNotasxBimestreBoleta($alumno->ALUCOD, $vidcurso, $vbimestre);
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(20, $yCurso);
                $this->pdf->Cell(10, 5, (($filaCurso < 10) ? ('0' . $filaCurso) : $filaCurso), 1, 0, 'C', TRUE);
                $this->pdf->SetXY(30, $yCurso);
                $this->pdf->Cell(50, 5, utf8_decode($rowcur->cursocor), 1, 0, 'L', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
# BLOQUE BIMESTRE 1        
                if ($vbimestre >= 1) {
                    $this->pdf->SetXY(80, $yCurso);
                    $vnotaCuanti1 = $dataNota[0]->pb;
                    $vnotaCuali1 = $this->getCualitativo((int) $dataNota[0]->pb);
                    if ($vnotaCuanti1 > 10 && $vnotaCuanti1 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti1 = (($vnotaCuanti1 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti1 = (($vnotaCuanti1 > 0) ? (($vnotaCuanti1 < 10) ? ('0' . $vnotaCuanti1) : $vnotaCuanti1) : '');
                    }
                    $this->pdf->Cell($ancho, 5, $vnotaCuanti1, 1, 0, 'C', TRUE);
                    if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                        // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                    } else {
                        if ($vnotaCuali1 === 'A' || $vnotaCuali1 === 'AD' || $vnotaCuali1 === 'B') {
                            $this->pdf->SetTextColor(0, 0, 204);
                        } else {
                            $this->pdf->SetTextColor(255, 0, 51);
                        }
                        $this->pdf->SetXY(91, $yCurso);
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                            $vnotaCuali1 = (($vnotaCuali1 != '') ? 'EXO' : 'EXO');
                            $this->pdf->SetTextColor(0, 0, 204);
                            $this->pdf->Cell(11, 5, $vnotaCuali1, 1, 0, 'C', TRUE);
                        } else {
                            $this->pdf->Cell(11, 5, $vnotaCuali1, 1, 0, 'C', TRUE);
                        }
                    }
                } else {
                    $vnotaCuanti1 = "";
                    $vnotaCuali1 = "";
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    $this->pdf->SetXY(80, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                    $this->pdf->SetXY(91, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                }
# ================================ BLOQUE DE NOTAS ======================================
# BLOQUE BIMESTRE 2
                /*       if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                  $this->pdf->SetXY(102, $yCurso);
                  $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                  } else {
                  $this->pdf->SetXY(102, $yCurso);
                  $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                  $this->pdf->SetXY(113, $yCurso);
                  $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                  } */
                if ($vbimestre >= 2) {
                    $this->pdf->SetXY(102, $yCurso);
                    $vnotaCuanti2 = $dataNota[1]->pb;
                    $vnotaCuali2 = $this->getCualitativo((int) $dataNota[1]->pb);
                    if ($vnotaCuanti2 > 10 && $vnotaCuanti2 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti2 = (($vnotaCuanti2 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti2 = (($vnotaCuanti2 > 0) ? (((int) $vnotaCuanti2 < 10) ? ('0' . (int) $vnotaCuanti2) : $vnotaCuanti2) : '');
                    }
                    $this->pdf->Cell($ancho, 5, $vnotaCuanti2, 1, 0, 'C', TRUE);
                    if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                        // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                    } else {
                        if ($vnotaCuali2 === 'A' || $vnotaCuali2 === 'AD' || $vnotaCuali2 === 'B') {
                            $this->pdf->SetTextColor(0, 0, 204);
                        } else {
                            $this->pdf->SetTextColor(255, 0, 51);
                        }
                        $this->pdf->SetXY(113, $yCurso);
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                            $vnotaCuali2 = (($vnotaCuali2 != '') ? 'EXO' : 'EXO');
                            $this->pdf->SetTextColor(0, 0, 204);
                            $this->pdf->Cell(11, 5, $vnotaCuali2, 1, 0, 'C', TRUE);
                        } else {
                            $this->pdf->Cell(11, 5, $vnotaCuali2, 1, 0, 'C', TRUE);
                        }
                    }
                } else {
                    $vnotaCuanti2 = "";
                    $vnotaCuali2 = "";
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    $this->pdf->SetXY(102, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                    $this->pdf->SetXY(113, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                }
# BLOQUE BIMESTRE 3
                /* if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                  $this->pdf->SetXY(124, $yCurso);
                  $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                  } else {
                  $this->pdf->SetXY(124, $yCurso);
                  $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                  $this->pdf->SetXY(135, $yCurso);
                  $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                  } */
                if ($vbimestre >= 3) {
                    $this->pdf->SetXY(124, $yCurso);
                    $vnotaCuanti3 = $dataNota[2]->pb;
                    $vnotaCuali3 = $this->getCualitativo((int) $dataNota[2]->pb);
                    if ($vnotaCuanti3 > 10 && $vnotaCuanti3 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti3 = (($vnotaCuanti3 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti3 = (($vnotaCuanti3 > 0) ? (((int) $vnotaCuanti3 < 10) ? ('0' . (int) $vnotaCuanti3) : $vnotaCuanti3) : '');
                    }
                    $this->pdf->Cell($ancho, 5, $vnotaCuanti3, 1, 0, 'C', TRUE);
                    if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                        // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                    } else {
                        if ($vnotaCuali3 === 'A' || $vnotaCuali3 === 'AD' || $vnotaCuali3 === 'B') {
                            $this->pdf->SetTextColor(0, 0, 204);
                        } else {
                            $this->pdf->SetTextColor(255, 0, 51);
                        }
                        $this->pdf->SetXY(135, $yCurso);
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                            $vnotaCuali3 = (($vnotaCuali3 != '') ? 'EXO' : 'EXO');
                            $this->pdf->SetTextColor(0, 0, 204);
                            $this->pdf->Cell(11, 5, $vnotaCuali3, 1, 0, 'C', TRUE);
                        } else {
                            $this->pdf->Cell(11, 5, $vnotaCuali3, 1, 0, 'C', TRUE);
                        }
                    }
                } else {
                    $vnotaCuanti3 = "";
                    $vnotaCuali3 = "";
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    $this->pdf->SetXY(124, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                    $this->pdf->SetXY(135, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                }
# BLOQUE BIMESTRE 4
                if ($vbimestre >= 4) {
                    $this->pdf->SetXY(146, $yCurso);
                    $vnotaCuanti4 = $dataNota[3]->pb;
                    $vnotaCuali4 = $this->getCualitativo((int) $dataNota[3]->pb);
                    if ($vnotaCuanti4 > 10 && $vnotaCuanti4 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti4 = (($vnotaCuanti4 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti4 = (($vnotaCuanti4 > 0) ? (((int) $vnotaCuanti4 < 10) ? ('0' . (int) $vnotaCuanti4) : $vnotaCuanti4) : '');
                    }
                    $this->pdf->Cell($ancho, 5, $vnotaCuanti4, 1, 0, 'C', TRUE);
                    if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                        // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                    } else {
                        if ($vnotaCuali4 === 'A' || $vnotaCuali4 === 'AD' || $vnotaCuali4 === 'B') {
                            $this->pdf->SetTextColor(0, 0, 204);
                        } else {
                            $this->pdf->SetTextColor(255, 0, 51);
                        }
                        $this->pdf->SetXY(157, $yCurso);
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                            $vnotaCuali4 = (($vnotaCuali4 != '') ? 'EXO' : 'EXO');
                            $this->pdf->SetTextColor(0, 0, 204);
                            $this->pdf->Cell(11, 5, $vnotaCuali4, 1, 0, 'C', TRUE);
                        } else {
                            $this->pdf->Cell(11, 5, $vnotaCuali4, 1, 0, 'C', TRUE);
                        }
                    }
                } else {
                    $vnotaCuanti4 = "";
                    $vnotaCuali4 = "";
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 22;
                    else
                        $ancho = 11;
                    $this->pdf->SetXY(146, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                    $this->pdf->SetXY(157, $yCurso);
                    $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                }
# BLOQUE BIMESTRE 5
                if ($vbimestre >= 4) {
                    $vpromFinalCuant = '';
                    $vpromFinalCual = '';
                    if ($vnotaCuanti1 != '' && $vnotaCuanti2 != '' && $vnotaCuanti3 != '' && $vnotaCuanti4 != '') {
                        $vpromFinalCuant = round((($vnotaCuanti1 + $vnotaCuanti2 + $vnotaCuanti3 + $vnotaCuanti4) / 4), 0);
                        $vpromFinalCual = $this->getCualitativo((int) $vpromFinalCuant);
                    }
                    if ($vpromFinalCuant > 10 && $vpromFinalCuant <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                        $ancho = 26;
                    else
                        $ancho = 13;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vpromFinalCuant = (($vpromFinalCuant != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vpromFinalCuant = (($vpromFinalCuant > 0) ? (((int) $vpromFinalCuant < 10) ? ('0' . (int) $vpromFinalCuant) : $vpromFinalCuant) : '');
                    }

                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->Cell($ancho, 5, $vpromFinalCuant, 1, 0, 'C', TRUE);
                    if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                        // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                    } else {
                        if ($vpromFinalCual === 'A' || $vpromFinalCual === 'AD' || $vpromFinalCual === 'B') {
                            $this->pdf->SetTextColor(0, 0, 204);
                        } else {
                            $this->pdf->SetTextColor(255, 0, 51);
                        }
                        $this->pdf->SetXY(181, $yCurso);
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                            $vpromFinalCual = (($vpromFinalCual != '') ? 'EXO' : 'EXO');
                            $this->pdf->SetTextColor(0, 0, 204);
                            $this->pdf->Cell($ancho, 5, $vpromFinalCual, 1, 0, 'C', TRUE);
                        } else {
                            $this->pdf->Cell($ancho, 5, $vpromFinalCual, 1, 0, 'C', TRUE);
                        }
                    }
                } else {
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S') {
                        $this->pdf->SetFillColor(208, 222, 240);
                        $this->pdf->SetXY(168, $yCurso);
                        $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                    } else {
                        $this->pdf->SetFillColor(208, 222, 240);
                        $this->pdf->SetXY(168, $yCurso);
                        $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                        $this->pdf->SetXY(181, $yCurso);
                        $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    }
                }


                if ($rowcur->cursocod === "21" && $vbimestre >= 1) { // Si es Curso COMPUTACION
                    $notaTIC1cuan = $vnotaCuanti1;
                    $notaTIC1cual = $vnotaCuali1;
                }
                if ($rowcur->cursocod === "21" && $vbimestre >= 2) { // Si es Curso COMPUTACION
                    $notaTIC2cuan = $vnotaCuanti2;
                    $notaTIC2cual = $vnotaCuali2;
                }
                if ($rowcur->cursocod === "21" && $vbimestre >= 3) { // Si es Curso COMPUTACION
                    $notaTIC3cuan = $vnotaCuanti3;
                    $notaTIC3cual = $vnotaCuali3;
                }
                if ($rowcur->cursocod === "21" && $vbimestre >= 4) { // Si es Curso COMPUTACION
                    $notaTIC4cuan = $vnotaCuanti4;
                    $notaTIC4cual = $vnotaCuali4;
                }
// =========== Para acumular arreglos de cursos oficiales =========
                $arrCurso["cursos1"][] = $vnotaCuanti1;
                $arrCurso["cursos2"][] = $vnotaCuanti2;
                $arrCurso["cursos3"][] = $vnotaCuanti3;
                $arrCurso["cursos4"][] = $vnotaCuanti4;
// ======= Acumuladores para promedios y puntajes ===========
                $vPuntaje += (int) $vnotaCuanti1;
                if ((int) $vPuntaje > 0) {
                    $filaCursollenados++;
                }
                $vPuntaje2 += (int) $vnotaCuanti2;
                if ((int) $vPuntaje2 > 0) {
                    $filaCursollenados2++;
                }
                $vPuntaje3 += (int) $vnotaCuanti3;
                if ((int) $vPuntaje3 > 0) {
                    $filaCursollenados3++;
                }
                $vPuntaje4 += (int) $vnotaCuanti4;
                if ((int) $vPuntaje4 > 0) {
                    $filaCursollenados4++;
                }
// ========================================================
                $yCurso += 5;
                $filaCurso++;
            } // -*- Fin FOR -*-

            if ($alumno->INSTRUCOD === "S") {
                if ($vbimestre >= 1) {
                    $notaTIC1cuan = (($filaCursollenados > 0) ? (round($vPuntaje / $filaCursollenados)) : '');
                    $notaTIC1cual = $this->getCualitativo((int) $notaTIC1cuan);
                }
                if ($vbimestre >= 2) {
                    $notaTIC2cuan = (($filaCursollenados2 > 0) ? (round($vPuntaje2 / $filaCursollenados2)) : '');
                    $notaTIC2cual = $this->getCualitativo((int) $notaTIC2cuan);
                }
                if ($vbimestre >= 3) {
                    $notaTIC3cuan = (($filaCursollenados3 > 0) ? (round($vPuntaje3 / $filaCursollenados3)) : '');
                    $notaTIC3cual = $this->getCualitativo((int) $notaTIC3cuan);
                }
                if ($vbimestre >= 4) {
                    $notaTIC4cuan = (($filaCursollenados4 > 0) ? (round($vPuntaje4 / $filaCursollenados4)) : '');
                    $notaTIC4cual = $this->getCualitativo((int) $notaTIC4cuan);
                }
            }

# ================================ BLOQUE DE CONDUCTA ===================================
            $dataConducta = $this->objNota->getNotasConducta($alumno->ALUCOD, $vbimestre);
            if ($vbimestre >= 1) {
                $notacondCuan1 = $dataConducta[0]->pb;
                $notacondCual1 = $this->getCualitativo($notacondCuan1);
            }
            if ($vbimestre >= 2) {
                $notacondCuan2 = $dataConducta[1]->pb;
                $notacondCual2 = $this->getCualitativo($notacondCuan2);
            }
            if ($vbimestre >= 3) {
                $notacondCuan3 = $dataConducta[2]->pb;
                $notacondCual3 = $this->getCualitativo($notacondCuan3);
            }
            if ($vbimestre >= 4) {
                $notacondCuan4 = $dataConducta[3]->pb;
                $notacondCual4 = $this->getCualitativo($notacondCuan4);
            }
            $yCurso += 2;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(60, 5, utf8_decode('SE DESENVUELVE EN ENTORNOS VIRTUALES - TIC'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($notaTIC1cuan > 10 && $notaTIC1cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(80, $yCurso);
            $vnota1 = (($notaTIC1cuan < 10) ? '0' . $notaTIC1cuan : $notaTIC1cuan);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                $this->pdf->Cell(22, 5, (($vnota1 > 0) ? $vnota1 : ''), 1, 0, 'C', TRUE);
            else
                $this->pdf->Cell(11, 5, (($vnota1 > 0) ? $vnota1 : ''), 1, 0, 'C', TRUE);

            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                // NO TIENE COLUMNA CUANTITATIVO
            } else {
                if ($notaTIC1cual === 'A' || $notaTIC1cual === 'AD' || $notaTIC1cual === 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCurso);
                $this->pdf->Cell(11, 5, $notaTIC1cual, 1, 0, 'C', TRUE);
            }
# BLOQUE BIMESTRE 2
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($notaTIC2cuan > 10 && $notaTIC2cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(102, $yCurso);
            $vnota2 = (($notaTIC2cuan < 10) ? '0' . $notaTIC2cuan : $notaTIC2cuan);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                $this->pdf->Cell(22, 5, (($vnota2 > 0) ? $vnota2 : ''), 1, 0, 'C', TRUE);
            else
                $this->pdf->Cell(11, 5, (($vnota2 > 0) ? $vnota2 : ''), 1, 0, 'C', TRUE);

            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                // NO TIENE COLUMNA CUANTITATIVO
            } else {
                if ($notaTIC2cual == 'A' || $notaTIC2cual == 'AD' || $notaTIC2cual == 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(113, $yCurso);
                $this->pdf->Cell(11, 5, $notaTIC2cual, 1, 0, 'C', TRUE);
            }
# BLOQUE BIMESTRE 3
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($notaTIC3cuan > 10 && $notaTIC3cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(124, $yCurso);
            $vnota3 = (($notaTIC3cuan < 10) ? '0' . $notaTIC3cuan : $notaTIC3cuan);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                $this->pdf->Cell(22, 5, (($vnota3 > 0) ? $vnota3 : ''), 1, 0, 'C', TRUE);
            else
                $this->pdf->Cell(11, 5, (($vnota3 > 0) ? $vnota3 : ''), 1, 0, 'C', TRUE);

            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                // NO TIENE COLUMNA CUANTITATIVO
            } else {
                if ($notaTIC3cual == 'A' || $notaTIC3cual == 'AD' || $notaTIC3cual == 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(135, $yCurso);
                $this->pdf->Cell(11, 5, $notaTIC3cual, 1, 0, 'C', TRUE);
            }
# BLOQUE BIMESTRE 4
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($notaTIC4cuan > 10 && $notaTIC4cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(146, $yCurso);
            $vnota4 = (($notaTIC4cuan < 10) ? '0' . $notaTIC4cuan : $notaTIC4cuan);
            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                $this->pdf->Cell(22, 5, (($vnota4 > 0) ? $vnota4 : ''), 1, 0, 'C', TRUE);
            else
                $this->pdf->Cell(11, 5, (($vnota4 > 0) ? $vnota4 : ''), 1, 0, 'C', TRUE);

            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                // NO TIENE COLUMNA CUANTITATIVO
            } else {
                if ($notaTIC4cual == 'A' || $notaTIC4cual == 'AD' || $notaTIC4cual == 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(157, $yCurso);
                $this->pdf->Cell(11, 5, $notaTIC4cual, 1, 0, 'C', TRUE);
            }
# BLOQUE BIMESTRE 5

            if ($vbimestre >= 4) {
                $vpromFinalTICCuant = '';
                $vpromFinalTICCual = '';
                if ($notaTIC1cuan != '' && $notaTIC2cuan != '' && $notaTIC3cuan != '' && $notaTIC4cuan != '') {
                    $vpromFinalTICCuant = round((($notaTIC1cuan + $notaTIC2cuan + $notaTIC3cuan + $notaTIC4cuan) / 4), 0);
                    $vpromFinalTICCual = $this->getCualitativo((int) $vpromFinalTICCuant);
                }
                if ($vpromFinalTICCuant > 10 && $vpromFinalTICCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                    $ancho = 26;
                else
                    $ancho = 13;
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                    $vpromFinalTICCuant = (($vpromFinalTICCuant != '') ? 'EXO' : 'EXO');
                } else {
                    $vpromFinalTICCuant = (($vpromFinalTICCuant > 0) ? (((int) $vpromFinalTICCuant < 10) ? ('0' . (int) $vpromFinalTICCuant) : $vpromFinalTICCuant) : '');
                }

                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->Cell($ancho, 5, $vpromFinalTICCuant, 1, 0, 'C', TRUE);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                } else {
                    if ($vpromFinalTICCual === 'A' || $vpromFinalTICCual === 'AD' || $vpromFinalTICCual === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(181, $yCurso);
                    $this->pdf->Cell($ancho, 5, $vpromFinalTICCual, 1, 0, 'C', TRUE);
                }
            } else {
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(181, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                }
            }


            $yCurso += 5;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(60, 5, utf8_decode('GESTIONA SU APRENDIZAJE DE MANERA AUTÓNOMA'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);
            if ($notacondCuan1 > 10 && $notacondCuan1 <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetXY(80, $yCurso);
            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3)
                $this->pdf->Cell(22, 5, $notacondCuan1, 1, 0, 'C', TRUE);
            else
                $this->pdf->Cell(11, 5, $notacondCuan1, 1, 0, 'C', TRUE);
            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                // NO TIENE COLUMNA
            } else {
                if ($notacondCual1 === 'A' || $notacondCual1 === 'AD' || $notacondCual1 === 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCurso);
                $this->pdf->Cell(11, 5, $notacondCual1, 1, 0, 'C', TRUE);
            }
# BLOQUE BIMESTRE 2
            if ($vbimestre >= 2) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notacondCuan2 > 10 && $notacondCuan2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(102, $yCurso);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3)
                    $this->pdf->Cell(22, 5, $notacondCuan2, 1, 0, 'C', TRUE);
                else
                    $this->pdf->Cell(11, 5, $notacondCuan2, 1, 0, 'C', TRUE);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    // NO TIENE COLUMNA
                } else {
                    if ($notacondCual2 === 'A' || $notacondCual2 === 'AD' || $notacondCual2 === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(113, $yCurso);
                    $this->pdf->Cell(11, 5, $notacondCual2, 1, 0, 'C', TRUE);
                }
            } else {
                if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                    $ancho = 22;
                else
                    $ancho = 11;
                $this->pdf->SetXY(102, $yCurso);
                $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                $this->pdf->SetXY(113, $yCurso);
                $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
            }
# BLOQUE BIMESTRE 3
            if ($vbimestre >= 3) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notacondCuan3 > 10 && $notacondCuan3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(124, $yCurso);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3)
                    $this->pdf->Cell(22, 5, $notacondCuan3, 1, 0, 'C', TRUE);
                else
                    $this->pdf->Cell(11, 5, $notacondCuan3, 1, 0, 'C', TRUE);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    // NO TIENE COLUMNA
                } else {
                    if ($notacondCual3 === 'A' || $notacondCual3 === 'AD' || $notacondCual3 === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(135, $yCurso);
                    $this->pdf->Cell(11, 5, $notacondCual3, 1, 0, 'C', TRUE);
                }
            } else {
                if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                    $ancho = 22;
                else
                    $ancho = 11;
                $this->pdf->SetXY(124, $yCurso);
                $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                $this->pdf->SetXY(135, $yCurso);
                $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
            }
# BLOQUE BIMESTRE 4
            if ($vbimestre >= 4) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notacondCuan4 > 10 && $notacondCuan4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(146, $yCurso);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3)
                    $this->pdf->Cell(22, 5, $notacondCuan4, 1, 0, 'C', TRUE);
                else
                    $this->pdf->Cell(11, 5, $notacondCuan4, 1, 0, 'C', TRUE);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    // NO TIENE COLUMNA
                } else {
                    if ($notacondCual4 === 'A' || $notacondCual4 === 'AD' || $notacondCual4 === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(157, $yCurso);
                    $this->pdf->Cell(11, 5, $notacondCual4, 1, 0, 'C', TRUE);
                }
            } else {
                if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                    $ancho = 22;
                else
                    $ancho = 11;
                $this->pdf->SetXY(146, $yCurso);
                $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
                $this->pdf->SetXY(157, $yCurso);
                $this->pdf->Cell($ancho, 5, '', 1, 0, 'C', FALSE);
            }
# BLOQUE BIMESTRE 5
            if ($vbimestre >= 4) {
                $vpromFinalCONDCuant = '';
                $vpromFinalCONDCual = '';
                if ($notacondCuan1 != '' && $notacondCuan2 != '' && $notacondCuan3 != '' && $notacondCuan4 != '') {
                    $vpromFinalCONDCuant = round((($notacondCuan1 + $notacondCuan2 + $notacondCuan3 + $notacondCuan4) / 4), 0);
                    $vpromFinalCONDCual = $this->getCualitativo((int) $vpromFinalCONDCuant);
                }
                if ($vpromFinalCONDCuant > 10 && $vpromFinalCONDCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S')
                    $ancho = 26;
                else
                    $ancho = 13;
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                    $vpromFinalCONDCuant = (($vpromFinalCONDCuant != '') ? 'EXO' : 'EXO');
                } else {
                    $vpromFinalCONDCuant = (($vpromFinalCONDCuant > 0) ? (((int) $vpromFinalCONDCuant < 10) ? ('0' . (int) $vpromFinalCONDCuant) : $vpromFinalCONDCuant) : '');
                }

                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->Cell($ancho, 5, $vpromFinalCONDCuant, 1, 0, 'C', TRUE);
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                } else {
                    if ($vpromFinalCONDCual === 'A' || $vpromFinalCONDCual === 'AD' || $vpromFinalCONDCual === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(181, $yCurso);
                    $this->pdf->Cell($ancho, 5, $vpromFinalCONDCual, 1, 0, 'C', TRUE);
                }
            } else {
                if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(181, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                }
            }
# ================================ BLOQUE RESUMEN ANUAL ===================================
            $yCurso += 7;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(60, 10, utf8_decode('RESUMEN ANUAL'), 1, 0, 'C', TRUE);
            // ======== Primer Bimestre =============
            $this->pdf->SetFont('Arial', 'B', 7);
            $promAnual1 = (($filaCursollenados > 0) ? $vPuntaje : '');
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(80, $yCurso);
            $this->pdf->Cell(22, 5, 'I B', 1, 0, 'C', TRUE);
            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetTextColor(0, 0, 204);
            $this->pdf->SetXY(80, $yCurso + 5);
            $this->pdf->Cell(22, 5, $promAnual1, 1, 0, 'C', TRUE);
            // ======== Segundo Bimestre =============
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFont('Arial', 'B', 7);
            $promAnual2 = (($filaCursollenados2 > 0) ? $vPuntaje2 : '');
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(102, $yCurso);
            $this->pdf->Cell(22, 5, 'II B', 1, 0, 'C', TRUE);
            $this->pdf->SetFillColor(255, 255, 255);

            $this->pdf->SetTextColor(0, 0, 204);
            $this->pdf->SetXY(102, $yCurso + 5);
            $this->pdf->Cell(22, 5, $promAnual2, 1, 0, 'C', TRUE);


            // ======== Tercer Bimestre =============
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFont('Arial', 'B', 7);
            $promAnual3 = (($filaCursollenados3 > 0) ? $vPuntaje3 : '');
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(124, $yCurso);
            $this->pdf->Cell(22, 5, 'III B', 1, 0, 'C', TRUE);
            $this->pdf->SetFillColor(255, 255, 255);

            $this->pdf->SetTextColor(0, 0, 204);
            $this->pdf->SetXY(124, $yCurso + 5);
            $this->pdf->Cell(22, 5, $promAnual3, 1, 0, 'C', TRUE);

            // ======== Cuarto Bimestre =============
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFont('Arial', 'B', 7);
            $promAnual4 = (($filaCursollenados4 > 0) ? $vPuntaje4 : '');
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(146, $yCurso);
            $this->pdf->Cell(22, 5, 'IV B', 1, 0, 'C', TRUE);
            $this->pdf->SetFillColor(255, 255, 255);

            $this->pdf->SetTextColor(0, 0, 204);
            $this->pdf->SetXY(146, $yCurso + 5);
            $this->pdf->Cell(22, 5, $promAnual4, 1, 0, 'C', TRUE);

            if ($vbimestre >= 4) {
                $totalPuntajeFinal = ($promAnual1 + $promAnual2 + $promAnual3 + $promAnual4);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(168, $yCurso);
                $this->pdf->Cell(26, 5, 'TOTAL', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(168, $yCurso + 5);
                $this->pdf->Cell(26, 5, $totalPuntajeFinal, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(168, $yCurso);
                $this->pdf->Cell(26, 5, 'TOTAL', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(168, $yCurso + 5);
                $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
            }
# ================================ BLOQUE A/B ===================================
            $yCurso = $this->pdf->GetY() + 7;
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(10, 5, utf8_decode('A'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(30, $yCurso);
            $this->pdf->Cell(50, 5, utf8_decode('PROMEDIO BIMESTRAL'), 1, 0, 'L', TRUE);
            /* $this->pdf->SetXY(20, $yCurso + 5);
              $this->pdf->Cell(10, 5, utf8_decode('B'), 1, 0, 'C', TRUE);
              $this->pdf->SetXY(30, $yCurso + 5);
              $this->pdf->Cell(50, 5, utf8_decode('ORDEN DE MÉRITO EN EL AULA'), 1, 0, 'L', TRUE); */

            $this->pdf->SetFont('Arial', 'B', 7);
            if ($vbimestre >= 1)
                $promAnual1 = (($filaCursollenados > 0) ? (round($vPuntaje / $filaCursollenados)) : '');
            if ($vbimestre >= 2)
                $promAnual2 = (($filaCursollenados2 > 0) ? (round($vPuntaje2 / $filaCursollenados2)) : '');
            if ($vbimestre >= 3)
                $promAnual3 = (($filaCursollenados3 > 0) ? (round($vPuntaje3 / $filaCursollenados3)) : '');
            if ($vbimestre >= 4)
                $promAnual4 = (($filaCursollenados4 > 0) ? (round($vPuntaje4 / $filaCursollenados4)) : '');

            // Primer Bimestre
            $this->pdf->SetFillColor(255, 255, 255);
            if ($promAnual1 > 10 && $promAnual1 <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(80, $yCurso);
            $this->pdf->Cell(22, 5, $promAnual1, 1, 0, 'C', TRUE);
            // Segundo Bimestre
            $this->pdf->SetFillColor(255, 255, 255);
            if ($promAnual2 > 10 && $promAnual2 <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $prombim1 = (($promAnual2 < 10) ? '0' . $promAnual2 : $promAnual2);
            $this->pdf->SetXY(102, $yCurso);
            $this->pdf->Cell(22, 5, (($prombim1 > 0) ? $prombim1 : ''), 1, 0, 'C', TRUE);
            // Tercer Bimestre
            $this->pdf->SetFillColor(255, 255, 255);
            if ($promAnual3 > 10 && $promAnual3 <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $prombim2 = (($promAnual3 < 10) ? '0' . $promAnual3 : $promAnual3);
            $this->pdf->SetXY(124, $yCurso);
            $this->pdf->Cell(22, 5, (($prombim2 > 0) ? $prombim2 : ''), 1, 0, 'C', TRUE);

            // Cuarto Bimestre

            $this->pdf->SetFillColor(255, 255, 255);
            if ($promAnual4 > 10 && $promAnual4 <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $prombim3 = (($promAnual4 < 10) ? '0' . $promAnual4 : $promAnual4);
            $this->pdf->SetXY(146, $yCurso);
            $this->pdf->Cell(22, 5, (($prombim3 > 0) ? $prombim3 : ''), 1, 0, 'C', TRUE);
            /* $this->pdf->SetFillColor(255, 255, 255);
              $this->pdf->SetXY(146, $yCurso + 5);
              $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE); */


            if ($vbimestre >= 4) {
                $vPromBimestral = round(($promAnual1 + $prombim1 + $prombim2 + $prombim3) / 4);
                if ($vPromBimestral > 10 && $vPromBimestral <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(168, $yCurso);
                $this->pdf->Cell(26, 5, $vPromBimestral, 1, 0, 'C', TRUE);
                $this->pdf->SetTextColor(0, 0, 0);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(168, $yCurso);
                $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                /* $this->pdf->SetXY(168, $yCurso + 5);
                  $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE); */
            }
# ================================ BLOQUE AREAS ===================================
            $yCurso = $this->pdf->GetY() + 7;
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(30, $yCurso);
            $this->pdf->Cell(50, 10, utf8_decode('ÁREAS BASICAS'), 1, 0, 'C', TRUE);

            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(80, $yCurso);
            $this->pdf->Cell(44, 5, 'PROMEDIOS BIMESTRALES', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(80, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'I B', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(91, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'II B', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(102, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'III B', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(113, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'IV B', 1, 0, 'C', TRUE);

            $this->pdf->SetXY(124, $yCurso);
            $this->pdf->Cell(22, 5, 'PROMEDIO ANUAL', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(124, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'CUAN', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(135, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'CUAL', 1, 0, 'C', TRUE);

            $this->pdf->SetXY(146, $yCurso);
            $this->pdf->Cell(8, 10, 'PRP', 1, 0, 'C', TRUE);

            $filaCursoOficial = 1;
            $vpromBim = 0;
            $vpromBim2 = 0;
            $vpromBim3 = 0;
            $vpromBim4 = 0;
            $vpromCurso1 = 0;
            $vpromCurso2 = 0;
            $vpromCurso3 = 0;
            $vpromCurso4 = 0;
            $vpromCurso5 = 0;
            $vpromCurso6 = 0;
            $vpromCurso7 = 0;
            $vpromCurso8 = 0;
            $vpromCurso9 = 0;
            $vpromCurso10 = 0;
            $yCursoOficial = $yCurso + 10;
            foreach ($dataCursoOficial as $rowcur) {
                if ($alumno->INSTRUCOD === "P") {
                    if ($filaCursoOficial === 1) { // ARTE
                        $vpromBim = $arrCurso["cursos1"][0];
                        $vpromBim2 = $arrCurso["cursos2"][0];
                        $vpromBim3 = $arrCurso["cursos3"][0];
                        $vpromBim4 = $arrCurso["cursos4"][0];
                        $vpromCurso1 += $vpromBim;
                    } elseif ($filaCursoOficial === 2) { // CIENCIA YA AMBIENTE
                        $vpromBim = $arrCurso["cursos1"][1];
                        $vpromBim2 = $arrCurso["cursos2"][1];
                        $vpromBim3 = $arrCurso["cursos3"][1];
                        $vpromBim4 = $arrCurso["cursos4"][1];
                        $vpromCurso2 += $vpromBim;
                    } elseif ($filaCursoOficial === 3) { // COMUNICACION
                        $vpromBim = round(($arrCurso["cursos1"][2] + $arrCurso["cursos1"][3] + $arrCurso["cursos1"][4]) / 3);
                        $vpromBim2 = round(($arrCurso["cursos2"][2] + $arrCurso["cursos2"][3] + $arrCurso["cursos2"][4]) / 3);
                        $vpromBim3 = round(($arrCurso["cursos3"][2] + $arrCurso["cursos3"][3] + $arrCurso["cursos3"][4]) / 3);
                        $vpromBim4 = round(($arrCurso["cursos4"][2] + $arrCurso["cursos4"][3] + $arrCurso["cursos4"][4]) / 3);
                        $vpromCurso3 += $vpromBim;
                    } elseif ($filaCursoOficial === 4) { // EDU. FISICA
                        $vpromBim = $arrCurso["cursos1"][5];
                        $vpromBim2 = $arrCurso["cursos2"][5];
                        $vpromBim3 = $arrCurso["cursos3"][5];
                        $vpromBim4 = $arrCurso["cursos4"][5];
                        $vpromCurso4 += $vpromBim;
                    } elseif ($filaCursoOficial === 5) { // RELIGION
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                            $vpromBim = 0;
                            $vpromBim2 = 0;
                            $vpromBim3 = 0;
                            $vpromBim4 = 0;
                        } else {
                            $vpromBim = $arrCurso["cursos1"][6];
                            $vpromBim2 = $arrCurso["cursos2"][6];
                            $vpromBim3 = $arrCurso["cursos3"][6];
                            $vpromBim4 = $arrCurso["cursos4"][6];
                        }
                        $vpromCurso5 += $vpromBim;
                    } elseif ($filaCursoOficial === 6) { // MATEMATICA
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = round(($arrCurso["cursos1"][7] + $arrCurso["cursos1"][8]) / 2);
                            $vpromBim2 = round(($arrCurso["cursos2"][7] + $arrCurso["cursos2"][8]) / 2);
                            $vpromBim3 = round(($arrCurso["cursos3"][7] + $arrCurso["cursos3"][8]) / 2);
                            $vpromBim4 = round(($arrCurso["cursos4"][7] + $arrCurso["cursos4"][8]) / 2);
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = round(($arrCurso["cursos1"][7] + $arrCurso["cursos1"][8] + $arrCurso["cursos1"][9]) / 3);
                            $vpromBim2 = round(($arrCurso["cursos2"][7] + $arrCurso["cursos2"][8] + $arrCurso["cursos2"][9]) / 3);
                            $vpromBim3 = round(($arrCurso["cursos3"][7] + $arrCurso["cursos3"][8] + $arrCurso["cursos3"][9]) / 3);
                            $vpromBim4 = round(($arrCurso["cursos4"][7] + $arrCurso["cursos4"][8] + $arrCurso["cursos4"][9]) / 3);
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = round(($arrCurso["cursos1"][7] + $arrCurso["cursos1"][8] + $arrCurso["cursos1"][9] + $arrCurso["cursos1"][10]) / 4);
                            $vpromBim2 = round(($arrCurso["cursos2"][7] + $arrCurso["cursos2"][8] + $arrCurso["cursos2"][9] + $arrCurso["cursos2"][10]) / 4);
                            $vpromBim3 = round(($arrCurso["cursos3"][7] + $arrCurso["cursos3"][8] + $arrCurso["cursos3"][9] + $arrCurso["cursos3"][10]) / 4);
                            $vpromBim4 = round(($arrCurso["cursos4"][7] + $arrCurso["cursos4"][8] + $arrCurso["cursos4"][9] + $arrCurso["cursos4"][10]) / 4);
                        }
                        $vpromCurso6 += $vpromBim;
                    } elseif ($filaCursoOficial === 7) { // PERSONAL SOCIAL
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][9];
                            $vpromBim2 = $arrCurso["cursos2"][9];
                            $vpromBim3 = $arrCurso["cursos3"][9];
                            $vpromBim4 = $arrCurso["cursos4"][9];
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][10];
                            $vpromBim2 = $arrCurso["cursos2"][10];
                            $vpromBim3 = $arrCurso["cursos3"][10];
                            $vpromBim4 = $arrCurso["cursos4"][10];
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        }
                        $vpromCurso7 += $vpromBim;
                    } elseif ($filaCursoOficial === 8) { // INGLES
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][10];
                            $vpromBim2 = $arrCurso["cursos2"][10];
                            $vpromBim3 = $arrCurso["cursos3"][10];
                            $vpromBim4 = $arrCurso["cursos4"][10];
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][12];
                            $vpromBim2 = $arrCurso["cursos2"][12];
                            $vpromBim3 = $arrCurso["cursos3"][12];
                            $vpromBim4 = $arrCurso["cursos4"][12];
                        }
                        $vpromCurso8 += $vpromBim;
                    } elseif ($filaCursoOficial === 9) { // COMPUTACION
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][12];
                            $vpromBim2 = $arrCurso["cursos2"][12];
                            $vpromBim3 = $arrCurso["cursos3"][12];
                            $vpromBim4 = $arrCurso["cursos4"][12];
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][13];
                            $vpromBim2 = $arrCurso["cursos2"][13];
                            $vpromBim3 = $arrCurso["cursos3"][13];
                            $vpromBim4 = $arrCurso["cursos4"][13];
                        }
                        $vpromCurso9 += $vpromBim;
                    }
                } elseif ($alumno->INSTRUCOD === "S") {
                    if ($filaCursoOficial === 1) { // ARTE
                        $vpromBim = $arrCurso["cursos1"][0];
                        $vpromBim2 = $arrCurso["cursos2"][0];
                        $vpromBim3 = $arrCurso["cursos3"][0];
                        $vpromBim4 = $arrCurso["cursos4"][0];
                        $vpromCurso1 += $vpromBim;
                    } elseif ($filaCursoOficial === 2) { // CIENCIA Y TECNOLOGIA
                        if ($arrCurso["cursos1"][1] > 0 && $arrCurso["cursos1"][2] > 0 && $arrCurso["cursos1"][3] > 0) {
                            $vpromBim = round(($arrCurso["cursos1"][1] + $arrCurso["cursos1"][2] + $arrCurso["cursos1"][3]) / 3);
                        } else {
                            $vpromBim = 0;
                        }
                        if ($arrCurso["cursos2"][1] > 0 && $arrCurso["cursos2"][2] > 0 && $arrCurso["cursos2"][3] > 0) {
                            $vpromBim2 = round(($arrCurso["cursos2"][1] + $arrCurso["cursos2"][2] + $arrCurso["cursos2"][3]) / 3);
                        } else {
                            $vpromBim2 = 0;
                        }
                        if ($arrCurso["cursos3"][1] > 0 && $arrCurso["cursos3"][2] > 0 && $arrCurso["cursos3"][3] > 0) {
                            $vpromBim3 = round(($arrCurso["cursos3"][1] + $arrCurso["cursos3"][2] + $arrCurso["cursos3"][3]) / 3);
                        } else {
                            $vpromBim3 = 0;
                        }
                        if ($arrCurso["cursos4"][1] > 0 && $arrCurso["cursos4"][2] > 0 && $arrCurso["cursos4"][3] > 0) {
                            $vpromBim4 = round(($arrCurso["cursos4"][1] + $arrCurso["cursos4"][2] + $arrCurso["cursos4"][3]) / 3);
                        } else {
                            $vpromBim4 = 0;
                        }
                        $vpromCurso2 += $vpromBim;
                    } elseif ($filaCursoOficial === 3) { // CIENCIAS SOCIALES
                        $vpromBim = $arrCurso["cursos1"][4];
                        $vpromBim2 = $arrCurso["cursos2"][4];
                        $vpromBim3 = $arrCurso["cursos3"][4];
                        $vpromBim4 = $arrCurso["cursos4"][4];
                        $vpromCurso3 += $vpromBim;
                    } elseif ($filaCursoOficial === 4) { // COMUNICACION
                        if ($alumno->GRADOCOD <= 2) {
                            if ($arrCurso["cursos1"][5] > 0 && $arrCurso["cursos1"][6] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][5] + $arrCurso["cursos1"][6] ) / 2);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][5] > 0 && $arrCurso["cursos2"][6] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][5] + $arrCurso["cursos2"][6] ) / 2);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][5] > 0 && $arrCurso["cursos3"][6] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][5] + $arrCurso["cursos3"][6] ) / 2);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][5] > 0 && $arrCurso["cursos4"][6] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][5] + $arrCurso["cursos4"][6] ) / 2);
                            } else {
                                $vpromBim4 = 0;
                            }
                        } elseif ($alumno->GRADOCOD > 2) {
                            if ($arrCurso["cursos1"][5] > 0 && $arrCurso["cursos1"][6] > 0 && $arrCurso["cursos1"][7] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][5] + $arrCurso["cursos1"][6] + $arrCurso["cursos1"][7] ) / 3);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][5] > 0 && $arrCurso["cursos2"][6] > 0 && $arrCurso["cursos2"][7] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][5] + $arrCurso["cursos2"][6] + $arrCurso["cursos2"][7] ) / 3);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][5] > 0 && $arrCurso["cursos3"][6] > 0 && $arrCurso["cursos3"][7] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][5] + $arrCurso["cursos3"][6] + $arrCurso["cursos3"][7] ) / 3);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][5] > 0 && $arrCurso["cursos4"][6] > 0 && $arrCurso["cursos4"][7] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][5] + $arrCurso["cursos4"][6] + $arrCurso["cursos4"][7] ) / 3);
                            } else {
                                $vpromBim4 = 0;
                            }
                        }
                        $vpromCurso4 += $vpromBim;
                    } elseif ($filaCursoOficial === 5) { // DPCC
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][7];
                            $vpromBim2 = $arrCurso["cursos2"][7];
                            $vpromBim3 = $arrCurso["cursos3"][7];
                            $vpromBim4 = $arrCurso["cursos4"][7];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][8];
                            $vpromBim2 = $arrCurso["cursos2"][8];
                            $vpromBim3 = $arrCurso["cursos3"][8];
                            $vpromBim4 = $arrCurso["cursos4"][8];
                        }
                        $vpromCurso5 += $vpromBim;
                    } elseif ($filaCursoOficial === 6) { // EDU. FISICA
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][8];
                            $vpromBim2 = $arrCurso["cursos2"][8];
                            $vpromBim3 = $arrCurso["cursos3"][8];
                            $vpromBim4 = $arrCurso["cursos4"][8];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][9];
                            $vpromBim2 = $arrCurso["cursos2"][9];
                            $vpromBim3 = $arrCurso["cursos3"][9];
                            $vpromBim4 = $arrCurso["cursos4"][9];
                        }
                        $vpromCurso6 += $vpromBim;
                    } elseif ($filaCursoOficial === 7) { // COMPUTACION
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][9];
                            $vpromBim2 = $arrCurso["cursos2"][9];
                            $vpromBim3 = $arrCurso["cursos3"][9];
                            $vpromBim4 = $arrCurso["cursos4"][9];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][10];
                            $vpromBim2 = $arrCurso["cursos2"][10];
                            $vpromBim3 = $arrCurso["cursos3"][10];
                            $vpromBim4 = $arrCurso["cursos4"][10];
                        }
                        $vpromCurso7 += $vpromBim;
                    } elseif ($filaCursoOficial === 8) { // EDU. RELIGION
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                            $vpromBim = 0;
                            $vpromBim2 = 0;
                            $vpromBim3 = 0;
                            $vpromBim4 = 0;
                        } else {
                            if ($alumno->GRADOCOD <= 2) {
                                $vpromBim = $arrCurso["cursos1"][10];
                                $vpromBim2 = $arrCurso["cursos2"][10];
                                $vpromBim3 = $arrCurso["cursos3"][10];
                                $vpromBim4 = $arrCurso["cursos4"][10];
                            } elseif ($alumno->GRADOCOD > 2) {
                                $vpromBim = $arrCurso["cursos1"][11];
                                $vpromBim2 = $arrCurso["cursos2"][11];
                                $vpromBim3 = $arrCurso["cursos3"][11];
                                $vpromBim4 = $arrCurso["cursos4"][11];
                            }
                        }
                        $vpromCurso8 += $vpromBim;
                    } elseif ($filaCursoOficial === 9) { // INGLES
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][12];
                            $vpromBim2 = $arrCurso["cursos2"][12];
                            $vpromBim3 = $arrCurso["cursos3"][12];
                            $vpromBim4 = $arrCurso["cursos4"][12];
                        }
                        $vpromCurso9 += $vpromBim;
                    } elseif ($filaCursoOficial === 10) { // MATEMATICAS
                        if ($alumno->GRADOCOD <= 2) {
                            if ($arrCurso["cursos1"][12] > 0 && $arrCurso["cursos1"][13] > 0 && $arrCurso["cursos1"][14] > 0 && $arrCurso["cursos1"][15] > 0 && $arrCurso["cursos1"][16] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][12] + $arrCurso["cursos1"][13] + $arrCurso["cursos1"][14] + $arrCurso["cursos1"][15] + $arrCurso["cursos1"][16] ) / 5);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][12] > 0 && $arrCurso["cursos2"][13] > 0 && $arrCurso["cursos2"][14] > 0 && $arrCurso["cursos2"][15] > 0 && $arrCurso["cursos2"][16] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][12] + $arrCurso["cursos2"][13] + $arrCurso["cursos2"][14] + $arrCurso["cursos2"][15] + $arrCurso["cursos2"][16] ) / 5);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][12] > 0 && $arrCurso["cursos3"][13] > 0 && $arrCurso["cursos3"][14] > 0 && $arrCurso["cursos3"][15] > 0 && $arrCurso["cursos3"][16] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][12] + $arrCurso["cursos3"][13] + $arrCurso["cursos3"][14] + $arrCurso["cursos3"][15] + $arrCurso["cursos3"][16] ) / 5);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][12] > 0 && $arrCurso["cursos4"][13] > 0 && $arrCurso["cursos4"][14] > 0 && $arrCurso["cursos4"][15] > 0 && $arrCurso["cursos4"][16] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][12] + $arrCurso["cursos4"][13] + $arrCurso["cursos4"][14] + $arrCurso["cursos4"][15] + $arrCurso["cursos4"][16] ) / 5);
                            } else {
                                $vpromBim4 = 0;
                            }
                        } elseif ($alumno->GRADOCOD > 2) {
                            if ($arrCurso["cursos1"][13] > 0 && $arrCurso["cursos1"][14] > 0 && $arrCurso["cursos1"][15] > 0 && $arrCurso["cursos1"][16] > 0 && $arrCurso["cursos1"][17] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][13] + $arrCurso["cursos1"][14] + $arrCurso["cursos1"][15] + $arrCurso["cursos1"][16] + $arrCurso["cursos1"][17] ) / 5);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][13] > 0 && $arrCurso["cursos2"][14] > 0 && $arrCurso["cursos2"][15] > 0 && $arrCurso["cursos2"][16] > 0 && $arrCurso["cursos2"][17] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][13] + $arrCurso["cursos2"][14] + $arrCurso["cursos2"][15] + $arrCurso["cursos2"][16] + $arrCurso["cursos2"][17] ) / 5);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][13] > 0 && $arrCurso["cursos3"][14] > 0 && $arrCurso["cursos3"][15] > 0 && $arrCurso["cursos3"][16] > 0 && $arrCurso["cursos3"][17] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][13] + $arrCurso["cursos3"][14] + $arrCurso["cursos3"][15] + $arrCurso["cursos3"][16] + $arrCurso["cursos3"][17] ) / 5);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][13] > 0 && $arrCurso["cursos4"][14] > 0 && $arrCurso["cursos4"][15] > 0 && $arrCurso["cursos4"][16] > 0 && $arrCurso["cursos4"][17] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][13] + $arrCurso["cursos4"][14] + $arrCurso["cursos4"][15] + $arrCurso["cursos4"][16] + $arrCurso["cursos4"][17] ) / 5);
                            } else {
                                $vpromBim4 = 0;
                            }
                        }
                        $vpromCurso10 += $vpromBim;
                    }
                }
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(20, $yCursoOficial);
                $this->pdf->Cell(10, 5, (($filaCursoOficial < 10) ? ('0' . $filaCursoOficial) : $filaCursoOficial), 1, 0, 'C', TRUE);
                $this->pdf->SetXY(30, $yCursoOficial);
                $this->pdf->Cell(50, 5, utf8_decode($rowcur->cursocor), 1, 0, 'L', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
                // 1 bimestre 
                if ($vpromBim > 10 && $vpromBim <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(80, $yCursoOficial);
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->Cell(11, 5, (($vpromBim > 0) ? (((int) $vpromBim < 10) ? ('0' . (int) $vpromBim) : $vpromBim) : ''), 1, 0, 'C', TRUE);
                }
                // 2 bimestre 
                if ($vpromBim2 > 10 && $vpromBim2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCursoOficial);
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->Cell(11, 5, (($vpromBim2 > 0) ? (((int) $vpromBim2 < 10) ? ('0' . (int) $vpromBim2) : $vpromBim2) : ''), 1, 0, 'C', TRUE);
                }
                // 3 bimestre 
                if ($vpromBim3 > 10 && $vpromBim3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(102, $yCursoOficial);
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->Cell(11, 5, (($vpromBim3 > 0) ? (((int) $vpromBim3 < 10) ? ('0' . (int) $vpromBim3) : $vpromBim3) : ''), 1, 0, 'C', TRUE);
                }
                // 4 bimestre                
                if ($vpromBim4 > 10 && $vpromBim4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(113, $yCursoOficial);
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->Cell(11, 5, (($vpromBim4 > 0) ? (((int) $vpromBim4 < 10) ? ('0' . (int) $vpromBim4) : $vpromBim4) : ''), 1, 0, 'C', TRUE);
                }
                if ($vbimestre >= 4) {
                    $vpromAreaCuan = "";
                    $vpromAreaCual = "";
                    if ($vpromBim != '' && $vpromBim2 != '' && $vpromBim3 != '' && $vpromBim4 != '') {
                        $vpromAreaCuan = round(($vpromBim + $vpromBim2 + $vpromBim3 + $vpromBim4) / 4);
                        $vpromAreaCual = $this->getCualitativo((int) $vpromAreaCuan);
                    }
                    $this->pdf->SetTextColor(0, 0, 204);
                    if ($vpromAreaCuan > 10 && $vpromAreaCuan <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(124, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
                        $this->pdf->Cell(11, 5, $vpromAreaCuan, 1, 0, 'C', TRUE);
                    }
                    if ($vpromAreaCual === 'A' || $vpromAreaCual === 'AD' || $vpromAreaCual === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(135, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
                        $this->pdf->Cell(11, 5, $vpromAreaCual, 1, 0, 'C', TRUE);
                    }
                } else {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(124, $yCursoOficial);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(135, $yCursoOficial);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                }
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);

                $yCursoOficial += 5;
                $filaCursoOficial ++;
            }
# BLOQUE TIC
            $yCursoOficial += 2;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCursoOficial);
            $this->pdf->Cell(60, 5, utf8_decode('SE DESENVUELVE EN ENTORNOS VIRTUALES - TIC'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($notaTIC1cuan > 10 && $notaTIC1cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(80, $yCursoOficial);
            $this->pdf->Cell(11, 5, $notaTIC1cuan, 1, 0, 'C', TRUE);

            if ($vbimestre >= 2) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($notaTIC2cuan > 10 && $notaTIC2cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, $notaTIC2cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }
            // 3 bimestre
            if ($vbimestre >= 3) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($notaTIC3cuan > 10 && $notaTIC3cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, $notaTIC3cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            // 4 bimestre
            if ($vbimestre >= 4) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($notaTIC4cuan > 10 && $notaTIC4cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, $notaTIC4cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            if ($vbimestre >= 4) {
                $vpromFinalTICCuant = '';
                $vpromFinalTICCual = '';
                if ($notaTIC1cuan != '' && $notaTIC2cuan != '' && $notaTIC3cuan != '' && $notaTIC4cuan != '') {
                    $vpromFinalTICCuant = round((($notaTIC1cuan + $notaTIC2cuan + $notaTIC3cuan + $notaTIC4cuan) / 4), 0);
                    $vpromFinalTICCual = $this->getCualitativo((int) $vpromFinalTICCuant);
                }
                if ($vpromFinalTICCuant > 10 && $vpromFinalTICCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(11, 5, $vpromFinalTICCuant, 1, 0, 'C', TRUE);
                $this->pdf->SetXY(135, $yCursoOficial);

                if ($vpromFinalTICCual === 'A' || $vpromFinalTICCual === 'AD' || $vpromFinalTICCual === 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->Cell(11, 5, $vpromFinalTICCual, 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(135, $yCursoOficial);

                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }



# BLOQUE AUTONOMA
            $yCursoOficial += 5;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCursoOficial);
            $this->pdf->Cell(60, 5, utf8_decode('GESTIONA SU APRENDIZAJE - AUTONOMÍA'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($notacondCuan1 > 10 && $notacondCuan1 <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(80, $yCursoOficial);
            $this->pdf->Cell(11, 5, $notacondCuan1, 1, 0, 'C', TRUE);

            if ($vbimestre >= 2) {
                if ($notacondCuan2 > 10 && $notacondCuan2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, $notacondCuan2, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }
            // 3 bimestre
            if ($vbimestre >= 3) {
                if ($notacondCuan3 > 10 && $notacondCuan3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, $notacondCuan3, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            // 4 bimestre
            if ($vbimestre >= 4) {
                if ($notacondCuan4 > 10 && $notacondCuan4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, $notacondCuan4, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            if ($vbimestre >= 4) {
                $vpromFinalCONCuant = '';
                $vpromFinalCONCual = '';
                if ($notacondCuan1 != '' && $notacondCuan2 != '' && $notacondCuan3 != '' && $notacondCuan4 != '') {
                    $vpromFinalCONCuant = round((($notacondCuan1 + $notacondCuan2 + $notacondCuan3 + $notacondCuan4) / 4), 0);
                    $vpromFinalCONCual = $this->getCualitativo((int) $vpromFinalTICCuant);
                }
                if ($vpromFinalCONCuant > 10 && $vpromFinalCONCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }

                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(11, 5, $vpromFinalCONCuant, 1, 0, 'C', TRUE);

                if ($vpromFinalCONCual === 'A' || $vpromFinalCONCual === 'AD' || $vpromFinalCONCual === 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(135, $yCursoOficial);
                $this->pdf->Cell(11, 5, $vpromFinalCONCual, 1, 0, 'C', TRUE);


                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(135, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }
# BLOQUE DE VALORES
            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
                $this->pdf->SetFont('Arial', 'B', 8);
                $yCursoOficial += 6;
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(20, $yCursoOficial);
                $this->pdf->Cell(60, 5, utf8_decode('APRECIACIÓN ANUAL'), 1, 0, 'C', TRUE);

                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(80, $yCursoOficial);
                $this->pdf->Cell(74, 5, 'APROBADO', 1, 0, 'C', TRUE);

                $this->pdf->SetTextColor(0, 0, 0);
            } else {
                $this->pdf->SetFont('Arial', 'B', 8);
                $yCursoOficial += 6;
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(20, $yCursoOficial);
                $this->pdf->Cell(60, 5, utf8_decode('APRECIACIÓN ANUAL'), 1, 0, 'C', TRUE);

                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(80, $yCursoOficial);
                $this->pdf->Cell(74, 5, 'APROBADO', 1, 0, 'C', TRUE);

                $this->pdf->Image("http://sistemas-dev.com/intranet/images/valores_notas.jpg", 19.5, $yCursoOficial + 6, 135, 25, 'JPG', '');
                $this->pdf->SetTextColor(0, 0, 0);
            }
# BLOQUE DE FIRMAS
            if ($alumno->INSTRUCOD === "P") {
                if ($alumno->GRADOCOD > 3) {
                    $iniYPie = 200;
                } elseif ($alumno->GRADOCOD == 3) {
                    $iniYPie = 195;
                } else {
                    $iniYPie = 190;
                }
            } else {
                if ($alumno->GRADOCOD > 2)
                    $iniYPie = 224;
                else
                    $iniYPie = 220;
            }
            /* $this->pdf->SetFont('Arial', 'B', 6);
              $this->pdf->Line(160, $iniYPie, 195, $iniYPie);
              $this->pdf->SetXY(163, $iniYPie);
              $this->pdf->Cell(30, 5, 'TUTOR(A)', 0, 0, 'C'); */

            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->Line(160, $iniYPie + 40, 195, $iniYPie + 40);
            $this->pdf->SetXY(154, $iniYPie + 35);
            $this->pdf->Cell(50, 4, utf8_decode($alumno->PROFE), 0, 0, 'C');
            $this->pdf->SetXY(163, $iniYPie + 40);
            $this->pdf->Cell(30, 5, utf8_decode('TUTOR(A)'), 0, 0, 'C');

            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(155, $iniYPie + 60);
            $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
// ============ Enviando al correo la Boleta ======================================
                /* $resp = $this->objAlumno->getCorreosxAlumno($alumno->ALUCOD);
                  if ($resp) {
                  $arrEmail = array(
                  0 => array('email' => $resp->pademail, 'nombre' => $resp->padnom),
                  1 => array('email' => $resp->mademail, 'nombre' => $resp->madnom),
                  2 => array('email' => $resp->apoemail, 'nombre' => $resp->aponom)
                  );
                  EnviarMailAdjuntos($arrEmail, $pathFile);
                  } */
            }
        }

        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
//$file_contents = $this->pdf->Output('Reporte_boletas.pdf','S');	
//echo $file_contents;
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
            $timer = "<script>";
            $timer .= " setTimeout(function(){ ";
            $timer .= "     window.close(); ";
            $timer .= " },3000); ";
            $timer .= "</script>";
            echo $timer;
        }
    }

    public function generarResumenBimestre() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        //print_r($_POST); exit;
// ============= Variables POST =================
        $vnivel = $this->input->post("cbnivel");
        $vgrado = $this->input->post("cbgrado");
        $vbimestre = $this->input->post("cbbimestre");
// ==============================================

        $this->load->library('pdf');
        //$arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);

        $this->pdf = new Pdf ();
        $this->pdf->SetTopMargin(0.2);
        $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
        $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage('L', 'A4');
# BLOQUE HEAD
        $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 260, 13, 'JPG', '');
        $dataResumen = $this->objSalon->getResumenPorPeriodo($vnivel, $vgrado, $vbimestre);

# CREAMOS TITULO DE LA BOLETA
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetXY(85, 20);
        $this->pdf->Cell(120, 10, utf8_decode("INFORME DE ESTUDIANTES DESAPROBADOS 2021"), 0, 0, 'C');

        if ($vnivel == "P") {
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(20, 40);
            $this->pdf->Cell(40, 5, utf8_decode("A LA                           :"), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(60, 40);
            $this->pdf->Cell(100, 5, utf8_decode("DIRECTORA LIDIA ORÉ MONTES"), 0, 0, 'L');
        } elseif ($vnivel == "S") {
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(20, 40);
            $this->pdf->Cell(40, 5, utf8_decode("AL                             :"), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(60, 40);
            $this->pdf->Cell(100, 5, utf8_decode("DIRECTOR DOMINGO HUAYTALLA LLALLAHUI"), 0, 0, 'L');
        }
        if ($vnivel == "P") {
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(20, 45);
            $this->pdf->Cell(40, 5, utf8_decode("DEL                            :"), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(60, 45);
            $this->pdf->Cell(100, 5, utf8_decode("SUB DIRECTOR WILVER CORREA CERNA"), 0, 0, 'L');
        } elseif ($vnivel == "S") {
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(20, 45);
            $this->pdf->Cell(40, 5, utf8_decode("DE LA                       :"), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(60, 45);
            $this->pdf->Cell(100, 5, utf8_decode("COORDINADORA RUTH ARISANCA GARCIA"), 0, 0, 'L');
        }
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(20, 50);
        $this->pdf->Cell(40, 5, utf8_decode("ASUNTO                   :"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(60, 50);
        $this->pdf->Cell(100, 5, utf8_decode("ESTUDIANTES DESAPROBADOS"), 0, 0, 'L');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(20, 55);
        $this->pdf->Cell(40, 5, utf8_decode("GRADO                     :"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(60, 55);
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->Cell(20, 5, utf8_decode($vgrado . "°"), 0, 0, 'L');
        $this->pdf->SetXY(80, 55);
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->Cell(20, 5, utf8_decode("NIVEL      :"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(100, 55);
        if ($vnivel == "P")
            $this->pdf->Cell(40, 5, utf8_decode("PRIMARIA"), 0, 0, 'L');
        else
            $this->pdf->Cell(40, 5, utf8_decode("SECUNDARIA"), 0, 0, 'L');
        $this->pdf->SetXY(140, 55);
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->Cell(30, 5, utf8_decode("BIMESTRE :"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(170, 55);
        $this->pdf->Cell(20, 5, utf8_decode($vbimestre), 0, 0, 'L');

        $this->pdf->SetXY(190, 55);
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->Cell(30, 5, utf8_decode("FECHA :"), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(220, 55);
        $this->pdf->Cell(40, 5, date("d/m/Y H:i:s"), 0, 0, 'L');

        // Mostrando cuadro de Resumen
        $iniX = ($vnivel == "P") ? 0 : 5; // Para correr el X y no descuadre para secundaria
        $iniFila = 65;
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY((10 - $iniX), $iniFila);
        $this->pdf->Cell(5, 10, utf8_decode("N°"), 1, 0, 'C');

        $this->pdf->SetXY((15 - $iniX), $iniFila);
        $this->pdf->Cell(30, 10, utf8_decode("AULA"), 1, 0, 'C');

        $this->pdf->SetXY((45 - $iniX), $iniFila);
        $this->pdf->Cell(60, 10, utf8_decode("ESTUDIANTE"), 1, 0, 'C');

        $dataCursos = $this->objSalon->getCursosSubAreas($vnivel, $vgrado);
        $ancho = count($dataCursos) * 10;
        $this->pdf->SetXY((105 - $iniX), $iniFila);
        $this->pdf->Cell($ancho, 5, utf8_decode("ÁREAS"), 1, 0, 'C');
        $filaSub = ($iniFila + 5);
        $filaX = ($vnivel == "P") ? 105 : 100;
        foreach ($dataCursos as $curso) {
            $this->pdf->SetXY($filaX, $filaSub);
            $this->pdf->Cell(10, 5, $curso->cursopre, 1, 0, 'C');
            $filaX += 10;
        }
        $this->pdf->SetXY($filaX, $iniFila);
        $this->pdf->Cell(15, 10, utf8_decode("CANT."), 1, 0, 'C');

        $item = 1;
        $iniFila += 10;

        $iniX = ($vnivel == "P") ? 0 : 5; // Para correr el X y no descuadre para secundaria
        $this->pdf->SetFont('Arial', '', 7);
        foreach ($dataResumen as $resumen) {
            $aula = explode("-", $resumen->aula);
            $notas = explode("*", $resumen->notas);
            $this->pdf->SetXY((10 - $iniX), $iniFila);
            $this->pdf->Cell(5, 5, $item, 1, 0, 'C');
            $this->pdf->SetXY((15 - $iniX), $iniFila);
            $this->pdf->Cell(30, 5, utf8_decode($aula[2]), 1, 0, 'L');
            $this->pdf->SetXY((45 - $iniX), $iniFila);
            $this->pdf->Cell(60, 5, utf8_decode($resumen->nomcomp), 1, 0, 'L');
            $filaX = ($vnivel == "P") ? 105 : 100;
            for ($x = 0; $x < count($notas) - 1; $x++) {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY($filaX, $iniFila);
                if ((int) $notas[$x] > 10 && (int) $notas[$x] <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->Cell(10, 5, (((int) $notas[$x] < 10) ? ("0" . $notas[$x]) : $notas[$x]), 1, 0, 'C', TRUE);
                $filaX += 10;
            }
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetXY($filaX, $iniFila);
            $this->pdf->Cell(15, 5, utf8_decode($resumen->total), 1, 0, 'C');
            $iniFila += 5;
            $item++;
        }


        $this->pdf->AddPage('L', 'A4');
        $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 260, 13, 'JPG', '');
        // Mostrando cuadro de Resumen
        $iniFila = 20;
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY(10, $iniFila);
        $this->pdf->Cell(5, 5, utf8_decode("N°"), 1, 0, 'C');
        $this->pdf->SetXY(15, $iniFila);
        $this->pdf->Cell(30, 5, utf8_decode("AULA"), 1, 0, 'C');
        $this->pdf->SetXY(45, $iniFila);
        $this->pdf->Cell(60, 5, utf8_decode("ESTUDIANTE"), 1, 0, 'C');
        $this->pdf->SetXY(105, $iniFila);
        $this->pdf->Cell(85, 5, utf8_decode("ACCIONES REALIZADAS"), 1, 0, 'C');
        $this->pdf->SetXY(190, $iniFila);
        $this->pdf->Cell(95, 5, utf8_decode("COMPROMISO PP.FF"), 1, 0, 'C');
        $fila = 1;
        $iniFila += 5;
        foreach ($dataResumen as $resumen) {
            $aula = explode("-", $resumen->aula);
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetXY(10, $iniFila);
            $this->pdf->Cell(5, 5, utf8_decode($fila), 1, 0, 'C');
            $this->pdf->SetXY(15, $iniFila);
            $this->pdf->Cell(30, 5, utf8_decode($aula[2]), 1, 0, 'L');
            $this->pdf->SetXY(45, $iniFila);
            $this->pdf->Cell(60, 5, utf8_decode($resumen->nomcomp), 1, 0, 'L');
            $this->pdf->SetXY(105, $iniFila);
            $this->pdf->Cell(85, 5, "", 1, 0, 'L');
            $this->pdf->SetXY(190, $iniFila);
            $this->pdf->Cell(95, 5, "", 1, 0, 'L');
            $fila++;
            $iniFila += 5;
        }

        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->SetXY(215, $iniFila + 50);
        $this->pdf->Line(230, $iniFila + 50, 270, $iniFila + 50);
        if ($vnivel == "P")
            $this->pdf->Cell(70, 5, "WILVER CORREA CERNA", 0, 0, 'C');
        else
            $this->pdf->Cell(70, 5, "RUTH ARISANCA GARCIA", 0, 0, 'C');

        $this->pdf->Output('Reporte_Resumen_boletas.pdf', 'I');
    }


    public function plantillaboletainicialGeneric() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
        $vflgGen = $this->input->post("flgGenerar");
// ==============================================

        $vFlagCuantitativo = FALSE;
        //$vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        if ($vflgGen == '0') {
            $this->pdf = new Pdf ();
        }
        foreach ($arraAlumnos as $alumno) {
            $arrApreAnual = array();
            $contdesapro = 0;
            if ($vflgGen == '1') {
                $this->pdf = new Pdf ();
            }

            if (($alumno->INSTRUCOD == 'P' || $alumno->INSTRUCOD == 'S') && $this->ano >= 2022) {
                $vFlagCuantitativo = TRUE;
            }

            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);

            $vFilaIni = 55;
            $vFilaIni2 = 60;

# BLOQUE : LISTADO DE SUB-AREAS
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetXY(20, $vFilaIni);
            $this->pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(30, $vFilaIni);
            $this->pdf->Cell(40, 10, utf8_decode('AREA CURRICULAR'), 1, 0, 'C', TRUE);
			$this->pdf->SetFont('Arial', 'B', 5);
			$this->pdf->SetXY(70, $vFilaIni);
			$this->pdf->Cell(123, 5, 'LOGROS DE APRENDIZAJE POR COMPETENCIA', 1, 0, 'C', TRUE);
 
			$vFilaIni +=5;
            $this->pdf->SetXY(70, $vFilaIni);
            $this->pdf->Cell(73, 5, 'COMPETENCIAS', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(143, $vFilaIni);
            $this->pdf->Cell(25, 5, utf8_decode('LOGRO'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(168, $vFilaIni);
            $this->pdf->Cell(25, 5, utf8_decode('PROM. ÁREA'), 1, 0, 'C', TRUE);


// ========================================================
            $dataCursos = $this->objSalon->getCursosSubAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
			
            //$dataCursoOficial = $this->objSalon->getCursosAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
// ========================================================                        
            $this->pdf->SetFillColor(208, 222, 240);
            $yCurso = 65;
            $filaCurso = 1;

            foreach ($dataCursos as $rowcur) {
                $vidcurso = $rowcur->cursocod;
                $dataNota = $this->objNota->getNotasxBimestreBoletaInicialNew($alumno->ALUCOD, $vidcurso, $vbimestre, $vunidad);
				$dataCompetencias = $this->objSalon->getCompetenciaPorUnidadCurso( $vidcurso, $vunidad,$alumno->NEMO);
				
				/*echo "<pre>"; 
				print_r($dataNota); 
				echo "</pre>"; 
				exit;*/
				
				/*echo "<pre>"; 
				print_r($dataCompetencias); 
				echo "</pre>"; 
				echo "Cantidad : ".$this->obtieneTotalCompetenciaXCurso($dataCompetencias); exit;*/
				$anchoCurso = 5 * (int)$this->obtieneTotalCompetenciaXCurso($dataCompetencias);
                $this->pdf->SetFont('Arial', '', 6);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(20, $yCurso);
                $this->pdf->Cell(10, $anchoCurso, (($filaCurso < 10) ? ('0' . $filaCurso) : $filaCurso), 1, 0, 'C', TRUE);
                $this->pdf->SetXY(30, $yCurso);
                $this->pdf->Cell(40, $anchoCurso, utf8_decode($rowcur->cursocor), 1, 0, 'L', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
				//echo print_r($this->obtieneCompetenciasXCurso($dataCompetencias)); exit;
				// Cargando competencias
				$filaComp = $yCurso;
				$dataCompetencia = $this->obtieneCompetenciasXCurso($dataCompetencias);
				for($i=0;$i<count($dataCompetencia); $i++){
					$this->pdf->SetTextColor(0, 0, 0);
					$this->pdf->SetXY(70, $filaComp);
					$this->pdf->Cell(73, 5, $dataCompetencia[$i], 1, 0, 'L', FALSE);
					
					// cargando notas de la competencias
					$campoCompetencia = "n1e".($i+1);
                    if ($dataNota[0]->$campoCompetencia > 10 && $dataNota[0]->$campoCompetencia <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }					
					$this->pdf->SetXY(143, $filaComp);
					$this->pdf->Cell(25, 5, $this->getCualitativo($dataNota[0]->$campoCompetencia), 1, 0, 'C', TRUE);
				
					$filaComp+=5;
				}
				// cargando los promedios de area
				if ($dataNota[0]->pf > 10 && $dataNota[0]->pf <= 20) {
					$this->pdf->SetTextColor(0, 0, 204);
				} else {
					$this->pdf->SetTextColor(255, 0, 51);
				}					
				$this->pdf->SetXY(168, $yCurso);
				$this->pdf->Cell(25, $anchoCurso, $this->getCualitativo($dataNota[0]->pf), 1, 0, 'C', TRUE);
			
				$yCurso +=$anchoCurso;
				$filaCurso++;
			}
			
			// Mostrando comportamientos
			$yCurso +=5;
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetTextColor(0, 0, 0);
			$this->pdf->SetFont('Arial', 'B', 7);
			$this->pdf->SetXY(20, $yCurso);
			$this->pdf->Cell(50, 5, 'COMPORTAMIENTO', 1, 0, 'C', TRUE);
			
			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetFont('Arial', 'B',7);
			
			$dataConducta = $this->objNota->getNotasConducta($alumno->ALUCOD, $vbimestre, $vunidad);	
                    if ($dataConducta[0]->pb=="AD" || $dataConducta[0]->pb=="A") {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }				
			$this->pdf->SetXY(70, $yCurso);
			$this->pdf->Cell(123, 5, $dataConducta[0]->pb, 1, 0, 'C', TRUE);			
			
		// colocando cuadro de valores
		$this->pdf->SetTextColor(0, 0, 0);
		$yCurso +=10;
		$this->pdf->Image("http://sistemas-dev.com/intranet/images/imagen_escala_inicial.jpg", 19.5, $yCurso, 175,50, 'JPG', '');
		// colocando firmas 
		$yCurso +=25;
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->Line(35, $yCurso+50, 85, $yCurso+50);
            $this->pdf->SetXY(35, $yCurso+45);
            $this->pdf->Cell(50, 4, utf8_decode($alumno->PROFE), 0, 0, 'C');
            $this->pdf->SetXY(45, $yCurso + 50);
            $this->pdf->Cell(30, 5, utf8_decode('TUTOR(A)'), 0, 0, 'C');


           $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->Line(120, $yCurso+50, 170, $yCurso+50);
            $this->pdf->SetXY(130, $yCurso + 50);
            $this->pdf->Cell(30, 5, utf8_decode('DIRECCIÓN'), 0, 0, 'C');
			
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetXY(80, $yCurso + 90);
            $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'C');


            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                //$pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');			
			}
			
		

        }

			if ($vflgGen == 0) {
				$this->pdf->Output('Reporte_boletas_inicial.pdf', 'I');
			} else {
				echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
				$timer = "<script>";
				$timer .= " setTimeout(function(){ ";
				$timer .= "     window.close(); ";
				$timer .= " },3000); ";
				$timer .= "</script>";
				echo $timer;
			}	

		
	}	
			
			
			
			
	public function obtieneTotalCompetenciaXCurso($listaCompetencia){
		$total=0;
		$limiteEval =10;
		if(count($listaCompetencia)>0){
			for($x=1; $x<=$limiteEval; $x++){
				$campo = "EVAL".$x."PESO";
				if($listaCompetencia[0]->$campo >0){
					$total ++;
				}
			}
		}
		return $total;
	}	
			
	public function obtieneCompetenciasXCurso($listaCompetencia){
		
		$datos = [];
		$limiteEval =10;
		if(count($listaCompetencia)>0){
			for($x=1; $x<=$limiteEval; $x++){
				$campo = "EVAL".$x."PESO";
				$campoDesc = "EVAL".$x."DES";
				
				if($listaCompetencia[0]->$campo >0){
					//echo $listaCompetencia[0]->$campoDesc; exit;
					$datos[$x-1] = utf8_decode($listaCompetencia[0]->$campoDesc);
					
				}									
			}
			//print_r($datos); exit;
		}
		return $datos;
	}
			
			
			
    public function generarboletaunidades() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $valucod = $this->input->post("cbalumno");
        $vbimestre = $this->input->post("cbperiodo");
        $vunidad = $this->input->post("cbunidad");
        $vflgGen = $this->input->post("flgGenerar");
// ==============================================

        $vFlagCuantitativo = FALSE;
        //$vflgGen = 0; // 0 : Genera Boletas Online 1: Genera Boletas fisicas
        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, $valucod);
        if ($vflgGen == '0') {
            $this->pdf = new Pdf ();
        }
        foreach ($arraAlumnos as $alumno) {
            $arrApreAnual = array();
            $contdesapro = 0;
            if ($vflgGen == '1') {
                $this->pdf = new Pdf ();
            }

            if (($alumno->INSTRUCOD == 'P' || $alumno->INSTRUCOD == 'S') && $this->ano >= 2022) {
                $vFlagCuantitativo = TRUE;
            }

            $this->pdf->SetTopMargin(0.2);
            $this->pdf->SetTitle('BOLETA DE NOTAS -' . $this->ano);
            $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->AliasNbPages();
            $this->pdf->AddPage('P', 'A4');
# BLOQUE HEAD
            $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 180, 13, 'JPG', '');
// =============================================================================
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->SetXY(160, 20);
            $this->pdf->Cell(50, 3, utf8_decode('AÑO ESCOLAR ' . $this->ano), 0, 0, 'C');
# CREAMOS TITULO DE LA BOLETA
            $this->pdf->SetFont('Arial', 'B', 14);
            $this->pdf->SetXY(95, 22);
            $this->pdf->Cell(40, 3, utf8_decode("BOLETA DE INFORMACIÓN"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
            $this->pdf->Rect(29, 30, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 31);
            $this->pdf->Cell(28, 4, utf8_decode("ESTUDIANTE :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 31);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->NOMCOMP), 0, 0, 'L', TRUE);
            $cadNemodes = explode("-", $alumno->NEMODES);
# BLOQUE : DATOS DEL AULA
            $this->pdf->Rect(29, 36, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(30, 37);
            $this->pdf->Cell(28, 4, 'NIVEL             :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 37);
            $this->pdf->Cell(25, 4, trim($cadNemodes[0]), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(83, 37);
            $this->pdf->Cell(15, 4, 'GRADO :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(98, 37);
            $this->pdf->Cell(10, 4, utf8_decode($alumno->GRADOCOD . 'º'), 0, 0, 'C', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(108, 37);
            $this->pdf->Cell(14, 4, 'AULA :', 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(122, 37);
            $this->pdf->Cell(26, 4, utf8_decode(trim($cadNemodes[2])), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetXY(148, 37);
            $this->pdf->Cell(20, 4, utf8_decode('Nº ORDEN :'), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(168, 37);
            $this->pdf->Cell(15, 4, $alumno->NUMORD, 0, 0, 'C', TRUE);
# BLOQUE : DATOS DEL TUTOR
            $this->pdf->Rect(29, 42, 155, 6);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(30, 43);
            $this->pdf->Cell(28, 4, utf8_decode("TUTOR(A)      :"), 0, 0, 'L', TRUE);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->SetXY(58, 43);
            $this->pdf->Cell(125, 4, utf8_decode($alumno->PROFE), 0, 0, 'L', TRUE);

            $vFilaIni = 50;
            $vFilaIni2 = 60;

# BLOQUE : LISTADO DE SUB-AREAS
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetXY(20, $vFilaIni);
            $this->pdf->Cell(10, 15, utf8_decode('Nº'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(30, $vFilaIni);
            $this->pdf->Cell(50, 15, utf8_decode('SUB-ÁREAS'), 1, 0, 'C', TRUE);
			$this->pdf->SetFont('Arial', 'B', 5);
			$this->pdf->SetXY(80, $vFilaIni);
			$this->pdf->Cell(88, 5, 'LOGROS DE APRENDIZAJE POR COMPETENCIA', 1, 0, 'C', TRUE);
 
$vFilaIni +=5;
            $this->pdf->SetXY(80, $vFilaIni);
            $this->pdf->Cell(14, 5, 'UNIDAD', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(94, $vFilaIni);
            $this->pdf->Cell(8, 5, 'LOGRO', 1, 0, 'C', TRUE);
            // Validar para agrupar para Secundaria 3, 4 y 5

            $this->pdf->SetXY(80, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'I', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(87, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'II', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(94, $vFilaIni2);
            $this->pdf->Cell(8, 5, utf8_decode('I B'), 1, 0, 'C', TRUE);


            $this->pdf->SetXY(102, $vFilaIni);
            $this->pdf->Cell(14, 5, 'UNIDAD', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(116, $vFilaIni);
            $this->pdf->Cell(8, 5, 'LOGRO', 1, 0, 'C', TRUE);

            $this->pdf->SetXY(102, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'III', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(109, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'IV', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(116, $vFilaIni2);
            $this->pdf->Cell(8, 5, utf8_decode('II B'), 1, 0, 'C', TRUE);


            $this->pdf->SetXY(124, $vFilaIni);
            $this->pdf->Cell(14, 5, 'UNIDAD', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(138, $vFilaIni);
            $this->pdf->Cell(8, 5, 'LOGRO', 1, 0, 'C', TRUE);

            $this->pdf->SetXY(124, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'V', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(131, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'VI', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(138, $vFilaIni2);
            $this->pdf->Cell(8, 5, utf8_decode('III B'), 1, 0, 'C', TRUE);


            $this->pdf->SetXY(146, $vFilaIni);
            $this->pdf->Cell(14, 5, 'UNIDAD', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(160, $vFilaIni);
            $this->pdf->Cell(8, 5, 'LOGRO', 1, 0, 'C', TRUE);

            $this->pdf->SetXY(146, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'VII', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(153, $vFilaIni2);
            $this->pdf->Cell(7, 5, 'VIII', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(160, $vFilaIni2);
            $this->pdf->Cell(8, 5, utf8_decode('IV B'), 1, 0, 'C', TRUE);

			//$this->pdf->Rect(168, $vFilaIni-5, 10, 20, 'DF');
			$this->pdf->SetFont('Arial', 'B', 7);
			if($this->ano<=2022){
				$this->pdf->SetXY(168, $vFilaIni-5);
				$this->pdf->Cell(26, 5, 'PROM. FINAL', 'LRT', 0, 'C', TRUE);
			} else {
				$this->pdf->SetXY(168, $vFilaIni-5);
				$this->pdf->Cell(26, 5, 'LOGRO ANUAL', 'LRT', 0, 'C', TRUE);
			}
			if($this->ano>=2023){
				$this->pdf->SetXY(168, $vFilaIni);
				$this->pdf->Cell(26, 5, 'DE', 'LR', 0, 'C', TRUE);
			}            

            if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) {
                $this->pdf->SetXY(168, $vFilaIni2);
                $this->pdf->Cell(13, 5, 'CUANT', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(181, $vFilaIni2);
                $this->pdf->Cell(13, 5, 'CUAL', 1, 0, 'C', TRUE);
            } else {
				$this->pdf->SetXY(168, $vFilaIni2);
                $this->pdf->Cell(26, 5, 'COMPETENCIA', 'LRB', 0, 'C', TRUE);
			}
// ========================================================
            $dataCursos = $this->objSalon->getCursosSubAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
            $dataCursoOficial = $this->objSalon->getCursosAreas($alumno->INSTRUCOD, $alumno->GRADOCOD);
// ========================================================                        
            $this->pdf->SetFillColor(208, 222, 240);
            $yCurso = 65;
            $filaCurso = 1;
            $filaCursollenados = 0;
            $filaCursollenados2 = 0;
            $filaCursollenados3 = 0;
            $filaCursollenados4 = 0;
            $filaCursollenadosUnidad1 = 0;
            $filaCursollenadosUnidad2 = 0;
            $filaCursollenadosUnidad3 = 0;
            $filaCursollenadosUnidad4 = 0;
            $filaCursollenadosUnidad5 = 0;
            $filaCursollenadosUnidad6 = 0;
            $filaCursollenadosUnidad7 = 0;
            $filaCursollenadosUnidad8 = 0;
            $vPuntaje = 0;
            $vPuntaje2 = 0;
            $vPuntaje3 = 0;
            $vPuntaje4 = 0;
            $vPuntajeUnidad1 = 0;
            $vPuntajeUnidad2 = 0;
            $vPuntajeUnidad3 = 0;
            $vPuntajeUnidad4 = 0;
            $vPuntajeUnidad5 = 0;
            $vPuntajeUnidad6 = 0;
            $vPuntajeUnidad7 = 0;
            $vPuntajeUnidad8 = 0;
            $arrCurso = array();
            $arrCurso2 = array();
            $arrCurso3 = array();
            $arrCurso4 = array();
            $notaTIC1cuan = "";
            $notaTIC1cual = "";
            $notaTIC2cuan = "";
            $notaTIC2cual = "";
            $notaTIC3cuan = "";
            $notaTIC3cual = "";
            $notaTIC4cuan = "";
            $notaTIC4cual = "";
            $notaTIC5cuan = "";
            $notaTIC5cual = "";
            $notaTIC6cuan = "";
            $notaTIC6cual = "";
            $notaTIC7cuan = "";
            $notaTIC7cual = "";
            $notaTIC8cuan = "";
            $notaTIC8cual = "";

            $notaGAC1cuan = "";
            $notaGA1cual = "";
            $notaGA2cuan = "";
            $notaGA2cual = "";
            $notaGA3cuan = "";
            $notaGA3cual = "";
            $notaGA4cuan = "";
            $notaGA4cual = "";
            $notaGA5cuan = "";
            $notaGA5cual = "";
            $notaGA6cuan = "";
            $notaGA6cual = "";
            $notaGA7cuan = "";
            $notaGA7cual = "";
            $notaGA8cuan = "";
            $notaGA8cual = "";

            foreach ($dataCursos as $rowcur) {
                $vidcurso = $rowcur->cursocod;
                $dataNota = $this->objNota->getNotasxBimestreBoleta($alumno->ALUCOD, $vidcurso, $vbimestre, $vunidad);
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(20, $yCurso);
                $this->pdf->Cell(10, 5, (($filaCurso < 10) ? ('0' . $filaCurso) : $filaCurso), 1, 0, 'C', TRUE);
                $this->pdf->SetXY(30, $yCurso);
                $this->pdf->Cell(50, 5, utf8_decode($rowcur->cursocor), 1, 0, 'L', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);

# BLOQUE BIMESTRE 1        
                if ($vunidad >= 1) {

                    $this->pdf->SetXY(80, $yCurso);
                    $vnotaUnidad1 = $dataNota[0]->pb;
                    $vnotaCuali1 = $this->getCualitativo((int) $dataNota[0]->pb);
                    if ($vnotaUnidad1 > 10 && $vnotaUnidad1 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad1 = (($vnotaUnidad1 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad1 = (($vnotaUnidad1 > 0) ? (($vnotaUnidad1 < 10) ? ( $vnotaUnidad1) : $vnotaUnidad1) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad1, $alumno->INSTRUCOD) : $vnotaUnidad1, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad1 = "";
                    $vnotaCuali1 = "";
                    $this->pdf->SetXY(80, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                if ($vunidad >= 2) {
                    $this->pdf->SetXY(87, $yCurso);
                    $vnotaUnidad2 = $dataNota[1]->pb;
                    $vnotaCuali2 = $this->getCualitativo((int) $dataNota[1]->pb);
                    if ($vnotaUnidad2 > 10 && $vnotaUnidad2 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad2 = (($vnotaUnidad2 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad2 = (($vnotaUnidad2 > 0) ? (($vnotaUnidad2 < 10) ? ( $vnotaUnidad2) : $vnotaUnidad2) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad2, $alumno->INSTRUCOD) : $vnotaUnidad2, 1, 0, 'C', TRUE);
                } else {
                    $vnotaCuali2 = "";
                    $vnotaUnidad2 = "";
                    $this->pdf->SetXY(87, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }


                // PROMEDIO DE BIMESTRE 1
                $this->pdf->SetFillColor(208, 222, 240);
                if ($vunidad >= 2) {
                    $vnotaCuanti1 = round(($vnotaUnidad1 + $vnotaUnidad2) / 2);
                    $this->pdf->SetXY(94, $yCurso);
                    if ($vnotaCuanti1 > 10 && $vnotaCuanti1 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti1 = (($vnotaCuanti1 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti1 = (($vnotaCuanti1 > 0) ? (($vnotaCuanti1 < 10) ? ('0' . $vnotaCuanti1) : $vnotaCuanti1) : '');
                    }
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaCuanti1, $alumno->INSTRUCOD) : $vnotaCuanti1, 1, 0, 'C', TRUE);

                    //$this->pdf->SetFillColor(208, 222, 240);
                    //$this->pdf->SetXY(94, $yCurso);
                    //$this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                } else {
                    $vnotaCuanti1 = 0;
                    //$this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(94, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', FALSE);
                }



# ================================ BLOQUE DE NOTAS ======================================
# BLOQUE BIMESTRE 2
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vunidad >= 3) {
                    $this->pdf->SetXY(102, $yCurso);
                    $vnotaUnidad3 = $dataNota[2]->pb;
                    $vnotaCuali3 = $this->getCualitativo((int) $dataNota[2]->pb);
                    if ($vnotaUnidad3 > 10 && $vnotaUnidad3 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad3 = (($vnotaUnidad3 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad3 = (($vnotaUnidad3 > 0) ? (((int) $vnotaUnidad3 < 10) ? ('0' . (int) $vnotaUnidad3) : $vnotaUnidad3) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad3, $alumno->INSTRUCOD) : $vnotaUnidad3, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad3 = "";
                    $vnotaCuali3 = "";
                    $this->pdf->SetXY(102, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                if ($vunidad >= 4) {
                    $this->pdf->SetXY(109, $yCurso);
                    $vnotaUnidad4 = $dataNota[3]->pb;
                    $vnotaCuali4 = $this->getCualitativo((int) $dataNota[3]->pb);
                    if ($vnotaUnidad4 > 10 && $vnotaUnidad4 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad4 = (($vnotaUnidad4 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad4 = (($vnotaUnidad4 > 0) ? (((int) $vnotaUnidad4 < 10) ? ('0' . (int) $vnotaUnidad4) : $vnotaUnidad4) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad4, $alumno->INSTRUCOD) : $vnotaUnidad4, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad4 = "";
                    $vnotaCuali4 = "";
                    $this->pdf->SetXY(109, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                // PROMEDIO DE BIMESTRE 2
                $this->pdf->SetFillColor(208, 222, 240);
                if ($vunidad >= 4) {
                    $vnotaCuanti2 = round(($vnotaUnidad3 + $vnotaUnidad4) / 2);
                    $this->pdf->SetXY(116, $yCurso);
                    if ($vnotaCuanti2 > 10 && $vnotaCuanti2 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti2 = (($vnotaCuanti2 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti2 = (($vnotaCuanti2 > 0) ? (($vnotaCuanti2 < 10) ? ('0' . $vnotaCuanti2) : $vnotaCuanti2) : '');
                    }
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaCuanti2, $alumno->INSTRUCOD) : $vnotaCuanti2, 1, 0, 'C', TRUE);
                } else {
                    $vnotaCuanti2 = 0;
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(116, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }

# BLOQUE BIMESTRE 3
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vunidad >= 5) {
                    $this->pdf->SetXY(124, $yCurso);
                    $vnotaUnidad5 = $dataNota[4]->pb;
                    $vnotaCuali5 = $this->getCualitativo((int) $dataNota[4]->pb);
                    if ($vnotaUnidad5 > 10 && $vnotaUnidad5 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad5 = (($vnotaUnidad5 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad5 = (($vnotaUnidad5 > 0) ? (((int) $vnotaUnidad5 < 10) ? ('0' . (int) $vnotaUnidad5) : $vnotaUnidad5) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad5, $alumno->INSTRUCOD) : $vnotaUnidad5, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad5 = "";
                    $vnotaCuali5 = "";
                    $this->pdf->SetXY(124, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                if ($vunidad >= 6) {
                    $this->pdf->SetXY(131, $yCurso);
                    $vnotaUnidad6 = $dataNota[5]->pb;
                    $vnotaCuali6 = "";
                    if ($vnotaUnidad6 > 10 && $vnotaUnidad6 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad6 = (($vnotaUnidad6 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad6 = (($vnotaUnidad6 > 0) ? (((int) $vnotaUnidad6 < 10) ? ('0' . (int) $vnotaUnidad6) : $vnotaUnidad6) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad6, $alumno->INSTRUCOD) : $vnotaUnidad6, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad6 = "";
                    $vnotaCuali6 = "";
                    $this->pdf->SetXY(131, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                // PROMEDIO DE BIMESTRE 3 
                $this->pdf->SetFillColor(208, 222, 240);
                if ($vunidad >= 6) {
                    $vnotaCuanti3 = round(($vnotaUnidad5 + $vnotaUnidad6) / 2);
                    $this->pdf->SetXY(138, $yCurso);
                    if ($vnotaCuanti3 > 10 && $vnotaCuanti3 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti3 = (($vnotaCuanti3 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti3 = (($vnotaCuanti3 > 0) ? (($vnotaCuanti3 < 10) ? ('0' . $vnotaCuanti3) : $vnotaCuanti3) : '');
                    }
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaCuanti3, $alumno->INSTRUCOD) : $vnotaCuanti3, 1, 0, 'C', TRUE);
                } else {
                    $vnotaCuanti3 = 0;
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(138, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }


# BLOQUE BIMESTRE 4
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vunidad >= 7) {
                    $this->pdf->SetXY(146, $yCurso);
                    $vnotaUnidad7 = $dataNota[6]->pb;
                    $vnotaCuali7 = $this->getCualitativo((int) $dataNota[6]->pb);
                    if ($vnotaUnidad7 > 10 && $vnotaUnidad7 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad7 = (($vnotaUnidad7 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad7 = (($vnotaUnidad7 > 0) ? (((int) $vnotaUnidad7 < 10) ? ('0' . (int) $vnotaUnidad7) : $vnotaUnidad7) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad7, $alumno->INSTRUCOD) : $vnotaUnidad7, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad7 = "";
                    $vnotaCuali7 = "";
                    $this->pdf->SetXY(146, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                if ($vunidad >= 8) {
                    $this->pdf->SetXY(153, $yCurso);
                    $vnotaUnidad8 = $dataNota[7]->pb;
                    $vnotaCuali8 = $this->getCualitativo((int) $dataNota[7]->pb);
                    if ($vnotaUnidad8 > 10 && $vnotaUnidad8 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaUnidad8 = (($vnotaUnidad8 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaUnidad8 = (($vnotaUnidad8 > 0) ? (((int) $vnotaUnidad8 < 10) ? ('0' . (int) $vnotaUnidad8) : $vnotaUnidad8) : '');
                    }
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaUnidad8, $alumno->INSTRUCOD) : $vnotaUnidad8, 1, 0, 'C', TRUE);
                } else {
                    $vnotaUnidad8 = "";
                    $vnotaCuali8 = "";
                    $this->pdf->SetXY(153, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', FALSE);
                }

                // PROMEDIO DE BIMESTRE
                $this->pdf->SetFillColor(208, 222, 240);
                if ($vunidad >= 8) {
                    $vnotaCuanti4 = round(($vnotaUnidad7 + $vnotaUnidad8) / 2);
                    $this->pdf->SetXY(160, $yCurso);
                    if ($vnotaCuanti4 > 10 && $vnotaCuanti4 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vnotaCuanti4 = (($vnotaCuanti4 != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vnotaCuanti4 = (($vnotaCuanti4 > 0) ? (($vnotaCuanti4 < 10) ? ('0' . $vnotaCuanti4) : $vnotaCuanti4) : '');
                    }
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnotaCuanti4, $alumno->INSTRUCOD) : $vnotaCuanti4, 1, 0, 'C', TRUE);
                } else {
                    $vnotaCuanti4 = 0;
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(160, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }


# BLOQUE BIMESTRE 5
                if ($vunidad >= 8) {
                    $vpromFinalCuant = '';
                    $vpromFinalCual = '';
                    if ($vnotaCuanti1 != '' && $vnotaCuanti2 != '' && $vnotaCuanti3 != '' && $vnotaCuanti4 != '') {
                        $vpromFinalCuant = round((($vnotaCuanti1 + $vnotaCuanti2 + $vnotaCuanti3 + $vnotaCuanti4) / 4), 0);
                        $vpromFinalCual = $this->getCualitativoStandar((int) $vpromFinalCuant, $alumno->INSTRUCOD);
                    }
                    if ($vpromFinalCuant > 10 && $vpromFinalCuant <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano <=2022) || $this->ano >=2023)
                        $ancho = 26;
                    else
                        $ancho = 13;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vpromFinalCuant = (($vpromFinalCuant != '') ? 'EXO' : 'EXO');
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $vpromFinalCuant = (($vpromFinalCuant > 0) ? (((int) $vpromFinalCuant < 10) ? ('0' . (int) $vpromFinalCuant) : $vpromFinalCuant) : '');
                    }
					
					if($this->ano <=2022){
						$this->pdf->SetFillColor(208, 222, 240);
						$this->pdf->Cell($ancho, 5, ($alumno->INSTRUCOD == 'P')?'-':$vpromFinalCuant, 1, 0, 'C', TRUE);
					}
					
                    if ($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano <=2022) {
                        // SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
                    } else {
                        if ($vpromFinalCual === 'A' || $vpromFinalCual === 'AD' || $vpromFinalCual === 'B') {
                            $this->pdf->SetTextColor(0, 0, 204);
                        } else {
                            $this->pdf->SetTextColor(255, 0, 51);
                        }
						
						if($this->ano <=2022){
							$this->pdf->SetXY(181, $yCurso);
						} else {
							$this->pdf->SetXY(168, $yCurso);
						}
						
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                            $vpromFinalCual = (($vpromFinalCual != '') ? 'EXO' : 'EXO');
                            $this->pdf->SetTextColor(0, 0, 204);
                            $this->pdf->Cell($ancho, 5, $vpromFinalCual, 1, 0, 'C', TRUE);
                        } else {
                            $this->pdf->Cell($ancho, 5, $vpromFinalCual, 1, 0, 'C', TRUE);
                        }
                    }
                } else {
                    $vnotaCuanti4 = 0;
                    if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano <=2022) || $this->ano >=2023) {
                        $this->pdf->SetFillColor(208, 222, 240);
                        $this->pdf->SetXY(168, $yCurso);
                        $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                    } else {
                        $this->pdf->SetFillColor(208, 222, 240);
                        $this->pdf->SetXY(168, $yCurso);
                        $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                        $this->pdf->SetXY(181, $yCurso);
                        $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    }
                }

                if (($alumno->INSTRUCOD === "P" && $this->ano < 2022) || $this->ano >= 2022) {
                    if ($rowcur->cursocod === "21" && $vunidad >= 1) { // Si es Curso COMPUTACION
                        $notaTIC1cuan = $vnotaUnidad1;
                        $notaTIC1cual = $vnotaCuali1;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 2) { // Si es Curso COMPUTACION
                        $notaTIC2cuan = $vnotaUnidad2;
                        $notaTIC2cual = $vnotaCuali2;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 3) { // Si es Curso COMPUTACION
                        $notaTIC3cuan = $vnotaUnidad3;
                        $notaTIC3cual = $vnotaCuali3;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 4) { // Si es Curso COMPUTACION
                        $notaTIC4cuan = $vnotaUnidad4;
                        $notaTIC4cual = $vnotaCuali4;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 5) { // Si es Curso COMPUTACION
                        $notaTIC5cuan = $vnotaUnidad5;
                        $notaTIC5cual = $vnotaCuali5;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 6) { // Si es Curso COMPUTACION
                        $notaTIC6cuan = $vnotaUnidad6;
                        $notaTIC6cual = $vnotaCuali6;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 7) { // Si es Curso COMPUTACION
                        $notaTIC7cuan = $vnotaUnidad7;
                        $notaTIC7cual = $vnotaCuali7;
                    }
                    if ($rowcur->cursocod === "21" && $vunidad >= 8) { // Si es Curso COMPUTACION
                        $notaTIC8cuan = $vnotaUnidad8;
                        $notaTIC8cual = $vnotaCuali8;
                    }
                }
// =========== Para acumular arreglos de cursos oficiales =========
                $arrCurso["cursos1"][] = $vnotaCuanti1;
                $arrCurso["cursos2"][] = $vnotaCuanti2;
                $arrCurso["cursos3"][] = $vnotaCuanti3;
                $arrCurso["cursos4"][] = $vnotaCuanti4;
// ======= Acumuladores para promedios y puntajes ===========
                if ($vbimestre >= 1) {
                    if ($vunidad >= 1 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad1 += (int) $vnotaUnidad1;
                        if ((int) $vPuntajeUnidad1 > 0) {
                            $filaCursollenadosUnidad1++;
                        }
                    }
                    if ($vunidad >= 2 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad2 += (int) $vnotaUnidad2;
                        if ((int) $vPuntajeUnidad2 > 0) {
                            $filaCursollenadosUnidad2 ++;
                        }
                    }

                    $vPuntaje += (int) $vnotaCuanti1;
                    if ((int) $vPuntaje > 0) {
                        $filaCursollenados++;
                    }
                }
                if ($vbimestre >= 2) {
                    if ($vunidad >= 3 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad3 += (int) $vnotaUnidad3;
                        if ((int) $vPuntajeUnidad3 > 0) {
                            $filaCursollenadosUnidad3++;
                        }
                    }

                    if ($vunidad >= 4 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad4 += (int) $vnotaUnidad4;
                        if ((int) $vPuntajeUnidad4 > 0) {
                            $filaCursollenadosUnidad4++;
                        }
                    }

                    $vPuntaje2 += (int) $vnotaCuanti2;
                    if ((int) $vPuntaje2 > 0) {
                        $filaCursollenados2++;
                    }
                }
                if ($vbimestre >= 3) {
                    if ($vunidad >= 5 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad5 += (int) $vnotaUnidad5;
                        if ((int) $vPuntajeUnidad5 > 0) {
                            $filaCursollenadosUnidad5++;
                        }
                    }

                    if ($vunidad >= 6 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad6 += (int) $vnotaUnidad6;
                        if ((int) $vPuntajeUnidad6 > 0) {
                            $filaCursollenadosUnidad6++;
                        }
                    }

                    $vPuntaje3 += (int) $vnotaCuanti3;
                    if ((int) $vPuntaje3 > 0) {
                        $filaCursollenados3++;
                    }
                }
                if ($vbimestre >= 4) {

                    if ($vunidad >= 7 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad7 += (int) $vnotaUnidad7;
                        if ((int) $vPuntajeUnidad7 > 0) {
                            $filaCursollenadosUnidad7++;
                        }
                    }

                    if ($vunidad >= 8 && $alumno->INSTRUCOD === "S" || $alumno->INSTRUCOD === "P") {
                        $vPuntajeUnidad8 += (int) $vnotaUnidad8;
                        if ((int) $vPuntajeUnidad8 > 0) {
                            $filaCursollenadosUnidad8++;
                        }
                    }

                    $vPuntaje4 += (int) $vnotaCuanti4;
                    if ((int) $vPuntaje4 > 0) {
                        $filaCursollenados4++;
                    }
                }
// ========================================================
                $yCurso += 5;
                $filaCurso++;
            } // -*- Fin FOR -*-

            if ($alumno->INSTRUCOD === "S" && $this->ano < 2022) {
                if ($vunidad >= 1) {
                    $notaTIC1cuan = (($filaCursollenadosUnidad1 > 0) ? (round($vPuntajeUnidad1 / $filaCursollenadosUnidad1)) : '');
                    $notaTIC1cual = $this->getCualitativo((int) $notaTIC1cuan);
                }
                if ($vunidad >= 2) {
                    $notaTIC2cuan = (($filaCursollenadosUnidad2 > 0) ? (round($vPuntajeUnidad2 / $filaCursollenadosUnidad2)) : '');
                    $notaTIC2cual = $this->getCualitativo((int) $notaTIC2cuan);
                }
                if ($vunidad >= 3) {
                    $notaTIC3cuan = (($filaCursollenadosUnidad3 > 0) ? (round($vPuntajeUnidad3 / $filaCursollenadosUnidad3)) : '');
                    $notaTIC3cual = $this->getCualitativo((int) $notaTIC3cuan);
                }
                if ($vunidad >= 4) {
                    $notaTIC4cuan = (($filaCursollenadosUnidad4 > 0) ? (round($vPuntajeUnidad4 / $filaCursollenadosUnidad4)) : '');
                    $notaTIC4cual = $this->getCualitativo((int) $notaTIC4cuan);
                }
                if ($vunidad >= 5) {
                    $notaTIC5cuan = (($filaCursollenadosUnidad5 > 0) ? (round($vPuntajeUnidad5 / $filaCursollenadosUnidad5)) : '');
                    $notaTIC5cual = $this->getCualitativo((int) $notaTIC5cuan);
                }
                if ($vunidad >= 6) {
                    $notaTIC6cuan = (($filaCursollenadosUnidad6 > 0) ? (round($vPuntajeUnidad6 / $filaCursollenadosUnidad6)) : '');
                    $notaTIC6cual = $this->getCualitativo((int) $notaTIC6cuan);
                }
                if ($vunidad >= 7) {
                    $notaTIC7cuan = (($filaCursollenadosUnidad7 > 0) ? (round($vPuntajeUnidad7 / $filaCursollenadosUnidad7)) : '');
                    $notaTIC7cual = $this->getCualitativo((int) $notaTIC7cuan);
                }
                if ($vunidad >= 8) {
                    $notaTIC8cuan = (($filaCursollenadosUnidad8 > 0) ? (round($vPuntajeUnidad8 / $filaCursollenadosUnidad8)) : '');
                    $notaTIC8cual = $this->getCualitativo((int) $notaTIC8cuan);
                }
            }

            // GA PARA PRIMARIA Y SECUNDARIA
            if ($this->ano <= 2023) {

                if ($vunidad >= 1) {
                    $notaGA1cuan = (($filaCursollenadosUnidad1 > 0) ? (round($vPuntajeUnidad1 / $filaCursollenadosUnidad1)) : '');
                    $notaGA1cual = $this->getCualitativo((int) $notaGA1cuan);
                }
                if ($vunidad >= 2) {
                    $notaGA2cuan = (($filaCursollenadosUnidad2 > 0) ? (round($vPuntajeUnidad2 / $filaCursollenadosUnidad2)) : '');
                    $notaGA2cual = $this->getCualitativo((int) $notaGA2cuan);
                }
                if ($vunidad >= 3) {
                    $notaGA3cuan = (($filaCursollenadosUnidad3 > 0) ? (round($vPuntajeUnidad3 / $filaCursollenadosUnidad3)) : '');
                    $notaGA3cual = $this->getCualitativo((int) $notaGA3cuan);
                }
                if ($vunidad >= 4) {
                    $notaGA4cuan = (($filaCursollenadosUnidad4 > 0) ? (round($vPuntajeUnidad4 / $filaCursollenadosUnidad4)) : '');
                    $notaGA4cual = $this->getCualitativo((int) $notaGA4cuan);
                }
                if ($vunidad >= 5) {
                    $notaGA5cuan = (($filaCursollenadosUnidad5 > 0) ? (round($vPuntajeUnidad5 / $filaCursollenadosUnidad5)) : '');
                    $notaGA5cual = $this->getCualitativo((int) $notaGA5cuan);
                }
                if ($vunidad >= 6) {
                    $notaGA6cuan = (($filaCursollenadosUnidad6 > 0) ? (round($vPuntajeUnidad6 / $filaCursollenadosUnidad6)) : '');
                    $notaGA6cual = $this->getCualitativo((int) $notaGA6cuan);
                }
                if ($vunidad >= 7) {
                    $notaGA7cuan = (($filaCursollenadosUnidad7 > 0) ? (round($vPuntajeUnidad7 / $filaCursollenadosUnidad7)) : '');
                    $notaGA7cual = $this->getCualitativo((int) $notaGA7cuan);
                }
                if ($vunidad >= 8) {
                    $notaGA8cuan = (($filaCursollenadosUnidad8 > 0) ? (round($vPuntajeUnidad8 / $filaCursollenadosUnidad8)) : '');
                    $notaGA8cual = $this->getCualitativo((int) $notaGA8cuan);
                }
            }
# ================================ BLOQUE DE CONDUCTA ===================================
            $dataConducta = $this->objNota->getNotasConducta($alumno->ALUCOD, $vbimestre, $vunidad);
            if ($vunidad >= 1) {
                $notacondCuan1 = $dataConducta[0]->pb;
                $notacondCual1 = $this->getCualitativo($notacondCuan1);
            }
            if ($vunidad >= 2) {
                $notacondCuan2 = $dataConducta[1]->pb;
                $notacondCual2 = $this->getCualitativo($notacondCuan2);
            }
            if ($vunidad >= 3) {
                $notacondCuan3 = $dataConducta[2]->pb;
                $notacondCual3 = $this->getCualitativo($notacondCuan3);
            }
            if ($vunidad >= 4) {
                $notacondCuan4 = $dataConducta[3]->pb;
                $notacondCual4 = $this->getCualitativo($notacondCuan4);
            }
            if ($vunidad >= 5) {
                $notacondCuan5 = $dataConducta[4]->pb;
                $notacondCual5 = $this->getCualitativo($notacondCuan5);
            }
            if ($vunidad >= 6) {
                $notacondCuan6 = $dataConducta[5]->pb;
                $notacondCual6 = $this->getCualitativo($notacondCuan6);
            }
            if ($vunidad >= 7) {
                $notacondCuan7 = $dataConducta[6]->pb;
                $notacondCual7 = $this->getCualitativo($notacondCuan7);
            }
            if ($vunidad >= 8) {
                $notacondCuan8 = $dataConducta[7]->pb;
                $notacondCual8 = $this->getCualitativo($notacondCuan8);
            }


            $yCurso += 2;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(60, 5, utf8_decode('SE DESENVUELVE EN ENTORNOS VIRTUALES - TIC'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);

            if ($notaTIC1cuan > 10 && $notaTIC1cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetXY(80, $yCurso);
            $vnota1 = (($notaTIC1cuan < 10) ? '0' . $notaTIC1cuan : $notaTIC1cuan);
            $vnota1 = (($vnota1 > 0) ? $vnota1 : '');
            $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota1, $alumno->INSTRUCOD) : $vnota1, 1, 0, 'C', TRUE);

            // SEGUNDA UNIDAD
            if ($vunidad >= 2) {
                if ($notaTIC2cuan > 10 && $notaTIC2cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(87, $yCurso);
                $vnota2 = (($notaTIC2cuan < 10) ? '0' . $notaTIC2cuan : $notaTIC2cuan);
                $vnota2 = (($vnota2 > 0) ? $vnota2 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota2, $alumno->INSTRUCOD) : $vnota2, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // PROMEDIO DEL BIMESTRE 1
            if ($vunidad >= 2) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromUnidadTIC = round(($vnota1 + $vnota2) / 2);
                if ($vpromUnidadTIC > 10 && $vpromUnidadTIC <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(94, $yCurso);
                $vpromUnidadTIC = (($vpromUnidadTIC > 0) ? $vpromUnidadTIC : '');
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC, $alumno->INSTRUCOD) : $vpromUnidadTIC, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(94, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }


# BLOQUE BIMESTRE 2
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($vunidad >= 3) {
                if ($notaTIC3cuan > 10 && $notaTIC3cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(102, $yCurso);
                $vnota3 = (($notaTIC3cuan < 10) ? '0' . $notaTIC3cuan : $notaTIC3cuan);
                $vnota3 = (($vnota3 > 0) ? $vnota3 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota3, $alumno->INSTRUCOD) : $vnota3, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(102, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // CUARTA UNIDAD
            if ($vunidad >= 4) {
                if ($notaTIC4cuan > 10 && $notaTIC4cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(109, $yCurso);
                $vnota4 = (($notaTIC4cuan < 10) ? '0' . $notaTIC4cuan : $notaTIC4cuan);
                $vnota4 = (($vnota4 > 0) ? $vnota4 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota4, $alumno->INSTRUCOD) : $vnota4, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(109, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }


            // PROMEDIO DEL BIMESTRE 2
            if ($vunidad >= 4) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromUnidadTIC2 = round(($vnota3 + $vnota4) / 2);
                if ($vpromUnidadTIC2 > 10 && $vpromUnidadTIC2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(116, $yCurso);
                $vpromUnidadTIC2 = (($vpromUnidadTIC2 > 0) ? $vpromUnidadTIC2 : '');
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC2, $alumno->INSTRUCOD) : $vpromUnidadTIC2, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(116, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }

# BLOQUE BIMESTRE 3
            // QUINTA UNIDAD
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($vunidad >= 5) {
                if ($notaTIC5cuan > 10 && $notaTIC5cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(124, $yCurso);
                $vnota5 = (($notaTIC5cuan < 10) ? '0' . $notaTIC5cuan : $notaTIC5cuan);
                $vnota5 = (($vnota5 > 0) ? $vnota5 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota5, $alumno->INSTRUCOD) : $vnota5, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(124, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // SEXTA UNIDAD
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($vunidad >= 6) {
                if ($notaTIC6cuan > 10 && $notaTIC6cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(131, $yCurso);
                $vnota6 = (($notaTIC6cuan < 10) ? '0' . $notaTIC6cuan : $notaTIC6cuan);
                $vnota6 = (($vnota6 > 0) ? $vnota6 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota6, $alumno->INSTRUCOD) : $vnota6, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(131, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // PROMEDIO DEL BIMESTRE 3
            if ($vunidad >= 6) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromUnidadTIC3 = round(($vnota5 + $vnota6) / 2);
                if ($vpromUnidadTIC3 > 10 && $vpromUnidadTIC3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(138, $yCurso);
                $vpromUnidadTIC3 = (($vpromUnidadTIC3 > 0) ? $vpromUnidadTIC3 : '');
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC3, $alumno->INSTRUCOD) : $vpromUnidadTIC3, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(138, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }

# BLOQUE BIMESTRE 4
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($vunidad >= 7) {
                if ($notaTIC7cuan > 10 && $notaTIC7cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(146, $yCurso);
                $vnota7 = (($notaTIC7cuan < 10) ? '0' . $notaTIC7cuan : $notaTIC7cuan);
                $vnota7 = (($vnota7 > 0) ? $vnota7 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota7, $alumno->INSTRUCOD) : $vnota7, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(146, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // OCTAVA UNIDAD
            if ($vunidad >= 8) {
                if ($notaTIC8cuan > 10 && $notaTIC8cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(153, $yCurso);
                $vnota8 = (($notaTIC8cuan < 10) ? '0' . $notaTIC8cuan : $notaTIC8cuan);
                $vnota8 = (($vnota8 > 0) ? $vnota8 : '');
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vnota8, $alumno->INSTRUCOD) : $vnota8, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(153, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // PROMEDIO DEL BIMESTRE
            if ($vunidad >= 8) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromUnidadTIC4 = round(($vnota7 + $vnota8) / 2);
                if ($vpromUnidadTIC4 > 10 && $vpromUnidadTIC4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(160, $yCurso);
                $vpromUnidadTIC4 = (($vpromUnidadTIC4 < 10) ? '0' . $vpromUnidadTIC4 : $vpromUnidadTIC4);
                $vpromUnidadTIC4 = (($vpromUnidadTIC4 > 0) ? $vpromUnidadTIC4 : '');
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC4, $alumno->INSTRUCOD) : $vpromUnidadTIC4, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(160, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }

# BLOQUE BIMESTRE 5

            if ($vunidad >= 8) {
                $vpromFinalTICCuant = '';
                $vpromFinalTICCual = '';
                if ($vpromUnidadTIC != '' && $vpromUnidadTIC2 != '' && $vpromUnidadTIC3 != '' && $vpromUnidadTIC4 != '') {
                    $vpromFinalTICCuant = round((($vpromUnidadTIC + $vpromUnidadTIC2 + $vpromUnidadTIC3 + $vpromUnidadTIC4) / 4), 0);
                    $vpromFinalTICCual = $this->getCualitativoStandar((int) $vpromFinalTICCuant, $alumno->INSTRUCOD);
                }
                if ($vpromFinalTICCuant > 10 && $vpromFinalTICCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) || $this->ano>=2023 )
                    $ancho = 26;
                else
                    $ancho = 13;
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                    $vpromFinalTICCuant = (($vpromFinalTICCuant != '') ? 'EXO' : 'EXO');
                } else {
                    $vpromFinalTICCuant = (($vpromFinalTICCuant > 0) ? (((int) $vpromFinalTICCuant < 10) ? ('0' . (int) $vpromFinalTICCuant) : $vpromFinalTICCuant) : '');
                }

				if($this->ano <=2022){
					$this->pdf->SetFillColor(208, 222, 240);
					$this->pdf->Cell($ancho, 5,($alumno->INSTRUCOD == 'P')?'-':$vpromFinalTICCuant, 1, 0, 'C', TRUE);
				}
				
				if($this->ano <=2022) {
					if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
						// SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
					} else {
						if ($vpromFinalTICCual === 'A' || $vpromFinalTICCual === 'AD' || $vpromFinalTICCual === 'B') {
							$this->pdf->SetTextColor(0, 0, 204);
						} else {
							$this->pdf->SetTextColor(255, 0, 51);
						}
						
						if($this->ano <=2022){
							$this->pdf->SetXY(181, $yCurso);
						} else {
							$this->pdf->SetXY(168, $yCurso);
						}
						$this->pdf->Cell($ancho, 5, $vpromFinalTICCual, 1, 0, 'C', TRUE);
					}
				} else {
						if ($vpromFinalTICCual === 'A' || $vpromFinalTICCual === 'AD' || $vpromFinalTICCual === 'B') {
							$this->pdf->SetTextColor(0, 0, 204);
						} else {
							$this->pdf->SetTextColor(255, 0, 51);
						}
						
						if($this->ano <=2022){
							$this->pdf->SetXY(181, $yCurso);
						} else {
							$this->pdf->SetXY(168, $yCurso);
						}
						$this->pdf->Cell($ancho, 5, $vpromFinalTICCual, 1, 0, 'C', TRUE);					
				}
            } else {
                if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) || $this->ano>=2023 ) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(181, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                }
            }


            $yCurso += 5;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(60, 5, utf8_decode('GESTIONA SU APRENDIZAJE DE MANERA AUTÓNOMA'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);

            if ($notaGA1cuan > 10 && $notaGA1cuan <= 20) {
                $this->pdf->SetTextColor(0, 0, 204);
            } else {
                $this->pdf->SetTextColor(255, 0, 51);
            }
            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetXY(80, $yCurso);
            $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA1cuan, $alumno->INSTRUCOD) : $notaGA1cuan, 1, 0, 'C', TRUE);

            // SEGUNDA UNIDAD
            if ($vunidad >= 2) {
                if ($notaGA2cuan > 10 && $notaGA2cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(87, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA2cuan, $alumno->INSTRUCOD) : $notaGA2cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(87, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // PROMEDIO DEL BIMESTRE
            if ($vunidad >= 2) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromGABimestre1 = round(($notaGA1cuan + $notaGA2cuan) / 2);
                if ($vpromGABimestre1 > 10 && $vpromGABimestre1 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(94, $yCurso);
                $vpromGABimestre1 = (($vpromGABimestre1 > 0 && $vpromGABimestre1 < 10) ? '0' . $vpromGABimestre1 : $vpromGABimestre1);
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre1, $alumno->INSTRUCOD) : $vpromGABimestre1, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(94, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }
# BLOQUE BIMESTRE 2
            if ($vunidad >= 3) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notaGA3cuan > 10 && $notaGA3cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(102, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA3cuan, $alumno->INSTRUCOD) : $notaGA3cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(102, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }
            // UNIDAD 4
            if ($vunidad >= 4) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notaGA4cuan > 10 && $notaGA4cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(109, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA4cuan, $alumno->INSTRUCOD) : $notaGA4cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(109, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }
            // PROMEDIO DE BIMESTRE 2
            if ($vunidad >= 4) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromGABimestre2 = round(($notaGA3cuan + $notaGA4cuan) / 2);
                if ($vpromGABimestre2 > 10 && $vpromGABimestre2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(116, $yCurso);
                $vpromGABimestre2 = (($vpromGABimestre2 > 0 && $vpromGABimestre2 < 10) ? '0' . $vpromGABimestre2 : $vpromGABimestre2);
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre2, $alumno->INSTRUCOD) : $vpromGABimestre2, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(116, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }

# BLOQUE BIMESTRE 3
            if ($vunidad >= 5) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notaGA5cuan > 10 && $notaGA5cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(124, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA5cuan, $alumno->INSTRUCOD) : $notaGA5cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(124, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }
            // UNIDAD 6
            if ($vunidad >= 6) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notaGA6cuan > 10 && $notaGA6cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(131, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA6cuan, $alumno->INSTRUCOD) : $notaGA6cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(131, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // PROMEDIO DE BIMESTRE 3
            if ($vunidad >= 6) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromGABimestre3 = round(($notaGA5cuan + $notaGA6cuan) / 2);
                if ($vpromGABimestre3 > 10 && $vpromGABimestre3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(138, $yCurso);
                $vpromGABimestre3 = (($vpromGABimestre3 > 0 && $vpromGABimestre3 < 10) ? '0' . $vpromGABimestre3 : $vpromGABimestre3);
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre3, $alumno->INSTRUCOD) : $vpromGABimestre3, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(138, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }

# BLOQUE BIMESTRE 4
            if ($vunidad >= 7) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notaGA7cuan > 10 && $notaGA7cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(146, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA7cuan, $alumno->INSTRUCOD) : $notaGA7cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(146, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }
            // UNIDAD 8
            if ($vunidad >= 8) {
                $this->pdf->SetFont('Arial', '', 7);
                if ($notaGA8cuan > 10 && $notaGA8cuan <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(153, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($notaGA8cuan, $alumno->INSTRUCOD) : $notaGA8cuan, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(153, $yCurso);
                $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
            }

            // PROMEDIO DE UNIDAD
            if ($vunidad >= 8) {
                $this->pdf->SetFillColor(208, 222, 240);
                $vpromGABimestre4 = round(($notaGA7cuan + $notaGA8cuan) / 2);
                if ($vpromGABimestre4 > 10 && $vpromGABimestre4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(160, $yCurso);
                $vpromGABimestre4 = (($vpromGABimestre4 > 0 && $vpromGABimestre4 < 10) ? '0' . $vpromGABimestre4 : $vpromGABimestre4);
                $this->pdf->Cell(8, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre4, $alumno->INSTRUCOD) : $vpromGABimestre4, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(160, $yCurso);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            }

# BLOQUE BIMESTRE 5
            if ($vunidad >= 8) {
                $vpromFinalGACuant = '';
                $vpromFinalGACual = '';
                if ($vpromGABimestre1 != '' && $vpromGABimestre2 != '' && $vpromGABimestre3 != '' && $vpromGABimestre4 != '') {
                    $vpromFinalGACuant = round((($vpromGABimestre1 + $vpromGABimestre2 + $vpromGABimestre3 + $vpromGABimestre4) / 4), 0);
                    $vpromFinalGACual = $this->getCualitativoStandar((int) $vpromFinalGACuant, $alumno->INSTRUCOD);
                }
                if ($vpromFinalGACuant > 10 && $vpromFinalGACuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) || $this->ano>=2023 )
                    $ancho = 26;
                else
                    $ancho = 13;
                if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                    $vpromFinalGACuant = (($vpromFinalGACuant != '') ? 'EXO' : 'EXO');
                } else {
                    $vpromFinalGACuant = (($vpromFinalGACuant > 0) ? (((int) $vpromFinalGACuant < 10) ? ('0' . (int) $vpromFinalGACuant) : $vpromFinalGACuant) : '');
                }

				if($this->ano <=2022){
					$this->pdf->SetFillColor(208, 222, 240);
					$this->pdf->Cell($ancho, 5,($alumno->INSTRUCOD == 'P')?'-': $vpromFinalGACuant, 1, 0, 'C', TRUE);
				}
				if($this->ano <=2022) {
					if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
						// SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
					} else {
						if ($vpromFinalGACual === 'A' || $vpromFinalGACual === 'AD' || $vpromFinalGACual === 'B') {
							$this->pdf->SetTextColor(0, 0, 204);
						} else {
							$this->pdf->SetTextColor(255, 0, 51);
						}
						
						if($this->ano <=2022){
						 $this->pdf->SetXY(181, $yCurso);
						} else {
							$this->pdf->SetXY(168, $yCurso);
						}
						$this->pdf->Cell($ancho, 5, $vpromFinalGACual, 1, 0, 'C', TRUE);
					}
				} else {
						if ($vpromFinalGACual === 'A' || $vpromFinalGACual === 'AD' || $vpromFinalGACual === 'B') {
							$this->pdf->SetTextColor(0, 0, 204);
						} else {
							$this->pdf->SetTextColor(255, 0, 51);
						}
						
						if($this->ano <=2022){
						 $this->pdf->SetXY(181, $yCurso);
						} else {
							$this->pdf->SetXY(168, $yCurso);
						}
						$this->pdf->Cell($ancho, 5, $vpromFinalGACual, 1, 0, 'C', TRUE);					
				}
            } else {
                if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) || $this->ano>=2023 ) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(181, $yCurso);
                    $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                }
            }


            // Bloque de conducta >=2022
            if ($this->ano >= 2022) {
                $yCurso += 5;
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetFont('Arial', 'B', 6);
                $this->pdf->SetXY(20, $yCurso);
                $this->pdf->Cell(60, 5, utf8_decode(($this->ano>=2023 && $alumno->INSTRUCOD=='S' ) ? 'CONDUCTA' : 'COMPORTAMIENTO'), 1, 0, 'L', TRUE);

                $this->pdf->SetFont('Arial', '', 7);

                if ($notacondCuan1 > 10 && $notacondCuan1 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(80, $yCurso);
                $this->pdf->Cell(7, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan1, $alumno->INSTRUCOD) : $notacondCuan1, 1, 0, 'C', TRUE);

                // SEGUNDA UNIDAD
                if ($vunidad >= 2) {
                    if ($notacondCuan2 > 10 && $notacondCuan2 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(87, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo  && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan2, $alumno->INSTRUCOD) : $notacondCuan2, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(87, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }

                // PROMEDIO DEL BIMESTRE 1
                if ($vunidad >= 2) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $vpromCondBimestre1 = round(($notacondCuan1 + $notacondCuan2) / 2);
                    if ($vpromCondBimestre1 > 10 && $vpromCondBimestre1 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(94, $yCurso);
                    $vpromCondBimestre1 = (($vpromCondBimestre1 > 0 && $vpromCondBimestre1 < 10) ? '0' . $vpromCondBimestre1 : $vpromCondBimestre1);
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo   && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($vpromCondBimestre1, $alumno->INSTRUCOD) : $vpromCondBimestre1, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(94, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }
# BLOQUE BIMESTRE 2
                if ($vunidad >= 3) {
                    $this->pdf->SetFont('Arial', '', 7);
                    if ($notacondCuan3 > 10 && $notacondCuan3 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(102, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo  && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan3, $alumno->INSTRUCOD) : $notacondCuan3, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(102, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }
                // UNIDAD 4
                if ($vunidad >= 4) {
                    $this->pdf->SetFont('Arial', '', 7);
                    if ($notacondCuan4 > 10 && $notacondCuan4 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(109, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan4, $alumno->INSTRUCOD) : $notacondCuan4, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetXY(109, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }
                // PROMEDIO DE BIMESTRE 2
                if ($vunidad >= 4) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $vpromCondBimestre2 = round(($notacondCuan3 + $notacondCuan4) / 2);
                    if ($vpromCondBimestre2 > 10 && $vpromCondBimestre2 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(116, $yCurso);
                    $vpromCondBimestre2 = (($vpromCondBimestre2 > 0 && $vpromCondBimestre2 < 10) ? '0' . $vpromCondBimestre2 : $vpromCondBimestre2);
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo  && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($vpromCondBimestre2, $alumno->INSTRUCOD) : $vpromCondBimestre2, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(116, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }

# BLOQUE BIMESTRE 3
                if ($vunidad >= 5) {
                    $this->pdf->SetFont('Arial', '', 7);
                    if ($notacondCuan5 > 10 && $notacondCuan5 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(124, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan5, $alumno->INSTRUCOD) : $notacondCuan5, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(124, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }
                // UNIDAD 6
                if ($vunidad >= 6) {
                    $this->pdf->SetFont('Arial', '', 7);
                    if ($notacondCuan6 > 10 && $notacondCuan6 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(131, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan6, $alumno->INSTRUCOD) : $notacondCuan6, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(131, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }

                // PROMEDIO DE BIMESTRE 3
                if ($vunidad >= 6) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $vpromCondBimestre3 = round(($notacondCuan5 + $notacondCuan6) / 2);
                    if ($vpromCondBimestre3 > 10 && $vpromCondBimestre3 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(138, $yCurso);
                    $vpromCondBimestre3 = (($vpromCondBimestre3 > 0 && $vpromCondBimestre3 < 10) ? '0' . $vpromCondBimestre3 : $vpromCondBimestre3);
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($vpromCondBimestre3, $alumno->INSTRUCOD) : $vpromCondBimestre3, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(138, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }

# BLOQUE BIMESTRE 4
                if ($vunidad >= 7) {
                    $this->pdf->SetFont('Arial', '', 7);
                    if ($notacondCuan7 > 10 && $notacondCuan7 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(146, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan7, $alumno->INSTRUCOD) : $notacondCuan7, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(146, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }
                // UNIDAD 8
                if ($vunidad >= 8) {
                    $this->pdf->SetFont('Arial', '', 7);
                    if ($notacondCuan8 > 10 && $notacondCuan8 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(153, $yCurso);
                    $this->pdf->Cell(7, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($notacondCuan8, $alumno->INSTRUCOD) : $notacondCuan8, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(153, $yCurso);
                    $this->pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
                }

                // PROMEDIO DE UNIDAD
                if ($vunidad >= 8) {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $vpromCondBimestre4 = round(($notacondCuan7 + $notacondCuan8) / 2);
                    if ($vpromCondBimestre4 > 10 && $vpromCondBimestre4 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(160, $yCurso);
                    $vpromCondBimestre4 = (($vpromCondBimestre4 > 0 && $vpromCondBimestre4 < 10) ? '0' . $vpromCondBimestre4 : $vpromCondBimestre4);
                    $this->pdf->Cell(8, 5, ($vFlagCuantitativo && $alumno->INSTRUCOD=='P') ? $this->getCualitativoStandar($vpromCondBimestre4, $alumno->INSTRUCOD) : $vpromCondBimestre4, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(160, $yCurso);
                    $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
                }

# BLOQUE BIMESTRE 5
                if ($vunidad >= 8) {
                    $vpromFinalCONDCuant = '';
                    $vpromFinalCONDCual = '';
                    if ($vpromCondBimestre1 != '' && $vpromCondBimestre2 != '' && $vpromCondBimestre3 != '' && $vpromCondBimestre4 != '') {
                        $vpromFinalCONDCuant = round((($vpromCondBimestre1 + $vpromCondBimestre2 + $vpromCondBimestre3 + $vpromCondBimestre4) / 4), 0);
                        $vpromFinalCONDCual = $this->getCualitativoStandar((int) $vpromFinalCONDCuant, $alumno->INSTRUCOD);
                    }
                    if ($vpromFinalCONDCuant > 10 && $vpromFinalCONDCuant <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) || $this->ano>=2023 )
                        $ancho = 26;
                    else
                        $ancho = 13;
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") { // SI ES EL CURSO RELIGION Y ESTA EXONERADO
                        $vpromFinalCONDCuant = (($vpromFinalCONDCuant != '') ? 'EXO' : 'EXO');
                    } else {
                        $vpromFinalCONDCuant = (($vpromFinalCONDCuant > 0) ? (((int) $vpromFinalCONDCuant < 10) ? ('0' . (int) $vpromFinalCONDCuant) : $vpromFinalCONDCuant) : '');
                    }

					if ($this->ano <= 2022) {
						$this->pdf->SetFillColor(208, 222, 240);
						$this->pdf->Cell($ancho, 5, ($alumno->INSTRUCOD == 'P')?'-':$vpromFinalCONDCuant, 1, 0, 'C', TRUE);
					}
					
					if ($this->ano <= 2022) {
						if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3) {
							// SECUNDARIA A PARTIR DE 3° NO TIENE NOTAS CUANTITATIVO
						} else {
							if ($vpromFinalCONDCual === 'A' || $vpromFinalCONDCual === 'AD' || $vpromFinalCONDCual === 'B') {
								$this->pdf->SetTextColor(0, 0, 204);
							} else {
								$this->pdf->SetTextColor(255, 0, 51);
							}
							if ($this->ano <= 2022) {
								$this->pdf->SetXY(181, $yCurso);
							} else {
								$this->pdf->SetXY(168, $yCurso);
							}
							$this->pdf->Cell($ancho, 5, $vpromFinalCONDCual, 1, 0, 'C', TRUE);
						}
					} else {
							if ($vpromFinalCONDCual === 'A' || $vpromFinalCONDCual === 'AD' || $vpromFinalCONDCual === 'B') {
								$this->pdf->SetTextColor(0, 0, 204);
							} else {
								$this->pdf->SetTextColor(255, 0, 51);
							}
							if ($this->ano <= 2022) {
								$this->pdf->SetXY(181, $yCurso);
							} else {
								$this->pdf->SetXY(168, $yCurso);
							}
							$this->pdf->Cell($ancho, 5,  ($alumno->INSTRUCOD == 'P') ? $vpromFinalCONDCual : $vpromFinalCONDCuant, 1, 0, 'C', TRUE);
					}
                } else {
                    if (($alumno->GRADOCOD >= 3 && $alumno->INSTRUCOD == 'S' && $this->ano<=2022) || $this->ano>=2023 ) {
                        $this->pdf->SetFillColor(208, 222, 240);
                        $this->pdf->SetXY(168, $yCurso);
                        $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                    } else {
                        $this->pdf->SetFillColor(208, 222, 240);
                        $this->pdf->SetXY(168, $yCurso);
                        $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                        $this->pdf->SetXY(181, $yCurso);
                        $this->pdf->Cell(13, 5, '', 1, 0, 'C', TRUE);
                    }
                }
            }
# ================================ BLOQUE RESUMEN ANUAL ===================================
            if (($alumno->INSTRUCOD == "S" && $this->ano == 2022) || (($alumno->INSTRUCOD == "S" || $alumno->INSTRUCOD == "P") && $this->ano < 2022)) {
                $yCurso += 7;
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetFont('Arial', 'B', 8);
                $this->pdf->SetXY(20, $yCurso);
                $this->pdf->Cell(60, 10, utf8_decode('PUNTAJE BIMESTRAL'), 1, 0, 'C', TRUE);
                // ======== Primer Bimestre =============
                $this->pdf->SetFont('Arial', 'B', 7);
                $promAnual1 = (($filaCursollenados > 0) ? $vPuntaje : '');
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(80, $yCurso);
                $this->pdf->Cell(22, 5, 'I B', 1, 0, 'C', TRUE);
                if ($vunidad >= 2) {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(80, $yCurso + 5);
                    $this->pdf->Cell(22, 5, $promAnual1, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(80, $yCurso + 5);
                    $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                }
                // ======== Segundo Bimestre =============
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFont('Arial', 'B', 7);
                $promAnual2 = (($filaCursollenados2 > 0) ? $vPuntaje2 : '');
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(102, $yCurso);
                $this->pdf->Cell(22, 5, 'II B', 1, 0, 'C', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vunidad >= 4) {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(102, $yCurso + 5);
                    $this->pdf->Cell(22, 5, $promAnual2, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(102, $yCurso + 5);
                    $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                }

                // ======== Tercer Bimestre =============
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFont('Arial', 'B', 7);
                $promAnual3 = (($filaCursollenados3 > 0) ? $vPuntaje3 : '');
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(124, $yCurso);
                $this->pdf->Cell(22, 5, 'III B', 1, 0, 'C', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vunidad >= 6) {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(124, $yCurso + 5);
                    $this->pdf->Cell(22, 5, $promAnual3, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(124, $yCurso + 5);
                    $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                }

                // ======== Cuarto Bimestre =============
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFont('Arial', 'B', 7);
                $promAnual4 = (($filaCursollenados4 > 0) ? $vPuntaje4 : '');
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(146, $yCurso);
                $this->pdf->Cell(22, 5, 'IV B', 1, 0, 'C', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vunidad >= 8) {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(146, $yCurso + 5);
                    $this->pdf->Cell(22, 5, $promAnual4, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(146, $yCurso + 5);
                    $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                }
                if ($vbimestre >= 4) {
                    $totalPuntajeFinal = ($promAnual1 + $promAnual2 + $promAnual3 + $promAnual4);
                    $this->pdf->SetTextColor(0, 0, 0);
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, 'TOTAL', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(168, $yCurso + 5);
                    $this->pdf->Cell(26, 5, $totalPuntajeFinal, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetTextColor(0, 0, 0);
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, 'TOTAL', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(168, $yCurso + 5);
                    $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                }
# ================================ BLOQUE A/B ===================================
                $yCurso = $this->pdf->GetY() + 7;
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetFont('Arial', 'B', 7);
                $this->pdf->SetXY(20, $yCurso);
                $this->pdf->Cell(60, 5, utf8_decode('PROMEDIO BIMESTRAL'), 1, 0, 'C', TRUE);
                /* $this->pdf->SetXY(20, $yCurso + 5);
                  $this->pdf->Cell(10, 5, utf8_decode('B'), 1, 0, 'C', TRUE);
                  $this->pdf->SetXY(30, $yCurso + 5);
                  $this->pdf->Cell(50, 5, utf8_decode('ORDEN DE MÉRITO EN EL AULA'), 1, 0, 'L', TRUE); */

                $this->pdf->SetFont('Arial', 'B', 7);
                if ($vbimestre >= 1)
                    $promAnual1 = (($filaCursollenados > 0) ? (round($vPuntaje / $filaCursollenados)) : '');
                if ($vbimestre >= 2)
                    $promAnual2 = (($filaCursollenados2 > 0) ? (round($vPuntaje2 / $filaCursollenados2)) : '');
                if ($vbimestre >= 3)
                    $promAnual3 = (($filaCursollenados3 > 0) ? (round($vPuntaje3 / $filaCursollenados3)) : '');
                if ($vbimestre >= 4)
                    $promAnual4 = (($filaCursollenados4 > 0) ? (round($vPuntaje4 / $filaCursollenados4)) : '');

                // Primer Bimestre
                if ($vunidad >= 2) {
                    $this->pdf->SetFillColor(255, 255, 255);
                    if ($promAnual1 > 10 && $promAnual1 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(80, $yCurso);
                    $this->pdf->Cell(22, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($promAnual1, $alumno->INSTRUCOD) : $promAnual1, 1, 0, 'C', TRUE);
                } else {
                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(80, $yCurso);
                    $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                }

                // Segundo Bimestre
                $this->pdf->SetFillColor(255, 255, 255);
                if ($promAnual2 > 10 && $promAnual2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $prombim1 = (($promAnual2 < 10) ? '0' . $promAnual2 : $promAnual2);
                $this->pdf->SetXY(102, $yCurso);
                $prombim1 = (($prombim1 > 0) ? $prombim1 : '');
                $this->pdf->Cell(22, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($prombim1, $alumno->INSTRUCOD) : $prombim1, 1, 0, 'C', TRUE);
                // Tercer Bimestre
                $this->pdf->SetFillColor(255, 255, 255);
                if ($promAnual3 > 10 && $promAnual3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $prombim2 = (($promAnual3 < 10) ? '0' . $promAnual3 : $promAnual3);
                $this->pdf->SetXY(124, $yCurso);
                $this->pdf->Cell(22, 5, (($prombim2 > 0) ? $prombim2 : ''), 1, 0, 'C', TRUE);

                // Cuarto Bimestre

                $this->pdf->SetFillColor(255, 255, 255);
                if ($promAnual4 > 10 && $promAnual4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $prombim3 = (($promAnual4 < 10) ? '0' . $promAnual4 : $promAnual4);
                $this->pdf->SetXY(146, $yCurso);
                $this->pdf->Cell(22, 5, (($prombim3 > 0) ? $prombim3 : ''), 1, 0, 'C', TRUE);
                /* $this->pdf->SetFillColor(255, 255, 255);
                  $this->pdf->SetXY(146, $yCurso + 5);
                  $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE); */


                if ($vbimestre >= 4) {
                    $vPromBimestral = round(($promAnual1 + $prombim1 + $prombim2 + $prombim3) / 4);
                    if ($vPromBimestral > 10 && $vPromBimestral <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, $vPromBimestral, 1, 0, 'C', TRUE);
                    $this->pdf->SetTextColor(0, 0, 0);
                } else {
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(168, $yCurso);
                    $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE);
                    /* $this->pdf->SetXY(168, $yCurso + 5);
                      $this->pdf->Cell(26, 5, '', 1, 0, 'C', TRUE); */
                }
            }
# ================================ BLOQUE AREAS ===================================
            $this->pdf->SetTextColor(0, 0, 0);
            $yCurso = $this->pdf->GetY() + 7;
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetXY(20, $yCurso);
            $this->pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C', TRUE);
            $this->pdf->SetXY(30, $yCurso);
            $this->pdf->Cell(50, 10, utf8_decode('ÁREAS BASICAS'), 1, 0, 'C', TRUE);

            $this->pdf->SetFont('Arial', 'B', 7);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetXY(80, $yCurso);
            $this->pdf->Cell(44, 5, ($this->ano >=2023) ? 'LOGROS POR BIMESTRE' : 'PROMEDIOS BIMESTRALES', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(80, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'I B', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(91, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'II B', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(102, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'III B', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(113, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'IV B', 1, 0, 'C', TRUE);

            $this->pdf->SetXY(124, $yCurso);
            $this->pdf->Cell(22, 5, ($this->ano >=2023) ? 'LOGRO ANUAL' :'PROMEDIO ANUAL', 1, 0, 'C', TRUE);
			
			if($this->ano >=2023){
            $this->pdf->SetXY(124, $yCurso + 5);
            $this->pdf->Cell(22, 5, 'COMPETENCIA', 1, 0, 'C', TRUE);			
			}
			else {
            $this->pdf->SetXY(124, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'CUAN', 1, 0, 'C', TRUE);
            $this->pdf->SetXY(135, $yCurso + 5);
            $this->pdf->Cell(11, 5, 'CUAL', 1, 0, 'C', TRUE);				
			}


            $this->pdf->SetXY(146, $yCurso);
            $this->pdf->Cell(8, 10, 'PRP', 1, 0, 'C', TRUE);

            $filaCursoOficial = 1;
            $vpromBim = 0;
            $vpromBim2 = 0;
            $vpromBim3 = 0;
            $vpromBim4 = 0;
            $vpromCurso1 = 0;
            $vpromCurso2 = 0;
            $vpromCurso3 = 0;
            $vpromCurso4 = 0;
            $vpromCurso5 = 0;
            $vpromCurso6 = 0;
            $vpromCurso7 = 0;
            $vpromCurso8 = 0;
            $vpromCurso9 = 0;
            $vpromCurso10 = 0;
            $yCursoOficial = $yCurso + 10;
			$dataNotaPRP=0;
			$notaPRP='';
            foreach ($dataCursoOficial as $rowcur) {
			// ----------- Obteniendo los cursos Oficiales Recuperados ---------------------------------
				$vidcursoOfi =  $rowcur->cursocod;
				$dataNotaPRP = $this->objNota->getNotaPRP($vnemo, $valucod, $vidcursoOfi);
				if(!empty($dataNotaPRP)){
					//print_r($dataNotaPRP); //exit;
					$notaPRP = $dataNotaPRP[0]->pb;
					//echo "Dato : ".$notaPRP. " Curso : ".$vidcursoOfi; exit;
				} else {
					//echo "no es array : ".$notaPRP; exit;
				}
			// -------------------------------------------------------------------------------------------------------
                if ($alumno->INSTRUCOD === "P") {
                    if ($filaCursoOficial === 1) { // ARTE						
                        $vpromBim = $arrCurso["cursos1"][0];
                        $vpromBim2 = $arrCurso["cursos2"][0];
                        $vpromBim3 = $arrCurso["cursos3"][0];
                        $vpromBim4 = $arrCurso["cursos4"][0];
                        $vpromCurso1 += $vpromBim;
                    } elseif ($filaCursoOficial === 2) { // CIENCIA YA AMBIENTE
                        $vpromBim = $arrCurso["cursos1"][1];
                        $vpromBim2 = $arrCurso["cursos2"][1];
                        $vpromBim3 = $arrCurso["cursos3"][1];
                        $vpromBim4 = $arrCurso["cursos4"][1];
                        $vpromCurso2 += $vpromBim;
                    } elseif ($filaCursoOficial === 3) { // COMUNICACION
                        $vpromBim = round(($arrCurso["cursos1"][2] + $arrCurso["cursos1"][3] + $arrCurso["cursos1"][4]) / 3);
                        $vpromBim2 = round(($arrCurso["cursos2"][2] + $arrCurso["cursos2"][3] + $arrCurso["cursos2"][4]) / 3);
                        $vpromBim3 = round(($arrCurso["cursos3"][2] + $arrCurso["cursos3"][3] + $arrCurso["cursos3"][4]) / 3);
                        $vpromBim4 = round(($arrCurso["cursos4"][2] + $arrCurso["cursos4"][3] + $arrCurso["cursos4"][4]) / 3);
                        $vpromCurso3 += $vpromBim;
                    } elseif ($filaCursoOficial === 4) { // EDU. FISICA
                        $vpromBim = $arrCurso["cursos1"][5];
                        $vpromBim2 = $arrCurso["cursos2"][5];
                        $vpromBim3 = $arrCurso["cursos3"][5];
                        $vpromBim4 = $arrCurso["cursos4"][5];
                        $vpromCurso4 += $vpromBim;
                    } elseif ($filaCursoOficial === 5) { // RELIGION
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                            $vpromBim = 0;
                            $vpromBim2 = 0;
                            $vpromBim3 = 0;
                            $vpromBim4 = 0;
                        } else {
                            $vpromBim = $arrCurso["cursos1"][6];
                            $vpromBim2 = $arrCurso["cursos2"][6];
                            $vpromBim3 = $arrCurso["cursos3"][6];
                            $vpromBim4 = $arrCurso["cursos4"][6];
                        }
                        $vpromCurso5 += $vpromBim;
                    } elseif ($filaCursoOficial === 6) { // MATEMATICA
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = round(($arrCurso["cursos1"][7] + $arrCurso["cursos1"][8]) / 2);
                            $vpromBim2 = round(($arrCurso["cursos2"][7] + $arrCurso["cursos2"][8]) / 2);
                            $vpromBim3 = round(($arrCurso["cursos3"][7] + $arrCurso["cursos3"][8]) / 2);
                            $vpromBim4 = round(($arrCurso["cursos4"][7] + $arrCurso["cursos4"][8]) / 2);
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = round(($arrCurso["cursos1"][7] + $arrCurso["cursos1"][8] + $arrCurso["cursos1"][9]) / 3);
                            $vpromBim2 = round(($arrCurso["cursos2"][7] + $arrCurso["cursos2"][8] + $arrCurso["cursos2"][9]) / 3);
                            $vpromBim3 = round(($arrCurso["cursos3"][7] + $arrCurso["cursos3"][8] + $arrCurso["cursos3"][9]) / 3);
                            $vpromBim4 = round(($arrCurso["cursos4"][7] + $arrCurso["cursos4"][8] + $arrCurso["cursos4"][9]) / 3);
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = round(($arrCurso["cursos1"][7] + $arrCurso["cursos1"][8] + $arrCurso["cursos1"][9] + $arrCurso["cursos1"][10]) / 4);
                            $vpromBim2 = round(($arrCurso["cursos2"][7] + $arrCurso["cursos2"][8] + $arrCurso["cursos2"][9] + $arrCurso["cursos2"][10]) / 4);
                            $vpromBim3 = round(($arrCurso["cursos3"][7] + $arrCurso["cursos3"][8] + $arrCurso["cursos3"][9] + $arrCurso["cursos3"][10]) / 4);
                            $vpromBim4 = round(($arrCurso["cursos4"][7] + $arrCurso["cursos4"][8] + $arrCurso["cursos4"][9] + $arrCurso["cursos4"][10]) / 4);
                        }
                        $vpromCurso6 += $vpromBim;
                    } elseif ($filaCursoOficial === 7) { // PERSONAL SOCIAL
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][9];
                            $vpromBim2 = $arrCurso["cursos2"][9];
                            $vpromBim3 = $arrCurso["cursos3"][9];
                            $vpromBim4 = $arrCurso["cursos4"][9];
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][10];
                            $vpromBim2 = $arrCurso["cursos2"][10];
                            $vpromBim3 = $arrCurso["cursos3"][10];
                            $vpromBim4 = $arrCurso["cursos4"][10];
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        }
                        $vpromCurso7 += $vpromBim;
                    } elseif ($filaCursoOficial === 8) { // INGLES
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][10];
                            $vpromBim2 = $arrCurso["cursos2"][10];
                            $vpromBim3 = $arrCurso["cursos3"][10];
                            $vpromBim4 = $arrCurso["cursos4"][10];
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][12];
                            $vpromBim2 = $arrCurso["cursos2"][12];
                            $vpromBim3 = $arrCurso["cursos3"][12];
                            $vpromBim4 = $arrCurso["cursos4"][12];
                        }
                        $vpromCurso8 += $vpromBim;
                    } elseif ($filaCursoOficial === 9) { // COMPUTACION
                        if ($alumno->GRADOCOD <= 2) { // DE 1 A 2 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        } elseif ($alumno->GRADOCOD == 3) { // 3 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][12];
                            $vpromBim2 = $arrCurso["cursos2"][12];
                            $vpromBim3 = $arrCurso["cursos3"][12];
                            $vpromBim4 = $arrCurso["cursos4"][12];
                        } elseif ($alumno->GRADOCOD >= 4) { // DE 4 A 6 GRADO DE PRIMARIA
                            $vpromBim = $arrCurso["cursos1"][13];
                            $vpromBim2 = $arrCurso["cursos2"][13];
                            $vpromBim3 = $arrCurso["cursos3"][13];
                            $vpromBim4 = $arrCurso["cursos4"][13];
                        }
                        $vpromCurso9 += $vpromBim;
                    }
                } elseif ($alumno->INSTRUCOD === "S") {
                    if ($filaCursoOficial === 1) { // ARTE
                        $vpromBim = $arrCurso["cursos1"][0];
                        $vpromBim2 = $arrCurso["cursos2"][0];
                        $vpromBim3 = $arrCurso["cursos3"][0];
                        $vpromBim4 = $arrCurso["cursos4"][0];
                        $vpromCurso1 += $vpromBim;
                    } elseif ($filaCursoOficial === 2) { // CIENCIA Y TECNOLOGIA
                        if ($arrCurso["cursos1"][1] > 0 && $arrCurso["cursos1"][2] > 0 && $arrCurso["cursos1"][3] > 0) {
                            $vpromBim = round(($arrCurso["cursos1"][1] + $arrCurso["cursos1"][2] + $arrCurso["cursos1"][3]) / 3);
                        } else {
                            $vpromBim = 0;
                        }
                        if ($arrCurso["cursos2"][1] > 0 && $arrCurso["cursos2"][2] > 0 && $arrCurso["cursos2"][3] > 0) {
                            $vpromBim2 = round(($arrCurso["cursos2"][1] + $arrCurso["cursos2"][2] + $arrCurso["cursos2"][3]) / 3);
                        } else {
                            $vpromBim2 = 0;
                        }
                        if ($arrCurso["cursos3"][1] > 0 && $arrCurso["cursos3"][2] > 0 && $arrCurso["cursos3"][3] > 0) {
                            $vpromBim3 = round(($arrCurso["cursos3"][1] + $arrCurso["cursos3"][2] + $arrCurso["cursos3"][3]) / 3);
                        } else {
                            $vpromBim3 = 0;
                        }
                        if ($arrCurso["cursos4"][1] > 0 && $arrCurso["cursos4"][2] > 0 && $arrCurso["cursos4"][3] > 0) {
                            $vpromBim4 = round(($arrCurso["cursos4"][1] + $arrCurso["cursos4"][2] + $arrCurso["cursos4"][3]) / 3);
                        } else {
                            $vpromBim4 = 0;
                        }
                        $vpromCurso2 += $vpromBim;
                    } elseif ($filaCursoOficial === 3) { // CIENCIAS SOCIALES
                        $vpromBim = $arrCurso["cursos1"][4];
                        $vpromBim2 = $arrCurso["cursos2"][4];
                        $vpromBim3 = $arrCurso["cursos3"][4];
                        $vpromBim4 = $arrCurso["cursos4"][4];
                        $vpromCurso3 += $vpromBim;
                    } elseif ($filaCursoOficial === 4) { // COMUNICACION
                        if ($alumno->GRADOCOD <= 2) {
                            if ($arrCurso["cursos1"][5] > 0 && $arrCurso["cursos1"][6] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][5] + $arrCurso["cursos1"][6] ) / 2);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][5] > 0 && $arrCurso["cursos2"][6] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][5] + $arrCurso["cursos2"][6] ) / 2);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][5] > 0 && $arrCurso["cursos3"][6] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][5] + $arrCurso["cursos3"][6] ) / 2);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][5] > 0 && $arrCurso["cursos4"][6] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][5] + $arrCurso["cursos4"][6] ) / 2);
                            } else {
                                $vpromBim4 = 0;
                            }
                        } elseif ($alumno->GRADOCOD > 2) {
                            if ($arrCurso["cursos1"][5] > 0 && $arrCurso["cursos1"][6] > 0 && $arrCurso["cursos1"][7] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][5] + $arrCurso["cursos1"][6] + $arrCurso["cursos1"][7] ) / 3);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][5] > 0 && $arrCurso["cursos2"][6] > 0 && $arrCurso["cursos2"][7] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][5] + $arrCurso["cursos2"][6] + $arrCurso["cursos2"][7] ) / 3);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][5] > 0 && $arrCurso["cursos3"][6] > 0 && $arrCurso["cursos3"][7] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][5] + $arrCurso["cursos3"][6] + $arrCurso["cursos3"][7] ) / 3);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][5] > 0 && $arrCurso["cursos4"][6] > 0 && $arrCurso["cursos4"][7] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][5] + $arrCurso["cursos4"][6] + $arrCurso["cursos4"][7] ) / 3);
                            } else {
                                $vpromBim4 = 0;
                            }
                        }
                        $vpromCurso4 += $vpromBim;
                    } elseif ($filaCursoOficial === 5) { // DPCC
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][7];
                            $vpromBim2 = $arrCurso["cursos2"][7];
                            $vpromBim3 = $arrCurso["cursos3"][7];
                            $vpromBim4 = $arrCurso["cursos4"][7];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][8];
                            $vpromBim2 = $arrCurso["cursos2"][8];
                            $vpromBim3 = $arrCurso["cursos3"][8];
                            $vpromBim4 = $arrCurso["cursos4"][8];
                        }
                        $vpromCurso5 += $vpromBim;
                    } elseif ($filaCursoOficial === 6) { // EDU. FISICA
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][8];
                            $vpromBim2 = $arrCurso["cursos2"][8];
                            $vpromBim3 = $arrCurso["cursos3"][8];
                            $vpromBim4 = $arrCurso["cursos4"][8];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][9];
                            $vpromBim2 = $arrCurso["cursos2"][9];
                            $vpromBim3 = $arrCurso["cursos3"][9];
                            $vpromBim4 = $arrCurso["cursos4"][9];
                        }
                        $vpromCurso6 += $vpromBim;
                    } elseif ($filaCursoOficial === 7) { // COMPUTACION
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][9];
                            $vpromBim2 = $arrCurso["cursos2"][9];
                            $vpromBim3 = $arrCurso["cursos3"][9];
                            $vpromBim4 = $arrCurso["cursos4"][9];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][10];
                            $vpromBim2 = $arrCurso["cursos2"][10];
                            $vpromBim3 = $arrCurso["cursos3"][10];
                            $vpromBim4 = $arrCurso["cursos4"][10];
                        }
                        $vpromCurso7 += $vpromBim;
                    } elseif ($filaCursoOficial === 8) { // EDU. RELIGION
                        if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                            $vpromBim = 0;
                            $vpromBim2 = 0;
                            $vpromBim3 = 0;
                            $vpromBim4 = 0;
                        } else {
                            if ($alumno->GRADOCOD <= 2) {
                                $vpromBim = $arrCurso["cursos1"][10];
                                $vpromBim2 = $arrCurso["cursos2"][10];
                                $vpromBim3 = $arrCurso["cursos3"][10];
                                $vpromBim4 = $arrCurso["cursos4"][10];
                            } elseif ($alumno->GRADOCOD > 2) {
                                $vpromBim = $arrCurso["cursos1"][11];
                                $vpromBim2 = $arrCurso["cursos2"][11];
                                $vpromBim3 = $arrCurso["cursos3"][11];
                                $vpromBim4 = $arrCurso["cursos4"][11];
                            }
                        }
                        $vpromCurso8 += $vpromBim;
                    } elseif ($filaCursoOficial === 9) { // INGLES
                        if ($alumno->GRADOCOD <= 2) {
                            $vpromBim = $arrCurso["cursos1"][11];
                            $vpromBim2 = $arrCurso["cursos2"][11];
                            $vpromBim3 = $arrCurso["cursos3"][11];
                            $vpromBim4 = $arrCurso["cursos4"][11];
                        } elseif ($alumno->GRADOCOD > 2) {
                            $vpromBim = $arrCurso["cursos1"][12];
                            $vpromBim2 = $arrCurso["cursos2"][12];
                            $vpromBim3 = $arrCurso["cursos3"][12];
                            $vpromBim4 = $arrCurso["cursos4"][12];
                        }
                        $vpromCurso9 += $vpromBim;
                    } elseif ($filaCursoOficial === 10) { // MATEMATICAS
                        if ($alumno->GRADOCOD <= 2) {
                            if ($arrCurso["cursos1"][12] > 0 && $arrCurso["cursos1"][13] > 0 && $arrCurso["cursos1"][14] > 0 && $arrCurso["cursos1"][15] > 0 && $arrCurso["cursos1"][16] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][12] + $arrCurso["cursos1"][13] + $arrCurso["cursos1"][14] + $arrCurso["cursos1"][15] + $arrCurso["cursos1"][16] ) / 5);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][12] > 0 && $arrCurso["cursos2"][13] > 0 && $arrCurso["cursos2"][14] > 0 && $arrCurso["cursos2"][15] > 0 && $arrCurso["cursos2"][16] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][12] + $arrCurso["cursos2"][13] + $arrCurso["cursos2"][14] + $arrCurso["cursos2"][15] + $arrCurso["cursos2"][16] ) / 5);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][12] > 0 && $arrCurso["cursos3"][13] > 0 && $arrCurso["cursos3"][14] > 0 && $arrCurso["cursos3"][15] > 0 && $arrCurso["cursos3"][16] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][12] + $arrCurso["cursos3"][13] + $arrCurso["cursos3"][14] + $arrCurso["cursos3"][15] + $arrCurso["cursos3"][16] ) / 5);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][12] > 0 && $arrCurso["cursos4"][13] > 0 && $arrCurso["cursos4"][14] > 0 && $arrCurso["cursos4"][15] > 0 && $arrCurso["cursos4"][16] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][12] + $arrCurso["cursos4"][13] + $arrCurso["cursos4"][14] + $arrCurso["cursos4"][15] + $arrCurso["cursos4"][16] ) / 5);
                            } else {
                                $vpromBim4 = 0;
                            }
                        } elseif ($alumno->GRADOCOD > 2) {
                            if ($arrCurso["cursos1"][13] > 0 && $arrCurso["cursos1"][14] > 0 && $arrCurso["cursos1"][15] > 0 && $arrCurso["cursos1"][16] > 0 && $arrCurso["cursos1"][17] > 0) {
                                $vpromBim = round(($arrCurso["cursos1"][13] + $arrCurso["cursos1"][14] + $arrCurso["cursos1"][15] + $arrCurso["cursos1"][16] + $arrCurso["cursos1"][17] ) / 5);
                            } else {
                                $vpromBim = 0;
                            }
                            if ($arrCurso["cursos2"][13] > 0 && $arrCurso["cursos2"][14] > 0 && $arrCurso["cursos2"][15] > 0 && $arrCurso["cursos2"][16] > 0 && $arrCurso["cursos2"][17] > 0) {
                                $vpromBim2 = round(($arrCurso["cursos2"][13] + $arrCurso["cursos2"][14] + $arrCurso["cursos2"][15] + $arrCurso["cursos2"][16] + $arrCurso["cursos2"][17] ) / 5);
                            } else {
                                $vpromBim2 = 0;
                            }
                            if ($arrCurso["cursos3"][13] > 0 && $arrCurso["cursos3"][14] > 0 && $arrCurso["cursos3"][15] > 0 && $arrCurso["cursos3"][16] > 0 && $arrCurso["cursos3"][17] > 0) {
                                $vpromBim3 = round(($arrCurso["cursos3"][13] + $arrCurso["cursos3"][14] + $arrCurso["cursos3"][15] + $arrCurso["cursos3"][16] + $arrCurso["cursos3"][17] ) / 5);
                            } else {
                                $vpromBim3 = 0;
                            }
                            if ($arrCurso["cursos4"][13] > 0 && $arrCurso["cursos4"][14] > 0 && $arrCurso["cursos4"][15] > 0 && $arrCurso["cursos4"][16] > 0 && $arrCurso["cursos4"][17] > 0) {
                                $vpromBim4 = round(($arrCurso["cursos4"][13] + $arrCurso["cursos4"][14] + $arrCurso["cursos4"][15] + $arrCurso["cursos4"][16] + $arrCurso["cursos4"][17] ) / 5);
                            } else {
                                $vpromBim4 = 0;
                            }
                        }
                        $vpromCurso10 += $vpromBim;
                    }
                }
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetXY(20, $yCursoOficial);
                $this->pdf->Cell(10, 5, (($filaCursoOficial < 10) ? ('0' . $filaCursoOficial) : $filaCursoOficial), 1, 0, 'C', TRUE);
                $this->pdf->SetXY(30, $yCursoOficial);
                $this->pdf->Cell(50, 5, utf8_decode($rowcur->cursocor), 1, 0, 'L', TRUE);
                $this->pdf->SetFillColor(255, 255, 255);
                // 1 bimestre 
                if ($vunidad >= 2) {
                    if ($vpromBim > 10 && $vpromBim <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(80, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
                        $vpromBim = (($vpromBim > 0) ? (((int) $vpromBim < 10) ? ('0' . (int) $vpromBim) : $vpromBim) : '');
                        $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromBim, $alumno->INSTRUCOD) : $vpromBim, 1, 0, 'C', TRUE);
                    }
                } else {
                    $this->pdf->SetXY(80, $yCursoOficial);
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                }


                // 2 bimestre 
                if ($vunidad >= 4) {
                    if ($vpromBim2 > 10 && $vpromBim2 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(91, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
                        $vpromBim2 = (($vpromBim2 > 0) ? (((int) $vpromBim2 < 10) ? ('0' . (int) $vpromBim2) : $vpromBim2) : '');
                        $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromBim2, $alumno->INSTRUCOD) : $vpromBim2, 1, 0, 'C', TRUE);
                    }
                } else {
                    $this->pdf->SetXY(91, $yCursoOficial);
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                }

                // 3 bimestre 
                if ($vunidad >= 6) {
                    if ($vpromBim3 > 10 && $vpromBim3 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(102, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
						$vpromBim3 = (($vpromBim3 > 0) ? (((int) $vpromBim3 < 10) ? ('0' . (int) $vpromBim3) : $vpromBim3) : '');
						$this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromBim3, $alumno->INSTRUCOD) : $vpromBim3, 1, 0, 'C', TRUE);
                        //$this->pdf->Cell(11, 5, (($vpromBim3 > 0) ? (((int) $vpromBim3 < 10) ? ('0' . (int) $vpromBim3) : $vpromBim3) : ''), 1, 0, 'C', TRUE);
                    }
                } else {
                    $this->pdf->SetXY(102, $yCursoOficial);
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                }

                // 4 bimestre     
                if ($vunidad >= 8) {
                    if ($vpromBim4 > 10 && $vpromBim4 <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(113, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
                        $this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
						$vpromBim4 = (($vpromBim4 > 0) ? (((int) $vpromBim4 < 10) ? ('0' . (int) $vpromBim4) : $vpromBim4) : '');
                        $this->pdf->Cell(11, 5,($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromBim4, $alumno->INSTRUCOD) : $vpromBim4, 1, 0, 'C', TRUE);
                    }
                } else {
                    $this->pdf->SetXY(113, $yCursoOficial);
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                }

                if ($vunidad >= 8) {
                    $vpromAreaCuan = "";
                    $vpromAreaCual = "";
                    if ($vpromBim != '' && $vpromBim2 != '' && $vpromBim3 != '' && $vpromBim4 != '') {
                        $vpromAreaCuan = round(($vpromBim + $vpromBim2 + $vpromBim3 + $vpromBim4) / 4);
                        $vpromAreaCual = $this->getCualitativoStandar((int) $vpromAreaCuan, $alumno->INSTRUCOD);
                    }

                    // falta validar los PRP

                    $arrApreAnual[] = $vpromAreaCuan;
                    if ($vpromAreaCuan == 0 && $alumno->REPITE === "E") {
                        $vpromAreaCuan = 11;
                    }
					
                    //Se agrega Validacion para PRP
					if($notaPRP!='' || $notaPRP>0){
						if ((int) $notaPRP <= 10) {
							$contdesapro ++;
						}	
					} else {
						if ((int) $vpromAreaCuan <= 10) {
							$contdesapro ++;
						}						
					}

                    $this->pdf->SetTextColor(0, 0, 204);
                    if ($vpromAreaCuan > 10 && $vpromAreaCuan <= 20) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(124, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
						if($this->ano <= 2022)
							$this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
						else 
							$this->pdf->Cell(22, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
						if($this->ano <= 2022)
							$this->pdf->Cell(11, 5,($alumno->INSTRUCOD === "P")?'-':$vpromAreaCuan, 1, 0, 'C', TRUE);
						else 
							$this->pdf->Cell(22, 5,$vpromAreaCual, 1, 0, 'C', TRUE);
                    }
                    if ($vpromAreaCual === 'A' || $vpromAreaCual === 'AD' || $vpromAreaCual === 'B') {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }
                    $this->pdf->SetXY(135, $yCursoOficial);
                    if ($rowcur->cursocod === "14" && $alumno->REPITE === "E") {
                        $this->pdf->SetTextColor(0, 0, 204);
						if($this->ano <= 2022)
							$this->pdf->Cell(11, 5, 'EXO', 1, 0, 'C', TRUE);
						else 
							$this->pdf->Cell(22, 5, 'EXO', 1, 0, 'C', TRUE);
                    } else {
						if($this->ano <= 2022){
							if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3)
								$this->pdf->Cell(11, 5, '-', 1, 0, 'C', TRUE);
							else
								$this->pdf->Cell(11, 5, $vpromAreaCual, 1, 0, 'C', TRUE);
						} 
                    }
                } else {
					if($this->ano >=2023){
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(124, $yCursoOficial);
                    $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);						
					} else {						
                    $this->pdf->SetTextColor(0, 0, 204);
                    $this->pdf->SetXY(124, $yCursoOficial);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                    $this->pdf->SetXY(135, $yCursoOficial);
                    $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
					}
                }
                $this->pdf->SetTextColor(0, 0, 204);
				if ($notaPRP > 10 && $notaPRP <= 20) {
					$this->pdf->SetTextColor(0, 0, 204);
				} else {
					$this->pdf->SetTextColor(255, 0, 51);
				}				
                $this->pdf->SetXY(146, $yCursoOficial);
				//echo $notaPRP ; exit;
                $this->pdf->Cell(8, 5, $this->getCualitativoStandar($notaPRP, $alumno->INSTRUCOD) , 1, 0, 'C', TRUE); 

                $yCursoOficial += 5;
                $filaCursoOficial ++;
            }
# BLOQUE TIC
            $yCursoOficial += 2;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6); 
            $this->pdf->SetXY(20, $yCursoOficial);
            $this->pdf->Cell(60, 5, utf8_decode(($this->ano>=2023) ? 'ENTORNO VIRTUAL - TIC' : 'SE DESENVUELVE EN ENTORNOS VIRTUALES - TIC'), 1, 0, 'L', TRUE);

            if ($vunidad >= 2) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vpromUnidadTIC > 10 && $vpromUnidadTIC <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(80, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC, $alumno->INSTRUCOD) : $vpromUnidadTIC, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(80, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            if ($vunidad >= 4) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vpromUnidadTIC2 > 10 && $vpromUnidadTIC2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC2, $alumno->INSTRUCOD) : $vpromUnidadTIC2, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }
            // 3 bimestre
            if ($vunidad >= 6) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vpromUnidadTIC3 > 10 && $vpromUnidadTIC3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC3, $alumno->INSTRUCOD) : $vpromUnidadTIC3, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            // 4 bimestre
            if ($vunidad >= 8) {
                $this->pdf->SetFont('Arial', '', 7);
                $this->pdf->SetFillColor(255, 255, 255);
                if ($vpromUnidadTIC4 > 10 && $vpromUnidadTIC4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromUnidadTIC4, $alumno->INSTRUCOD) : $vpromUnidadTIC4, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            if ($vunidad >= 8) {
                $vpromFinalTICCuant = '';
                $vpromFinalTICCual = '';
                if ($vpromUnidadTIC != '' && $vpromUnidadTIC2 != '' && $vpromUnidadTIC3 != '' && $vpromUnidadTIC4 != '') {
                    $vpromFinalTICCuant = round((($vpromUnidadTIC + $vpromUnidadTIC2 + $vpromUnidadTIC3 + $vpromUnidadTIC4) / 4), 0);
                    $vpromFinalTICCual = $this->getCualitativoStandar((int) $vpromFinalTICCuant, $alumno->INSTRUCOD);
                }
                if ($vpromFinalTICCuant > 10 && $vpromFinalTICCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(124, $yCursoOficial);
				if($this->ano <= 2022)
					$this->pdf->Cell(11, 5, ($alumno->INSTRUCOD === "P")?'-':$vpromFinalTICCuant, 1, 0, 'C', TRUE);
			    else
					 $this->pdf->Cell(22, 5, $vpromFinalTICCual, 1, 0, 'C', TRUE);
				 
                $this->pdf->SetXY(135, $yCursoOficial);

                if ($vpromFinalTICCual === 'A' || $vpromFinalTICCual === 'AD' || $vpromFinalTICCual === 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
				
				if($this->ano <= 2022) {
					if ($alumno->GRADOCOD > 2 && $alumno->INSTRUCOD == 'S')					
						$this->pdf->Cell(11, 5, '-', 1, 0, 'C', TRUE);
					else
						$this->pdf->Cell(11, 5, $vpromFinalTICCual, 1, 0, 'C', TRUE);
				} 

                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            } else {
				if($this->ano >=2023){
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);	
				$this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
				} else {
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(135, $yCursoOficial);

                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);					
				}

            }



# BLOQUE AUTONOMA
            $yCursoOficial += 5;
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFillColor(208, 222, 240);
            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->SetXY(20, $yCursoOficial);
            $this->pdf->Cell(60, 5, utf8_decode(($this->ano>=2023) ? 'GESTIONA SU APRENDIZAJE - AUTONOMÍA' : 'GESTIONA SU APRENDIZAJE - AUTONOMÍA'), 1, 0, 'L', TRUE);

            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetFillColor(255, 255, 255);
            if ($vunidad >= 2) {
                if ($vpromGABimestre1 > 10 && $vpromGABimestre1 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(80, $yCursoOficial);
                
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre1, $alumno->INSTRUCOD) : $vpromGABimestre1, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(80, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            if ($vunidad >= 4) {
                if ($vpromGABimestre2 > 10 && $vpromGABimestre2 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre2, $alumno->INSTRUCOD) : $vpromGABimestre2, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(91, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }
            // 3 bimestre
            if ($vunidad >= 6) {
                if ($vpromGABimestre3 > 10 && $vpromGABimestre3 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre3, $alumno->INSTRUCOD) : $vpromGABimestre3, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(102, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            // 4 bimestre
            if ($vunidad >= 8) {
                if ($vpromGABimestre4 > 10 && $vpromGABimestre4 <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, ($vFlagCuantitativo) ? $this->getCualitativoStandar($vpromGABimestre4, $alumno->INSTRUCOD) : $vpromGABimestre4, 1, 0, 'C', TRUE);
            } else {
                $this->pdf->SetXY(113, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
            }

            if ($vunidad >= 8) {
                $vpromFinalCONCuant = '';
                $vpromFinalCONCual = '';
                if ($vpromGABimestre1 != '' && $vpromGABimestre2 != '' && $vpromGABimestre3 != '' && $vpromGABimestre4 != '') {
                    // echo $vpromCondBimestre1 ."+". $vpromCondBimestre2 ."+". $vpromCondBimestre3 ."+". $vpromCondBimestre4;
                    $vpromFinalCONCuant = round((($vpromGABimestre1 + $vpromGABimestre2 + $vpromGABimestre3 + $vpromGABimestre4) / 4), 0);
                    $vpromFinalCONCual = $this->getCualitativoStandar((int) $vpromFinalCONCuant, $alumno->INSTRUCOD);
                }
                if ($vpromFinalCONCuant > 10 && $vpromFinalCONCuant <= 20) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }

                $this->pdf->SetXY(124, $yCursoOficial);
				if($this->ano <= 2022) 
					$this->pdf->Cell(11, 5, ($alumno->INSTRUCOD === "P")?'-':$vpromFinalCONCuant, 1, 0, 'C', TRUE);
				else
					$this->pdf->Cell(22, 5, $vpromFinalCONCual, 1, 0, 'C', TRUE);
				
                if ($vpromFinalCONCual === 'A' || $vpromFinalCONCual === 'AD' || $vpromFinalCONCual === 'B') {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
				if($this->ano <= 2022) {
					$this->pdf->SetXY(135, $yCursoOficial);
					if ($alumno->GRADOCOD > 2 && $alumno->INSTRUCOD == 'S')
						$this->pdf->Cell(11, 5, '-', 1, 0, 'C', TRUE);
					else
						$this->pdf->Cell(11, 5, $vpromFinalCONCual, 1, 0, 'C', TRUE);
				}
				
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
            } else {
				if($this->ano >=2023){
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(22, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);	
				$this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);				
				} else {
                $this->pdf->SetXY(124, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(135, $yCursoOficial);
                $this->pdf->Cell(11, 5, '', 1, 0, 'C', TRUE);
                $this->pdf->SetXY(146, $yCursoOficial);
                $this->pdf->Cell(8, 5, '', 1, 0, 'C', TRUE);
				}
            }
# BLOQUE DE VALORES
            if ($alumno->INSTRUCOD === "S" && $alumno->GRADOCOD >= 3 && $vunidad >= 8) {
                $vgrado = $alumno->GRADOCOD;
                if ($vunidad >= 8) {
                    $txtFn = "";
                    // Logica para la apreciacion del Alumno
                    if ($alumno->INSTRUCOD == 'P') { // Condicionar por primer grado
                        $rojo = 0;
                        if (($arrApreAnual[2] <= 10 && $arrApreAnual[5] <= 10)) { // COMUNICACION Y MATEMATICAS
                            $txtFn = "DESAPROBADO";
                            $rojo = 1;
                            // --------------- Validamos todo los cursos que tengan C ---------------
                        } elseif ($arrApreAnual[0] <= 10 || $arrApreAnual[1] <= 10 || $arrApreAnual[2] <= 10 || $arrApreAnual[3] <= 10 || $arrApreAnual[4] <= 10 || $arrApreAnual[5] <= 10 || $arrApreAnual[6] <= 10 || $arrApreAnual[7] <= 10 || $arrApreAnual[8] <= 10) {
                            $txtFn = "REQUIERE RECUPERACIÓN";
                            // -- Validamos los Cursos : 
                            // -- Matematicas y Comunicacion
                            // -- Que tengan como promedio C para los grados 2 3 y 4
                            //} elseif(($arrApreAnual[2]>=11 && $arrApreAnual[2]<=12) && ($arrApreAnual[5]>=11 && $arrApreAnual[5]<=12) && ($vgrado==2 || $vgrado==3 || $vgrado==4)){
                            //$txtFn = "REQUIERE RECUPERACIÓN";
                            // -- Validamos los Cursos : 
                            // -- Matematicas - Comunicacion - Computacion - Ingles - P. Social - CyA 
                            // -- Que tengan como promedio C para los grados 5 y 6		
                        } elseif ((($arrApreAnual[0] >= 11 && $arrApreAnual[0] <= 12) || ($arrApreAnual[3] >= 11 && $arrApreAnual[3] <= 12) || ($arrApreAnual[4] >= 11 && $arrApreAnual[4] <= 12) || ($arrApreAnual[8] >= 11 && $arrApreAnual[8] <= 12) || ($arrApreAnual[7] >= 11 && $arrApreAnual[7] <= 12) || ($arrApreAnual[2] >= 11 && $arrApreAnual[2] <= 12) || ($arrApreAnual[5] >= 11 && $arrApreAnual[5] <= 12) || ($arrApreAnual[6] >= 11 && $arrApreAnual[6] <= 12) || ($arrApreAnual[1] >= 11 && $arrApreAnual[1] <= 12)) && ($vgrado == 2 || $vgrado == 3 || $vgrado == 4 || $vgrado == 5 || $vgrado == 6)) {
                            $txtFn = "REQUIERE RECUPERACIÓN";
                        } elseif ($contdesapro > 3) {
                            $txtFn = "DESAPROBADO";
                            $rojo = 1;
                        } else {
                            $txtFn = "APROBADO";
                        }
                    } elseif ($alumno->INSTRUCOD == 'S') {
						if($this->ano>=2023){
							$rojo = 0;
							if ($contdesapro == 0) {
								$txtFn = "APROBADO";
							}  elseif ($contdesapro > 0 && $contdesapro <= 3) {
								$txtFn = "REQUIERE RECUPERACIÓN";
							} elseif ($contdesapro > 3) {
								$rojo = 1; 
								$txtFn = "DESAPROBADO"; 
							}							
						} else {						
							$rojo = 0;
							if ($contdesapro == 0) {
								$txtFn = "APROBADO";
							} elseif((int)$arrApreAnual[0]<=10 && (int)$arrApreAnual[1]<=10) { // COMUNICACION Y MATEMATICAS
							  $txtFn = "DESAPROBADO";
							  $color = 1;
							  }  elseif ($contdesapro > 0 && $contdesapro <= 3) {
								$txtFn = "REQUIERE RECUPERACIÓN";
							} elseif ($contdesapro > 3) {
								$rojo = 0; // 1;
								$txtFn = "REQUIERE RECUPERACIÓN"; //"DESAPROBADO";
							}
						}
                    }
                }
                $this->pdf->SetFont('Arial', 'B', 8);
                $yCursoOficial += 6;
                $this->pdf->SetTextColor(0, 0, 0);
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY(20, $yCursoOficial);
                $this->pdf->Cell(60, 5, utf8_decode('APRECIACIÓN ANUAL'), 1, 0, 'C', TRUE);

                if ($rojo == 0) {
                    $this->pdf->SetTextColor(0, 0, 204);
                } else {
                    $this->pdf->SetTextColor(255, 0, 51);
                }
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY(80, $yCursoOficial);
                $this->pdf->Cell(74, 5, utf8_decode($txtFn), 1, 0, 'C', TRUE);

                $this->pdf->SetTextColor(0, 0, 0);
            } else {
				if($this->ano>=2023){
					if($alumno->INSTRUCOD == 'P'){
						$this->pdf->Image("http://sistemas-dev.com/intranet/images/escala_primaria.jpg", 19.5, $yCursoOficial + 8, 135, 45, 'JPG', '');
					}
					if($alumno->INSTRUCOD == 'S'){
						$this->pdf->Image("http://sistemas-dev.com/intranet/images/escala_secundaria.jpg", 19.5, $yCursoOficial + 5.5, 135, 40, 'JPG', '');
					}
				} else {
					if ($this->ano == 2022 && $alumno->INSTRUCOD == 'P')
						$this->pdf->Image("http://sistemas-dev.com/intranet/images/valores_logros.jpg", 19.5, $yCursoOficial + 8, 135, 45, 'JPG', '');
					else
						$this->pdf->Image("http://sistemas-dev.com/intranet/images/valores_notas.jpg", 19.5, $yCursoOficial + 5.5, 135, 25, 'JPG', '');
				}
                if ($vunidad >= 8) {
                    if ($alumno->REPITE === "E")
                        $arrApreAnual[4] = 20;
                    $vgrado = $alumno->GRADOCOD;
                    $txtFn = "";
                    // Logica para la apreciacion del Alumno
                    if ($alumno->INSTRUCOD == 'P') { // Condicionar por primer grado
                        $rojo = 0;
                        if (($arrApreAnual[2] <= 10 && $arrApreAnual[5] <= 10)) { // COMUNICACION Y MATEMATICAS
                            $txtFn = "DESAPROBADO";
                            $rojo = 1;
                            // --------------- Validamos todo los cursos que tengan C ---------------
                        } elseif ($arrApreAnual[0] <= 10 || $arrApreAnual[1] <= 10 || $arrApreAnual[2] <= 10 || $arrApreAnual[3] <= 10 || $arrApreAnual[4] <= 10 || $arrApreAnual[5] <= 10 || $arrApreAnual[6] <= 10 || $arrApreAnual[7] <= 10 || $arrApreAnual[8] <= 10) {
                            //print_r($arrApreAnual); exit;
                            $txtFn = "REQUIERE RECUPERACIÓN";
                            // -- Validamos los Cursos : 
                            // -- Matematicas y Comunicacion
                            // -- Que tengan como promedio C para los grados 2 3 y 4
                            //} elseif(($arrApreAnual[2]>=11 && $arrApreAnual[2]<=12) && ($arrApreAnual[5]>=11 && $arrApreAnual[5]<=12) && ($vgrado==2 || $vgrado==3 || $vgrado==4)){
                            //$txtFn = "REQUIERE RECUPERACIÓN";
                            // -- Validamos los Cursos : 
                            // -- Matematicas - Comunicacion - Computacion - Ingles - P. Social - CyA 
                            // -- Que tengan como promedio C para los grados 5 y 6		
                        } elseif ((($arrApreAnual[0] >= 11 && $arrApreAnual[0] <= 12) || ($arrApreAnual[3] >= 11 && $arrApreAnual[3] <= 12) || ($arrApreAnual[4] >= 11 && $arrApreAnual[4] <= 12) || ($arrApreAnual[8] >= 11 && $arrApreAnual[8] <= 12) || ($arrApreAnual[7] >= 11 && $arrApreAnual[7] <= 12) || ($arrApreAnual[2] >= 11 && $arrApreAnual[2] <= 12) || ($arrApreAnual[5] >= 11 && $arrApreAnual[5] <= 12) || ($arrApreAnual[6] >= 11 && $arrApreAnual[6] <= 12) || ($arrApreAnual[1] >= 11 && $arrApreAnual[1] <= 12)) && ($vgrado == 2 || $vgrado == 3 || $vgrado == 4 || $vgrado == 5 || $vgrado == 6)) {
                            $txtFn = "REQUIERE RECUPERACIÓN";
                        } elseif ($contdesapro > 3) {
                            $txtFn = "DESAPROBADO";
                            $rojo = 1;
                        } else {
                            $txtFn = "APROBADO";
                        }
                    } elseif ($alumno->INSTRUCOD == 'S') {
						if($this->ano>=2023){
							$rojo = 0;
							if ($contdesapro == 0) {
								$txtFn = "APROBADO";
							}  elseif ($contdesapro > 0 && $contdesapro <= 3) {
								$txtFn = "REQUIERE RECUPERACIÓN";
							} elseif ($contdesapro > 3) {
								$rojo = 1; 
								$txtFn = "DESAPROBADO"; 
							}							
						} else {
							$rojo = 0;
							if ($contdesapro == 0) {
								$txtFn = "APROBADO";
							} elseif((int)$arrApreAnual[0]<=10 && (int)$arrApreAnual[1]<=10) { // COMUNICACION Y MATEMATICAS
							  $txtFn = "DESAPROBADO";
							  $color = 1;
							  }  elseif ($contdesapro > 0 && $contdesapro <= 3) {
								$txtFn = "REQUIERE RECUPERACIÓN";
							} elseif ($contdesapro > 3) {
								$rojo = 0; //1;
								$txtFn = "REQUIERE RECUPERACIÓN"; // "DESAPROBADO";
							}
						}
					}



                    $this->pdf->SetFont('Arial', 'B', 8);
					if($this->ano>=2023){
						if ($alumno->INSTRUCOD == 'P') 
							$yCursoOficial += 55;
						if ($alumno->INSTRUCOD == 'S') 
							$yCursoOficial += 42;						
					} else {
						if ($alumno->INSTRUCOD == 'P') 
							$yCursoOficial += 55;
						if ($alumno->INSTRUCOD == 'S') 
							$yCursoOficial += 30;
					}
                    $this->pdf->SetTextColor(0, 0, 0);
                    $this->pdf->SetFillColor(208, 222, 240);
                    $this->pdf->SetXY(20, $yCursoOficial);
                    $this->pdf->Cell(60, 5, utf8_decode('APRECIACIÓN ANUAL'), 1, 0, 'C', TRUE);

                    if ($rojo == 0) {
                        $this->pdf->SetTextColor(0, 0, 204);
                    } else {
                        $this->pdf->SetTextColor(255, 0, 51);
                    }

                    $this->pdf->SetFillColor(255, 255, 255);
                    $this->pdf->SetXY(80, $yCursoOficial);
                    $this->pdf->Cell(74, 5, utf8_decode($txtFn), 1, 0, 'C', TRUE);
                }
                $this->pdf->SetTextColor(0, 0, 0);
            }
# BLOQUE DE FIRMAS


            if ($alumno->INSTRUCOD === "P") {
                if ($alumno->GRADOCOD > 3) {
                    $iniYPie = 200;
                } elseif ($alumno->GRADOCOD == 3) {
                    $iniYPie = 195;
                } else {
                    $iniYPie = 190;
                }
            } else {
                if ($alumno->GRADOCOD > 2)
                    $iniYPie = 200;
                else
                    $iniYPie = 220;
            }
            /* $this->pdf->SetFont('Arial', 'B', 6);
              $this->pdf->Line(160, $iniYPie, 195, $iniYPie);
              $this->pdf->SetXY(163, $iniYPie);
              $this->pdf->Cell(30, 5, 'TUTOR(A)', 0, 0, 'C'); */
            $this->pdf->SetFont('Arial', 'B', 6);

            if ($alumno->INSTRUCOD === "P" && $vunidad == 8) {
                $this->pdf->SetXY(150, $iniYPie);
                $this->pdf->Image("http://marianista.sistemas-dev.com/images/firmas/primaria.jpg", 160, $iniYPie - 20, 37, 18);
                $this->pdf->Line(161, $iniYPie, 198, $iniYPie);
                $this->pdf->Cell(60, 5, utf8_decode('DIRECTORA: LIDIA ORÉ MONTES'), 0, 0, 'C');
            }
            if ($alumno->INSTRUCOD === "S" && $vunidad == 8) {
                if ($alumno->GRADOCOD > 2) {
                    $this->pdf->SetXY(150, $iniYPie + 10);
                    $this->pdf->Image("http://marianista.sistemas-dev.com/images/firmas/secundaria.jpg", 160, $iniYPie - 8, 37, 16);
                    $this->pdf->Line(161, $iniYPie + 10, 198, $iniYPie + 10);
                } else {
                    $this->pdf->SetXY(150, $iniYPie);
                    $this->pdf->Image("http://marianista.sistemas-dev.com/images/firmas/secundaria.jpg", 160, $iniYPie - 20, 37, 16);
                    $this->pdf->Line(161, $iniYPie, 198, $iniYPie);
                }
                $this->pdf->Cell(60, 5, utf8_decode('DIRECTOR: DOMINGO HUAYTALLA LL.'), 0, 0, 'C');
            }

            $this->pdf->SetFont('Arial', 'B', 6);
            $this->pdf->Line(161, $iniYPie + 40, 198, $iniYPie + 40);
            $this->pdf->SetXY(154, $iniYPie + 35);
            $this->pdf->Cell(50, 4, utf8_decode($alumno->PROFE), 0, 0, 'C');
            $this->pdf->SetXY(163, $iniYPie + 40);
            $this->pdf->Cell(30, 5, utf8_decode('TUTOR(A)'), 0, 0, 'C');

            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(157, $iniYPie + 60);
            $this->pdf->Cell(50, 5, 'VILLA MARIA DEL TRIUNFO ' . date("d") . ' DE ' . $this->getMesDescripcion(date('m')) . ' DEL ' . date("Y"), 0, 0, 'L');

            if ($vflgGen == 1) {
                if (!is_dir('../intranet/boletas/' . $this->ano))
                    mkdir('../intranet/boletas/' . $this->ano, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre, 0755);
                if (!is_dir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad))
                    mkdir('../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad, 0755);
                $rutaFile = '../intranet/boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/UNI' . $vunidad . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                //$pathFile = 'boletas/' . $this->ano . '/' . $vnemo . '/BIM' . $vbimestre . '/BOLETA_' . $vnemo . '_' . $alumno->DNI . '.pdf';
                $this->pdf->Output($rutaFile, 'F');
// ============ Enviando al correo la Boleta ======================================
                /* $resp = $this->objAlumno->getCorreosxAlumno($alumno->ALUCOD);
                  if ($resp) {
                  $arrEmail = array(
                  0 => array('email' => $resp->pademail, 'nombre' => $resp->padnom),
                  1 => array('email' => $resp->mademail, 'nombre' => $resp->madnom),
                  2 => array('email' => $resp->apoemail, 'nombre' => $resp->aponom)
                  );
                  EnviarMailAdjuntos($arrEmail, $pathFile);
                  } */
            }
        }

        if ($vflgGen == 0) {
            $this->pdf->Output('Reporte_boletas.pdf', 'I');
//$file_contents = $this->pdf->Output('Reporte_boletas.pdf','S');	
//echo $file_contents;
        } else {
            echo "<CENTER>PROCESO DE GENERACION DE BOLETAS GENERADO CORRECTAMENTE.</CENTER>";
            $timer = "<script>";
            $timer .= " setTimeout(function(){ ";
            $timer .= "     window.close(); ";
            $timer .= " },3000); ";
            $timer .= "</script>";
            echo $timer;
        }
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

    public function getMesDescripcion($vmes = '') {
        $vdes = "";
        switch ($vmes) {
            case "01": $vdes = "ENERO";
                break;
            case "02": $vdes = "FEBRERO";
                break;
            case "03": $vdes = "MARZO";
                break;
            case "04": $vdes = "ABRIL";
                break;
            case "05": $vdes = "MAYO";
                break;
            case "06": $vdes = "JUNIO";
                break;
            case "07": $vdes = "JULIO";
                break;
            case "08": $vdes = "AGOSTO";
                break;
            case "09": $vdes = "SETIEMBRE";
                break;
            case "10": $vdes = "OCTUBRE";
                break;
            case "11": $vdes = "NOVIEMBRE";
                break;
            case "12": $vdes = "DICIEMBRE";
                break;
            default: $vdes = "NULL";
        }
        return $vdes;
    }

    public function getCuantativo($pa = '') {
        $ncu = '';
        if ($pa == '')
            return 0;
        if ($pa == 'AD')
            $ncu = 4;
        if ($pa == 'A')
            $ncu = 3;
        if ($pa == 'B')
            $ncu = 2;
        if ($pa == 'C')
            $ncu = 1;
        return $ncu;
    }

    public function getCualitativoInicial($pa = '') {
        $ncu = '';
        if ($pa == 0)
            return '';
        if ($pa == 4)
            $ncu = 'AD';
        if ($pa == 3)
            $ncu = 'A';
        if ($pa == 2)
            $ncu = 'B';
        if ($pa == 1)
            $ncu = 'C';
        return $ncu;
    }

    public function getCualitativo($pa = 0) {
        $ncu = '';
        if ($pa == 0 || $pa =="")
            return "";
        if ($pa >= 0 && $pa <= 10)
            $ncu = 'C';
        if ($pa >= 11 && $pa <= 12)
            $ncu = 'B';
        if ($pa >= 13 && $pa <= 16)
            $ncu = 'A';
        if ($pa >= 17 && $pa <= 20)
            $ncu = 'AD';
        return $ncu;
    }

    public function getCualitativoStandar($pa = 0, $nivel='') {
        $ncu = '';
        if ($pa == "EXO") {
            $ncu = 'EXO';
        } elseif ($pa == "") {
			$ncu = '';
		} else {
			if($nivel=='P'){
				if ($pa >= 0 && $pa <= 10)
					$ncu = 'C';
				if ($pa >= 11 && $pa <= 12)
					$ncu = 'B';
				if ($pa >= 13 && $pa <= 16)
					$ncu = 'A';
				if ($pa >= 17 && $pa <= 20)
					$ncu = 'AD';
			} elseif($nivel=='S'){
				if ($pa >= 0 && $pa <= 10)
					$ncu = 'C';
				if ($pa >= 11 && $pa <= 13)
					$ncu = 'B';
				if ($pa >= 14 && $pa <= 17)
					$ncu = 'A';
				if ($pa >= 18 && $pa <= 20)
					$ncu = 'AD';				
			}

        }
        return $ncu;
    }

}
