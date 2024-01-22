<input type="hidden" name="id" id="id" value="<?php echo @$productos[0]->id; ?>"> 
<input type="hidden" name="cat" id="cat" value="<?php echo @$productos[0]->id_categoria; ?>"> 
<input type="hidden" name="subcat" id="subcat" value="<?php echo @$productos[0]->id_subcategoria; ?>">
<input type="hidden" name="prov" id="prov" value="<?php echo @$productos[0]->id_proveedor; ?>"> 
<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
  var id_categoria  = 0;
  var id_subcat     = 0;
  var idprov        = 0;
  var ids           = document.getElementById("id").value;
  ids               = parseInt(ids.length);
  
  if(ids==0){
    id_categoria = 0;
    id_subcat    = 0;
    idprov       = 0;
  }else{
    id_categoria     = document.getElementById("cat").value;
    id_subcat        = document.getElementById("subcat").value;
    idprov           = document.getElementById("prov").value;
  }
  function regresar(){
    window.location="<?php echo base_url()?>productos";
  }
</script>
<?php
  //Codigo Barras
  $codigoBarras = array(
  'name'        => 'codigo',
  'id'          => 'codigo',
  'size'        => 50,
  'value'       => set_value('codigo',@$productos[0]->codigo),
  'type'        => 'text',
  'class'       => 'form-control',
  //'onkeypress'  => 'return letras(event)',
  );
  //Descripcion
  $Descripcion  = array(
  'name'        => 'descripcion',
  'id'          => 'descripcion',
  'size'        => 50,
  'value'       => set_value('descripcion',@$productos[0]->descripcion),
  'rows'        => '2',
  'class'       => 'form-control',
  );
  //
  $PCompra      = array(
  'name'        => 'pcompra',
  'id'          => 'pcompra',
  'size'        => 50,
  'value'       => set_value('pcompra',@$productos[0]->precio_compra),
  'type'        => 'text',
  'onkeypress'  => "return  SoloNumerosDecimales3(event, '0.0', 4, 2);",
  'class'       => 'form-control',
  );
  $PVenta      = array(
  'name'        => 'pventa',
  'id'          => 'pventa',
  'size'        => 50,
  'value'       => set_value('pventa',@$productos[0]->precio_venta),
  'type'        => 'text',
  'onkeypress'  => "return  SoloNumerosDecimales3(event, '0.0', 4, 2);",
  'class'       => 'form-control',
  );
  $OpcionesUnida= array(
  '0'                 => '---Elige Unidad de Media---',
  'Unidad/Pza'        => 'Unidad/Pza',
  'Litro'             => 'Litro',
  'Kilo'              => 'Kilo',
  );

  $Inventariable  = array(
  '0'             => '---Elegir Opción---',
  '1'             => 'Si',
  '2'             => 'No',
  );
  $Stock      = array(
  'name'        => 'stock',
  'id'          => 'stock',
  'size'        => 50,
  'value'       => set_value('stock',@$productos[0]->stock),
  'type'        => 'text',
  'onkeypress'  => "return  validarNumeros(event)",
  'class'       => 'form-control',
  );

?>
<script src="<?php echo base_url();?>js/JsonProductos.js"></script>
<h1 class="page-header"><span class="glyphicon glyphicon-th-list"></span> <?php echo $titulo; ?></h1>
<div id="mensaje"></div>
<form class="form-horizontal" name="formulario" id="formulario" role="form">
  <div class="form-group">
    <label for="codigo" class="col-lg-3 control-label">Código de Barras:</label>
    <div class="col-lg-3">
    <?php echo form_input($codigoBarras); ?>
    </div>
  </div>
  <div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label">Descripción:</label>
    <div class="col-lg-3">
      <?php  echo form_textarea($Descripcion); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="pcompra" class="col-lg-3 control-label">Precio Compra:</label>
    <div class="col-lg-3">
    <?php echo form_input($PCompra); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="pventa" class="col-lg-3 control-label">Precio Venta:</label>
    <div class="col-lg-3">
      <?php echo form_input($PVenta); ?> 
    </div>
  </div>

  <div class="form-group">
    <label for="unidadmedida" class="col-lg-3 control-label">Unidad de Medida:</label>
    <div class="col-lg-3">
      <?php echo  form_dropdown('unidadmedida', $OpcionesUnida, set_value('unidadmedida',@$productos[0]->unidadmedida),'class="form-control" id="unidadmedida"'); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="categoria" class="col-lg-3 control-label">Categoria:</label>
    <div class="col-lg-3">
      <select name="categoria" id="categoria" class="form-control"></select>
    </div>
  </div>

  <div class="form-group">
    <label for="subcategoria" class="col-lg-3 control-label">Sub-Categoria:</label>
    <div class="col-lg-3">
      <select name="subcategoria" id="subcategoria" class="form-control"></select>
    </div>
  </div>

  <div class="form-group">
    <label for="inventario" class="col-lg-3 control-label">El Producto es Inventariable:</label>
    <div class="col-lg-3">
      <?php echo  form_dropdown('inventario', $Inventariable, set_value('inventario',@$productos[0]->inventariable),'class="form-control" id="inventario"'); ?>
    </div>
  </div>

   <div class="form-group">
    <label for="stock" class="col-lg-3 control-label">Stock Minimo:</label>
    <div class="col-lg-3">
      <?php echo form_input($Stock); ?>
    </div>
  </div>

   <div class="form-group">
    <label for="proveedor" class="col-lg-3 control-label">Proveedor:</label>
    <div class="col-lg-3">
      <select name="proveedor" id="proveedor" class="form-control"></select>
    </div>
  </div>
 
  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-10">
      <button type="button" onclick="regresar()" class="btn btn-default">Regresar</button>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Producto</button>
      <?php if($titulo=="Nuevo Producto"){ ?>
      <button type="reset" class="btn btn-default">Nuevo</button>
      <?php } ?>
    </div>
  </div>
  <hr/>
</form>   

