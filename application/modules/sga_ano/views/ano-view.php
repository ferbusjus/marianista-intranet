<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Cambio de Año</h3>
<div id="mensaje"></div>
<hr/><br/> 
<form name="frmReporte" id="frmReporte" action="<?= BASE_URL ?>sga_ano/cambio" method="post" >
    <table  width="100%">
        <tr>
            <td style="width: 20%;text-align: center;"> Año Actual: </td>
            <td  style="width: 20%">
                <select name="cbano" id="cbano" style="width: 50%" class="form-control input-sm">
                    <?php for ($ano = ANO_INICIO_PEN; $ano <= $vano+1; $ano++) : ?>
                        <option value="<?=$ano?>" <?=(($s_ano_vig==$ano)?'selected="selected"':'')?>><?=$ano?></option>
                    <?php endfor; ?>
                </select>
            </td>
            <td   style="width: 20%;text-align: center;">
                &nbsp;&nbsp;<button type="Submit"   id="BtnSubmit" class="btn btn-primary"><i class="fa fa-align-left"></i> Cambiar</button>
            </td>
            <td   style="width: 40%;text-align: center;"></td>
        </tr>
    </table>

</form>    
<br/> 
<hr/>



