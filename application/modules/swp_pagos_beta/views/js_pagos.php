

<script type="text/javascript" >
    var save_method;
    var gridTable;
    var checked = "";
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });

        mayuscula("input#txtAlumnoSearch");

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
            source: "<?= BASE_URL ?>swp_pagos_beta/filtroAlumno",
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
            var tipodoc = $("#cbdocumento").val();
            var numdoc = $("#txtdocumento").val();            
            $("#txtcliente").val("");
            $("#txtdireccion").val("");
            
            if (tipodoc == '01') {
                consultar_dni(numdoc);
            } else if (tipodoc == '02') { 
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
        $("#cbdocumento").change(function ()
        {
            var vid = $(this).val();
            console.log(vid);
            $("#txtdocumento").val("");
            $("#txtcliente").val("");
            $("#txtdireccion").val("");
            if (vid == "01") {
                $("#txtdocumento").attr("placeholder", "Ingrese DNI");
                $("#txtdocumento").attr("maxlength", 8);
            }
            if (vid == "02") {
                $("#txtdocumento").attr("placeholder", "Ingrese RUC");
                $("#txtdocumento").attr("maxlength", 11);
            }
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

        $("#rbdOptions1,#rbdOptions2,#rbdOptions3").click(function () {
            console.log($(this).val());
            var label = '';
            $("#divlblComprobante").html("");
            $("#txtserie").val("");
            if ($(this).val() == '01') {
                $("#txtserie").val("R001");
                label = 'RECIBO:';
            }
            if ($(this).val() == '02') {
                $("#txtserie").val("B001");
                label = 'BOLETA:';
            }
            if ($(this).val() == '03') {
                label = 'FACTURA:';
                $("#txtserie").val("F001");
            }
            $("#divlblComprobante").html(label);
        });

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

    function js_registrarPago()
    {
        checked = "";
        $('#btnSave').text('Pagar');
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
            url: "<?= BASE_URL ?>swp_pagos_beta/getPago/",
            type: "POST",
            dataType: "json",
            data: {varrCheck: checked, vIdAlumno: vAlucod},
            success: function (data)
            {
                if (data['data'].length > 0) {
                    var vChecks = '';
                    var vTotal = 0;
                    // $("#divUlPagos").html("");
                    var row = 1;
                    $.each(data['data'], function (i, item) {
                        // if (item.concob == '02' || item.concob == '04') { // Para cuota de insripcion y matricula con pagos diferentes
                        // vChecks += '<li><input type="checkbox" name="chk[]" disabled="disabled" checked="checked" />&nbsp;' + item.concepto + '&nbsp;:&nbsp;<input type="text" value=""  style="width: 40px;text-align:center;color:black;background:orange;" name="inputPagos[]" id="txtmonto_' + row + '" size="4" onkeypress="return NumCheck(event, this);" onkeyup="js_recalcular(this.id, this.value);" maxlength="5" placeholder="00.00"/></li>';
                        //} else {
                        vChecks += '<li><input type="checkbox" name="chk[]" disabled="disabled" checked="checked" />&nbsp;' + item.concepto + '  : ' + item.fecven + '</li>';
                        //}
                        vTotal += parseFloat(item.montopen);
                    });
                    // $("#divUlPagos").html(vChecks);
                    $("#lblTotal").html("S/" + vTotal.toFixed(2));
                    $("#txttotal").val(vTotal);
                    var txt = $("#txtAlumnoSearch").val(); // $("#cbalumno option:selected").text();
                    $("#pAlumno").html("<b>ALUMNO(A) :</b> " + txt);
                    $('input[name="txtalucod"]').val(vAlucod);
                    $('input[name="txtmescodId"]').val(data['arrMescodId']);
                    $('input[name="txtconcodId"]').val(data['arrConcodId']);
                }
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('PAGO EN CAJA'); // Set title to Bootstrap modal title
                $('#hidnemo').val(vnemo);
                $("#txtnumrecibo").focus();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_recalcular(id, val) {
        var total = $("#txttotal").val();
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
        }
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
                url: "<?= BASE_URL ?>swp_pagos_beta/deletePago/",
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
        var vcomp = $("#cbcomprobante").val();
        var vidnemo = $("#hidnemo").val();


        if ($.trim(numrec) == "") {
            alert("Ingrese el numero de Recibo.");
            $("#txtnumrecibo").focus();
            return false;
        }

        if ((total == '' || total == 0) && (vtemp == '' || vtemp == 0)) {
            alert("Debe de ingresar el monto del concepto de Pago.");
            return false;
        }
        var vIdAlu = $("#txtalucod").val();
        var vIdsMes = $("#txtmescodId").val();
        var vIdsCobro = $("#txtconcodId").val();
        //inputs-box
        var arrPagos = "";
        $("input[name='inputPagos[]']").each(function ()
        {
            arrPagos += $(this).val() + "|";
        });
        //alert(vIdsMes);
        if (arrPagos == '' && vIdsMes == '03') {
            arrPagos = $("#txttotal").val();
        }

        $('#btnSave').text('Grabando...');

        var arrdata = {
            vIdAlu: vIdAlu,
            vIdsMes: vIdsMes,
            vIdsCobro: vIdsCobro,
            vnumrec: numrec,
            varrPagos: arrPagos,
            vcbtipo: vcbtipo,
            vfecha: vfecha,
            vcomp: vcomp,
            vidnemo: vidnemo
        };
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos_beta/savePago/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                $('#modal_form').modal('hide');
                gridTable.ajax.reload(null, false);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
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
        $("#formPrincipal").attr("action", "<?= BASE_URL ?>swp_pagos_beta/printeecc/");
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
            url: "<?= BASE_URL ?>swp_pagos_beta/getAddConcepto/",
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

        if ($.trim(vnumrecibo) == '') {
            alert("Ingrese el numero de Recibo");
            return false;
        }

        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos_beta/grabarConcepto/",
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
                "url": "<?= BASE_URL ?>swp_pagos_beta/lstPagos/",
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

function consultar_dni(dni) {
    $.ajax({
        url : '<?= BASE_URL ?>swp_pagos_beta/srvreniec',
        data: {num_documento: dni, tipo: 'dni'},
        method :  'POST',
        dataType : "json"
    }).then(function(data){
        if(data.respuesta == 'ok') {
            $("#txtcliente").val(data.nombre);
        } else {
            $("#txtcliente").val(data.mensaje);
        }
       /* $("#icon_search_document").show();
        $("#icon_searching_document").hide();
        $(".search_document").prop('disabled', false);*/

        console.log(data);

    }/*, function(reason){
        swal({
            title: 'ERROR',
            text: 'Error al conectarse a la SUNAT, recarga la página e inténtalo nuevamente!',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $("#icon_search_document").show();
            $("#icon_searching_document").hide();
            $(".search_document").prop('disabled', false);
        });
    }*/);
}

function consultar_ruc(ruc) {
    $.ajax({
        url : '<?= BASE_URL ?>swp_pagos_beta/srvreniec',
        data: {num_documento: ruc, tipo: 'ruc'},
        method :  'POST',
        dataType : "json"
    }).then(function(data){
      /*  $("#icon_search_document").show();
        $("#icon_searching_document").hide();
        $(".search_document").prop('disabled', false);*/

        $("#txtcliente").val(data.razon_social);
        $("#txtdireccion").val(data.direccion);
        console.log(data);
    }/*, function(reason){
        swal({
            title: 'ERROR',
            text: 'Error al conectarse a la SUNAT, recarga la página e inténtalo nuevamente!',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $("#icon_search_document").show();
            $("#icon_searching_document").hide();
            $(".search_document").prop('disabled', false);
        });
    }*/);
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
