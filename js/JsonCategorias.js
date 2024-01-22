$(document).ready(function(){
		$("#Nombre").focus();
		// Al hacer click en el botón para guardar
	$("form#formulario").submit(function()
	{
			
				var Categorias  	   = new Object();
				Categorias.Id          = $('input#id').val();
				Categorias.Descripcion = $('input#descripcion').val();
				Categorias.Estatus     = $('select#estatus').val();
				$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
				var DatosJson = JSON.stringify(Categorias);
				$.post(baseurl + 'categorias/SaveCategoria',
					{ 
						CategoriasPost: DatosJson
					},
					function(data, textStatus) {
						$("#"+data.campo+"").focus();
						$("#mensaje").html(data.error_msg);
					}, 
					"json"		
				);
				return false;
			
			
	});

	//Guarda SUBCATEGORIA
	$("form#formulario2").submit(function()
	{
			
				var SubCategorias  	   = new Object();
				SubCategorias.Id          = $('input#id').val();
				SubCategorias.IdCategoria = $('select#categoria').val();
				SubCategorias.Descripcion = $('input#descripcion').val();
				SubCategorias.Estatus     = $('select#estatus').val();
				$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
				var DatosJson = JSON.stringify(SubCategorias);
				$.post(baseurl + 'categorias/SaveSubcategoria',
					{ 
						SubCategoriasPost: DatosJson
					},
					function(data, textStatus) {
						$("#"+data.campo+"").focus();
						$("#mensaje").html(data.error_msg);
					}, 
					"json"		
				);
				return false;
			
			
	});

	var categoria = $("#categoria");
	categoria.append("<option value='0'>Cargando Categoria...</option>");
	$.getJSON(baseurl + "productos/categorias",function(objetosretorna){
		categoria.empty();
		categoria.append("<option value='0'>---Elige Categoria---</option>");
		$.each(objetosretorna, function(i,ObjetoReturn){
			var seleccion = "";
			if(id_subcat!=0){
				if(id_subcat==ObjetoReturn.id){
					seleccion = "selected='selected'";
				}
			}
			var nuevaFila = "<option value='"+ObjetoReturn.id+"' "+seleccion+">" + ObjetoReturn.descripcion+"</option>";
			categoria.append(nuevaFila);
		});
	});

});