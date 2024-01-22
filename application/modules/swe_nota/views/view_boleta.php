<?php

$ruta = $archivo;
// echo $ruta; exit; 
//if (file_exists ($ruta)) {
//$ruta ='http://sistemas-dev.com/MANUAL_DE_USUARIO.pdf';
//echo "Ruta :".$ruta; 
//$content = file_get_contents($ruta);
    header ("Content-type:application/pdf");
//header('Content-Length: '.strlen( $content ));
    header ("Content-Disposition:inline;filename='" . $ruta . "'");
//header('Cache-Control: public, must-revalidate, max-age=0');
//header('Pragma: public');
//header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
//header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
//echo $content;
    readfile ($ruta);
//} else {
   // echo "<script>alert('NO EXISTE AUN BOLETA PARA ESTA UNIDAD.');</script>";
//}
?>

