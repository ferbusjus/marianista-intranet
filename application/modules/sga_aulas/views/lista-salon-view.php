
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Lista de Aulas</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <table class="table table-bordered table-striped" id="tbl_lista_aulas">
        <thead>
            <tr>
                <th style="width:10%;text-align: center;">C&oacute;digo</th>
                <th style="width:70%;text-align: center;">Descripcion del Aula</th>
                <th style="width:10%;text-align: center;">Cant.</th>
                <th style="width:10%;text-align: center;">Opc.</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</center>

<!-- Inicio : Modal Matricula-->         
<div class="modal fade" id="popAlumnos" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Listado de Alumno</h4>
            </div>
            <!-- Contenedor Tabs -->
          <!--  <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab1">LISTA DE ALUMNOS</a></li>
            </ul>
          -->
            <!-- Contenedor Tabs -->
            <!-- Tabs Independientes -->
            <div class="tab-content">                        
                <!-- INI :  Contenedor Tab 11 -->
                <!-- <div id="tab1" class="tab-pane fade in active">-->
                    <div class='box-body'>
                        <table class="table table-bordered table-striped" id="tbl_lista_alumnos" >
                            <thead>
                                <tr>
                                    <th style="width:10%;text-align: center;">C&oacute;digo</th>
                                    <th style="width:80%;text-align: center;">Apellidos y Nombres</th>
                                    <th style="width:10%;text-align: center;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                  
              <!--  </div>-->
            </div>    

        </div>
    </div>
</div>

