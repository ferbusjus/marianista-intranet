<input type="hidden" value="<?php echo @$proveedor[0]->id; ?>" id="id" name="id"> 
<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
 
  function regresar(){
    window.location="<?php echo base_url()?>proveedores";
  }
</script>
<?php
//Nombre
  $Nombre       = array(
  'name'        => 'Nombre',
  'id'          => 'Nombre',
  'size'        => 50,
  'value'       => set_value('Nombre',@$proveedor[0]->nombre_proveedor),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );

  $direccion    = array(
  'name'        => 'direccion',
  'id'          => 'direccion',
  'size'        => 50,
  'value'       => set_value('direccion',@$proveedor[0]->direccion),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  );
  $telefono     = array(
  'name'        => 'telefono',
  'id'          => 'telefono',
  'size'        => 50,
  'value'       => set_value('telefono',@$proveedor[0]->telefono),
  'type'        => 'text',
  'class'       => 'form-control',
  'onkeypress'  => 'return validarNumeros(event);',
  );
   $cp           = array(
  'name'        => 'cp',
  'id'          => 'cp',
  'size'        => 50,
  'value'       => set_value('cp',@$proveedor[0]->cp),
  'type'        => 'text',
  'class'       => 'form-control',
  'onkeypress'  => 'return validarNumeros(event);',
  );
 
?>
<script src="<?php echo base_url();?>js/JsonProveedores.js"></script>
<h1 class="page-header"><span class="glyphicon glyphicon-th-list"></span> <?php echo $titulo; ?></h1>
<div id="mensaje"></div>
<form class="form-horizontal" name="formulario" id="formulario" role="form">
  <input type="hidden" value="0" id="validamail" name="validamail">
  <input type="hidden" value="0" id="validarfc" name="validarfc">
  <div class="form-group">
    <label for="Nombre" class="col-lg-3 control-label">Proveedor:</label>
    <div class="col-lg-3">
      <?php echo form_input($Nombre); ?>
    </div>
  </div>
  

  <div class="form-group">
    <label for="apellidos" class="col-lg-3 control-label">Direccion:</label>
    <div class="col-lg-3">
      <?php echo form_input($direccion); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="cp" class="col-lg-3 control-label">Telefono:</label>
    <div class="col-lg-3">
      <?php echo form_input($telefono); ?>
    </div>
  </div>

 <div class="form-group">
    <label for="cp" class="col-lg-3 control-label">Codigo Postal:</label>
    <div class="col-lg-3">
      <?php echo form_input($cp); ?>
    </div>
  </div>

 
  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-10">
      <button type="button" onclick="regresar()" class="btn btn-default">Regresar</button>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Proveedor</button>
      <?php if($titulo=="Nuevo Cliente"){ ?>
      <button type="reset" class="btn btn-default">Nuevo</button>
      <?php } ?>
    </div>
  </div>
  <hr/>
</form>		
<script type="text/javascript">
  
</script>
