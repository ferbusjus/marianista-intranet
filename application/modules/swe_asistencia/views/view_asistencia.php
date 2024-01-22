<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h2 class="page-header"><span class="glyphicon glyphicon-list"></span> Ver Asistencias</h2>
<div id="mensaje"></div>
<hr/><br/>  
<center>
    <form name="frmAsistencia" class="form-horizontal"  role="form" id="frmAsistencia" action="<?= BASE_URL ?>swe_asistencia/generaReporte" onsubmit="return validaPost();" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 col-form-label">Alumno :</label>
            <div class="col-sm-8">
                <select name="cbalumno" id="cbalumno" style="background-color:#F2F5A9" class="form-control">
                    <option value="0"> ::: SELECCIONE HIJO :::<option>
                        <?php foreach ($dataHijos as $row) : ?>
                        <option value="<?= $row->nemo . '|' . $row->dni ?>"> <?= $row->dni . ' | ' . $row->nombres . ' | ' . $row->nemodes ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2"></div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 col-form-label">Tipo :</label>
            <div class="col-sm-3">
                <select name="cbtipo" id="cbtipo" class="form-control">
                    <option value="0">:: TODOS ::</option>
                    <option value="I" selected="selected">- INGRESO -</option>
                    <option value="S">- SALIDA -</option>
                </select>
            </div>
            <label for="inputEmail3" class="col-sm-2 col-form-label">Mes :</label>
            <div class="col-sm-3">
                <select name="cbmes" id="cbmes"   class="form-control">
                    <option value="0">:: SELECCIONE ::</option>
                    <option value="01" <?= (date("m") == '01') ? 'selected="selected"' : '' ?>>- ENERO - </option>
                    <option value="02" <?= (date("m") == '02') ? 'selected="selected"' : '' ?>>- FEBRERO - </option>
                    <option value="03" <?= (date("m") == '03') ? 'selected="selected"' : '' ?>>- MARZO - </option>
                    <option value="04" <?= (date("m") == '04') ? 'selected="selected"' : '' ?>>- ABRIL - </option>
                    <option value="05" <?= (date("m") == '05') ? 'selected="selected"' : '' ?>>- MAYO - </option>
                    <option value="06" <?= (date("m") == '06') ? 'selected="selected"' : '' ?>>- JUNIO - </option>
                    <option value="07" <?= (date("m") == '07') ? 'selected="selected"' : '' ?>>- JULIO - </option>
                    <option value="08" <?= (date("m") == '08') ? 'selected="selected"' : '' ?>>- AGOSTO - </option>
                    <option value="09" <?= (date("m") == '09') ? 'selected="selected"' : '' ?>>- SETIEMBRE - </option>
                    <option value="10" <?= (date("m") == '10') ? 'selected="selected"' : '' ?>>- OCTUBRE - </option>
                    <option value="11" <?= (date("m") == '11') ? 'selected="selected"' : '' ?>>- NOVIEMBRE - </option>
                    <option value="12" <?= (date("m") == '12') ? 'selected="selected"' : '' ?>>- DICIEMBRE - </option>
                </select>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <button type="button"  id="btnBuscar" class="btn btn-primary btn-md">
            <span class="glyphicon glyphicon-search"></span> Mostrar
        </button>
        <button type="submit"   id="btnPrint" class="btn btn-success btn-md">
            <span class="glyphicon glyphicon-print"></span> Imprimir
        </button>
        

    </form>
</center>
<br/>
<hr/>
<br/>
<table class="table table-bordered"    id="viewListado" style="width: 100%">
    <thead>
    <th style="width: 10%;text-align: center">Codigo</th>
    <th style="width: 10%;text-align: center">Fecha</th>
    <th style="width: 10%;text-align: center">Hora</th>
    <th style="width: 10%;text-align: center">Asis.</th>
    <th style="width: 10%;text-align: center">Evento</th>
    <th style="width: 50%;text-align: center">Observacion</th>
    <thead>

    <tbody>
        <tr>
            <td colspan=6><center>No Hay Informaci&oacute;n</center></td>
</tr>

</tbody>
</table>
