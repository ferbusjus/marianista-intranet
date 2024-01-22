
<html>

    <head>
        <script type="text/javascript" >

            function imprimir() {
                window.print();
            }
        </script>        
        <style>
            * {
                font-size: 12px;
                font-family: 'Trebuchet MS';
            }

            td,
            th,
            tr,
            table {
                border-top: 1px solid black;
                border-collapse: collapse;
            }

            td.producto,
            th.producto {
                width: 120px;
                max-width: 120px;
                font-size: 9px;
            }

            td.cantidad,
            th.cantidad {
                width: 10px;
                max-width: 10px;
                word-break: break-all;
                font-size: 9px;
            }

            td.precio,
            th.precio {
                width: 30px;
                max-width: 30px;
                word-break: break-all;
                font-size: 9px;
                text-align: right;
            }

            .centrado {
                text-align: center;
                font-size:7px;
                font-weight: bold;
            }

            .ticket {
                width: 165px;
                max-width: 165px;
            } 
        </style>

    </head>
    <!--  -->
    <body onload="javascript:imprimir();">
        <div class="ticket">
            <center><img src="<?= BASE_URL ?>images/insigniachico.png"width="80px" height="80px"  ></center>
            <center>
                <br><label style="font-weight: bold;font-size: 14px"><?= utf8_decode($tipodesc . ' ' . $tipoElectro) ?></label>
                <br><label style="font-weight: bold;font-size: 12px">R.U.C: <?= $empresa[0]->ruc ?></label>
                <br><label style="font-weight: bold;font-size: 12px"><?= $numero ?></label>
                <br>
                **************************************
                <br><label style="font-size: 8px"><?= utf8_decode($empresa[0]->nombre_comercial) ?>     </label>           
                <br><label style="font-weight: bold;">"<?= $empresa[0]->razon_social ?>" </label>
                <br>
                <br><label style="font-size: 8px"><?= utf8_decode($empresa[0]->direccion) ?>     </label>  
                <br><label style="font-size: 8px">Tel&eacute;fono: <?= utf8_decode($empresa[0]->telefono) ?>     </label>  
                <br>
                **************************************                
            </center>
            <label style="font-size: 8px">Sr(s): <?= $apoderado ?> </label> 
            <br>
            <label style="font-size: 8px">Alumno(a): <?= $alumno[0]->NOMCOMP ?> </label> 
            <br>
            <!--
            <label style="font-size: 8px">Aula: <?//= $nemodes ?>     </label> 
            <br>-->
            <br>

            <table>
                <thead>
                    <tr>
                        <th class="cantidad">#</th>
                        <th class="producto">CONCEPTO</th>
                        <th class="precio">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $fila = 1;
                    $total = 0;
                    foreach ($dataPago as $pago) { ?>
                    <?php 
                        if($pago->concob=='02'){
                            $exo = ($pago->flgexonera=='1')? ' - (EXO)' : '';
                            $descPago ='MATRICULA - ' . $this->ano.$exo; // utf8_decode(nombreConcepto($pago->concob). ' - ' . $this->ano.$exo);
                        } else {
                            $descPago =nombreConcepto($pago->concob) . ' - ' . nombreMesesCompleto($pago->mescob) . ' - ' . $this->ano;
                        }
                    ?>
                        <tr>
                            <td class="cantidad"><?= $fila ?></td>
                            <td class="producto"><?= $descPago ?></td>
                            <td class="precio"><?= $pago->montocob ?></td>
                        </tr>
    <?php $fila++;
    $total += $pago->montocob;
} ?>                                      
                </tbody>
            </table>
            <br> 
            <?php //if ($total == 0): ?>
               <!-- <div style="font-weight: bold;font-size: 10px;float: right;margin-top: 3px;">                 
                    OCURRIO UN ERROR INTERNO.!
                </div>            
               -->
<?php //else: ?>
               
    <?php if ($total == 0) { $total = 0.00 ; } ?>
    <?php if ($tipoDoc == '03') : ?>
                    <div style="font-weight: normal;font-size: 10px;float: right;margin-top: 3px;">                 
                        Op. Gratuitas:     S/ 0.00
                    </div>
                    <div style="font-weight: normal;font-size: 10px;float: right;margin-top: 3px;">                 
                        Op. Exoneradas:     S/ 0.00
                    </div>
                    <div style="font-weight: normal;font-size: 10px;float: right;margin-top: 3px;">                 
                        Op. Gravadas:      S/.<?= utf8_decode(number_format($total, 2, '.', ',')) ?>
                    </div>
                    <div style="font-weight: normal;font-size: 10px;float: right;margin-top: 3px;">                 
                        Op. Inafecta:       S/ 0.00
                    </div>          
                    <div style="font-weight: normal;font-size: 10px;float: right;margin-top: 3px;">                 
                        IGV (18%):         S/ 0.00
                    </div> 
                    <div style="font-weight: normal;font-size: 10px;float: right;margin-top: 3px;">                 
                        Otros Cargos:      S/ 0.00
                    </div>
                    <br>
                    ************************************** 
                    <div style="font-weight: bold;font-size: 10px;float: right;margin-top: 3px;">                 
                        TOTAL: S/ <?= utf8_decode(number_format($total, 2, '.', ',')) ?>
                    </div>
                    ************************************** 
                <?php else: ?>
                    <br>
                    <div style="font-weight: bold;font-size: 10px;float: right;margin-top: 10px;">                 
                        TOTAL: S/ <?= utf8_decode(number_format($total, 2, '.', ',')) ?>
                    </div>             
    <?php endif; ?>
                <br><br>
                <div style="font-weight: bold;font-size: 7px;float: left">
    <?= utf8_decode("SON : " . convertir_a_letras($total) . ' Y 00/100 Soles.') ?>
                </div>
                <br><div style="font-weight: bold;font-size: 7px;float: left"><?= utf8_decode("Fecha de Emisión : " . date('d/m/Y H:i:s')) ?></div>                         
                <br><div style="font-weight: bold;font-size: 7px;float: left"><?= utf8_decode("Atendido por : " . $dataUsuario->nomcomp) ?></div>
                <br><br>
                <center>
                    <img style="width: 60px;" src="<?= $rutaqr ?>" />
                </center>           
                <br>
               <!-- 
               <div style="font-size: 7px;float: left">
                <?//= utf8_decode("Autorizado mediante Resolución  impresa de la Venta Electrónica, Para consultar el documento ingrese a:"); ?>
                </div>            
                <p class="centrado">                   
                    <br>www.marianista.pe
                </p>
                -->
                <?php if ($tipoDoc != '01') { ?>
                    <div style="font-size: 7px;float: left">
                    <?= utf8_decode("Estimado Cliente, Conserve su Ticket de compra, Por regulación de SUNAT es indispensable presentarlo para solicitar cambios o devoluciones."); ?>
                    </div>        
                    <br>
    <?php } ?>
<?php //endif; ?>
        </div>
    </body>

</html>