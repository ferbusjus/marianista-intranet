<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// Incluimos el archivo fpdf
require_once APPPATH . "/third_party/fpdf/fpdf.php";

//require_once APPPATH . "/third_party/fpdf/rotation.php";
//Extendemos la clase Pdf de la clase fpdf para que herede todas sus variables y funciones
class Pdf extends FPDF {

    public function __construct() {
        parent::__construct();
    }

    // El encabezado del PDF
    /*function Header() {
        //Put the watermark
        $this->SetFont('Arial', 'B', 50);
        $this->SetTextColor(255, 192, 203);
        $this->RotatedText(15, 270, 'COLEGIO MARIANISTA 2020', 90); //40 ANGULO
    }*/

    function RotatedText($x, $y, $txt, $angle) {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    /* public function Header(){
      $this->Image('imagenes/logo.png',10,8,22);
      $this->SetFont('Arial','B',13);
      $this->Cell(30);
      $this->Cell(120,10,'ESCUELA X',0,0,'C');
      $this->Ln('5');
      $this->SetFont('Arial','B',8);
      $this->Cell(30);
      $this->Cell(120,10,'INFORMACION DE CONTACTO',0,0,'C');
      $this->Ln(20);
      } */

    // El pie del pdf
    public function Footer() {
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}
