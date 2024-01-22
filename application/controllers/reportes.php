<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class reportes extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('seguridad_model');
		$this->load->model('reportes_model');
	}
	public function index(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/
          $this->load->view('constant');
          $this->load->view('view_header'); 
          $this->load->view('reportes/view_reportes');
          $this->load->view('view_footer');
          
	}
     public function GeneraReporte(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $Reporte   = json_decode($this->input->post('MiReporte'));
          $FInicial  = explode("/",$Reporte->Finicial);
          $FFinal    = explode("/",$Reporte->FFinal);
          $Documento = $Reporte->Documento; 
          $FInicial  = $FInicial[2]."-".$FInicial[0]."-".(int)$FInicial[1]." 00:00:00"; 
          $FFinal    = $FFinal[2]."-".$FFinal[0]."-".(int)$FFinal[1]." 23:59:59"; 

          echo json_encode($this->reportes_model->reportesGenera($FInicial, $FFinal, $Documento));
     }
}