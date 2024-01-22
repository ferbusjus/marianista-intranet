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
    .error {
        color: #ac2925;
        margin-bottom: 15px;
    }
    .event-tooltip {
        width:250px;
        background: rgba(0, 0, 0, 0.85);
        color:#FFF;
        padding:10px;
        position:absolute;
        z-index:10001;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;

    }    
</style>


<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<link href="<?php echo base_url('css/fullcalendar.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/multiselect/bootstrap-multiselect.css') ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/timepicker/css/bootstrap-timepicker.min.css') ?>" rel="stylesheet">
<!-- Custom CSS -->
<style>
    #calendar {
        max-width: 100%;
    }
    /*  .col-centered{
          float: none;
          margin: 0 auto;
      }*/

</style>

<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Calendario de Citas</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />

    <body>
        <!-- Page Content -->
        <div class="container">

            <div class="row">
                <div class="col-lg-12 text-center">
                    <!--<h1>FullCalendar PHP MySQL</h1>-->
                    <!--<p class="lead">Completa con rutas de archivo predefinidas que no tendrás que cambiar!</p>-->
                    <div id="calendar" class="col-centered">
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <!-- Modal -->
            <div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form class="form-horizontal" method="POST" >
                            <input type="hidden" name="txttitulo" id="txttitulo"  value="PENDIENTE" >
                            <input type="hidden" name="start" id="start" >
                            <input type="hidden" name="end"  id="end" >
                            <input type="hidden" name="txtalucod"  id="txtalucod" >
                            <input type="hidden" name="txtnemo" id="txtnemo" >
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" >Agendar Cita</h4>
                            </div>
                            <div class="modal-body">
                                <div class="alert" ></div>
                                <div class="error"></div>
                                <div class="form-group">
                                    <label for="title" class="col-sm-2 control-label">Alumno</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="txtAlumnoSearch" class="form-control" id="txtAlumnoSearch" placeholder="ESCRIBA EL ALUMNO">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="title" class="col-sm-2 control-label">Motivo</label>
                                    <div class="col-sm-10">
                                        <select name="cbmotivo"   id="cbmotivo" multiple="multiple">
                                            <?php foreach ($lstmotivo as $row) { ?>
                                                <option value="<?= $row->idmotivo ?>"><?= $row->idmotivo . " : " . strtoupper($row->descripcion) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title" class="col-sm-2 control-label">Asisten</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="txtasiste" class="form-control" id="txtasiste" placeholder="Ejem. PAPÁ / MAMÁ / TIOS">
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label for="title" class="col-sm-2 control-label">Observacion</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="description" name="description" placeholder="Escriba algun comentario y/o Observacion"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="color" class="col-sm-2 control-label">Prioridad</label>
                                    <div class="col-sm-4">
                                        <select name="color" class="form-control" id="color">
                                            <option  value="#FF0000" style="color:#FF0000;">&#9724; Muy Urgente</option>
                                            <option  value="#008000" style="color:#008000;" selected="selected">&#9724; Urgente</option>						  
                                            <option  value="#FFD700" style="color:#FFD700;">&#9724; Normal</option>
                                        </select> 
                                    </div>
                                    <label for="color" class="col-sm-2 control-label">Hora</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="txthora" class="form-control" id="txthora" readonly="" placeholder="">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <!-- Modal -->
            <!-- <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                 <div class="modal-dialog" role="document">
                     <div class="modal-content">
                         <form class="form-horizontal" method="POST" action="sga_calendario/editEvento">
                             <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                 <h4 class="modal-title" id="myModalLabel">Modificar Evento</h4>
                             </div>
                             <div class="modal-body">
 
                                 <div class="form-group">
                                     <label for="title" class="col-sm-2 control-label">Titulo</label>
                                     <div class="col-sm-10">
                                         <input type="text" name="title" class="form-control" id="title" placeholder="Titulo">
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     <label for="color" class="col-sm-2 control-label">Color</label>
                                     <div class="col-sm-10">
                                         <select name="color" class="form-control" id="color">
                                             <option value="">Seleccionar</option>
                                             <option style="color:#0071c5;" value="#0071c5">&#9724; Azul oscuro</option>
                                             <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquesa</option>
                                             <option style="color:#008000;" value="#008000">&#9724; Verde</option>						  
                                             <option style="color:#FFD700;" value="#FFD700">&#9724; Amarillo</option>
                                             <option style="color:#FF8C00;" value="#FF8C00">&#9724; Naranja</option>
                                             <option style="color:#FF0000;" value="#FF0000">&#9724; Rojo</option>
                                             <option style="color:#000;" value="#000">&#9724; Negro</option>
 
                                         </select>
                                     </div>
                                 </div>
                                 <div class="form-group"> 
                                     <div class="col-sm-offset-2 col-sm-10">
                                         <div class="checkbox">
                                             <label class="text-danger"><input type="checkbox"  name="delete"> Eliminar Evento</label>
                                         </div>
                                     </div>
                                 </div>
 
                                 <input type="hidden" name="id" class="form-control" id="id">
 
 
                             </div>
                             <div class="modal-footer">
                                 <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                 <button type="submit" class="btn btn-primary">Guardar</button>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
            -->
        </div>
        <!-- /.container -->
    </body>
</center>  
<!-- FullCalendar -->
<script src="<?php echo base_url('assets/timepicker/js/bootstrap-timepicker.min.js') ?>"></script>     
<script src="<?php echo base_url('assets/multiselect/bootstrap-multiselect.js') ?>"></script>
<script src="<?php echo base_url('assets/moment.min.js') ?>"></script>
<script src="<?php echo base_url('assets/fullcalendar/fullcalendar.min.js') ?>"></script>
<script src="<?php echo base_url('assets/fullcalendar/fullcalendar.js') ?>"></script>
<script src="<?php echo base_url('assets/fullcalendar/locale/es.js') ?>"></script>


