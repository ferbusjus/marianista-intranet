
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
    'maxlength' =>20,
    'value' => set_value ('password1', @$usuarios[0]->PASSWORD),
    'type' => 'password',
    'class' => 'form-control',
);

$Password2 = array(
    'name' => 'password2',
    'id' => 'password2',
    'size' => 20,
    'value' => set_value ('password2', @$usuarios[0]->PASSWORD),
    'type' => 'password',
    'class' => 'form-control',
);
?>
<h2 class="page-header"><span class="glyphicon glyphicon-th-list"></span> <?php echo $titulo; ?></h2>
<div id="mensaje"></div>
<form class="form-horizontal" name="formulario" id="formulario" role="form" method="POST">
    <input type="hidden" value="0" id="validamail" name="validamail">
    <div class="form-group">
        <label for="Nombre" class="col-lg-2 control-label">Tipo :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Nombre); ?>
        </div>
        
        <label for="apellidos" class="col-lg-2 control-label">Familia :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Apellidos); ?>
        </div>
    </div>

  <div class="form-group">
        <label for="Nombre" class="col-lg-2 control-label">Telefono 1 :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Nombre); ?>
        </div>
        
        <label for="apellidos" class="col-lg-2 control-label">Telefono 2 :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Apellidos); ?>
        </div>
    </div>
    
      <div class="form-group">
        <label for="Nombre" class="col-lg-2 control-label">Direccion :</label>
        <div class="col-lg-8">
            <?php echo form_input ($Nombre); ?>
        </div>
    </div>
    

            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Datos del Padre </div>
                    </div>     
                     <div style="padding-top:30px" class="panel-body" >
                        <div class="form-group">
                            <label for="Nombre" class="col-lg-2 control-label">Nombres :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Nombre); ?>
                            </div>

                            <label for="apellidos" class="col-lg-2 control-label">Apellidos :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Apellidos); ?>
                            </div>
                        </div>                

                        <div class="form-group">
                            <label for="Nombre" class="col-lg-2 control-label">Celular. :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Nombre); ?>
                            </div>

                            <label for="apellidos" class="col-lg-2 control-label">Tel. Trabajo :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Apellidos); ?>
                            </div>
                        </div>  

                        <div class="form-group">
                            <label for="Nombre" class="col-lg-2 control-label">E-Mail :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Nombre); ?>
                            </div>

                            <label for="apellidos" class="col-lg-2 control-label">DNI :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Apellidos); ?>
                            </div>
                        </div>  
                     </div>
            </div>     
    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Datos de la Madre </div>
                    </div>     
                     <div style="padding-top:30px" class="panel-body" >
                        <div class="form-group">
                            <label for="Nombre" class="col-lg-2 control-label">Nombres :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Nombre); ?>
                            </div>

                            <label for="apellidos" class="col-lg-2 control-label">Apellidos :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Apellidos); ?>
                            </div>
                        </div>                

                        <div class="form-group">
                            <label for="Nombre" class="col-lg-2 control-label">Celular. :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Nombre); ?>
                            </div>

                            <label for="apellidos" class="col-lg-2 control-label">Tel. Trabajo :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Apellidos); ?>
                            </div>
                        </div>  

                        <div class="form-group">
                            <label for="Nombre" class="col-lg-2 control-label">E-Mail :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Nombre); ?>
                            </div>

                            <label for="apellidos" class="col-lg-2 control-label">DNI :</label>
                            <div class="col-lg-3">
                                <?php echo form_input ($Apellidos); ?>
                            </div>
                        </div>  
                     </div>
            </div>   
    
<!--
    <div class="form-group">
        <label for="email" class="col-lg-2 control-label">Email :</label>
        <div class="col-lg-3">
            <?php echo form_input ($email); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="password1" class="col-lg-2 control-label">Telefono :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Password1); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="password2" class="col-lg-2 control-label">Direccion :</label>
        <div class="col-lg-3">
            <?php echo form_input ($Password2); ?>
        </div>
    </div>
-->
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-9">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Cambios</button>
        </div>
    </div>
    <hr/>
</form>		
