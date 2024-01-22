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
    .negrita { font-weight: bold; font-size: 16px}
    .datepicker{z-index:9999 !important}
    .ui-datepicker { position: relative; z-index: 10000 !important; }
    .nomargin {
        padding: 5px 3px 5px 3px !important;
    }
    .nav-tabs .badge{
        position: absolute;
        top: -10px;
        right: -10px;
        background: red;
    }    
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
        <input type="hidden" name="user" id="user" value="<?= $usuario ?>" />
        <input type="hidden" name="hidnemo" id="hidnemo" value="" />
        <input type="hidden" name="htxtalumno" id="htxtalumno" value="" />
        <input type="hidden" name="htxtsalon" id="htxtsalon" value="" />        
        <input type="hidden" name="rbdTipo" id="rbdTipo" value="" />  
        <input type="hidden" name="htxtnumrecibo" id="htxtnumrecibo" value="" />          
        <input type="hidden" name="htxtflagdni" id="htxtflagdni" value="" />  
        <input type="hidden" name="htxtdni" id="htxtdni" value="" />  
        <input type="hidden" name="htxtpaterno" id="htxtpaterno" value="" />  
        <input type="hidden" name="htxtmaterno" id="htxtmaterno" value="" />  
        <input type="hidden" name="htxtnombres" id="htxtnombres" value="" />  
        <input type="hidden" name="htxtfamcod" id="htxtfamcod" value="" />  
        <table style="text-align:center;width: 100%" border="0">
            <tr style="height: 50px">
                <td style ="width: 10%;float: center"><b>Alumno:</b> </td>
                <td  style ="width: 60%;">                    
                    <input type="text" class="form-control"  id="txtAlumnoSearch" name="txtAlumnoSearch" placeholder="Escriba Apellido del Alumno" />                    
                </td>             
                <td  style ="width: 8%;text-align:center;">
                    &nbsp;   <button type="button"   id="btnReset" name="btnReset" data-toggle="tooltip" title="Buscar"  class="btn btn-primary"><i class="glyphicon glyphicon-refresh" /></i> 
                </td>                
                <td style ="width: 23%;">
                    <button type="button"   id="btnBuscar" name="btnBuscar" onclick="js_verPagos();" class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i> Consultar
                </td>
            </tr>

            <!--
            <tr style="height: 40px">               
                <td style ="width: 5%;float: center"><b>Aula:</b> </td>
                <td style ="width: 30%">
                    <select name="cbsalon" id="cbsalon" style="width: 98%" class="form-control input-sm">
                        <option value="0">:::::::::::::::: Seleccione Salon  ::::::::::::::::</option>
            <?php //foreach ($dataSalones as $salon) : ?>
                            <option value="<?php //echo $salon->NEMO                   ?>"><?php //echo $salon->NEMO . " - " . $salon->NEMODES                   ?></option>
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
                    <button type="button"   id="btnPagar" name="btnPagar" onclick="js_registrarPago();" class="btn btn-danger" style="margin: 5px" /><i class="glyphicon glyphicon-share" /></i> Registrar Pago                
                    <button type="button"   id="btnImprimir" name="btnImprimir" onclick="js_imprimir();" class="btn btn-primary" style="margin: 5px" /><i class="glyphicon glyphicon-print" /></i> Imprimir EE.CC  
                   <!-- <button type="button"   id="btnConcepto" name="btnConcepto" onclick="js_concepto();" class="btn btn-primary" style="margin: 5px" /><i class="glyphicon glyphicon-share" /></i> Agregar Concepto  -->
                    <button type="button"   id="btnBoletas" name="btnConcepto" onclick="js_verBoletas();" class="btn btn-danger" style="margin: 5px" /><i class="glyphicon glyphicon-share" /></i> Ver Boletas 
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
        <div class="modal-content" style="width: 750px">
            <!--
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title">Titulo</h4>
             </div>
            -->
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home"><b>PAGO DE PENSIÓN</b></a></li>
                        <li><a data-toggle="tab" href="#menu1"><b>DATOS DE FAMILIA</b><span class="badge">Nuevo</span></a></li> 
                           <!-- <li><a data-toggle="tab" href="#menu1"><b>COMPROBANTES</b><span class="badge">Nuevo</span></a></li>-->
                    </ul>                                                
                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <!-- :::::::::::::: BLOQUE DE INPUT OCULTOS :::::::::::::: -->
                            <input type="hidden" value="" name="txtalucod" id="txtalucod"/>
                            <input type="hidden" value="" name="txtmescodId" id="txtmescodId"/>
                            <input type="hidden" value="" name="txtconcodId" id="txtconcodId"/>
                          <!--  <input type="hidden" value="0" name="txttotal" id="txttotal"/>-->
                            <input type="hidden" value="0" name="txttemp" id="txttemp"/>
                            <input type="hidden" value="<?= $ano ?>" name="hanio" id="hanio"/>
                            <input type="hidden" value="" name="hfamcod" id="hfamcod"/>
                            <!-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div class="form-body">
                                <div class="alert alert-info">
                                    <p id="pAlumno"> </p>                   
                                </div>      

                                <div class="form-group">
                                    <div class="col-md-8 ">           
                                        <div class="custom-control custom-radio custom-control-inline">     
                                            <!--<div id="divrecibo">-->
                                            <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions1" value="01" >
                                            <label class="form-check-label" for="inlineRadio1">RECIBO</label>                                                                    
                                            <!--</div>-->
                                            <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions2" value="02" >
                                            <label class="form-check-label" for="inlineRadio1">BOLETA</label>           
                                            <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions3" value="03">
                                            <label class="form-check-label" for="inlineRadio1">FACTURA</label>       
                                        </div>                                                                  
                                    </div>       
                                    <label class="control-label col-md-1" id="divlblComprobante">&nbsp;</label>
                                    <div class="col-md-3">                               
                                        <input type="text" id="txtnumrecibo"  disabled=""  name="txtnumrecibo" class="form-control negrita "  maxlength="10" >
                                    </div>
                                </div> 
                                <div class="form-group">



                                    <div class="col-md-8">     
                                        <div class="form-group">
                                            <div class="col-md-3">     
                                                <input type="text" id="txtdni"  name="txtdni" class="form-control"  readonly="" maxlength="8" placeholder="DNI" >
                                            </div>
                                            <div class="col-md-9">   
                                                <input type="text" id="txtcliente"  disabled="" name="txtcliente" readonly="" class="form-control"  maxlength="10" placeholder="NOMBRE CLIENTE" >
                                            </div>
                                        </div>
                                    </div>                                                               

                                    <label class="control-label col-md-1"><!-- FECHA --> </label> <!--  col-sm-offset-8 -->
                                    <div class="col-md-3">
                                        <div class='input-group date'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span> 
                                            <input type='text' name="txtfecha" id="txtfecha" class="form-control calendario"  readonly="" value="<?php echo date('Y-m-d'); ?>" placeholder="Fecha de Inicio" data-date-format="yyyy-mm-dd" required="" />
                                        </div>                                
                                    </div>
                                </div> 

                                <hr><br>                                                      

                                <div class="form-group"> 
                                    <div class="col-md-7 ">
                                        <ul class="list-group" id="divUlPagos">                                                                          
                                        </ul>                                                                 
                                    </div>
                                    <label class="control-label col-md-2">TOTAL : </label>
                                    <div class="col-md-3">
                                        <h2> <span class="label label-warning" style="padding: .2em 1.5em .3em 1.5em" id="lblTotal" ></span></h2>
                                        <input type="text" id="txttotal"  value=""  name="txttotal" class="form-control" style="background-color: khaki;font-weight: bold;text-align: right; font-size: 18px;"  maxlength="3" >
                                    </div>
                                </div>
                                <div class="form-group">                                                          
                                    <div class="col-md-6 ">           
                                        <div class="custom-control custom-radio custom-control-inline">                                                                    
                                            <input class="form-check-input" type="checkbox" name="chkHabilita" id="chkHabilita" onclick="js_habilita(this.checked);" value="0" >
                                            <label class="form-check-label" for="inlineRadio1">Modificar Monto de la Pensión</label>
                                        </div>                                                                  
                                    </div>   
                                   <!-- Solo para Colegio que trabajan con Pagos por Caja y Banco -->
                                   <label class="control-label col-md-3">Modalidad : </label>
                                    <div class="col-md-3">
                                        <select name="cbtipo" id="cbtipo"  class="form-control input-sm" style="background-color: khaki" onchange="limpiaVoucher(this.value)">                                
                                            <option value="1" selected="selected">OFICINA</option>
                                            <option value="2">IZIPAY</option>
                                            <option value="3">TRANSFERENCIA - BCP</option>
                                            <option value="4">TRANSFERENCIA - BBVA</option>
                                        </select>           
                                    </div>
                                </div>
                                
                                <div class="form-group">    
                                    <div class="col-md-6 ">           
                                       &nbsp;
                                    </div>   
                                   <!-- Solo para Colegio que trabajan con Pagos por Caja y Banco -->
                                   <label class="control-label col-md-3">Voucher : </label>
                                    <div class="col-md-3">
                                        <input type="text" id="txtvoucher"  name="txtvoucher" class="form-control " placeholder="# de Voucher"  maxlength="20" >
                                    </div>
                                </div>                                
                                <!-- 
                                <div class="form-group">
                                     <label class="control-label col-md-2">Raz. Social: </label>
                                     <div class="col-md-7">
                                         <select name="cbruc" id="cbruc"  style="width: 100%"  class="form-control input-sm">                                
                                             <option value="01" selected="selected"> 10423973663 - COORPORACION MARIANISTA SAC </option>
                                             <option value="02">20556889237 - COLEGIO MARIANISTAS V.M.T SAC</option>
                                         </select>           
                                     </div>
                                 </div> 
                                -->
                            </div>
                        </div>
                        <div id="menu1" class="tab-pane fade">
                            <br>
                            <table class="table table-striped table-bordered"    id="viewPadres" style="width: 100%">
                                <thead>
                                    <tr class="tableheader">
                                        <th style="width: 4%;text-align: center">APO</th>
                                        <th style="width: 8%;text-align: center">Tipo</th>
                                        <th style="width: 16%;text-align: center">Ape. Paterno</th>
                                        <th style="width: 16%;text-align: center">Ape. Materno</th>
                                        <th style="width: 15%;text-align: center">Nombres</th>
                                        <th style="width: 12%;text-align: center">Documento</th>
                                        <th style="width: 4%;text-align: center">&nbsp;</th>
                                    </tr>
                                <thead>
                                <tbody>

                                </tbody>        
                            </table>                                                            
                        </div>
                    </div>                                                      
                </form>
            </div>
            <div class="modal-footer">
                <div style="float:left; text-align: center; width: 50%">
     
                </div>
                <div style="float:right; width: 50%">
                                    <button type="button" id="btnSave"  name="btnSave" onclick="js_save();" disabled="disabled" class="btn btn-primary" /><i class="glyphicon glyphicon-cog"  ></i>&nbsp;Pagar
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" /><i class="glyphicon glyphicon-remove" ></i>&nbsp;Cerrar
                </div>

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
                            <label for="apellidos" class="col-lg-2 control-label">&nbsp;</label>
                            <div class="col-md-6 ">           
                                <!--
                                <div class="custom-control custom-radio custom-control-inline">     
                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions1" value="01" >
                                    <label class="form-check-label" for="inlineRadio1">RECIBO</label>                                                                    
                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions2" value="02" >
                                    <label class="form-check-label" for="inlineRadio1">BOLETA</label>           
                                    <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions3" value="03">
                                    <label class="form-check-label" for="inlineRadio1">FACTURA</label>       
                                </div>         
                                -->                                                         
                            </div>       
 
                            <div class="col-md-4">                               
                               <!-- <input type="text" id="txtnumrecibo"  disabled=""  name="txtnumrecibo" class="form-control negrita "  maxlength="10" >-->
                            </div>
                        </div> 
                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Concepto:</label>
                            <div class="col-lg-6">
                                <select name="cbconcepto" id="cbconcepto" class="form-control">
                                    <option value='0'>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>        

    
                            <div class="col-md-4">                               
                                <!--
                                <div class='input-group date'>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span> 
                                    <input type='text' name="txtfecha" id="txtfecha" class="form-control calendario"  readonly="" value="<?php //echo date('Y-m-d'); ?>" placeholder="Fecha de Inicio" data-date-format="yyyy-mm-dd" required="" />
                                </div>  
                                -->
                            </div>

                        </div>
                        <div class="form-group" style="display:none;">
                            <label for="apellidos" class="col-lg-2 control-label">Recibo:</label>
                            <div class="col-lg-3">
                                <input type="text" value="" name="txtrecibo" class="form-control"   maxlength="8" id="txtrecibo"/>
                            </div>                              
                        </div>                        

                        <div class="form-group">
                            <label for="Monto" class="col-lg-2 control-label">Monto:</label>
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

<!-- 3. Modal para Listar Comprobantes Realizados por Codigo -->
<div class="modal fade" id="modal_comprobantes" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 600px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Titulo</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form2" class="form-horizontal">
                    <!-- :::::::::::::: BLOQUE DE INPUT OCULTOS :::::::::::::: -->
                    <input type="hidden" value="" name="txtid" id="txtid"/>
                    <!-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                    <div class="form-body">
                        <div class="form-group">
                            <div class="col-lg-12">
                                <table class="table table-striped table-bordered"    id="viewBoletasAlumno" style="width: 100%">
                                    <thead>
                                        <tr class="tableheader">
                                            <th style="width: 10%;text-align: center">#</th>
                                            <th style="width: 40%;text-align: center">Fecha / Hora</th>
                                            <th style="width: 30%;text-align: center">Comprobante</th>    
                                            <th style="width: 20%;text-align: center">Config.</th>  
                                        </tr>
                                    <thead>
                                    <tbody>
                                    </tbody>        
                                </table>
                            </div>                              
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->                                
<!-- ========================= FIN BLOQUE DE MODALS ====================================== -->