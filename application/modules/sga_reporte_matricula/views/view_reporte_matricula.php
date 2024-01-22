<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Reporte de Matriculas</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmReporteMatriculas" id="frmReporteMatriculas" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%"><b>Tipo Reporte </b></td>   
                <td style ="width: 15%" >
                    <select name="cbtiporeporte" id="cbtiporeporte" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::: Seleccione ::::: </option>
                        <option value="1"> POR LISTAS </option>
                        <option value="2"> POR TOTALES </option>
                      <!--   <option value="3"> POR DOCUMENTOS </option>-->
                    </select>
                </td>     

                <td style ="width: 10%"><b>Nivel : </b></td>
                <td style ="width: 15%">
                    <select name="cbnivel" id="cbnivel" style="width: 100%" class="form-control input-sm">
                        <option value=""> :: Todos :: </option>
                        <?php foreach ($dataNivel as $nivel) : ?>
                            <option value="<?php echo $nivel->INSTRUCOD ?>"><?php echo $nivel->INSTRUCOD . " - " . $nivel->INSTRUDES ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>        
                <td style ="width: 10%"><b> Grado : </b></td>
                <td style ="width: 15%" >
                    <select name="cbgrado" id="cbgrado" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::: Todos ::::: </option>
                    </select>
                </td>           

                <td style ="width: 10%"><b> Aula :</b> </td>
                <td style ="width: 15%" >
                    <select name="cbaula" id="cbaula" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::: Todos :::::  </option>
                    </select>
                </td>         

            </tr>
            <tr>
                <td colspan="8" style ="width: 100%; text-align: center;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Generar Reporte</button>

                        <button type="button" class="btn btn-success dropdown-toggle"
                                data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Desplegar menú</span>
                        </button>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void();" onclick="js_reportes('pdf')"> Exportar PDF</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void();" onclick="js_reportes('excel')">Exportar Excel</a></li>
                        </ul>
                    </div>

                </td> 
            </tr>

        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
