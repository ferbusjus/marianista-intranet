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
<style>
    #mdialTamanio{
        width: 70% !important;
    }
    .list-group-item {
        cursor:pointer;
    }

    .list-group-item:hover{
        background-color: #96daff;
        font-weight: bold;
    }
    .marca{
        background-color: #96daff;
        font-weight:bold;
    }    
</style>

<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
    /* swal({
     type: 'success',
     title: 'Aviso',
     text: 'SISTEMA ACTUALIZADO CORRECTAMENTE.'
     });*/
</script>

<div class="container-fluid">
    <h3 >
        <i class="fa fa-refresh fa-spin fa-1x"></i>&nbsp;Registro de Matriculas
    </h3>               
</div>
<br/>
<hr/><br/> 
<center>
    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
    <input type="hidden" name="flgMatricula" id="flgMatricula" value="<?= $flg_matricula ?>" />
    <form  id="formPrincipal" action=""  method="POST" onsubmit="return false;"  >
        <table style="text-align:center;width: 100%" border="0">        
            <tr style="height: 40px;">
                <td style ="width: 5%;float: center"><b>Año:</b> </td>
                <td style ="width: 8%">
                    <select name="cbano" id="cbano"  class="form-control"   disabled="" > <?php //echo (($flg_matricula == 1) ? "" : "disabled=''") ?>
                        <?php for ($ano = ANO_INICIO_PEN; $ano <= $s_ano_vig; $ano++) : ?>
                            <option value="<?= $ano ?>" <?= (($s_ano_vig == $ano) ? 'selected="selected"' : '') ?>><?= $ano ?></option>
                        <?php endfor; ?>
                    </select>
                </td>                    
                <td style ="width: 10%;float: center"><b>Filtrar Por:</b> </td>
                <td style ="width: 15%">
                    <select name="cbfiltro" id="cbfiltro" class="form-control">
                        <option value="">:::: Seleccione ::::</option>
                        <option value="1">DNI</option>
                        <option value="2" selected="">APELLIDOS</option>
                    </select>  
                </td>   
                <td style ="width: 52%;text-align: center">
                    <input type="text" name="txtsearch" placeholder="Ingrese el Apellido del Alumno a Buscar y luego presione (ENTER)"  data-toggle="tooltip" title="Ingrese el texto a Buscar"  class="form-control"  maxlength="30" id="txtsearch"/>
                </td>    
                <td style ="width: 10%" >
                    <button type="button"   id="btnRefresh" name="btnRefresh" data-toggle="tooltip" title="Aplicar Filtro"  class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i>                    
                </td>
            </tr>

            <tr style="height: 50px;">
                <td colspan="4" style="text-align: left"> 
                    <input type="checkbox" id="chkBusqueda" name="chkBusqueda[]" value="P" checked="" >Alumnos Promovidos
                    &nbsp;&nbsp;&nbsp;<input type="checkbox" id="chkBusqueda" name="chkBusqueda[]" value="R" checked="" >Alumnos Retirados
                    &nbsp;&nbsp;&nbsp;<input type="checkbox" id="chkBusqueda" name="chkBusqueda[]" value="A" checked="" >Alumnos Antiguos
                   <!-- &nbsp;&nbsp;&nbsp;<input type="checkbox" id="chkBusqueda" name="chkBusqueda[]" value="V" checked="" >Alumnos Vigentes-->
                </td>
                <td  colspan="2" style="text-align: right" >              
                    <!--<button type="button"   id="btnMostrarAll" name="btnMostrarAll" data-toggle="tooltip" title="Mostrar todo los Alumnos" class="btn btn-success"/><i class="glyphicon glyphicon-refresh" /></i> Mostrar Todos-->
                    &nbsp;<button type="button"   id="btnMatricular" name="btnMatricular"  data-toggle="tooltip" title="Buscar Alumnos" class="btn btn-labeled btn-success" <?= ($s_ano_vig < 2024) ? "disabled='disabled'" : "" ?> ><span class="btn-label"><i class="fa fa-search"></i></span> Buscar Alumnos</button>
                    &nbsp;&nbsp; <button type="button"   id="btnPopupMatricula" name="btnPopupMatricula" data-toggle="tooltip" title="Matricular"  class="btn btn-danger" <?= ($s_ano_vig < 2024) ? "disabled='disabled'" : "" ?> /><i class="glyphicon glyphicon-plus-sign" /></i> Matricular
                </td>
            </tr>
        </table>
    </form>
</center>   
<br>
<hr/>
<br>
<div id="divTblPagos">
    <table class="table table-striped table-bordered"    id="viewMatricula" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">Periodo</th>
                <th style="width: 5%;text-align: center">Código</th> 
                <th style="width: 5%;text-align: center">DNI</th>
                <th style="width: 45%;text-align: center">Apellidos y Nombres</th>                      
                <th style="width: 5%;text-align: center">NGS</th>       
                <!--<th style="width: 10%;text-align: center">Aula</th>-->
                <th style="width: 10%;text-align: center">
	<select name="idAula" id="idAula" class="form-control" style="border: 1px dashed">
            <option value="" style="text-align: center;"> :: MOSTRAR  TODAS :: </option>
                    <?php foreach($lstAulas as $aula): ?>
            <option value="<?=$aula->nemo;?>" style="background-color: #fcf8e3"><?=$aula->aulades;?></option>    
                    <?php endforeach; ?>
                  </select>
                </th>
                <th style="width: 7%;text-align: center">Estado</th>     
                <th style="width: 10%;text-align: center">Fecha-Mat</th>    
                <th style="width: 8%;text-align: center">Config.</th>
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

<?php $this->load->view('lista-alumno-buscar-view') ?>      
<?php //$this->load->view('lista-alumno-matricula-view') ?>      


</div>

<link href="<?php echo base_url('assets/wizard/css/smart_wizard.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/wizard/css/smart_wizard_theme_arrows.css') ?>" rel="stylesheet">

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document" id="mdialTamanio">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">REGISTRO DE MATRICULA</h4>
            </div>
            <div class="modal-body">
                <!-- CAMPOS OCULTOS PARA IMPRIMIR COMPROBANTES -->
                <form action="#" id="formPrint"  method="post" target="_blank"  >                    
                    <input type="hidden" name="hidnemo" id="hidnemo" value="" />
                    <input type="hidden" name="htxtalumno" id="htxtalumno" value="" />
                    <input type="hidden" name="htxtsalon" id="htxtsalon" value="" />        
                    <input type="hidden" name="rbdTipo" id="rbdTipo" value="" />  
                    <input type="hidden" name="htxtnumrecibo" id="htxtnumrecibo" value="" />          
                </form>
                <!-- CAMPOS OCULTOS PARA IMPRIMIR CONTRATO / CONSTANCIA -->
                <form action="#" id="formPrintServicio"  method="post" target="_blank"  >                    
                    <input type="hidden" name="hdnip" id="hdnip" value="" />
                    <input type="hidden" name="hnomcomp" id="hnomcomp" value="" />
                    <input type="hidden" name="hdnia" id="hdnia" value="" />        
                    <input type="hidden" name="hnomcompa" id="hnomcompa" value="" />        
                    <input type="hidden" name="hnivel" id="hnivel" value="" />    
                    <input type="hidden" name="hgrado" id="hgrado" value="" />    
                    <input type="hidden" name="hdireccion" id="hdireccion" value="" /> 
                    <input type="hidden" name="htipo" id="htipo" value="" /> 
                    <input type="hidden" name="hdocumentos" id="hdocumentos" value="" />   
                    <input type="hidden" name="htxtdsc" id="htxtdsc" value="" />   
                    <input type="hidden" name="haula" id="haula" value="" />   
                    <input type="hidden" name="htipoalu" id="htipoalu" value="" />   
                </form>

                <form action="#" id="myForm" role="form" data-toggle="validator" method="post" accept-charset="utf-8" >
                    <input type="hidden" id="accion" name="accion" value="" >
                    <input type="hidden" id="aulacod" name="aulacod" value="" >
                    <!--<input type="hidden" id="totalcomp" name="totalcomp" value="300" >-->
                    <input type="hidden" id="numcomprobante" name="numcomprobante" value="" >
                    <input type="hidden" id="flgerror" name="flgerror" value="" >
                    <input type="hidden" id="fecpago" name="fecpago" value="<?= date("Y-m-d") ?>" >
                    <input type="hidden" id="famcod" name="famcod" value="" >
                    <input type="hidden" id="hdnemo" name="hdnemo" value="" >					

                    <!-- SmartWizard html -->
                    <div id="smartwizard">
                        <ul>
                            <li><a href="#step-1">Paso 1<br /><small><b>DATOS ALUMNO</b></small></a></li>
                            <li><a href="#step-2">Paso 2<br /><small><b>DATOS PADRE</b></small></a></li>
                            <li><a href="#step-3">Paso 3<br /><small><b>DATOS MADRE</b></small></a></li>
                            <li><a href="#step-4">Paso 4<br /><small><b>DATOS APODERADO</b></small></a></li>
                            <li><a href="#step-5">Paso 5<br /><small><b>DATOS AULA</b></small></a></li>
                            <li><a href="#step-6">Paso 6<br /><small><b>RESUMEN FINAL</b></small></a></li>
                            <li><a href="#step-7" >Paso 7<br /><small><b>PAGO MATRICULA</b></small></a></li>
                        </ul>
                        <div id="smartwizardPasos">
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS ALUMNO :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-1">
                                <div id="form-step-0" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Cod. Sistema:</label>
                                            <input type="text" class="form-control" id="alucod" name="alucod"
                                                   placeholder="Ingresar Código" readonly >
                                        </div>
                                        <div class="col-sm-2">
                                            <label for="email">Tipo de documento:</label>
                                            <select name="tipodoc" id="tipodoc"   class="form-control" required="" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="1" selected=""  >LE/DNI </option>
                                                <option value="2"  >CARNET EXT. </option>        
                                                <option value="3"  >PASAPORTE </option>   
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>                                        
                                        <div class="col-sm-4">
                                            <label for="email">Número de documento:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="dni" minlength="8" name="dni"   maxlength="12"
                                                       placeholder="Ingresar número de documento" required="" onblur="validarDni();"  >
                                                <!--<div class="help-block with-errors"></div>-->
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchalumno" onclick="javascript:consultar_dni($('#dni').val(), 'A', 'dni');"></span></span>
                                            </div>                                            
                                           <!-- <input type="text" class="form-control" id="dni" minlength="8" name="dni"   maxlength="12"
                                                   placeholder="Ingresar número de documento" required="" onblur="validarDni();"  >
                                            <div class="help-block with-errors"></div> -->
                                        </div>  
                                    </div>                                     
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Paterno:</label>
                                            <input type="text" class="form-control" id="apepat"  name="apepat"  maxlength="25" 
                                                   placeholder="Ingresar Apellido Paterno" required="">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Materno:</label>
                                            <input type="text" class="form-control" id="apemat" name="apemat"  maxlength="25"
                                                   placeholder="Ingresar Apellido Materno" required="">
                                            <div class="help-block with-errors"></div>
                                        </div>  
                                    </div>                                   
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Nombre(s):</label>
                                            <input type="text" class="form-control" id="nombres" name="nombres"  maxlength="50"
                                                   placeholder="Ingresar Nombres" required="">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Fecha de Nacimiento:</label>
                                            <input type="date" class="form-control" id="fecnac" name="fecnac" >
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Celular ó Telefono:</label> 
                                            <input type="text" class="form-control" id="telefono"  maxlength="9" name="telefono" 
                                                   placeholder="Ingresar Celular ó Telefono" >      
                                        </div>
                                    </div>     
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Colegio Procedencia:</label>
                                            <input type="text" class="form-control" id="procede" name="procede"  placeholder="Ingresar Lugar de Procedencia" >
                                        </div>
                                        <div class="col-sm-6">Sexo:</label> 
                                            <select name="sexo" id="sexo"   class="form-control" required="" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="M"  >MASCULINO </option>
                                                <option value="F"  >FEMENINO </option>                                        
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>     
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Correo Electrónico:</label>
                                            <input type="email" class="form-control" id="aluemail" name="aluemail" 
                                                   placeholder="Ingresar Correo electrónico" maxlength="100">
                                            <div class="help-block with-errors"></div>
                                        </div>                                          
                                    </div>       

                                </div>
                            </div>
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS PADRE :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-2">
                                <div id="form-step-1" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <div class="col-sm-12" style="text-align: right">
                                            <button type="button" id="btnMemoriaPapaCopy" style="display:none;" name="btnMemoriaPapaCopy" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Copiar Datos en Memoria"><i class="glyphicon glyphicon-copy"></i>Copiar</button>
                                            <button type="button" id="btnMemoriaPapaPaste" style="display:none;" name="btnMemoriaPapaPaste" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Pegar Datos almacendo Memoria"><i class="glyphicon glyphicon-paste"></i>Pegar</button>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label for="email">Tipo de documento:</label>
                                            <select name="tipodocpad" id="tipodocpad"   class="form-control" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="1" selected=""  >LE/DNI </option>
                                                <option value="2"  >CARNET EXT. </option>        
                                                <option value="3"  >PASAPORTE </option>   
                                            </select>
                                        </div>                                        
                                        <div class="col-sm-4">
                                            <label for="email">Número de documento:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="dnipater" minlength="8"  maxlength="12" name="dnipater" 
                                                       placeholder="Ingresar DNI" >
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchpadre" onclick="javascript:consultar_dni($('#dnipater').val(), 'P', 'dnipater');"></span></span>
                                            </div>
                                        </div>  

                                        <div class="col-sm-6">
                                            <label for="email">Fecha de Nacimiento:</label>
                                            <input type="date" class="form-control" id="padfecnac" name="padfecnac" >
                                        </div>                                     
                                    </div>                                     


                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Paterno:</label>
                                            <input type="text" class="form-control" id="padpater" maxlength="30" name="padpater" 
                                                   placeholder="Ingresar Apellido Paterno" >

                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Materno:</label>
                                            <input type="text" class="form-control" id="padmater" maxlength="30" name="padmater" 
                                                   placeholder="Ingresar Apellido Materno" >

                                        </div>  
                                    </div>                                   
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Nombre(s):</label>
                                            <input type="text" class="form-control" id="padnom" maxlength="50" name="padnom" 
                                                   placeholder="Ingresar Nombres" >

                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Parentesco:</label>
                                            <select name="padparentesco" id="padparentesco"   class="form-control" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="P"  selected="">PAPÁ </option>
                                                <option value="M"  >MAMÁ </option>
                                                <option value="A" >ABUELO </option>
                                                <option value="B" >ABUELA </option>
                                                <option value="T" >TÍO </option>
                                                <option value="U" >TÍA </option>
                                                <option value="O" >OTROS </option>                                               
                                            </select>

                                        </div>                                        
                                    </div>  

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Correo Electrónico:</label>
                                            <input type="text" class="form-control" id="pademail" maxlength="100" name="pademail"
                                                   placeholder="Ingresar Correo Electrónico" >
                                        </div>
                                    </div>                                      
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Número de Teléfono:</label>
                                            <input type="text" class="form-control" id="padtelefono" minlength="7"  maxlength="8" name="padtelefono"
                                                   placeholder="Ingresar Telefono">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Número de Celular:</label>
                                            <input type="tel" class="form-control" id="padcelu" minlength="8" maxlength="9"   name="padcelu"
                                                   placeholder="Ingresar Celular" >
                                        </div>
                                    </div>      
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Dirección:</label>
                                            <input type="text" class="form-control" id="paddireccion" maxlength="150" name="paddireccion" 
                                                   placeholder="Ingresar Dirección">
                                        </div>
                                    </div>                                          
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">RUC:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="padruc" minlength="11"  maxlength="11" name="padruc" 
                                                       placeholder="Ingresar RUC" >
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchrucpapa" onclick="javascript:consultar_ruc($('#padruc').val(), 'P', 'padruc');"></span></span>                                                
                                            </div>	
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Razón Social:</label>
                                            <input type="text" class="form-control" id="padrazon"  name="padrazon" maxlength="100" 
                                                   placeholder="Ingresar Razón Social" >
                                        </div>
                                    </div>                                     

                                    <div class="form-group">
                                        <div class="col-sm-12 checkbox">
                                            <label>
                                                <input type="checkbox" value="S" id="chkpapa" name="chkpapa">
                                                Desea recibir comunicados publicados por el Centro Educativo.
                                            </label>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS MADRE :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-3">
                                <div id="form-step-2" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <div class="col-sm-12" style="text-align: right">
                                            <button type="button" id="btnMemoriaMamaCopy" style="display:none;" name="btnMemoriaMamaCopy" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Copiar Datos en Memoria"><i class="glyphicon glyphicon-copy"></i>Copiar</button>
                                            <button type="button" id="btnMemoriaMamaPaste" style="display:none;" name="btnMemoriaMamaPaste" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Pegar Datos almacendo Memoria"><i class="glyphicon glyphicon-paste"></i>Pegar</button>
                                        </div>
                                    </div>                                       
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label for="email">Tipo de documento:</label>
                                            <select name="tipodocmad" id="tipodocmad"   class="form-control" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="1" selected=""  >LE/DNI </option>
                                                <option value="2"  >CARNET EXT. </option>        
                                                <option value="3"  >PASAPORTE </option>   
                                            </select>
                                        </div>                                        
                                        <div class="col-sm-4">
                                            <label for="email">Número de documento:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="dnimater" minlength="8"  maxlength="12" name="dnimater" 
                                                       placeholder="Ingresar DNI" >
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchpadre" onclick="javascript:consultar_dni($('#dnimater').val(), 'M', 'dnimater');"></span></span>
                                            </div>
                                        </div>  

                                        <div class="col-sm-6">
                                            <label for="email">Fecha de Nacimiento:</label>
                                            <input type="date" class="form-control" id="madfecnac"  name="madfecnac"
                                                   placeholder="dd/mm/yyyy">

                                        </div>                                     
                                    </div>                                     
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Paterno:</label>
                                            <input type="text" class="form-control" id="madpater" maxlength="30" name="madpater"
                                                   placeholder="Ingresar Apellido Paterno">

                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Materno:</label>
                                            <input type="text" class="form-control" id="madmater" maxlength="30" name="madmater"
                                                   placeholder="Ingresar Apellido Materno" >

                                        </div>  
                                    </div>                                   
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Nombre(s):</label>
                                            <input type="text" class="form-control" id="madnom" maxlength="50" name="madnom"
                                                   placeholder="Ingresar Nombres" >

                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Parentesco:</label>
                                            <select name="madparentesco"  class="form-control" name="madparentesco" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="P" >PAPÁ </option>
                                                <option value="M" selected=""  >MAMÁ </option>
                                                <option value="A" >ABUELO </option>
                                                <option value="B" >ABUELA </option>
                                                <option value="T" >TÍO </option>
                                                <option value="U" >TÍA </option>
                                                <option value="O" >OTROS </option>                                                
                                            </select>

                                        </div>                                        
                                    </div>  
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Correo Electrónico:</label>
                                            <input type="text" class="form-control" id="mademail" maxlength="50" name="mademail"
                                                   placeholder="Ingresar Correo Electrónico" >
                                        </div>
                                    </div>                                      
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Número de Teléfono:</label>
                                            <input type="text" class="form-control" id="madtelefono" minlength="7" maxlength="8"  name="madtelefono"
                                                   placeholder="Ingresar Teléfono">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Número de Celular:</label>
                                            <input type="tel" class="form-control" id="madcelu" minlength="7" maxlength="9" name="madcelu"
                                                   placeholder="Ingresar Celular" >
                                        </div>
                                    </div>      
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Dirección:</label>
                                            <input type="text" class="form-control" id="maddireccion" maxlength="150" name="maddireccion" 
                                                   placeholder="Ingresar Dirección">

                                        </div>
                                    </div>                                          
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">RUC:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="madruc" minlength="11"  maxlength="11" name="madruc" 
                                                       placeholder="Ingresar RUC"  >
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchrucmama" onclick="javascript:consultar_ruc($('#madruc').val(), 'M', 'madruc');"></span></span>                                                
                                            </div>		
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Razón Social:</label>
                                            <input type="text" class="form-control" id="madrazon"  name="madrazon"
                                                   placeholder="Ingresar Razón Social" >
                                        </div>
                                    </div>                                     

                                    <div class="form-group">
                                        <div class="col-sm-12 checkbox">
                                            <label>
                                                <input type="checkbox" value="S" id="chkmama" name="chkmama"> 
                                                Desea recibir comunicados publicados por el Centro Educativo.
                                            </label>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS APODERADO :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-4" class="">
                                <div id="form-step-3" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div style="float: right">
                                                <label style="font-weight: bold">
                                                    <input type="radio"   id="chkJalar1" onclick="js_jalarDatos('P');" name="chkJalar" >
                                                    Copiar Datos del Padre
                                                </label>
                                                <label style="font-weight: bold">
                                                    <input type="radio"  id="chkJalar2" onclick="js_jalarDatos('M');" name="chkJalar">
                                                    Copiar Datos de la Madre
                                                </label>                   
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <div class="col-sm-2">
                                            <label for="email">Tipo de documento:</label>
                                            <select name="tipodocapo" id="tipodocapo"   class="form-control" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="1" selected=""  >LE/DNI </option>
                                                <option value="2"  >CARNET EXT. </option>        
                                                <option value="3"  >PASAPORTE </option>   
                                            </select>
                                        </div>                                        
                                        <div class="col-sm-4">
                                            <label for="email">Número de documento:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="dniapo" minlength="8"  maxlength="12" name="dniapo" 
                                                       placeholder="Ingresar DNI" >
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchpadre" onclick="javascript:consultar_dni($('#dniapo').val(), 'AP', 'dniapo');"></span></span>
                                            </div>
                                        </div>  

                                        <div class="col-sm-6">
                                            <label for="email">Fecha de Nacimiento:</label>
                                            <input type="date" class="form-control" id="apofecnac"  name="apofecnac"
                                                   placeholder="dd/mm/yyyy">
                                        </div>                                     
                                    </div>                                     
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Paterno:</label>
                                            <input type="text" class="form-control" id="apopater" maxlength="30" name="apopater"
                                                   placeholder="Ingresar Apellido Paterno" >
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Apellido Materno:</label>
                                            <input type="text" class="form-control" id="apomater" maxlength="30" name="apomater"
                                                   placeholder="Ingresar Apellido Materno" >
                                        </div>  
                                    </div>                                   
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Nombre(s):</label>
                                            <input type="text" class="form-control" id="aponom" maxlength="50" name="aponom"
                                                   placeholder="Ingresar Nombres" >
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Parentesco:</label>
                                            <select name="apoparentesco"   class="form-control" id="apoparentesco">
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="P" >PAPÁ </option>
                                                <option value="M"  >MAMÁ</option>
                                                <option value="A" >ABUELO </option>
                                                <option value="B" >ABUELA </option>
                                                <option value="T" >TÍO </option>
                                                <option value="U" >TÍA </option>
                                                <option value="O" >OTROS </option>                                                
                                            </select>
                                        </div>                                        
                                    </div>  
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Correo Electrónico:</label>
                                            <input type="text" class="form-control" id="apoemail" maxlength="50"  name="apoemail"
                                                   placeholder="Ingresar Correo Electrónico" >
                                        </div>
                                    </div>                                      
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Número de Teléfono:</label>
                                            <input type="text" class="form-control" id="apotelefono" minlength="7"  maxlength="8" name="apotelefono"
                                                   placeholder="Ingresar Teléfono">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Número de Celular:</label>
                                            <input type="tel" class="form-control" id="apocelu" minlength="9" maxlength="9" name="apocelu"
                                                   placeholder="Ingresar Celular" >
                                        </div>
                                    </div>      
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="email">Dirección:</label>
                                            <input type="text" class="form-control" id="apodireccion" maxlength="150" name="apodireccion"
                                                   placeholder="Ingresar Dirección">
                                        </div>
                                    </div>                                          
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">RUC:</label>												  
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="aporuc" minlength="11"  maxlength="11" name="aporuc" 
                                                       placeholder="Ingresar RUC">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" style="cursor: pointer" id="btnsearchrucapo" onclick="javascript:consultar_ruc($('#aporuc').val(), 'A', 'aporuc');"></span></span>                                                
                                            </div>											
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Razón Social:</label>
                                            <input type="text" class="form-control" id="aporazon"  name="aporazon"
                                                   placeholder="Ingresar Razón Social" >
                                        </div>
                                    </div>                                     

                                    <div class="form-group">
                                        <div class="col-sm-12 checkbox">
                                            <label>
                                                <input type="checkbox" value="S" id="chkapoderado" name="chkapoderado">
                                                Desea recibir comunicados publicados por el Centro Educativo.
                                            </label>
                                        </div>    
                                    </div>
                                </div>                                
                            </div>
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS AULA :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-5" class="">
                                <div id="form-step-4" role="form" data-toggle="validator">

                                    <div class="form-group" id="lblaulanterior" style="display:none;">
                                        <div class="col-sm-12">
                                            <label for="email">Aula Anterior:</label>
                                            <input type="text" class="form-control" id="aulaant" name="aulaant" value="" readonly="">
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="email">Nivel:</label>
                                            <select name="cb_nivel"  id="cb_nivel" required="" class="form-control" onchange="cargaListaGrado();" >
                                                <option value="" >:::: SELECCIONE :::: </option>
                                                <option value="I" >INICIAL </option>
                                                <option value="P"  >PRIMARIA </option>
                                                <option value="S" >SECUNDARIA </option>                                              
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Grado:</label>
                                            <select name="cb_grado" id="cb_grado"  required="" class="form-control" onchange="cargarListarAulas();" >         
                                                <option value="">:: SELECCIONE ::</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>  

                                    <div class="form-group">
                                        <div class="col-sm-12">                                            
                                            <ul class="list-group"  id="list-group" >  
                                            </ul>            
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">                                            
                                            <label for="email">Observación:</label>
                                            <textarea class="form-control" rows="2" id="txtcomentarios" name="txtcomentarios"></textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>   
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS RESUMEN FINAL :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-6" class="">
                                <div id="form-step-5" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <h5><b>DETALLES:</b></h5>


                                            <div class="panel panel-default">
                                                <div class="panel-heading">Cursos a Cargo</div>
                                                <div class="panel-body" id="lstMarcacurso" >
                                                </div>
                                            </div>

                                            <div class="panel panel-default">
                                                <div class="panel-heading">Pensiones Pendientes</div>
                                                <div class="panel-body" id="lstMarcadeuda">
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-sm-6">
                                            <h5><b>ARCHIVOS:</b></h5>
                                            <ul class="list-group"  >
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <label>
                                                        Contrato de Servicio Educativo
                                                        &nbsp;&nbsp;&nbsp;<span style='font-size:16px; color:blue;cursor:pointer;'  id="btnImpServ" class='glyphicon glyphicon-print'  data-toggle='tooltip' title='Imprimir'></span>
                                                    </label>
                                                    <div style="float: right">
                                                        <label style="font-weight: bold">
                                                            <input type="radio"   id="chkPrint1"  name="chkPrint[]">
                                                            PADRE
                                                        </label>
                                                        <label style="font-weight: bold">
                                                            <input type="radio"  id="chkPrint2" name="chkPrint[]">
                                                            MADRE
                                                        </label>                   
                                                        <label style="font-weight: bold">
                                                            <input type="radio"  id="chkPrint3" name="chkPrint[]" >
                                                            APODERADO
                                                        </label> 
                                                    </div>

                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        Constancia de Vacante
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size:16px; color:blue;cursor:pointer;'   id="btnImpConst" class='glyphicon glyphicon-print'  data-toggle='tooltip' title='Imprimir'></span>
                                                    </label>
                                                    <div style="float: right">
                                                        <label style="font-weight: bold">
                                                            <input type="radio"   id="chkPrint4"  name="chkPrint1[]">
                                                            PADRE
                                                        </label>
                                                        <label style="font-weight: bold">
                                                            <input type="radio"  id="chkPrint5" name="chkPrint1[]">
                                                            MADRE
                                                        </label>                   
                                                        <label style="font-weight: bold">
                                                            <input type="radio"  id="chkPrint6" name="chkPrint1[]" >
                                                            APODERADO
                                                        </label> 
                                                    </div>                                                    
                                                </li>                                                       
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        Utiles escolares
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size:16px; color:blue;cursor:pointer;'  class='glyphicon glyphicon-download-alt'  data-toggle='tooltip' title='Descargar'></span>
                                                    </label>
                                                </li>                                               
                                            </ul>            
                                        </div>                                          
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <h5><b>DOCUMENTOS:</b></h5>
                                            <ul class="list-group"  >
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <label>
                                                        <input type="checkbox" value="D001" valordocu="Libreta de Notas" id="chkapoderado_1" name="chkDocumentos[]">
                                                        Libreta de Notas
                                                    </label>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        <input type="checkbox" value="D002" valordocu="Certificado de Estudios" id="chkapoderado_2" name="chkDocumentos[]" onclick="$('#txtdsc').val('');" >
                                                        Certificado de Estudios
                                                    </label>
                                                    <input type="text" class="form-control" style="width: 50%" id="txtdsc" name="txtdsc" value="" placeholder="OBSERVACION...." maxlength="80">
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        <input type="checkbox" value="D003" valordocu="Constancia de Matrícula (SIAGIE)" id="chkapoderado_3" name="chkDocumentos[]">
                                                        Constancia de Matrícula (SIAGIE)
                                                    </label>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        <input type="checkbox" value="D007" valordocu="Ficha de Matrícula (SIAGIE)" id="chkapoderado_7" name="chkDocumentos[]">
                                                        Ficha de Matrícula (SIAGIE)
                                                    </label>
                                                </li>                                                
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        <input type="checkbox" value="D004" valordocu="Copia de DNI (Alumno)" id="chkapoderado_4" name="chkDocumentos[]">
                                                        Copia de DNI (Alumno)
                                                    </label>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        <input type="checkbox" value="D005" valordocu="Copia de DNI (Padres)" id="chkapoderado_5" name="chkDocumentos[]">
                                                        Copia de DNI (Padres)
                                                    </label>
                                                </li>
                                                <!--
                                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                    <label>
                                                        <input type="checkbox" value="D006" valordocu="Copia de Partida" id="chkapoderado_6" name="chkDocumentos[]">
                                                        Copia de Partida
                                                    </label>
                                                </li>                   
                                                -->
                                            </ul>            
                                            <div class="help-block with-errors"></div>
                                        </div>                                                                                                  
                                    </div>
                                </div>
                            </div>
                            <!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: BLOQUE DATOS PAGO :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                            <div id="step-7" class="">
                                <div id="form-step-6" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-6">
                                                    <label for="email">Responsable de Pago:</label>
                                                    <select name="cmbResponsablePago" id="cmbResponsablePago"  required="" class="form-control"  onchange="cambiaResponsable()" >
                                                        <option value="" >:::: SELECCIONE :::: </option>
                                                        <option value="P" >PADRE </option>
                                                        <option value="M"  >MADRE </option>
                                                        <option value="A" >APODERADO </option>                                              
                                                    </select>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="email">&nbsp;</label>
                                                    <input type="text" class="form-control" id="nomcliente" readonly="" name="nomcliente">
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-10 radio">
                                                    <label style="font-weight: bold">
                                                        <input type="radio" value="01"  id="chkComprobante1" name="chkTipoPago[]" onclick="generar_comprobante('01')">
                                                        RECIBO
                                                    </label>
                                                    <label style="font-weight: bold">
                                                        <input type="radio" value="02" id="chkComprobante2" name="chkTipoPago[]" onclick="generar_comprobante('02')">
                                                        BOLETA
                                                    </label>                   
                                                    <label style="font-weight: bold">
                                                        <input type="radio" value="03" id="chkComprobante3" name="chkTipoPago[]" onclick="generar_comprobante('03')">
                                                        FACTURA
                                                    </label>         
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class="btn btn-primary" type="button" style="font-size: 16px;width: 100%; font-weight: bold;" id="lblcomprobante">
                                                        &nbsp;
                                                    </button>  
                                                </div>    
                                            </div>
                                        </div>                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-3">
                                                    <select  id="idmedio" name="idmedio"   required="" class="form-control"  >
                                                        <option value="" >:::: SELECCIONE :::: </option>
                                                        <option value="1" >OFICINA </option>   
                                                        <option value="2" >IZIPAY </option> 
                                                        <option value="3" >TRANSFERENCIA - BCP </option> 
                                                        <option value="4" >TRANSFERENCIA - BBVA </option>  
                                                        <option value="5" >RECAUDO - BCP </option> 
                                                    </select>      
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="text" class="form-control" id="voucher" name="voucher" maxlength="20" placeholder="Num. Voucher" >
                                                </div>
                                                <div class="col-sm-5">
                                                    &nbsp;
                                                </div>
                                                <div class="col-sm-2" style="text-align: right" >
                                                    <button class="btn btn-primary" type="button" style="font-size: 16px;width: 100%; font-weight: bold;">
                                                        <?= date("d/m/Y") ?>
                                                    </button>  
                                                </div>       
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <hr/>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <!--< <div class="col-sm-6">
                                                     <ul class="list-group"  id="list-group">
                                                         <li class="list-group-item d-flex justify-content-between align-items-center" >
                                                             <input type="checkbox" value="02-03-300" id="chkPension_1" checked=""  name="chkPension[]">&nbsp;MATRICULA - 2020
                                                             <span class="badge badge-primary badge-pill">S/300.00</span>
                                                         </li>                                                       
                                                     </ul>                                                       
                                                 </div>-->
                                                <div class="col-sm-6">  
                                                    <label style="background: #fff9c4">
                                                        <input type="checkbox" value="1"  id="chkExoneraPago" name="chkExoneraPago" onclick="js_exonerar(this.checked)">
                                                        EXONERAR PAGO DE MATRÍCULA
                                                    </label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div style="float: right;font-weight: bold; font-size: 18px">TOTAL : &nbsp;</div>
                                                </div>                                                    
                                                <div class="col-sm-2">
                                                    <input type="text" id="totalcomp" onkeypress="return validaNumeros(event, this);" style="background-color: #fff9c4;font-weight: bold;font-size: 18px;text-align: right" name="totalcomp" value="0" maxlength="3" class="form-control" >
                                                </div>
                                            </div>   
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-6">
                                                    <textarea class="form-control" rows="2" id="txtobsExoneracion" name="txtobsExoneracion" ></textarea>
                                                </div>                                                      
                                            </div>
                                        </div>
                                        <br>
                                        <hr/>
                                        <br>                                                
                                    </div>  
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>

        </form>                
    </div>
</div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
<script src="<?php echo base_url('assets/wizard/js/jquery.smartWizard.js') ?>"></script>



