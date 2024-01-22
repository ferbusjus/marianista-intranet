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
<form class="form-horizontal" role="form" id="frmResumen" name="frmResumen" method="POST" target="_blank"  > 
    <div class="form-group">
        <div class="col-lg-12">
            <h4><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Resumen de Notas</h4>
        </div>         
    </div>

    <div class="form-group">
        <div class="col-lg-12">
            <hr/>
        </div>
    </div>    

    <div class="form-group">
        <label  class="col-sm-2 col-md-2 control-label">Nivel :</label>
        <div class="col-sm-2 col-md-2">
            <select class="form-control" id="cbnivel" name="cbnivel">
                <option value="">::: SELECCIONE :::</option>
                <?php foreach ($listaNivel as $lista) { ?>
                    <option value="<?= $lista['instrucod'] ?>" ><?= $lista['instrucod'] . " | " . $lista['descripcion'] ?></option>
                <?php } ?>
            </select>
        </div>

        <label  class="col-sm-2 col-md-2 control-label">Grado :</label>
        <div class="col-sm-2 col-md-2">
            <select class="form-control" id="cbgrado" name="cbgrado" disabled="">
                <option value="">::: SELECCIONE :::</option>
            </select>
        </div>
        <label  class="col-sm-2 col-md-2 control-label">Bimestre :</label>
        <div class="col-sm-2 col-md-2">
            <select class="form-control" id="cbbimestre" name="cbbimestre">
                <option value="">::: SELECCIONE :::</option>
                <option value="1">I BIMESTRE</option>
                <option value="2">II BIMESTRE</option>
                <option value="3">III BIMESTRE</option>
                <option value="4">IV BIMESTRE</option>
            </select>
        </div> 


    </div>
    <div class="form-group">
        <div class="col-sm-4 col-md-4">&nbsp;</div>
        <div class="col-sm-4 col-md-4">
                    <button type="button" id="btnGenerar" class="btn btn-primary btn-sm btn-block">Generar Resumen
        </div>
        <div class="col-sm-4 col-md-4">&nbsp;</div>
        </button>
    </div>
</div>
<div class="form-group">
    <div class="col-lg-12">
        <hr/>
    </div>
</div>
</form>