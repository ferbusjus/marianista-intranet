<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>


<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Distribución de Alumnos</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmCambioSalon" id="frmCambioSalon" action=""  method="post">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;</td>
                <td style ="width: 10%">&nbsp;<b>Salon Origen:</b> </td>
                <td style ="width: 30%">
                    <select name="cbsalon" id="cbsalon" style="width: 80%" class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::: Seleccione Salon Origen  :::::::::::::::::::::::::</option>
                        <?php foreach ($dataSalones as $salon) : ?>
                            <option value="<?php echo $salon->NEMO . '-' . $salon->INSTRUCOD . '-' . $salon->GRADOCOD ?>"><?php echo $salon->NEMO . " : " . $salon->NEMODES ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>   
                <td style ="width: 10%">
                    &nbsp;
                </td>                 
            </tr>          
        </table>
    </form>
</center>

<br/>
<hr/>
<br/>
<center>
    <div class="alert alert-success" role="alert" id="divAlerta">
        <h4 class="alert-heading">Comunicado!</h4>
        <p>Esta opción de Distribución de Alumno solo estará Habilitada hasta antes del inicio de año escolar. Despues del inicio ya no se podra realizar por esta opción.</p>
        <p class="mb-0">Cualquier inconveniente comunicarse con info@sistemas-dev.com</p>
    </div>
</center>
<br/>
<div class="form-group">
    <div class="col-sm-12">                                            
    <table class="table table-striped table-bordered"    id="viewListaMigracion" style="width: 100%">
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;text-align: center">DNI</th>
                <th style="width: 25%;text-align: center">Apellidos y Nombres Completos</th>
                <th style="width: 30%;text-align: center">Observacion</th>
                <th style="width: 30%;text-align: center">Aula Nueva</th> 
                <th style="width: 10%;text-align: center">Estado</th>
            </tr>
        <thead>
        <tbody>
            
        </tbody>        
    </table>
    </div>

</div>
