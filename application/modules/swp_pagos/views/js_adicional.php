<script >
    var gridTable;
    var arrConceptos = [];
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });
        $("#divInterno").hide();
        mayuscula("input#txtAlumnoSearch");
        $("#txtAlumnoSearch").keypress(function (key) {

            if ((key.charCode < 97 || key.charCode > 122)//letras mayusculas
                    && (key.charCode < 65 || key.charCode > 90) //letras minusculas
                    && (key.charCode != 45) //retroceso
                    && (key.charCode != 241) //ñ
                    && (key.charCode != 209) //Ñ
                    && (key.charCode != 32) //espacio
                    && (key.charCode != 225) //á
                    && (key.charCode != 233) //é
                    && (key.charCode != 237) //í
                    && (key.charCode != 243) //ó
                    && (key.charCode != 250) //ú
                    && (key.charCode != 193) //Á
                    && (key.charCode != 201) //É
                    && (key.charCode != 205) //Í
                    && (key.charCode != 211) //Ó
                    && (key.charCode != 218) //Ú
                    && (key.charCode != 0) //Ú
                    )
                return false;
        });

        $("#txtAlumnoSearch").autocomplete({
            source: "<?= BASE_URL ?>swp_pagos/filtroAlumnoAll",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.alucod != '') {
                    var vnemosdes = ui.item.nemodes;
                    vnemosdes = vnemosdes.split("-");
                    $('#htxtalumno').val(ui.item.alucod);
                    $('#htxtsalon').val(ui.item.nemo);
                    $('#txtpaterno').val(ui.item.apepat);
                    $('#txtmaterno').val(ui.item.apemat);
                    $('#txtnombres').val(ui.item.nombres);
                    $("#txtAlumnoSearch").val(ui.item.apepat + " " + ui.item.apemat + ", " + ui.item.nombres);
                    $("#divInterno").hide();
                    //$("#txtAlumnoSearch").attr("disabled", true);
                } else {
                    $('#htxtalumno').val('');
                    $('#htxtsalon').val('');
                    $("#txtAlumnoSearch").val('');
                    $('#txtpaterno').val('');
                    $('#txtmaterno').val('');
                    $('#txtnombres').val('');
                    $("#divInterno").hide();
                    //$("#txtAlumnoSearch").attr("disabled", false);
                }
                //  return false;
            },
            open: function () {
                console.log("ABIERTO");
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                console.log("CERRADO");
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $("#rbdOptions1,#rbdOptions2").click(function () {
            console.log("entro ....");
            var label = '';
            $("#divlblComprobante").html("");
            $("#txtnumrecibo").val("");
            $("#rbdTipo").val("");
            var vnemo = $("#htxtsalon").val();
            var vRazon = $("#cbrazon").val();
            if (vRazon === '0') {
                alert("DEBE DE SELECCIONAR A QUE RAZÓN SOCIAL SE REGISTRARA EL PAGO");
                $("#rbdOptions1").attr("checked", false);
                $("#rbdOptions2").attr("checked", false);
                return false;
            }
            if ($(this).val() === '01') {
                $("#rbdTipo").val("01");
                label = 'RECIBO :';
            }
            if ($(this).val() === '02') {
                $("#rbdTipo").val("02");
                label = 'BOLETA :';
            }
            $("#divlblComprobante").html(label);
            // Llamando al ajax que obtiene el numero de document
            $.ajax({
                url: "<?= BASE_URL ?>swp_pagos/getDocumentoAdicional/",
                type: "POST",
                dataType: "json",
                data: {vRazon: vRazon, vTipo: $(this).val()},
                beforeSend: function () {
                    $('.loading').show();
                },
                success: function (data) {
                    $("#txtnumrecibo").val(data['gencod']);
                },
                complete: function () {
                    $('.loading').hide();
                }});

        });

        $("#btnSearch").click(function () {
            $("#divInterno").show();
            $('#htxtalumno').val('');
            $('#htxtsalon').val('');
            $("#txtAlumnoSearch").val('');
            $("#txtAlumnoSearch").focus();
        });

        /*  <tr>
         <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL</td>
         <td style="text-align: right; font-weight: bold;">S/140.00</td>
         </tr>*/
        $("#btnadd").click(function ()
        {
            var tipo = $('#cbtipo').val();
            var concepto = $('#cbconcepto').val();
            var monto = $('#txtmontoConcepto').val();
            if (tipo === "0") {
                alert("SELECCIONE EL TIPO");
                return false;
            }
            if (concepto === "0") {
                alert("SELECCIONE EL TIPO");
                return false;
            }
            if ($.trim(monto) === "") {
                alert("INGRESE EL MONTO");
                return false;
            }
            // Verificamos si existe ya el ID
            for (var x = 0; x < arrConceptos.length; x++) {
                if (arrConceptos[x]["idconcepto"] === concepto) {
                    alert("YA EXISTE EL CONCEPTO EN LA LISTA.");
                    return false;
                }
            }
            var numrow = arrConceptos.length + 1;
            var arrfila = {
                item: numrow,
                idtipo: tipo,
                tipo: $('select[name="cbtipo"] option:selected').text(),
                idconcepto: concepto,
                concepto: $('select[name="cbconcepto"] option:selected').text(),
                monto: $.trim(monto)
            };
            arrConceptos.push(arrfila);
            var nuevaFila = "";
            var data = JSON.parse(JSON.stringify(arrConceptos));
            $("#tblConceptos tbody").html("");
            var y = 1;
            for (var i in data) {
                //console.log(data[i].tipo);
                nuevaFila += "<tr id='fila_" + i + "'>";
                nuevaFila += "<th scope='row' style='text-align: center'>" + y + "</th>";
                nuevaFila += " <td style='text-align: center'>" + data[i].tipo + "</td>";
                nuevaFila += "  <td>" + data[i].concepto + "</td>";
                nuevaFila += "  <td  style='text-align: right'>S/" + data[i].monto + ".00</td>";
                nuevaFila += "  <td  style='text-align: center'><i onclick='delRowTable(" + i + ")' style='cursor:pointer;' title='ELIMINAR CONCEPTO' class='glyphicon glyphicon-trash' /></i></td>";
                nuevaFila += " </tr>";
                $("#tblConceptos tbody").html(nuevaFila);
                y++;
            }
            $('#txtmontoConcepto').val("");
        });

        //========================================================
        /*$("#modal").ajaxStart(function () {
         $(this).show();
         $("#fade").show();
         });
         
         $("#modal").ajaxComplete(function () {
         $(this).hide();
         $("#fade").hide();
         });*/
        //==================== Cargamos los registros ==============
        js_listar();
        //========================================================
        $("#txtfecha").datepicker({
            dateFormat: 'dd/mm/yy',
            language: 'es',
            showToday: true,
            autoclose: true
        });
        //========================================================
        js_cargaConcepto();
    });

    function delRowTable(index) {
        arrConceptos.splice(index, 1);
        $("#fila_" + index).remove();

        var nuevaFila = "";
        var data = JSON.parse(JSON.stringify(arrConceptos));
        $("#tblConceptos tbody").html("");
        var y = 1;
        for (var i in data) {
            //console.log(data[i].tipo);
            nuevaFila += "<tr id='fila_" + i + "'>";
            nuevaFila += "<th scope='row' style='text-align: center'>" + y + "</th>";
            nuevaFila += " <td style='text-align: center'>" + data[i].tipo + "</td>";
            nuevaFila += "  <td>" + data[i].concepto + "</td>";
            nuevaFila += "  <td  style='text-align: right'>S/" + data[i].monto + "</td>";
            nuevaFila += "  <td  style='text-align: center'><i onclick='delRowTable(" + i + ")' style='cursor:pointer;' title='ELIMINAR CONCEPTO' class='glyphicon glyphicon-trash' /></i></td>";
            nuevaFila += " </tr>";
            $("#tblConceptos tbody").html(nuevaFila);
            y++;
        }

    }

    function js_marca() {
        $("#txtmontoConcepto").val("");
        if ($("#cbconcepto").val() == '30') {
            $("#txtmontoConcepto").val("30");
        }
        $("#txtmontoConcepto").focus();
    }

    function js_addconcepto() {
        arrConceptos = [];
        $("#tblConceptos tbody").html("");
        $('#formAdicional')[0].reset(); // reset form on modals
        $('#htxtalumno').val('');
        $('#htxtsalon').val('');
        $("#rbdTipo").val('');
        mayuscula("input#txtpaterno");
        mayuscula("input#txtmaterno");
        mayuscula("input#txtnombres");
        mayuscula("input#txtrecibo");
        $('#modal_conceptos').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('AGREGAR CONCEPTO DE PAGO'); // Set title to Bootstrap modal title               
    }

    function js_cargaConcepto() {
        var tipo = $("#cbtipo").val();
        $("#cbconcepto").empty();
        if (tipo != '0') {
            var data = {
                idTipo: tipo
            };
            $.ajax({
                url: "<?= BASE_URL ?>swp_pagos/getConceptos/",
                type: "POST",
                dataType: "json",
                data: data,
                beforeSend: function () {
                    $("#cbconcepto").append("<option value=''>Cargando... </option>");
                },
                success: function (data)
                {
                    if (data['data'].length > 0) {
                        $("#cbconcepto").empty();
                        $("#cbconcepto").append("<option value='0'>:::::::::::: Seleccione ::::::::::::</option>");
                        $.each(data['data'], function (i, item) {
                            $("#cbconcepto").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error interno. Comuniquese con el Administrador.');
                }
            });
        }
    }

    function js_save_concepto()
    {
        $('#btnSaveConcepto').attr("disabled", false);
        var vconcepto = $("#cbconcepto").val();
        var vtipo = $("#cbtipo").val();
        var vrazon = $("#cbrazon").val();
        var vmonto = $("#txtmontoConcepto").val();
        var vfecha = $("#txtfecha").val();
        var vnumrecibo = $("#txtnumrecibo").val();
        var vpaterno = $("#txtpaterno").val();
        var vmaterno = $("#txtmaterno").val();
        var vnombres = $("#txtnombres").val();
        var valucod = $("#htxtalumno").val();
        var vtipocomp = $("#rbdTipo").val();
        var vmodalidad = $("#cbtipoModalidad").val();
        var vvoucher = $("#txtvoucher").val();

        var dataItems = arrConceptos;

        /*if ($.trim(vpaterno) == '') {
         alert("Ingrese el Apellido Paterno del Alumno");
         $("#txtpaterno").focus();
         return false;
         }
         
         if ($.trim(vmaterno) == '') {
         alert("Ingrese el Apellido Materno del Alumno");
         $("#txtmaterno").focus();
         return false;
         }
         
         if ($.trim(vnombres) == '') {
         alert("Ingrese los Nombres del Alumno");
         $("#txtnombres").focus();
         return false;
         }*/

        if (vtipo == '0') {
            alert("Seleccione un Tipo de Pago");
            return false;
        }
        if (vconcepto == '0') {
            alert("Seleccione un Concepto de Pago");
            return false;
        }
        if (vrazon == '0') {
            alert("Seleccione la Razón de Pago");
            return false;
        }


        /*  if ($.trim(vmonto) == '') {
         alert("Ingrese el monto del concepto de Pago");
         return false;
         }*/

        if ($.trim(vnumrecibo) == '') {
            alert("Ingrese el número de Recibo");
            return false;
        }
        // que valide que ingrese el voucher si el medio de pago es diferente a oficina
        if ($.trim(vvoucher) == '' && vmodalidad!=1) {
            alert("Ingrese el número de Voucher");
            return false;
        }
        
        if (arrConceptos.length <= 0) {
            alert("DEBE DE AGREGAR CONCEPTOS A LA LISTA.");
            return false;
        }
        var arrdata = {
            vfecha: vfecha,
            vapepat: vpaterno,
            vapemat: vmaterno,
            vnom: vnombres,
            vmonto: vmonto,
            vconcepto: vconcepto,
            vnumrecibo: vnumrecibo,
            vtipo: vtipo,
            vrazon: vrazon,
            valucod: valucod,
            vtipocomp: vtipocomp,
            vmodalidad : vmodalidad,
            vvoucher : vvoucher,
            dataItems: dataItems
        };

        $('#btnSaveConcepto').attr("disabled", true);
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/grabarConceptoAdic/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                if (data['flg'] == 0) {
                    $('#btnSaveConcepto').attr("disabled", false);
                    $('#modal_conceptos').modal('hide');
                    gridTable.ajax.reload(null, false);
                    js_imprimirComprobante();
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('OCURRIO UN ERROR INTERNO.');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });

    }

    function js_eliminaPago(vid, vnumrecibo) {
        var msg = window.confirm("ESTA SEGURO DE ELIMINAR EL PAGO ?");
        if (msg) {
            var usu = $("#husuario").val();
            if (usu === 'LHUAYTALLA'  || usu === 'MHUAYTALLA' || usu === 'FHERRERA') {
                var arrdata = {
                    vid: vid,
                    vnumrecibo: vnumrecibo
                };
                $.ajax({
                    url: "<?= BASE_URL ?>swp_pagos/deletePagoAdicional/",
                    type: "POST",
                    dataType: "json",
                    data: arrdata,
                    success: function (data)
                    {
                        alert(data['msg']);
                        if (data['flg'] == 0) {
                            gridTable.ajax.reload(null, false);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error get data from ajax');
                    }
                });
            } else {
                alert('SU USUARIO NO TIENE PRIVILEGIOS PARA ELIMINAR EL REGISTRO.\nCONTACTAR CON : LIZZETH HUAYTALLA.');
            }
        }
    }

    function js_reImprimirPago(vnumrecibo, vrazon) {
        $("#txtnumero").val(vnumrecibo);
        $("#txtrazon").val(vrazon);
        $("#formPrincipal").attr("action", "<?= BASE_URL ?>swp_pagos/reimprimirTicket/");
        $("#formPrincipal").submit();
    }
    function js_imprimirComprobante() {
        $("#formAdicional").attr("action", "<?= BASE_URL ?>swp_pagos/printTicket/");
        $("#formAdicional").submit();
    }

    function NumCheck(e, field) {
        key = e.keyCode ? e.keyCode : e.which
        if (key == 8)
            return true
        if (key > 47 && key < 58) {
            if (field.value == "")
                return true
            regexp = /.[0-9]{2}$/
            return !(regexp.test(field.value))
        }
        if (key == 46) {
            if (field.value == "")
                return false
            regexp = /^[0-9]+$/
            return regexp.test(field.value)
        }
        return false
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

    function js_listar() {
        gridTable = $('#viewAdiconales').DataTable({
            "ordering": false,
            "searching": true,
            "bFilter": true,
            "bInfo": true,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 15,
            "lengthMenu": [[15, 30, 50, -1], [15, 30, 50, "All"]],
            "bLengthChange": true,
            "ajax": {
                "url": "<?= BASE_URL ?>swp_pagos/lstPagosAdicional/",
                "type": "POST"
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
                "searchPlaceholder": "Dato para buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {"className": "dt-center", "targets": [0, 1, 3, 4, 5, 6]}
            ],
            "columns": [
                {"data": "id"},
                {"data": "fecreg"},
                {"data": "nomcomp"},
                {"data": "concepto"},
                {"data": "recibo"},
                {"data": "monto"},
                {"data": "config"}
            ]
        });
    }

    function limpiaVoucher(valor){
        if(valor=='1'){
             $("#txtvoucher").val("");
        } else {
            $("#txtvoucher").val("");
            $("#txtvoucher").focus();
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
</script>    