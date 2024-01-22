
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />
<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;
    }

</style>

<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Envio Reporte Banco</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteMes" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <input type="hidden" name="hcadena" id="hcadena" value="" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 5%">&nbsp; Banco : </td>
                <td style ="width: 15%">
                    <select name="cbBanco" id="cbBanco" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::::::::::::: Ninguna ::::::::::::::: </option>
                        <option value="B1" > - BBVA - CONTINENTAL - </option>
                        <option value="B2" selected="selected"> - BCP - BANCO DE CREDITO - </option>
                        <option value="B3"> - SCK - SCOTIABANK - </option>
                        <option value="B4"> - INT - INTERBANK - </option>
                    </select>
                </td>            
                <td style ="width: 5%">&nbsp; Raz&oacute;n : </td>
                <td style ="width: 15%">
                    <select name="cbRazon" id="cbRazon" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::::::::::::: Ninguna ::::::::::::::: </option>
                        <option value="01-20517718778" selected="selected">COORPORACION MARIANISTA SAC </option>
                        <option value="02-20556889237">COLEGIO MARIANISTAS V.M.T SAC</option>
                    </select>
                </td>      
                <td style ="width: 5%">&nbsp; Tipo : </td>
                <td style ="width: 10%">
                    <select name="cbTipo" id="cbTipo" style="width: 100%"  class="form-control input-sm">
                        <option value=""> ::::::::::::::: Ninguna ::::::::::::::: </option>
                        <option value="01" >PENSIONES PAGADAS </option>
                        <!-- <option value="02">MATRICULAS REALIZADAS</option>-->
                        <option value="03" >PENSIONES PENDIENTES</option>
                    </select>
                </td>      
                <td style ="width: 5%">&nbsp; Desde : </td>
                <td style ="width: 10%">
                    <input type="date" value="<?= date('Y-m-d') ?>" id="fini" class="form-control" name="fini" />

                </td>      
                <td style ="width: 5%">&nbsp; Hasta : </td>
                <td style ="width: 10%">
                    <input type="date" value="<?= date('Y-m-d') ?>" id="ffin" class="form-control"  name="ffin" />
                </td>   
                <td style ="width: 5%">&nbsp; Mes : </td>
                <td style ="width: 10%">
                    <select name="cbPeriodo" id="cbPeriodo"    class="form-control input-sm">
                        <option value="">NINGUNO </option>
                        <option value="03">03. MARZO </option>
                        <option value="04">04. ABRIL</option>
                        <option value="05">05. MAYO</option>
                        <option value="06">06. JUNIO</option>
                        <option value="07">07. JULIO</option>
                        <option value="08">08. AGOSTO</option>
                        <option value="09">09. SETIEMBRE</option>
                        <option value="10">10. OCTUBRE</option>
                        <option value="11">11. NOVIEMBRE</option>
                        <option value="12">12. DICIEMBRE</option>
                    </select>
                </td>                     
            </tr>
            <tr style="height: 40px">
                <td  colspan="10" style ="width: 100%;text-align: center;">&nbsp;
                    <div id="divMensaje" style="display:none;">                       
                    </div>
                </td>   
               
            </tr>
                        <!--<tr style="height: 40px">
                            <td  colspan="4" style ="width: 70%;text-align: center;">&nbsp;
                                <div id="divMensaje" style="display:none;">                       
                                </div>
                            </td>   
                            <td style ="width: 10%">&nbsp; Mes : </td>
                            <td style ="width: 20%">
                                <select name="cbPeriodo" id="cbPeriodo"   multiple=""  class="form-control input-sm select-multiple">
                                    <option value="03">03. MARZO </option>
                                    <option value="04">04. ABRIL</option>
                                    <option value="05">05. MAYO</option>
                                    <option value="06" selected="">06. JUNIO</option>
                                    <option value="07">07. JULIO</option>
                                    <option value="08">08. AGOSTO</option>
                                    <option value="09">09. SETIEMBRE</option>
                                    <option value="10">10. OCTUBRE</option>
                                    <option value="11">11. NOVIEMBRE</option>
                                    <option value="12">12. DICIEMBRE</option>
                                </select>
                            </td>                   
                        </tr>-->
            <tr style="height: 40px">
                <td colspan="10" style ="width: 100%;text-align: center;">
                    <button type="button"   id="btnGenerar"  name="btnGenerar" class="btn btn-primary"><i class="glyphicon glyphicon-cloud-upload"></i> Generar</button>
                    <button type="button"   id="btnDownload"  name="btnDownload" class="btn btn-danger" disabled="disabled"><i class="glyphicon glyphicon-download"></i> Descargar</button>
                </td>                      
            </tr>

        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
