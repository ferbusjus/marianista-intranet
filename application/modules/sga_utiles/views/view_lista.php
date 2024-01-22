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
<form class="form-horizontal" role="form">    
    <div class="form-group">
        <div class="col-lg-12">
            <h4><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Entrega Lista de Utiles</h4>
        </div>         
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <hr/>
        </div>
    </div>    
    <div class="form-group">
        <label for="ejemplo_email_3" class="col-lg-1 control-label">Alumno :</label>
        <div class="col-lg-9">
            <input type="text" class="form-control" id="txtSearch" name="txtSearch" data-toggle="tooltip" title="Ingresa el Nombre del Alumno a Filtrar"  placeholder="Ingresa el Nombre del Alumno a Filtrar..">
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <hr/>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <table class="table table-striped table-bordered" id="viewUtiles" style="width: 100%">
                <thead>
                    <tr class="tableheader">
                        <th style="width: 10%;text-align: center">DNI</th>
                        <th style="width: 10%;text-align: center">Codigo</th>
                        <th style="width: 40%;text-align: center">Apellidos y Nombres</th>
                        <th style="width: 30%;text-align: center">Aula</th>
                        <th style="width: 10%;text-align: center">Lista</th>
                    </tr>
                <thead>
                <tbody>
                    <?php foreach ($dataAlumnos as $lista) { ?>
                        <?php
                        if ($lista->FLGUTILES === '0') {
                            $class = "glyphicon glyphicon-unchecked optChek";
                            $title = "PENDIENTE";
                        } else {
                            $class = "glyphicon glyphicon-check";
                            $title = "RECIBIDO";
                        }
                        ?>
                        <tr>
                            <td style="width: 10%;text-align: center"><?= $lista->DNI ?></td>
                            <td style="width: 10%;text-align: center"><?= $lista->ALUCOD ?></td>
                            <td style="width: 40%;text-align: left"><?= $lista->NOMCOMP ?></td>
                            <td style="width: 30%;text-align: left"><?= $lista->NEMODES ?></td>
                            <td style="width: 10%;text-align: center"><i  value="<?= $lista->ALUCOD ?>" onclick="js_marca(<?= $lista->ALUCOD ?>, <?= $lista->FLGUTILES ?>);" style="cursor: pointer" title="<?= $title ?>"  data-toggle="tooltip"  class="<?= $class ?>"></i></td>
                        </tr>                    
                    <?php } ?>
                </tbody>        
            </table>            
        </div>
    </div>
</form>