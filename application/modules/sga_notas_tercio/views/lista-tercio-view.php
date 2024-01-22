<style type="text/css">
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
        top: 20%;
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
    .modal-dialog {
        width: 40%;
    }    
    .modal-dialog-sm {
        position:absolute;
        top:50% !important;
        transform: translate(0, -50%) !important;
        -ms-transform: translate(0, -50%) !important;
        -webkit-transform: translate(0, -50%) !important;
        margin:auto;
        width:40%;
    }        
</style>

<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Listado de Notas - Tercio</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form  id="formPrincipal" action=""  method="POST" class="form-horizontal"   >      
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <div class="form-group">
            <label  class="col-sm-2 col-form-label">Años :</label>
            <div class="col-sm-2">
                <select name="idanio" id="idanio" class="form-control" >
                    <option value="">TODOS</option>
                    <option value="5">5 AÑOS</option>
                    <option value="4">4 AÑOS</option>
                    <option value="3">3 AÑOS</option>
                    <option value="2">2 AÑOS</option>
                    <option value="1">1 AÑOS</option>                     
                </select>
            </div>
            <label  class="col-sm-2 col-form-label">Alumno :</label>
            <div class="col-sm-4">
                <input type="text" class="form-control"  id="txtAlumnoSearch" name="txtAlumnoSearch" placeholder="Escriba Apellido del Alumno" />                  
            </div>   
            <div class="col-sm-2">
                <!-- <button type="button"  id="btnSearch" class="btn btn-primary btn-md">
                     <span class="glyphicon glyphicon-search"></span> 
                 </button>    -->
                <button type="button"  id="btnClear" class="btn btn-success btn-md">
                    <span class="glyphicon glyphicon-erase"></span> 
                </button>
                <button type="button"  id="btnImprimir" class="btn btn-danger btn-md">
                    <span class="glyphicon glyphicon-print"></span>
                </button>                        
            </div>              
        </div>

        <button type="button"  id="btnBuscar" class="btn btn-primary btn-md">
            <span class="glyphicon glyphicon-search"></span> Mostrar Registros
        </button>
        <button type="button"  id="btnAgregar" class="btn btn-success btn-md">
            <span class="glyphicon glyphicon-send"></span> Agregar Notas
        </button>
    </form>
</center>

<br>
<hr/>
<br>
<div id="divTblAlumnos">
    <table class="table table-striped table-bordered"    id="viewControl" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">Puesto</th>
                <th style="width: 10%;text-align: center">Codigo</th>
                <th style="width: 45%;text-align: center">Apellidos y Nombre</th>       
                <th style="width: 10%;text-align: center">Años</th>  
                <th style="width: 10%;text-align: center">Puntaje</th>
                <th style="width: 10%;text-align: center">Promedio</th>   
                <th style="width: 10%;text-align: center">Conf</th>
            </tr>
        </thead>
        <tbody>
        </tbody>        
    </table>
</div>
<br/>
<div id="fade"></div>
<div id="modal">
    <img id="loader" src="<?= BASE_URL ?>/images/loader.gif" width="150px" height="150px" />
</div>

<!-- 1. Modals : Para Mostrar los Años de los Alumnos -->
<div class="modal fade" id="modal_detalle" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">PROMEDIOS POR AÑOS</h4>
            </div>
            <div id="PrintDivModal">
                <div class="modal-body form">

                    <form action="#" id="form2" class="form-horizontal">
                        <div class="form-body">  
                            <div class="form-group">
                                <label  class="col-xs-12 col-sm-4 col-form-label">CODIGO :</label>
                                <div class="col-xs-12 col-sm-8" id="divcodigo"></div>
                            </div>
                            <div class="form-group">
                                <label  class="col-xs-12 col-sm-4 col-form-label">ALUMNO :</label>
                                <div class="col-xs-12 col-sm-8" id="divnomcomp"></div>
                            </div>     
                            <div class="form-group">
                                <label  class="col-xs-12 col-sm-4 col-form-label">AULA :</label>
                                <div class="col-xs-12 col-sm-8" id="divaula"></div>
                            </div>                             
                            <div class="form-group">
                                <div class="col-md-12"> 
                                    <table class="table table-striped table-bordered"    id="viewdetalle" style="width: 100%">
                                        <thead>
                                            <tr class="tableheader">
                                                <th style="width: 10%;text-align: center">#</th>
                                                <th style="width: 50%;text-align: center">AULA</th>       
                                                <th style="width: 20%;text-align: center">PUNT</th>  
                                                <th style="width: 20%;text-align: center">PROM</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>        
                                    </table>
                                </div>                                                                                 
                            </div> 
                        </div>
                </div>           
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnprint" class="btn btn-success" >Imprimir</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>                
        </div>
    </div>
</div>

<!-- 2. Modals : Para Agregar Notas de Tercio a Alumnos Nuevos  -->
<div class="modal fade" id="modal_puntaje" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">AGREGAR NOTA Y PUNTAJE</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form3" class="form-horizontal">
                    <div class="form-body">  
                        <div class="form-group">
                            <label  class="col-xs-12 col-sm-4 col-form-label">CODIGO :</label>
                            <div class="col-xs-12 col-sm-8" >
                                <select class="form-control" id="cbAlumno">
                                    <option value="0">NINGUNA</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-xs-12 col-sm-4 col-form-label">AULA :</label>
                            <div class="col-xs-12 col-sm-8" ></div>
                        </div>  
                        <div class="form-group">
                            <hr class="clearfix" />
                        </div>
                        <div class="form-group">
                            <label  class="col-xs-12 col-sm-4 col-form-label">AÑO :</label>
                            <div class="col-xs-12 col-sm-8" >
                                <select class="form-control" id="cbAnio">
                                    <option value="0">NINGUNA</option>
                                    <option value="1">AÑO 1</option>
                                    <option value="2">AÑO 2</option>
                                    <option value="3">AÑO 3</option>
                                    <option value="4">AÑO 4 </option>
                                    <option value="5">AÑO 5</option>
                                </select>                                
                            </div>
                        </div>                           
                        <div class="form-group">
                            <label  class="col-xs-12 col-sm-4 col-form-label">CURSO :</label>
                            <div class="col-xs-12 col-sm-8" >
                                <select class="form-control" id="cbCurso">
                                    <option value="0">NINGUNA</option>
                                </select>                                
                            </div>
                        </div>     

                        <div class="form-group">
                            <label  class="col-xs-12 col-sm-4 col-form-label">PROMEDIO :</label>
                            <div class="col-xs-12 col-sm-8" >
                                <input type="text" class="form-control" value="" id="txtpromedio" maxlength="3"  />                       
                            </div>
                        </div>                            
                        <div class="form-group">
                            <label  class="col-xs-12 col-sm-4 col-form-label">PUNTAJE :</label>
                            <div class="col-xs-12 col-sm-8" >
                                <input type="text" class="form-control" value="" id="txtpuntaje" maxlength="3"  />                       
                            </div>
                        </div>                                
                    </div>                        

                </form>
            </div>           
            <div class="modal-footer">
                <button type="button" id="btnSave" class="btn btn-success" >Grabar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>                
        </div>
    </div>
</div>
<script src="<?php echo base_url('js/jquery.PrintArea.js') ?>"></script>