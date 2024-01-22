$(document).ready(function(){
	$("#Direccion").focus();
	$("form#formulario").submit(function()
	{
		var DirClientes 		   = new Object();
		DirClientes.Direccion      = $('input#Direccion').val();
		DirClientes.nExterior	   = $('input#nExterior').val();
		DirClientes.nInterior	   = $('input#nInterior').val();
		DirClientes.CP 	     	   = $('input#cp').val();
		DirClientes.Estado     	   = $('input#estado').val();
		DirClientes.Municipio      = $('input#municipio').val();
		DirClientes.Ciudad         = $('input#ciudad').val();
		DirClientes.Colonia    	   = $('select#colonia').val();
		DirClientes.Telefono       = $('input#telefono').val();
		DirClientes.Referencias    = $('input#Referencias').val();
		DirClientes.idcliente	   = $('input#idcliente').val();
		$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
		var DatosJson = JSON.stringify(DirClientes);
		$.post(baseurl + 'clientes/GuardaDireccion',
			{ 
				ClientesPost: DatosJson
			},
			function(data, textStatus) {
				$("#"+data.campo+"").focus();
				$("#mensaje").html(data.error_msg);
			}, 
			"json"		
		);
		return false;
	});
	$("#cp").blur(function(){
			var cp  = $("#cp").val();
			$.getJSON(baseurl + "clientes/BuscaCP",{cp:cp},function(objetosretorna){
				$.each(objetosretorna, function(i,codigoPostal){
					$("#colonia").empty();
					$("#colonia").append("<option value='0'>Elige Colonia...</option>");
					$("#pais").val("Mexico");
					$("#estado").val(codigoPostal.Estado);
					$("#municipio").val(codigoPostal.Municipio);
					$("#ciudad").val(codigoPostal.ciudad);
					var colonias = (codigoPostal.Colonia).split(";");
					var cuantos  = colonias.length;
					for (i=0;i<cuantos;i++) { 
						$("#colonia").append("<option value='"+colonias[i]+"'>" + colonias[i] +"</option>");
					}
				});
			});
	});

});