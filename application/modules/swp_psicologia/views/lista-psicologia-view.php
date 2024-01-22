<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;
    }
    /* ================= Para Datatable ===================*/
    th.dt-center, td.dt-center { text-align: center;vertical-align: middle; }    
    th.dt-center, td.dt-right { text-align: right; } 
    th.dt-center, td.dt-left { text-align: left; } 
    /* ===================================================*/
    .datepicker{z-index:9999 !important}
    .ui-datepicker { position: relative; z-index: 10000 !important; }

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

<style type="text/css">
    #ui-datepicker-div
    {
        z-index: 9999999;
    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<link href="<?php echo base_url('assets/timepicker/css/bootstrap-timepicker.min.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/multiselect/bootstrap-multiselect.css') ?>" rel="stylesheet">
<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Lista de Atenciones - PSICÓLOGO</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <form  id="formPrincipal" action=""  method="POST"  target="_blank" >
        <input type="hidden" name="idreporte" id="idreporte" value="" />
        <table style="text-align:center;width: 100%" border="0">
            <tr style="height: 40px">               
                <td style ="width: 15%;float: center"><b>Estado :</b> </td>
                <td style ="width: 20%;">
                    <select name="idestado" id="idestado" class="form-control" >
                        <option value="">::: TODOS :::</option>
                        <option value="0">PENDIENTE</option>
                        <option value="1">ATENDIDO</option>
                        <?php //foreach ($lstmotivo as $row) { ?>
                            <!--<option value="<?//= $row->idmotivo ?>"><?//= $row->idmotivo . " : " . strtoupper($row->descripcion) ?></option>-->
                        <?php //} ?>
                    </select>
                </td>   
                <td style ="width: 15%;text-align: center"> </td>
                <td style ="width: 20%">                
                </td>        
                <td style ="width: 30%;text-align: right;">
                   <!-- <button type="button" class="btn btn-primary"   id="btnGrafico" name="btnGrafico">
                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Ver Grafico
                    </button>-->
                    <button type="button" class="btn btn-danger"   id="btnRefresh" name="btnRefresh">
                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Ver Todos
                    </button>
                    <button type="button" class="btn btn-primary"   id="btnBuscar" name="btnBuscar" onclick="javascript:js_addEgreso();">
                        <span class="glyphicon glyphicon-plus-sign"  aria-hidden="true"></span> Registrar
                    </button>                    
                </td>
            </tr>

        </table>
    </form>
</center>
<br>
<hr/>
<br>
<div id="divTblPagos">
    <table class="table table-striped table-bordered"    id="viewPsicologia" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">Codigo</th>
                <th style="width: 10%;text-align: center">Fecha / Hora</th>                                            
                <th style="width: 30%;text-align: center">Apellidos y Nombres</th>
                <th style="width: 25%;text-align: center">Motivo</th>   
                <th style="width: 10%;text-align: center">Estado</th>
                <th style="width: 5%;text-align: center">Alerta</th>
                <th style="width: 5%;text-align: center">NGS</th>
                <th style="width: 10%;text-align: center">Conf</th>
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
<div class="modal fade" id="modal_egresos" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Titulo</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form2" class="form-horizontal">
                    <input type="hidden" name="hidnemo" id="hidnemo" value="" />
                    <input type="hidden" name="htxtalumno" id="htxtalumno" value="" />
                    <input type="hidden" name="htxtsalon" id="htxtsalon" value="" />          
                    <input type="hidden" name="htxtaccion" id="htxtaccion" value="" />     
                    <input type="hidden" name="htxtidcita" id="htxtidcita" value="" />     

                    <div class="form-body">  

                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Motivo :</label>
                            <div class="col-lg-6">
                                <select name="cbmotivo"   id="cbmotivo" multiple="multiple">
                                    <?php foreach ($lstmotivo as $row) { ?>
                                        <option value="<?= $row->idmotivo ?>"><?= $row->idmotivo . " : " . strtoupper($row->descripcion) ?></option>
                                    <?php } ?>
                                </select>
                            </div>   

                            <div class="col-lg-4">
                                <div class="form-check form-check-inline" id="divasiste">
                                    <input class="form-check-input" type="checkbox" id="ckhasistio" value="0">
                                    <label class="form-check-label" for="ckhasistio">Asistio Alumno ?</label>
                                </div>
                            </div>
                        </div>                              

                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Alumno:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control"   id="txtAlumnoSearch" name="txtAlumnoSearch" placeholder="Escriba Apellido del Alumno" />
                            </div>                              
                            <div class="col-lg-1">
                                &nbsp;<button type="button"   id="btnReset" name="btnReset" data-toggle="tooltip" title="Buscar"  class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i>
                            </div>                                                            
                        </div> 

                        <div class="form-group">
                            <label for="title" class="col-sm-2 control-label">Asisten</label>
                            <div class="col-sm-10">
                                <input type="text" name="txtasiste" class="form-control" id="txtasiste" placeholder="Ejem. PAPÁ / MAMÁ / TIOS">
                            </div>
                        </div>  

                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Fecha:</label>
                            <div class="col-lg-4">
                                <div class='input-group date'>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span> 
                                    <input type='text' name="txtfecha" id="txtfecha" class="form-control calendario"  readonly="" value="<?php echo date('Y-m-d'); ?>" placeholder="Fecha de Inicio" data-date-format="yyyy-mm-dd" required="" />
                                </div>
                            </div>      
                            <label for="apellidos" class="col-lg-2 control-label">Hora:</label>
                            <div class="col-lg-4">            
                                <div class="input-group bootstrap-timepicker timepicker">
                                    <input id="txthora" name="txthora" type="text" readonly="" class="form-control input-small">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                </div>            
                            </div>
                        </div>    

                        <div class="form-group">
                            <label for="color" class="col-sm-2 control-label">Prioridad:</label>
                            <div class="col-sm-4">
                                <select name="color" class="form-control" id="color">
                                    <option  value="#FF0000" style="color:#FF0000;">&#9724; Muy Urgente</option>
                                    <option  value="#008000" style="color:#008000;" selected="selected">&#9724; Urgente</option>						  
                                    <option  value="#FFD700" style="color:#FFD700;">&#9724; Normal</option>
                                </select> 
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="apellidos" class="col-lg-2 control-label">Inteligencia:</label>
                            <div class="col-lg-10">
                                <textarea class="form-control rounded-0" id="txtinteligencia" disabled=""  maxlength="500"  name="txtinteligencia" rows="2"></textarea>
                                <div class="invalid-feedback">Max. 500 caracteres.</div>
                            </div>                              
                        </div>    
                        <div class="form-group">
                            <label for="Monto" class="col-lg-2 control-label">Emocional:</label>
                            <div class="col-lg-10">
                                <textarea class="form-control rounded-0" id="txtemocional"  disabled=""  maxlength="500"  name="txtemocional" rows="2"></textarea>
                                <div class="invalid-feedback">Max. 500 caracteres.</div>
                            </div>                              
                        </div>
                        <div class="form-group">
                            <label for="Monto" class="col-lg-2 control-label">Recomendacion:</label>
                            <div class="col-lg-10">
                                <textarea class="form-control rounded-0" id="txtrecomendaciones" disabled=""  maxlength="1000"  name="txtrecomendaciones" rows="2"></textarea>
                                <div class="invalid-feedback">Max. 1000 caracteres.</div>
                            </div>                              
                        </div>                                                    
                    </div>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnSaveConcepto" onclick="js_grabar()" class="btn btn-primary">Grabar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>                
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<!-- 2. Modal para Mosrar los graficos -->
<div class="modal fade" id="modal_grafico" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Titulo</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form3" class="form-horizontal">
                    <div class="form-body">  
                        <div id="viewgrafico">
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" id="btnSaveGraph"  class="btn btn-primary">Grabar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>          
            </div>
        </div>
    </div>
</div>    
<script src="<?php echo base_url('assets/timepicker/js/bootstrap-timepicker.min.js') ?>"></script>     
<script src="<?php echo base_url('assets/multiselect/bootstrap-multiselect.js') ?>"></script>                            