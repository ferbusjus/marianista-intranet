<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Reporte de Deudores por Periodo</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReportePagos" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                
                <td style ="width: 10%"><b>Nivel : </b></td>
                <td style ="width: 15%">
                    <select name="cbnivel" id="cbnivel" style="width: 100%" class="form-control input-sm">
                        <option value="0"> :: Seleccione :: </option>
                        <?php foreach ($dataNivel as $nivel) : ?>
                            <option value="<?php echo $nivel->INSTRUCOD ?>"><?php echo $nivel->INSTRUCOD . " - " . $nivel->INSTRUDES ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>        
                <td style ="width: 10%"><b> Grado : </b></td>
                <td style ="width: 15%" >
                    <select name="cbgrado" id="cbgrado" style="width: 100%" class="form-control input-sm">
                        <option value="0"> ::::: Todos ::::: </option>
                    </select>
                </td>           
                
                <td style ="width: 10%"><b> Aula :</b> </td>
                <td style ="width: 15%" >
                    <select name="cbaula" id="cbaula" style="width: 100%" class="form-control input-sm">
                        <option value="0"> ::::: Todos :::::  </option>
                    </select>
                </td>         
                <td style ="width: 10%"><b> Mes :</b> </td>   
                <td style ="width: 15%" >
                    <select name="cbmes" id="cbmes" style="width: 100%" class="form-control input-sm">
                        <option value="0"> :: Seleccione :: </option>
                        <option value="01"> 01. ENERO </option>
                        <option value="02"> 02. FEBRERO </option>
                        <option value="03"> 03. MARZO </option>
                        <option value="04"> 04. ABRIL </option>
                        <option value="05"> 05. MAYO </option>
                        <option value="06"> 06. JUNIO</option>
                        <option value="07"> 07. JULIO </option>
                        <option value="08"> 08. AGOSTO </option>
                        <option value="09"> 09. SETIEMBRE </option>
                        <option value="10"> 10. OCTUBRE </option>
                        <option value="11"> 11. NOVIEMBRE </option>
                        <option value="12"> 12. DICIEMBRE </option>
                    </select>
                </td>                      
            </tr>
            <tr>
                <td colspan="8" style ="width: 100%; text-align: center;">
                    &nbsp;<button type="button"   id="btnPrint"  name="btnPrint" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Exportar PDF</button>
                     &nbsp;<button type="button"   id="btnExcel"  name="btnExcel" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Exportar XLS</button>
                </td> 
            </tr>

        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
