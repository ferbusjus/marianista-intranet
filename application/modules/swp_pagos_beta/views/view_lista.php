<style  type="text/css">
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;
    }
    /* ================= Para Datatable ===================*/
    th.dt-center, td.dt-center { text-align: center; /*vertical-align: middle;*/ }    
    th.dt-center, td.dt-right { text-align: right; } 
    th.dt-center, td.dt-left { text-align: left; } 
    td.dt-fecha { text-align: center;  font-size: 12px ;font-weight: bold; } 
    /* ===================================================*/

    .datepicker{z-index:9999 !important}
    .ui-datepicker { position: relative; z-index: 10000 !important; }
</style>

<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Pago por Caja</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <form  id="formPrincipal" action=""  method="POST"  target="_blank" >
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <input type="hidden" name="hidnemo" id="hidnemo" value="" />
        <input type="hidden" name="htxtalumno" id="htxtalumno" value="" />
        <input type="hidden" name="htxtsalon" id="htxtsalon" value="" />        
        <table style="text-align:center;width: 100%" border="0">
            <tr style="height: 50px">
                <td style ="width: 10%;float: center"><b>Alumno:</b> </td>
                <td  style ="width: 60%;">                    
                    <input type="text" class="form-control"  id="txtAlumnoSearch" name="txtAlumnoSearch" placeholder="Escriba Apellido del Alumno" />                    
                </td>             
                <td  style ="width: 8%;text-align:center;">
                    &nbsp;   <button type="button"   id="btnReset" name="btnReset" data-toggle="tooltip" title="Buscar"  class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i> 
                </td>                
                <td style ="width: 23%;">
                    <button type="button"   id="btnBuscar" name="btnBuscar" onclick="js_verPagos();" class="btn btn-primary"><i class="glyphicon glyphicon-refresh" /></i> Consultar
                </td>
            </tr>

            <!--
            <tr style="height: 40px">               
                <td style ="width: 5%;float: center"><b>Aula:</b> </td>
                <td style ="width: 30%">
                    <select name="cbsalon" id="cbsalon" style="width: 98%" class="form-control input-sm">
                        <option value="0">:::::::::::::::: Seleccione Salon  ::::::::::::::::</option>
            <?php //foreach ($dataSalones as $salon) : ?>
                            <option value="<?php //echo $salon->NEMO     ?>"><?php //echo $salon->NEMO . " - " . $salon->NEMODES     ?></option>
            <?php //endforeach; ?>
                    </select>
                </td>   
                <td style ="width: 5%;text-align: center"><b>Alumno:</b></td>
                <td style ="width: 45%">
                    <select name="cbalumno" id="cbalumno" style="width: 98%" class="form-control input-sm">
                        <option value="0">:::::::::::::::: Seleccione Alumno ::::::::::::::::</option>
                    </select>
                </td>        
                <td style ="width: 15%;">
                    <button type="button"   id="btnBuscar" name="btnBuscar" onclick="js_verPagos();" class="btn btn-primary"><i class="glyphicon glyphicon-refresh" /></i> Consultar
                </td>
            </tr>
            -->

            <tr style="height: 40px">
                <td colspan="4" style="text-align:center;" >
                    &nbsp;<button type="button"   id="btnPagar" name="btnPagar" onclick="js_registrarPago();" class="btn btn-danger"><i class="glyphicon glyphicon-share" /></i> Registrar Pago                
                        &nbsp;<button type="button"   id="btnImprimir" name="btnImprimir" onclick="js_imprimir();" class="btn btn-primary"><i class="glyphicon glyphicon-print" /></i> Imprimir EE.CC  
                            &nbsp;<button type="button"   id="btnConcepto" name="btnConcepto" onclick="js_concepto();" class="btn btn-primary"><i class="glyphicon glyphicon-share" /></i> Agregar Concepto  
                                </td>
                                </tr>
                                </table>
                                </form>
                                </center>
                                <br>
                                <hr/>
                                <br>
                                <div id="divTblPagos">
                                    <table class="table table-striped table-bordered"    id="viewPagos" style="width: 100%">
                                        <thead>
                                            <tr class="tableheader">
                                                <th style="width: 5%;text-align: center"><center><input type="checkbox" id="checkall" class="select-all" disabled="" /></center></th>
                                        <th style="width: 10%;text-align: center">Estado</th>
                                        <th style="width: 10%;text-align: center">Fec-Ven</th>
                                        <th style="width: 35%;text-align: center">Concepto de Pago</th>
                                        <th style="width: 10%;text-align: center">Fec-Reg</th>
                                        <th style="width: 10%;text-align: center">Pendiente</th>
                                        <th style="width: 5%;text-align: center">Mora</th>
                                        <th style="width: 10%;text-align: center">Total</th>    
                                        <th style="width: 5%;text-align: center">Conf.</th>  
                                        </tr>
                                        <thead>
                                        <tbody>
                                        </tbody>        
                                    </table>
                                </div>
                                <br/>
                                <!-- =========================== BLOQUE DE MODALS ======================================= -->
                                <!-- 1. Modal para mostrar los Pagos -->
                                <div class="modal fade" id="modal_form" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="width: 700px">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">Titulo</h4>
                                            </div>
                                            <div class="modal-body form">
                                                <form action="#" id="form" class="form-horizontal">
                                                    <!-- :::::::::::::: BLOQUE DE INPUT OCULTOS :::::::::::::: -->
                                                    <input type="hidden" value="" name="txtalucod" id="txtalucod"/>
                                                    <input type="hidden" value="" name="txtmescodId" id="txtmescodId"/>
                                                    <input type="hidden" value="" name="txtconcodId" id="txtconcodId"/>
                                                  <!--  <input type="hidden" value="0" name="txttotal" id="txttotal"/>-->
                                                    <input type="hidden" value="0" name="txttemp" id="txttemp"/>
                                                    <!-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                                                    <div class="form-body">
                                                        <div class="alert alert-info">
                                                            <p id="pAlumno"> </p>                   
                                                        </div>      



                                                        <div class="form-group">
                                                            <div class="col-md-6 ">           
                                                                <div class="custom-control custom-radio custom-control-inline">                                                                    
                                                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions1" value="01" checked="">
                                                                    <label class="form-check-label" for="inlineRadio1">Recibo</label>

                                                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions2" value="02">
                                                                    <label class="form-check-label" for="inlineRadio1">Boleta</label>           

                                                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions3" value="03">
                                                                    <label class="form-check-label" for="inlineRadio1">Factura</label>       
                                                                </div>                                                                  
                                                            </div>        

                                                            <label class="col-md-2 control-label"><div id="divlblComprobante">RECIBO : </div></label>
                                                            <div class="col-md-4 ">          
                                                                <div class="form-group">
                                                                    <div class="col-md-5 "> 
                                                                        <input type="text" id="txtserie" value="R001" disabled=""  name="txtserie" class="form-control"  maxlength="4" placeholder="001">    
                                                                    </div>
                                                                    <div class="col-md-7"> 
                                                                        <input type="text" id="txtnumrecibo"   name="txtnumrecibo" class="form-control input-number"  maxlength="6" placeholder="000001"> 
                                                                    </div>                
                                                                </div>
                                                            </div>        


                                                        </div>
                                                        <hr/><br/>                     
                                                        <!--
                                                        <div class="form-group">
                                                            <div class="col-md-6">
                                                                <div class="custom-control custom-radio custom-control-inline">
                                                                    
                                                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions1" value="01" checked="">
                                                                  <label class="form-check-label" for="inlineRadio1">Recibo</label>
                                                                  
                                                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions2" value="02">
                                                                  <label class="form-check-label" for="inlineRadio1">Boleta</label>           
                                                                  
                                                                  <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions3" value="03">
                                                                  <label class="form-check-label" for="inlineRadio1">Factura</label>       
                                                                  
                                                                </div>
                                                            
                                                            </div>
                                                            <label class="col-md-2 control-label"><div id="divlblComprobante">RECIBO : </div></label>
                                                            <div class="col-md-2">   
                                                                        <input type="text" id="txtserie"   name="txtserie" class="form-control"  maxlength="4" placeholder="0001">    
                                                               </div>
                                                            <div class="col-md-2">   
                                                                        <input type="text" id="txtnumrecibo"   name="txtnumrecibo" class="form-control"  maxlength="5" placeholder="99999">                                                      
                                                            </div>
                                                        </div>                                                        
                                                        -->
                                                        <div class="form-group">
                                                            <label class="control-label col-md-2">Documento:</label>
                                                            <div class="col-md-2">
                                                                <select name="cbdocumento" id="cbdocumento"   class="form-control">                                
                                                                    <option value="01" selected="selected">DNI</option>
                                                                    <option value="02">RUC</option>
                                                                </select>      
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" id="txtdocumento"   name="txtdocumento" value="" maxlength="8" class="form-control input-number" placeholder="Ingrese DNI" >
                                                            </div>
                                                            <div class="col-md-1">
                                                                <button type="button"  id="btnFiltrar" data-toggle="tooltip" title="Buscar"  class="btn btn-primary"><i class="glyphicon glyphicon-search"></i></button>                                                                 
                                                            </div>
                                                            <label class="control-label col-md-1">FECHA:</label>
                                                            <div class="col-md-3">
                                                                <div class='input-group date'>
                                                                    <span class="input-group-addon">
                                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                                    </span> 
                                                                    <input type='text' name="txtfecha" id="txtfecha" class="form-control calendario"  readonly="" value="<?php echo date('Y-m-d'); ?>" placeholder="Fecha de Inicio" data-date-format="yyyy-mm-dd" required="" />
                                                                </div>                                
                                                            </div>
                                                        </div> 

                                                        <div class="form-group">
                                                            <label class="control-label col-md-2">Cliente:</label>
                                                            <div class="col-md-10">
                                                                <input type="text" id="txtcliente"   name="txtcliente" value="" maxlength="60" class="form-control" placeholder="Ingrese Nombre del Cliente" >
                                                            </div>
                                                        </div>                                                         
                                                        <div class="form-group">
                                                            <label class="control-label col-md-2">Direccion:</label>
                                                            <div class="col-md-10">
                                                                <input type="text" id="txtdireccion"   name="txtdireccion" value="" maxlength="100" class="form-control" placeholder="Ingrese Direccion" >
                                                            </div>
                                                        </div> 

                                                        <!--
                                                        <div class="form-group">
                                                            <label class="control-label col-md-2">Observacion:</label>
                                                            <div class="col-md-6">
                                                                <textarea rows="3"  name="txtobs"  id="txtobs" class="form-control" placeholder="Escribe aquí una observación"></textarea>
                                                            </div>
                                                            <label class="control-label col-md-2">Subtotal</label>
                                                            <div class="col-md-2">
                                                                <input type="text" id="txtsubtotal"   name="txtsubtotal"  disabled="disabled" class="form-control" >
                                                            </div>                                                            
                                                        </div> 
                                                        -->
                                                        <hr/><br/> 

                                                        <div class="form-group">                                                        
                                                            <label class="control-label col-md-2 ">Subtotal</label>
                                                            <div class="col-md-2">
                                                                <input type="text" id="txtsubtotal"   name="txtsubtotal"  disabled="disabled" class="form-control" >
                                                            </div>     
                                                            <label class="control-label col-md-2 ">IGV (18%)</label>
                                                            <div class="col-md-2">
                                                                <input type="text" id="txtigvl"   name="txtigvl"  disabled="disabled" class="form-control" >
                                                            </div>                                                                        
                                                            <label class="control-label col-md-2 ">Total</label>
                                                            <div class="col-md-2">
                                                                <input type="text" id="txtstotal"   name="txtstotal"  disabled="disabled" class="form-control" >
                                                            </div>                                                                
                                                        </div> 


                                                        <!--
                                                        <div class="form-group">
                                                            <div class="col-md-6">
                                                            </div>
                                                            <label class="control-label col-md-2">TOTAL : </label>
                                                            <div class="col-md-4">
                                                                <h2> <span class="label label-warning" id="lblTotal"></span></h2>
                                                                <input type="hidden" id="txttotal"   name="txttotal" >
                                                            </div>
                                                        </div>
                                                        -->
                                                        
                                                         <hr/><br/> 
                                                         
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">   
                                                <button type="button" id="btnSave"  name="btnSave" onclick="js_save();" class="btn btn-primary"><i class="glyphicon glyphicon-share" ></i>&nbsp;Pagar
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove" ></i>&nbsp;Cerrar
                                                        </div>
                                                        </div><!-- /.modal-content -->    
                                                        </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                        <!-- End  modal 1 -->

                                                        <!-- 2. Modal para Agregar Conceptos de Pago -->
                                                        <div class="modal fade" id="modal_conceptos" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content" style="width: 600px">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title">Titulo</h4>
                                                                    </div>
                                                                    <div class="modal-body form">
                                                                        <form action="#" id="form2" class="form-horizontal">
                                                                            <!-- :::::::::::::: BLOQUE DE INPUT OCULTOS :::::::::::::: -->
                                                                            <input type="hidden" value="" name="txtidAlumno" id="txtidAlumno"/>
                                                                            <!-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                                                                            <div class="form-body">
                                                                                <div class="alert alert-info">
                                                                                    <p id="infoAlumno"> </p>                   
                                                                                </div>      

                                                                                <div class="form-group">
                                                                                    <label for="apellidos" class="col-lg-3 control-label">Concepto:</label>
                                                                                    <div class="col-lg-6">
                                                                                        <select name="cbconcepto" id="cbconcepto" class="form-control">
                                                                                            <option value='0'>:::::::::::: Seleccione ::::::::::::</option>
                                                                                        </select>
                                                                                    </div>                              
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="apellidos" class="col-lg-3 control-label">Recibo:</label>
                                                                                    <div class="col-lg-3">
                                                                                        <input type="text" value="" name="txtrecibo" class="form-control"  maxlength="8" id="txtrecibo"/>
                                                                                    </div>                              
                                                                                </div>                        

                                                                                <div class="form-group">
                                                                                    <label for="Monto" class="col-lg-3 control-label">Monto:</label>
                                                                                    <div class="col-lg-3">
                                                                                        <input type="text" value="" name="txtmontoConcepto" class="form-control" onkeypress="return NumCheck(event, this);" maxlength="6" id="txtmontoConcepto"/>
                                                                                    </div>                              
                                                                                </div>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" id="btnSaveConcepto" onclick="js_save_concepto()" class="btn btn-primary">Grabar</button>
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                                                    </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                        <!-- ========================= FIN BLOQUE DE MODALS ====================================== -->