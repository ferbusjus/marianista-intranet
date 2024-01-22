<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class verBoletas extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('cobros_model', 'objCobro');
    }

    public function index() {
        //Ym9sZXRhc18yMDE5MDY4XzIwMTcwNDM2LnBkZg==
        //$encrip = base64_encode("boletas_2019068_20170436.pdf");
	
        $vId = $this->input->get("id");
        if ($vId != "") {
            $ano = 2023; // date("Y");
            $idArrDeco = base64_decode($vId);
            $idArrDeco = explode("*", $idArrDeco);
            $valucod = trim($idArrDeco[0]);
            $vdni = trim($idArrDeco[1]);
            $vnemo = trim($idArrDeco[2]);

            // verificacion de Nivel
            if ($vnemo <= '2023013') {
                $fechafin = "2023-12-21 13:00:00";
                $fechahora = "2023*12*21*13*00*00";
                $txtPantalla = "INICIAL";
            } elseif ($vnemo >= '2023014' && $vnemo <= '2023045') {
                $fechafin = "2023-12-21 13:00:00";
                $fechahora = "2023*12*21*13*00*00";
                $txtPantalla = "PRIMARIA";
            } elseif ($vnemo >= '2023047') {
                $fechafin = "2023-12-21 13:00:00";
                $fechahora = "2023*12*21*13*00*00";
                $txtPantalla = "SECUNDARIA";
            }
            // Verificacion de Fechas
            $hoy = date("Y-m-d H:i:s");
            $temporal = ($hoy >= $fechafin) ? 0 : 1;
            //
            //  echo $temporal; exit;
            if ($temporal == 1 /* && $vnemo >= '2020040' */) {
                $data['fechafin'] = $fechafin;
                $data['fechahora'] = $fechahora;
                $data['txtPantalla'] = $txtPantalla;
                // ================ Temporal ======================
                $this->load->view('verboleta/view-conteo', $data);
                // ================================================
            } else {
                # 1. VERIFICAMSOS SI EL ALUMNO TIENE BECA INTEGRAL
                $tieneBeca = $this->objCobro->getTieneBecaAlumno($valucod, $ano);
                if ($tieneBeca == '1') { // 1 : BECA TOTAL 0 : NO TIENE BECA
                    $vbimestre = 4;
                    $vunidad = 8;
                    // Cuando es por Unidades agregar a la ruta la carpeta por Unidad
                    $pdf = $vnemo . "/BIM" . $vbimestre . "/UNI" . $vunidad . "/BOLETA_" . $vnemo . "_" . $vdni . ".pdf";
                    $ruta = "boletas/" . $ano . "/" . $pdf;
                    header("Content-type:application/pdf");
                    header("Content-Disposition:inline;filename='" . $ruta . "'");
                    readfile($ruta);
                } else {
                    $totalPago = $this->objCobro->getTotalPagos($valucod, $ano);
					//echo $totalPago; exit;
                    if ($totalPago == 0 || $totalPago < 9) { // No ha realizado ningun pago
                        $dataPago = $this->objCobro->getPagosviewBoleta($valucod, $ano);
                        $cadmeses = "";
                        foreach ($dataPago as $row) {
                            $cadmeses .= nombreMesesCompleto($row->mescob) . ", ";
                        }
                        $cadmeses = substr($cadmeses, 0, strlen($cadmeses) - 2);
                        $data["ruta"] = "";
                        $data["vmeses"] = $cadmeses;
                        $data["datapago"] = $dataPago;
                        $this->load->view('verboleta/view-bloqueo', $data);
                    } else {
                        $vbimestre = 4;
                        $vunidad = 8;
                        $pdf = $vnemo . "/BIM" . $vbimestre . "/UNI" . $vunidad . "/BOLETA_" . $vnemo . "_" . $vdni . ".pdf";
                        $ruta = "boletas/" . $ano . "/" . $pdf;
                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename='" . $ruta . "'");
                        readfile($ruta);
                        /* if ($totalPago == 1) { // Pago hasta Marzo
                          $vbimestre = 1;
                          $vbunidad = 1;
                          $pdf = $vnemo . "/BIM" . $vbimestre . "/UNI" . $vunidad . "/BOLETA_" . $vnemo . "_" . $vdni . ".pdf";
                          $ruta = "boletas/" . $ano . "/" . $pdf;
                          } else {
                          $ruta = "";
                          }
                          $dataPago = $this->objCobro->getPagosviewBoleta($valucod,$ano);
                          if (count($dataPago) > 0) {
                          $cadmeses = "";
                          foreach ($dataPago as $row) {
                          $cadmeses .= nombreMesesCompleto($row->mescob) . ", ";
                          }
                          $cadmeses = substr($cadmeses, 0, strlen($cadmeses) - 2);
                          $data["ruta"] = base64_encode($ruta);
                          $data["flgver"] = 1; // (($vbimestre == 4) ? 1 : 0);
                          $data["vmeses"] = $cadmeses;
                          $data["datapago"] = $dataPago;
                          $this->load->view('verboleta/view-default', $data);
                          } else {
                          header("Content-type:application/pdf");
                          header("Content-Disposition:inline;filename='" . $ruta . "'");
                          readfile($ruta);
                          } */
                    }
                }
            }
        } else {
            echo "<center>NO ESTAS AUTORIZADO. <BR>FAVOR DE INGRESAR DESDE EL CAMPUS MARIANISTA.<br><a href='http://marianista.edu.pe'>Ir al Campus</a></center>";
        }
    }

    public function view() {
        if (isset($_POST['hruta'])) {
            $ruta = $this->input->post("hruta");
            $ruta = base64_decode($ruta);
            header("Content-type:application/pdf");
            header("Content-Disposition:inline;filename='" . $ruta . "'");
            readfile($ruta);
        } else {
            echo "<center>NO ESTAS AUTORIZADO. <BR>FAVOR DE INGRESAR DESDE EL CAMPUS MARIANISTA.<br><a href='http://marianista.pe'>Ir al Campus</a></center>";
        }
    }

    public function generar() {
        $dataAlumnos = $this->objAlumno->getAlumnosBoletas();
        foreach ($dataAlumnos as $row) {
            //$url = base64_encode($row->nemo . "/BIM" . $vbimestre . "/BOLETA_" . $row->nemo . "_" . $row->alucod . ".pdf");
            //$url = "http://sistemas-dev.com/intranet/verBoletas?id=" . $url;
            $url = "http://sistemas-dev.com/intranet/verBoletas?id=" . base64_encode($row->alucod . "*" . $row->dni . "*" . $row->nemo);
            $this->objAlumno->updateRuta($row->alucod, $url);
        }
    }

}
