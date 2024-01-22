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
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<form class="form-horizontal" role="form" id="frmReporteBoletas" name="frmReporteBoletas" method="POST" target="_blank"  > 
    <input type="hidden" id="anio"  name="anio" value="<?=$anio?>" />
    <input type="hidden" id="flgGenerar"  name="flgGenerar" value="0" />
    <div class="form-group">
        <div class="col-lg-12">
            <h4><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Reporte de Notas por Periodo</h4>
        </div>         
    </div>

    <center id="divComunicado">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Comunicado!</h4>
            <p>Ejecutar el procesos de generacion de Notas por Periodo en horarios que no sature el sistema y genere lentitud a otras áreas que tambien lo utilizan. </p>
            <p class="mb-0">Cualquier inconveniente comuniquese con info@sistemas-dev.com</p>
        </div>
    </center>    
    <div class="form-group">
        <div class="col-lg-12">
            <hr/>
        </div>
    </div>    

    <div class="form-group">
        <label  class="col-sm-12 col-md-2 control-label">Aula :</label>
        <div class="col-sm-12 col-md-4">
            <select class="form-control" id="cbaula" name="cbaula">
                <option value="">::: SELECCIONE :::</option>
                <?php foreach ($listaAulas as $lista) { ?>
                    <option value="<?= $lista->nemo ?>" style="color: #000;font-style: italic;font-weight: 500 /*background-color: <?//= (($lista->instrucod == 'I') ? 'aqua' : (($lista->instrucod == 'P') ? 'oldlace' : 'thistle')) ?>"><?= $lista->nemo . " | " . $lista->nemodes ?></option>
                <?php } ?>
            </select>
        </div>
        <label  class="col-sm-12 col-md-1 control-label">Periodo :</label>
        <div class="col-sm-12 col-md-2">
            <select class="form-control" id="cbperiodo" name="cbperiodo" disabled="">
                <option value="">::: SELECCIONE :::</option>
                <option value="1">I BIMESTRE</option>
                <option value="2">II BIMESTRE</option>
                <option value="3">III BIMESTRE</option>
                <option value="4">IV BIMESTRE</option>
            </select>
        </div>  
        <div class="col-sm-12 col-md-3">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <button type="button" id="btnGenerar" class="btn btn-primary btn-sm btn-block">Generar Resumen
                </div>
            </div>
        </div>        
    </div> 

</div>
<div class="form-group">
    <div class="col-lg-12">
        <hr/>
    </div>
</div>
</form>