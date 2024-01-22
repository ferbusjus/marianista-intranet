<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
</script>
<script src="<?php echo base_url()?>js/jquery-1.10.2.js"></script>
<script src="<?php echo base_url()?>js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>js/JsonVenta.js"></script>
<script type="text/javascript">
function AsignaSession(){
  document.getElementById("idsessionventa").value="<?php echo md5(rand(1000,50000)); ?>";
}
function RefrescarPagina(){
  if(confirm("Si cambias de Cliente se Perderan los Cambios")) { 
    location.reload();
  }else{

    return false;
  }

}
</script>
<h1 class="page-header"><span class="glyphicon glyphicon-list"></span> Nueva Venta</h1>
<div id="mensaje"></div>
<hr/><br/>
<table border=0 width="100%">
	<tr>
		<td colspan="10">
			<table>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;Cliente:&nbsp;&nbsp;&nbsp;</td>
					<td><input type="text" name="BuscaCliente" id="BuscaCliente" class="form-control input-sm" autocomplete="off" size="30" /></td>
					<td>&nbsp;<button type="reset" class="btn btn-default" onclick="RefrescarPagina();"><span class="glyphicon glyphicon-refresh"></span> Cambiar Cliente</button></td>
					<td>&nbsp;<label id="lblNombrecliente" name="lblNombrecliente"> </label>
            <label id="lblidcliente" name="lblidcliente"> </label>
          </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="10"><br/></td>
	</tr>
    <tr>
      <td>Producto: </td>
      <td><input type="text" name="BuscaProducto" id="BuscaProducto" disabled="disabled" class="form-control input-sm" autocomplete="off" size="30" /></td>
      <td>Cantidad:</td>
      <td><input  type="text" name="cantidad" id="cantidad" onkeypress="return validarNumeros(event)" autocomplete="off"  class="form-control input-sm" size="3" value="1" /></td>
      <td>
        <input type="hidden" name="descripcion"  id="descripcion" />
        <input type="hidden" name="costo"  id="costo" />
        <input type="hidden" name="idProveedor"  id="idProveedor" />
        <input type="hidden" name="codigo"  id="codigo" />
        <input type="hidden" name="Proveedor"  id="Proveedor" />
        <input type="hidden" name="Cliente"  id="Cliente" />
        <input type="hidden" name="idsessionventa"  id="idsessionventa" value="<?php echo md5(rand(1000,50000)); ?>" /></td>
      <td>Existencia:</td>
      <td>
        <input type="text" name="existencia" id="existencia" class="form-control input-sm"  size="3" readonly="readonly"/>
      </td>
      <td>Precio:</td>
      <td><input type="text" name="precioventa" class="form-control input-sm"  id="precioventa" size="3" readonly="readonly"/></td>
     <td>
      &nbsp;<button type="submit" disabled="disabled" id="AgregaProducto" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>
      
    </td>
    </tr>
    </table>
<br/><hr/><br/>
<form   name="formulario" id="formulario" role="form">
<table class="table table-bordered table-striped"    id="carrito">
  <thead>
    <th>Código</th>
    <th>Descripcion</th>
    <th>Proveedor</th>
    <th>Precio</th>
    <th>Cantidad</th>
    <th>Total</th>
    <th></th>
  <thead>
  
   <tbody>
        <tr>
            <td colspan=7><center>No Hay Productos Agregados</center></td>
        </tr>
        
   </tbody>
   <tfoot> 
   <tr>
    <td colspan=5 align="right">Sub-Total:</td>
    <td colspan=2><label id="lblsubtotal" name="lblsubtotal">$ 0</label><input type="hidden" name="txtsubtotal" id="txtsubtotal" value="0"/C></td>
  </tr>
  <tr>
    <td colspan=5 align="right">IVA:</td>
    <td colspan=2><label id="lbliva" name="lbliva">$ 0</label><input type="hidden" name="txtIva" id="txtIva" value="0"/></td>
  </tr>
  <tr>
    <td colspan=5 align="right">Total:</td>
    <td colspan=2><label id="lbltotal" name="lbltotal">$ 0</label><input type="hidden" name="txtTotal" id="txtTotal" value="0"/></td>
  </tr>
</tfoot> 
  </table>
  <!--<p class="bg-success">Direcciónes de Embarque</p>
  <input type="radio" value="0"/> Dir. de Embarque <input type="radio" value="1"> Dir de Recolección. 
<br/>
  Dir. de Entrega: 
  <select name="dirEnvio">
    <option value="0">--Elige Dirección</option>
  </select>-->
 <center>
  <button type="reset" class="btn btn-default" onclick="javascript:location.reload();"><span class="glyphicon glyphicon-edit"></span> Nueva Venta</button> &nbsp;
  <button type="submit" id="SaveOrder" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved"></span> Crear Venta</button></center>
</form>		
<!-- Popup de confirmacion -->

