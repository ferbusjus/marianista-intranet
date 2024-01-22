<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Reporte Cierre de Mes</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteMes" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 20%">&nbsp;Seleccione : </td>
                <td style ="width: 30%">
                    <select name="cbmes" id="cbmes" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::::::::::::: Ninguna ::::::::::::::: </option>
                            <option value="01"> - Enero - </option>
                            <option value="02"> - Febrero - </option>
                            <option value="03"> - Marzo - </option>
                            <option value="04"> - Abril - </option>
                            <option value="05"> - Mayo - </option>
                            <option value="06"> - Junio - </option>
                            <option value="07"> - Julio - </option>
                            <option value="08"> - Agosto - </option>
                            <option value="09"> - Setiembre - </option>
                            <option value="10"> - Octubre - </option>
                            <option value="11"> - Noviembre - </option>
                            <option value="12"> - Diciembre - </option>
                    </select>
                </td>               
                <td style ="width: 50%;text-align: center;">
                    <button type="button"   id="btnPrint"  name="btnPrint" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Generar Reporte</button>
                </td>                   
            </tr>

        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
