$(document).ready(function(){
	//alert(id_categoria);
	$('#codigo').focus();
	$('#subcategoria').prop('disabled', true);
	$('#stock').prop('disabled', true);
	$('#subcategoria').append("<option value='0'>Elige Sub-Categoria...</option>");
	//Cargamos Categorias
	var categoria = $("#categoria");
	categoria.append("<option value='0'>Cargando Categoria...</option>");
	$.getJSON(baseurl + "productos/categorias",function(objetosretorna){
		categoria.empty();
		categoria.append("<option value='0'>---Elige Categoria---</option>");
		$.each(objetosretorna, function(i,ObjetoReturn){
			var seleccion = "";
			if(id_categoria==ObjetoReturn.id){
				seleccion = "selected='selected'";
			}
			var nuevaFila = "<option value='"+ObjetoReturn.id+"' "+seleccion+">" + ObjetoReturn.descripcion+"</option>";
			categoria.append(nuevaFila);
		});
	});
	//Si es diferente a cero hacemos select 
	if(id_subcat!=0){
			$('#subcategoria').empty();
			$("#subcategoria").append("<option value='0'>Cargando Subcategoria...</option>");
			$.getJSON(baseurl + "productos/subcategorias",{filtro: id_categoria},function(objetosretorna){
				$("#subcategoria").empty();
				$("#subcategoria").append("<option value='0'>Elige Sub-Categoria...</option>");
				$.each(objetosretorna, function(i,subcategoria){
					var seleccion2 = "";
					if(id_subcat==subcategoria.id){
						seleccion2 = "selected='selected'";
					}
					$("#subcategoria").append("<option value='"+subcategoria.id+"' "+seleccion2+">" + subcategoria.descripcion+"</option>");
				});
			});
			$('#subcategoria').prop('disabled', false);
			$('#codigo').prop('disabled', true);
	}
	//Cargamos Proveedores
	var proveedor = $("#proveedor");
	proveedor.append("<option value='0'>---Elige Proveedor---</option>");
	$.getJSON( baseurl + "productos/proveedores",function(objetosretorna1){
		$.each(objetosretorna1, function(i,ObjetoReturn1){
			var seleccion3 = "";
			if(idprov==ObjetoReturn1.id){
				seleccion3 = "selected='selected'";
			}
			var nuevaFila = "<option value='"+ObjetoReturn1.id+"' "+seleccion3+">" + ObjetoReturn1.nombre_proveedor+"</option>";
			proveedor.append(nuevaFila);
		});
	});
	//Guardamos Formulario
	// Al hacer click en el botón para guardar
	$("form#formulario").submit(function()
	{
			var Producto 		= new Object();
			Producto.Id 		 = $('input#id').val();
			Producto.Codigo      = $('input#codigo').val();
			Producto.Descripcion = $('textarea#descripcion').val();
			Producto.Pcompra     = $('input#pcompra').val();
			Producto.Pventa      = $('input#pventa').val();
			Producto.Categoria   = $('select#categoria').val();
			Producto.SubCategoria= $('select#subcategoria').val();
			Producto.Inventario  = $('select#inventario').val();
			Producto.Stock       = $('input#stock').val();
			Producto.Proveedor   = $('select#proveedor').val();
			Producto.unidadmedida= $('select#unidadmedida').val();
			$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
			var DatosJson = JSON.stringify(Producto);
			$.post(baseurl + 'productos/GuardaProductos',
				{ 
					Productos: DatosJson
				},
				function(data, textStatus) {
					$("#"+data.campo+"").focus();
					$("#mensaje").html(data.error_msg);
				}, 
				"json"		
			);
			return false;
	});
	//
	$("#inventario").change(function(){
			var inventario = $('select#inventario').val();
			if(inventario=="1"){
				$('#stock').prop('disabled', false);
				$('#stock').focus();
			}else{
				$('#stock').prop('disabled', true);
			}
	});
	$("#categoria").change(function(){
			var categoria = $("#categoria").val();
			$('#subcategoria').empty();
			if(categoria==0){
				$('#subcategoria').append("<option value='0'>Elige Sub-Categoria...</option>");
				$('#subcategoria').prop('disabled', true);
			}else{
				$("#subcategoria").append("<option value='0'>Cargando Subcategoria...</option>");
				$.getJSON(baseurl + "productos/subcategorias",{filtro: $("#categoria").val()},function(objetosretorna){
					$("#subcategoria").empty();
					$("#subcategoria").append("<option value='0'>Elige Sub-Categoria...</option>");
					$.each(objetosretorna, function(i,subcategoria){
						$("#subcategoria").append("<option value='"+subcategoria.id+"'>" + subcategoria.descripcion+"</option>");
					});
				});
				$('#subcategoria').prop('disabled', false);
			}
			
		});

		var inventario = $('select#inventario').val();
		if(inventario=="1"){
			$('#stock').prop('disabled', false);
		}else{
			$('#stock').prop('disabled', true);
		}
});