<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class sga_aulas extends CI_Controller {

    public $token = '';
    public $modulo = 'AULAS';
    public $ano = '';

    function __construct() {
        parent::__construct();
        $this->load->model('salon_model', 'obj_salon');
        $this->load->library('form_validation');
        $this->load->model('seguridad_model');
        $this->ano = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('lista-salon-js');
        $this->load->view('lista-salon-view');
        $this->load->view('view_footer');
    }

    public function cargaInit() {
        $arrAulas = array();
        $dataAulas = $this->obj_salon->getAllAulas();
        if (is_array($dataAulas) && count($dataAulas) > 0) {
            $arrAulas = array(
                'total' => count($dataAulas),
                'dataAulas' => $dataAulas
            );
        }
        echo json_encode($arrAulas);
    }

    public function getListadoPorNemo() {
        $arrAlumnos = array();
        $vnemo = $this->input->post("vnemo");
        $dataAlumnos = $this->obj_salon->getAlumnosNemo($vnemo);
        if (is_array($dataAlumnos) && count($dataAlumnos) > 0) {
            $arrAlumnos = array(
                'total' => count($dataAlumnos),
                'dataAlumnos' => $dataAlumnos
            );
        } else {
            $arrAlumnos = array(
                'total' => 0,
                'dataAlumnos' => array()
            );
        }
        echo json_encode($arrAlumnos);
    }

    public function printLista($vnemo) {
        // -------------------------- Query's para el Reporte ---------------------------------------------------------------------------
        if ($this->ano < date("Y")) {
            $dataAlumno = $this->obj_salon->getAlumnosAnteriores($vnemo);
        } else {
            $dataAlumno = $this->obj_salon->getAlumnosNemo($vnemo);
        }
        $dataAula = $this->obj_salon->getDatosNemo($vnemo);
        // --------------------------------------------------------------------------------------------------------------------------------------
        $this->load->library('pdf');
        $this->pdf = new Pdf();
        #Establecemos los m�rgenes izquierda, arriba y derecha:
        //$this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 20, 5, 18, 18, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(190, 10, 'LISTA DE ALUMNOS - ' . $this->ano, 0, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(20, 25);
        $this->pdf->Cell(20, 5, utf8_decode('AULA : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(40, 25);
        $this->pdf->Cell(50, 5, utf8_decode($dataAula->nemodes), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetXY(20, 30);
        $this->pdf->Cell(20, 5, 'TUTOR(A) : ', 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(40, 30);
        $this->pdf->Cell(50, 5, utf8_decode($dataAula->nomcomp), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetXY(20, 35);
        $this->pdf->Cell(170, 5, '..............................................................................................................................................................................................', 0, 0, 'C');

        $this->pdf->SetXY(20, 40);
        $this->pdf->Cell(5, 5, utf8_decode('Nº'), 0, 0, 'C');
        $this->pdf->SetXY(25, 40);
        $this->pdf->Cell(25, 5, 'DNI', 0, 0, 'C');
        $this->pdf->SetXY(50, 40);
        $this->pdf->Cell(115, 5, 'APELLIDOS Y NOMBRES', 0, 0, 'C');
        $this->pdf->SetXY(165, 40);
        $this->pdf->Cell(30, 5, 'ESTADO', 0, 0, 'C');

        $this->pdf->SetXY(20, 42);
        $this->pdf->Cell(170, 5, '..............................................................................................................................................................................................', 0, 0, 'C');
        $rowY = 47;
        $fila = 1;
        foreach ($dataAlumno as $row) {
            if($row->estado==="MATRICULADO" && $this->ano<date("Y")){
                $this->pdf->SetFillColor(195,253,213);
                //$this->pdf->SetFont('Arial', 'B', 9);
            } else {
                $this->pdf->SetFillColor(255,255,255);
                //$this->pdf->SetFont('Arial', '', 9);
            }
            $this->pdf->SetXY(20, $rowY);
            $this->pdf->Cell(5, 5, $fila, 0, 0, 'C', TRUE);
            $this->pdf->SetXY(25, $rowY);
            $this->pdf->Cell(25, 5, $row->dni, 0, 0, 'C', TRUE);
            $this->pdf->SetXY(50, $rowY);
            $this->pdf->Cell(115, 5, utf8_decode($row->nomcomp), 0, 0, 'L', TRUE);
            $this->pdf->SetXY(165, $rowY);
            $this->pdf->Cell(30, 5, $row->estado, 0, 0, 'C', TRUE);
            $rowY += 5;
            $fila ++;
        }

        $this->pdf->Output('Listado_de_Alumno_(AULA: ' . utf8_decode($dataAula->nemodes) . ').pdf', 'I');
    }

}
