<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h2 class="page-header"><span class="glyphicon glyphicon-list"></span> Ver Pagos Realizados</h2>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmAsistencia" action=""  method="post" target="_blank" >
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <input type="hidden" name="htxtsalon" id="htxtsalon" value="" />
        <input type="hidden" name="htxtalumno" id="htxtalumno" value="" />
        <input type="hidden" name="htxtnumrecibo" id="htxtnumrecibo" value="" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">

                <td style ="width: 30%"><b>&nbsp;Seleccionar Hijo : </b> </td>
                <td style ="width: 50%" colspan="3">
                    <select name="cbalumno" id="cbalumno" style="width: 100%" onchange="cambiaHijo(this.value);" style="background-color:#F2F5A9" class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::::::: Seleccione Hijo ::::::::::::::::::::::::::::</option>
                        <?php foreach ($dataHijos as $row) : ?>
                            <option value="<?= $row->nemo . '|' . $row->alucod . '|' . $row->idalumno ?>"><?= $row->alucod . ' | ' . $row->nombres . ' | ' . $row->nemodes ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>           
                <td style ="width: 20%">
                    &nbsp;<button type="button"   id="btnBuscar" class="btn btn-primary"><i class="fa fa-align-left"></i> Ver Pagos</button>
                    &nbsp;<button type="button"   id="btnBoletas" class="btn btn-info"><i class="fa fa-align-left"></i> Ver Boletas</button>
                </td> 
            </tr>
        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
<table class="table table-bordered table-striped"    id="viewListado" style="width: 100%">
    <thead>
    <th style="width: 10%;text-align: center">Item</th>     
     <th style="width: 45%;text-align: center">Concepto de Pago</th>         
    <th style="width: 10%;text-align: center">Pendiente</th>    
    <th style="width: 10%;text-align: center">Pagado</th>
    <th style="width: 15%;text-align: center">Fecha Pago</th>       
    <th style="width: 10%;text-align: center">Estado</th>    
    </thead>
    <tbody>
        <tr>
            <td colspan=6><center>No Hay Informaci&oacute;n</center></td>
        </tr>
    </tbody>
</table>

<div id="ModalVerPagos" class="modal fade">
    <div class="modal-dialog modal-lg">   
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Comprobantes de Pago</h3>
            </div>
            <div class="modal-body">
                <form action="#" id="form" class="form-horizontal">
                    <div id="divDetalleNotas">                         
                    <table class="table table-bordered table-striped"    id="viewResumen" style="width: 100%;">
                        <thead>
                        <th style="width: 20%;text-align: center">Recibo</th>
                        <th style="width: 50%;text-align: center">Fecha / Hora</th>
                        <th style="width: 20%;text-align: center">Monto</th>
                        <th style="width: 10%;text-align: center">Opc.</th>
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
