<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>

<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Resumen General</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteGeneral" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;Razón : </td>
                <td style ="width: 15%">
                    <select name="cbrazon" id="cbrazon" style="width: 100%" class="form-control input-sm">
                        <option value="T"> ::::::::::: TODOS ::::::::::: </option>
                        <?php foreach ($dataEmpresa as $empresa) : ?>
                            <option value="<?php echo $empresa->idrazon ?>"><?php echo $empresa->ruc . " - " . $empresa->razon_social ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>            
                <td style ="width: 10%">&nbsp; Comprobante : </td>
                <td style ="width: 15%" >
                    <select name="cbcomprobante" id="cbcomprobante" style="width: 100%" class="form-control input-sm">
                        <option value="T"> ::::::::::: TODOS ::::::::::: </option>
                        <option value="01">01 - RECIBOS</option>
                        <option value="02">02 - BOLETAS</option>
                        <option value="03">03 - FACTURAS</option>
                    </select>
                </td>                    
                <td style ="width: 10%">&nbsp; Usuario : </td>
                <td style ="width: 15%" >
                    <select name="cbusuario" id="cbusuario" <?=($rol_usuario!='1')?'disabled':''?> style="width: 100%" class="form-control input-sm">
                        <option value="T"> ::::::::::: TODOS ::::::::::: </option>
                        <?php foreach ($dataUsuarios as $usuario) : ?>
                            <option value="<?php echo $usuario->usucod ?>"  <?=($rol_usuario!='1' && $usuario->usucod==$usucod)?'selected':''?>><?php echo $usuario->apellidos ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>  

                <td style ="width: 10%">&nbsp; Desde : </td>
                <td style ="width: 8%" >
                    <div class='input-group date'>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span> 
                        <input type='text' name="finicial" id="finicial" class="form-control calendario"  readonly="" value="<?php echo date('d/m/Y'); ?>" placeholder="Fecha de Inicio" data-date-format="dd/mm/yyyy" required="" style="width:100px;"/>
                    </div>
                </td>   
                <td style ="width: 10%">&nbsp; Hasta : </td>
                <td style ="width: 8%" >
                    <div class='input-group date'>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span> 
                        <input type='text' name="ffinal" id="ffinal" class="form-control calendario"  readonly="" value="<?php echo date('d/m/Y'); ?>" placeholder="Fecha Fin" data-date-format="dd/mm/yyyy" required="" style="width:100px;"/>
                    </div>
                </td>    
                <td style ="width: 14%;text-align: center;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td  colspan="10" style ="width: 100%; height: 70px; text-align: center;">
                    <button type="button"   id="btnPrint"  name="btnPrint" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Exportar PDF</button>
                    <button type="button"   id="btnExcel"  name="btnExcel" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Exportar XLS</button>
                    <div class="checkbox" style="float: right">
                        <label><input type="checkbox" value="1" name="chkReporte" id="chkReporte">Exportar solo Agendas</label>
                    </div>
                </td>   
            </tr>
        </table>
    </form>
</center>
<br/>
<hr/>
<br/>
