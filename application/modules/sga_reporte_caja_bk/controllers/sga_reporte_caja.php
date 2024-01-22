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
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataNivel"] = $this->objSalon->getNivel();
        $data["dataUsuarios"] = $this->objCobros->getUsuarios();
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

    public function printpagos() {
        /* echo "<pre>";
          print_r ($_POST);
          echo "</pre>"; */

        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vnivel = $this->input->post("cbnivel");
        $vfecha = $this->input->post("finicial");
        $vusuario = $this->input->post("cbusuario");
        $vcomp = $this->input->post("cbcomprobante");
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
        $this->pdf->Cell(20, 5, utf8_decode('Nivel : '), 0, 0, 'L');
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
        $this->pdf->Cell(110, 5, utf8_decode('APELLIDOS Y NOMBRES'), 0, 0, 'C');
        $this->pdf->Cell(10, 5, utf8_decode('NGS'), 0, 0, 'C');
        if ($vusuario == 'T') {
            $this->pdf->Cell(50, 5, utf8_decode('CONCEPTO DE PAGO'), 0, 0, 'C');
            $this->pdf->Cell(20, 5, utf8_decode('USUARIO'), 0, 0, 'C');
        } else {
            $this->pdf->Cell(70, 5, utf8_decode('CONCEPTO DE PAGO'), 0, 0, 'C');
        }
        $this->pdf->Cell(20, 5, utf8_decode('BANCO'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('CAJA'), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);

        if ($vnivel == 'T') {
            $arrayNivel = array(
                'I' => 'INICIAL',
                'P' => 'PRIMARIA',
                'S' => 'SECUNDARIA'
            );
        } else {
            $arrayNivel = array($vnivel => $vniveldes);
        }
        $vtotalfb = 0;
        $vtotalfc = 0;
        $vnfecha = substr($vfecha, 6, 4) . '-' . substr($vfecha, 3, 2) . '-' . substr($vfecha, 0, 2);
        foreach ($arrayNivel as $key => $value) {
            //$vfecha = '2017-08-27'; //date ("Y-m-d", strtotime ($vfecha));
            $vtotalc = 0;
            $vtotalb = 0;
            $dataPago = $this->objCobros->getPagoxNivel($key, $vnfecha, $this->S_ANO, $vusuario, $vcomp);
            $this->pdf->SetFont('Arial', '', 10);
            if (is_array($dataPago) && count($dataPago) > 0) {
                foreach ($dataPago as $pagos) {
                    $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
                    $this->pdf->Cell(110, 5, utf8_decode($pagos->nomcomp), 0, 0, 'L');
                    $this->pdf->Cell(10, 5, $pagos->ngs, 0, 0, 'C');
                    if ($vusuario == 'T') {
                        $this->pdf->Cell(50, 5, utf8_decode($pagos->condes . ' - ' . $pagos->mesdes), 0, 0, 'L');
                        $this->pdf->Cell(20, 5, utf8_decode($pagos->usumod), 0, 0, 'C');
                    } else {
                        $this->pdf->Cell(70, 5, utf8_decode($pagos->condes . ' - ' . $pagos->mesdes), 0, 0, 'L');
                    }
                    //$this->pdf->Cell (20, 5, (($pagos->tipopago == 'C') ? 'Caja' : 'Banco'), 0, 0, 'C');
                    if ($pagos->tipopago == 'B') {
                        $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                        $this->pdf->Cell(30, 5, '', 0, 0, 'C');
                        $vtotalb += $pagos->montocob;
                    } else {
                        $this->pdf->Cell(20, 5, '', 0, 0, 'C');
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
        $dataPagoOtros = $this->objCobros->getPagosOtros($vnfecha, $this->S_ANO, $vusuario, $vcomp);
        $vtotalAdic = 0;
        if (is_array($dataPagoOtros) && count($dataPagoOtros) > 0) {
            foreach ($dataPagoOtros as $pagos) {
                $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
                $this->pdf->Cell(110, 5, utf8_decode($pagos->nomcomp), 0, 0, 'L');
                $this->pdf->Cell(10, 5, $pagos->ngs, 0, 0, 'C');
                if ($vusuario == 'T') {
                    $this->pdf->Cell(50, 5, utf8_decode($pagos->condes), 0, 0, 'L');
                    $this->pdf->Cell(20, 5, utf8_decode($pagos->usumod), 0, 0, 'C');
                } else {
                    $this->pdf->Cell(70, 5, utf8_decode($pagos->condes), 0, 0, 'L');
                }
                if ($pagos->tipopago == 'B') {
                    $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                    $this->pdf->Cell(30, 5, '', 0, 0, 'C');
                    $vtotalb += $pagos->montocob;
                } else {
                    $this->pdf->Cell(20, 5, '', 0, 0, 'C');
                    $this->pdf->Cell(30, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
                    $vtotalAdic += $pagos->montocob;
                }
                $this->pdf->Ln(5);
            }

            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(220, 5, 'TOTAL OTROS =>', 0, 0, 'R');
            $this->pdf->Cell(20, 5, 'S/.0.00', 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalAdic, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
        }

        if ($vtotalfb > 0 || $vtotalfc > 0) {
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(5);
            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(240, 5, 'TOTAL BANCO =>', 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalfb, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            $this->pdf->Cell(240, 5, 'TOTAL CAJA =>', 0, 0, 'R');
            $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalfc, 2, '.', ','), 0, 0, 'R');
            $this->pdf->Ln(5);
            if ($vtotalAdic > 0) {
                $this->pdf->Cell(240, 5, 'TOTAL OTROS =>', 0, 0, 'R');
                $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalAdic, 2, '.', ','), 0, 0, 'R');
                $this->pdf->Ln(5);
            }

            $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
            $this->pdf->Ln(5);
            $this->pdf->Cell(240, 5, 'TOTAL =>', 0, 0, 'R');
            if ($vtotalAdic > 0) {
                $this->pdf->Cell(30, 5, 'S/.' . number_format(($vtotalfb + $vtotalfc + $vtotalAdic), 2, '.', ','), 0, 0, 'R');
            } else {
                $this->pdf->Cell(30, 5, 'S/.' . number_format(($vtotalfb + $vtotalfc), 2, '.', ','), 0, 0, 'R');
            }
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
