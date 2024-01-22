<script src="<?php echo base_url()?>js/jquery-1.10.2.js"></script>
<script src="<?php echo base_url()?>js/jquery-ui.js"></script>
<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
</script>
<script src="<?php echo base_url();?>js/JsonOrdenCompra.js"></script>
<script type="text/javascript">
function AsignaSession(){
  document.getElementById("idsession").value="<?php echo md5(rand(1000,50000)); ?>";
}
</script>
<h1 class="page-header"><span class="glyphicon glyphicon-list"></span> Ordenes de Compra</h1>
<div id="mensaje"></div>
<hr/><br/>
<table border=0 width="100%">
    <tr>
      <td>Producto: </td>
      <td><input type="text" name="BuscaProducto" id="BuscaProducto" class="form-control input-sm" autocomplete="off" size="30" /></td>
      <td>Cantidad:</td>
      <td><input  type="text" name="cantidad" id="cantidad" autocomplete="off"  class="form-control input-sm" size="3" value="1" /></td>
      <td>
        <input type="hidden" name="descripcion"  id="descripcion" />
        <input type="hidden" name="costo"  id="costo" />
        <input type="hidden" name="idProveedor"  id="idProveedor" />
        <input type="hidden" name="codigo"  id="codigo" />
        <input type="hidden" name="idsession"  id="idsession" value="<?php echo md5(rand(1000,50000)); ?>" /></td>
      <td>Existencia:</td>
      <td>
        <input type="text" name="existencia" id="existencia" class="form-control input-sm"  size="3" readonly="readonly"/>
      </td>
      <td>Precio Compra:</td>
      <td>
        <input type="text" name="precio"  id="precio" class="form-control input-sm" size="3" readonly="readonly"/>
      </td>
      <td>Precio Venta:</td>
      <td><input type="text" name="precioventa" class="form-control input-sm"  id="precioventa" size="3" readonly="readonly"/></td>
     <td>
      &nbsp;<button type="submit" id="AgregaProducto" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>
      
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
    <th>Precio Compra</th>
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
 <center>
  <button type="reset" class="btn btn-default" onclick="javascript:location.reload();"><span class="glyphicon glyphicon-edit"></span> Nueva Orden de Compra</button> &nbsp;
  <button type="submit" id="SaveOrder" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved"></span> Guardar Orden Compra</button></center>
</form>		

