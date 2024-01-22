<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url (); ?>";
</script>


<h3 class="page-header"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Cambio de Salon</h3>
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
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;</td>
                <td style ="width: 10%">&nbsp; <b>Alumno :</b> </td>
                <td style ="width: 30%" >
                    <select name="cbalumno" id="cbalumno" style="width: 80%" class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::::::: Seleccione Alumno ::::::::::::::::::::::::::::</option>
                    </select>
                </td>           
                <td style ="width: 10%">
                    &nbsp;<button type="button"   id="btnProcesar" name="btnProcesar" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i> Procesar</button>
                </td>       
            </tr>
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;</td>
                <td style ="width: 10%">&nbsp; <b>Salon Destino :</b> </td>
                <td style ="width: 30%">
                    <select name="cbsalonDestino" id="cbsalonDestino" style="width: 80%" class="form-control input-sm">
                        <option value="0">:::::::::::::::::::::::: Seleccione Salon Destino  :::::::::::::::::::::::::</option>
                </td>           
                <td style ="width: 10%" >
                    &nbsp;
                </td> 
            </tr>            
        </table>
    </form>
</center>
<br/>
<hr/>
