<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Reporte de Egresos</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteEgresos" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 30%">&nbsp;</td>
                <td style ="width: 10%">&nbsp; Fecha : </td>
                <td style ="width: 20%" >
                    <div class='input-group date'>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span> 
                        <input type='text' name="finicial" id="finicial" class="form-control calendario"  readonly="" value="<?php echo date('d/m/Y'); ?>" placeholder="Fech" data-date-format="dd/mm/yyyy" required="" style="width:150px;"/>
                    </div>
                </td>           
                <td style ="width: 40%;text-align: center;">
                    <button type="button"   id="btnPrintEgreso"  name="btnPrintEgreso" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Generar Reporte</button>
                </td>                   
            </tr>

        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
