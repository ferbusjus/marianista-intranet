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
function EliminarProducto(producto, id,codigo){
    confirmar=confirm("Eliminar Producto: " + producto + ", Recuerda Una vez Eliminado No podras Recuperarlo"); 

    if (confirmar){
    	document.getElementById('mensaje').innerHTML = "<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Eliminando Producto...</center></div></div>";
    	 var Producto 		 = new Object();
		Producto.Id      	 = id;
		Producto.Codigo      = codigo;
		var DatosJson = JSON.stringify(Producto);
		$.post(currentLocation + '/deleteproducto',
		{ 
			MiProducto: DatosJson
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
<h1 class="page-header"><span class="glyphicon glyphicon-list-alt"></span> Catalogo de Productos</h1>
<div id="mensaje"></div>
	<p align="right">
 	 <a href="productos/nuevo">
 	 	<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Nuevo Producto</button>
 	 </a>  
 	 </p>
 	 <br/>
 	 
	<table id="productos" border="0" cellpadding="0" cellspacing="0" width="100%" class="pretty">
		<thead>
			<tr>

				<th></th>
				<th>Clave</th>
				<th>Producto</th>
				<th>Existencia</th>
				<th>Costo</th>
				<th>Precio</th>
				<th>Familia</th>
				<th>Sub Familia</th>
				<th>Proveedor</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if($productos){
					foreach($productos as $producto){
						$codigo       = base64_encode($producto->codigo);
						$id           = base64_encode($producto->id);

						echo '<tr>';
						echo '<td>';
						echo '<a href="productos/editarProducto/'.$id.'/'.$codigo.'"><button type="button" title="Editar Producto" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></button></a> &nbsp;';
						?>
						<a ><button onclick="EliminarProducto('<?php echo $producto->descripcion; ?>','<?php echo $id; ?>','<?php echo $codigo; ?>');" type="button" title="Eliminar Producto" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button></a>
						<?php
						echo '</td>';
						echo '<td>'.$producto->codigo.'</td>';
						echo '<td>'.$producto->descripcion.'</td>';
						echo '<td>'.$producto->cantidad.'</td>';
						echo '<td>$ '.$producto->precio_compra.'</td>';
						echo '<td>$ '.$producto->precio_venta.'</td>';
						echo '<td>'.$producto->DesCategoria.'</td>';
						echo '<td>'.$producto->DesCategoria.'</td>';
						echo '<td>'.$producto->nombre_proveedor.'</td>';
						echo '</tr>';
					}
				}else{
					echo '<tr><td colspan=9><center>No Existe Informacion</center></td></tr>';
				}
			?>
		</tbody>
	</table>
<script type="text/javascript">

    $(document).ready(function() {
    $('#productos').dataTable( {
        "scrollX": true
    } );
} );

</script>