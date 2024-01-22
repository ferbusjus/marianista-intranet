$(document).ready(function(){
	//Guardamos Formulario
	$("#Nombre").focus();
	// Al hacer click en el botón para guardar
	$("form#formulario").submit(function()
	{
			var Mail   			 = $('input#validamail').val();
			var rfc              = $('input#validarfc').val();
			if(Mail=="1"){
				$("#mensaje").html("<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Correo es Incorrecto</div>");
				$("#email").focus();
				return false;
			}else if(rfc=="1"){
				$("#mensaje").html("<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El RFC es Incorrecto</div>");
				$("#rfc").focus();
				return false;
			}else{
				var Clientes 		 = new Object();
				Clientes.Id          = $('input#id').val();
				Clientes.Nombre      = $('input#Nombre').val();
				Clientes.Apellidos   = $('input#apellidos').val();
				Clientes.CP 	     = $('input#cp').val();
				Clientes.Pais        = $('input#pais').val();
				Clientes.Estado      = $('input#estado').val();
				Clientes.Municipio   = $('input#municipio').val();
				Clientes.Ciudad      = $('input#ciudad').val();
				Clientes.Colonia     = $('select#colonia').val();
				Clientes.Calle       = $('input#Calle').val();
				Clientes.Email       = $('input#email').val();
				Clientes.RFC         = $('input#rfc').val();
				Clientes.Telefono    = $('input#telefono').val();
				$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
				var DatosJson = JSON.stringify(Clientes);
				$.post(baseurl + 'clientes/GuardaClientes',
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
			}
			
	});
	if(codigopostal!=0){
		$.getJSON(baseurl + "clientes/BuscaCP",{cp:codigopostal},function(objetosretorna){
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
						var selecion = "";
						if(col==colonias[i]){
							selecion = "selected='selected'";
						}
						$("#colonia").append("<option value='"+colonias[i]+"' "+selecion+">" + colonias[i] +"</option>");
					}
				});
			});
	}
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