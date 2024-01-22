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

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Ingresos Adicionales</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <form  id="formPrincipal" action=""  method="POST"  target="_blank" >
        <table style="text-align:center;width: 100%" border="0">
            <tr style="height: 40px">               
                <td style ="width: 5%;float: center">&nbsp; </td>
                <td style ="width: 30%">
                    &nbsp;
                </td>   
                <td style ="width: 5%;text-align: center">&nbsp;</td>
                <td style ="width: 45%">
                    &nbsp;
                </td>        
                <td style ="width: 15%;">
                    <button type="button"   id="btnBuscar" name="btnBuscar" onclick="javascript:js_addconcepto();" class="btn btn-primary"><i class="glyphicon glyphicon-refresh" /></i> Registrar
                </td>
            </tr>

        </table>
    </form>
</center>
<br>
<hr/>
<br>
<div id="divTblPagos">
    <table class="table table-striped table-bordered"    id="viewAdiconales" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">ID</th>
                <th style="width: 10%;text-align: center">Fec-Reg</th>
                <th style="width: 35%;text-align: center">Apellidos y Nombres</th>
                <th style="width: 30%;text-align: center">Concepto</th>
                <th style="width: 10%;text-align: center">Recibo</th>
                <th style="width: 10%;text-align: center">Monto</th>          
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

                        <div class="form-group">
                            <label for="apellidos" class="col-lg-3 control-label">Concepto:</label>
                            <div class="col-lg-8">
                                <select name="cbconcepto" id="cbconcepto" class="form-control">
                                    <option value='0'>:::::::::::: Seleccione ::::::::::::</option>
                                </select>
                            </div>     
                        </div>                              
                    </div>

                    <div class="form-group">
                        <label for="apellidos" class="col-lg-3 control-label">Apellidos:</label>
                        <div class="col-lg-4">
                            <input type="text" value="" name="txtpaterno" class="form-control"  maxlength="30" id="txtpaterno" placeholder="Apellido Paterno"/>
                        </div>                           
                        <div class="col-lg-4">
                            <input type="text" value="" name="txtmaterno" class="form-control"  maxlength="30" id="txtmaterno" placeholder="Apellido Materno"/>
                        </div>                       
                    </div>

                    <div class="form-group">
                        <label for="apellidos" class="col-lg-3 control-label">Nombres:</label>
                        <div class="col-lg-8">
                            <input type="text" value="" name="txtnombres" class="form-control"  maxlength="50" id="txtnombres" placeholder="Nombres"/>
                        </div>                             
                    </div>

                    <div class="form-group">
                        <label for="apellidos" class="col-lg-3 control-label">Fecha:</label>
                        <div class="col-lg-4">
                            <div class='input-group date'>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span> 
                                <input type='text' name="txtfecha" id="txtfecha" class="form-control calendario"  readonly="" value="<?php echo date ('d/m/Y'); ?>" placeholder="Fecha de Inicio" data-date-format="dd/mm/yyyy" required="" style="width:130px;"/>
                            </div>
                        </div>                              
                    </div>    

                    <div class="form-group">
                        <label for="apellidos" class="col-lg-3 control-label">Recibo:</label>
                        <div class="col-lg-4">
                            <input type="text" value="" name="txtrecibo" class="form-control"  maxlength="10" id="txtrecibo"/>
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
            <div class="modal-footer">
                <button type="button" id="btnSaveConcepto" onclick="js_save_concepto()" class="btn btn-primary">Grabar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>                
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->