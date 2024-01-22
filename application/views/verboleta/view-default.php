<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MARIANISTA - 2020</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script >
            $(document).ready(function () {
                $('#modal_form').modal({backdrop: 'static', keyboard: false});
            });
        </script>
    </head>
    <body>
        <form method="POST" target="_parent" action="http://sistemas-dev.com/intranet/verBoletas/view" name="form1" id="form1" >
            <input type="hidden" value="<?= $ruta ?>" id="hruta" name="hruta" />
            <div class="container">
                <div class="modal fade" id="modal_form" >
                    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" style="text-align: center">COMUNICADO</h4>
                            </div>
                            <!--
                            <div class="modal-body">
                                <p>
                                    Estimado padre de familia, le recordamos que usted mantiene un pago pendiente por  pensión de enseñanza*. Es de considerar, que el esfuerzo en conjunto que realizamos como Familia Marianista, se verá reflejado en una sólida formación educativa.
                                </p>
                                <p style="font-size: 12px; font-style: italic;margin-top:10px; ">*Agradeceremos omitir este mensaje, en caso usted haya realizado el pago dentro de las 48 horas.</p>
                                <br>
                                <p style="font-size: 12px; margin-left:450px;font-weight: bold ">Atentamente,</p>
                                <p style="font-size: 12px; margin-left:450px;font-weight: bold ">Tesorería.</p>
                            </div>
                            -->
                            <div class="modal-body">
                               <p>
                                    Estimado padre de familia, si usted no puede visualizar la Boleta de información de notas de su menor hijo(a) por esta vía deberá acercarse a la oficina del Colegio de manera presencial.
                                </p>                               
                                <br>                                
                               <!-- <p>
                                    Estimado padre de familia, le recordamos que usted mantiene un pago pendiente por pensión de enseñanza del mes de Marzo. Es de considerar, que el esfuerzo en conjunto que realizamos como Familia Marianista, se verá reflejado en una sólida formación educativa.
                                </p>
                                <p>
                                    Para visualizar la boleta de información de notas de la<b>I Unidad</b>, debe estar al día en el pago de sus pensiones.
                                </p>
                                <p style="font-size: 12px; font-style: italic;margin-top:15px; font-weight: 500">*Agradecemos omitir este mensaje, en caso usted haya realizado el pago dentro de las 48 horas. Se realizará la actualización del caso automáticamente.</p>
                                <br>
                                <div style="margin: 5px 15px">
                                <table class="table table-sm table-bordered"     style="width: 100%;">
                                    <thead>
                                    <th style="width: 10%;text-align: center" scope="col">#</th>
                                    <th style="width: 70%;text-align: center" scope="col">Mes </th>
                                    <th style="width: 20%;text-align: center" scope="col">Monto</th>
                                    <thead>
                                    <tbody>
                                <?php $fila=1; foreach($datapago as $pago){ ?>
                                        <tr>
                                                <td style=";text-align: center"><?=$fila;?></td>
                                                <td style="text-align: left"><?="PENSION - ".nombreMesesCompleto($pago->mescob);?> </td>
                                                <td style="text-align: right">S/<?=$pago->montopen;?></td>              
                                        </tr>
                                <?php $fila++; } ?>
                                    </tbody>
                                </table>                                  
                                </div>
                                <p style="font-size: 12px; margin-left:450px;font-weight: bold ">Atentamente,</p>
                                <p style="font-size: 12px; margin-left:450px;font-weight: bold ">Tesorería.</p>
                            </div>               -->             
                          <!--  <div class="modal-footer">
                                <center>
                                    <?php if($flgver=='1'){ ?>
                                    <button type="submit" id="btnVer" class="btn btn-success"> Ver Boleta </button>
                                    <?php } elseif($flgver=='0') { ?>
                                    <button type="submit" id="btnVer" class="btn btn-success"> Ver Boleta (Solo I Unidad)</button>
                                    <?php } ?>
                                </center>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--   <div class="container" style="padding: 170px 270px">
           <div class="alert alert-success" role="alert">
               <center><h4 class="alert-heading">Comunicado!</h4></center>
               <p>
                   Estimado padre de familia, le recordamos que usted mantiene un pago pendiente por  pensión de enseñanza*. Es de considerar, que el esfuerzo en conjunto que realizamos como Familia Marianista, se verá reflejado en una sólida formación educativa.
                   <a href="<?= $ruta ?>" style="cursor: pointer;font-weight: bold;text-decoration:none;" target="_parent">Ver Boleta</a>
               </p>
               <p style="font-size: 12px; font-style: italic;margin-top:10px; ">*Agradeceremos omitir este mensaje, en caso usted haya realizado el pago dentro de las 48 horas.</p>
               <br>
               <p style="font-size: 12px; margin-left:500px;font-weight: bold ">Atentamente,</p>
               <p style="font-size: 12px; margin-left:500px;font-weight: bold ">Tesorería.</p>
           </div>
           <hr>
        -->
        <!--
        <table class="table table-bordered table-striped"     style="width: 50%;">
            <thead>
            <th style="width: 10%;text-align: center">#</th>
            <th style="width: 70%;text-align: center">Mes </th>
            <th style="width: 20%;text-align: center">Monto</th>
            <thead>
            <tbody>
        <?php //$fila=1; foreach($datapago as $pago){ ?>
                <tr>
                        <td style=";text-align: center"><?//=$fila;?></td>
                        <td style="text-align: left"><?//="PENSION - ".nombreMesesCompleto($pago->mescob);?> </td>
                        <td style="text-align: right"><?//=$pago->montopen;?></td>              
                </tr>
        <?php //$fila++; } ?>
            </tbody>
        </table>  
        -->
        <!-- </div> -->
    </body>
</html>