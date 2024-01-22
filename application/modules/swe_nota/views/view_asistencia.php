<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;
         
    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>


<?php
    //$block = (($this->session->userdata ('USUCOD')=="FM0146")?"none":"block");
    $block = 0; //(($this->session->userdata ('USUCOD')=="FM0146")?1:0);
?>

<h2 class="page-header"><span class="glyphicon glyphicon-list"></span> Ver Notas</h2>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmAsistencia" action="<?= BASE_URL ?>swe_asistencia/generaReporte" onsubmit="return validaPost();" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?=$this->token?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
     
                <td style ="width: 30%"><b>&nbsp;Seleccionar Hijo : </b> </td>
                <td style ="width: 50%" colspan="3">
                    <select name="cbalumno" id="cbalumno" style="width: 100%" style="background-color:#F2F5A9" class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::::::: Seleccione Hijo ::::::::::::::::::::::::::::</option>
                        <?php foreach($dataHijos as $row) : ?>
                            <option value="<?=$row->nemo.'|'.$row->alucod.'|'.$row->idalumno.'|'.$row->instrucod  ?>"><?=$row->alucod.' | '.$row->nombres.' | '.$row->nemodes ?></option>
                        <?php endforeach;?>
                    </select>
                </td>           
                <td style ="width: 20%">
                    &nbsp;<button type="button"   id="btnBuscar" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Ver Boleta</button>
                </td> 
            </tr>
            <tr style="height: 40px; display: none;">
                <td><b> &nbsp; Bimestre / Unidad :</b> </td>
                <td>
                    <select name="cbbimestre" id="cbbimestre"  onchange="cargaUnidades(this.value)"  style="width: 100%"  class="form-control input-sm">
                        <option value="0">::::: Seleccione Bimestre :::::</option>
                        <option value="1" >&nbsp; - I Bimestre -  </option>
                        <option value="2">&nbsp; - II Bimestre - </option>
                        <option value="3">&nbsp; - III Bimestre - </option>
                        <option value="4">&nbsp; - IV Bimestre - </option>
                    </select>
                </td>
                <td>&nbsp;</td>       
                <td>
                    <select name="cbunidad" id="cbunidad"  style="width: 100%"  class="form-control input-sm">
                        <option value="0">::::: Seleccione Unidad :::::</option>
                    </select>
                </td>
                <td>
                   <?php if($block): ?>
                    <button type="button"  id="btnDetalles" class="btn btn-success">
                      <span class="glyphicon glyphicon-new-window"></span> Ver Resumen
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
<div id="divConducta" style="display:none;">
    <table style="width: 100%" border="0" >
        <tr>
            <td align="right" width="60%" style="font-weight: bold;font-size: 16px;">NOTA CONDUCTA DE UNIDAD : &nbsp;</td>
            <td align="left" width="40%" id="divNota" style="font-weight: bold;font-size: 16px;"></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>    
    </table>
</div>     

<table class="table table-bordered table-striped"    id="viewListado" style="width: 100%;">
    <thead>
    <th style="width: 10%;text-align: center">C&oacute;digo</th>
    <th style="width: 40%;text-align: center">Descripci&oacute;n Profesor(a)</th>
    <th style="width: 40%;text-align: center">Descripci&oacute;n Curso</th>
    <th style="width: 10%;text-align: center">Prom.</th>
   <!-- <th style="width: 10%;text-align: center">Cond.</th>-->
    <thead>

    <tbody>
        <tr>
            <td colspan=4><center>No Hay Informaci&oacute;n</center></td>
</tr>

</tbody>
</table>

<div id="ModalAlert" class="modal fade">
    <div class="modal-dialog">   
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Aviso</h3>
            </div>
            <div class="modal-body">
                <form action="#" id="form" class="form-horizontal">
                    <div>
                        <b>Estimado Padre de Familia,</b><br>
                        Por favor regularizar su pago del mes de <label id="divMes" style="font-weight: bold;text-transform: uppercase;"></label></b> para que pueda visualizar sus Notas. <br><br>
                    <b>COLEGIO MARIANISTA</b>
                    </div>                    
                </form>           
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div id="ModalVerDetalleNotas" class="modal fade">
    <div class="modal-dialog" style="width:600px">   
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Resumen del Bimestre I</h3>
            </div>
            <div class="modal-body">
                <form action="#" id="form" class="form-horizontal">
                    <div id="divDetalleNotas">                         
                    <table class="table table-bordered table-striped"    id="viewResumen" style="width: 100%;">
                        <thead>
                        <th style="width: 10%;text-align: center">C&oacute;digo</th>
                        <th style="width: 60%;text-align: center">Descripci&oacute;n Curso</th>
                        <th style="width: 10%;text-align: center">I U</th>
                         <th style="width: 10%;text-align: center">II U</th>
                        <th style="width: 10%;text-align: center">Prom.</th>
                        <thead>
                        <tbody>
                        </tbody>
                    </table>                        
                    </div>                    
                </form>           
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
