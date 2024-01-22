<style type="text/css">
th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
</style>
<script type="text/javascript">
var baseurl = "<?php echo base_url(); ?>";
var currentLocation = window.location;
function EliminarCliente(Cliente, id,codigo){
    confirmar=confirm("Eliminar a " + Cliente + ", Recuerda Una vez Eliminado No podras Recuperarlo"); 

    if (confirmar){
    	document.getElementById('mensaje').innerHTML = "<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Eliminando Cliente...</center></div></div>";
    	 var Cliente 		 = new Object();
		Cliente.Id      	 = id;
		Cliente.Codigo      = codigo;
		var DatosJson = JSON.stringify(Cliente);
		$.post(currentLocation + '/deletecliente',
		{ 
			MiCliente: DatosJson
		},
		function(data, textStatus) {
			//
			$("#mensaje").html(data.error_msg);
		}, 
		"json"		
		);
    } else{
    } 
  }
  
</script>
<h1 class="page-header"><span class="glyphicon glyphicon-list-alt"></span> Catalogo de Clientes</h1>
<div id="mensaje"></div>
<p align="right">
 	 <a href="clientes/nuevo">
 	 	<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Nuevo Cliente</button>
 	 </a>  
 	 </p>
 	 <br/>
	<table id="clientes" border="0" cellpadding="0" cellspacing="0" width="100%" class="pretty">
		<thead>
			<tr>
				<th></th>
				<th>Codigo</th>
				<th>Nombre</th>
				<th>Direccion</th>
				<th>Ciudad</th>
				<th>Municipio</th>
				<th>Estado</th>
				<th>RFC</th>
				<th>F_registro</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$idEncriptado = "";
				if($clientes){
					foreach($clientes as $cliente){
						$codigoCliente = base64_encode($cliente->CODIGO_CLIENTE);
						$idCliente     = base64_encode($cliente->ID);
						echo '<tr>';
						echo '<td>';
						//echo '<a href="clientes/DirEnvio/'.$codigoCliente.'/'.$idCliente.'"><button type="button" title="Direcciones de Envio" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-list"></span></button></a> &nbsp;';
						echo '<a href="clientes/EditarCliente/'.$idCliente.'/'.$codigoCliente.'"><button type="button" title="Editar Cliente" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></button></a> &nbsp;';
						?>
						<button type="button" onclick="EliminarCliente('<?php echo $cliente->NOMBRE.' '.$cliente->APELLIDOS; ?>','<?php echo $idCliente; ?>','<?php echo $codigoCliente; ?>');" title="Eliminar Cliente" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
						<?php
						echo '</td>';
						echo '<td>'.$cliente->CODIGO_CLIENTE.'</td>';
						echo '<td>'.$cliente->NOMBRE.' '.$cliente->APELLIDOS.'</td>';
						echo '<td>'.$cliente->CALLE.'</td>';
						echo '<td>'.$cliente->LOCALIDAD.'</td>';
						echo '<td>'.$cliente->MUNICIPIO.'</td>';
						echo '<td>'.$cliente->ESTADO.'</td>';
						//echo '<td>'.$cliente->PAIS.'</td>';
						echo '<td>'.$cliente->RFC.'</td>';
						echo '<td>'.$cliente->FECHA_REGISTRO.'</td>';
						echo '</tr>';
					}
				}else{
					echo '<tr><td colspan=11><center>No Existe Informacion</center></td></tr>';
				}
			?>
		</tbody>
	</table>
<script type="text/javascript">

            $(document).ready(function() {
    $('#clientes').dataTable( {
        "scrollX": true
    } );
} );

</script>
			