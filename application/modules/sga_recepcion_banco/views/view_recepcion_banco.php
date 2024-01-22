<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Recepcion Cobro Banco</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmAsistencia" action="sga_recepcion_banco/cargarFile"   method="post" target="iframeUpload" enctype="multipart/form-data">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <input type="hidden" name="hcadena" id="hcadena" value="" />
        <iframe style="display: none;" name="iframeUpload"></iframe>         
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp; Banco : </td>
                <td style ="width: 20%">
                    <select name="cbBanco" id="cbBanco" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::::::::::::: Ninguna ::::::::::::::: </option>
                        <option value="B1" > - BBVA - CONTINENTAL - </option>
                        <option value="B2" selected="selected"> - BCP - BANCO DE CREDITO - </option>
                        <option value="B3"> - SCK - SCOTIABANK - </option>
                        <option value="B4"> - INT - INTERBANK - </option>
                    </select>
                </td>     
                <td style ="width: 10%">&nbsp; Raz&oacute;n : </td>
                <td style ="width: 35%">
                    <select name="cbRazon" id="cbRazon" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::::::::::::: Ninguna ::::::::::::::: </option>
                        <option value="01-20517718778" selected="selected">COORPORACION MARIANISTA SAC </option>
                        <option value="02-20556889237">COLEGIO MARIANISTAS V.M.T SAC</option>
                    </select>
                </td>                                                 
                <td style ="width: 25%;text-align: center;" >
                  <!--  <button type="button"   id="btnGenerar"  name="btnGenerar" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Cargar</button>-->
                    <button type="submit"   id="btnProcesar"  name="btnProcesar" class="btn btn-primary" ><i class="glyphicon glyphicon-print"></i> Procesar</button>                    
                </td>      
            </tr>                       
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp; Archivo : </td>                            
                <td colspan="3" style ="width: 70%">
                    <input type="file" id="txtfile"  name="txtfile"    value="" >
                </td>                      
                <td style ="width: 20%;text-align: center;" >
                    &nbsp;
                </td>      
            </tr>            
            <tr style="height: 40px">
                <td  colspan="5" style ="width: 100%;text-align: center;">&nbsp;
                    <div id="divLoading" style="display:none;">                       
                    </div>
                </td>                  
            </tr>
        </table>
    </form>
</center>
<br/>

<div id="divTblPagos"></div>
    
<hr/>
<br/>
