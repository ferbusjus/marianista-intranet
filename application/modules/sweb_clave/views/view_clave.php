<input type="hidden" value="<?php echo @$usuarios[0]->ID; ?>" id="id" name="id"> 
<?php
//Nombre
$Nombre = array(
    'name' => 'Nombre',
    'id' => 'Nombre',
    'size' => 50,
    'disabled' =>'disabled',
    'value' => set_value ('Nombre', @$usuarios[0]->NOMBRE),
    'type' => 'text',
    'class' => 'form-control',
    'style' => 'text-transform:uppercase',
    'onkeypress' => 'return validarn(event);',
);

$Apellidos = array(
    'name' => 'Apellidos',
    'id' => 'Apellidos',
    'disabled' =>'disabled',    
    'size' => 50,
    'value' => set_value ('Apellidos', @$usuarios[0]->APELLIDOS),
    'type' => 'text',
    'class' => 'form-control',
    'style' => 'text-transform:uppercase',
    'onkeypress' => 'return validarn(event);',
);

$email = array(
    'name' => 'email',
    'id' => 'email',
    'size' => 50,
    'value' => set_value ('email', @$usuarios[0]->EMAIL),
    'type' => 'text',
    'maxlength' =>50,
    'class' => 'form-control',
    'onblur' => 'validarEmail(this.value);',
);

$Password1 = array(
    'name' => 'password1',
    'id' => 'password1',
    'size' => 20,
        'disabled' =>'disabled',    
    'maxlength' =>20,
    'value' => set_value ('password1', @$usuarios[0]->PASSWORD),
    'type' => 'form-control',
    'class' => 'form-control',
);

$Password2 = array(
    'name' => 'password2',
    'id' => 'password2',
    'size' => 20,
    'maxlength' =>10,
    'value' => '', //set_value ('password2', @$usuarios[0]->PASSWORD)
    'type' => 'form-control',
    'class' => 'form-control',
);
?>
<div id="mensaje"></div>
<form class="form-horizontal" name="formulario" id="formulario" role="form" method="POST">
    <input type="hidden" value="0" id="validamail" name="validamail">
    <div class="form-group" style="display:none;">
        <label for="Nombre" class="col-lg-3 control-label">Tipo :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Nombre); ?>
        </div>
    </div>


    <div class="form-group" >
        <label for="apellidos" class="col-lg-3 control-label">Familia </label>
        <div class="col-lg-6">
            <?php echo form_input ($Apellidos); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="email" class="col-lg-3 control-label">Correo </label>
        <div class="col-lg-6">
            <?php echo form_input ($email); ?>
        </div>
    </div>

    <div class="form-group">    
        <label for="password1" class="col-lg-3 control-label">Clave Actual</label>
        <div class="col-lg-4">
            <?php echo form_input ($Password1); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="password2" class="col-lg-3 control-label">Nueva Clave</label>
        <div class="col-lg-4">
            <?php echo form_input ($Password2); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-9">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Cambios</button>
        </div>
    </div>

</form>		
