<html>
<head>
<title>:TICKET :</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="GENERATOR" content="Quanta Plus KDE"> 
<style type="text/css">
.Imprime {
	
	font-family:Arial;
	font-size:16px;
}
@page{
   margin: 0;
}
</style>
</head>
<body onload="print();"> 
 
                
        <table border=0 width=310 cellspacing="0" cellpadding="0" class="Imprime"> 
		<tr>
		<td><center><img src="<?php echo base_url(); ?>img/HRline200.png" width="250"></center></td>	
		</tr>
                <tr>
		<td><center><?php echo NOMBRE_EMPRESA; ?></center></td>	
		</tr>
                <tr>
		<td><center><?php echo LEYENDA_EXTRA;?></center></td>	
		</tr>
                <tr>
		<td><center><?php echo CALLE;?></center></td>	
		</tr>
                
                <tr>
		<td><center><?php echo CP.', '.CIUDAD.', '.ESTADO; ?></center></td>	
		</tr>
                <tr>
		<td><center><?php echo TELEFONO; ?></center></td>	
		</tr>
                <tr>
		<td><center><img src="<?php echo base_url(); ?>img/HRline200.png" width="250"></center></td>	
		</tr>
                <tr>
		<td><center>
                FECHA: <?php echo date("Y")."-".date("m")."-".date("d")." HORA: ".date("H:i:s"); ?>
                <br/>
                VENDEDOR: <?php echo $this->session->userdata('NOMBRE').' '.$this->session->userdata('APELLIDOS'); ?>
                <BR/> 
				DOCUMENTO: <strong><?php echo $NumOrder; ?> </strong>
                </center></td>	
		</tr>
                
                <tr>
		<td>
                    <table align='center' border=0 width=310 cellspacing="0" cellpadding="0" class="Imprime" >
                    <tr><td colspan=5><hr/></td></tr>
                    <tr>
                    <td align='right'><small>COD.</small></td>
                    <td align='center'><small>DESCRIPCION</small></td>
                    <td align='center'><small>CANT.</small></td>
                    <td align='center'><small>PRECIO</small></td>
                    <td align='left'><small>TOTAL</small></td>
                    </tr>
                    <tr><td colspan=5><hr/></td></tr>
                    <?php
                    	foreach ($ListOrder as $key => $value) {
                            $cuantosLetras = strlen($value->DESCRIPCION);
                            if($cuantosLetras >=13)
                            {
                                $cuantosLetras = 13;
                            }
                            if($cuantosLetras <=13)
                            {
                                $cuantosLetras = $cuantosLetras;
                            }
                    		# code...
                    		 echo '<tr>';
							 echo '<td style="font-size:12px;" align="right"><small>'.$value->CLAVE.'</small></td>';
							 echo '<td style="font-size:12px;" align="center"><small>'.substr($value->DESCRIPCION,0,$cuantosLetras).'</small></td>';
							 echo '<td style="font-size:12px;">'.$value->SALIDAS.'</td>';
							 echo '<td style="font-size:12px;" align="left">'.$value->PRECIO.'</td>';
							 echo '<td style="font-size:12px;" align="left">'.($value->PRECIO) * ($value->SALIDAS).'</td>';
							 echo '</tr>';
                    	}
                     ?>
                     
			  
               <tr><td colspan=5><hr/></td></tr>
               <tr><td colspan=3 align='center'>SUBTOTAL: </td><td colspan=2 align='center'>$ <?php echo $DocOrder[0]->BRUTO; ?></td></tr>
               <tr><td colspan=3 align='center'>IVA: </td><td colspan=2 align='center'>$ <?php echo $DocOrder[0]->TOTAL_IMPUESTO; ?></td></tr>
               <tr><td colspan=3 align='center'>TOTAL: </td><td colspan=2 align='center'>$ <?php echo $DocOrder[0]->TOTAL; ?></td></tr>
               
                    </table>
                </td>	
		</tr>    
    <tr>
    <td><br/><br/><center><img src="<?php echo base_url(); ?>img/HRline200.png" width="250"></center></td> 
    </tr>
                <tr>
    <td><center>GRACIAS POR SU PREFERENCIA!</center></td>  
    </tr>
                <tr>
    <td><center><img src="<?php echo base_url(); ?>img/HRline200.png" width="250"></center></td>  
    </tr>      
		</table>
           <div id='all_table' style='page-break-after: always'></div> 
		
		

</body>
</html>
	
	
	
	
	