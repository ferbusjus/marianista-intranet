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

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Registro de Becas</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <form  id="formPrincipal" action=""  method="POST"  target="_blank" >
        <table style="text-align:center;width: 100%" border="0">
            <tr style="height: 40px">               
                <td style ="width: 15%;float: center"><b>Tipo Becas :</b> </td>
                <td style ="width: 20%">
                    <select name="idbeca" id="idbeca" class="form-control">
                        <option value="">::::::::::: Todos ::::::::::</option>
                        <?php foreach ($becas as $beca) { ?>
                            <option value="<?= $beca->BECACOD ?>"><?= $beca->BECADES ?></option>
                        <?php } ?>
                    </select>
                </td>   
                <td style ="width: 20%;text-align: center">&nbsp; </td>
                <td style ="width: 20%">
                    &nbsp;                 
                </td>        
                <td style ="width: 25%;text-align: right;">
                    <button type="button"   id="btnRefresh" name="btnRefresh"  class="btn btn-danger"><i class="glyphicon glyphicon-refresh" /></i>Ver Todos&nbsp;
                        <button type="button"   id="btnAgregar" name="btnAgregar" <?=(($s_ano_vig==$vano)?'':'disabled="disabled"')?>  class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign" /></i> Registrar
                            </td>
                            </tr>

                            </table>
                            </form>
                            </center>
                            <br>
                            <hr/>
                            <br>
                            <div id="divTblPagos">
                                <table class="table table-striped table-bordered"    id="viewBecas" style="width: 100%">
                                    <thead>
                                        <tr class="tableheader">
                                            <th style="width: 10%;text-align: center">DNI</th>
                                            <th style="width: 40%;text-align: center">Apellidos y Nombres</th>
                                            <th style="width: 5%;text-align: center">NGS </th>
                                            <th style="width: 10%;text-align: center">Mes Inicio</th>                
                                            <th style="width: 10%;text-align: center">Mes Fin</th>
                                            <th style="width: 20%;text-align: center">Tipo Beca</th>
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
                            <!-- 1. Modal para Agregar Becas -->
                            <div class="modal fade" id="modal_becas" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="width: 600px">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Titulo</h4>
                                        </div>
                                        <div class="modal-body form">
                                            <form action="#" id="form2" class="form-horizontal">
                                                <div class="form-body">  

                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Alumno :</label>
                                                        <div class="col-lg-8">
                                                            <select name="cbalumno" id="cbalumno" class="form-control">
                                                                <option value='0'>:::::::::::: Seleccione ::::::::::::</option>
                                                            </select>
                                                        </div>     
                                                    </div>                              

                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Mes Ini :</label>
                                                        <div class="col-lg-4">
                                                            <select name="cbmes1" id="cbmes1" class="form-control">
                                                                <option value=''>Seleccione</option>
                                                                <option value='03'>Marzo</option>
                                                                <option value='04'>Abril</option>
                                                                <option value='05'>Mayo</option>
                                                                <option value='06'>Junio</option>
                                                                <option value='07'>Julio</option>
                                                                <option value='08'>Agosto</option>
                                                                <option value='09'>Setiembre</option>
                                                                <option value='10'>Octubre</option>
                                                                <option value='11'>Noviembre</option>
                                                                <option value='12'>Diciembre</option>
                                                            </select>
                                                        </div>     
                                                    </div>     

                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Mes Fin :</label>
                                                        <div class="col-lg-4">
                                                            <select name="cbmes2" id="cbmes2" class="form-control">
                                                                <option value=''>Seleccione</option>
                                                                <option value='03'>Marzo</option>
                                                                <option value='04'>Abril</option>
                                                                <option value='05'>Mayo</option>
                                                                <option value='06'>Junio</option>
                                                                <option value='07'>Julio</option>
                                                                <option value='08'>Agosto</option>
                                                                <option value='09'>Setiembre</option>
                                                                <option value='10'>Octubre</option>
                                                                <option value='11'>Noviembre</option>
                                                                <option value='12'>Diciembre</option>
                                                            </select>
                                                        </div>     
                                                    </div>   

                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Motivo Beca :</label>
                                                        <div class="col-lg-8">
                                                            <select name="cbmotbeca" id="cbmotbeca" class="form-control">
                                                                <option value='0'>:::::::::::: Seleccione ::::::::::::</option>
                                                            </select>
                                                        </div>     
                                                    </div>    
                                                    
                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Tipo Beca :</label>
                                                        <div class="col-lg-8">
                                                            <select name="cbtipobeca" id="cbtipobeca" class="form-control">
                                                                <option value='0'>:::::::::::: Seleccione ::::::::::::</option>
                                                            </select>
                                                        </div>     
                                                    </div>     
                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Pensión Actual  :</label>
                                                        <div class="col-lg-4">
                                                            <input type="text" value="340.00" name="txtpension" disabled="" class="form-control" maxlength="7" id="txtpension" style="width: 50%; font-weight: bold;"/>
                                                        </div>     
                                                    </div>   
                                                    <div class="form-group">
                                                        <label for="apellidos" class="col-lg-3 control-label">Monto :</label>
                                                        <div class="col-lg-4">
                                                            <input type="text" value="" name="txtmonto" readonly="" class="form-control" onkeypress="return validaNumeros(event, this);"  style="width: 50%; font-weight: bold;" maxlength="7" id="txtmonto"/>
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
                            </div><!-- /.modal -->