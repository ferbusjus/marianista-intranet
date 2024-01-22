<input type="hidden" value="<?php echo @$usuarios[0]->ID; ?>" id="id" name="id"> 
<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
 
  function regresar(){
    window.location="<?php echo base_url()?>usuarios";
  }
</script>
<?php
//Nombre
  $Nombre       = array(
  'name'        => 'Nombre',
  'id'          => 'Nombre',
  'size'        => 50,
  'value'       => set_value('Nombre',@$usuarios[0]->NOMBRE),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );

  $Apellidos    = array(
  'name'        => 'Apellidos',
  'id'          => 'Apellidos',
  'size'        => 50,
  'value'       => set_value('Apellidos',@$usuarios[0]->APELLIDOS),
  'type'        => 'text',
  'class'       => 'form-control',
  'style'       => 'text-transform:uppercase',
  'onkeypress'  => 'return validarn(event);',
  );

  $email        = array(
  'name'        => 'email',
  'id'          => 'email',
  'size'        => 50,
  'value'       => set_value('email',@$usuarios[0]->EMAIL),
  'type'        => 'text',
  'class'       => 'form-control',
  'onblur'      => 'validarEmail(this.value);',
  );
  $TipoUsuario    = array(
  '0'             => '---Elegir Opción---',
  '1'             => 'Administrador',
  '2'             => 'Vendedor',
  );

  $Password1    = array(
  'name'        => 'password1',
  'id'          => 'password1',
  'size'        => 50,
  'value'       => set_value('password1',@$usuarios[0]->PASSWORD),
  'type'        => 'password',
  'class'       => 'form-control',
  );

  $Password2    = array(
  'name'        => 'password2',
  'id'          => 'password2',
  'size'        => 50,
  'value'       => set_value('password2',@$usuarios[0]->PASSWORD),
  'type'        => 'password',
  'class'       => 'form-control',
  );
  $Estatus        = array(
  '0'             => '---Elegir Opción---',
  '1'             => 'Activo',
  '2'             => 'Inactivo',
  );
  $disabled = "";
  if($this->session->userdata('ID')==@$usuarios[0]->ID and $this->session->userdata('TIPOUSUARIO')!=1){
    $disabled = "disabled='disabled'";
  }

 
?>
<script src="<?php echo base_url();?>js/JsonUsuarios.js"></script>
<h1 class="page-header"><span class="glyphicon glyphicon-th-list"></span> <?php echo $titulo; ?></h1>
<div id="mensaje"></div>
<form class="form-horizontal" name="formulario" id="formulario" role="form">
  <input type="hidden" value="0" id="validamail" name="validamail">
  <div class="form-group">
    <label for="Nombre" class="col-lg-3 control-label">Nombre:</label>
    <div class="col-lg-3">
      <?php echo form_input($Nombre); ?>
    </div>
  </div>
  

  <div class="form-group">
    <label for="apellidos" class="col-lg-3 control-label">Apellidos:</label>
    <div class="col-lg-3">
      <?php echo form_input($Apellidos); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="email" class="col-lg-3 control-label">Email:</label>
    <div class="col-lg-3">
      <?php echo form_input($email); ?>
    </div>
  </div>

 <div class="form-group">
    <label for="TipoUsuario" class="col-lg-3 control-label">Tipo Usuario:</label>
    <div class="col-lg-3">
      <?php echo  form_dropdown('TipoUsuario', $TipoUsuario, set_value('TipoUsuario',@$usuarios[0]->TIPO),'class="form-control" id="TipoUsuario" '.$disabled.' '); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="password1" class="col-lg-3 control-label">Password:</label>
    <div class="col-lg-3">
      <?php echo form_input($Password1); ?>
    </div>
  </div>

   <div class="form-group">
    <label for="password2" class="col-lg-3 control-label">Confirmar Password:</label>
    <div class="col-lg-3">
      <?php echo form_input($Password2); ?>
    </div>
  </div>

  <div class="form-group">
    <label for="Estatus" class="col-lg-3 control-label">Estatus:</label>
    <div class="col-lg-3">
      <?php echo  form_dropdown('Estatus', $Estatus, set_value('Estatus',@$usuarios[0]->ESTATUS),'class="form-control" id="Estatus"'); ?>
    </div>
  </div>
 
  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-10">
      <?php if($this->session->userdata('TIPOUSUARIO')==1){ ?>
      <button type="button" onclick="regresar()" class="btn btn-default">Regresar</button>
      <?php } ?>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Usuario</button>
      <?php if($titulo=="Nuevo Usuario"){ ?>
      <button type="reset" class="btn btn-default">Nuevo</button>
      <?php } ?>
    </div>
  </div>
  <hr/>
</form>		
<script type="text/javascript">
  
</script>
