<?php

/**
 * @package       modules/sga_reporte_notas_periodo/controller
 * @name            sga_reporte_notas_periodo.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2023 SISTEMAS-DEV
 * @version         1.0 - 25/09/2023
 */
class sga_reporte_notas_periodo extends CI_Controller {

    public $token = '';
    public $modulo = 'REPORTE-NOTAS-PERIODO';
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
            $this->load->view('js_reporte_notas');
            $this->load->view('view_lista', $data);
            $this->load->view('view_footer');  
        } else {
            $this->load->view('constant');
            $this->load->view('view_header');
            $this->load->view('view_default');
            $this->load->view('view_footer');
        }            
    }
    
    public function reporte_bimestre(){
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        if (!$_POST) {
            echo "<center>Generacion de Reporte No Permitido</center";
            exit;
        }
// ============= Variables POST =================
        $vnemo = $this->input->post("cbaula");
        $vbimestre = $this->input->post("cbperiodo");
        //$vflgGen = $this->input->post("flgGenerar");
// ==============================================

        $this->load->library('pdf');
        $arraAlumnos = $this->objAlumno->getAlumnosxSalon($vnemo, 'T');
        $dataNemo = $this->objSalon->getDatosNemo($vnemo);
         $this->pdf = new Pdf ();

        $this->pdf->SetTopMargin(0.2);
        $this->pdf->SetTitle('CONSOLIDADO DE NOTAS -' . $this->ano);
        $this->pdf->SetAuthor('SISTEMAS-DEV.COM');
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage('L', 'A4');
# BLOQUE HEAD
        $this->pdf->Image("http://sistemas-dev.com/intranet/images/cabecera_boleta.jpg", 18, 5, 250, 13, 'JPG', '');
// =============================================================================
# CREAMOS TITULO DE LA BOLETA
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->SetXY(95, 22);
        $this->pdf->Cell(90, 3, utf8_decode("REPORTE CONSOLIDADO - POR PERIODO"), 0, 0, 'C');
# BLOQUE : DATOS DEL ALUMNO
        //$this->pdf->Rect(19, 30, 155, 6);
        $this->pdf->SetFont('Arial', 'B', 10);
        //$this->pdf->SetFillColor(208, 222, 240);
        $this->pdf->SetXY(20, 30);
        $this->pdf->Cell(20, 4, utf8_decode("AULA :"), 0, 0, 'L');
        $this->pdf->SetXY(40, 30);
        $this->pdf->Cell(70, 4, utf8_decode($dataNemo->nemodes), 0, 0, 'L');
        $this->pdf->SetXY(110, 30);
        $this->pdf->Cell(20, 4, utf8_decode("PERIODO :"), 0, 0, 'L');        
        $this->pdf->SetXY(130, 30);
        $this->pdf->Cell(20, 4, $vbimestre. " BIMESTRE", 0, 0, 'L');   
        
        // PINTANDO LAS CABECERAS
        $this->pdf->SetFont('Arial', 'B', 9);
        //$this->pdf->SetFillColor(208, 222, 240);
        $this->pdf->SetXY(10, 35);
        $this->pdf->Cell(5, 5, utf8_decode("N°"), 1, 0, 'C');
        $this->pdf->SetXY(15, 35);
        $this->pdf->Cell(70, 5, utf8_decode("APELLIDOS Y NOMBRES"), 1, 0, 'C');
        // PINTANDO LOS CURSOS OFICIALES
        
        $dataCursoOficial = $this->objSalon->getCursosAreas($dataNemo->instrucod, $dataNemo->gradocod);
         
        $iniX = 85;
        foreach($dataCursoOficial as $curso){
            $this->pdf->SetXY($iniX, 35);
            $this->pdf->Cell(15, 5, utf8_decode($curso->cursopre), 1, 0, 'C');           
            $iniX +=15 ;  
        }
        // IMPRIMIENDO CURSOS TRANSVERSALES
            //$this->pdf->SetXY($iniX+15, 35);
            $this->pdf->Cell(15, 5, utf8_decode('TIC'), 1, 0, 'C');   
            $this->pdf->SetXY($iniX+15, 35);
            $this->pdf->Cell(15, 5, utf8_decode('GA'), 1, 0, 'C'); 

        // IMPRIMIENDO ALUMNOS CON CURSOS
        $this->pdf->SetFont('Arial', '', 9);
        $fila=1;
        $iniY = 40;
        foreach($arraAlumnos as $alumno){
            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetXY(10, $iniY);
            $this->pdf->Cell(5, 5, $fila, 1, 0, 'C');
            $this->pdf->SetXY(15, $iniY);
            $this->pdf->Cell(70, 5, utf8_decode((strlen($alumno->NOMCOMP)>35) ? substr($alumno->NOMCOMP, 0,35) : $alumno->NOMCOMP ), 1, 0, 'L');            
            
            // IMPRIMIENDO PROMEDIOS POR CURSO
            $iniX = 85;
            $promTic = 0;
            foreach($dataCursoOficial as $curso){
               
                if($curso->cursonat=="D") {
                    $obj = $this->objSalon->getNumInternoXCursoPadre(
                            $alumno->INSTRUCOD, 
                            $alumno->GRADOCOD, 
                            $curso->cursocod);
                   
                    $promedio = $this->objSalon->getPromedioDependiente(
                            $alumno->ALUCOD, 
                            $vbimestre, 
                            $alumno->INSTRUCOD, 
                            $alumno->GRADOCOD, 
                            $curso->cursocod, 
                            $obj->total);
                     //print_r($promedio); exit;
                } else {

                    $promedio = $this->objSalon->getPromedioOficial(
                            $alumno->ALUCOD, 
                            $vbimestre, 
                            $curso->cursocod);
                    if($curso->cursocod=='21') $promTic = $promedio->prom;
                    // print_r($promedio); exit;
                }                        
                
                if($promedio->prom>10) 
                    $this->pdf->SetTextColor(0, 0, 204);
                else 
                    $this->pdf->SetTextColor(255, 0, 51);
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY($iniX, $iniY);
                $this->pdf->Cell(7.5, 5, $promedio->prom, 1, 0, 'C', TRUE);   
                if(funCualitativoStandar($promedio->prom, $alumno->INSTRUCOD)=="AD" || funCualitativoStandar($promedio->prom, $alumno->INSTRUCOD)=="A") 
                    $this->pdf->SetTextColor(0, 0, 204);
                else 
                    $this->pdf->SetTextColor(255, 0, 51);   
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY($iniX+7.5, $iniY);
                $this->pdf->Cell(7.5, 5, funCualitativoStandar($promedio->prom, $alumno->INSTRUCOD), 1, 0, 'C', TRUE); 
                $iniX +=15 ;  
            }            
            // IMPRIMIENDO CURSOS ADICIONALES
                $objGA = $this->objSalon->getNumInternoXNivelGrado(
                        $alumno->INSTRUCOD, 
                        $alumno->GRADOCOD);      
                $promGA = $this->objSalon->getPromedioGA(
                        $alumno->ALUCOD, 
                        $vbimestre, 
                        $alumno->INSTRUCOD, 
                        $alumno->GRADOCOD, 
                        $objGA->total);                
                $promedioGA = $promGA->prom;
                if($promTic>10) 
                    $this->pdf->SetTextColor(0, 0, 204);
                else 
                    $this->pdf->SetTextColor(255, 0, 51);            
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY($iniX, $iniY);
                $this->pdf->Cell(7.5, 5, $promTic, 1, 0, 'C', TRUE); 
                $iniX +=7.5 ;
                if(funCualitativoStandar($promTic, $alumno->INSTRUCOD)=="AD" || funCualitativoStandar($promTic, $alumno->INSTRUCOD)=="A") 
                    $this->pdf->SetTextColor(0, 0, 204);
                else 
                    $this->pdf->SetTextColor(255, 0, 51);                 
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY($iniX, $iniY);
                $this->pdf->Cell(7.5, 5, funCualitativoStandar($promTic, $alumno->INSTRUCOD), 1, 0, 'C', TRUE); 
                $iniX +=7.5 ; 
                if($promedioGA>10) 
                    $this->pdf->SetTextColor(0, 0, 204);
                else 
                    $this->pdf->SetTextColor(255, 0, 51);                  
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->SetXY($iniX, $iniY);
                $this->pdf->Cell(7.5, 5, $promedioGA , 1, 0, 'C', TRUE); 
                $iniX +=7.5 ;
                if(funCualitativoStandar($promedioGA, $alumno->INSTRUCOD)=="AD" || funCualitativoStandar($promedioGA, $alumno->INSTRUCOD)=="A") 
                    $this->pdf->SetTextColor(0, 0, 204);
                else 
                    $this->pdf->SetTextColor(255, 0, 51);                  
                $this->pdf->SetFillColor(208, 222, 240);
                $this->pdf->SetXY($iniX, $iniY);
                $this->pdf->Cell(7.5, 5, funCualitativoStandar($promedioGA, $alumno->INSTRUCOD), 1, 0, 'C', TRUE); 

            $fila++;
            $iniY +=5;
        }        
        $this->pdf->Output('Reporte_consolidado_por_periodo.pdf', 'I');

    }    
    
    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }
    
}