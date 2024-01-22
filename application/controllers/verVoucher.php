<?php


class verVoucher extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('cobros_model', 'objCobros');
        $this->load->model('familia_model', 'objFamilia');
        $this->ano = $vano = 2020;
    }
      public function index() {
          
        $vtipo = $this->input->get("tipo"); // '01'; // (tipo de documento)
        $vAlumno =$this->input->get("id");   //"20180023"; // (Codigo del Alumno)
        $vcomp = $this->input->get("idcomp"); // "R001-00003385"; // (Numero de comprobante)
        $vano = $this->input->get("anio");  // 2020
        // ================= Obteniendo informacion  ===========================
        $dataAlumno = $this->objSalon->getDataAlumnoApp($vAlumno,$vano);
        $instru = $dataAlumno[0]->INSTRUCOD;
        $nemodes = $dataAlumno[0]->NEMODES;
        if ($instru == 'I' || $instru == 'P') {
            $vruc = RUC_PRIMARIA;
            $vTipoRazon = "R01";
        } else {
            $vruc = RUC_SECUNDARIA;
            $vTipoRazon = "R02";
        }
        if ($vtipo == '01') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
            /* $vTipoDesc = "RECIBO";
              $vTipoElectro = "ELECTRÓNICO"; */
        } elseif ($vtipo == '02') {
            $vTipoDesc = "BOLETA";
            $vTipoElectro = "ELECTRÓNICA";
        } elseif ($vtipo == '03') {
            $vTipoDesc = "FACTURA";
            $vTipoElectro = "ELECTRÓNICA";
        }
        $dataEmpresa = $this->objSalon->getDatosEmpresa($vruc);
        //$vDniAlumno = $this->objAlumno->getDniAlumno($vAlumno);

        //$dataAlumno = $this->objSalon->getDatoAlumno($vDniAlumno);
        $dataFamilia = $this->objAlumno->getFamiliaAlumno($dataAlumno[0]->DNI);
        /*$dataCampus = $this->objAlumno->geUsuarioCampus($vDniAlumno);
        if (count($dataCampus) > 0) {
            $vusucampus = $dataCampus->usuario;
            $vclavecampus = "********";
        } else {
            $vusucampus = strtolower($dataAlumno[0]->APEPAT . "." . $dataAlumno[0]->APEMAT . "." . substr($dataAlumno[0]->NOMBRES, 0, 1));
            $vclavecampus = $dataCampus->dni;
        }        */
        $dataApoderado = "";
        if ($dataFamilia->flgpadapo == '1') {
            $dataApoderado = $dataFamilia->paddni . ' - ' . $dataFamilia->padre;
        } else {
            $dataApoderado = $dataFamilia->maddni . ' - ' . $dataFamilia->madre;
        }
        $dataPago = $this->objCobros->getPagoxAlumnoRecApp($vcomp, $vAlumno,$vano );

        $numregs = count($dataPago);
        // ============ Obteniendo la altura del Ticket =====================
        $limite = 0;
        if ($numregs > 0) {
            $limite = ($numregs * 3);
        }
        if ($vtipo == '03') {
            $limite += 25;
        }
        $limite = 125 + $limite;
        $vusuPago = $this->objCobros->getDatoUsuarioPagoApp($vcomp, $vTipoRazon, $vano);
        $datoUsuario = $this->objCobros->getDatoUsuario($vusuPago->usumod);

        // ==================================================================
        $this->load->library('PdfAutoPrint');
        $this->pdf = new PDFAutoPrint($orientation = 'P', $unit = 'mm', array(45, $limite));
        $this->pdf->SetAuthor('SISTEMAS-DEV - ' . $this->ano);
        $this->pdf->SetTitle($vTipoDesc . ' ELECTRONICA - ' . $this->ano);
        #Establecemos los mรกrgenes izquierda, arriba y derecha:
        $this->pdf->SetMargins(5, 5, 5);
        #Establecemos el margen inferior:
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->AddPage();
        $this->pdf->Image(BASE_URL . '/images/insigniachico.png', 13, 2, 18, 16, 'PNG');
        // ==================== BLOQUE DEL COMPROBANTE ==============
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->SetXY(8, 20);
        $this->pdf->Cell(30, 3, $vTipoDesc . ' ' . utf8_decode($vTipoElectro), 0, 0, 'C');
        $this->pdf->SetXY(8, 23);
        $this->pdf->Cell(30, 3, 'R.U.C : ' . $dataEmpresa[0]->ruc, 0, 0, 'C');
        $this->pdf->SetXY(8, 26);
        $this->pdf->Cell(30, 3, $vcomp, 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(3, 29);
        $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');
        // ================BLOQUE RAZON SOCIAL =====================
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(2, 33);
        $this->pdf->Cell(42, 3, utf8_decode('"' . $dataEmpresa[0]->nombre_comercial . '"'), 0, 0, 'C');
        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->SetXY(2, 35);
        $this->pdf->Cell(42, 3, utf8_decode('"' . $dataEmpresa[0]->razon_social . '"'), 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(2, 40);
        $this->pdf->Cell(42, 3, utf8_decode($dataEmpresa[0]->direccion), 0, 0, 'C');
        $this->pdf->SetXY(2, 42);
        $this->pdf->Cell(42, 3, utf8_decode('Teléfono : ' . $dataEmpresa[0]->telefono), 0, 0, 'C');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(3, 45);
        $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');
        // ================BLOQUE DATOS DEL CLIENTE =====================
        $data = explode("-", $dataApoderado);
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(3, 48);
        if ($vtipo == '03') {
            $this->pdf->Cell(13, 2, utf8_decode("Razón Social: "), 0, 0, 'L');
        } else {
            $this->pdf->Cell(10, 2, utf8_decode("Señor(es): "), 0, 0, 'L');
        }
        $this->pdf->SetFont('Arial', '', 5);

        if ($vtipo == '03') { // esta en duro el ruc
            $this->pdf->SetXY(16, 48);
            $this->pdf->Cell(15, 2, '10' . trim($data[0]) . '2', 0, 0, 'L');
            $this->pdf->SetXY(3, 50);
            $this->pdf->Cell(40, 2, utf8_decode(trim($data[1])), 0, 0, 'L');
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(3, 52);
            $this->pdf->Cell(13, 2, utf8_decode("Alumno(a): "), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(16, 52);
            $this->pdf->Cell(15, 2, utf8_decode($dataAlumno[0]->DNI), 0, 0, 'L');
        } else {
            $this->pdf->SetXY(13, 48);
            $this->pdf->Cell(10, 2, $data[0], 0, 0, 'L');
            $this->pdf->SetXY(3, 50);
            $this->pdf->Cell(40, 2, utf8_decode(trim($data[1])), 0, 0, 'L');
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(3, 52);
            $this->pdf->Cell(10, 2, utf8_decode("Alumno(a): "), 0, 0, 'L');
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(13, 52);
            $this->pdf->Cell(10, 2, utf8_decode($dataAlumno[0]->DNI), 0, 0, 'L');
        }


        $this->pdf->SetXY(3, 54);
        $this->pdf->Cell(40, 2, utf8_decode(substr($dataAlumno[0]->NOMCOMP, 0, 38)), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(3, 56);
        $this->pdf->Cell(10, 2, utf8_decode("Aula: "), 0, 0, 'L');
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->SetXY(13, 56);
        $this->pdf->Cell(10, 2, utf8_decode($nemodes), 0, 0, 'L');
        // ================BLOQUE CABECERA BOLETA =====================
        $this->pdf->SetFont('Arial', 'B', 5);
        $this->pdf->SetXY(4, 60);
        $this->pdf->Cell(4, 3, utf8_decode("#"), 'TB', 0, 'C');
        $this->pdf->SetXY(8, 60);
        $this->pdf->Cell(27, 3, utf8_decode("CONCEPTO"), 'TB', 0, 'C');
        /* $this->pdf->Cell(21, 3, utf8_decode("CONCEPTO"), 'TB', 0, 'C');
          $this->pdf->SetXY(29, 60);
          $this->pdf->Cell(6, 3, utf8_decode("MORA"), 'TB', 0, 'C'); */
        $this->pdf->SetXY(35, 60);
        $this->pdf->Cell(7, 3, utf8_decode("TOTAL"), 'TB', 0, 'C');
        // ================BLOQUE DETALLE BOLETA =====================
        $iniFila = 63;
        $filas = 1;
        $total = 0;
        $fechapago = "";
        foreach ($dataPago as $pago) {
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(4, $iniFila);
            $this->pdf->Cell(4, 3, utf8_decode($filas), 'B', 0, 'C');
            $this->pdf->SetXY(8, $iniFila);
            if($pago->flgexonera==1) $exo = " (EXO)"; else  $exo =""; // se agrega para las matriculas exoneradas
            $this->pdf->Cell(27, 3, utf8_decode(nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob).$exo), 'B', 0, 'L'); //. ' - ' . $this->ano
            /* $this->pdf->Cell(21, 3, utf8_decode(nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob)), 'B', 0, 'L');
              $this->pdf->SetXY(29, $iniFila);
              $this->pdf->Cell(6, 3, utf8_decode('0.00'), 'B', 0, 'R'); */
            $this->pdf->SetXY(35, $iniFila);
            $this->pdf->Cell(7, 3, utf8_decode($pago->montocob), 'B', 0, 'R');
            $iniFila += 3;
            $total += $pago->montocob;
            $fechapago = $pago->fecmod;
        }

        if ($vtipo == '03') { // Solo para Facturas
            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Gratuitas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Exoneradas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Gravadas: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Op. Inafecta: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("IGV (18%): "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Otros Cargos: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/     0.00"), 0, 0, 'R');

            $iniFila += 3;
            $this->pdf->SetFont('Arial', '', 5);
            $this->pdf->SetXY(3, $iniFila);
            $this->pdf->Cell(40, 3, '======================================', 0, 0, 'C');

            $iniFila += 2;
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(18, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Importe Total: "), 0, 0, 'L');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');
        } else {

            $iniFila += 5;
            $this->pdf->SetFont('Arial', 'B', 5);
            $this->pdf->SetXY(22, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("Importe Total: "), 0, 0, 'C');
            $this->pdf->SetXY(32, $iniFila);
            $this->pdf->Cell(10, 3, utf8_decode("S/ " . number_format($total, 2, '.', ',')), 0, 0, 'R');
        }
        $iniFila += 4;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode("SON : " . convertir_a_letras($total) . ' Y 00/100 SOLES.'), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Fecha de Emisión : ' . $fechapago), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Atendido por         : ' . $datoUsuario->nomcomp), 0, 0, 'L');
        
       /* $iniFila += 4;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Usuario Campus : ' . $vusucampus), 0, 0, 'L');
        $iniFila += 2;
        $this->pdf->SetXY(3, $iniFila);
        $this->pdf->Cell(40, 2, utf8_decode('Clave Campus    : ' . $vclavecampus), 0, 0, 'L');    */
        $iniFila += 5;        
        
        // ===================Creacion de Codigo QR =========================
        $this->load->library('qrcodephp');   
        /* RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE | */
        //$textqr = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . date("d/m/Y") . '|' . $vtipo . '|';
        $algorithm = MCRYPT_BLOWFISH;
        $key = 'keyappweb';
        $data = $dataEmpresa[0]->ruc . '|01|' . $vcomp . '|' . $total . '|' . $vtipo . '|';
        /* $mode = MCRYPT_MODE_CBC;
          $iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, $mode),MCRYPT_DEV_URANDOM);
          $encrypted = mcrypt_encrypt($algorithm, $key, $data, $mode, $iv); */
        $plainText = base64_encode($data);
        /* $encrypted_data = base64_decode($plain_text);
          $decoded = mcrypt_decrypt($algorithm, $key, $encrypted_data, $mode, $iv); */
        $textqr = "http://www.sistemas-dev.com/verboletas.php?id=" . $plainText;
        // echo $textqr; exit;
        $rutaqr = $_SERVER['DOCUMENT_ROOT'] . "/intranet/img/qr/" . $vcomp . ".png";
        $rutaticket = base_url() . "img/qr/" . $vcomp . ".png";
        QRcode::png($textqr, $rutaqr, 'Q', 15, 0); 
        // =================================================================
        $this->pdf->Image($rutaticket, 13, $iniFila, 18, 16, 'PNG');
        $iniFila += 18;
        $this->pdf->SetFont('Arial', '', 4);
        $this->pdf->SetXY(4, $iniFila);
        //$this->pdf->MultiCell(40, 2, utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica, Para consultar el documento ingrese a:"), 0, 'L');
        $this->pdf->MultiCell(40, 2, utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica."), 0, 'L');
        /* $iniFila += 6;
          $this->pdf->SetFont('Arial', 'B', 5);
          $this->pdf->SetXY(4, $iniFila);
          $this->pdf->MultiCell(40, 2, utf8_decode("www.marianista.pe"), 0, 'C'); */
        if ($vtipo != '01') { // diferentes de Recibo
            $iniFila += 4;
            $this->pdf->SetFont('Arial', '', 4);
            $this->pdf->SetXY(4, $iniFila);
            $this->pdf->MultiCell(40, 2, utf8_decode("Estimado Cliente, Conserve su Ticket de compra, Por regulación de SUNAT es indispensable presentarlo para solicitar cambios o devoluciones."), 0, 'L');
        }
     //   $this->pdf->AutoPrint();

        $this->pdf->output($vAlumno . '-' . $vcomp . '.pdf', 'I');
    }  
    
}