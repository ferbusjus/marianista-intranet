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

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Lista de Alumnos</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>

    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <form  id="formPrincipal" action=""  method="POST"  >
        <table style="text-align:center;width: 100%" border="0">
            <tr >               
                <td style ="width: 10%;float: center"><b>Nivel :</b> </td>
                <td style ="width: 15%">
                    <select name="cbnivel" id="cbnivel" data-toggle="tooltip" title="Seleccione el Nivel"  class="form-control">
                        <option value="">::::::: Todos :::::::</option>
                        <?php foreach ($dataNivel as $nivel) : ?>
                            <option value="<?php echo $nivel->INSTRUCOD ?>"><?php echo $nivel->INSTRUCOD . " - " . $nivel->INSTRUDES ?></option>
                        <?php endforeach; ?>                        
                    </select>
                </td>  
                <td style ="width: 10%;float: center"><b>Grado :</b> </td>
                <td style ="width: 15%">
                    <select name="cbgrado" id="cbgrado" data-toggle="tooltip" title="Seleccione el Grado" class="form-control">
                        <option value="">::::::: Todos :::::::</option>
                    </select>                    
                </td>    
                <td style ="width: 10%" ></td>
                <td  style="width: 20%;text-align: center">
                    &nbsp;
                </td>                      
                <td  style="width: 20%;text-align: right">
                    <button type="button"   id="btnAgregar"  <?=(($s_ano_vig==$vano)?'':'disabled="disabled"')?>  name="btnAgregar" data-toggle="tooltip" title="Agregar  Alumnos" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign" /></i> Agregar Alumno
                </td>
            </tr>            
            <tr style="height: 40px;">
                <td style ="width: 10%;float: center"><b>Filtrar Por:</b> </td>
                <td style ="width: 15%">
                    <select name="cbfiltro" id="cbfiltro" class="form-control">
                        <option value="">:::: Seleccione ::::</option>
                        <option value="1">DNI</option>
                        <option value="2">CODIGO</option>
                        <option value="3" selected="">APELLIDOS</option>
                         <option value="4">NUN LIBRO</option>
                    </select>
                </td>   
                <td colspan="2" style ="width: 25%;text-align: center">
                    <input type="text" value="" name="txtsearch" placeholder="Seleccione el Filtro" data-toggle="tooltip" title="Ingrese el texto a Buscar"  class="form-control" maxlength="15" id="txtsearch"/>
                </td>    
                <td style ="width: 10%" >
                    <button type="button"   id="btnRefresh" name="btnRefresh" data-toggle="tooltip" title="Aplicar Filtro"  class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i>                    
                </td>
                <td  style="width: 20%;text-align: center">
                    <!--<span style="font-weight: bold; font-size: 20px;font-family: Trebuchet MS">AÑO : 2018</span>-->
                </td>                
                <td  style="width: 20%;text-align: right">
                    <button type="button"   id="btnMostrarAll" name="btnMostrarAll" data-toggle="tooltip" title="Mostrar todo los Alumnos" class="btn btn-danger"><i class="glyphicon glyphicon-refresh" /></i> Mostrar Todos
                </td>
            </tr>
        </table>
    </form>
</center>
<br>
<hr/>
<br>
<div id="divTblPagos">
    <table class="table table-striped table-bordered"    id="viewAlumnos" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 10%;text-align: center">Codigo</th>
                <th style="width: 35%;text-align: center">Apellidos y Nombres</th>            
                <th style="width: 10%;text-align: center">Aula</th>
                <th style="width: 5%;text-align: center">Nivel </th>
                <th style="width: 5%;text-align: center">Grado</th>          
                <th style="width: 10%;text-align: center">Matricula</th>     
                <th style="width: 15%;text-align: center">Estado</th>     
                <th style="width: 10%;text-align: center">Config.</th>
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

<!-- Modal Detalle Alumno-->
<div class="modal fade" id="modalAlumno" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Nuevo Alumno</h4>
            </div>
            <!-- Contenedor Tabs -->
            <!--<ul class="nav nav-tabs">-->
            <!--<li class="active"><a data-toggle="tab" href="#tab1">1. DATOS ALUMNO</a></li>-->
            <!--<li ><a data-toggle="tab" href="#tab2">2. DATOS ADICIONALES</a></li>-->
            <!--</ul>-->
            <!-- Contenedor Tabs -->
            <!-- Tabs Independientes -->
            <form class="form-horizontal" method="post" id="frmAlumno" name="frmAlumno"  > 
                <input type="hidden"  id="accion" name="accion" value="" >
                <input type="hidden"  id="hcb_familia" name="hcb_familia" value="" >

                <input type="hidden"  id="hcombo" name="hcombo" value="0" >
                <!--<div class="tab-content">                        -->

                <!-- INI :  Contenedor Tab 1 -->
                <!--<div id="tab1" class="tab-pane fade in active">-->
                <div class="modal-body">
                    <input type="hidden"  id="txtcodigo" name="txtcodigo" value="" >
                    <div class="form-group has-success">
                        <label for="codigo" class="col-sm-3 control-label">DNI</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="txtdni" name="txtdni" maxlength="8" placeholder="Numero de DNI" required=""  >                            
                        </div>
                        <div class="col-sm-3" >
                            <input type="text" class="form-control" style="display:none" id="txtlibro" name="txtlibro" maxlength="4" placeholder="Nº Libro"   >        
                        </div>                        
                        <!--
                        <div class="col-sm-5">
                            <button type="button"   id="btnReniec" name="btnReniec" data-toggle="tooltip" title="CONSULTA POR RENIEC"  class="btn btn-primary">
                                <i class="glyphicon glyphicon-search" /></i>     
                            </button> &nbsp;

                            <img id="divmodal" src="<?//= BASE_URL ?>/img/gif-load.gif" width="30px" height="30px"  />

                        </div>
                        -->
                    </div>                                
                    <div class="form-group has-warning">
                        <label for="codigo" class="col-sm-3 control-label">Ape- Paterno</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtpaterno"  name="txtpaterno" maxlength="25" placeholder="Apellido Paterno" required>
                        </div>
                    </div>
                    <div class="form-group has-warning">
                        <label for="nombre" class="col-sm-3 control-label">Ape- Materno</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtmaterno"   name="txtmaterno" maxlength="25"  placeholder="Apellido Materno" required>
                        </div>
                    </div>
                    <div class="form-group has-warning">
                        <label for="nombre" class="col-sm-3 control-label">Nombres</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtnombre"   name="txtnombre" maxlength="50"  placeholder="Nombres Completos" required>
                        </div>
                    </div>              
                    <div class="form-group">
                        <label for="nombre" class="col-sm-3 control-label">Direcci&oacute;n</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtdireccion" name="txtdireccion" maxlength="60"  placeholder="Av. Ejemplo ·1587 - VILLA MARIA DEL TRIUNFO" >
                        </div>
                    </div>   

                    <div class="form-group">
                        <label for="nombre" class="col-sm-3 control-label">Colegio Procede</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="txtprocede" name="txtprocede" maxlength="100"  placeholder="Ejem : Colegio Marianista" >
                        </div>
                    </div>   

                    <div class="form-group">
                        <label for="nombre" class="col-sm-3 control-label">Telefono(s)</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="txttelefono" name="txttelefono" maxlength="9"  placeholder="Telefono 1 " >
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="txttelefono2" name="txttelefono2" maxlength="9"  placeholder="Telefono 2" >
                        </div>                        
                    </div>                    
                    <!--<div class="form-group" style="display: none;">
                        <label for="estado" class="col-sm-3 control-label">Estado</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="cb_estado" name="cb_estado" required>
                                <option value="">::::: Seleccione :::::</option>
                                <option value="V">ACTIVO</option>
                                <option value="R" >DESHABILITADO</option>
                                <option value="M" >MATRICULADO</option>                                
                            </select>
                        </div>
                    </div>     -->
                    <div class="form-group" id="divlabel">
                        <label for="codigo" class="col-sm-3 control-label">Aula</label>
                        <div class="col-sm-8" >
                            <h3>
                                <span class="label label-warning" id="lblaula"></span>
                            </h3>
                        </div>
                    </div>       

                    <div class="form-group" id="divcheck">
                        <label for="codigo" class="col-sm-3 control-label"></label>           
                        <input type="checkbox" onclick="js_carga(this.checked);" name="chkHermanos" id="chkHermanos" value="0"> <b>Tiene Hermanos ?</b>               
                    </div>
                    <div class="form-group ">
                        <label for="codigo" class="col-sm-3 control-label">Familia</label>
                        <div class="col-sm-8" id="divcbfamilia">
                            <select class="form-control" disabled="disabled" id="cb_familia" name="cb_familia" >
                                <!--<option value="">::::::::::::::::::: NINGUNO :::::::::::::::::::<option>   -->  
                                    <?php //foreach ($dataFamilia as $familia) : ?>
                                    <!--<option value="<?php //echo $familia->FAMCOD ?>"><?php //echo $familia->FAMCOD . " - " . $familia->FAMDES ?></option>-->
                                <?php //endforeach; ?>                                     
                            </select>
                        </div>
                        <div class="col-sm-8" id="divtxtfamilia">
                            <input type="text" class="form-control" id="txtfamilia" name="txtfamilia" maxlength="60" readonly=""  >
                        </div>                        
                    </div>                      



                    <!--
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Nivel</label>
                        <div class="col-sm-4">
                            <select class="form-control" style="width:150px" id="cb_nivel" disabled="disabled" name="cb_nivel" onchange="javascript:cargaGrado()" >
                                <option value="">:::: Seleccione ::::</option>
                                <option value="I" >Inicial</option>
                                <option value="P">Primaria</option>
                                <option value="S">Secundaria</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Grado</label>
                        <div class="col-sm-4">
                            <select class="form-control" style="width:150px" id="cb_grado" disabled="disabled" name="cb_grado" onchange="javascript:cargaAula()" >
                                <option value="">::::: Ninguno :::::</option>
                            </select>
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Pais</label>
                        <div class="col-sm-4">
                            <select class="form-control" style="width:150px" id="cb_seccion" disabled="disabled" name="cb_seccion" >
                                <option value="">::::: Ninguno :::::</option>
                            </select>
                        </div>                                   
                    </div>   
                    -->
                </div>
                <!--</div>-->

                <!-- Tabs Independientes -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btngrabar">Grabar Datos</button>
                </div>

                <!--</div>-->
            </form>

        </div>
    </div>
</div>


