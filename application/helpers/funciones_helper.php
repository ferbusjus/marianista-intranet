<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//si no existe la función invierte_date_time la creamos
if (!function_exists('invierte_date_time')) {

    //formateamos la fecha y la hora, función de cesarcancino.com
    function invierte_date_time($fecha) {
        $datetime_format = '';
        if (!empty($fecha)) {
            $day = substr($fecha, 8, 2);
            $month = substr($fecha, 5, 2);
            $year = substr($fecha, 0, 4);
            $hour = substr($fecha, 11, 8);
            $datetime_format = $day . "-" . $month . "-" . $year . '<br> ' . $hour;
        }
        return $datetime_format;
    }

}

if (!function_exists('invierte_date')) {

    //formateamos la fecha y la hora, función de cesarcancino.com
    function invierte_date($fecha) {
        $datetime_format = '';
        if (!empty($fecha)) {
            $day = substr($fecha, 8, 2);
            $month = substr($fecha, 5, 2);
            $year = substr($fecha, 0, 4);
            $datetime_format = $day . "-" . $month . "-" . $year;
        }
        return $datetime_format;
    }

}

if (!function_exists('devuelveArrayFechasEntreOtrasDos')) {

    function devuelveArrayFechasEntreOtrasDos($fechaInicio, $fechaFin) {
        $arrayFechas = array();
        $fechaMostrar = $fechaInicio;

        while (strtotime($fechaMostrar) <= strtotime($fechaFin)) {
            $arrayFechas[] = $fechaMostrar;
            $fechaMostrar = date("d-m-Y", strtotime($fechaMostrar . " + 1 day"));
        }

        return $arrayFechas;
    }

}

if (!function_exists('devuelvedia')) {

    function devuelvedia($fecha) {
        $fechats = strtotime($fecha); //a timestamp
        $vdia = '';
        switch (date('w', $fechats)) {
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

    if (!function_exists('notificaError')) {

        function notificaError($vmensaje = '', $vmail = 'ffernandox@hotmail.com', $vcole = 'MARIANISTA') {
            $vResp = '';
            $vcuerpo = '';
            $vcuerpoErr = '';
            $vmensaje = (is_array($vmensaje) == FALSE) ? array($vmensaje) : $vmensaje;
            foreach ($vmensaje as $error => $desc) {
                $vcuerpoErr .= "<tr style='width:100px'><td align='center' style='width:600px'><b>$error:</b></td><td align='center'>$desc</td></tr>";
            }
            if (trim($vcuerpoErr) != "") {
                $vcuerpo .= "<table align='center' border=1 width ='700px' >";
                $vcuerpo .= "<tr><td  align='center' colspan='2' style='font-size:14px;font-weight:bold;'>ERRORRES ENCONTRADOS EN EL SISTEMAS</td></tr>";
                $vcuerpo .= $vcuerpoErr . "</table>";
            } else {
                $vcuerpo = 'No Hay Ningun Error.';
            }

            $mail = new Mailer();
            $mail->CharSet = 'UTF-8';
            $mail->SetLanguage('es');
            $mail->FromName = "Colegio Marianista";
            $mail->From = "info@sistemas-dev.com";
            $mail->Subject = "LOG DE ERRORES  - " . $vcole;
            $mail->AddAddress($vmail);
            $mail->AddReplyTo('info@sistemas-dev.com', 'SISTEMAS-DEV');
            $mail->addCC("info@sistemas-dev.com");
            $mail->msgHTML($vcuerpo);
            $mail->IsHTML(true);
            if (!$mail->send()) {
                $vResp = 'Error: ' . $mail->ErrorInfo;
            } else {
                $vResp = 1;
            }
        }

    }

    if (!function_exists('EnviarMailAdjuntos')) {

        function EnviarMailAdjuntos($arrEmail = array(), $pathAdjunto = '',$nomArchivo='') {
            $vResp = '';
            $vcuerpo = '';
            $vcuerpo .= "<table border=0 width ='700px' >";
            $vcuerpo .= "<tr><td  align='left' style='font-size:12px;font-weight:bold;'>Estimad@ Padre de Familia se adjunta la boleta de pago.</td></tr>";
            $vcuerpo .= "</table>";

            $mail = new Mailer();
            $mail->CharSet = 'UTF-8';
            // $mail->Mailer = "smtp";
            //$mail->Host = "localhost";
            //$mail->Username = "fercimas@gmail.com";
            //$mail->Password="978431734";
            //$mail->Port = 25;
            $mail->SetLanguage('es');
            $mail->FromName = "Colegio Marianista";
            $mail->From = "boletaelectronica@marianista.edu.pe";
            $mail->Subject = "BOLETA DE PAGO  -  MARIANISTA ".date("Y");
            foreach ($arrEmail as $fila) {
              //  print_r($fila); exit;
                if (trim($fila['email']) != "") { // verificando email validos
                    $mail->AddAddress($fila['email']);
                }
            }
            $mail->AddReplyTo('boletaelectronica@marianista.edu.pe', 'MARIANISTA');
            $mail->addCC("tesoreria@marianista.edu.pe");
            $mail->addCC("ffernandox@hotmail.com");
            $mail->AddAttachment($pathAdjunto, $nomArchivo);
            $mail->msgHTML($vcuerpo);
            $mail->IsHTML(true);
            if (!$mail->send()) {
                $vResp = 'Error: ' . $mail->ErrorInfo;
            } else {
                $vResp = 1;
            }
            return $vResp;
        }
        
    }

    if (!function_exists('nombreGrado')) {

        function nombreGrado($nivel = 0, $grado = 0) {
            $vdsc = '';
            if ($nivel == 'I') {
                if ($grado == '3')
                    $vdsc = '3 Años';
                elseif ($grado == '4')
                    $vdsc = '4 Años';
                elseif ($grado == '5')
                    $vdsc = '5 Años';
                else
                    $vdsc = 'Todos';
            } elseif ($nivel == 'P') {
                if ($grado == '1')
                    $vdsc = 'Primer Grado';
                elseif ($grado == '2')
                    $vdsc = 'Segundo Grado';
                elseif ($grado == '3')
                    $vdsc = 'Tercer Grado';
                elseif ($grado == '4')
                    $vdsc = 'Cuarto Grado';
                elseif ($grado == '5')
                    $vdsc = 'Quinto Grado';
                elseif ($grado == '6')
                    $vdsc = 'Sexto Grado';
                else
                    $vdsc = 'Todos';
            } elseif ($nivel == 'S') {
                if ($grado == '1')
                    $vdsc = 'Primer Año';
                elseif ($grado == '2')
                    $vdsc = 'Segundo Año';
                elseif ($grado == '3')
                    $vdsc = 'Tercer Año';
                elseif ($grado == '4')
                    $vdsc = 'Cuarto Año';
                elseif ($grado == '5')
                    $vdsc = 'Quinto Año';
                else
                    $vdsc = 'Todos';
            } else {
                $vdsc = 'Todos';
            }
            return $vdsc;
        }

    }

    if (!function_exists('nombreNivel')) {

        function nombreNivel($nivel = 0) {
            switch ($nivel) {
                case 'I': $nivel = "Inicial";
                    break;
                case 'P': $nivel = "Primaria";
                    break;
                case 'S': $nivel = "Secundaria";
                    break;
                default: $nivel = "Todos";
                    break;
            }
            return $nivel;
        }

    }

    if (!function_exists('nombreConcepto')) {

        function nombreConcepto($vtipo = '') {
            $vdesConcepto = 'OTROS';
            switch ($vtipo) {
                case '02':
                    $vdesConcepto = 'MATRICULA';
                    break;
                case '01':
                    $vdesConcepto = utf8_decode('PENSIÓN');
                    break;
            }

            return $vdesConcepto;
        }

    }

    if (!function_exists('nombreMesesCompleto')) {

        function nombreMesesCompleto($vmes = '') {
            $vmesdes = '';
            switch ($vmes) {
                case '01':
                    $vmesdes = 'ENERO';
                    break;
                case '02':
                    $vmesdes = 'FEBRERO';
                    break;
                case '03':
                    $vmesdes = 'MARZO';
                    break;
                case '04':
                    $vmesdes = 'ABRIL';
                    break;
                case '05':
                    $vmesdes = 'MAYO';
                    break;
                case '06':
                    $vmesdes = 'JUNIO';
                    break;
                case '07':
                    $vmesdes = 'JULIO';
                    break;
                case '08':
                    $vmesdes = 'AGOSTO';
                    break;
                case '09':
                    $vmesdes = 'SETIEMBRE';
                    break;
                case '10':
                    $vmesdes = 'OCTUBRE';
                    break;
                case '11':
                    $vmesdes = 'NOVIEMBRE';
                    break;
                case '12':
                    $vmesdes = 'DICIEMBRE';
                    break;
            }

            return $vmesdes;
        }

    }

    if (!function_exists('nombreMeses')) {

        function nombreMeses($vmes = '') {
            $vmesdes = '';
            switch ($vmes) {
                case '03':
                    $vmesdes = 'MAR';
                    break;
                case '04':
                    $vmesdes = 'ABR';
                    break;
                case '05':
                    $vmesdes = 'MAY';
                    break;
                case '06':
                    $vmesdes = 'JUN';
                    break;
                case '07':
                    $vmesdes = 'JUL';
                    break;
                case '08':
                    $vmesdes = 'AGO';
                    break;
                case '09':
                    $vmesdes = 'SET';
                    break;
                case '10':
                    $vmesdes = 'OCT';
                    break;
                case '11':
                    $vmesdes = 'NOV';
                    break;
                case '12':
                    $vmesdes = 'DIC';
                    break;
            }

            return $vmesdes;
        }

    }

    if (!function_exists('nombremes')) {

        function nombremes($mes) {
            $nombre = 'Number de mes not valid.';
            if (is_numeric($mes) && $mes != 0) {
                setlocale(LC_TIME, 'spanish');
                $nombre = strftime("%B", mktime(0, 0, 0, $mes, 1, 2000));
            }
            return strtoupper($nombre);
        }

    }

// FUNCIONES DE CONVERSION DE NUMEROS A LETRAS.

    function centimos() {
        global $importe_parcial;

        $importe_parcial = number_format($importe_parcial, 2, ".", "") * 100;

        if ($importe_parcial > 0)
            $num_letra = " con " . decena_centimos($importe_parcial);
        else
            $num_letra = "";

        return $num_letra;
    }

    function unidad_centimos($numero) {
        switch ($numero) {
            case 9: {
                    $num_letra = "nueve céntimos";
                    break;
                }
            case 8: {
                    $num_letra = "ocho céntimos";
                    break;
                }
            case 7: {
                    $num_letra = "siete céntimos";
                    break;
                }
            case 6: {
                    $num_letra = "seis céntimos";
                    break;
                }
            case 5: {
                    $num_letra = "cinco céntimos";
                    break;
                }
            case 4: {
                    $num_letra = "cuatro céntimos";
                    break;
                }
            case 3: {
                    $num_letra = "tres céntimos";
                    break;
                }
            case 2: {
                    $num_letra = "dos céntimos";
                    break;
                }
            case 1: {
                    $num_letra = "un céntimo";
                    break;
                }
        }
        return $num_letra;
    }

    function decena_centimos($numero) {
        if ($numero >= 10) {
            if ($numero >= 90 && $numero <= 99) {
                if ($numero == 90)
                    return "noventa céntimos";
                else if ($numero == 91)
                    return "noventa y un céntimos";
                else
                    return "noventa y " . unidad_centimos($numero - 90);
            }
            if ($numero >= 80 && $numero <= 89) {
                if ($numero == 80)
                    return "ochenta céntimos";
                else if ($numero == 81)
                    return "ochenta y un céntimos";
                else
                    return "ochenta y " . unidad_centimos($numero - 80);
            }
            if ($numero >= 70 && $numero <= 79) {
                if ($numero == 70)
                    return "setenta céntimos";
                else if ($numero == 71)
                    return "setenta y un céntimos";
                else
                    return "setenta y " . unidad_centimos($numero - 70);
            }
            if ($numero >= 60 && $numero <= 69) {
                if ($numero == 60)
                    return "sesenta céntimos";
                else if ($numero == 61)
                    return "sesenta y un céntimos";
                else
                    return "sesenta y " . unidad_centimos($numero - 60);
            }
            if ($numero >= 50 && $numero <= 59) {
                if ($numero == 50)
                    return "cincuenta céntimos";
                else if ($numero == 51)
                    return "cincuenta y un céntimos";
                else
                    return "cincuenta y " . unidad_centimos($numero - 50);
            }
            if ($numero >= 40 && $numero <= 49) {
                if ($numero == 40)
                    return "cuarenta céntimos";
                else if ($numero == 41)
                    return "cuarenta y un céntimos";
                else
                    return "cuarenta y " . unidad_centimos($numero - 40);
            }
            if ($numero >= 30 && $numero <= 39) {
                if ($numero == 30)
                    return "treinta céntimos";
                else if ($numero == 91)
                    return "treinta y un céntimos";
                else
                    return "treinta y " . unidad_centimos($numero - 30);
            }
            if ($numero >= 20 && $numero <= 29) {
                if ($numero == 20)
                    return "veinte céntimos";
                else if ($numero == 21)
                    return "veintiun céntimos";
                else
                    return "veinti" . unidad_centimos($numero - 20);
            }
            if ($numero >= 10 && $numero <= 19) {
                if ($numero == 10)
                    return "diez céntimos";
                else if ($numero == 11)
                    return "once céntimos";
                else if ($numero == 11)
                    return "doce céntimos";
                else if ($numero == 11)
                    return "trece céntimos";
                else if ($numero == 11)
                    return "catorce céntimos";
                else if ($numero == 11)
                    return "quince céntimos";
                else if ($numero == 11)
                    return "dieciseis céntimos";
                else if ($numero == 11)
                    return "diecisiete céntimos";
                else if ($numero == 11)
                    return "dieciocho céntimos";
                else if ($numero == 11)
                    return "diecinueve céntimos";
            }
        } else
            return unidad_centimos($numero);
    }

    function unidad($numero) {
        switch ($numero) {
            case 9: {
                    $num = "nueve";
                    break;
                }
            case 8: {
                    $num = "ocho";
                    break;
                }
            case 7: {
                    $num = "siete";
                    break;
                }
            case 6: {
                    $num = "seis";
                    break;
                }
            case 5: {
                    $num = "cinco";
                    break;
                }
            case 4: {
                    $num = "cuatro";
                    break;
                }
            case 3: {
                    $num = "tres";
                    break;
                }
            case 2: {
                    $num = "dos";
                    break;
                }
            case 1: {
                    $num = "uno";
                    break;
                }
        }
        return $num;
    }

    function decena($numero) {
        if ($numero >= 90 && $numero <= 99) {
            $num_letra = "noventa ";

            if ($numero > 90)
                $num_letra = $num_letra . "y " . unidad($numero - 90);
        } else if ($numero >= 80 && $numero <= 89) {
            $num_letra = "ochenta ";

            if ($numero > 80)
                $num_letra = $num_letra . "y " . unidad($numero - 80);
        } else if ($numero >= 70 && $numero <= 79) {
            $num_letra = "setenta ";

            if ($numero > 70)
                $num_letra = $num_letra . "y " . unidad($numero - 70);
        } else if ($numero >= 60 && $numero <= 69) {
            $num_letra = "sesenta ";

            if ($numero > 60)
                $num_letra = $num_letra . "y " . unidad($numero - 60);
        } else if ($numero >= 50 && $numero <= 59) {
            $num_letra = "cincuenta ";

            if ($numero > 50)
                $num_letra = $num_letra . "y " . unidad($numero - 50);
        } else if ($numero >= 40 && $numero <= 49) {
            $num_letra = "cuarenta ";

            if ($numero > 40)
                $num_letra = $num_letra . "y " . unidad($numero - 40);
        } else if ($numero >= 30 && $numero <= 39) {
            $num_letra = "treinta ";

            if ($numero > 30)
                $num_letra = $num_letra . "y " . unidad($numero - 30);
        } else if ($numero >= 20 && $numero <= 29) {
            if ($numero == 20)
                $num_letra = "veinte ";
            else
                $num_letra = "veinti" . unidad($numero - 20);
        } else if ($numero >= 10 && $numero <= 19) {
            switch ($numero) {
                case 10: {
                        $num_letra = "diez ";
                        break;
                    }
                case 11: {
                        $num_letra = "once ";
                        break;
                    }
                case 12: {
                        $num_letra = "doce ";
                        break;
                    }
                case 13: {
                        $num_letra = "trece ";
                        break;
                    }
                case 14: {
                        $num_letra = "catorce ";
                        break;
                    }
                case 15: {
                        $num_letra = "quince ";
                        break;
                    }
                case 16: {
                        $num_letra = "dieciseis ";
                        break;
                    }
                case 17: {
                        $num_letra = "diecisiete ";
                        break;
                    }
                case 18: {
                        $num_letra = "dieciocho ";
                        break;
                    }
                case 19: {
                        $num_letra = "diecinueve ";
                        break;
                    }
            }
        } else
            $num_letra = unidad($numero);

        return $num_letra;
    }

    function centena($numero) {
        if ($numero >= 100) {
            if ($numero >= 900 & $numero <= 999) {
                $num_letra = "novecientos ";

                if ($numero > 900)
                    $num_letra = $num_letra . decena($numero - 900);
            } else if ($numero >= 800 && $numero <= 899) {
                $num_letra = "ochocientos ";

                if ($numero > 800)
                    $num_letra = $num_letra . decena($numero - 800);
            } else if ($numero >= 700 && $numero <= 799) {
                $num_letra = "setecientos ";

                if ($numero > 700)
                    $num_letra = $num_letra . decena($numero - 700);
            } else if ($numero >= 600 && $numero <= 699) {
                $num_letra = "seiscientos ";

                if ($numero > 600)
                    $num_letra = $num_letra . decena($numero - 600);
            } else if ($numero >= 500 && $numero <= 599) {
                $num_letra = "quinientos ";

                if ($numero > 500)
                    $num_letra = $num_letra . decena($numero - 500);
            } else if ($numero >= 400 && $numero <= 499) {
                $num_letra = "cuatrocientos ";

                if ($numero > 400)
                    $num_letra = $num_letra . decena($numero - 400);
            } else if ($numero >= 300 && $numero <= 399) {
                $num_letra = "trescientos ";

                if ($numero > 300)
                    $num_letra = $num_letra . decena($numero - 300);
            } else if ($numero >= 200 && $numero <= 299) {
                $num_letra = "doscientos ";

                if ($numero > 200)
                    $num_letra = $num_letra . decena($numero - 200);
            } else if ($numero >= 100 && $numero <= 199) {
                if ($numero == 100)
                    $num_letra = "cien ";
                else
                    $num_letra = "ciento " . decena($numero - 100);
            }
        } else
            $num_letra = decena($numero);

        return $num_letra;
    }

    function cien() {
        global $importe_parcial;

        $parcial = 0;
        $car = 0;

        while (substr($importe_parcial, 0, 1) == 0)
            $importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);

        if ($importe_parcial >= 1 && $importe_parcial <= 9.99)
            $car = 1;
        else if ($importe_parcial >= 10 && $importe_parcial <= 99.99)
            $car = 2;
        else if ($importe_parcial >= 100 && $importe_parcial <= 999.99)
            $car = 3;

        $parcial = substr($importe_parcial, 0, $car);
        $importe_parcial = substr($importe_parcial, $car);

        $num_letra = centena($parcial) . centimos();

        return $num_letra;
    }

    function cien_mil() {
        global $importe_parcial;

        $parcial = 0;
        $car = 0;

        while (substr($importe_parcial, 0, 1) == 0)
            $importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);

        if ($importe_parcial >= 1000 && $importe_parcial <= 9999.99)
            $car = 1;
        else if ($importe_parcial >= 10000 && $importe_parcial <= 99999.99)
            $car = 2;
        else if ($importe_parcial >= 100000 && $importe_parcial <= 999999.99)
            $car = 3;

        $parcial = substr($importe_parcial, 0, $car);
        $importe_parcial = substr($importe_parcial, $car);

        if ($parcial > 0) {
            if ($parcial == 1)
                $num_letra = "mil ";
            else
                $num_letra = centena($parcial) . " mil ";
        }

        return $num_letra;
    }

    function millon() {
        global $importe_parcial;

        $parcial = 0;
        $car = 0;

        while (substr($importe_parcial, 0, 1) == 0)
            $importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);

        if ($importe_parcial >= 1000000 && $importe_parcial <= 9999999.99)
            $car = 1;
        else if ($importe_parcial >= 10000000 && $importe_parcial <= 99999999.99)
            $car = 2;
        else if ($importe_parcial >= 100000000 && $importe_parcial <= 999999999.99)
            $car = 3;

        $parcial = substr($importe_parcial, 0, $car);
        $importe_parcial = substr($importe_parcial, $car);

        if ($parcial == 1)
            $num_letras = "un millón ";
        else
            $num_letras = centena($parcial) . " millones ";

        return $num_letras;
    }

    function convertir_a_letras($numero) {
        global $importe_parcial;
        if($numero==0){
            return " CERO Y 00/100 SOLES";
        }
        $importe_parcial = $numero;

        if ($numero < 1000000000) {
            if ($numero >= 1000000 && $numero <= 999999999.99)
                $num_letras = millon() . cien_mil() . cien();
            else if ($numero == 1000 || $numero == 2000 || $numero == 3000)
                $num_letras = cien_mil();
            else if ($numero > 1000 && $numero <= 999999.99)
                $num_letras = cien_mil() . cien();
            else if ($numero >= 1 && $numero <= 999.99)
                $num_letras = cien();
            else if ($numero >= 0.01 && $numero <= 0.99) {
                if ($numero == 0.01)
                    $num_letras = "un céntimo";
                else
                    $num_letras = convertir_a_letras(($numero * 100) . "/100") . " céntimos";
            }
        }
        return strtoupper($num_letras);
    }

    function funCualitativoStandar($pa = 0, $nivel='') {
        $ncu = '';
        if ($pa == "EXO") {
            $ncu = 'EXO';
        } elseif ($pa == "") {
			$ncu = '';
		} else {
			if($nivel=='P'){
				if ($pa >= 0 && $pa <= 10)
					$ncu = 'C';
				if ($pa >= 11 && $pa <= 12)
					$ncu = 'B';
				if ($pa >= 13 && $pa <= 16)
					$ncu = 'A';
				if ($pa >= 17 && $pa <= 20)
					$ncu = 'AD';
			} elseif($nivel=='S'){
				if ($pa >= 0 && $pa <= 10)
					$ncu = 'C';
				if ($pa >= 11 && $pa <= 13)
					$ncu = 'B';
				if ($pa >= 14 && $pa <= 17)
					$ncu = 'A';
				if ($pa >= 18 && $pa <= 20)
					$ncu = 'AD';				
			}

        }
        return $ncu;
    }
    
}