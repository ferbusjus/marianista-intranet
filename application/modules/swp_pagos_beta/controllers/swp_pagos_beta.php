<?php

/**
 * @package       modules/sga_pagos/controller
 * @name            sga_pagos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 2017/07/09
 */
class swp_pagos_beta extends CI_Controller {

    public $token = '';
    public $modulo = 'PAGOS';

    public function __construct() {
        parent::__construct();
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('seguridad_model');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        /* if ($this->seguridad_model->restringirIp () == FALSE) {
          $this->load->view ('constant');
          $this->load->view ('view_header');
          $this->load->view ('view_default');
          $this->load->view ('view_footer');
          } else { */
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data['token'] = $this->token();
        $data["dataSalones"] = $this->objSalon->getSalones();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('js_pagos');
        $this->load->view('view_lista', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function adicional() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $data['token'] = $this->token();
        //$data["dataSalones"] = $this->objSalon->getSalones ();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('js_adicional');
        $this->load->view('view_lista_adiconal');
        $this->load->view('view_footer');
    }

    public function getPago() {
        $output = array();
        $flgArr = 0;
        if ($this->input->is_ajax_request()) {
            

            
            $vIdAlumno = $this->input->post("vIdAlumno");
            $varrCheck = $this->input->post("varrCheck");
            // if ($varrCheck != "" && strlen ($varrCheck) > 5) {
            $varrCheck = substr($varrCheck, 0, -1);
            $vsqlMescobIn = "";
            $vsqlConcobIn = "";
            if (strlen($varrCheck) > 5) {
                $flgArr = 1;
                $varrCheck = explode("*", $varrCheck);
                foreach ($varrCheck as $row) {
                    $vParte = explode("|", $row);
                    $vsqlMescobIn .= $vParte[1] . ",";
                    $vsqlConcobIn .= $vParte[0] . ",";
                }
            } else {
                $vParte = explode("|", $varrCheck);
                $vsqlMescobIn .= $vParte[1];
                $vsqlConcobIn .= $vParte[0];
            }

            if ($vsqlMescobIn != "" && $vsqlConcobIn != "" && $flgArr == 1) {
                $vsqlMescobIn = substr($vsqlMescobIn, 0, -1);
                $vsqlConcobIn = substr($vsqlConcobIn, 0, -1);
            }

            $dataSalon = $this->objSalon->getSalones("2019001");
            print_r($dataSalon); exit;            
            $data = $this->objCobros->getPagoxId($vIdAlumno, $vsqlMescobIn, $vsqlConcobIn);


                        
            $arrData = array();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $arrData [] = $fila;
                }
            }
            $output = array(
                "data" => $arrData,
                "arrMescodId" => $vsqlMescobIn,
                "arrConcodId" => $vsqlConcobIn
            );
        }
        echo json_encode($output);
    }

    public function savePago() {
        $output = array();
        if ($this->input->is_ajax_request()) {
            $vIdAlu = $this->input->post("vIdAlu");
            $vIdsMes = $this->input->post("vIdsMes");
            $vIdsCobro = $this->input->post("vIdsCobro");
            $vnumrec = $this->input->post("vnumrec");
            $vfecha = $this->input->post("vfecha") . ' ' . date("H:i:s");
            $varrPagos = $this->input->post("varrPagos");
            $vcbtipo = $this->input->post("vcbtipo");
            $vcomp = $this->input->post("vcomp");
            $vIdnemo = $this->input->post("vidnemo");
            // ------ Obtenemos el instrucod por Codigo de Salon
            $dataSalon = $this->objSalon->getSalones($vIdnemo);
            $instru = $dataSalon[0]->INSTRUCOD;
            if ($instru == 'I' || $instru == 'P') {
                $vruc = 'R01';
            } else {
                $vruc = 'R02';
            }
            // --------------------------------------------------------
            if (strlen($vIdsMes) == 2) {
                if ($vIdsCobro == '02' /* || $vIdsCobro == '01' */ && $vIdsMes == '03') {
                    //$varrPagos = substr ($varrPagos, 0, -1);
                    $varrPagos = trim($varrPagos);
                    $data = $this->objCobros->grabarPension($vIdAlu, $vIdsMes, $vIdsCobro, $vnumrec, $varrPagos, $vcbtipo, $vfecha, $vcomp, $vruc);
                } else {
                    $data = $this->objCobros->grabarPension($vIdAlu, $vIdsMes, $vIdsCobro, $vnumrec, 0, $vcbtipo, $vfecha, $vcomp, $vruc);
                }
            } else {
                $arrDataMes = explode(",", $vIdsMes);
                $arrDataCob = explode(",", $vIdsCobro);
                $arrDataPago = explode("|", $varrPagos);
                $i = 0;
                for ($x = 0; $x < count($arrDataMes); $x++) {
                    if ($arrDataCob[$x] == '02' /* || $arrDataCob[$x] == '01' */ && $arrDataMes[$x] == '03') {
                        $data = $this->objCobros->grabarPension($vIdAlu, $arrDataMes[$x], $arrDataCob[$x], $vnumrec, $arrDataPago[$i], $vcbtipo, $vfecha, $vcomp, $vruc);
                        $i++;
                    } else {
                        $data = $this->objCobros->grabarPension($vIdAlu, $arrDataMes[$x], $arrDataCob[$x], $vnumrec, 0, $vcbtipo, $vfecha, $vcomp, $vruc);
                    }
                }
            }
            if ($data) {
                $output = array(
                    "msg" => "Pago Realizado con Exito."
                );
            } else {
                $output = array(
                    "msg" => "Hubo un error en la Transaccion. Vuelva a intentarlo."
                );
            }
        }
        echo json_encode($output);
    }

    public function srvreniec() {
        $vnumdocu = $this->input->post("num_documento");
        $vtipo = $this->input->post("tipo");
        // El servico es de 6 Meses  | Inicio el 17.03.2019
        $password = 'fergus_aLKJHLKJ876987ADSFGASDF';
        if ($vtipo == 'dni') {
            $ruta = "https://facturalahoy.com/api/persona/" . $vnumdocu . '/' . $password;
        } elseif ($vtipo == 'ruc') {
            $ruta = "https://facturalahoy.com/api/empresa/" . $vnumdocu . '/' . $password;
        } else {
            $resp['repsuesta'] = 'error';
            echo json_encode();
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $ruta,
            CURLOPT_USERAGENT => 'Consulta Datos'
        ));

        $resp = curl_exec($curl);
        echo $resp;
        curl_close($curl);
        exit();
    }

    public function filtroAlumno() {
        $returnArr = array();
        $txtFiltro = $this->input->get("term");
        $dataFiltro = $this->objCobros->filtroAlumno($txtFiltro);
        foreach ($dataFiltro as $arrdata) {
            $alucod = $arrdata->ALUCOD;
            $rowArray['value'] = $alucod . " | " . $arrdata->NOMCOMP . " | " . $arrdata->NEMODES;
            $rowArray['alucod'] = $alucod;
            $rowArray['nomcomp'] = $arrdata->NOMCOMP;
            $rowArray['nemo'] = $arrdata->NEMO;
            $rowArray['nemodes'] = $arrdata->NEMODES;
            array_push($returnArr, $rowArray);
        }
        echo json_encode($returnArr);
    }

    public function lstPagosAdicional() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $data = $this->objCobros->getPagoAllAdicionales();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $arrData [] = array(
                        "id" => $fila->id,
                        "fecreg" => $fila->fecreg,
                        "nomcomp" => $fila->nomcomp,
                        "concepto" => $fila->condes,
                        "recibo" => $fila->numrecibo,
                        "monto" => 'S/' . $fila->montocob
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => count($data),
                    "recordsFiltered" => count($data),
                    "data" => $arrData
                );
            } else {
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => $arrData
                );
            }
            echo json_encode($output);
        } else {
            echo json_encode($output);
        }
    }

    public function lstPagos() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $vIdAlumno = $this->input->post("idAlumno");
            $data = $this->objCobros->getPagoxAlumno($vIdAlumno);
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $vimg = base_url() . "img/pagado.png";
                    if ($fila->estado == 'C')
                        $vimgDel = "<img src='" . base_url() . "img/delete.png' width='25px' heigth='25px' title='Eliminar Pago' onclick=\"javascript:js_delpago('" . $fila->concob . "','" . $fila->mescob . "','" . $vIdAlumno . "');\" />";
                    else
                        $vimgDel = "";
                    if ($fila->tipopago == 'C') {
                        $vtitle = "Pago en Caja";
                        $vimgPago = base_url() . "img/efectivo.png";
                    } else {
                        $vtitle = "Pago en Banco";
                        $vimgPago = base_url() . "img/banco.png";
                    }
                    $arrData [] = array(
                        "chk" => (($fila->estado == 'P') ? '<input type="checkbox" class="chk-box" name="chkPagos[]" value="' . $fila->concob . '|' . $fila->mescob . '" />' : '<img src="' . $vimg . '" width="20px" heigth="20px"  />'),
                        "estado" => (($fila->estado == 'P') ? '<b>Pendiente</b>' : '<b>Pagado</b>&nbsp;<img src="' . $vimgPago . '" title="' . $vtitle . '" width="25px" heigth="25px" />'),
                        "fecven" => invierte_date($fila->fecven),
                        "concepto" => $fila->condes . (($fila->concob == '01') ? (' - ' . $fila->mesdes) : ''),
                        "fecreg" => invierte_date_time($fila->fecmod),
                        "motno" => 'S/' . $fila->montopen,
                        "mora" => "S/0.00",
                        "total" => 'S/' . (($fila->estado == 'C') ? $fila->montocob : $fila->montopen),
                        "config" => $vimgDel
                    ); // $fila->concob == '02' || $fila->concob == '04' && 
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => count($data),
                    "recordsFiltered" => count($data),
                    "data" => $arrData
                );
            } else {
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => $arrData
                );
            }
            echo json_encode($output);
        } else {
            echo json_encode($output);
        }
    }

    public function deletePago() {
        $vconcob = $this->input->post("vconcob");
        $vmescob = $this->input->post("vmescob");
        $valucod = $this->input->post("valucod");
        $resp = $this->objCobros->deletePagoxAlumno($valucod, $vconcob, $vmescob);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'REGISTRO ELIMINADO CORRECTAMENTE!.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL ELIMINAR EL PAGO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function getAddConcepto() {
        $output = array();
        $dataConceptos = $this->objCobros->getConceptos();
        if (is_array($dataConceptos) && count($dataConceptos) > 0) {
            foreach ($dataConceptos as $concep) {
                $arrData [] = array(
                    'id' => $concep->concob,
                    'value' => strtoupper($concep->condes)
                );
            }
            $output = array('data' => $arrData);
        }
        echo json_encode($output);
    }

    public function grabarConceptoAdic() {
        $output = array();
        $vfecha = $this->input->post("vfecha");
        $vapepat = $this->input->post("vapepat");
        $vapemat = $this->input->post("vapemat");
        $vnom = $this->input->post("vnom");
        $vmonto = $this->input->post("vmonto");
        $vnumrecibo = $this->input->post("vnumrecibo");
        $vconcepto = $this->input->post("vconcepto");

        $dataPost = array(
            'anocob' => date('Y'),
            'apepat' => $vapepat,
            'apemat' => $vapemat,
            'nombres' => $vnom,
            'nomcomp' => ($vapepat . " " . $vapemat . ", " . $vnom),
            'concob' => $vconcepto,
            'mescob' => date('m'),
            'montoini' => $vmonto,
            'fecemi' => $vfecha,
            'fecven' => $vfecha,
            'montocob' => $vmonto,
            'moncod' => '001',
            'estado' => 'C',
            'montopen' => 0,
            'numrecibo' => $vnumrecibo,
            'fecreg' => date('Y-m-d'),
            'usureg' => 'SISTEMAS',
            'orden' => 0,
            'tipopago' => 'C'
        );
        $resp = $this->objCobros->grabaNuevoConceptoAdicional($dataPost);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE EL CONCEPTO DE COBRO AL ALUMNO.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL REGISTRAR EL CONCEPTO DE PAGO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function grabarConcepto() {
        $output = array();
        $vIdAlumno = $this->input->post("vIdAlumno");
        $vmonto = $this->input->post("vmonto");
        $vnumrecibo = $this->input->post("vnumrecibo");
        $vconcepto = $this->input->post("vconcepto");
        $valida = $this->objCobros->ValidaConceptoxAlumno($vIdAlumno, $vconcepto);
        if ($valida > 0) { // Ya existe el concepto
            $output = array('flg' => 1, 'msg' => 'YA EXISTE EL CONCEPTO DE COBRO ASIGNADO AL ALUMNO');
        } else {
            $dataPost = array(
                'anocob' => date('Y'),
                'alucod' => $vIdAlumno,
                'concob' => $vconcepto,
                'mescob' => date('m'),
                'montoini' => $vmonto,
                'fecemi' => date('Y-m-d'),
                'fecven' => date('Y-m-d'),
                'montocob' => $vmonto,
                'moncod' => '001',
                'estado' => 'C',
                'montopen' => 0,
                'numrecibo' => $vnumrecibo,
                'fecreg' => date('Y-m-d'),
                'usureg' => 'SISTEMAS',
                'orden' => 0,
                'tipopago' => 'C'
            );
            $res = $this->objCobros->grabaNuevoConcepto($dataPost);
            if ($res) {
                $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE EL CONCEPTO DE COBRO AL ALUMNO.');
            } else {
                $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL REGISTRAR EL CONCEPTO DE COBRO.');
            }
        }
        echo json_encode($output);
    }

    public function printeecc() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        // print_r($_POST); exit;
        $vNemo = $_POST["htxtsalon"]; //$_POST["cbsalon"];
        $vAlumno = $_POST["htxtalumno"]; //$_POST["cbalumno"];

        $dataSalon = $this->objSalon->getSalones($vNemo);
        $vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);
        $dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataPago = $this->objCobros->getPagoxAlumno($vAlumno);

        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        #Establecemos los márgenes izquierda, arriba y derecha:
        //$this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage('L');
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(260, 10, 'REPORTE DE EE.CC  - ' . date("Y"), 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Código : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, $dataSalon[0]->NEMO, 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(35, 5, 'Aula : ', 0, 0, 'R');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(100, 5, utf8_decode($dataSalon[0]->NEMODES), 0, 0, 'L');
        //$this->pdf->Ln ();
        //$this->pdf->SetFont ('Arial', '', 10);
        // $this->pdf->Cell (275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        //$this->pdf->Line (10, 28, 200, 28);
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('Alumno(a) : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(120, 5, utf8_decode($dataAlumno[0]->NOMCOMP), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->SetFont('Arial', 'B', 10);
        $valorY = 50;
        // $this->pdf->SetXY (11, $valorY);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(20, 5, utf8_decode('CÓDIGO'), 0, 0, 'C');
        $this->pdf->Cell(70, 5, utf8_decode('CONCEPTO DE CONCEPTO'), 0, 0, 'C');
        $this->pdf->Cell(20, 5, utf8_decode('PERIODO'), 0, 0, 'C');
        $this->pdf->Cell(15, 5, utf8_decode('MES'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('FECHA-VEN'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('FECHA-COBRO'), 0, 0, 'C');
        $this->pdf->Cell(30, 5, utf8_decode('RECIBO'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('PENDIENTE'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('COBRADO'), 0, 0, 'C');
        // $this->pdf->Cell (20, 10, utf8_decode ('T-PAGO'), 0, 0, 'C');
        $this->pdf->Cell(25, 5, utf8_decode('TOTAL'), 0, 0, 'C');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', '', 10);
        $vtotalp = 0;
        $vtotalc = 0;
        $vtotalt = 0;
        foreach ($dataPago as $pagos) {
            $this->pdf->Cell(20, 5, $pagos->concob, 0, 0, 'C');
            $this->pdf->Cell(70, 5, (($pagos->concob == '01') ? ($pagos->condes . ' - ' . $pagos->mesdes) : $pagos->condes), 0, 0, 'L');
            $this->pdf->Cell(20, 5, $pagos->mescob, 0, 0, 'C');
            $this->pdf->Cell(15, 5, $pagos->mesdes, 0, 0, 'C');
            $this->pdf->Cell(25, 5, invierte_date($pagos->fecven), 0, 0, 'C');
            $this->pdf->Cell(25, 5, invierte_date($pagos->fecmod), 0, 0, 'C');
            $this->pdf->Cell(30, 5, $pagos->numrecibo, 0, 0, 'C');
            $this->pdf->Cell(25, 5, $pagos->monsig . $pagos->montopen, 0, 0, 'R');
            $this->pdf->Cell(25, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
            $this->pdf->Cell(20, 5, $pagos->monsig . $pagos->montocob, 0, 0, 'R');
            $this->pdf->Ln(5);
            $vtotalp += $pagos->montopen;
            $vtotalc += $pagos->montocob;
            $vtotalt += $pagos->montocob;
        }

        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln(5);
        $this->pdf->Cell(205, 5, ' ', 0, 0, 'C');
        $this->pdf->Cell(25, 5, 'S/.' . number_format($vtotalp, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(25, 5, 'S/.' . number_format($vtotalc, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Cell(20, 5, 'S/.' . number_format($vtotalt, 2, '.', ','), 0, 0, 'R');
        $this->pdf->Ln(5);
        $this->pdf->Cell(275, 3, '--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');

        $this->pdf->Output('Reporte_de_EECC_por_Alumno.pdf', 'I');
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
