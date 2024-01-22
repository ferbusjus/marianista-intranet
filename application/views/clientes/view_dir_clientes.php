<script type="text/javascript">
  var baseurl = "<?php echo base_url(); ?>";
</script>
<script src="<?php echo base_url();?>js/JsonClientesDireccion.js"></script>
<h1 class="page-header"><span class="glyphicon glyphicon-th-list"></span> Alta Direccion de Envio</h1>
<div id="mensaje"></div>




<form name="formulario" id="formulario" role="form">

  <input type="hidden" value="<?php echo $codigoClie; ?>" id="idcliente" name="idcliente">
  <div class="form-group">
    <label for="Direccion" class="col-lg-3 control-label">Dirección:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" name="Direccion" id="Direccion" onkeypress="return validarn(event);" style="text-transform:uppercase" />
    </div>
  </div>
  

  <div class="form-group">
    <label for="nExterior" class="col-lg-3 control-label">Nº Exterior:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress="return validarNumeros(event)"  name="nExterior" id="nExterior"/>
    </div>
  </div>
<br/><br/>
  <div class="form-group">
    <label for="nInterior" class="col-lg-3 control-label">Nº Interior:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress="return validarNumeros(event)"  name="nInterior" id="nInterior" />
    </div>
  </div>

<div class="form-group">
    <label for="cp" class="col-lg-3 control-label">Código Postal:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress="return validarNumeros(event)"  name="cp" id="cp" style="text-transform:uppercase"/>
    </div>
  </div>
<br/><br/>
  <div class="form-group">
    <label for="estado" class="col-lg-3 control-label">Estado:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress="return validarn(event)"  name="estado" id="estado" style="text-transform:uppercase" />
    </div>
  </div>

  <div class="form-group">
    <label for="municipio" class="col-lg-3 control-label">Municipio:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress="return validarn(event)"  name="municipio" id="municipio" style="text-transform:uppercase"/>
    </div>
  </div>
<br/><br/>
  <div class="form-group">
    <label for="ciudad" class="col-lg-3 control-label">Ciudad:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress="return validarn(event)"  name="ciudad" id="ciudad" style="text-transform:uppercase"/>
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
<br/><br/>

  <div class="form-group">
    <label for="telefono" class="col-lg-3 control-label">Telefono:</label>
    <div class="col-lg-3">
      <input type="text"  class="form-control"  name="telefono" id="telefono" onkeypress="return  validarNumeros(event)" />
    </div>
  </div>

  <div class="form-group">
    <label for="Referencias" class="col-lg-3 control-label">Referencias:</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" onkeypress=""  name="Referencias" id="Referencias" style="text-transform:uppercase" />
    </div>
  </div>
 <br/><br/>
  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-10">
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span> Guardar Dirección Envio</button>
      <button type="reset" class="btn btn-default">Nuevo</button>
    </div>
  </div>
</form>		

