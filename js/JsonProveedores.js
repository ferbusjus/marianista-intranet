$(document).ready(function(){
		$("#Nombre").focus();
		// Al hacer click en el botón para guardar
	$("form#formulario").submit(function()
	{
			
				var Proveedores 		= new Object();
				Proveedores.Id          = $('input#id').val();
				Proveedores.Nombre      = $('input#Nombre').val();
				Proveedores.Direccion   = $('input#direccion').val();
				Proveedores.Telefono    = $('input#telefono').val();
				Proveedores.CP 	        = $('input#cp').val();
				$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
				var DatosJson = JSON.stringify(Proveedores);
				$.post(baseurl + 'proveedores/SaveProveedor',
					{ 
						ProveedoresPost: DatosJson
					},
					function(data, textStatus) {
						$("#"+data.campo+"").focus();
						$("#mensaje").html(data.error_msg);
					}, 
					"json"		
				);
				return false;
			
			
	});

});