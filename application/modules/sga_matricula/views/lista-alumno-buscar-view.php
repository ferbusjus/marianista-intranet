<?php $this->load->view('lista-alumno-buscar-js') ?>         
<!--<div class="modal fade" id="modalAlumnoFiltro" role="dialog">-->
    <div class="modal fade" id="modalAlumnoFiltro" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Buscar Alumno</h4>
            </div>
            <form class="form-horizontal" method="post" id="frmAlumno" name="frmAlumno" onsubmit="return false;"  > 
              
                <div class="modal-body">

                    <table style="text-align:center;width: 100%" border="0">
                        <tr style="height: 40px;">
                            <td style ="width: 10%;float: center"><b>Filtrar Por:</b> </td>
                            <td style ="width: 20%">
                                <select name="cbfiltro2" id="cbfiltro2" class="form-control">
                                    <option value="">:::: Seleccione ::::</option>
                                    <option value="1">DNI</option>
                                    <option value="2" selected="selected">APELLIDOS</option>
                                </select>
                            </td>   
                            <td colspan="2" style ="width: 60%;text-align: center">
                                <input type="text" value="" id="txtbuscar"  name="txtbuscar" placeholder="Seleccione el Filtro" data-toggle="tooltip" title="Ingrese el texto a Buscar"  class="form-control" maxlength="15"  />
                            </td>    
                            <td style ="width:10%" >
                                <button type="button"   id="btnFiltrar" name="btnFiltrar" data-toggle="tooltip" title="Buscar"  class="btn btn-primary"><i class="glyphicon glyphicon-search" /></i>                    
                            </td>

                        </tr>        
                    </table>

                    <br>
                    <hr/>
                    <br>
                    <div id="divTblPagos2">
                        <table class="table table-striped table-bordered"    id="viewAlumnosFiltro" style="width: 100%">
                            <thead>
                                <tr class="tableheader">
                                    <th style="width: 10%;text-align: center">DNI</th>
                                    <th style="width: 45%;text-align: center">Apellidos y Nombres</th>            
                                    <th style="width: 10%;text-align: center">Estado</th>
                                    <th style="width: 30%;text-align: center">Aula </th> 
                                    <th style="width: 5%;text-align: center">Conf</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>        
                        </table>
                    </div>                    
                </div>
                <!--</div>-->

                <!-- Tabs Independientes -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>                 
                </div>
                <!--</div>-->
            </form>

        </div>
    </div>
</div>        
