<script>
    var gridTable;
    $(document).ready(function () {
        //==================== Cargamos los registros ==============
        js_listar();
        mayuscula("input#proveedor");
        mayuscula("input#num_comprobante");
        mayuscula("input#descripcion");
        $('#monto').mask('#.##0.00', {reverse: true});
        // Inicializamos los campos bloqueados
        $("#chkRuc").val("0");
        $("#ruc_proveedor").attr("disabled", false);
        $("#proveedor").attr("disabled", false);  
        $("#chkRuc").attr("checked", false);  
        $("#divImagenes").hide();
        //========================================================
        $("#fecha_gasto,#fecha_pago").datepicker({
            dateFormat: 'yy-mm-dd',
            language: 'es',
            showToday: true,
            autoclose: true
        });
        
         $("#proveedor").autocomplete({
            source: "<?= BASE_URL ?>swp_egresos/filtroProveedor/1",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui);
                if (ui.item.ruc != '') {
                    console.log("encontro...");
                    $("#ruc_proveedor").val(ui.item.ruc );
                    $("#proveedor").val(ui.item.razon );
                   // $("#proveedor").attr("ruc", true);
                   // $("#txtAlumnproveedoroSearch").css("font-weight","bold");
                  //  $("#proveedor").css("color","red");
                    $("#btnBuscar").focus();
                } else {
                    console.log("no existe...");
                    $("#ruc_proveedor").val('');
                    $("#proveedor").val('');
                    //$("#proveedor").attr("disabled", false);
                }
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });
        
         $("#ruc_proveedor").autocomplete({
            source: "<?= BASE_URL ?>swp_egresos/filtroProveedor/2",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.ruc != '') {
                    $("#ruc_proveedor").val(ui.item.ruc );
                    $("#proveedor").val(ui.item.razon );
                    $("#btnBuscar").focus();
                } else {
                    $("#ruc_proveedor").val('');
                    $("#proveedor").val('');
                }
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });
        
        //========================================================
        $("#idrazon,#idcomp,#idresp").change(function ()
        {
             //var grupo = $(this).val();
            $('#viewEgresos').DataTable().destroy();
            js_listar();
        });
        
        $("#btnRefresh").click(function ()
        {
            $("#idrazon").val("");
            $("#idcomp").val("");
            $("#idresp").val("");
            $('#viewEgresos').DataTable().destroy();
            js_listar();
        })
        
        $('#chkMostrarEliminados').click(function (){
           if($(this).is(':checked') ) {
                $(this).val("1");
            } else {
                 $(this).val("0");
            }        
            js_listar();
        });
        
        $('#chkRuc').click(function (){
            if($(this).is(':checked') ) {
                $(this).val("1");
                $("#ruc_proveedor").attr("disabled", true);
                $("#proveedor").attr("disabled", true);
            } else {
                $(this).val("0");
                $("#ruc_proveedor").attr("disabled", false);
                $("#proveedor").attr("disabled", false);                
            }
        });
        
        $("#form2").submit(function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
         /*   var arrdata = $(this).serialize();            
            console.log(arrdata);
        $.ajax({
            url: "<?= BASE_URL ?>swp_egresos/cargar",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                if (data['flg'] == 0) {
                    //$('#modal_egresos').modal('hide');
                    //gridTable.ajax.reload(null, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //alert('Error get data from ajax');
                alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });*/
    
            // vTipoComp = 6 (Pago Efectivo)        
            if ($("#id_comprobante") !=6 && $.trim($("#num_comprobante")) =="") {
                alert("Ingrese el Número de Comprobante");
                $("#txt_comprobante").focus();
                return false;
            }
            
            if(!$('#chkRuc').is(':checked') ) {
                if($.trim($("#ruc_proveedor").val()).length<11) {
                    alert("Debe de ingresar un RUC correcto.");
                    return false;                
                }

                if($.trim($("#proveedor").val()).length<10) {
                    alert("Debe de ingresar una Razón Social correcta.");
                    return false;                
                }    
            }        
            
            if($("#file1").val() =="" && $("#file2").val() =="" && $("#id").val()=="") {
                alert("Debe de adjuntar como mínimo un archivo.");
                return false;
            }
    
            //var arrdata = $(this).serialize();       
            var arrdata  = new FormData($('#form2')[0]);
           // var file1 = $("#file1").prop("files")[0];
          //  var file2 = $("#file2").prop("files")[1];
          //  arrdata.append("archivo1", file1);
          //  arrdata.append("archivo2", file2);
            arrdata.append("accion", 0);
            //console.log(arrdata);
            //console.log(file1);
        $.ajax({
            url: "<?= BASE_URL ?>swp_egresos/procesarGasto",
            type: "POST",
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                if (data['flg'] == 0) {
                    $('#modal_egresos').modal('hide');
                    gridTable.ajax.reload(null, false);
                }
            }
        });          
        e.preventDefault();
        });

    });

    function js_descargar(archivo){
        var file = "http://sistemas-dev.com/intranet/gastos_files/"+archivo;
        window.open(file, "_blank");
    }
    
    function js_addEgreso(tipo, titulo) {
        $('#form2')[0].reset(); // reset form on modals
        //mayuscula("input#txtdescripcion");
        //mayuscula("input#txtcomprobante");

        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>swp_egresos/getdatos/",
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                //$("#resultado").html("Procesando, espere por favor...");
                spinnerShow();
            },
            success: function (data)
            {
                console.log(data);
                if(tipo==1) {
                    spinnerHide();
                }
                if (data['razon'].length > 0) {
                    $("#id_razon").empty();
                    $("#id_razon").append("<option value=''>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['razon'], function (i, item) {
                        $("#id_razon").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }

                if (data['responsable'].length > 0) {
                    $("#id_responsable").empty();
                    $("#id_responsable").append("<option value=''>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['responsable'], function (i, item) {
                        $("#id_responsable").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }

                if (data['comprobante'].length > 0) {
                    $("#id_comprobante").empty();
                    $("#id_comprobante").append("<option value=''>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['comprobante'], function (i, item) {
                        $("#id_comprobante").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }
                
                if (data['inputacion'].length > 0) {
                    $("#id_inputacion").empty();
                    $("#id_inputacion").append("<option value=''>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['inputacion'], function (i, item) {
                        $("#id_inputacion").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }
                
                if (data['gasto'].length > 0) {
                    $("#id_tipo_gasto").empty();
                    $("#id_tipo_gasto").append("<option value=''>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['gasto'], function (i, item) {
                        $("#id_tipo_gasto").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }
                
                if (data['caja'].length > 0) {
                    $("#id_caja").empty();
                    $("#id_caja").append("<option value=''>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['caja'], function (i, item) {
                        $("#id_caja").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }
                
                $('#modal_egresos').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(titulo); // Set title to Bootstrap modal title       
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_grabar_egreso()
    {
        var vRazon = $("#cb_razon_social").val();
        var vResponsable = $("#cb_responsable").val();
        var vTipoGasto = $("#cb_tipo_gasto").val();
        var vImputacion = $("#cb_tipo_inputacion").val();
        var vRuc = $("#txtruc").val();
        var vProveedor = $("#txtproveedor").val();
        var vfecha = $("#txtfecha").val();
         var vTipoComp = $("#cb_tipo_comprobante").val();
         var vComprobante = $("#txt_comprobante").val();
         var vMonto = $("#txtmonto").val();
         var vDescripcion = $("#txtdescripcion").val();
         
        if (vRazon == '0') {
            alert("Seleccione Razón Social");
            return false;
        }

        if (vResponsable === '0') {
            alert("Seleccione el Responsable");
            return false;
        }
        
        if (vTipoGasto === '0') {
            alert("Seleccione el Tipo de Gasto");
            return false;
        }
        
        if (vImputacion === '0') {
            alert("Seleccione el tipo de Imputación");
            return false;
        }
        
        if (vImputacion === '0') {
            alert("Seleccione el tipo de Imputación");
            return false;
        }        
        if ($.trim(vRuc) == '') {
            alert("Ingrese el RUC del Proveedor");
            $("#txtruc").focus();
            return false;
        }

        if ($.trim(vProveedor) == '') {
            alert("Ingrese el Proveedor");
            $("#txtproveedor").focus();
            return false;
        }
        
        if (vTipoComp === '0') {
            alert("Seleccione el tipo de Comprobante");
            return false;
        }
      
        if ($.trim(vComprobante) == '') {
            alert("Ingrese el Número de Comprobante");
            $("#txt_comprobante").focus();
            return false;
        }
        
        if ($.trim(vDescripcion) == '') {
            alert("Ingrese la descripción del Gasto");
            $("#txtdescripcion").focus();
            return false;
        }

        /*if ($.trim(vcomp) == '' && vcbcomp != 5) {
            alert("Ingrese el Numero de Comprobante");
            $("#txtcomprobante").focus();
            return false;
        }*/

        if ($.trim(vfecha) == '') {
            alert("Seleccione la fecha");
            return false;
        }

        if ($.trim(vMonto) == '') {
            alert("Ingrese el Monto");
            return false;
        }
        
        if( $("#file1").val() =='' && $("#file2").val() =='') {
            alert("Debe de subir obligatoriamente un Archivo.");
            return false;
        }
         
        var arrdata = {
            vrazon: vRazon,
            vresponsable: vResponsable,
            vtipogasto: vTipoGasto,
            vimputacion: vImputacion,
            vruc: vRuc,
            vproveedor: vProveedor,
            vfecha: vfecha,
            vtipocomp: vTipoComp,
            vcomprobante: vComprobante,
            vmonto: vMonto,
            vdescripcion: vDescripcion            
        };

        $.ajax({
            url: "<?= BASE_URL ?>swp_egresos/grabarEgreso/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                if (data['flg'] == 0) {
                    $("#form2").submit();
                    //$('#modal_egresos').modal('hide');
                    //gridTable.ajax.reload(null, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });


    }

    function js_listar() {
        var idrazon = $("#idrazon").val();
        var idcomp = $("#idcomp").val();
        var idresp = $("#idresp").val();
        var flagBorrado = $('#chkMostrarEliminados').val();
        gridTable = $('#viewEgresos').DataTable({
            "ordering": false,
            //"searching": false,
            // "bFilter": false,
            "bInfo": true,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            // "iDisplayLength": 20,
            "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "Todos"]],
            "bLengthChange": false,
            "ajax": {
                "url": "<?= BASE_URL ?>swp_egresos/lista/",
                "type": "POST",
                data: {idrazon: idrazon, idcomp: idcomp, idresp :idresp, flagBorrado :flagBorrado}
            },
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla.",
                "info": "Del _START_ al _END_ de _TOTAL_ ",
                "infoEmpty": "Mostrando 0 registros de un total de 0.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "infoPostFix": "(actualizados)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "<img src='http://sistemas-dev.com/intranet/img/gif-load.gif' >",
                "search": "Buscar:",
                "searchPlaceholder": "Descripcion a buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {"className": "dt-center", "targets": [0,5,7,8,9,10]}
            ],
            "columns": [
                {"data": "id"},
                {"data": "razon"},
                {"data": "responsable"},
                {"data": "comprobante"},
                {"data": "descripcion"},
               // {"data": "num_comprobante"},
                {"data": "monto"},
                {"data": "archivo"},
                {"data": "usureg"},
                {"data": "fecreg"},
				{"data": "estado"},
                {"data": "conf"}
            ],
            "rowCallback": function( Row, Data) {
                    //console.log(JSON.stringify(Row) +"========="+JSON.stringify(Data));
                   if ( Data["flgactivo"] == "0" )
                   {
                           $('td', Row).css({'background-color':'Red', 'color':'white'});
                   }
            }			
        });
    }

    function js_editar(vId){
        var arrdata = {
            vId: vId
        };    
        // Cargamos los datos para los combos
         js_addEgreso(0, "EDITAR GASTO : "+vId );
         setTimeout(function(){
                $.ajax({
                   url: "<?= BASE_URL ?>swp_egresos/getGasto/",
                   type: "POST",
                   dataType: "json",
                   data: arrdata,
                   beforeSend: function () {
                       //spinnerShow();
                   },
                   success: function (data)
                   {                       
                       var datoEgreso = data['gasto'][0];
                       if (datoEgreso) {
                           $("#id").val(datoEgreso.id);
                           $("#id_razon").val(datoEgreso.id_razon);
                           $("#id_responsable").val(datoEgreso.id_responsable);
                           $("#id_tipo_gasto").val(datoEgreso.id_tipo_gasto);
                           $("#id_caja").val(datoEgreso.id_caja);
                           $("#id_inputacion").val(datoEgreso.id_inputacion);
                           if(datoEgreso.flg_ruc==1){
                               $("#ruc_proveedor").attr("disabled", true);
                               $("#proveedor").attr("disabled", true);                        
                               $("#chkRuc").attr("checked", true);
                               $("#chkRuc").val("1");
                           } else {
                               $("#ruc_proveedor").attr("disabled", false);
                               $("#proveedor").attr("disabled", false);                        
                               $("#chkRuc").attr("checked", false);
                               $("#chkRuc").val("0");
                           }
                           $("#flg_ruc").val(datoEgreso.flg_ruc);
                          $("#ruc_proveedor").val(datoEgreso.ruc_proveedor);
                           $("#proveedor").val(datoEgreso.proveedor);
                           $("#fecha_gasto").val(datoEgreso.fecha_gasto);
						   $("#fecha_pago").val(datoEgreso.fecha_pago);
                           $("#id_comprobante").val(datoEgreso.id_comprobante);
                           $("#num_comprobante").val(datoEgreso.num_comprobante);
                           $("#monto").val(datoEgreso.monto);
                           $("#descripcion").val(datoEgreso.descripcion);
                           $("#divImagenes").show();
                           $("#imagen1").html(datoEgreso.archivo_1);
                           $("#imagen2").html(datoEgreso.archivo_2);
                       }
                       spinnerHide();
                   }
               });             
        }, 1000);

    }

    function js_eliminar(vId) {
        var msg = window.confirm("Esta seguro de Eliminar el registro ?");
        if (msg) {
            var arrdata = {
                vId: vId
            };
            $.ajax({
                url: "<?= BASE_URL ?>swp_egresos/eliminaEgreso/",
                type: "POST",
                dataType: "json",
                data: arrdata,
                success: function (data)
                {
                    alert(data['msg']);
                    if (data['flg'] == 0) {
                        $('#modal_egresos').modal('hide');
                        gridTable.ajax.reload(null, false);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });


        }
    }

    function validaNumeros(evt, input) {
        // Backspace = 8, Enter = 13, ‘0' = 48, ‘9' = 57, ‘.’ = 46, ‘-’ = 43
        var key = window.Event ? evt.which : evt.keyCode;
        var chark = String.fromCharCode(key);
        var tempValue = input.value + chark;
        if (key >= 48 && key <= 57) {
            if (filter(tempValue) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            if (key == 8 || key == 13 || key == 0) {
                return true;
            } else if (key == 46) {
                if (filter(tempValue) === false) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    function filter(vcad) {
        var preg = /^([0-9]+\.?[0-9]{0,2})$/;
        if (preg.test(vcad) === true) {
            return true;
        } else {
            return false;
        }
    }



    function spinnerShow() {
        $("#modal").css("display", "block");
        $("#fade").css("display", "block");
    }

    function spinnerHide() {
        $("#modal").css("display", "none");
        $("#fade").css("display", "none");
    }

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }
    function minuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toLowerCase());
        });
    }

</script>