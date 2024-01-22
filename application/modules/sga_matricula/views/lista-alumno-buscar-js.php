<script>
    $(document).ready(function () {
        mayuscula("input#txtbuscar");
        $('[data-toggle="tooltip"]').tooltip();
        $("#cbfiltro2").change(function ()
        {
            var tipo = $(this).val();
            if (tipo == 1) {
                $("#txtbuscar").val("");
                $("#txtbuscar").attr('maxlength', '8');
                $("#txtbuscar").attr('placeholder', 'Ingrese el numero de DNI');
                $("#txtbuscar").focus();
                $('#viewAlumnosFiltro').DataTable().destroy();
                $("#viewAlumnosFiltro tbody").html("");
            } else if (tipo == 2) {
                $("#txtbuscar").val("");
                $("#txtbuscar").attr('maxlength', '30');
                $("#txtbuscar").attr('placeholder', 'Ingrese Apellidos del Alumno');
                $("#txtbuscar").focus();
                $('#viewAlumnosFiltro').DataTable().destroy();
                $("#viewAlumnosFiltro tbody").html("");
            }
        });
        $("#btnFiltrar").click(function ()
        {
            js_filtrar();
        });


        $("#txtbuscar").keypress(function (event) {
            if (event.keyCode == 13) {
                //alert($("#txtsearch").val());
                if ($('#cbfiltro2').val() != '') {
                    js_filtrar();
                } else {
                    alert("SELECCIONE  EL TIPO DE FILTRO.");
                }
            }
            //alert($(this).attr('value'));
        });
        
    });

    function js_filtrar() {
        var strChk = "";
        $('input[name="chkBusqueda[]"]:checked').each(function () {
            strChk += "*" + $(this).attr('value') + "*,";
        });
        if (strChk != '') {
            strChk = strChk.substring(0, strChk.length - 1);
        }
        console.log(strChk);

        var dataFiltro = {
            vtipo: $("#cbfiltro2").val(),
            vfiltro: $.trim($("#txtbuscar").val()),
            vEstado : strChk
        };
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "<?= BASE_URL ?>sga_matricula/FiltrarAlumno",
            data: dataFiltro,
            success: function (dataJson) {
                var data = dataJson.length;
                $('#viewAlumnosFiltro').DataTable().destroy();
                $("#viewAlumnosFiltro tbody").html("");
                if (data > 0) {
                    var nuevaFila = "";
                    $.each(dataJson, function (i, row) {
                        var color = "";
                        var flgnuevo = 0;
                        if ($.trim(row.aula) === 'NINGUNO') {
                            color = "style='background-color:yellow;font-weight:bold;'";
                            flgnuevo = 1;
                        }
                        var vdni = (($.trim(row.dni) != '') ? row.dni : '');
                        var vestado ="";
                        if(row.estado==='P'){
                            vestado ='PROMOVIDO';
                        }
                        if(row.estado==='V'){
                            vestado ='VIGENTE';
                        }                        
                        if(row.estado==='R'){
                            vestado ='RETIRADO';
                        }                                      
                        if(row.estado==='A'){
                            vestado ='ANTIGUO';
                        }                                                              
                        nuevaFila +=
                                "<tr " + color + "> "
                                + "<td  style='width: 10%;text-align: center' >" + vdni + "</td>"
                                + "<td  style='width:45%;text-align: left' >" + row.nomcomp + "</td>"
                                + "<td  style='width: 10%;text-align: center' >" + vestado  + "</td>"
                                + "<td style='width: 30%;text-align: left'  >" + row.nemodes + "</td>"
                               // + "<td  align='center'><span style='font-size:15px; color:black;cursor:pointer;' onclick='js_matricular(\"" + row.alucod + "\",\"" + row.flg_matricula + "\"," + flgnuevo + ")';  class='glyphicon glyphicon-share-alt'  data-toggle='tooltip' title='Registrar'></span></td>"
                               + "<td  style='width: 5%;text-align: center' ><span style='font-size:15px; color:black;cursor:pointer;' onclick='fn_editarMatricula(\"" + row.alucod + "\",\"" + row.flg_matricula + "\")'  class='glyphicon glyphicon-new-window'  data-toggle='tooltip' title='Matricular'></span></td>"
                                + "</tr>";

                        // alert(nuevaFila);
                        //$(nuevaFila).appendTo("#viewAlumnosFiltro tbody");
                    });
                    $("#viewAlumnosFiltro tbody").html(nuevaFila);
                }
                $('#viewAlumnosFiltro').DataTable({
                    "ordering": false,
                    "bInfo": true,
                    "searching": false,
                    "bFilter": false,
                    "bDestroy": true,
                    // "iDisplayLength": 20,
                    "lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "Todos"]],
                    "bLengthChange": false
                });
            }
        });
    }



    function js_matricular(vId, vFlag, vNew)
    {
        $('#frmMatricula')[0].reset();
        var vEstado = ((vFlag == 0) ? 'M' : 'E');
        var url = "";
        if (vNew == 1) { // si es Nuevo
            url = "<?= BASE_URL ?>sga_matricula/getDatosAlumnoNuevo";
        } else {
            url = "<?= BASE_URL ?>sga_matricula/getDatosAlumno";
        }
        var parametros = {
            vId: vId,
            vflg: vFlag
        };

        $.ajax({
            type: "POST",
            url: url,
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                $('#modalAlumnoFiltro').modal('hide');
                $('#modalMatricula').modal('show');
            },
            success: function (dataJson) {
                // ----------- Hiddens con valores Llaves -------
                $("#txtalucod").val(dataJson.ALUCOD);
                $("#haccion").val(vEstado);
                $("#hdni").val(dataJson.DNI);
                $("#lbldni").val(dataJson.DNI);
                $("#lbllibro").val(dataJson.NUMLIBRO);
                $("#hestado").val(dataJson.ESTADO);
                // ------------------------------------------------
                $("#lblapellidos").val(dataJson.APEPAT + " " + dataJson.APEMAT);
                $("#lblnombres").val(dataJson.NOMBRES);

                if (vNew == 1) { // Alumno nuevo
                    $("#divold").hide();
                    $("#divactual").html("Aula:");
                    $("#cb_nivel").empty();
                    $("#cb_grado").empty();
                    $("#cb_aula").empty();
                    $('#cb_nivel').attr("disabled", false);
                    $('#cb_grado').attr("disabled", true);
                    $('#cb_aula').attr("disabled", true);
                    $("#hinstru").val(""); // falta valor ???? 

                    var newOptions = {
                        '': ':::: Seleccione ::::',
                        'I': 'Inicial',
                        'P': 'Primaria',
                        'S': 'Secundaria'
                    };
                    $.each(newOptions, function (val, text) {
                        $('#cb_nivel')
                                .append($("<option></option>")
                                        .attr("value", val)
                                        .text(text));
                    });
                    $("#cb_grado").append('<option value="">NINGUNO</option>');
                    $("#cb_aula").append('<option value="">NINGUNO</option>');

                } else { // No es Nuevo
                    if (vFlag == 0) { // NO MATRICULADO
                        $("#lblnemo").html(dataJson.NEMODES);
                        // ------------ Div descripcion de aula ------------
                        $("#divold").show();
                        $("#divactual").html("Nuevo:");
                        $("#hinstru").val(dataJson.INSTRUCODP);
                        $("#cb_nivel").empty();
                        $("#cb_nivel").append('<option value="' + dataJson.INSTRUCODP + '">' + dataJson.INSTRUDESP + '</option>');
                        $("#cb_grado").empty();
                        $("#cb_grado").append('<option value="' + dataJson.GRADOCODP + '">' + dataJson.GRADODESP + '</option>');
                        cargaAula($("#cb_nivel").val(), $("#cb_grado").val(), 'cb_aula');
                        //$("#cb_aula > option[value='" + dataJson.AULACOD + "']").attr('selected', 'selected');
                    } else { // MATRICULADO
                        // ------------ Div descripcion de aula ------------
                        $("#hinstru").val(dataJson.INSTRUCOD);
                        $("#divold").hide();
                        $("#divactual").html("Aula:");
                        $("#cb_nivel").empty();
                        $("#cb_nivel").append('<option value="' + dataJson.INSTRUCOD + '">' + dataJson.INSTRUDES + '</option>');
                        // $("#cb_grado").empty();
                        //$("#cb_grado").append('<option value="' + dataJson.GRADOCOD + '">' + dataJson.GRADODES + '</option>');

                        cargaGrado();
                        setTimeout(function () {
                            $("#cb_grado > option[value='" + dataJson.GRADOCOD + "']").attr('selected', 'selected');

                            cargaAula($("#cb_nivel").val(), $("#cb_grado").val(), 'cb_aula');
                            setTimeout(function () {
                                $("#cb_aula > option[value='" + dataJson.AULACOD + "']").attr('selected', 'selected');
                            }, 1000);

                        }, 1000);


                    }
                }

                if (dataJson.ESTADO != 'N') { // Si es Alumno no es Nuevo
                    // ------------- Cargamos los cursos a cargo --------------------
                    js_cargarCursoCargo(vId);
                    // ------------- Cargamos los documentos  ----------------------
                    js_cargarDocumentos(vId, 'chdoc', 'txtcomentarios');
                    // ------------- Cargamos los pagos -----------------------------
                    js_cargaPagos(dataJson.ALUCOD);
                    // ---------------------------------------------------------------
                }

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });
    }

    function js_cargarCursoCargo(vId) {
        var parametros = {
            vId: vId
        };
        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_alumnos/getCursoCargo",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {

            },
            success: function (data) {
                // alert(data['arrHead'].nomcomp);
                var vreg = data['arrBody'];
                var html = '';
                var fila = 1;
                $("#viewGridCursos").html('');
                html += '<table class="table table-striped table-bordered"    id="xxx" style="width: 100%">';
                html += '   <thead>';
                html += '   <tr class="tableheader">';
                html += '   <th style="width: 10%;text-align: center">Codigo</th>';
                html += '   <th style="width: 35%;text-align: center">Descripcion Curso</th>';
                html += '   <th style="width: 10%;text-align: center">Promedio</th>';
                html += '    </tr>';
                html += '   <thead>';
                html += '<tbody>';
                if (vreg.length > 0) {
                    vtotalCcargo = vreg.length;
                    var x = 0;
                    for (x = 0; x < vreg.length; x++) {
                        var campo = vreg[x].split('-');
                        html += '   <tr>';
                        html += '   <td align="center">' + fila + '</td>';
                        html += '   <td align="left">' + campo[0] + '</td>';
                        html += '   <td align="center">' + campo[1] + '</td>';
                        html += '   </tr>';
                        fila++;
                    }

                } else {
                    html += '   <tr>';
                    html += '   <td colspan="3"><CENTER>NO SE ENCONTRARON CURSOS A CARGO.</CENTER></td>';
                    html += '   </tr>';
                }
                html += '</tbody>';
                html += '</table>';
                $("#viewGridCursos").html(html);
                //alert(data['arrBody'][0].toUpperCase());
                /* $.each(data, function (i, item) {                   
                 });*/
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });
    }

    function js_cargarDocumentos(vId, objchk, objobs) {
        var parametros = {
            vId: vId
        };
        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_alumnos/getDatosDocumentos",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
            },
            success: function (dataJson) {
                if (dataJson.length > 0) {
                    for (x = 0; x < dataJson.length; x++) {
                        for (i = 1; i <= 6; i++) {
                            if ($("#" + objchk + i).val() == dataJson[x].IDDOCU) {
                                $("#" + objchk + i).attr('checked', true);
                            }
                        }
                    }
                    // imprimimos la observacion
                    $("#" + objobs).val(dataJson[0].OBSERVACION);
                }
            }
        });
    }

    function js_cargaPagos(vId) {
        var html = "";
        var token = $('#token').val();
        console.log("Token :" + token);
        $.ajax({
            type: "POST",
            //dataType: "jsonp",
            dataType: "json",
            //jsonp: 'callback',
            // url: "http://www.marianista.pe/getpagos/getpagos.php",
            url: "<?= BASE_URL ?>sga_matricula/getpagos",
            //data: "id=" + vIdAlu + "&vMes=" + vMes + "&vAcc=1",
            data: "vid=" + vId + "&token=" + token,
            success: function (dataJson) {
                $("#resultados_ajax").html("");
                var data = dataJson['data'];
                console.log("Data : " + data);
                var totalPago = 0;
                if (data.length > 0) {
                    // vtotalPagos = data.length;
                    console.log("vtotalPagos2 = " + vtotalPagos);
                    var linea = 1;
                    html += '<table class="table table-striped table-bordered" id="xxxxx" style="width: 100%">';
                    html += '<thead>';
                    html += '   <tr class="tableheader">';
                    html += ' <th style="width:10%;text-align:center;">#</th>';
                    html += ' <th style="width:20%;text-align:center;">Fecha Pago</th>';
                    // html += ' <th>Periodo</th>';
                    html += ' <th style="width:40%;text-align:center;">Concepto de Pago</th>';
                    html += ' <th style="width:15%;text-align:center;">Monto</th>';
                    html += ' <th style="width:15%;text-align:center;">Pagado</th>';
                    html += ' </tr>';
                    html += ' </thead>';
                    html += ' <tbody>';
                    $.each(data, function (i, item) {
                        var vfecha = $.trim(item.fecmod);
                        if (vfecha != '') {
                            vfecha = vfecha.split(" ");
                            vfecha = vfecha[0];
                            totalPago++;
                        } else {
                            vfecha = '-';
                        }
                        var vcolorfila = ((vfecha == '-') ? 'color:red;' : '');
                        html += '<tr  style="' + vcolorfila + '">';
                        html += ' <td style="width:10%;text-align:center;">' + linea + '</td>';
                        html += ' <td style="width:20%;text-align:center;">' + ((vfecha != "-") ? fechaFormat(vfecha) : vfecha) + '</td>';
                        //  html += ' <td>' + +'</td>';
                        html += ' <td  style="width:40%;text-align:left;">' + item.condes + ' - ' + item.mesdes.toUpperCase() + '</td>';
                        html += ' <td style="width:15%;text-align:center;">S/. ' + item.montoini + '</td>';
                        html += ' <td style="width:15%;text-align:center;font-weight:bold">S/. ' + item.montocob + '</td>';
                        html += ' </tr>';
                        linea++;
                    });
                    vtotalPagos = totalPago;
                    console.log("vtotalPagos2 = " + vtotalPagos);
                    html += '</tbody></table>';
                    $("#divListaPagos").html(html);
                    // $('#table_pagos').dataTable({searching: false, paging: false});
                } else {
                    $("#divListaPagos").html("<center>NO SE ENCONTRARON PAGOS REALIZADOS.</center>");
                }
            }
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

    function cargaGrado() {
        $("#cb_grado").empty();
        $("#cb_grado").append('<option value="">Cargando....</option>');
        $('#cb_grado').attr("disabled", false);

        $("#cb_aula").empty();
        $("#cb_aula").append('<option value="">NINGUNO</option>');
        $('#cb_aula').attr("disabled", true);

        $.getJSON('<?= BASE_URL ?>sga_alumnos/getGrado/' + $("#cb_nivel").val(),
                function (json) {
                    $("#cb_grado").empty();
                    $("#cb_grado").append('<option value="">:: Seleccione ::</option>');
                    $.each(json, function (id, value) {
                        $("#cb_grado").append('<option value="' + id + '">' + value + '</option>');
                    });
                });
    }

    function cargaAula() {
        $("#cb_aula").empty();
        $("#cb_aula").append('<option value="">Cargando....</option>');
        $('#cb_aula').attr("disabled", false);
        $.getJSON('<?= BASE_URL ?>sga_alumnos/getSeccion/' + $("#cb_nivel").val() + '/' + $("#cb_grado").val() + '/1',
                function (json) {
                    $("#cb_aula").empty();
                    $("#cb_aula").append('<option value="">:: Seleccione ::</option>');
                    $.each(json, function (id, value) {
                        $("#cb_aula").append('<option value="' + id + '">' + value + '</option>');
                    });
                });
    }

</script>