var currentLocation = window.location;
$(document).ready(function(){

	$("#GeneraReporte").click(function()
	{
		var finicial =  $('input#finicial').val();
		var ffinal   =  $('input#ffinal').val();
		if(finicial==""){
			alert("Elige la Fecha Inicial");
			$("#finicial").focus();
			return false;
		}else if(ffinal==""){

			alert("Elige la Fecha Final");
			$("#ffinal").focus();
			return false;
		}else if((Date.parse(finicial)) > (Date.parse(ffinal))){
			alert("La Fecha Inicial no puede ser mayor que la Fecha Final");
			$("#finicial").focus();
			return false;
		}else{
			var Reportes = new Object();
			Reportes.Finicial      = $('input#finicial').val();
			Reportes.FFinal 	   = $('input#ffinal').val();
			Reportes.Documento	   = $('select#documento').val(); 
			$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Generando Reporte...</center></div></div>");	
			var DatosJson = JSON.stringify(Reportes);
			$.post(currentLocation + '/GeneraReporte',
			{ 
				MiReporte: DatosJson
			},
				function(data, textStatus) {
					$("#reportes tbody").html("");
					var Subtotal = 0;
					var iva      = 0;
					var total    = 0;
					$.each(data, function(i, item) {
						Subtotal = parseFloat(Subtotal) + parseFloat(item.BRUTO);
						iva      = parseFloat(iva) + parseFloat(item.TOTAL_IMPUESTO);
						total    = parseFloat(total) + parseFloat(item.TOTAL);

						var nuevaFila =
						"<tr>"
						+"<td>"+item.ID+"</td>"
						+"<td>"+item.TIPO+"</td>"
						+"<td>"+item.FECHA+"</td>"
						+"<td>$ "+item.TOTAL_IMPUESTO+"</td>"
						+"<td>$ "+item.BRUTO+"</td>"
						+"<td>$ "+item.TOTAL+"</td>" 
						+"</tr>";
						$(nuevaFila).appendTo("#reportes tbody");

					});
					$("#lblsubtotal").text("$ " + Subtotal.toFixed(2));
					$("#lblimpuesto").text("$ " + iva.toFixed(2));
					$("#lbltotal").text("$ " + total.toFixed(2));
					if(total=="0"){
						var nuevaFila =
						"<tr>"
						+"<td colspan=6><center>Rango de Fecha: "+finicial+" al "+ffinal+", Tipo de Documento: "+$('select#documento').val()+", No Existen Registros</center></td>"
						+"</tr>";
						$(nuevaFila).appendTo("#reportes tbody");
					}
					$("#mensaje").text("");
				}, 
				"json"		
			);

				return false;
		}
		
	});

});