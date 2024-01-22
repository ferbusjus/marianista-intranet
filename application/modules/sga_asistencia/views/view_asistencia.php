<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h1 class="page-header"><span class="glyphicon glyphicon-list"></span> Asistencias</h1>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmAsistencia" action="<?= BASE_URL ?>sga_asistencia/generaReporte" onsubmit="return validaPost();" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;Salon : </td>
                <td style ="width: 30%">
                    <select name="cbsalon" id="cbsalon" style="width: 100%" class="form-control input-sm">
                        <option value="0">:::::::::::::: Seleccione Salon ::::::::::::::</option>
                        <?php foreach ($dataSalones as $salon) : ?>
                            <option value="<?php echo $salon->NEMO ?>"><?php echo $salon->NEMO . " - " . $salon->NEMODES ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>        
                <td style ="width: 10%">&nbsp; Alumno : </td>
                <td style ="width: 40%" colspan="3">
                    <select name="cbalumno" id="cbalumno" style="width: 100%" class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::::::: Seleccione Alumno ::::::::::::::::::::::::::::</option>
                    </select>
                </td>           
                <td style ="width: 10%">
                    &nbsp;<button type="button"   id="btnBuscar" class="btn btn-primary"><i class="fa fa-align-left"></i> Buscar</button>
                </td> 
            </tr>
            <tr style="height: 40px">
                <td>
                    <!-- Filtro:   -->
                </td>
                <td style="text-align: left">
                    <!-- 
                    <input type="radio" name="optalumno" class="clsoptalumno" id="opt1" checked="checked" value="1" />&nbsp;Por Alumno&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="optalumno" class="clsoptalumno" id="opt2" value="2" />&nbsp;Por Salon
                    -->
                    <!-- 
                      <input type="text" name="txtdesde" id="txtdesde" style="width: 100px"  size="12" />&nbsp;&nbsp; Hasta :&nbsp;&nbsp; 
                     <input type="text" name="txthasta" id="txthasta"   style="width: 100px" size="12" />
                    -->
                </td>

                <td> &nbsp; Tipo : </td>
                <td>
                    <select name="cbtipo" id="cbtipo"  style="width: 100%"  class="form-control input-sm">
                        <option value="0">::::::: Todos :::::::</option>
                        <option value="I" selected="selected">Ingreso</option>
                        <option value="S">Salida</option>
                    </select>
                </td>
                <td>&nbsp; Mes : </td>       
                <td>
                    <select name="cbmes" id="cbmes"  style="width: 100%"  class="form-control input-sm">
                        <option value="0">::::::: Seleccione :::::::</option>
                        <option value="01" <?= (date ("m") == '01') ? 'selected="selected"' : '' ?>>- ENERO - </option>
                        <option value="02" <?= (date ("m") == '02') ? 'selected="selected"' : '' ?>>- FEBRERO - </option>
                        <option value="03" <?= (date ("m") == '03') ? 'selected="selected"' : '' ?>>- MARZO - </option>
                        <option value="04" <?= (date ("m") == '04') ? 'selected="selected"' : '' ?>>- ABRIL - </option>
                        <option value="05" <?= (date ("m") == '05') ? 'selected="selected"' : '' ?>>- MAYO - </option>
                        <option value="06" <?= (date ("m") == '06') ? 'selected="selected"' : '' ?>>- JUNIO - </option>
                        <option value="07" <?= (date ("m") == '07') ? 'selected="selected"' : '' ?>>- JULIO - </option>
                        <option value="08" <?= (date ("m") == '08') ? 'selected="selected"' : '' ?>>- AGOSTO - </option>
                        <option value="09" <?= (date ("m") == '09') ? 'selected="selected"' : '' ?>>- SETIEMBRE - </option>
                        <option value="10" <?= (date ("m") == '10') ? 'selected="selected"' : '' ?>>- OCTUBRE - </option>
                        <option value="11" <?= (date ("m") == '11') ? 'selected="selected"' : '' ?>>- NOVIEMBRE - </option>
                        <option value="12" <?= (date ("m") == '12') ? 'selected="selected"' : '' ?>>- DICIEMBRE - </option>
                    </select>
                </td>
                <td>&nbsp;<button type="submit"   id="btnPrint" class="btn btn-success"><i class="fa fa-align-left"></i> Imprime</button></td>
            </tr>
        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
<div id="divTblAsistencia">
<table class="table table-striped table-bordered"    id="viewListado" style="width: 100%">
    <thead>
        <tr class="tableheader">
            <th style="width: 10%;text-align: center">Codigo</th>
            <th style="width: 10%;text-align: center">Fecha</th>
            <th style="width: 10%;text-align: center">Hora</th>
            <th style="width: 10%;text-align: center">Asis.</th>
            <th style="width: 10%;text-align: center">Evento</th>
            <th style="width: 45%;text-align: center">Observacion</th>
            <th style="width: 5%;text-align: center">Conf.</th>
        </tr>
    <thead>
<tbody>
</tbody>        
</table>
</div>
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                Hello world!
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer"><button type="button" class="btn btn-primary">OK</button></div>
        </div>
    </div>
</div>


<div id="ModalObs" class="modal fade">
    <div class="modal-dialog">   
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Registrar Observacion</h3>
            </div>
            <div class="modal-body">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" name="acc" id="acc" value="" />
                    <input type="hidden" name="fecha" id="fecha" value="" />
                    <input type="hidden" name="alucod" id="alucod" value="" />
                    <input type="hidden" name="idreg" id="idreg" value="" />
                    <input type="hidden" name="evento" id="evento" value="" />
                    <table border="0" width="550px">
                        <tr><td width="350px">
                                <table border="0" width="100%">            
                                    <?php $i = 1;
                                    foreach ($lstObservacion as $objObs) {
                                        ?>  
                                        <tr>        
                                            <td width="15%"><input type="checkbox" <?= (($objObs->id_conducta == '09') ? 'onclick="activaChk(this.checked)"' : '') ?> name="chkConducta[]" id="chkOpcion<?= $i ?>" value="<?= $objObs->id_conducta; ?>" /></td>
                                            <td width="85%">
                                                <?php
                                                echo $objObs->id_conducta . " | ";
                                                if ($objObs->opc == '1')
                                                    echo $objObs->dsc_conducta;
                                                ?>
                                            </td>  
                                        </tr>                               
    <?php $i++;
} ?>     
                                    <tr>
                                        <td width="15%">&nbsp;</td>
                                        <td width="85%">
                                            <textarea id="txtotros" name="txtotros" disabled="" placeholder="Otros" value="" cols="5" rows="2"  style="width:100%" > </textarea>
                                        </td>
                                    </tr>
                                </table>      
                            </td>        
                            <!--
                            <td width="200px">
                                <textarea name="txtobservacion" id="txtobservacion" style="width:100%" cols="5" rows="4" placeholder="Observacion adicional.."> </textarea>
                            </td>
                            -->
                        </tr>
                    </table>
                </form>           
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    /*    $("#myModal").on("show", function() {    // wire up the OK button to dismiss the modal when shown
     $("#myModal a.btn").on("click", function(e) {
     console.log("button pressed");   // just as an example...
     $("#myModal").modal('hide');     // dismiss the dialog
     });
     });
     $("#myModal").on("hide", function() {    // remove the event listeners when the dialog is dismissed
     $("#myModal a.btn").off("click");
     });
     
     $("#myModal").on("hidden", function() {  // remove the actual elements from the DOM when fully hidden
     $("#myModal").remove();
     });
     
     $("#myModal").modal({                    // wire up the actual modal functionality and show the dialog
     "backdrop"  : "static",
     "keyboard"  : true,
     "show"      : true                     // ensure the modal is shown immediately
     });*/
</script>

<script type="text/javascript">
    $(function () {
        // $("#txtdesde").datepicker();
        //$("#txthasta").datepicker();
    });
</script> 