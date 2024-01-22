<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
</script>
<script src="<?php echo base_url()?>js/jquery-1.10.2.js"></script>
<script src="<?php echo base_url()?>js/jquery-ui.js"></script>
<script src="<?php echo base_url()?>js/JsReportes.js"></script>
<h1 class="page-header"><span class="glyphicon glyphicon-list"></span> Reportes</h1>
<div id="mensaje"></div>
<hr/><br/> 
<table>
<tr>
<td>Fecha Inicial: </td>
<td><input type="text" name="finicial" id="finicial" class="form-control input-sm"  size="20" /></td>
<td> &nbsp; &nbsp; &nbsp;Fecha Final: </td>
<td><input type="text" name="ffinal" id="ffinal" class="form-control input-sm"  size="20" /></td>
<td>&nbsp; &nbsp; &nbsp; Tipo de Documento: </td>
<td>
<select name="documento" id="documento" class="form-control input-sm">
    <option value="Venta">Ventas</option>
    <option value="Entrada">Compras</option>
 </select>
</td>
<td>
      &nbsp;<button type="submit"   id="GeneraReporte" class="btn btn-primary"><i class="fa fa-align-left"></i> Generar Reporte</button>
      
    </td>
</tr>
</table>
<hr/>
<br/>
<table class="table table-bordered table-striped"    id="reportes">
  <thead>
    <th>Documento</th>
    <th>Movimiento</th>
    <th>Fecha</th>
    <th>Impuesto</th>
    <th>Sub-Total</th>
    <th>Total</th>
  <thead>
  
   <tbody>
        <tr>
            <td colspan=6><center>No Hay Información</center></td>
        </tr>
        
   </tbody>
   <tfoot> 
   <tr>
    <td colspan=3 align="right"> </td>
    <td><label id="lblimpuesto" name="lblimpuesto">$ 0</label></td>
    <td><label id="lblsubtotal" name="lblsubtotal">$ 0</label></td>
    <td><label id="lbltotal" name="lbltotal">$ 0</label></td>
  </tr>
   
</tfoot> 
  </table>
      
                      
        <script type="text/javascript">
           $(function() {
                $( "#finicial" ).datepicker();
                $( "#ffinal" ).datepicker();
             });
        </script> 