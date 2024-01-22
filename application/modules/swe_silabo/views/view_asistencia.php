<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;
         
    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h2 class="page-header"><span class="glyphicon glyphicon-list"></span> Ver Silabos</h2>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmAsistencia" action="<?= BASE_URL ?>swe_asistencia/generaReporte" onsubmit="return validaPost();" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?=$this->token?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
     
                <td style ="width: 30%"><b>&nbsp;Seleccionar Hijo : </b> </td>
                <td style ="width: 50%" colspan="3">
                    <select name="cbalumno" id="cbalumno" style="width: 100%"  class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::::::: Seleccione Hijo ::::::::::::::::::::::::::::</option>
                        <?php foreach($dataHijos as $row) : ?>
                            <option value="<?=$row->nemo.'|'.$row->alucod ?>"><?=$row->alucod.' | '.$row->nombres.' | '.$row->nemodes ?></option>
                        <?php endforeach;?>
                    </select>
                </td>           
                <td style ="width: 20%">
                    &nbsp;<button type="button"   id="btnBuscar" class="btn btn-primary"><i class="fa fa-align-left"></i> Mostrar</button>
                </td> 
            </tr>
            <tr style="height: 40px">
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
                <td><b>&nbsp; / &nbsp;</b></td>       
                <td>
                    <select name="cbunidad" id="cbunidad"  style="width: 100%"  class="form-control input-sm">
                        <option value="0">::::: Seleccione Unidad :::::</option>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
<table class="table table-bordered table-striped"  id="viewListado" style="width: 100%">
    <thead>
    <th style="width: 2%;text-align: center">Cod.</th>
    <th style="width: 30%;text-align: center">Curso</th>
    <th style="width: 50%;text-align: center">Profesor(a)</th>
    <th style="width: 18%;text-align: center">Silabo</th>
    <thead>

    <tbody>
        <tr>
            <td colspan=8><center>No Hay Informaci&oacute;n</center></td>
</tr>
</tbody>
</table>



<div id="ModalVerDetalle" class="modal fade">
    <div class="modal-dialog" style="width:950px">   
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle del S&iacute;labo</h3>
            </div>
            <div class="modal-body">
                <form action="#" id="form" class="form-horizontal">
                    <div id="divcontenido">                       
                    </div>                    
                </form>           
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>