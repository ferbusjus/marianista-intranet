<?php

/**
 * @package       modules/sga_reporte_mes/controller
 * @name            sga_reporte_mes.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 2017/12/26
 */
class sga_reporte_mes extends CI_Controller {

    public $S_ANO = '';
    public $token = '';
    public $modulo = 'REPORTE-MES';

    public function __construct() {
        parent::__construct();
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('egresos_model', 'objEgresos');
        $this->load->model('seguridad_model');
        $this->S_ANO = $vano = $this->nativesession->get('S_ANO_VIG');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
//        $data["dataNivel"] = $this->objSalon->getNivel ();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('view_reporte_mes', $data);
        $this->load->view('js_reporte');
        $this->load->view('view_footer');
    }

    public function print_rmes() {
        /* echo "<pre>";
          print_r ($_POST);
          echo "</pre>"; */

        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vmes = $this->input->post("cbmes");

        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->SetAutoPageBreak(true, 5);

        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE CIERRE DE MES   - ' . $this->S_ANO, 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, 'Mes : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(30, 5, $this->fn_mes($vmes), 0, 0, 'L');
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
        $this->pdf->Cell(40, 5, utf8_decode('FECHA'), 0, 0, 'C');
        $this->pdf->Cell(60, 5, utf8_decode('COMPROBANTE'), 0, 0, 'C');
        $this->pdf->Cell(40, 5, utf8_decode('DEPOSITO'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('EFECTIVO'), 0, 0, 'R');
        $this->pdf->Cell(30, 5, utf8_decode('GASTOS'), 0, 0, 'R');
        $this->pdf->Cell(40, 5, utf8_decode('TOTAL'), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $fechaInicio = strtotime(ANO_VIG . "-" . $vmes . "-01");
        $fechaFin = strtotime(ANO_VIG . "-" . $vmes . "-" . $this->getUltimoDiaMes($this->S_ANO, $vmes)); //strtotime ("2017-" . $vmes . "-30");
        $this->pdf->SetFont('Arial', '', 10);

        $vtotaldeposito = 0;
        $vtotalefectivo = 0;
        $vtotalmegreso = 0;
        $vtotales = 0;
        for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {
            $dia = date('N', $i);
            if (/* $dia == 6 || */$dia == 7) { // Sabado / Domingo
                // ----------------------
            } else {
                $vfecha = date("Y-m-d", $i);
                $vcomprobante = $this->objCobros->getDatoReporte(1, $vfecha, $this->S_ANO);
                $vDepo = $this->objCobros->getDatoReporte(3, $vfecha, $this->S_ANO);
                $vEfect = $this->objCobros->getDatoReporte(2, $vfecha, $this->S_ANO);
                $vEgreso = $this->objCobros->getDatoReporte(4, $vfecha, $this->S_ANO);
                if (trim($vcomprobante[0]->minimo) != '' && trim($vcomprobante[0]->maximo) != '') {
                    $vcomp = $vcomprobante[0]->minimo . " - " . $vcomprobante[0]->maximo;
                } else {
                    $vcomp = "0000 - 0000";
                }
                if ($vDepo[0]->monto > 0) {
                    $vdeposito = $vDepo[0]->monto;
                    $vtotaldeposito += $vdeposito;
                } else {
                    $vdeposito = "0.00";
                }
                if ($vEfect[0]->monto > 0) {
                    $vefectivo = $vEfect[0]->monto;
                    $vtotalefectivo += $vefectivo;
                } else {
                    $vefectivo = "0.00";
                }

                if ($vEgreso[0]->monto > 0) {
                    $vmegreso = $vEgreso[0]->monto;
                    $vtotalmegreso += $vmegreso;
                } else {
                    $vmegreso = "0.00";
                }

                $vtotal = round(($vefectivo - $vmegreso), 2);
                $vtotales += $vtotal;

                /* echo "Fecha :".$vfecha;
                  echo "<pre>";
                  print_r($vcomprobante);
                  echo "</pre>"; */
                $this->pdf->Cell(40, 5, $vfecha, 0, 0, 'C');
                $this->pdf->Cell(60, 5, $vcomp, 0, 0, 'C');
                $this->pdf->Cell(40, 5, 'S/.' . $vdeposito, 0, 0, 'R');
                $this->pdf->Cell(30, 5, 'S/.' . $vefectivo, 0, 0, 'R');
                $this->pdf->Cell(30, 5, 'S/.' . $vmegreso, 0, 0, 'R');
                $this->pdf->Cell(40, 5, 'S/.' . number_format($vtotal, 2, '.', ','), 0, 0, 'R');
                $this->pdf->Ln(5);
            }
        }

        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(40, 5, '', 0, 0, 'C');
        $this->pdf->Cell(60, 5, 'TOTALES ==>>', 0, 0, 'C');
        $this->pdf->Cell(40, 5, 'S/.' . number_format($vtotaldeposito, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalefectivo, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(30, 5, 'S/.' . number_format($vtotalmegreso, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(40, 5, 'S/.' . number_format($vtotales, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

        $this->pdf->Output('Reporte_de_cierre_mensual.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

    public function getUltimoDiaMes($elAnio, $elMes) {
        return date("d", (mktime(0, 0, 0, $elMes + 1, 1, $elAnio) - 1));
    }

    public function fn_mes($vmes = '') {
        $vresp = '';
        switch ($vmes) {
            case '01' :
                $vresp = 'ENERO';
                break;
            case '02' :
                $vresp = 'FEBRERO';
                break;
            case '03' :
                $vresp = 'MARZO';
                break;
            case '04' :
                $vresp = 'ABRIL';
                break;
            case '05' :
                $vresp = 'MAYO';
                break;
            case '06' :
                $vresp = 'JUNIO';
                break;
            case '07' :
                $vresp = 'JULIO';
                break;
            case '08' :
                $vresp = 'AGOSTO';
                break;
            case '09' :
                $vresp = 'SETIEMBRE';
                break;
            case '10' :
                $vresp = 'OCTUBRE';
                break;
            case '11' :
                $vresp = 'NOVIEMBRE';
                break;
            case '12' :
                $vresp = 'DICIEMBRE';
                break;
        }
        return $vresp;
    }

}
