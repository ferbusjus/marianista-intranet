<?php $this->load->view('lista-alumno-matricula-js') ?>  
<!-- Modal Matricula Alumno-->
<!--<div class="modal fade" id="modalMatricula" role="dialog">-->
<div class="modal fade" id="modalMatricula" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Matricular Alumno</h4>
            </div>
            <!-- Contenedor Tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab1">1. MATRICULA</a></li>
                <li ><a data-toggle="tab" href="#tab2">2. PAGOS</a></li>
                <li ><a data-toggle="tab" href="#tab3">3. CURSO A CARGO</a></li>
                <li ><a data-toggle="tab" href="#tab4">4. DOCUMENTOS</a></li>
            </ul>
            <!-- Contenedor Tabs -->
            <!-- Tabs Independientes -->
            <form class="form-horizontal" method="post" id="frmMatricula" name="frmMatricula"  > 
                <div class="tab-content">                        
                    <!-- INI :  Contenedor Tab 1 -->
                    <div id="tab1" class="tab-pane fade in active">
                        <div class="modal-body">
                            <input type="hidden"  id="txtalucod" name="txtalucod" value="" >
                            <input type="hidden"  id="hdni" name="hdni" value="" >
                            <input type="hidden"  id="haccion" name="haccion" value="" >
                            <input type="hidden"  id="hestado" name="hestado" value="" >
                            <input type="hidden"  id="hinstru" name="hinstru" value="" >
                            <input type="hidden"  id="hanio" name="hanio" value="" >
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">DNI:</label> 
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" id="lbldni" name="lbldni" maxlength="8" value="" >
                                </div>
                                <label  class="col-sm-2 control-label">Nº Libro:</label> 
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="lbllibro" name="lbllibro" maxlength="5" value="" >
                                </div>                                
                            </div>                        
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">Apellidos:</label> 
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="lblapellidos" name="lblapellidos" value="" disabled="">
                                </div>
                            </div>                                 
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">Nombres:</label> 
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="lblnombres" name="lblnombres" value="" disabled="">
                                </div>
                            </div>        
                            <div class="form-group row" id="divold">
                                <div class="offset-xs-3 col-xs-3 text-right"><b>Anterior: </b></div>
                                <div class="offset-xs-3 col-xs-5 text-left" id="lblnemo"></div>                   
                            </div>   
                            <div class="form-group" >
                                <label  class="col-sm-3 control-label" id="divactual" >Nuevo:</label> 
                                <div class="col-sm-2">
                                    <select class="form-control" style="width:100px" disabled="disabled" id="cb_nivel"  name="cb_nivel" onchange="javascript:cargaGrado()"   >
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" style="width:100px" disabled="disabled" id="cb_grado"  name="cb_grado" onchange="javascript:cargaAula()"  >
                                    </select>                             
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" style="width:120px" id="cb_aula"  name="cb_aula"  >
                                    </select>                             
                                </div>
                            </div>
                            <hr/>
                            <br>
                            <div class="form-group" >
                                <label  class="col-sm-3 control-label" >&nbsp;</label> 
                                <div class="col-sm-7">
                                    <div class="custom-control custom-radio custom-control-inline">     
                                        <input class="form-check-input" type="radio" name="rbdLineaOptions" checked="checked" id="rbdOptions1" value="01" >
                                        <label class="form-check-label" for="inlineRadio1">RECIBO</label>                                                                    
                                        <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions2" value="02" >
                                        <label class="form-check-label" for="inlineRadio1">BOLETA</label>           
                                        <input class="form-check-input" type="radio" name="rbdLineaOptions" id="rbdOptions3" value="03">
                                        <label class="form-check-label" for="inlineRadio1">FACTURA</label>       
                                    </div>     
                                </div>
                            </div>                          
                            <div class="form-group" >
                                <label  class="col-sm-3 control-label" >Monto :</label> 
                                <div class="col-sm-2">   
                                    <input type="text" class="form-control" id="txttotales" style="font-weight: bold;font-size: 17px; text-align: right;background-color: palegreen;" name="txttotales" value="300" >
                                </div>
                                <label  class="col-sm-3 control-label" style="text-align: left" >Nuevos Soles.</label> 
                            </div>     
                            <hr/>
                        </div> 
                    </div>
                    <div id="tab2" class="tab-pane fade">
                        <div id="divListaPagos" ></div>  
                    </div>
                    <div id="tab3" class="tab-pane fade">
                        <div id="viewGridCursos" ></div>
                    </div>               
                    <div id="tab4" class="tab-pane fade">
                        <br>
                        <div class="form-group">
                            <label for="codigo" class="col-sm-3 control-label">Documentos Entregados</label>
                            <div class="col-sm-8">
                                <em><input type="checkbox"  id="chdoc1"  value="D001" alias="chkDocumentos"  name="chkDocumentos[]" />&nbsp; Libreta de Notas</em><br>
                                <em><input type="checkbox"  id="chdoc2" value="D002" alias="chkDocumentos"  name="chkDocumentos[]" />&nbsp; Certificado de Estudios</em><br>
                                <em><input type="checkbox"  id="chdoc3" value="D003" alias="chkDocumentos"  name="chkDocumentos[]" />&nbsp; Ficha Unica de Matricula</em><br>
                                <em><input type="checkbox"  id="chdoc4" value="D004" alias="chkDocumentos"  name="chkDocumentos[]" />&nbsp; Copia de DNI (Alumno)</em><br>
                                <em><input type="checkbox"  id="chdoc5" value="D005" alias="chkDocumentos"  name="chkDocumentos[]" />&nbsp; Copia de DNI (Padres)</em><br>
                                <em><input type="checkbox"  id="chdoc6" value="D006" alias="chkDocumentos"  name="chkDocumentos[]" />&nbsp; Copia de Partida</em><br>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="estado" class="col-sm-3 control-label">Comentarios</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="txtcomentarios" name="txtcomentarios" style="width: 100%" rows="2" ></textarea>
                            </div>
                        </div> 
                    </div>                           
                    <!-- Tabs Independientes -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnclose" >Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btngrabar" disabled="">Grabar Datos</button>
                    </div>

                    <!--</div>-->


                </div>
            </form>
        </div>
    </div>
</div>