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
function EliminarCategoria(Categoria, id){
    confirmar=confirm("Eliminar a " + Categoria + ", Al Eliminar se Borraran las Subcategorias, Recuerda Una vez Eliminado No podras Recuperarlo"); 

    if (confirmar){
    	document.getElementById('mensaje').innerHTML = "<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Eliminando Categoria...</center></div></div>";
    	 var Categoria 		 = new Object();
		Categoria.Id      	 = id;  
		var DatosJson = JSON.stringify(Categoria);
		$.post(currentLocation + '/DeleteCategoria',
		{ 
			MiCategoria: DatosJson
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
<h1 class="page-header"><span class="glyphicon glyphicon-list-alt"></span> Catalogo de Categorias</h1>
<div id="mensaje"></div>
<p align="right">
 	 <a href="categorias/nuevo">
 	 	<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Nuevo Categoria</button>
 	 </a>  
 	 </p>
 	 <br/>
	<table id="categorias" border="0" cellpadding="0" cellspacing="0" width="100%" class="pretty">
		<thead>
			<tr>
				<th></th>
				<th>N°</th>
				<th>Categoria</th>
				<th>Estatus</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if($categorias){
					$contador = 0;
					foreach($categorias as $categoria){
						$idcategoria     = base64_encode($categoria->id);
						$descrip         = base64_encode($categoria->descripcion);
						$contador        = $contador + 1;
						echo '<tr>';
						echo '<td>';
						echo '<a href="categorias/Editar/'.$idcategoria.'"><button type="button" title="Editar Categoria" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></button></a> &nbsp;';
						echo '<a href="categorias/Subcategoria/'.$idcategoria.'/'.$descrip.'"><button type="button"   title="Asignar Sub-categoria" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-list-alt"></span></button></a> &nbsp;';
						?>
						
						<button type="button" onclick="EliminarCategoria('<?php echo $categoria->descripcion; ?>','<?php echo $idcategoria; ?>');" title="Eliminar Categoria" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button>

						<?php
						echo '</td>';
						echo '<td>'.$contador.'</td>';
						echo '<td>'.$categoria->descripcion.'</td>';
						echo '<td>';
						if($categoria->estatus==1){
							echo "Activo";
						}else{
							echo "Inactivo";
						}
						echo '</td>'; 
						echo '</tr>';
					}
				}else{
					echo '<tr><td colspan=5><center>No Existe Informacion</center></td></tr>';
				}
			?>
		</tbody>
	</table>
<script type="text/javascript">

            $(document).ready(function() {
    $('#categorias').dataTable( {
        "scrollX": false
    } );
} );

</script>
			