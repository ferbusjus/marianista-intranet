<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>
<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Reportes</h3>
<div id="mensaje"></div>
<hr/><br/> 
<form name="frmReporte" id="frmReporte" action="<?= BASE_URL ?>sga_reporte_asistencia/reporte" method="post" target="_blank">

    <table>
        <tr>
            <td>Desde: &nbsp;&nbsp;</td>
            <td>
                <div class='input-group date'>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span> 
                    <input type='text' name="finicial" id="finicial" class="form-control calendario" value="<?php echo '01/' . date ('m/Y') ?>" placeholder="Fecha Inicio" data-date-format="dd/mm/yyyy" required="" style="width:150px;"/>
                </div>            

            </td>
            <td> &nbsp; &nbsp; &nbsp;Hasta: &nbsp;&nbsp;</td>
            <td>
                <div class='input-group date'>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span> 
                    <input type='text' name="ffinal" id="ffinal" class="form-control calendario" value="<?php echo date ('d/m/Y') ?>" placeholder="Fecha Fin" data-date-format="dd/mm/yyyy" required="" style="width:150px;"/>
                </div>              

            </td>
            <td>&nbsp; &nbsp; &nbsp; Salon: &nbsp;&nbsp;</td>
            <td>
                <select name="cbsalon" id="cbsalon" style="width: 100%" class="form-control input-sm">
                    <option value="0">:::::::::::::: Seleccione Salon ::::::::::::::</option>
                    <?php foreach ($dataSalones as $salon) : ?>
                        <option value="<?php echo $salon->NEMO ?>"><?php echo $salon->NEMO . " - " . $salon->NEMODES ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                &nbsp;&nbsp;<button type="button"   id="BtnReporte" class="btn btn-primary"><i class="fa fa-align-left"></i> Generar Reporte</button>

            </td>
        </tr>
    </table>

</form>    
<br/> 
<hr/>



