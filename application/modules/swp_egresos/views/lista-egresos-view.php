<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;
    }
    /* ================= Para Datatable ===================*/
    th.dt-center, td.dt-center { text-align: center; }    
    th.dt-center, td.dt-right { text-align: right; } 
    th.dt-center, td.dt-left { text-align: left; } 
    /* ===================================================*/
    #fade {
        display: none;
        position:absolute;
        top: 0%;
        left: 0%;
        width: 100%;
        height: 100%;
        background-color: #ababab;
        z-index: 1001;
        -moz-opacity: 0.8;
        opacity: .70;
        filter: alpha(opacity=80);
    }

    #modal {
        display: none;
        position: absolute;
        top: 45%;
        left: 45%;
        width: 200px;
        height: 200px;
        padding:30px 15px 0px;
        border: 3px solid #ababab;
        box-shadow:1px 1px 10px #ababab;
        border-radius:20px;
        background-color: white;
        z-index: 1002;
        text-align:center;
        overflow: auto;
    }    
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Registro de Gastos</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <form  id="formPrincipal" action=""  method="POST"  target="_blank" >
        <table style="text-align:center;width: 100%" border="0">
            <tr style="height: 40px">               
                <td style ="width: 10%;float: center"><b>Razón Social :</b> </td>
                <td style ="width: 15%">
                    <select name="idrazon" id="idrazon" class="form-control">
                        <option value="">::::::::::: Todos ::::::::::</option>
                        <?php foreach ($razon as $row) { ?>
                            <option value="<?= $row->id_razon ?>"><?= $row->id_razon . " : " . strtoupper ($row->razon_social) ?></option>
                        <?php } ?>
                    </select>
                </td>   
                <td style ="width: 10%;float: center"><b>Responsable :</b> </td>
                <td style ="width: 15%">
                    <select name="idresp" id="idresp" class="form-control">
                        <option value="">::::::::::: Todos ::::::::::</option>
                        <?php foreach ($responsable as $row) { ?>
                            <option value="<?= $row->id_responsable ?>"><?= $row->id_responsable . " : " . strtoupper ($row->responsable) ?></option>
                        <?php } ?>
                    </select>
                </td>                   
                <td style ="width: 15%;text-align: center"><b>Tipo Comprobante :</b> </td>
                <td style ="width: 15%">
                    <select name="idcomp" id="idcomp" class="form-control">
                        <option value="">::::::::::: Todos ::::::::::</option>
                        <?php foreach ($comprobantes as $row) { ?>
                            <option value="<?= $row->idcomprobante ?>"><?= $row->idcomprobante . " : " . strtoupper ($row->descripcion) ?></option>
                        <?php } ?>
                    </select>                    
                </td>        
                <td style ="width: 20%;text-align: right;">
                    <button type="button"   id="btnRefresh" name="btnRefresh"  class="btn btn-danger"><i class="glyphicon glyphicon-refresh" /></i>Ver Todos&nbsp;
                    <button type="button"   id="btnBuscar" name="btnBuscar" onclick="javascript:js_addEgreso(1, 'AGREGAR GASTO');" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign" /></i> Registrar
                </td>
            </tr>

        </table>
    </form>
</center>
<br>
<hr/>
<br>
<div id="divTblPagos">
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="0" id="chkMostrarEliminados">
  <label class="form-check-label" for="defaultCheck1">
    Mostrar registros eliminados
  </label>
</div>
    <table class="table table-striped table-bordered"    id="viewEgresos" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">ID</th>
                <th style="width: 18%;text-align: center">Razón</th>
                <th style="width: 16%;text-align: center">Responsable </th>
                <th style="width: 9%;text-align: center">T-Comp.</th>                
                <th style="width: 20%;text-align: center">Descripcion</th>                 
                <th style="width: 5%;text-align: center">Monto</th> 
                <th style="width: 5%;text-align: center">Archivo</th> 
					  <th style="width: 5%;text-align: center">Usu. Reg.</th> 
                <th style="width: 5%;text-align: center">Fec. Reg.</th> 
					  <th style="width: 7%;text-align: center">Estado</th> 
                <th style="width: 5%;text-align: center">Conf</th>
            </tr>
        <thead>
        <tbody>
        </tbody>        
    </table>
</div>
<br/>
<div id="fade"></div>
<div id="modal">
    <img id="loader" src="<?= BASE_URL ?>/images/waiting.gif" width="150px" height="150px" />
</div>
<!-- 1. Modal para Agregar Conceptos de Pago Adicionales -->
<div class="modal fade" id="modal_egresos" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
         <form  id="form2" class="form-horizontal" >
        <input type="hidden" value="" name="id"  id="id" />
         <iframe style="display: none;" name="iframeUpload"></iframe>   
            <div class="modal-header">
               <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title">Titulo</h4>
            </div>

            <div class="modal-body">
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Razón Social :</label>
                            <div class="col-lg-10">
                                <select name="id_razon" id="id_razon" class="form-control" required=""  >
                                    <option value=''>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>     
                        </div>                              

                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Responsable :</label>
                            <div class="col-lg-10">
                                <select name="id_responsable" id="id_responsable" class="form-control" required >
                                    <option value=''>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>     
                        </div>          
                        
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Tipo de Gasto :</label>
                            <div class="col-lg-10">
                                <select name="id_tipo_gasto" id="id_tipo_gasto" class="form-control" required>
                                    <option value=''>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>     
                        </div>     
                        
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Caja :</label>
                            <div class="col-lg-10">
                                <select name="id_caja" id="id_caja" class="form-control" required>
                                    <option value=''>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>     
                        </div>    
                
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Inputación Gasto:</label>
                            <div class="col-lg-10">
                                <select name="id_inputacion" id="id_inputacion" class="form-control" required>
                                    <option value=''>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>     
                        </div>         
                        
                        <div class="form-group">                 
                            <label for="ruc" class="col-lg-2 control-label">                                              
                                <input type="checkbox" id="chkRuc" name="flg_ruc" value="0">&nbsp;RUC:</label>
                            <div class="col-lg-3">
                                <input type="text" value="" name="ruc_proveedor" class="form-control" onkeypress="return validaNumeros(event, this);"  maxlength="11" id="ruc_proveedor" placeholder="RUC" />
                            </div>      
                            <label for="ruc" class="col-lg-2 control-label">Razón Social:</label>
                            <div class="col-lg-5">
                                <input type="text" value="" name="proveedor" class="form-control"  maxlength="100" id="proveedor"  placeholder="PROVEEDOR"/>
                            </div>                                
                        </div>
                        
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Fecha gasto:</label>
                            <div class="col-lg-3">
                                <div class='input-group date'>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span> 
                                    <input type='text' name="fecha_gasto" id="fecha_gasto" class="form-control calendario"   value="<?php echo date ('Y-m-d'); ?>" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" required />
                                </div>
                            </div>     
                            <label for="apellidos" class="col-lg-2 control-label">Fecha pago:</label>
                            <div class="col-lg-3">
                                <div class='input-group date'>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span> 
                                    <input type='text' name="fecha_pago" id="fecha_pago" class="form-control calendario"   value="" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" />
                                </div>
                            </div>   
                        </div>  
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Tipo Comp.:</label>
                            <div class="col-lg-10">
                                <select name="id_comprobante" id="id_comprobante" class="form-control" required>
                                    <option value=''>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div> 						
								 </div>
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">N° Comprobante:</label>
                            <div class="col-lg-3">
                                <input type="text" value="" name="num_comprobante" class="form-control"  maxlength="15" id="num_comprobante" placeholder="COMPROBANTE" />
                            </div>                                 
                            <label for="Monto" class="col-lg-2 control-label">Monto:</label>
                            <div class="col-lg-3">
                                <input type="text" value="" name="monto" class="form-control" maxlength="10" id="monto" placeholder="#.##0.00" required=""/>
                            </div>                              
                        </div>
                        
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Descripción:</label>
                            <div class="col-lg-10">
                                <input type="text" value="" name="descripcion" class="form-control"  maxlength="250" id="descripcion" placeholder="Descripción" required=""/>
                            </div>                                         
                        </div>
                            <div class="form-group" id="divImagenes">
                                <label for="apellidoss" class="col-lg-2 control-label">&nbsp;</label>
                                <div class="col-lg-5" style="font-weight: bold" id="imagen1">
                                </div>                            
                                <div class="col-lg-5" style="font-weight: bold" id="imagen2">
                                </div>                                            
                            </div>
                            <div class="form-group">
                                <label for="apellidos" class="col-lg-2 control-label">Archivos:</label>
                                <div class="col-lg-5">
                                    <input type="file" class="form-control-file" id="file1" name ="file[]">
                                </div>                            
                                <div class="col-lg-5">
                                    <input type="file" class="form-control-file" id="file2" name ="file[]">
                                </div>                                            
                            </div>
                         
                   
           
            </div>
           
            <div class="modal-footer">
                <button type="submit" id="btnSaveConcepto"  class="btn btn-primary">Grabar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>     
           </form>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->