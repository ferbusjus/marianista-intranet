<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Reporte - Pago Completo</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteCaja" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;Nivel : </td>
                <td style ="width: 20%">
                    <select name="cbnivel" id="cbnivel" style="width: 100%" class="form-control input-sm">
                        <option value="T"> ::::::::::::::: TODOS ::::::::::::::: </option>
                        <?php foreach ($dataNivel as $nivel) : ?>
                            <option value="<?php echo $nivel->INSTRUCOD ?>"><?php echo $nivel->INSTRUCOD . " - " . $nivel->INSTRUDES ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>        
                <td style ="width: 10%">&nbsp; Mes : </td>
                <td style ="width: 20%" >
                    <select name="cbmes" id="cbmes" style="width: 100%" class="form-control input-sm">
                        <option value=""> :::::: SELECCIONE :::::: </option>
                        <option value="03"> - MARZO - </option>
                        <option value="04"> - ABRIL - </option>
                        <option value="05"> - MAYO - </option>
                        <option value="06"> - JUNIO - </option>
                        <option value="07"> - JULIO - </option>
                        <option value="08"> - AGOSTO - </option>
                        <option value="09"> - SETIEMBRE - </option>
                        <option value="10"> - OCTUBRE - </option>
                        <option value="11"> - NOVIEMBRE - </option>
                        <option value="12"> - DICIEMBRE - </option>
                        
                    </select>
                </td>           
                <td style ="width: 40%;text-align: center;">
                    <button type="button"   id="btnPrintPdf"  name="btnPrintPdf" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Exportar PDF</button>
                    <button type="button"   id="btnPrintXls"  name="btnPrintXls" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Exportar XLS</button>
                </td>                   
            </tr>

        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
