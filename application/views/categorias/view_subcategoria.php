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
function EliminarSubCategoria(Subcategoria, id){
    confirmar=confirm("Eliminar a " + Subcategoria + ", Recuerda Una vez Eliminado No podras Recuperarlo"); 

    if (confirmar){
    	document.getElementById('mensaje').innerHTML = "<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Eliminando SubCategoria...</center></div></div>";
    	 var SubCategoria 		 = new Object();
		SubCategoria.Id      	 = id; 
		var DatosJson = JSON.stringify(SubCategoria);
		$.post(baseurl + 'categorias/DeleteSubcategoria',
		{ 
			MiSubCategoria: DatosJson
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
<h1 class="page-header"><span class="glyphicon glyphicon-list-alt"></span> Catalogo de SubCategorias [<?php echo $descripcionS; ?>]</h1>
<div id="mensaje"></div>
<p align="right">
	<a href="<?php echo base_url(); ?>categorias">
		<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Ir a Categorias</button>
	</a>
 	 <a href="<?php echo base_url(); ?>categorias/nuevosubcategoria/<?php echo base64_encode($idcategoria); ?>/<?php echo base64_encode($descripcionS); ?>">
 	 	<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Nuevo SubCategoria</button>
 	 </a>  
 	 </p>
 	 <br/>
	<table id="categorias" border="0" cellpadding="0" cellspacing="0" width="100%" class="pretty">
		<thead>
			<tr>
				<th></th>
				<th>N°</th>
				<th>Categoria</th>
				<th>Sub-Categoria</th>
				<th>Estatus</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if($subcategoria){
					$contador = 0;
					foreach($subcategoria as $subcat){
						$idsubcategoria     = base64_encode($subcat->id);
						$contador           = $contador + 1;
						echo '<tr>';
						echo '<td>';
						echo '<a href="'.base_url().'categorias/editarsubcategoria/'.base64_encode($subcategoria[0]->id_categoria).'/'.base64_encode($descripcionS).'/'.$idsubcategoria.'"><button type="button" title="Editar Categoria" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></button></a> &nbsp;';
						?>
						
						<button type="button" onclick="EliminarSubCategoria('<?php echo $subcat->descripcion; ?>','<?php echo $idsubcategoria; ?>');" title="Eliminar Categoria" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button>

						<?php
						echo '</td>';
						echo '<td>'.$contador.'</td>';
						echo '<td>'.$descripcionS.'</td>';
						echo '<td>'.$subcat->descripcion.'</td>';
						echo '<td>';
						if($subcat->estatus==1){
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
			