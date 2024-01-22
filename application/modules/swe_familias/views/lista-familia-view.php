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
    fieldset {
        min-width: 0;
        padding: 0;
        margin: 0;
        border: 0;
    }
    .well {
        min-height: 20px;
        padding: 14px;
        margin-bottom: 10px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    }
    .well-legend {
        display: block;
        font-size: 14px;
        width: auto;
        padding: 2px 7px 2px 5px;
        margin-bottom: 10px;
        line-height: inherit;
        color: #333;
        background: #fff;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    }    
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp;Lista de Familias</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <form  id="formPrincipal" action=""  method="POST"  >
        <table style="text-align:center;width: 100%" border="0">
            <tr >               
                <td style ="width: 10%;float: center"><b>Familia : </b> </td>
                <td style ="width: 25%">
                    <input type="text" value="" name="txtsearch" placeholder="Ingrese la Familia" data-toggle="tooltip" title="Ingrese el texto a Buscar"  class="form-control" maxlength="15" id="txtsearch"/>
                </td>  
                <td style ="width: 5%">
                    <button type="button"   id="btnRefresh" name="btnRefresh" data-toggle="tooltip" title="Aplicar Filtro"  class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i>             
                </td>    
                <td style ="width: 10%" ><b>Hijos</b></td>
                <td style ="width: 10%">
                    <select name="cbhijo" id="cbhijo" class="form-control">
                        <option value=""> Seleccione </option>
                        <option value="1">1 Hijo(a)</option>
                        <option value="2">2 Hijo(s)</option>
                        <option value="3">> 2 Hijo(s)</option>
                        <option value="0">Ninguno</option>
                    </select>
                </td>
                <td style ="width: 20%;text-align:right;">
                    <button type="button"    id="btnTodos" name="btnTodos" data-toggle="tooltip" title="Mostrar  Todos" class="btn btn-danger"><i class="glyphicon glyphicon-refresh" /></i> Mostrar Todos
                </td>
                <td  style="width: 20%;text-align:right;">
                    <button type="button"   id="btnAgregar" name="btnAgregar" data-toggle="tooltip" title="Agregar  Familia" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign" /></i> Agregar Familia
                </td>
            </tr>            
        </table>
    </form>
</center>
<br>
<hr/>
<br>

<div id="divTblPagos">
    <table class="table table-bordered table-striped" id="viewFamilias">
        <thead>
            <tr class="tableheader">
                <th style="width:10%;text-align: center;">C&oacute;digo</th>
                <th style="width:40%;text-align: center;">Descripcion de Familia</th>
                <th style="width:30%;text-align: center;">E-mail</th>
                <th style="width:10%;text-align: center;">#Hijos</th>
                <th style="width:10%;text-align: center;">Opc.</th>
            </tr>
        </thead>
    </table>
</div>
<br/>
<div id="fade"></div>
<div id="modal">
    <img id="loader" src="<?= BASE_URL ?>/images/waiting.gif" width="150px" height="150px" />
</div>

<!-- Inicio : Modal Matricula-->         
<div class="modal fade" id="popAlumnos" role="dialog">
    <div class="modal-dialog" role="document" style="width:450px;" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Generar Clave Token</h4>
            </div>
            <form class="form-horizontal" method="post" id="frmgenerador" name="frmgenerador"  > 
                <input type="hidden" name="txtflag" id="txtflag" value="" /> 
                <div class="modal-body">
                    <div class="form-group">
                        <label for="estado"  class="col-sm-3 control-label">Usuario :</label>
                        <div class="col-sm-4">
                            <input type="text" style="font-size: 16px;font-weight: bold" class="form-control" id="txtfamcod" name="txtfamcod" value="" disabled />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Familia :</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="txtfamdes" name="txtfamdes" value="" disabled />
                        </div>
                    </div>                                     
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Clave :</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="txtclave" name="txtclave" value="" disabled />                                       
                        </div>
                    </div>    
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-7" id="divEstado" style="color:red; font-weight: bold;text-transform: uppercase;">                                                          
                        </div>                                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="guardar_datos"><i class="glyphicon glyphicon-refresh" /></i>Generar</button>&nbsp;
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove" /></i>Cerrar</button>                   
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agregar / Modificar Familia -->
<div class="modal fade" id="modalFamilia" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Nueva Familia</h4>
            </div>
            <!-- Contenedor Tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab1"><i class="fa fa-suitcase"></i>&nbsp;1. DATOS PADRE</a></li>
                <li ><a data-toggle="tab" href="#tab2"><i class="fa fa-user"></i>&nbsp;2. DATOS MADRE</a></li>
            </ul>
            <!-- Contenedor Tabs -->
            <!-- Tabs Independientes -->
            <form class="form-horizontal" method="post" id="frmProceso" name="frmProceso"  > 
                <div class="modal-body">

                    <input type="hidden"  id="accion" name="accion" value="" >     
                    <input type="hidden"  id="famdes" name="famdes" value="" >   
                    <input type="hidden"  id="txtcodigo" name="txtcodigo" value="" > 
                    <div class="tab-content">                        
                        <!-- INI :  Contenedor Tab 1 -->
                        <div id="tab1" class="tab-pane fade in active">                                                                                                            
                            <div class="form-group">
                                <label for="codigo" class="col-sm-3 control-label">Apellidos</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtpaterno_p" name="txtpaterno_p" maxlength="25" placeholder="Apellido Paterno" required>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtmaterno_p" name="txtmaterno_p" maxlength="25"  placeholder="Apellido Materno" >
                                </div>                        
                            </div>
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">Nombres</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtnombre_p" name="txtnombre_p" maxlength="50"  placeholder="Nombres Completos" >
                                </div>
                            </div>            
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">DNI</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtdni_p" name="txtdni_p" maxlength="8"  placeholder="DNI"   >                                    
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtcelular_p" name="txtcelular_p" maxlength="10"  placeholder="Nº Celular" >
                                </div>                                
                            </div>                               
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">Direcci&oacute;n</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtdireccion_p" name="txtdireccion_p" maxlength="100"  placeholder="Av. Ejemplo ·1587 - VILLA MARIA DEL TRIUNFO" >
                                </div>
                            </div>   
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">E-mail</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtemail_p" name="txtemail_p" maxlength="60"  placeholder="Ejemp : demo@gmail.com" >
                                </div>
                            </div>   

                        </div>
                        <!-- INI :  Contenedor Tab 2 -->
                        <div id="tab2" class="tab-pane fade">
                            <div class="form-group">
                                <label for="codigo" class="col-sm-3 control-label">Apellidos</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtpaterno_m" name="txtpaterno_m" maxlength="25" placeholder="Apellido Paterno" required>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtmaterno_m" name="txtmaterno_m" maxlength="25"  placeholder="Apellido Materno" >
                                </div>                        
                            </div>
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">Nombres</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtnombre_m" name="txtnombre_m" maxlength="50"  placeholder="Nombres Completos" >
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">DNI</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtdni_m" name="txtdni_m" maxlength="8" placeholder="DNI"  >                                    
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txtcelular_m" name="txtcelular_m" maxlength="10"  placeholder="Nº Celular" >
                                </div>                                 
                            </div>                             
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">Direcci&oacute;n</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtdireccion_m" name="txtdireccion_m" maxlength="60"  placeholder="Av. Ejemplo ·1587 - VILLA MARIA DEL TRIUNFO" >
                                </div>
                            </div>   
                            <div class="form-group">
                                <label for="nombre" class="col-sm-3 control-label">E-mail</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtemail_m" name="txtemail_m" maxlength="60"  placeholder="Ejemp : demo@gmail.com" >
                                </div>
                            </div>   

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="codigo" class="col-sm-3 control-label">FAMILIA : </label>
                        <span  id ="lblfamdes" class="label label-info" style="margin-left: 15px; font-size: 16px;"></span>
                    </div>

                </div>

                <!-- Tabs Independientes -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btngrabar">Grabar Datos</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Inicio : Modal Hijos-->         
<div class="modal fade" id="popupHijos" role="dialog">
    <div class="modal-dialog" role="document" style="width:650px;" >
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Lista de Hijos</h4>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="viewHijos">
                    <thead>
                        <tr class="tableheader">
                            <th style="width:20%;text-align: center;">C&oacute;digo</th>
                            <th style="width:60%;text-align: center;">Apellidos y Nombres</th>
                            <th style="width:20%;text-align: center;">NGS</th>
                        </tr>
                    </thead>
                    <tbody>                        
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove" /></i>Cerrar</button>                   
            </div>

        </div>
    </div>
</div>