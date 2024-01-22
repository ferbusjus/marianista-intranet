<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 12px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Cierre Caja</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteCaja" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 30%"><b>TOTAL RECAUDADO:</b> </td>
                <td style ="width: 30%">
                    <b style="font-size: 20px">S/.1500.00 NUEVOS SOLES</b>
                </td>           
                <td style ="width: 40%;text-align: center;">
                    <button type="button"   id="btnPrint"  name="btnPrint" class="btn btn-primary"><i class="glyphicon glyphicon-cloud"></i> Cerrar Caja</button>                     
                </td>
            </tr>
        </table>
    </form>
</center>
<br/>

<hr/>
<br/>
    <br>
    <div id="divTblGrilla">
        <table class="table table-striped table-bordered"    id="viewGrilla" style="width: 100%">
            <thead>
                <tr class="tableheader">
                    <th style="width: 10%;text-align: center">Recibo</th>
                    <th style="width: 15%;text-align: center">Datos Familia</th>
                    <th style="width: 25%;text-align: center">Datos Alumno</th>
                    <th style="width: 15%;text-align: center">Concepto de Pago</th>
                    <th style="width: 8%;text-align: center">Fec-Reg</th>
                    <th style="width: 5%;text-align: center">Cobrado</th>
                    <th style="width: 20%;text-align: center">Aula</th>    
                    <th style="width: 2%;text-align: center">&nbsp;</th>  
                </tr>
            <thead>
            <tbody>
                <?php foreach($dataPagos as $pago ): ?>
                <tr >
                    <td style="width: 10%;text-align: center"><?=$pago->numrecibo?></td>
                    <td style="width: 15%;text-align: center"></td>
                    <td style="width: 25%;text-align: center"></td>
                    <td style="width: 15%;text-align: center"></td>
                    <td style="width: 8%;text-align: center"></td>
                    <td style="width: 5%;text-align: center"></td>
                    <td style="width: 20%;text-align: center"></td>    
                    <td style="width: 2%;text-align: center"></td>  
                </tr>                
                <?php endforeach; ?>
            </tbody>        
        </table>
    </div>