<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
    /* ================= Para Datatable ===================*/
    th.dt-center, td.dt-center { text-align: center; /*vertical-align: middle;*/ }    
    th.dt-center, td.dt-right { text-align: right; } 
    th.dt-center, td.dt-left { text-align: left; } 
    td.dt-fecha { text-align: center;  font-size: 12px ;font-weight: bold; } 
    /* ===================================================*/    
    #loading {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        display: none;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        text-align: center;
    }

    #loading-image {
        position: absolute;
        top: 35%;
        left: 50%;
        z-index: 100;
    }    
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>"; 
</script>
<div id="loading">
    <img id="loading-image" src="images/reniecloading.gif" alt="Loading..." /><br>
    <center><b>Un momento porfavor se esta Procesando.....</b></center>
</div>
<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Resumen - SUNAT</h3>
<div id="mensaje"></div>
<hr/><br/> 
<center>
    <form name="frmAsistencia" id="frmReporteCaja" method="post" target="_blank">
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
                <td style ="width: 10%">&nbsp;Razón Social : </td>
                <td style ="width: 20%">
                    <select name="cbrazon" id="cbrazon" style="width: 100%" class="form-control input-sm">
                        <option value=""> ::::::::::::::: TODOS ::::::::::::::: </option>
                        <?php foreach ($dataEmpresa as $empresa) : ?>
                            <option value="<?php echo $empresa->idrazon ?>"><?php echo $empresa->ruc . " - " . $empresa->razon_social ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>        
                <td style ="width: 10%">&nbsp; Fecha : </td>
                <td style ="width: 20%" >
                    <div class='input-group date'>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span> 
                        <input type='text' name="finicial" id="finicial" class="form-control calendario"  readonly="" value="<?php echo date('d/m/Y'); ?>" placeholder="Fecha de Inicio" data-date-format="dd/mm/yyyy" required="" style="width:150px;"/>
                    </div>
                </td>           
                <td style ="width: 40%;text-align: center;">
                    <button type="button"   id="btnResumen"  name="btnResumen" onclick="javascript:js_verResumen();" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Generar Resumen</button>
                    <button type="button"   id="btnEnviar"  name="btnEnviar" onclick="javascript:js_sendResumen();" class="btn btn-danger"><i class="glyphicon glyphicon-send"></i> Enviar a  SUNAT</button>
                </td>                   
            </tr>

        </table>
    </form>

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
                   <!-- <th style="width: 5%;text-align: center">Moneda</th>-->
                   <!-- <th style="width: 20%;text-align: center">Aula</th>    -->
                    <th style="width: 2%;text-align: center">&nbsp;</th>  
                </tr>
            <thead>
            <tbody>
            </tbody>        
        </table>
    </div>
    <br/>

</center>
<br/>
<hr/>
<br/>
