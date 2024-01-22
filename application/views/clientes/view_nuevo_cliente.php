<input type="hidden" value="<?php echo @$cliente[0]->ID; ?>" id="id" name="id"> 
<input type="hidden" value="<?php echo @$cliente[0]->CP; ?>" id="cps" name="cps"> 
<input type="hidden" value="<?php echo @$cliente[0]->COLONIA;?>" id="col" name="col">
<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
  var codigopostal  = 0;
  var ids           = document.getElementById("id").value;
  ids               = parseInt(ids.length);
  var col           = document.getElementById("col").value;
  if(ids==0){
    codigopostal = 0;
  }else{
    codigopostal = document.getElementById("cps").value;;
  }
  function regresar(){
    window.location="<?php echo base_url()?>clientes";
  }
</script>
<?php
//Nombre
  $Nombre       = array(
  'name'        => 'Nombre',
  'id'          => 'Nombre',
  'size'        => 50,
  'value'       => set_value('Nombre',@$cliente[0]->NOMBRE),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );

  $apellidos    = array(
  'name'        => 'apellidos',
  'id'          => 'apellidos',
  'size'        => 50,
  'value'       => set_value('apellidos',@$cliente[0]->APELLIDOS),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );
  $cp           = array(
  'name'        => 'cp',
  'id'          => 'cp',
  'size'        => 50,
  'value'       => set_value('cp',@$cliente[0]->CP),
  'type'        => 'text',
  'class'       => 'form-control',
  'onkeypress'  => 'return validarNumeros(event);',
  );
  $pais         = array(
  'name'        => 'pais',
  'id'          => 'pais',
  'size'        => 50,
  'value'       => set_value('pais',@$cliente[0]->PAIS),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );
  $estado       = array(
  'name'        => 'estado',
  'id'          => 'estado',
  'size'        => 50,
  'value'       => set_value('estado',@$cliente[0]->ESTADO),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );
  $municipio    = array(
  'name'        => 'municipio',
  'id'          => 'municipio',
  'size'        => 50,
  'value'       => set_value('municipio',@$cliente[0]->MUNICIPIO),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );
  $ciudad       = array(
  'name'        => 'ciudad',
  'id'          => 'ciudad',
  'size'        => 50,
  'value'       => set_value('ciudad',@$cliente[0]->LOCALIDAD),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );
  $Calle        = array(
  'name'        => 'Calle',
  'id'          => 'Calle',
  'size'        => 50,
  'value'       => set_value('Calle',@$cliente[0]->CALLE),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );
  //email
  $email        = array(
  'name'        => 'email',
  'id'          => 'email',
  'size'        => 50,
  'value'       => set_value('email',@$cliente[0]->EMAIL),
  'type'        => 'text',
  'class'       => 'form-control',
  'onblur'      => 'validarEmail(this.value);',
  );//
  $rfc          = array(
  'name'        => 'rfc',
  'id'          => 'rfc',
  'size'        => 50,
  'value'       => set_value('rfc',@$cliente[0]->RFC),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onblur'      => 'validarRFC(this.value);',
  );
  $telefono     = array(
  'name'        => 'telefono',
  'id'          => 'telefono',
  'size'        => 50,
  'value'       => set_value('telefono',@$cliente[0]->TELEFONO),
  'type'        => 'text',
  'class'       => 'form-control',
  'onkeypress'  => 'return  validarNumeros(event)',
  );
?>
<script src="<?php echo base_url();?>js/JsonClientes.js"></script>
<h1 class="page-header"><span class="glyphicon glyphicon-th-list"></span> <?php echo $titulo; ?></h1>
<div id="mensaje"></div>
<form class="form-horizontal" name="formulario" id="formulario" role="form">
  <input type="hidden" value="0" id="validamail" name="validamail">
  <input type="hidden" value="0" id="validarfc" name="validarfc">
  <div class="form-group">
    <label for="Nombre" class="col-lg-3 control-label">Nombre:</label>
    <div class="col-lg-3">
      <?php echo form_input($Nombre); ?>
    </div>
  </div>
  

  <div class="form-group">
    <label for="apellidos" class="col-lg-3 control-label">Apellidos:</label>
    <div class="col-lg-3">
      <?php echo form_input($apellidos); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="cp" class="col-lg-3 control-label">Codigo Postal:</label>
    <div class="col-lg-3">
      <?php echo form_input($cp); ?>
    </div>
  </div>

<div class="form-group">
    <label for="pais" class="col-lg-3 control-label">Pais:</label>
    <div class="col-lg-3">
      <?php echo form_input($pais); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="estado" class="col-lg-3 control-label">Estado:</label>
    <div class="col-lg-3">
      <?php echo form_input($estado); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="municipio" class="col-lg-3 control-label">Municipio:</label>
    <div class="col-lg-3">
      <?php echo form_input($municipio); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="ciudad" class="col-lg-3 control-label">Ciudad:</label>
    <div class="col-lg-3">
       <?php echo form_input($ciudad); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="colonia" class="col-lg-3 control-label">Colonia:</label>
    <div class="col-lg-3">
      <select name="colonia" id="colonia" class="form-control">
        <option value='0'>Elige Colonia...</option>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label for="Calle" class="col-lg-3 control-label">Calle:</label>
    <div class="col-lg-3">
      <?php echo form_input($Calle);?>
    </div>
  </div>

  

   <div class="form-group">
    <label for="email" class="col-lg-3 control-label">Email:</label>
    <div class="col-lg-3">
      <?php echo form_input($email); ?>
    </div>
  </div>

   <div class="form-group">
    <label for="rfc" class="col-lg-3 control-label">RFC:</label>
    <div class="col-lg-3">
      <?php echo form_input($rfc); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="telefono" class="col-lg-3 control-label">Telefono:</label>
    <div class="col-lg-3">
      <?php echo form_input($telefono); ?>
    </div>
  </div>
 
  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-10">
      <button type="button" onclick="regresar()" class="btn btn-default">Regresar</button>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Cliente</button>
      <?php if($titulo=="Nuevo Cliente"){ ?>
      <button type="reset" class="btn btn-default">Nuevo</button>
      <?php } ?>
    </div>
  </div>
  <hr/>
</form>		
<script type="text/javascript">
  
</script>
