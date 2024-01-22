

<script type="text/javascript" >
    var save_method;
    var gridTable;
    var checked = "";
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });
        $('[data-toggle="tooltip"]').tooltip();
        mayuscula("input#txtAlumnoSearch");
        $("#txttotal").hide();
        $("#lblTotal").show();

        var anio = $("#hanio").val();
        if (anio >= 2019) {
            $("#divrecibo").hide();
        } else {
            $("#divrecibo").show();
        }
        $("#txtAlumnoSearch").keypress(function (key) {
            console.log(key.charCode)
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
            source: "<?= BASE_URL ?>swp_pagos/filtroAlumno",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.alucod != '') {
                    var vnemosdes = ui.item.nemodes;
                    vnemosdes = vnemosdes.split("-");
                    $('#htxtalumno').val(ui.item.alucod);
                    $('#htxtsalon').val(ui.item.nemo);
                    $("#txtAlumnoSearch").val(ui.item.nomcomp + " (" + $.trim(vnemosdes[2]) + ")");
                    $("#txtAlumnoSearch").attr("disabled", true);
                    $("#btnBuscar").focus();
                } else {
                    $('#htxtalumno').val('');
                    $('#htxtsalon').val('');
                    $("#txtAlumnoSearch").val('');
                    $("#txtAlumnoSearch").attr("disabled", false);
                }
                //  return false;
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $("#txtfecha").datepicker({
            dateFormat: 'yy-mm-dd',
            language: 'es',
            showToday: true,
            autoclose: true
        });

        $("#btnFiltrar").click(function ()
        {
            var tipodoc = $('input:radio[name=rbdLineaOptions]:checked').val();
            var vTipoApo = $('input:radio[name=rbdLineaApoderados]:checked').val();
            var numdoc = $("#txtdni").val();
            if (numdoc == "") {
                alert("Ingrese el numero de Documento.")
                $("#txtdni").focus();
                return false;
            }
            if (typeof vTipoApo === "undefined") {
                alert("Seleccione el Tipo de Apoderado.");
                return false;
            }

            $("#txtcliente").val("");
            if (tipodoc == '01' || tipodoc == '02') { // Recibo y Boleta
                consultar_dni(numdoc);
            } else if (tipodoc == '03') { // Facturas
                consultar_ruc(numdoc);
            }
        });

        $("#btnReset").click(function ()
        {
            $("#viewPagos tbody").html("");
            $('#htxtalumno').val('');
            $('#htxtsalon').val('');
            $("#txtAlumnoSearch").val('');
            $("#txtAlumnoSearch").attr("disabled", false);
            $("#txtAlumnoSearch").focus();
        });

        //$("#cbalumno").change(function ()
        // {
        // $('#viewPagos').DataTable().destroy();
        //$("#viewPagos tbody").html("");
        //});

        $("#rbdApo1,#rbdApo2").click(function () {
            $("#txtcliente").val("");
            $("#txtdni").val("");
        });

        $("#rbdOptions1,#rbdOptions2,#rbdOptions3").click(function () {
            console.log($(this).val());
            var label = '';
            $("#divlblComprobante").html("");
            $("#txtnumrecibo").val("");
            $("#rbdTipo").val("");
            var vnemo = $("#htxtsalon").val();
            if ($(this).val() == '01') {
                $("#btnSave").attr("disabled", false);
                $("#txtdni").val($("#htxtdni").val());
                //alert($("#htxtpaterno").val() + " " + $("#htxtmaterno").val() + ", " + $("#htxtnombres").val());
                $("#txtcliente").val($("#htxtpaterno").val() + " " + $("#htxtmaterno").val() + ", " + $("#htxtnombres").val());
                $("#rbdTipo").val("01");
                label = 'RECIBO';
                $("#txtdni").attr("placeholder", "Ingrese DNI");
                $("#txtdni").attr("maxlength", 8);
            }
            if ($(this).val() == '02') {
                $("#btnSave").attr("disabled", false);
                $("#txtdni").val($("#htxtdni").val());
                //  alert($("#htxtpaterno").val() + " " + $("#htxtmaterno").val() + ", " + $("#htxtnombres").val());
                $("#txtcliente").val($("#htxtpaterno").val() + " " + $("#htxtmaterno").val() + ", " + $("#htxtnombres").val());
                $("#rbdTipo").val("02");
                label = 'BOLETA';
                $("#txtdni").attr("placeholder", "Ingrese DNI");
                $("#txtdni").attr("maxlength", 8);
                $("#btnFiltrar").attr("disabled", false);
                $("#rbdApo1").attr("disabled", false);
                $("#rbdApo2").attr("disabled", false);
            }
            if ($(this).val() == '03') {
                $("#btnSave").attr("disabled", false);
                label = 'FACTURA';
                $("#txtdni").val("");
                $("#txtcliente").val("");
                $("#txtdni").focus();
                $("#rbdTipo").val("03");
                $("#txtdni").attr("placeholder", "Ingrese RUC");
                $("#txtdni").attr("maxlength", 11);
                $("#btnFiltrar").attr("disabled", false);
                $("#rbdApo1").attr("disabled", false);
                $("#rbdApo2").attr("disabled", false);
            }
            $("#divlblComprobante").html(label);
            // Llamando al ajax que obtiene el numero de document
            $.ajax({
                url: "<?= BASE_URL ?>swp_pagos/getDocumento/",
                type: "POST",
                dataType: "json",
                data: {vidnemo: vnemo, vTipo: $(this).val()},
                beforeSend: function () {
                    $('.loading').show();
                },
                success: function (data) {
                    $("#txtnumrecibo").val(data['gencod']);
                    $('input[name="htxtnumrecibo"]').val(data['gencod']);
                },
                complete: function () {
                    $('.loading').hide();
                }});

        });

        $("#cbsalon").change(function ()
        {
            $.ajax({
                url: "<?= BASE_URL ?>sga_asistencia/lstalumno/" + $("#cbsalon").val(),
                dataType: "json",
                beforeSend: function () {
                    //$('.loading').show();
                    $("#cbalumno").empty();
                    $("#cbalumno").append("<option value=''>Cargando Alumnos ......</option>");
                },
                success: function (data) {
                    $("#cbalumno").empty();
                    $("#cbalumno").append("<option value='0'>::::::::::::::::::::::::::: Seleccione Alumno ::::::::::::::::::::::::::::</option>");
                    $.each(data, function (i, item) {
                        $("#cbalumno").append("<option value=\"" + item.ALUCOD + "\">" + item.ALUCOD + " : " + item.NOMCOMP + "</option>");
                    });
                },
                complete: function () {
                    // $('.loading').hide();
                }});
        });

        // ====================== Para Seleccionar todos =====================
        /*      $(".select-all").click(function () {
         alert('1');
         $('.chk-box').attr('checked', this.checked)
         });*/
        // ==================== Para De-seleccionar todos ====================
        $(".select-all").click(function () {
            if (this.checked)
            {
                $(".chk-box").attr("checked", true);
            } else
            {
                $(".chk-box").attr("checked", false);
            }
        });
        // =================================================================
        $('.input-number').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });


    });

    function js_pagar() {
        checked = "";
        $("input[name='chkPagos[]']:checked").each(function ()
        {
            //checked.push($(this).val());
            checked += $(this).val() + "*";
        });
    }

    function js_habilita(chk) {
        if (chk == true) {
            alert("RECUERDE QUE EL NUEVO MONTO SOLO APLICARÁ PARA LOS MESES SELECCIONADOS.");
            //$("#txttotal").show();
            //$("#lblTotal").hide();
            $('.input-number').each(function () {
                var id = $(this).attr('id');
                var tip = $(this).attr('tp');
                if (tip == '1') {
                    $("#" + id).removeAttr('disabled');
                }
            });
        } else {
            //$("#txttotal").hide();
            //$("#lblTotal").show();
            $('.input-number').each(function () {
                var id = $(this).attr('id');
                //var tip = $(this).attr('tp');
                // if (tip == '1') {
                $("#" + id).attr('disabled', 'disabled');
                // }
            });
        }
    }
    function js_registrarPago()
    {
        checked = "";
        $("#btnSave").attr("disabled", true);
        $("#btnFiltrar").attr("disabled", true);
        $("#rbdApo1").attr("disabled", true);
        $("#rbdApo2").attr("disabled", true);
        $("#divlblComprobante").html("");
        $("#txttotal").hide();
        $("#lblTotal").show();
        $('#btnSave').text('Pagar');
        //$("#rbdTipo").val("02"); // Tipo por defecto : Recibos
        var vAlucod = $("#htxtalumno").val(); // $("#cbalumno").val();
        var vnemo = $("#htxtsalon").val(); //$("#cbsalon").val();
        $("input[name='chkPagos[]']:checked").each(function ()
        {
            //checked.push($(this).val());
            checked += $(this).val() + "*";
        });
        // alert(checked);
        if (checked == "") {
            alert("Seleccione los Pagos a Cancelar.");
            return false;
        }
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/getPago/",
            type: "POST",
            dataType: "json",
            data: {varrCheck: checked, vIdAlumno: vAlucod, vidnemo: vnemo},
            success: function (data)
            {
                if (data['data'].length > 0) {
                    var vChecks = '';
                    var vTotal = 0;
                    $("#divUlPagos").html("");
                    var row = 1;
                    $.each(data['data'], function (i, item) { //<li class="list-group-item nomargin">01 - PENSIÓN MARZO - 27-03-2019 =======>S/.320.00</li>
                        if (item.concob == '01') { // Para cuota de insripcion y matricula con pagos diferentes
                            vChecks += '<li class="list-group-item nomargin">';
                            vChecks += '<input type="checkbox" name="chk[]" disabled="disabled" checked="checked" />&nbsp;' + item.concepto + '&nbsp;:&nbsp;';
                            vChecks += ' <div style="float:right;"><input type="text" tp="1" value="' + parseInt(item.montopen) + '" class="input-number"  style="width: 40px;text-align:center;color:black;background-color: khaki;" disabled="disabled"  name="inputPagos[]" id="txtmonto_' + row + '" size="4" onkeypress="return NumCheck(event, this);" onkeyup="js_recalcular(this.id, this.value);"  maxlength="5"  placeholder="00.00"/></div>';
                            vChecks += '</li>';
                        } else {
                            // vChecks += '<li class="list-group-item nomargin"><input type="checkbox" name="chk[]" disabled="disabled" checked="checked" />&nbsp;' + item.concepto + '  :  ' + item.fecven + '</li>';
                            vChecks += '<li class="list-group-item nomargin">';
                            vChecks += '<input type="checkbox" name="chk[]"  disabled="disabled" checked="checked" /><b>&nbsp;' + item.concepto + '&nbsp;:&nbsp;</b>';
                            vChecks += ' <div style="float:right;"><input type="text" tp="0" value="' + parseInt(item.montopen) + '" class="input-number"  style="width: 40px;text-align:center;color:black;background-color: silver;" disabled="disabled"  name="inputPagos[]" id="txtmonto_' + row + '" size="4"   maxlength="5" /></div>';
                            vChecks += '</li>';
                        }

                        // vChecks += '<li class="list-group-item nomargin"><input type="checkbox" name="chk[]" disabled="disabled" checked="checked" />&nbsp;' + item.concepto + '  : ' + item.fecven + '</li>';                        
                        vTotal += parseFloat(item.montopen);
                        row++;
                    });
                    $("#divUlPagos").html(vChecks);
                    $("#lblTotal").html("S/" + vTotal.toFixed(2));
                    $("#txttotal").val(vTotal);
                    var txt = $("#txtAlumnoSearch").val(); // $("#cbalumno option:selected").text();
                    $("#pAlumno").html("<b>ALUMNO(A) :</b> " + txt);
                    $('input[name="txtalucod"]').val(vAlucod);
                    $('input[name="txtnumrecibo"]').val(data['gencod']);
                    $('input[name="htxtnumrecibo"]').val(data['gencod']);
                    $('input[name="txtcliente"]').val(data['familia']);
                    $('input[name="txtdni"]').val(data['dni']);
                    $('input[name="htxtflagdni"]').val(data['flgDni']);
                    $('input[name="htxtfamcod"]').val(data['famcod']);
                    if (data['flgApo'] == "P") {
                        document.getElementById("rbdApo1").checked = true;
                    }
                    if (data['flgApo'] == "M") {
                        document.getElementById("rbdApo2").checked = true;
                    }
                    $('input[name="htxtpaterno"]').val(data['dataPaterno']);
                    $('input[name="htxtmaterno"]').val(data['dataMaterno']);
                    $('input[name="htxtnombres"]').val(data['dataNombre']);
                    $('input[name="htxtdni"]').val(data['dni']);
                    $('input[name="htxtnumrecibo"]').val(data['gencod']);
                    $('input[name="txtmescodId"]').val(data['arrMescodId']);
                    $('input[name="txtconcodId"]').val(data['arrConcodId']);
                    //js_cargaApoderado(data['famcod']);
                }
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                //$('.modal-title').text(':::::::::::::::::::::::: REGISTRAR PAGO - COLEGIO MARIANISTA ::::::::::::::::::::::::'); // Set title to Bootstrap modal title
                $('#hidnemo').val(vnemo);
                $("#txtnumrecibo").focus();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Ocurio un Error Interno\nComuniquese con el Administrador.');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_cargaApoderado(vfamcod) {
        if (vfamcod == '0' || vfamcod == '') {
            alert("No se cargo el Bloque DATOS DE FAMILIA  porque el Alumno no tiene Familia Asignada.");
            return false;
        }
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/getApoderados/",
            type: "POST",
            dataType: "json",
            data: {vfamcod: vfamcod},
            success: function (data)
            {
                $("#viewPadres tbody").html("");
                if (data['data'].length > 0) {
                    var data = data['data'];
                    var fila = 1;
                    $('input[name="hfamcod"]').val(vfamcod);
                    $.each(data, function (i, row) {
                        // var vimp = "<a class='btn' title='Imprimir' onclick='js_printBoleta(\"" + row.numrecibo + "\")' ><i class='glyphicon glyphicon-print'></i></a>";
                        if (row.ESAPO == 1 && fila == 1) {
                            var vchecked = "checked='checked'";
                        } else if (row.ESAPO == 1 && fila == 2) {
                            var vchecked = "checked='checked'";
                        } else {
                            var vchecked = "";
                        }
                        if (fila == 1)
                            var tipo = 'PAPÁ';
                        if (fila == 2)
                            var tipo = 'MAMÁ';
                        var nuevaFila =
                                " <tr id='fila_" + fila + "'>"
                                + " <td style='width: 4%;text-align: center'><input class='form-check-input' type='radio' " + vchecked + " onclick=\"js_marcaApoderado('" + row.DNI + "','" + fila + "')\" name='rbbFlagApoderado' id='rbdOptions_" + fila + "'value='" + fila + "' ></td>"
                                + " <td style='width: 8%;text-align: center'>" + tipo + "</td>"
                                + " <td style='width: 16%;text-align: left' id='rowpaterno_" + fila + "'>" + row.PATERNO + "</td>"
                                + " <td style='width: 16%;text-align: left' id='rowmaterno_" + fila + "'>" + row.MATERNO + "</td>"
                                + " <td style='width: 15%;text-align: left' id='rownombre_" + fila + "'>" + row.NOMBRE + "</td>"
                                + " <td style='width: 12%;text-align: center'>"
                                + "   <input type='text' value='" + row.DNI + "'  name='txtdnisearch_" + fila + "' class='form-control'   id='txtdnisearch_" + fila + "'  maxlength='8' />"
                                + " </td>"
                                + " <td style='width: 4%;text-align: center'>"
                                + "  <button type='button'  value='' onclick=\"js_filtrarDni('" + fila + "')\" data-toggle='tooltip' title='Buscar por RENIEC'  class='btn btn-primary'><i class='glyphicon glyphicon-search'></i></button>"
                                + " </td> "
                                + " </tr> "
                        $(nuevaFila).appendTo("#viewPadres tbody");
                        fila++;
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error Interno. Bloque Data de Padres');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_marcaApoderado(vdni, valor) {
        var vfamcod = $("#hfamcod").val();
        if (vdni == "" || vdni.length < 8) {
            alert("INGRESE UN DNI VALIDO Y LUEGO PRESIONE BUSCAR.");
          //  alert(valor);
            if (valor == 1) {
                $("#rbdOptions_1").attr("checked", false);
                 $("#rbdOptions_2").attr("checked", true);
                $("#txtdnisearch_1").focus();
            }
            if (valor == 2) {
                $("#rbdOptions_2").attr("checked", false);
                $("#rbdOptions_1").attr("checked", true);
                $("#txtdnisearch_2").focus();
            }
            return false;
        }
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/marcarApoderados/",
            type: "POST",
            dataType: "json",
            data: {vdni: vdni, valor: valor, vfamcod: vfamcod},
            success: function (data)
            {
                if (data["flgapo"] == '1') { // papa
                    $("#txtdni").val(data["data"][0].paddni);
                    $("#txtcliente").val(data["data"][0].padapepat + " " + data["data"][0].padapemat + ", " + data["data"][0].padnombre);
                    $("#rbdApo1").attr("checked", true);
                }
                if (data["flgapo"] == '2') { // mama 
                    $("#txtdni").val(data["data"][0].maddni);
                    $("#txtcliente").val(data["data"][0].madapepat + " " + data["data"][0].madapemat + ", " + data["data"][0].madnombre);
                    $("#rbdApo2").attr("checked", true);
                }
                alert(data["msg"]);
                js_cargaApoderado(vfamcod);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error Interno. Bloque Data de Padres');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_filtrarDni(row) {
        var vfamcod = $("#hfamcod").val();
        var vdni = $("#txtdnisearch_" + row).val();
        if (vdni.length < 8) {
            alert("Ingrese un numero de DNI Valido.");
            return false;
        }
        if (vdni != '') {
            $.ajax({
                url: '<?= BASE_URL ?>swp_pagos/srvreniec',
                data: {num_documento: vdni, tipo: 'dni'},
                method: 'POST',
                dataType: "json",
                beforeSend: function () {
                    $("#txtdnisearch_" + row).val("Cargando....");
                },
                success: function (data) {
                    if (data.respuesta == 'ok') {
                        $("#txtdnisearch_" + row).val(vdni);
                        $("#rowpaterno_" + row).html(data.ap_paterno);
                        $("#rowmaterno_" + row).html(data.ap_materno);
                        $("#rownombre_" + row).html(data.nombres);
                        // ==== Actualizamos los datos en la tabla familia 
                        var dataFamilia = {
                            dni: vdni,
                            tipo: row,
                            paterno: data.ap_paterno,
                            materno: data.ap_materno,
                            nombres: data.nombres,
                            vfamcod: vfamcod
                        };
                        js_actualizaFamilia(dataFamilia);
                    } else {
                        alert("Numero de DNI no Existe");
                    }
                },
                complete: function () {
                    // $('.loading').hide();
                }});
        }
    }

    function js_actualizaFamilia(data) {
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/updateDatosApoderado/",
            type: "POST",
            dataType: "json",
            data: data,
            success: function (data)
            {
                console.log(data["msg"]);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error Interno. Bloque Data de Padres');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }
    function js_recalcular(id, val) {
        /*   var total = $("#txttotal").val();
         $("#txttemp").val(0);
         //if ($("#" + id).val().length > 2) { 
         if (parseInt($("#" + id).val()) > 0) {
         $("#" + id).val(val);
         var vTotal = parseFloat($("#txttemp").val()) + parseFloat(val);
         $("#lblTotal").html("");
         $("#lblTotal").html("S/" + (vTotal + parseFloat(total)).toFixed(2));
         $("#txttemp").val(vTotal);
         } else {
         $("#lblTotal").html("");
         $("#lblTotal").html("S/" + parseFloat(total).toFixed(2));
         }*/
        var total = 0;
        $('.input-number').each(function () {
            //alert("ID:"+$(this).attr('id') + "   "+parseFloat($("#" + $(this).attr('id')).val()));
            //$("#" + id).removeAttr('disabled');
            if ($("#" + $(this).attr('id')).val() != "") {
                total += parseFloat($("#" + $(this).attr('id')).val())
            }
        });
        $("#lblTotal").html("");
        $("#lblTotal").html("S/" + parseFloat(total).toFixed(2));
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

    function js_delpago(vconcob, vmescob, valucod) {
        var msg = window.confirm("ESTA SEGURO DE ELIMINAR EL PAGO DEL MES : " + fn_meses(vmescob) + "?");
        if (msg) {
            var arrdata = {
                vconcob: vconcob,
                vmescob: vmescob,
                valucod: valucod
            };
            $.ajax({
                url: "<?= BASE_URL ?>swp_pagos/deletePago/",
                type: "POST",
                dataType: "json",
                data: arrdata,
                success: function (data)
                {
                    alert(data['msg']);
                    if (data['flg'] == 0) {
                        js_verPagos();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });


        }
    }

    function js_save() {
        var numrec = $("#txtnumrecibo").val();
        var vcbtipo = $("#cbtipo").val();
        var total = $("#txttotal").val();
        var vfecha = $("#txtfecha").val();
        var vtemp = $("#txttemp").val();
        var vDNI = $("#txtdni").val();
        var vFlgDni = $("#htxtflagdni").val();
        var vFamCod = $("#htxtfamcod").val();
        var vPaterno = $("#htxtpaterno").val();
        var vMaterno = $("#htxtmaterno").val();
        var vNombres = $("#htxtnombres").val();
        var vcomp = $('input:radio[name=rbdLineaOptions]:checked').val(); //$("#cbcomprobante").val();
        var vTipoApo = $('input:radio[name=rbdLineaApoderados]:checked').val();
        var vidnemo = $("#hidnemo").val();
        var chkflag = ($('#chkHabilita').is(':checked')) ? 1 : 0;
        if (vcomp == '02') { // Boletas
            if (typeof vTipoApo === "undefined") {
                alert("Seleccione el Tipo de Apoderado.");
                return false;
            }
            if ($.trim(vDNI) == "" || vDNI.length < 8) {
                alert("Ingrese un numero de Documento Valido.");
                $("#txtdni").focus();
                return false;
            }
        }
        if (vcomp == '03') { // Facturas
            if ($.trim(vDNI) == "" || vDNI.length < 11) {
                alert("Ingrese un numero de RUC Valido.");
                $("#txtdni").focus();
                return false;
            }
        }
        /*if ($.trim(vPaterno) == "" || $.trim(vMaterno) == "" || $.trim(vNombres) == "") {
         alert("LOS DATOS DEL APODERADO ESTA IMCOMPLETO\nINGRESE EL NUMERO DE DNI Y LUEGO PRESIONES EL BOTON BUSCAR");
         $("#txtdni").focus();
         return false;
         }*/

        if ((total == '' || total == 0) && (vtemp == '' || vtemp == 0)) {
            alert("Debe de ingresar el Monto.");
            return false;
        }
        var vIdAlu = $("#txtalucod").val();
        var vIdsMes = $("#txtmescodId").val();
        var vIdsCobro = $("#txtconcodId").val();
        //inputs-box
        var arrPagos = "";
        var i = 0;
        var flgVacio = 0;
        $("input[name='inputPagos[]']").each(function ()
        {
            if (i > 0) {
                arrPagos += '|';
            }
            arrPagos += $(this).val();
            if ($(this).val() == "") {
                flgVacio = 1;
            }
            i++;
        });
        /*   alert(vIdsCobro);
         alert(vIdsMes);        
         alert(arrPagos);
         return false;*/

        // if (arrPagos == '' /*&& vIdsCobro == '01'*/) {
        //  arrPagos = $("#txttotal").val();
        //}
        if (flgVacio == 1) {
            alert("UNO DE LOS CASILLEROS DE MONTO SE ENCUENTRA VACIO, VERIFIQUE.");
            return false;
        }

        $('#btnSave').text('Registrando...');

        var arrdata = {
            vIdAlu: vIdAlu,
            vIdsMes: vIdsMes,
            vIdsCobro: vIdsCobro,
            vnumrec: numrec,
            varrPagos: arrPagos,
            vcbtipo: vcbtipo,
            vfecha: vfecha,
            vcomp: vcomp,
            vidnemo: vidnemo,
            vTipoApo: vTipoApo,
            vDNI: vDNI,
            vFamCod: vFamCod,
            vFlgDni: vFlgDni,
            vPaterno: vPaterno,
            vMaterno: vMaterno,
            vNombres: vNombres,
            vchkflag: chkflag
        };
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/savePago/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                $('#modal_form').modal('hide');
                gridTable.ajax.reload(null, false);
                js_imprimirComprobante();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $('#btnSave').text('Pagar');
                alert('Error get data from ajax');
            }
        });

    }

    function js_imprimir() {
        var alucod = $("#htxtalumno").val(); //$("#cbalumno").val();
        if (alucod == 0) {
            alert("Seleccione un Alumno.");
            return false;
        }
        $("#formPrincipal").attr("action", "<?= BASE_URL ?>swp_pagos/printeecc/");
        $("#formPrincipal").submit();
    }

    function js_printBoleta(vBoleta) {
        $("#rbdTipo").val('02'); // Boletas
        $("#htxtnumrecibo").val(vBoleta);
        js_imprimirComprobantev2();
    }

    function js_imprimirComprobantev2() {
        var alucod = $("#htxtalumno").val();
        if (alucod == 0) {
            alert("Seleccione un Alumno.");
            return false;
        }
        $("#formPrincipal").attr("action", "<?= BASE_URL ?>swp_pagos/printTicketV2/");
        $("#formPrincipal").submit();
    }

    function js_imprimirComprobante() {
        var alucod = $("#htxtalumno").val();
        if (alucod == 0) {
            alert("Seleccione un Alumno.");
            return false;
        }
        $("#formPrincipal").attr("action", "<?= BASE_URL ?>swp_pagos/printTicket/");
        $("#formPrincipal").submit();
    }

    function js_concepto() {
        var vAlucod = $("#htxtalumno").val(); // $("#cbalumno").val();
        if (vAlucod == '0' || vAlucod == '') {
            alert("Seleccione al Alumno");
            return false;
        }
        $('#form2')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/getAddConcepto/",
            type: "POST",
            dataType: "json",
            data: {vIdAlumno: vAlucod},
            success: function (data)
            {
                if (data['data'].length > 0) {
                    var txt = $("#txtAlumnoSearch").val(); //$("#cbalumno option:selected").text();
                    $("#infoAlumno").html("<b>ALUMNO(A) :</b> " + txt);
                    $('input[name="txtidAlumno"]').val(vAlucod);
                    $("#cbconcepto").empty();
                    $("#cbconcepto").append("<option value='0'>:::::::::::: Seleccione ::::::::::::</option>");
                    $.each(data['data'], function (i, item) {
                        $("#cbconcepto").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                    });
                }
                $('#modal_conceptos').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('AGREGAR CONCEPTO DE PAGO'); // Set title to Bootstrap modal title
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });

    }

    function js_save_concepto()
    {
        var vconcepto = $("#cbconcepto").val();
        var vmonto = $("#txtmontoConcepto").val();
        var vAlucod = $("#htxtalumno").val(); //$("#cbalumno").val();
        var vnumrecibo = $("#txtrecibo").val();

        if (vconcepto == '0') {
            alert("Seleccione un Concepto de Pago");
            return false;
        }

        if ($.trim(vmonto) == '') {
            alert("Ingrese el monto del concepto de Pago");
            return false;
        }
        /*
         if ($.trim(vnumrecibo) == '') {
         alert("Ingrese el numero de Recibo");
         return false;
         }
         */
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/grabarConcepto/",
            type: "POST",
            dataType: "json",
            data: {vIdAlumno: vAlucod, vmonto: vmonto, vconcepto: vconcepto, vnumrecibo: vnumrecibo},
            success: function (data)
            {
                alert(data['msg']);
                if (data['flg'] == 0) {
                    $('#modal_conceptos').modal('hide');
                    gridTable.ajax.reload(null, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });

    }

    function js_verBoletas()
    {

        var vAlucod = $("#htxtalumno").val(); // $("#cbalumno").val();
        if (vAlucod == '0' || vAlucod == '') {
            alert("Seleccione al Alumno");
            return false;
        }
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos/getComprobantes/",
            type: "POST",
            dataType: "json",
            data: {vIdAlumno: vAlucod},
            success: function (data)
            {
                if (data['data'].length > 0) {
                    var data = data['data'];
                    $("#viewBoletasAlumno tbody").html("");
                    var fila = 1;
                    $.each(data, function (i, row) {
                        var vimp = "";
                        if (row.tipo_comp == '02') {
                            vimp = "<a class='btn' title='Imprimir' onclick='js_printBoleta(\"" + row.numrecibo + "\")' ><i class='glyphicon glyphicon-print'></i></a>";
                        } /*else {
                         vimp = "&nbps;";
                         }*/
                        var nuevaFila =
                                "<tr>"
                                + "<td style='text-align:center'>" + fila + "</td>"
                                + "<td>" + row.fecmod + " </td>"
                                + "<td style='text-align:center'>" + row.numrecibo + "</td>"
                                + "<td style='text-align:center'>"
                                + vimp
                                + "</td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#viewBoletasAlumno tbody");
                        fila++;
                    });
                }
                $('#modal_comprobantes').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('BOLETAS GENERADAS'); // Set title to Bootstrap modal title     
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_verPagos() {
        var alucod = $("#htxtalumno").val(); //$("#cbalumno").val();
        if (alucod == 0) {
            alert("Seleccione un Alumno.");
            return false;
        }
        gridTable = $('#viewPagos').DataTable({
            "ordering": false,
            "searching": false,
            "bFilter": false,
            "bInfo": true,
            "bDestroy": true,
            //"bRetrieve": true,
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 20,
            "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "All"]],
            // "iDisplayStart": 0,        
            "bLengthChange": false,
            "ajax": {
                "url": "<?= BASE_URL ?>swp_pagos/lstPagos/",
                "type": "POST",
                "data": {"idAlumno": alucod}
            },
            'initComplete': function (settings, json) {
                $("#checkall").removeAttr("disabled");
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
            /*"columnDefs": [
             {"className": "dt-center", "targets": [0, 1, 2, 4, 5, 6,7]}
             ],*/
            /*"columns": [
             {"className": "dt-center"},
             {"className": "dt-center"},
             {"className": "dt-center"},
             null,
             {"className": "dt-fecha"},
             {"className": "dt-center"},
             {"className": "dt-center"},
             {"className": "dt-center"}
             ],*/
            "columns": [
                {"data": "chk", "className": "dt-center"},
                {"data": "estado", "className": "dt-center"},
                {"data": "fecven", "className": "dt-center"},
                {"data": "concepto", "className": "dt-left"},
                {"data": "fecreg", "className": "dt-fecha"},
                {"data": "motno", "className": "dt-center"},
                {"data": "mora", "className": "dt-center"},
                {"data": "total", "className": "dt-center"},
                {"data": "config", "className": "dt-center"}
            ]

        });
    }

    function consultar_dni(dni) {
        $.ajax({
            url: '<?= BASE_URL ?>swp_pagos/srvreniec',
            data: {num_documento: dni, tipo: 'dni'},
            method: 'POST',
            dataType: "json",
            beforeSend: function () {
                $("#txtcliente").val("Cargando....");
            },
            success: function (data) {
                $("#txtcliente").val("");
                if (data.respuesta == 'ok') {
                    $("#txtcliente").val(data.nombre);
                    $("#htxtpaterno").val(data.ap_paterno);
                    $("#htxtmaterno").val(data.ap_materno);
                    $("#htxtnombres").val(data.nombres);
                } else {
                    $("#txtcliente").val(data.mensaje);
                    $("#htxtpaterno").val(data.mensaje);
                    $("#htxtmaterno").val(data.mensaje);
                    $("#htxtnombres").val(data.mensaje);
                }
            },
            complete: function () {
                // $('.loading').hide();
            }});
    }

    function consultar_ruc(ruc) {
        $.ajax({
            url: '<?= BASE_URL ?>swp_pagos/srvreniec',
            data: {num_documento: ruc, tipo: 'ruc'},
            method: 'POST',
            dataType: "json",
            beforeSend: function () {
                $("#txtcliente").val("Cargando....");
            },
            success: function (data) {
                $("#txtcliente").val("");
                $("#txtcliente").val(data.razon_social);
                $("#htxtpaterno").val("");
                $("#htxtmaterno").val("");
                $("#htxtnombres").val("");
                //$("#txtdireccion").val(data.direccion);
                console.log(data);
            },
            complete: function () {
                // $('.loading').hide();
            }});
    }

    function fn_meses(vmes) {
        var mesdes = "";
        switch (vmes) {
            case '02':
                mesdes = "FEBRERO";
                break;
            case '03':
                mesdes = "MARZO";
                break;
            case '04':
                mesdes = "ABRIL";
                break;
            case '05':
                mesdes = "MAYO";
                break;
            case '06':
                mesdes = "JUNIO";
                break;
            case '07':
                mesdes = "JULIO";
                break;
            case '08':
                mesdes = "AGOSTO";
                break;
            case '09':
                mesdes = "SETIEMBRE";
                break;
            case '10':
                mesdes = "OCTUBRE";
                break;
            case '11':
                mesdes = "NOVIEMBRE";
                break;
            case '12':
                mesdes = "DICIEMBRE";
                break;
        }
        return mesdes;
    }

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }

    function fechaFormat(fecha) {
        var vfecha = "";
        var vcadena = "";
        if (fecha != '' && fecha.length == 10) {
            vcadena = fecha.split("-");
            vfecha = vcadena[2] + "-" + vcadena[1] + "-" + vcadena[0];
        }
        return vfecha;
    }
</script>        
