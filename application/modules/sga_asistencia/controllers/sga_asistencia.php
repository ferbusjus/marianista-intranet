<?php

/**
 * @package       modules/sga_asistencia/controller
 * @name            sga_asistencia.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/13
 */
class sga_asistencia extends CI_Controller
{

    public $token = '';
    public $modulo = 'ASISTENCIA';

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('asistencia_model', 'objAsistencia');
        $this->load->model ('salon_model', 'objSalon');
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('observacion_model', 'objObservacion');
        $this->load->model ('seguridad_model');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        $this->seguridad_model->registraNavegacion ($this->modulo);
        $data['token'] = $this->token ();
        $data["dataSalones"] = $this->objSalon->getSalones ();
        $data["lstObservacion"] = $this->objObservacion->lstObservaciones ();
        $this->load->view ('constant');
        $this->load->view ('view_header');

        $this->load->view ('view_asistencia', $data);
        $this->load->view ('js_asistencia');
        $this->load->view ('view_footer');
    }

    public function lstalumno ($nemo = '')
    {
        $dataAlumno = $this->objAlumno->getAlumnos ($nemo);
        echo json_encode ($dataAlumno);
    }

    public function verobs ()
    {
        $vCodigo = $this->input->post ("vIdalumno");
        $vFecha = $this->input->post ("vFecha");
        //$arrJson = array('arrData'=>'');          
        $flgEdit = 0;

        $dataAlumno = $this->objObservacion->get_obervacion_alumno ($vCodigo, $vFecha);
        if (!empty ($dataAlumno) && is_array ($dataAlumno)) {
            $flgEdit = 1;
            foreach ($dataAlumno as $item)
            //$arrJson['arrData'][] =$item->id_conducta;
                $arrJson['arrData'][] = array('rows' => $item, 'flag' => $flgEdit);
        } else {
            $arrJson['arrData'][] = array('rows' => '', 'flag' => $flgEdit);
        }
        echo json_encode ($arrJson);
    }

    public function saveupdate ()
    {
        $arrConducta = explode ("|", $this->input->post ('arrconducta'));
        for ($x = 0; $x < count ($arrConducta); $x++) {
            $data = array(
                'alucod' => $this->input->post ('alucod'),
                'id' => $this->input->post ('id'),
                'id_conducta' => $arrConducta[$x],
                'fecha' => $this->input->post ('fecha'),
                'tipo' => $this->input->post ('tipo'),
                'otros' => (($arrConducta[$x] == '09') ? $this->input->post ('otros') : ''),
                'fecreg' => date ("Y-m-d H:i:s"),
                'acc' => $this->input->post ('acc'),
                'usureg' => ''
            );
            $res = $this->objObservacion->saveUpdate ($data);
        }
        echo json_encode (array("status" => TRUE));
    }

    public function listar ()
    {
        //$sub_array = array();
        $dataAsis = array('data' => '', 'msg' => 0);
        if ($this->input->post ('token') && $this->input->post ('token') == $this->nativesession->get ('token')) {
            sleep (1);
            $dataForm = json_decode ($this->input->post ('dataForm'));
            $arrData = array(
                'ALUCOD' => $dataForm->valumno,
                'NEMO' => $dataForm->vsalon,
                'TIPO' => $dataForm->vtipo,
                'MES' => $dataForm->vmes
            );

            $arrAsis = $this->objAsistencia->getAsistenciasAll ($arrData);
            /*
              $dataProc = $this->objAsistencia->getAsistencias($arrData);
              $dataDia = $this->objAsistencia->getAsistenciaDiaria($arrData);
              $arrAsis = array_merge($dataProc, $dataDia);
             */
            $dataAsis = array('data' => $arrAsis, 'msg' => 1);
        } else {
            $dataAsis = array('data' => '', 'msg' => 2);
        }

       /* $sub_array[] = '';
        $sub_array[] = '';
        $sub_array[] = '';
        $sub_array[] = '';
        $sub_array[] = '';
        $sub_array[] = '';
        $sub_array[] = '';

        $data[] = $sub_array;
        $output = array(
            "draw" => 0,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => $data
        );
        echo json_encode ($output);
        
        */
        echo json_encode ($dataAsis);
        //redirect('login', 'refresh');    	      
    }

    public function generarReporteCalves ()
    {
        $this->load->library ('pdf');
        $this->pdf = new Pdf ();

        $this->pdf->SetAutoPageBreak (true, 5);
        $this->pdf->AddPage ();

        $this->pdf->Image (BASE_URL . '/images/insigniachico.png', 10, 2, 18, 18, 'PNG');
        $this->pdf->SetFont ('Arial', 'B', 14);
        $vx = $this->pdf->GetX ();
        $vy = $this->pdf->GetY ();
        $this->pdf->SetXY (20, 10);
        $this->pdf->Cell (190, 10, 'REPORTE DE CLAVES - ' . date ("Y"), 0, 0, 'C');
        $dataClaves = $this->objAsistencia->getDataClaves ();


        $filaX = 10;
        $y = 0;
        $filas = 0;
        $rows = 1;
        $y1 = 25;
        $y2 = 30;
        $y3 = 35;
        $limite = 53;
        foreach ($dataClaves as $row) {
            // for($x=1;$x<=500;$x++) {
            if ($rows == $limite) {
                $this->pdf->AddPage ();
                $rows = 1;
                $filaX = 10;
                $y = 0;
                $filas = 0;
                $y1 = 10;
                $y2 = 15;
                $y3 = 20;
                $limite = 53;
            } else {
                if ($filas == 4) {
                    $filaX = 10;
                    $y+=20;
                    $filas = 0;
                }
            }
            $this->pdf->SetFont ('Arial', '', 6);
            $this->pdf->SetXY ($filaX, ($y1 + $y));
            $this->pdf->Cell (45, 5, utf8_decode ($row->nomcomp), 'L,T,R', 0, 'C');
            $this->pdf->SetXY ($filaX, ($y2 + $y));
            $this->pdf->SetFont ('Arial', '', 8);
            $this->pdf->Cell (45, 5, 'USUARIO : ' . $row->usuario, 'L,R', 0, 'C');
            $this->pdf->SetXY ($filaX, ($y3 + $y));
            $this->pdf->Cell (45, 5, utf8_decode ('CONTRASEÑA :') . $row->clave, 'L,R,B', 0, 'C');
            $filaX +=47;
            $filas +=1;
            $rows +=1;
            //}
        }

        $this->pdf->Output ('Reporte_de_claves.pdf', 'I');
    }

    public function generaReporte ()
    {
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
        setlocale (LC_TIME, 'es_ES');
        $vCodigo = $this->input->post ("cbalumno");
        $vSalon = $this->input->post ("cbsalon");
        $vTipo = $this->input->post ("cbtipo");
        $vMes = $this->input->post ("cbmes");

        $dataAlumno = $this->objSalon->getDatoAlumnoxCodigo ($vCodigo);
        $this->load->library ('pdf');

        $this->pdf = new Pdf ('L', 'mm', 'A5');
        #Establecemos los márgenes izquierda, arriba y derecha:
        //$this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak (true, 5);
        $this->pdf->AddPage ();
        $this->pdf->Image (BASE_URL . '/images/insigniachico.png', 10, 2, 18, 18, 'PNG');
        $this->pdf->SetFont ('Arial', 'B', 14);
        $this->pdf->Cell (190, 10, 'REPORTE DE ASISTENCIA - ' . date ("Y"), 0, 0, 'C');
        $this->pdf->Ln ();
        $this->pdf->SetFont ('Arial', 'B', 10);
        $this->pdf->Cell (20, 5, utf8_decode ('Código : '), 0, 0, 'L');
        $this->pdf->SetFont ('Arial', '', 10);
        $this->pdf->Cell (25, 5, $dataAlumno[0]->DNI, 0, 0, 'L');
        $this->pdf->SetFont ('Arial', 'B', 10);
        $this->pdf->Cell (45, 5, 'Apellidos y Nombres : ', 0, 0, 'C');
        $this->pdf->SetFont ('Arial', '', 10);
        $this->pdf->Cell (100, 5, utf8_decode ($dataAlumno[0]->NOMCOMP), 0, 0, 'L');
        $this->pdf->Ln ();
        $this->pdf->SetFont ('Arial', 'B', 10);
        $this->pdf->Cell (22, 5, 'NGS      : ', 0, 0, 'L');
        $this->pdf->SetFont ('Arial', '', 10);
        $this->pdf->Cell (25, 5, $dataAlumno[0]->INSTRUCOD . $dataAlumno[0]->GRADOCOD . $dataAlumno[0]->SECCIONCOD, 0, 0, 'L');
        $this->pdf->SetFont ('Arial', 'B', 10);
        $this->pdf->Cell (43, 5, 'Aula                             : ', 0, 0, 'L');
        $this->pdf->SetFont ('Arial', '', 10);
        $this->pdf->Cell (100, 5, utf8_decode ($dataAlumno[0]->NEMODES), 0, 0, 'L');
        $this->pdf->Ln ();
        $this->pdf->Line (10, 32, 200, 32);
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->Ln ();
        // obtenemos las posiciones de XY
        $this->pdf->SetFont ('Arial', 'B', 9);
        $vx = $this->pdf->GetX ();
        $vy = $this->pdf->GetY ();
        $this->pdf->SetXY ($vx, $vy);
        $this->pdf->Cell (20, 5, 'Fecha ', 'T,B', 0, 'C');
        $this->pdf->Cell (20, 5, 'Hora ', 'T,B', 0, 'C');
        $this->pdf->Cell (20, 5, 'T- Asis. ', 'T,B', 0, 'C');
        $this->pdf->Cell (20, 5, 'T- Ing. ', 'T,B', 0, 'C');
        $this->pdf->Cell (110, 5, 'Observaciones', 'T,B', 0, 'C');
        $this->pdf->SetFont ('Arial', '', 10);

        $arrData = array(
            'ALUCOD' => $vCodigo,
            'NEMO' => $vSalon,
            'TIPO' => $vTipo,
            'MES' => $vMes
        );

        /*
          $dataProc = $this->objAsistencia->getAsistencias($arrData);
          $dataDia = $this->objAsistencia->getAsistenciaDiaria($arrData);
          $dataAsis = array_merge($dataProc, $dataDia);
         */

        $dataAsis = $this->objAsistencia->getAsistenciasAll ($arrData);
        foreach ($dataAsis as $row) {
            $this->pdf->SetFont ('Arial', '', 8);
            $this->pdf->SetXY ($vx, $vy + 5);
            $this->pdf->Cell (20, 5, strftime ('%a ,%d-%b ', strtotime ($row->fecha)), 'B', 0, 'C');
            $this->pdf->Cell (20, 5, $row->hora, 'B', 0, 'C');
            if ($row->tipo == "") {
                //$this->pdf->SetTextColor(252,5,5);  
                if ($row->tipo == "" && $row->evento == "I") {
                    $this->pdf->SetFont ('Arial', 'B', 9);
                    $marca = "FALTO";
                } elseif ($row->tipo == "" && $row->evento == "S") {
                    $marca = "PUNTUAL";
                } else {
                    $marca = $row->tipo;
                }
                $this->pdf->Cell (20, 5, $marca, 'B', 0, 'C');
            } else {
                if ($row->tipo == "V" && $row->evento == "I") {
                    $this->pdf->SetFont ('Arial', 'B', 9);
                    $marca = "VACACIONES";
                    $this->pdf->Cell (20, 5, $marca, 'B', 0, 'C');
                }  elseif ($row->tipo == "R" && $row->evento == "I") {
                    $this->pdf->SetFont ('Arial', 'B', 9);
                    $marca = "FERIADO";
                    $this->pdf->Cell (20, 5, $marca, 'B', 0, 'C');
                }  elseif ($row->tipo == "E" && $row->evento == "I") {
                    $this->pdf->SetFont ('Arial', 'B', 9);
                    $marca = "EVENTO";
                    $this->pdf->Cell (20, 5, $marca, 'B', 0, 'C');
                }  elseif ($row->tipo == "P" && $row->evento == "I") {
                    $this->pdf->SetFont ('Arial', 'B', 9);
                    $marca = "PUNTUAL";
                    $this->pdf->Cell (20, 5, $marca, 'B', 0, 'C');        
                }  elseif ($row->tipo == "T" && $row->evento == "I") {
                    $this->pdf->SetFont ('Arial', 'B', 9);
                    $marca = "TARDANZA";
                    $this->pdf->Cell (20, 5, $marca, 'B', 0, 'C');                       
                }  else {
                    $this->pdf->SetFont ('Arial', '', 9);
                    //$this->pdf->SetTextColor(0,0,0); 
                    $this->pdf->Cell (20, 5, $row->tipo, 'B', 0, 'C');
                }
            }
            $this->pdf->SetFont ('Arial', '', 8);
            $this->pdf->Cell (20, 5, (($row->evento == "I") ? "Ingreso" : "Salida"), 'B', 0, 'C');
            $this->pdf->SetFont ('Arial', '', 6);
            $this->pdf->MultiCell (110, 5, utf8_decode ($row->observacion), 'T,B', 'L');
            $vy+=5;
        }

        $this->pdf->Output ('Reporte_de_Asistencia.pdf', 'I');
    }

    public function token ()
    {
        $this->token = md5 (uniqid (rand (), true));
        $this->nativesession->set ('token', $this->token);
        return $this->token;
    }

}
