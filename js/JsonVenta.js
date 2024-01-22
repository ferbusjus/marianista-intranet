var currentLocation = window.location;
$(function(){
	//Buscamos Cliente
  $("#BuscaCliente").autocomplete({
            source: currentLocation + "/BuscarCliente",
             select: function(event, ui) {  
				$('#lblNombrecliente').text(ui.item.NOMBRE +' '+ ui.item.APELLIDOS);
				$('#lblidcliente').text(ui.item.CODIGO_CLIENTE);
				$('#Cliente').val(ui.item.CODIGO_CLIENTE);
				$('#BuscaProducto').prop("disabled", false);
				$('#AgregaProducto').prop("disabled", false);
				document.getElementById("BuscaProducto").focus();
				$('#BuscaCliente').prop("disabled", true);
           }  
    });
  //Buscamos Producto
  $("#BuscaProducto").autocomplete({
            source: currentLocation + "/buscarproductos",
             select: function(event, ui) {  
				$('#descripcion').val(ui.item.descripcion);
				$('#codigo').val(ui.item.codigo);
				$('#precioventa').val(ui.item.precio_venta);
				$("#existencia").val(ui.item.cantidad);
				$("#Proveedor").val(ui.item.nombre_proveedor);
				$("#idProveedor").val(ui.item.id);
				$("#costo").val(ui.item.precio_compra);
           }  
    });
});
$(document).ready(function(){
	$("#BuscaCliente").focus();
	$("#AgregaProducto").click(function()
	{		var code = $('input#codigo').val();
			var idses= $('input#idsessionventa').val();
			var canti= $('input#existencia').val();
			var cant2= $('input#cantidad').val();
			canti = parseInt(canti);
			cant2 = parseInt(cant2);
			if(code==""){
				$('#BuscaProducto').val('');
				LimpiarTexto();
				alert("Teclea el Producto a Vender");
				return false;
				
			}else if(canti==0){
				$('#BuscaProducto').val('');
				LimpiarTexto();
				alert("No hay Existencias del Producto: " + code);
				return false;
			}else if(cant2>canti){
				$('#BuscaProducto').val('');
				LimpiarTexto();
				alert("Nadamas Tenemos Disponible: " + canti + ", Productos y Usted Solicito: " + cant2 + ", Productos de la Clave: " + code);
				return false;
			}else{
				var Producto = new Object();
				Producto.Codigo      = $('input#codigo').val();
				Producto.Descripcion = $('input#descripcion').val();
				Producto.Cantidad    = $('input#cantidad').val();
				Producto.Pcompra     = $('input#precioventa').val();
				Producto.IdSession   = $('input#idsessionventa').val();
				Producto.Proveedor   = $('input#Proveedor').val();
				Producto.Costo	     = $('input#costo').val();
				Producto.IdProveedor = $('input#idProveedor').val();
				var DatosJson = JSON.stringify(Producto);
				$.post(currentLocation + '/addcarrito',
				{ 
					MiCarrito: DatosJson
				},
				function(data, textStatus) {

					$("#carrito tbody").html("");
					var Subtotal = 0;
					var iva      = 0;
					var total    = 0;
					//Recibimos parametro y imprimimos
					$.each(data, function(i, item) {
						var cantsincero =  item.cantidad;
						cantsincero     = parseInt(cantsincero);
						if(cantsincero!=0){
							var Operacion= parseFloat(item.precio) * parseFloat(item.cantidad);
							Subtotal = parseFloat(Subtotal) + parseFloat(Operacion);
							iva      = parseFloat(Subtotal) * parseFloat(0.16);
							total    = parseFloat(iva) + parseFloat(Subtotal);
							var nuevaFila =
							"<tr>"
							+"<td>"+item.txtCodigo+"</td>"
							+"<td>"+item.descripcion+"</td>"
							+"<td>"+item.proveedor+"</td>"
							+"<td>$ "+item.precio+"</td>"
							+"<td>"+item.cantidad+"</td>"
							+"<td>$ "+Operacion+"</td>"
							+"<td><div align='center'>"
							+'<img onclick="EliminarItem('
							+"'"+item.txtCodigo+"',"
							+"'"+item.descripcion+"',"
							+"'-1',"
							+"'"+item.precio+"',"
							+"'"+idses+"',"
							+"'"+item.proveedor+"',"
							+"'"+item.costo+"',"
							+"'"+item.IdProveedor+"'"
							+ ')"' 
							+" src='img/delete.png' width='20'/></div></td>"
							+"</tr>";
							$(nuevaFila).appendTo("#carrito tbody");
							$("#lblsubtotal").text("$ " + Subtotal);
							$("#lbliva").text("$ " + iva);
							$("#lbltotal").text("$ " + total);
							$("#txtsubtotal").val(Subtotal);
							$("#txtIva").val(iva);
							$("#txtTotal").val(total);
						}
									
					});
						LimpiarTexto();
					}, 
					"json"		
				);
				return false;
			}
	});
	//Guardamos Venta
	$("#SaveOrder").click(function()
	{		var code = $('input#txtTotal').val();
			if(code=="0"){
				$('#BuscaProducto').val('');
				$("#BuscaProducto").focus();
				alert("No Hay Partidas que Cobrar");
				return false;
				
			}else{
				var Producto = new Object();
				Producto.IdSession   = $('input#idsessionventa').val();
				Producto.Subtotal    = $('input#txtsubtotal').val();
				Producto.IVA   		 = $('input#txtIva').val();
				Producto.Total 		 = $('input#txtTotal').val();
				Producto.Cliente 	 = $('input#Cliente').val();
				var DatosJson 		 = JSON.stringify(Producto);
				$("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='"+ baseurl +"/img/gif-load.gif'> Registrando Venta...</center></div></div>");
				$.post(currentLocation + '/saveOrder',
				{ 
					MiCarrito: DatosJson
				},
				function(data, textStatus) {
						$("#carrito tbody").html("");
						if(data.TipoMsg=="Error"){
							$("#mensaje").html("<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>"+data.Msg+"</div>");
						}else{
							$("#mensaje").html("<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>"+data.Msg+"</div>");
							//Abrimos Impresion  NumOrden
							var Url = baseurl+'ventas/ImprimeVenta/'+data.NumOrden;
							window.open(Url,'Pruebas','fullscreen=yes, scrollbars=auto');
						}
						var nuevaFila =
						"<tr>"
						+"<td colspan=7><center>No Hay Productos Agregados</center></td>"
						+"</tr>";
						$(nuevaFila).appendTo("#carrito tbody");
					}, 
					"json"		
				);
				$("#lblsubtotal").text("$ 0");
				$("#lbliva").text("$ 0");
				$("#lbltotal").text("$ 0");
				$('#BuscaCliente').prop("disabled", false);
				$('#BuscaCliente').val('')
				$('#lblNombrecliente').text('');
				$('#lblidcliente').text('');
				$("#BuscaCliente").focus();
				$('#BuscaProducto').prop("disabled",true);
				$('#AgregaProducto').prop("disabled", true);
				LimpiaOrder();
				AsignaSession();
				return false;
				
				
			}
			return false;
	});

});
function LimpiarTexto(){
		document.getElementById("BuscaProducto").value="";
		document.getElementById("codigo").value="";
		document.getElementById("cantidad").value="1";
		document.getElementById("precioventa").value="";
		$("#BuscaProducto").focus();
}
function LimpiaOrder(){
	document.getElementById("txtTotal").value="0";
}
function EliminarItem(codigo,descripcion,cantidad,pcompra,idsession,proveedor,costo,idproveedor){
		var Producto 		 = new Object();
		Producto.Codigo      = codigo;
		Producto.Descripcion = descripcion;
		Producto.Cantidad    = cantidad;
		Producto.Pcompra     = pcompra;
		Producto.IdSession   = idsession;
		Producto.Proveedor   = proveedor;
		Producto.Costo	     = costo;
		Producto.IdProveedor = idproveedor;
				
		var DatosJson = JSON.stringify(Producto);
		$.post(currentLocation + '/addcarrito',
		{ 
			MiCarrito: DatosJson
		},
		function(data, textStatus) {

			$("#carrito tbody").html("");
			var Subtotal = 0;
			var iva      = 0;
			var total    = 0;
			var contador = 0;
			//Recibimos parametro y imprimimos
			$.each(data, function(i, item) {
				var cantsincero =  item.cantidad;
				cantsincero     = parseInt(cantsincero);
				if(cantsincero!=0){
					contador   = contador + 1;
					var Operacion= parseFloat(item.precio) * parseFloat(item.cantidad);
					Subtotal = parseFloat(Subtotal) + parseFloat(Operacion);
					iva      = parseFloat(Subtotal) * parseFloat(0.16);
					total    = parseFloat(iva) + parseFloat(Subtotal);
					var nuevaFila =
					"<tr>"
					+"<td>"+item.txtCodigo+"</td>"
					+"<td>"+item.descripcion+"</td>"
					+"<td>"+item.proveedor+"</td>"
					+"<td>$ "+item.precio+"</td>"
					+"<td>"+item.cantidad+"</td>"
					+"<td>$ "+Operacion+"</td>"
					+"<td><div align='center'>"
					+'<img onclick="EliminarItem('
					+"'"+item.txtCodigo+"',"
					+"'"+item.descripcion+"',"
					+"'-1',"
					+"'"+item.precio+"',"
					+"'"+idsession+"',"
					+"'"+item.proveedor+"',"
					+"'"+item.costo+"',"
					+"'"+item.IdProveedor+"'"
					+ ')"' 
					+" src='img/delete.png' width='20'/></div></td>"
					+"</tr>";
					$(nuevaFila).appendTo("#carrito tbody");
					$("#lblsubtotal").text("$ " + Subtotal);
					$("#lbliva").text("$ " + iva);
					$("#lbltotal").text("$ " + total);
					$("#txtsubtotal").val(Subtotal);
					$("#txtIva").val(iva);
					$("#txtTotal").val(total);
				}
							
			});
				if(contador==0){
					$("#carrito tbody").html("");
					var nuevaFila =
					"<tr>"
					+"<td colspan=7><center>No Hay Productos Agregados</center></td>"
					+"</tr>";
					$(nuevaFila).appendTo("#carrito tbody");
					$("#lblsubtotal").text("$ 0");
					$("#lbliva").text("$ 0");
					$("#lbltotal").text("$ 0");
					LimpiaOrder();
				}
				LimpiarTexto();
			}, 
			"json"		
		);
		return false;		
	}