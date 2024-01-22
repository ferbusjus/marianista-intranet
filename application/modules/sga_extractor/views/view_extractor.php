<style>
    select {
        font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
        font-size: 11px;

    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
</script>
<h3 class="page-header"><span class="glyphicon glyphicon-list"></span> Extractor de Datos</h3>
<div id="mensaje"></div>
<hr/><br/> 
<form name="frmReporteMatriculas" id="frmReporteMatriculas" method="post" target="_blank">
    <center>
        <input type="hidden" name="token" id="token" value="<?= $this->token ?>" />
        <table style="text-align:center;width: 95%" border="0">
            <tr style="height: 40px">
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
                <td style ="width: 25%" >               
                    <button type="button" id="btnImprimir" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Generar Reporte</button>
                    </div>
                </td>                     
            </tr>
        </table>
    </center>
    <br/>
    <hr/>
    <br/>
    <div class="form-group">
        <div class="col-sm-3">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">DATOS DEL ALUMNO</h3>
                </div>
                <div class="panel-body">
                    <ul class="llist-group list-group-flush"  >
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <label>
                                <input type="checkbox" value="DNI" checked="checked" name="chkCampos[]">
                                DNI
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="APEPAT"  checked="checked" name="chkCampos[]" >
                                Apellido Paterno
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="APEMAT"   checked="checked" name="chkCampos[]">
                                Apellido Materno
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="NOMBRES"   checked="checked" name="chkCampos[]">
                                Nombres
                            </label>
                        </li>                                                
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="FECNAC"   checked="checked" name="chkCampos[]">
                                Fecha de Nacimiento
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="SEXO"   checked="checked" name="chkCampos[]">
                                Sexo
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="DIRECCION"   checked="checked" name="chkCampos[]">
                                Direccion
                            </label>
                        </li>         
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="ALUEMAIL" checked="checked" name="chkCampos[]">
                               Correo Electrónico
                            </label>
                        </li>                          
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="TELEFONO"   checked="checked" name="chkCampos[]">
                                Celular
                            </label>
                        </li>       
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="TELEFONO2"   checked="checked"  name="chkCampos[]">
                                Telefono
                            </label>
                        </li>                        
                    </ul>            
                </div>
            </div>
        </div>    
        
        <div class="col-sm-3">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">DATOS DEL PADRE</h3>
                </div>
                <div class="panel-body">
                    <ul class="llist-group list-group-flush"  >
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <label>
                                <input type="checkbox" value="DNIPATER"   checked="checked" name="chkCampos[]">
                                DNI
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADPATER"  checked="checked" name="chkCampos[]" >
                                Apellido Paterno
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADMATER"   checked="checked" name="chkCampos[]">
                                Apellido Materno
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADNOM"   checked="checked" name="chkCampos[]">
                                Nombres
                            </label>
                        </li>                                                
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADFECNAC"  checked="checked" name="chkCampos[]">
                                Fecha de Nacimiento
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADDIRECCION"   checked="checked" name="chkCampos[]">
                                Direccion
                            </label>
                        </li>         
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADCELU"   checked="checked" name="chkCampos[]">
                                Celular 1
                            </label>
                        </li>     
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADCELU2"   checked="checked" name="chkCampos[]">
                                Celular 2
                            </label>
                        </li>                            
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="PADEMAIL"   checked="checked"  name="chkCampos[]">
                                Correo Electrónico
                            </label>
                        </li>                        
                    </ul>            
                </div>
            </div>
        </div>   
        
        
        <div class="col-sm-3">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">DATOS DE LA MADRE</h3>
                </div>
                <div class="panel-body">
                    <ul class="llist-group list-group-flush"  >
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <label>
                                <input type="checkbox" value="DNIMATER"   checked="checked" name="chkCampos[]">
                                DNI
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADPATER"  checked="checked" name="chkCampos[]" >
                                Apellido Paterno
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADMATER"   checked="checked" name="chkCampos[]">
                                Apellido Materno
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADNOM"   checked="checked" name="chkCampos[]">
                                Nombres
                            </label>
                        </li>                                                
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADFECNAC"  checked="checked" name="chkCampos[]">
                                Fecha de Nacimiento
                            </label>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADDIRECCION"   checked="checked" name="chkCampos[]">
                                Direccion
                            </label>
                        </li>         
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADCELU"   checked="checked" name="chkCampos[]">
                                Celular 1
                            </label>
                        </li>       
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADCELU2"   checked="checked" name="chkCampos[]">
                                Celular 2
                            </label>
                        </li>                             
                        <li class="list-group-item d-flex justify-content-between align-items-center" >
                            <label>
                                <input type="checkbox" value="MADEMAIL"   checked="checked"  name="chkCampos[]">
                                Correo Electrónico
                            </label>
                        </li>                        
                    </ul>            
                </div>
            </div>
        </div>   


        
    </div>              
</form>


