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
</style>


<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Control Llenado de Notas</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>


    <form  id="formPrincipal" action=""  method="POST" class="form-horizontal"   >      
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 col-form-label">Nivel :</label>
            <div class="col-sm-2">
                <select name="idnivel" id="idnivel" class="form-control" >
                    <option value="0">SELECCIONE</option>
                    <?php foreach ($lstNivel as $nivel): ?>
                    <option value="<?php echo $nivel->instrucod ?>"><?php echo strtoupper($nivel->instrudes) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <label for="inputEmail3" class="col-sm-2 col-form-label">Bimestre :</label>
            <div class="col-sm-2">
                <select name="idbimestre" id="idbimestre"  disabled="" class="form-control" >
                    <option value="0">SELECCIONE</option>
                    <option value="1">I-BIMESTRE</option>
                    <option value="2">II-BIMESTRE</option>
                    <option value="3">III-BIMESTRE</option>
                    <option value="4">IV-BIMESTRE</option>
                </select>
            </div>   
            <label for="inputEmail3" class="col-sm-2 col-form-label">Unidad :</label>
            <div class="col-sm-2">
                <select name="idunidad" id="idunidad" disabled="" class="form-control" >
                    <option value="0">SELECCIONE</option>
                </select>
            </div>              
        </div>

        <button type="button"  id="btnBuscar" class="btn btn-primary btn-md">
            <span class="glyphicon glyphicon-search"></span> Mostrar Registros
        </button>
        <!--
        <button type="submit"   id="btnRefresh" class="btn btn-success btn-md">
            <span class="glyphicon glyphicon-print"></span> ver Todos
        </button>
        -->
    </form>
</center>

<br>
<hr/>
<br>
<div id="divTblPagos">
    <table class="table table-striped table-bordered"    id="viewControl" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">#</th>
                <th style="width: 20%;text-align: center">Aula</th>       
                <th style="width: 30%;text-align: center">Tutor(a)</th>  
                <th style="width: 5%;text-align: center">T-Regis.</th>
                <th style="width: 5%;text-align: center">T-Carga</th>   
                <th style="width: 30%;text-align: center">Avance</th>
                <th style="width: 5%;text-align: center">Conf</th>
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

<!-- 1. Modal s -->
<div class="modal fade" id="modal_detalle" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">DETALLE POR CURSOS</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form2" class="form-horizontal">
                    <div class="form-body">  
                        <div class="form-group">
                            <div class="col-md-12"> 
                                <table class="table table-striped table-bordered"    id="viewdetalle" style="width: 100%">
                                    <thead>
                                        <tr class="tableheader">
                                            <th style="width: 5%;text-align: center">#</th>
                                            <th style="width: 20%;text-align: center">Curso</th>       
                                            <th style="width: 25%;text-align: center">Profesor(a)</th>  
                                            <th style="width: 5%;text-align: center">Total</th>
                                            <th style="width: 5%;text-align: center">Carga</th>
                                            <th style="width: 5%;text-align: center">Falta</th>
                                            <th style="width: 35%;text-align: center">Avance</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>                
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
