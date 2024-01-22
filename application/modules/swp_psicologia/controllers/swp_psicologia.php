<?php

/**
 * @package       modules/swp_psicologia/controller
 * @name            swp_psicologia.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 09.06.2018
 */
class swp_psicologia extends CI_Controller {

    public $token = '';
    public $modulo = 'PSICOLOGIA';
    public $datasession = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('psicologia_model', 'objPsicologia');
        $this->load->model('seguridad_model');
        $this->datasession = $this->nativesession->get('arrDataSesion');
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
        $data['lstmotivo'] = $this->objPsicologia->getMotivos();
        //$data['comprobantes'] = $this->objPsicologia->getComprobantes ();
        $data['token'] = $this->token();

        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('psicologia-js');
        $this->load->view('lista-psicologia-view', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function grafico() {
        $this->load->library('graficos');
        $this->graficos = new Graficos();
       $data['grafico'] = $this->graficos->graficar_barras();
       $this->load->view('grafico-psicologia-view', $data);
    }

    public function eliminaEgreso() {
        $vId = $this->input->post("vId");
        $resp = $this->objPsicologia->eliminaEgreso($vId);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE ELIMINO CORRECTAMENTE EL EGRESO.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL ELIMINAR EL EGRESO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function listaMotivosGroup($idcita) {
        $datamotivos = '';
        $data = $this->objPsicologia->getMotivoxCita($idcita);
        foreach ($data as $lista) {
            $datamotivos .= "- " . $lista->descripcion . "<br>";
        }
        if ($datamotivos == '') {
            $datamotivos = 'NINGUNO';
        }
        return $datamotivos;
    }

    public function lista() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            // $data = $this->objPsicologia->listaEgresos ();
            $data = $this->objPsicologia->get_datatables();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $img = base_url() . '/images/bt_mini_delete.png';
                    $img2 = base_url() . '/images/bt_mini_edit.png';
                    $img3 = base_url() . '/images/imprimir.png';
                    $icono = base_url() . '/img/pagado.png';
                    $icono1 = base_url() . '/img/rojo.png';
                    $icono2 = base_url() . '/img/verde.png';
                    $icono3 = base_url() . '/img/amarillo.png';
                    $conf = "<img src='$img'  title='Eliminar' onclick=\"javascript:js_eliminar('$fila->idcita');\" style='cursor:pointer' />";
                    $conf .= "&nbsp;<img src='$img2'  title='Editar' onclick=\"javascript:js_editar('$fila->idcita');\" style='cursor:pointer' />";
                    if ($fila->titulo == 'ATENDIDO') {
                        $icono = '<img src="' . $icono . '" title="ATENDIDO" heigth="25px" width="25px">';
                        $conf = "<img src='$img3'  title='Imprimir'  style='cursor:pointer'  onclick=\"javascript:js_imprimir('$fila->idcita');\" heigth='25px' width='25px' />";
                    } else {
                        if ($fila->color == '#FF0000') { // ROJO
                            $icono = '<img src="' . $icono1 . '" title="URGENTE" heigth="25px" width="25px">';
                        } elseif ($fila->color == '#008000') { // VERDER
                            $icono = '<img src="' . $icono2 . '" title="MEDIA" heigth="25px" width="25px">';
                        } elseif ($fila->color == '#FFD700') { // AMARILLO
                            $icono = '<img src="' . $icono3 . '" title="BAJA" heigth="25px" width="25px">';
                        }
                    }
                    $fecha1 = strtotime(date("Y-m-d"));
                    $fecha2 = strtotime(substr($fila->feciniatencion, 0, 10));
                    if ($fecha1 > $fecha2)
                        $flgfecha = TRUE;
                    else
                        $flgfecha = FALSE;

                    $arrData [] = array(
                        "dni" => $fila->alucod,
                        "fecreg" => substr($fila->feciniatencion, 0, 10) . "<br>" . $fila->hora,
                        "motivo" => $this->listaMotivosGroup($fila->idcita),
                        "alumno" => $fila->nomcomp,
                        "alerta" => $icono,
                        "estado" => "<b>" . $fila->titulo . "</b><br>" . (($fila->flg_asiste == '0' && $flgfecha == TRUE) ? '(NO ASISTIO)' : ''),
                        "ngs" => $fila->instrucod . $fila->gradocod . $fila->seccioncod,
                        "conf" => $conf
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objPsicologia->count_all(),
                    "recordsFiltered" => $this->objPsicologia->count_filtered(),
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

    public function getdatos() {
        $output = array();
        $arrDataMotivo = array();
        $dataMotivos = $this->objPsicologia->getMotivos();
        if (is_array($dataMotivos) && count($dataMotivos) > 0) {
            foreach ($dataMotivos as $row) {
                $arrDataMotivo [] = array(
                    'id' => $row->idmotivo,
                    'value' => strtoupper($row->descripcion)
                );
            }
        }
        $output = array(
            'lstMotivo' => $arrDataMotivo,
        );

        echo json_encode($output);
    }

    public function getdatosCita() {
        $output = array();
        $arrdataMotivos = array();
        $vidcita = $this->input->post("idcita");
        $dataCita = $this->objPsicologia->getDataCita($vidcita);
        $dataMotivos = $this->objPsicologia->getMotivoxCita($vidcita);
        if (is_array($dataMotivos) && count($dataMotivos) > 0) {
            foreach ($dataMotivos as $row) {
                $arrdataMotivos [] = array(
                    'id' => $row->idmotivo,
                    'value' => strtoupper($row->descripcion)
                );
            }
        }
        // p.alucod,a.nomcomp,p.feciniatencion,p.fecfinatencion,hora,str_acudieron,str_inteligencia,str_emocional,str_recomendacion
        if (is_array($dataCita) && count($dataCita) > 0) {
            $output = array(
                'status' => 500,
                'dataCita' => $dataCita,
                'datamotivo' => $arrdataMotivos
            );
        } else {
            $output = array(
                'status' => 404,
                'dataCita' => NULL,
                'datamotivo' => NULL
            );
        }
        echo json_encode($output);
    }

    public function printCita() {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        $vidCita = $this->input->post("idreporte");
        // echo "REPORTE DE LA CITA : " . $vidCita;
        // exit;
        $dataCita = $this->objPsicologia->getDataCita($vidCita);
        //print_r($dataCita); exit;
        $this->load->library('pdf');
        $this->pdf = new Pdf ();
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 10, 10, 20, 20, 'PNG');
        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->Cell(180, 3, 'Fecha :' . date("Y-m-d"), 0, 0, 'R');
        $this->pdf->Ln();
        $this->pdf->Cell(180, 3, 'Hora :   ' . date("H:i:s"), 0, 0, 'R');
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->SetXY(20, 15);
        $this->pdf->Cell(180, 10, utf8_decode('FICHA ATENCIÓN DEL PSICÓLOGO '), 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '----------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('ALUMNO : '), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(60, 5, utf8_decode($dataCita[0]->nomcomp), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, 'FECHA    : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(20, 5, substr($dataCita[0]->feciniatencion, 0, 10), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(15, 5, 'AULA : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(60, 5, utf8_decode($dataCita[0]->nemodes), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(25, 5, 'ASISTIERON : ', 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(50, 5, utf8_decode($dataCita[0]->str_acudieron), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(275, 3, '----------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('EVALUACIÓN : '), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('-  INTELIGENCIA : '), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->MultiCell(180, 5, utf8_decode($dataCita[0]->str_inteligencia), 0, 'L', 0);

        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('-  EMOCIONAL : '), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->MultiCell(180, 5, utf8_decode($dataCita[0]->str_emocional), 0, 'L', 0);

        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(20, 5, utf8_decode('RECOMENDACIONES : '), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->MultiCell(180, 5, utf8_decode($dataCita[0]->str_recomendacion), 0, 'L', 0);

        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Cell(180, 5, utf8_decode('                                                                                                                                                _______________________________'), 0, 0, 'R');
        $this->pdf->Ln();
        $this->pdf->Cell(180, 5, utf8_decode('                                                                                                                                                FAUSTINO CALDERON RETAMOSO'), 0, 0, 'R');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->Cell(180, 5, utf8_decode('(PSICÓLOGO)                  '), 0, 0, 'R');

        $this->pdf->Output('Ficha_Psicologica.pdf', 'I');
    }

    public function grabarUpdate() {

        $output = array();
        $vtipomotivo = $this->input->post("vmotivo");
        $valumno = $this->input->post("valumno");
        $vfecha = $this->input->post("vfecha");
        $vintel = $this->input->post("vintel");
        $vemo = $this->input->post("vemo");
        $vreco = $this->input->post("vreco");
        $vtxtacude = $this->input->post("vtxtacude");
        $vdni = $this->input->post("vdni");
        $vnemo = $this->input->post("vnemo");
        $vusu = $this->datasession['USUCOD'];
        $arrMotivos = explode(",", $vtipomotivo);
        $txthora = $this->input->post("txthora");
        $txtaccion = $this->input->post("txtaccion");
        $idcita = $this->input->post("txtidcita");
        $flgasiste = $this->input->post("flgasiste");
        $vcolor = $this->input->post("vcolor");
        $vcolor = ($vcolor == '') ? '#008000' : $vcolor; // por defecto Verde
        $vestado = (($flgasiste == '1') ? 'ATENDIDO' : 'PENDIENTE');
        if ($txtaccion == '0') { // insert
            $dataPost = array(
                'alucod' => $vdni,
                'nemo' => $vnemo,
                'feciniatencion' => $vfecha,
                'fecfinatencion' => $vfecha,
                'hora' => $txthora,
                'motivos' => '',
                'str_acudieron' => $vtxtacude,
                'color' => $vcolor,
                'titulo' => $vestado,
                'str_inteligencia' => $vintel,
                'str_emocional' => $vemo,
                'str_recomendacion' => $vreco,
                'usureg' => $vusu,
                'flg_asiste' => $flgasiste
            );
            $resp = $this->objPsicologia->grabar($dataPost);
            $msg = 'SE REGISTRO CORRECTAMENTE.';
        } else {

            $dataPost = array(
                'feciniatencion' => $vfecha,
                'fecfinatencion' => $vfecha,
                'hora' => $txthora,
                'color' => $vcolor,
                'str_acudieron' => $vtxtacude,
                'titulo' => $vestado,
                'str_inteligencia' => $vintel,
                'str_emocional' => $vemo,
                'str_recomendacion' => $vreco,
                'fecmod' => date("Y-m-d H:i:s"),
                'usumod' => $vusu,
                'flg_asiste' => $flgasiste
            );
            $resp = $this->objPsicologia->update($dataPost, $idcita);
            $msg = 'SE MODIFICO CORRECTAMENTE.';
        }
        if ($resp) {
            if ($txtaccion == '0') {
                $idcita = $this->db->insert_id();
            } else {
                $this->objPsicologia->desactivaMotivosCita($idcita);
            }
            foreach ($arrMotivos as $idmotivo) {
                $arrDato = array(
                    'idcita' => $idcita,
                    'idmotivo' => $idmotivo,
                    'usureg' => $vusu
                );
                if ($txtaccion == '0') {
                    $this->objPsicologia->grabarMotivos($arrDato);
                } else {
                    $existe = $this->objPsicologia->verificaRegistro($idcita, $idmotivo);
                    if ($existe)
                        $this->objPsicologia->modificaMotivos($idcita, $idmotivo);
                    else
                        $this->objPsicologia->grabarMotivos($arrDato);
                }
            }

            $output = array('flg' => 0, 'msg' => $msg);
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR INTERNO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode($output);
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
