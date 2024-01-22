<?php

if (!defined ('BASEPATH'))
    exit ('No direct script access allowed');

class sga_reporte_asistencia extends CI_Controller
{

    function __construct ()
    {
        parent::__construct ();
        $this->load->model ('seguridad_model');
        $this->load->model ('salon_model', 'objSalon');
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('asistencia_model', 'objAsistencia');
        $this->load->helper ('funciones_helper');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        $data['titulo'] = "Reporte de Asistencia";
        $data["dataSalones"] = $this->objSalon->getSalones ();
        $this->load->view ('constant');
        $this->load->view ('view_header');
        $this->load->view ('reporte-script');
        $this->load->view ('reporte-view', $data);
        $this->load->view ('view_footer');
    }

    public function reporte ()
    {
        /* echo "<pre>";
          print_r($_POST);
          echo "</pre>"; */

        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }

        $vNemo = $this->input->post ("cbsalon");
        $vFdesde = str_replace ("/", "-", $this->input->post ("finicial"));
        $vFhasta = str_replace ("/", "-", $this->input->post ("ffinal"));

        $dataSalon = $this->objSalon->getSalones ($vNemo);
        $dataAlumnos = $this->objAlumno->getAlumnos ($vNemo);
        $this->load->library ('pdf');

        $this->pdf = new Pdf ();
        #Establecemos los márgenes izquierda, arriba y derecha:
        //$this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak (true, 5);
        $this->pdf->AddPage ('L');
        $this->pdf->Image (BASE_URL . '/images/insigniachico.png', 10, 5, 20, 20, 'PNG');
        $this->pdf->SetFont ('Arial', 'B', 14);
        $this->pdf->Cell (260, 10, 'REPORTE DE ASISTENCIA - ' . date ("Y"), 0, 0, 'C');
        $this->pdf->Ln ();
        $this->pdf->Ln ();
        $this->pdf->SetFont ('Arial', '', 8);
        $this->pdf->Cell (275, 3, '----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        $this->pdf->Ln ();
        $this->pdf->SetFont ('Arial', 'B', 8);
        $this->pdf->Cell (20, 5, utf8_decode ('Código : '), 0, 0, 'L');
        $this->pdf->SetFont ('Arial', '', 8);
        $this->pdf->Cell (20, 5, $dataSalon[0]->NEMO, 0, 0, 'L');
        $this->pdf->SetFont ('Arial', 'B', 8);
        $this->pdf->Cell (35, 5, 'Aula : ', 0, 0, 'R');
        $this->pdf->SetFont ('Arial', '', 8);
        $this->pdf->Cell (100, 5, utf8_decode ($dataSalon[0]->NEMODES), 0, 0, 'L');
        $this->pdf->Ln ();
        $this->pdf->SetFont ('Arial', 'B', 8);
        $this->pdf->Cell (20, 5, 'Desde : ', 0, 0, 'L');
        $this->pdf->SetFont ('Arial', '', 8);
        $this->pdf->Cell (20, 5, $vFdesde,0, 0, 'L');
        $this->pdf->SetFont ('Arial', 'B', 8);
        $this->pdf->Cell (35, 5, 'Hasta : ', 0, 0, 'R');
        $this->pdf->SetFont ('Arial', '', 8);
        $this->pdf->Cell (20, 5, $vFhasta, 0, 0, 'C');
        $this->pdf->Ln ();
        $this->pdf->SetFont ('Arial', '', 8);
        $this->pdf->Cell (275, 3, '----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
        //$this->pdf->Line (10, 28, 200, 28);
        $this->pdf->Ln ();
        // ================ Imprimiendo las asistencias ==================
        $this->pdf->SetFont ('Arial', 'B', 7);
        $valorY = 50;
        $this->pdf->SetXY (11, $valorY);
        $this->pdf->Cell (5, 10, utf8_decode ('Nº'), 1, 0, 'C');
        $this->pdf->SetXY (16, $valorY);
        $this->pdf->Cell (70, 10, utf8_decode ('Apellidos y Nombre'), 1, 0, 'C');
        

                
        $valorX = 86;
        $arrayFechas = devuelveArrayFechasEntreOtrasDos ($vFdesde, $vFhasta);
        $vtotal = count ($arrayFechas);
        if ($vtotal > 31) {
            echo "Rango no Permitido.";
            exit;
        }
        // $vtotal
       // echo "Total =>".$vtotal; exit; // 24
        // maximo permitido 17 por fila 
        for ($x = 0; $x < $vtotal; $x++) {
                $vdia = devuelvedia ($arrayFechas[$x]);
                if ( $vdia == 'Sab' ||  $vdia == 'Dom') {
                    // No debe de mostrar los dias SAB / DOM
                } else {
                    if ($vdia == 'Sab') {
                        $this->pdf->SetFillColor (208, 222, 240);
                    } else {
                        $this->pdf->SetFillColor (255, 255, 255);
                    }
                    $this->pdf->SetXY ($valorX, $valorY);
                    $this->pdf->Cell (8, 5, $vdia, 1, 0, 'C', TRUE);

                    $this->pdf->SetXY ($valorX, ($valorY + 5));
                    $this->pdf->Cell (8, 5, substr (str_replace ("-", "/", $arrayFechas[$x]), 0, 5), 1, 0, 'C', TRUE);
                    $valorX = $valorX + 8;
                }
        }
        
            
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (24, 5, 'TOTALES', 1, 0, 'C', TRUE);
            $valorY +=5;
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (8, 5, 'TP', 1, 0, 'C', TRUE);
            $valorX = $valorX + 8;
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (8, 5, 'TT', 1, 0, 'C', TRUE);
            $valorX = $valorX + 8;
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (8, 5, 'TF', 1, 0, 'C', TRUE);                        

        // =============== Imprimiendo los datos de los alumnos ===============
        $valorY += 5;
        foreach($dataAlumnos as $alu){
             $this->pdf->SetFont ('Arial', '', 7);
            $this->pdf->SetXY (11, $valorY);
            $this->pdf->Cell (5, 4, utf8_decode ($alu->NUMORD), 1, 0, 'C');
            $this->pdf->SetXY (16, $valorY);
            $this->pdf->Cell (70, 4, utf8_decode ($alu->NOMCOMP), 1, 0, 'L');       
            $valorY += 4;
        }
        // =============== Imprimiendo las asistencias de los alumnos ===============
       
        $valorY = 60;
        foreach($dataAlumnos as $alu){
             $valorX = 86;
             $tf = 0; $tt=0; $tp=0;
              $this->pdf->SetFont ('Arial', '', 7);
            for ($x = 0; $x < $vtotal; $x++) {
                    $vdia = devuelvedia ($arrayFechas[$x]);
                    if ( $vdia == 'Sab' ||  $vdia == 'Dom') {
                        // No debe de mostrar los dias SAB / DOM
                    } else {
                        if ($vdia == 'Sab') {
                            $this->pdf->SetFillColor (208, 222, 240);
                        } else {
                            $this->pdf->SetFillColor (255, 255, 255);
                        }
                        // ------- Formateando la fecha en formato Mysql -------------
                        $vfecha=$arrayFechas[$x]; 
                        $vfecha=date("Y-m-d",strtotime($vfecha));
                        // --------------------------------------------------------------------------
                        $vmarca = '-';
                        if($vfecha==date('Y-m-d')) {
                            $dataAsis = $this->objAsistencia->getAsistenciaHoy($vfecha, $alu->DNI);
                        } else {
                            $dataAsis = $this->objAsistencia->getAsistenciaxDia($vfecha, $alu->DNI);
                        }                                                                            
                        if(count($dataAsis)>0) $vmarca =$dataAsis[0]->t_asist;
                       
                        if($vmarca=='') {
                            $tf ++;
                            //$this->pdf->SetFillColor (208, 222, 240);
                        }
                        if($vmarca=='T') {
                            $tt ++;
                            //$this->pdf->SetFillColor (208, 222, 240);
                        }
                        if($vmarca=='P') {
                            $tp ++;
                            //$this->pdf->SetFillColor (208, 222, 240);
                        }
                         if($vmarca=='')  $vmarca='F';
                         if($vmarca=='R')  $vmarca='FR';
                         if($vmarca=='V')  $vmarca='VC';
                        if($vmarca=='FR') {
                            $this->pdf->SetFillColor (249, 255, 154);
                        } elseif($vmarca=='VC') {
                            $this->pdf->SetFillColor (242, 169, 79);
                        }  elseif($vmarca=='F') {
                            $this->pdf->SetFillColor (237, 59, 83);
                        } else {
                            $this->pdf->SetFillColor (255, 255, 255);
                        }
                        
                        $this->pdf->SetXY ($valorX, $valorY);
                        $this->pdf->Cell (8, 4, $vmarca, 1, 0, 'C', TRUE);
                        $valorX = $valorX + 8;
                    }
            }
            
            $this->pdf->SetFont ('Arial', 'B', 7);
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (8, 4, $tp, 1, 0, 'C', TRUE);
            $valorX = $valorX + 8;
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (8, 4, $tt, 1, 0, 'C', TRUE);
            $valorX = $valorX + 8;
            $this->pdf->SetFillColor (208, 222, 240);
            $this->pdf->SetXY ($valorX, $valorY);
            $this->pdf->Cell (8, 4, $tf, 1, 0, 'C', TRUE);                             
            
            $valorY += 4;
        }
        
       
        
        $this->pdf->Output ('Reporte_de_Asistencia_por_Aula.pdf', 'I');
    }

    public function devuelveArrayFechasEntreOtrasDos ($fechaInicio, $fechaFin)
    {
        $arrayFechas = array();
        $fechaMostrar = $fechaInicio;

        while (strtotime ($fechaMostrar) <= strtotime ($fechaFin)) {
            $arrayFechas[] = $fechaMostrar;
            $fechaMostrar = date ("d-m-Y", strtotime ($fechaMostrar . " + 1 day"));
        }

        return $arrayFechas;
    }

    public function devuelvedia ($fecha)
    {
        $fechats = strtotime ($fecha); //a timestamp
        //el parametro w en la funcion date indica que queremos el dia de la semana
        //lo devuelve en numero 0 domingo, 1 lunes,....
        $vdia = '';
        switch (date ('w', $fechats)) {
            case 0: $vdia = "Dom";
                break;
            case 1: $vdia = "Lun";
                break;
            case 2: $vdia = "Mar";
                break;
            case 3: $vdia = "Mie";
                break;
            case 4: $vdia = "Jue";
                break;
            case 5: $vdia = "Vie";
                break;
            case 6: $vdia = "Sab";
                break;
        }
        return $vdia;
    }

}
