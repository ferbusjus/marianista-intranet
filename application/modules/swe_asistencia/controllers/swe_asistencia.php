<?php

/**
 * @package       modules/sga_asistencia/controller
 * @name            sga_asistencia.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/13
 */
class swe_asistencia extends CI_Controller {

    public $token = '';
    public $modulo = 'ASISTENCIA';
    public $datasession = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('asistencia_model', 'objAsistencia');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('observacion_model', 'objObservacion');
        $this->load->model('seguridad_model');
        $this->datasession = $this->nativesession->get('arrDataSesion');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $data['token'] = $this->token();
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data["dataHijos"] = $this->objAlumno->getHijos($this->datasession['FAMCOD']);
        // Validando le nivel y grados que marcan asistencia
        $verificaAsistencia = $this->objAsistencia->verificaNivelxFamilia($this->datasession['FAMCOD']);
        $this->load->view('constant');
        $this->load->view('view_header');
        if ($verificaAsistencia) {
            $this->load->view('js_asistencia');
            $this->load->view('view_asistencia', $data);
        } else {
            $this->load->view('view_default');
        }
        $this->load->view('view_footer');
    }

    public function listar() {
        $dataAsis = array('data' => '', 'msg' => 0);
        if ($this->input->post('token') && $this->input->post('token') == $this->nativesession->get('token')) {
            sleep(1);
            $dataForm = json_decode($this->input->post('dataForm'));
            $arrData = array(
                'ALUCOD' => $dataForm->valumno,
                'NEMO' => $dataForm->vsalon,
                'TIPO' => $dataForm->vtipo,
                'MES' => $dataForm->vmes
            );

            $arrAsis = $this->objAsistencia->getAsistenciasAll($arrData);
            $dataAsis = array('data' => $arrAsis, 'msg' => 1);
        } else {
            $dataAsis = array('data' => '', 'msg' => 2);
        }
        echo json_encode($dataAsis);
        //redirect('login', 'refresh');    	      
    }

    public function generaReporte() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        setlocale(LC_TIME, 'es_ES');
        $vcombo = explode("|", $this->input->post("cbalumno"));
        $vCodigo = $vcombo[1];
        $vSalon = $vcombo[0];
        $vTipo = $this->input->post("cbtipo");
        $vMes = $this->input->post("cbmes");

        $dataAlumno = $this->objSalon->getDatoAlumno($vCodigo);
        $this->load->library('pdf');

        $this->pdf = new Pdf('L', 'mm', 'A5');
        #Establecemos los márgenes izquierda, arriba y derecha:
        //$this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 2, 18, 18, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(190, 10, 'REPORTE DE ASISTENCIA - ' . date("Y"), 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->Cell(20, 5, utf8_decode('Código : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->Cell(25, 5, $dataAlumno[0]->ALUCOD, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->Cell(45, 5, 'Apellidos y Nombres : ', 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->Cell(100, 5, utf8_decode($dataAlumno[0]->NOMCOMP), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->Cell(20, 5, 'NGS      : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->Cell(25, 5, $dataAlumno[0]->INSTRUCOD . $dataAlumno[0]->GRADOCOD . $dataAlumno[0]->SECCIONCOD, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->Cell(45, 5, 'Aula                             : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->Cell(100, 5, utf8_decode($dataAlumno[0]->NEMODES), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Line(10, 32, 200, 32);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->Ln();
        // obtenemos las posiciones de XY
        $this->pdf->SetFont('Arial', 'B', 10);
        $vx = $this->pdf->GetX();
        $vy = $this->pdf->GetY();
        $this->pdf->SetXY($vx, $vy);
        $this->pdf->Cell(20, 5, 'Fecha ', 'T,B', 0, 'C');
        $this->pdf->Cell(20, 5, 'Hora ', 'T,B', 0, 'C');
        $this->pdf->Cell(20, 5, 'T- Asis. ', 'T,B', 0, 'C');
        $this->pdf->Cell(20, 5, 'T- Ing. ', 'T,B', 0, 'C');
        $this->pdf->Cell(110, 5, 'Observaciones', 'T,B', 0, 'C');
        $this->pdf->SetFont('Arial', '', 10);

        $arrData = array(
            'ALUCOD' => $vCodigo,
            'NEMO' => $vSalon,
            'TIPO' => $vTipo,
            'MES' => $vMes
        );

        $dataAsis = $this->objAsistencia->getAsistenciasAll($arrData);
        foreach ($dataAsis as $row) {
            $this->pdf->SetXY($vx, $vy + 5);
            $this->pdf->Cell(20, 5, strftime('%a ,%d-%b ', strtotime($row->fecha)), 'B', 0, 'C');
            $this->pdf->Cell(20, 5, $row->hora, 'B', 0, 'C');
            if ($row->t_asist == "") {
                //$this->pdf->SetTextColor(252,5,5);  
                if ($row->t_asist == "" && $row->evento == "I") {
                    $this->pdf->SetFont('Arial', 'B', 10);
                    $marca = "FALTO";
                } elseif ($row->t_asist == "" && $row->evento == "S") {
                    $marca = "P";
                } else {
                    $marca = $row->t_asist;
                }
                $this->pdf->Cell(20, 5, $marca, 'B', 0, 'C');
            } else {
                if ($row->t_asist == "V" && $row->evento == "I") {
                    $this->pdf->SetFont('Arial', 'B', 10);
                    $marca = "VACACIONES";
                    $this->pdf->Cell(20, 5, $marca, 'B', 0, 'C');
                } elseif ($row->t_asist == "R" && $row->evento == "I") {
                    $this->pdf->SetFont('Arial', 'B', 10);
                    $marca = "FERIADO";
                    $this->pdf->Cell(20, 5, $marca, 'B', 0, 'C');
                } elseif ($row->t_asist == "E" && $row->evento == "I") {
                    $this->pdf->SetFont('Arial', 'B', 10);
                    $marca = "EVENTO";
                    $this->pdf->Cell(20, 5, $marca, 'B', 0, 'C');
                } elseif ($row->t_asist == "P" && $row->evento == "I") {
                    $this->pdf->SetFont('Arial', 'B', 9);
                    $marca = "PUNTUAL";
                    $this->pdf->Cell(20, 5, $marca, 'B', 0, 'C');
                } elseif ($row->t_asist == "T" && $row->evento == "I") {
                    $this->pdf->SetFont('Arial', 'B', 9);
                    $marca = "TARDANZA";
                    $this->pdf->Cell(20, 5, $marca, 'B', 0, 'C');
                } else {
                    $this->pdf->SetFont('Arial', '', 10);
                    //$this->pdf->SetTextColor(0,0,0); 
                    $this->pdf->Cell(20, 5, $row->t_asist, 'B', 0, 'C');
                }
            }
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(20, 5, (($row->evento == "I") ? "Ingreso" : "Salida"), 'B', 0, 'C');
            $this->pdf->MultiCell(110, 5, $row->conducta, 'T,B', 'L');
            $vy += 5;
        }

        $this->pdf->Output('Reporte_de_Asistencia.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
