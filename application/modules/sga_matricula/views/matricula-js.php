<script>
    var vtotalCcargo = 0;
    var vtotalPagos = 0;
    var gridTable;
    var numeroTab = 0;

    $(document).ready(function () {
        //==================== Cargamos los registros ==============
        js_listar('');
        //========================================================
        mayuscula("input[type=text]");
        mayuscula("textarea");

        $('[data-toggle="tooltip"]').tooltip();

        jalar_LocalStorage();
        $("#btnMemoriaPapaPaste").click(function ()
        {
            var dpadre = JSON.parse(localStorage.getItem("datosPadre"));
            console.log("datos : " + dpadre)
            $("#dnipater").val(dpadre.dni);
            $("#padparentesco").val(dpadre.parentesco);
            $("#tipodocpad").val(dpadre.dnitipo);
            $("#padfecnac").val(dpadre.fechanac);
            $("#padpater").val(dpadre.paterno);
            $("#padmater").val(dpadre.materno);
            $("#padnom").val(dpadre.nombres);
            $("#paddireccion").val(dpadre.direccion);
            $("#pademail").val(dpadre.email);
            $("#padtelefono").val(dpadre.telefono);
            $("#padcelu").val(dpadre.celular);
            $("#btnMemoriaPapaPaste").hide();
            $("#btnMemoriaPapaCopy").show();
            localStorage.removeItem("datosPadre");
        });

        $("#btnMemoriaPapaCopy").click(function ()
        {
            /* if (typeof (Storage) !== "undefined") {
             console.log("Navegador no soporta.")
             } else {*/
            var objeto = {
                'dni': $("#dnipater").val(),
                'parentesco': $("#padparentesco").val(),
                'dnitipo': $("#tipodocpad").val(),
                'fechanac': $("#padfecnac").val(),
                'paterno': $("#padpater").val(),
                'materno': $("#padmater").val(),
                'nombres': $("#padnom").val(),
                'direccion': $("#paddireccion").val(),
                'email': $("#pademail").val(),
                'telefono': $("#padtelefono").val(),
                'celular': $("#padcelu").val()
            };
            localStorage.setItem("datosPadre", JSON.stringify(objeto));
            $("#btnMemoriaPapaCopy").hide();
            /* }*/
        });

        $("#btnMemoriaMamaPaste").click(function ()
        {
            var dmadre = JSON.parse(localStorage.getItem("datosMadre"));
            console.log("datos : " + dmadre)
            $("#dnimater").val(dmadre.dni);
            $("#madparentesco").val(dmadre.parentesco);
            $("#tipodocmad").val(dmadre.dnitipo);
            $("#madfecnac").val(dmadre.fechanac);
            $("#madpater").val(dmadre.paterno);
            $("#madmater").val(dmadre.materno);
            $("#madnom").val(dmadre.nombres);
            $("#maddireccion").val(dmadre.direccion);
            $("#mademail").val(dmadre.email);
            $("#madtelefono").val(dmadre.telefono);
            $("#madcelu").val(dmadre.celular);
            $("#btnMemoriaMamaPaste").hide();
            $("#btnMemoriaMamaCopy").show();
            localStorage.removeItem("datosMadre");
        });

        $("#btnMemoriaMamaCopy").click(function ()
        {
            /* if (typeof (Storage) !== "undefined") {
             console.log("Navegador no soporta.")
             } else {*/
            var objeto = {
                'dni': $("#dnimater").val(),
                'parentesco': $("#madparentesco").val(),
                'dnitipo': $("#tipodocmad").val(),
                'fechanac': $("#madfecnac").val(),
                'paterno': $("#madpater").val(),
                'materno': $("#madmater").val(),
                'nombres': $("#madnom").val(),
                'direccion': $("#maddireccion").val(),
                'email': $("#mademail").val(),
                'telefono': $("#madtelefono").val(),
                'celular': $("#madcelu").val()
            };
            localStorage.setItem("datosMadre", JSON.stringify(objeto));
            $("#btnMemoriaMamaCopy").hide();
            /* }*/
        });

        $("#cbfiltro").change(function ()
        {
            var tipo = $(this).val();
            if (tipo == 1) {
                $("#txtsearch").attr('maxlength', '8');
                $("#txtsearch").attr('placeholder', 'Ingrese el DNI del Alumno a Buscar y luego presione (ENTER)');
                $("#txtsearch").focus();
            } else if (tipo == 2) {
                $("#txtsearch").attr('maxlength', '30');
                $("#txtsearch").attr('placeholder', 'Ingrese el Apellido del Alumno a Buscar y luego presione (ENTER)');
                $("#txtsearch").focus();
            } else {
                $("#txtsearch").attr('maxlength', '10');
                $("#txtsearch").attr('placeholder', 'Seleccione el Filtro');
            }
        });
        // ==================== Configuracion de los Wizzard ===========================
        var btnFinish = $('<button id="btnSaveMatricula"></button>').text('Grabar Datos')
                .addClass('btn btn-info')
                .on('click', function () {
                    if (!$(this).hasClass('disabled')) {
                        var elmForm = $("#myForm");
                        if (elmForm) {
                            elmForm.validator('validate');
                            var elmErr = elmForm.find('.has-error');
                            if (elmErr && elmErr.length > 0) {
                                alert('EXISTEN CAMPOS INCOMPLETOS EN EL FORMULARIO DE MATRICULA.');
                                return false;
                            } else {
                                //alert('Perfecto, Se registrara los Datos');								
                                //elmForm.submit();
                                //return false;	
                                $('#totalcomp').attr("disabled",false); // habilitamos para que se envie por post
                                var Formdata = elmForm.serialize();
                                grabaDatosMatricula(Formdata);
                                return false;
                            }
                        }
                    }
                });

        var btnCancel = $('<button></button>').text('Limpiar')
                .addClass('btn btn-danger')
                .on('click', function () {
                    // $('#smartwizard').smartWizard("reset");
                    $('#myForm div#form-step-' + numeroTab).find("input, textarea, select").val("");
                });



        $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
            console.log(stepDirection);
            if (stepDirection == 'forward')
                numeroTab = stepNumber + 1;
            else
                numeroTab = stepNumber - 1;
            console.log("tab : " + numeroTab);
            var elmForm = $("#form-step-" + stepNumber);
            //alert("stepNumber : "+stepNumber+" stepDirection : "+stepDirection);
            // stepDirection === 'forward' :- this condition allows to do the form validation
            // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
            if (stepDirection === 'forward' && elmForm) {
                elmForm.validator('validate');
                var elmErr = elmForm.children('.has-error');
                if (elmErr && elmErr.length > 0) {
                    // Form validation failed
                    return false;
                }
            }
            return true;
        });

        $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
            // Enable finish button only on last step
            if (stepNumber == 3) {
                $('.btn-finish').removeClass('disabled');
            } else {
                $('.btn-finish').addClass('disabled');
            }
        });

        // Smart Wizard
        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'arrows',
            transitionEffect: 'slide',
            toolbarSettings: {toolbarPosition: 'bottom',
                toolbarExtraButtons: [btnFinish, btnCancel]
            },
            anchorSettings: {
                markDoneStep: true, // add done css
                markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
                enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
            }
        });

        //$('#smartwizard').smartWizard({hiddenSteps: [2,3]});

        // =========================================================

        $('body').on('click', '#list-group li', function () {
            fn_quitar();
            //alert($(this).attr('value'))
            var aula = $(this).attr('value');
            var auladsc = $(this).attr('valaula');
            var parte = aula.split("*");
            $("#aulacod").val(parte[0]);
            $("#hdnemo").val(parte[1]);
            $("#haula").val(auladsc);
            $(this).addClass("marca");
        });

        $("#btnImpConst").click(function ()
        {
            var codDocumentos = "";
            var contChk = 0;
            $('input[name="chkPrint1[]"]:checked').each(function () {
                contChk++;
            });
            if (contChk === 0) {
                alert("PARA PODER IMPRIMIR DEBE DE SELECCIONAR CON QUE DATOS SE IMPRIMIRA LA CONSTANCIA.");
                return false;
            }

            $('input[name="chkDocumentos[]"]').each(function () {
                if ($(this).is(':checked')) {

                } else {
                    codDocumentos += $(this).attr('valordocu') + "*";
                }
            });
            console.log("Documentos : " + codDocumentos);
            if (codDocumentos != '') {
                codDocumentos = codDocumentos.substring(0, codDocumentos.length - 1);
            }

            var dni = $("#dni").val();
            var apa = $("#apepat").val();
            var ama = $("#apemat").val();
            var anom = $("#nombres").val();
            var vnivel = $('select[name="cb_nivel"] option:selected').text();
            var vgrado = $('#cb_grado').val();

            if (dni == "" || apa == "" || ama == "" || anom == "") {
                alert("PARA PODER IMPRIMIR EL CONTRATO DE SERVICIO\nDEBE DE INGRESAR LOS DATOS DEL ALUMNO.");
                return false;
            }

            if ($("#cb_nivel").val() == "" || $("#cb_grado").val() == "") {
                alert("PARA PODER IMPRIMIR EL CONTRATO DE SERVICIO\nDEBE DE SELECCIONAR EL NIVEL Y GRADO.");
                return false;
            }

            if ($("#chkPrint4").is(':checked')) {
                if ($("#dnipater").val() != '') {
                    var vtipo = 1;
                    var dnip = $("#dnipater").val();
                    var nomcompp = $("#padpater").val() + " " + $("#padmater").val() + ", " + $("#padnom").val();
                    if ($("#paddireccion").val() != '') {
                        var vdireccion = $("#paddireccion").val();
                    } else {
                        var vdireccion = " *SIN DIRECCION* ";
                    }
                } else {
                    alert("DEBE DE INGRESAR LOS DATOS DEL PADRE.");
                    return false;
                }
            }
            if ($("#chkPrint5").is(':checked')) {
                if ($("#dnimater").val() != '') {
                    var vtipo = 2;
                    var dnip = $("#dnimater").val();
                    var nomcompp = $("#madpater").val() + " " + $("#madmater").val() + ", " + $("#madnom").val();
                    if ($("#maddireccion").val() != '') {
                        var vdireccion = $("#maddireccion").val();
                    } else {
                        var vdireccion = " *SIN DIRECCION* ";
                    }
                } else {
                    alert("DEBE DE INGRESAR LOS DATOS DE LA MADRE.");
                    return false;
                }
            }
            if ($("#chkPrint6").is(':checked')) {
                if ($("#dniapo").val() != '') {
                    var vtipo = 3;
                    var dnip = $("#dniapo").val();
                    var nomcompp = $("#apopater").val() + " " + $("#apomater").val() + ", " + $("#aponom").val();
                    if ($("#apodireccion").val() != '') {
                        var vdireccion = $("#apodireccion").val();
                    } else {
                        var vdireccion = " *SIN DIRECCION* ";
                    }
                } else {
                    alert("DEBE DE INGRESAR LOS DATOS DEL APODERADO.");
                    return false;
                }
            }
            $('#hdocumentos').val(codDocumentos);
            $('#htxtdsc').val($('#txtdsc').val());
            $('#htipo').val(vtipo);
            $("#hdnip").val(dnip);
            $("#hnomcomp").val(nomcompp);
            $("#hdnia").val(dni);
            $("#hnomcompa").val(anom + ", " + apa + " " + ama);
            $("#hnivel").val(vnivel);
            $("#hgrado").val(vgrado);
            $("#hdireccion").val(vdireccion);
            $("#formPrintServicio").attr("action", "<?= BASE_URL ?>sga_matricula/printConstancia/")
            $("#formPrintServicio").submit();

        });

        $("#btnImpServ").click(function ()
        {
            var codDocumentos = "";
            var contChk = 0;
            $('input[name="chkPrint[]"]:checked').each(function () {
                contChk++;
            });
            if (contChk === 0) {
                alert("PARA PODER IMPRIMIR DEBE DE SELECCIONAR CON QUE DATOS SE IMPRIMIRA EL CONTRATO.");
                return false;
            }

            if ($("#accion").val() === 'M') {
                if ($.trim($("#aulacod").val()) === "") {
                    alert("SELECCIONE EL AULA.");
                    return false;
                }
            }

            $('input[name="chkDocumentos[]"]').each(function () {
                if ($(this).is(':checked')) {
                    codDocumentos += $(this).attr('valordocu') + "|1*";
                } else {
                    codDocumentos += $(this).attr('valordocu') + "|0*";
                }
            });
            console.log("Documentos : " + codDocumentos);
            if (codDocumentos != '') {
                codDocumentos = codDocumentos.substring(0, codDocumentos.length - 1);
            }

            var dni = $("#dni").val();
            var apa = $("#apepat").val();
            var ama = $("#apemat").val();
            var anom = $("#nombres").val();
            var vnivel = $('select[name="cb_nivel"] option:selected').text();
            var vgrado = $('select[name="cb_grado"] option:selected').text();

            if (dni == "" || apa == "" || ama == "" || anom == "") {
                alert("PARA PODER IMPRIMIR EL CONTRATO DE SERVICIO\nDEBE DE INGRESAR LOS DATOS DEL ALUMNO.");
                return false;
            }

            /* if ($("#dnipater").val() == "" && $("#dnimater").val() == "") {
             alert("PARA PODER IMPRIMIR EL CONTRATO DE SERVICIO\nDEBE DE INGRESAR LOS DATOS DEL PADRE Ó MADRE.");
             return false;
             }*/

            if ($("#cb_nivel").val() == "" || $("#cb_grado").val() == "") {
                alert("PARA PODER IMPRIMIR EL CONTRATO DE SERVICIO\nDEBE DE SELECCIONAR EL NIVEL Y GRADO.");
                return false;
            }

            if ($("#chkPrint1").is(':checked')) {
                if ($("#dnipater").val() != '') {
                    var dnip = $("#dnipater").val();
                    var nomcompp = $("#padpater").val() + " " + $("#padmater").val() + ", " + $("#padnom").val();
                    if ($("#paddireccion").val() != '') {
                        var vdireccion = $("#paddireccion").val();
                    } else {
                        var vdireccion = " *SIN DIRECCION* ";
                    }
                } else {
                    alert("DEBE DE INGRESAR LOS DATOS DEL PADRE.");
                    return false;
                }
            }
            if ($("#chkPrint2").is(':checked')) {
                if ($("#dnimater").val() != '') {
                    var dnip = $("#dnimater").val();
                    var nomcompp = $("#madpater").val() + " " + $("#madmater").val() + ", " + $("#madnom").val();
                    if ($("#maddireccion").val() != '') {
                        var vdireccion = $("#maddireccion").val();
                    } else {
                        var vdireccion = " *SIN DIRECCION* ";
                    }
                } else {
                    alert("DEBE DE INGRESAR LOS DATOS DE LA MADRE.");
                    return false;
                }
            }
            if ($("#chkPrint3").is(':checked')) {
                if ($("#dniapo").val() != '') {
                    var dnip = $("#dniapo").val();
                    var nomcompp = $("#apopater").val() + " " + $("#apomater").val() + ", " + $("#aponom").val();
                    if ($("#apodireccion").val() != '') {
                        var vdireccion = $("#apodireccion").val();
                    } else {
                        var vdireccion = " *SIN DIRECCION* ";
                    }
                } else {
                    alert("DEBE DE INGRESAR LOS DATOS DEL APODERADO.");
                    return false;
                }
            }

            $('#hdocumentos').val(codDocumentos);
            $('#htxtdsc').val($('#txtdsc').val());
            $("#hdnip").val(dnip);
            $("#hnomcomp").val(nomcompp);
            $("#hdnia").val(dni);
            $("#hnomcompa").val(apa + " " + ama + ", " + anom);
            $("#hnivel").val(vnivel);
            $("#hgrado").val(vgrado);
            $("#hdireccion").val(vdireccion);
            $("#formPrintServicio").attr("action", "<?= BASE_URL ?>sga_matricula/printServicio/")
            $("#formPrintServicio").submit();
        });

        $("#txtsearch").keypress(function (event) {
            if (event.keyCode == 13) {
                //alert($("#txtsearch").val());
                if ($('#cbfiltro').val() != '') {
                    $('#viewMatricula').DataTable().destroy();
                    js_listar('');
                } else {
                    alert("SELECCIONE  EL TIPO DE FILTRO.");
                }
            }
            //alert($(this).attr('value'));
        });

        $("#btnPopupMatricula").click(function ()
        {
            jalar_LocalStorage();
            var ano_ctual = (new Date).getFullYear();
            var ano_cmb = $('#cbano').val();
            $("#dni").attr("readonly", false);
            if (ano_cmb < ano_ctual) {
                alert("NO PUEDE REALIZAR MATRICULAS DE AÑOS YA CERRADOS.");
                return false;
            }
            numeroTab = 0;
            // $("#smartwizard ul:eq(0)").append('<li><a href="#step-7" >Paso 7<br /><small><b>PAGO MATRICULA</b></small></a></li>');  

            $('#smartwizard').smartWizard('reset');
            $('#myForm')[0].reset();
            $("#lblaulanterior").hide();
            $('#accion').val("N");
            $('#htipoalu').val("N");
            $('#list-group').html("");
            $('#lstMarcacurso').html("");
            $('#lstMarcadeuda').html("");
            $('#lstMarcacurso').html("NO TIENE NINGUN CURSO A CARGO.<span class='label label-success' style='float: right'>OK</span>");
            $('#lstMarcadeuda').html("NO TIENE DEUDAS PENDIENTES.<span class='label label-success' style='float: right'>OK</span>");
            //$('#flgerror').val("N");
            $('#myModal').modal('show');
        });

        $("#btnMatricular").click(function ()
        {
            var ano_ctual = (new Date).getFullYear();
            var ano_cmb = $('#cbano').val();

            if (ano_cmb < ano_ctual) {
                alert("NO PUEDE REALIZAR MATRICULAS DE AÑOS YA CERRADOS.");
                return false;
            }
            $('#frmAlumno')[0].reset();
            //$('#accion').val("nuevo");
            $('#viewAlumnosFiltro').DataTable().destroy();
            $("#viewAlumnosFiltro").html("");
            var html = '<thead>';
            html += '< tr class = "tableheader" > ';
            html += '<th style="width: 10%;text-align: center">DNI</th>';
            html += '<th style="width: 60%;text-align: center">Apellidos y Nombres</th>      ';
            html += '<th style="width: 10%;text-align: center">Estado</th>';
            html += '<th style="width: 10%;text-align: center">Aula </th> ';
            html += ' <th style="width: 10%;text-align: center">Config.</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            html += '</tbody> ';
            $("#viewAlumnosFiltro").html(html);
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
            $('#modalAlumnoFiltro').modal('show');
            $('.modal-title').text('MATRICULA - ' + $("#cbano").val()); // Set title to Bootstrap modal title       
            $('#txtbuscar').focus();
            $('#hanio').val($("#cbano").val());
            js_filtrar(); // llamamos al autofiltrado
        });


        $("#btnRefresh").click(function ()
        {
            if ($('#cbfiltro').val() != '') {
                $('#viewMatricula').DataTable().destroy();
                js_listar('');
            } else {
                alert("SELECCIONE  EL TIPO DE FILTRO.");
            }
        });

        $("#btnMostrarAll").click(function ()
        {
            $('#cbfiltro').val('');
            $('#txtsearch').val('');
            $("#txtsearch").attr('maxlength', '10');
            $("#txtsearch").attr('placeholder', 'Seleccione el Filtro');
            js_listar('');
        });

        $("#cbano").change(function ()
        {
            $('#cbfiltro').val('');
            $('#txtsearch').val('');
            $("#txtsearch").attr('maxlength', '10');
            $("#txtsearch").attr('placeholder', 'Seleccione el Filtro');
            js_listar('');
        });
    });

    function fn_quitar() {
        $("#list-group li").each(function () {
            $(this).removeClass("marca");
        });
    }

    function grabaDatosMatricula(varData) {
        var total = $("#totalcomp").val();

        if ($("#accion").val() === 'M' || $("#accion").val() === 'N') {
            if ($.trim($("#aulacod").val()) === "") {
                alert("SELECCIONE EL AULA A MATRICULAR.");
                return false;
            }

            if (!$('#chkExoneraPago').is(':checked')) {
                console.log("NO ESTA CHECKEADO");
            } else {
                console.log("ESTA CHECKEADO");
            }

            console.log("Checked : " + $('#chkExoneraPago').is(':checked'));
            if ($('#chkExoneraPago').is(':checked')) {
                // No valida Nada.
                console.log("No Valida Nada ..........");
                if ($.trim($("#txtobsExoneracion").val()) == "") {
                    alert("INGRESE EL MOTIVO QUIEN AUTORIZA LA ANULACIÓN DE LA MATRÍCULA. ");
                    return false;
                }
            }
           /* } else {*/
                // ==== Validamos si el monto es diferente a 300 (Matricula)
                console.log("Monto Modificado :" + parseInt(total));
                if (parseInt(total) < 290) {
                    if ($.trim($("#txtobsExoneracion").val()) == "") {
                        alert("INGRESE EL MOTIVO QUIEN AUTORIZA LA MODIFICACION DE LA MATRICULA. ");
                        return false;
                    }
                }
                // ==== Validamos que no deje el monto vacio 
                if (($.trim(total) === "" || total === '0' || total === 0) && !$('#chkExoneraPago').is(':checked') ) {
                    alert("EL MONTO DE LA MATRICULA TIENE QUE SER > 0 ");
                    return false;
                }

                if ($.trim($("#numcomprobante").val()) === "") {
                    alert("SELECCIONE EL TIPO DE COMPROBANTE.");
                    return false;
                }

                if ($.trim($("#cmbResponsablePago").val()) === "") {
                    alert("SELECCIONE EL RESPONSABLE DE LOS PAGOS.");
                    return false;
                }

          /*  }*/
            var tipoMedioPago = $("#idmedio").val();
            if (tipoMedioPago != '') {
                if (tipoMedioPago == '2' || tipoMedioPago == '3' || tipoMedioPago == '4') {
                    if ($("#voucher").val() == "") {
                        alert("INGRESE EL NUMERO DE VOUCHER O COMPROBANTE DE PAGO.");
                        $("#voucher").focus();
                        return false;
                    }  
                }
            }
            var contChk = 0;
            $('input[name="chkDocumentos[]"]:checked').each(function () {
                contChk++;
            });
            
            /*if (contChk < 3) {
             alert("DEBE DE SELECCIONAR COMO MINIMO 3 DOCUMENTOS ENTREGADOS");
             return;
             }*/
        }
        
        // SI ES ALUMNO ANTIGUO VALIDA PAGOS Y CURSOS
        if ((vtotalCcargo > 0 || vtotalPagos > 0) && $("#accion").val() === 'M') {
            var msg = window.confirm("EL ALUMNO TIENE CURSOS A CARGO Ó DEUDAS PENDIENTES\nESTA SEGURO DE MATRICULARLO ?.");
        } else {
            // SI ES ALUMNO NUEVO NO VALIDA NADA
            var msg = true;
        }
        if (msg) {
            $('#btnSaveMatricula').attr("disabled", true);
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>sga_matricula/saveMatricula2",
                data: varData,
                dataType: "json",
                success: function (data) {
                    console.log("Proceso : " + JSON.stringify(data));
                    if (data.flg === '0') {
                        //alert(data.msg);
                        swal({
                            type: 'success',
                            title: 'Aviso',
                            text: data.msg
                        });
                        console.log(data.error);
                        if (($("#accion").val() === 'M' || $("#accion").val() === 'N') /*&& !$('#chkExoneraPago').is(':checked')*/) {
                            // =============================
                            $("#htxtsalon").val(data.vnemo);
                            $("#htxtalumno").val(data.valucod);
                            $("#rbdTipo").val(data.vtipocomp);
                            $("#htxtnumrecibo").val(data.vnum);
                            $("#formPrint").attr("action", "<?= BASE_URL ?>swp_pagos_test/printTicket/");
                            $("#formPrint").submit();
                            // =============================
                        }
                        $('#myModal').modal('hide');
                        $('#btngrabar').attr("disabled", false);
                        gridTable.ajax.reload(null, false);
                    } else {
                        //alert("OCURRIO UN ERROR INTERNO EN EL PROCESO DE MATRICULA\nFAVOR DE COMUNICARSE CON EL ADMINISTRADOR.");
                        swal({
                            type: 'error',
                            title: 'Error',
                            text: 'OCURRIO UN ERROR INTERNO EN EL PROCESO DE MATRICULA, FAVOR DE COMUNICARSE CON EL ADMINISTRADOR.'
                        });
                        console.log(data.error);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    //alert('Error interno, Comuniquese con el Administrador : \nE-mail : info@sistemas-dev.com');
                    swal({
                        type: 'warning',
                        title: 'Error Interno',
                        text: 'Comuniquese con el Administrador al correo : info@sistemas-dev.com.'
                    });
                    $('#btngrabar').attr("disabled", false);
                }
            });
        }
    }

    function validarDni()
    {
        var accion = $('#accion').val();
        console.log("accion activa.");
        if (accion === 'N') {
            var vdni = $("#dni").val();
            console.log(vdni);
            $.ajax({
                url: "<?= BASE_URL ?>sga_matricula/verificaExistenciaAlumno/",
                type: "POST",
                dataType: "json",
                data: {dni: vdni},
                beforeSend: function () {
                    //  $('.loading').show();
                },
                success: function (data) {
                    if (data.flg === 1) {
                        alert(data.msg);
                        $("#dni").focus();
                        return true;
                    }
                },
                complete: function () {
                    //$('.loading').hide();
                }});
        }
    }

    function generar_comprobante(tipo) {

        var vnemo = $("#hdnemo").val();
        console.log("Nemo : " + vnemo);
        if (vnemo == "") {
            alert("SELECCIONE EL AULA A MATRICULAR.");
            $('#chkComprobante1').removeAttr('checked');
            $('#chkComprobante2').removeAttr('checked');
            $('#chkComprobante3').removeAttr('checked');
            return false;
        }
         $('#totalcomp').attr("disabled",false);
         //$('#totalcomp').val("300");
            if($("#cb_nivel").val()=="I"){
                $("#totalcomp").val("370");
            } else {
                $("#totalcomp").val("395");
            }         
        // $('#chkExoneraPago').removeAttr('checked');
         
        $("#lblcomprobante").html("");
        $("#numcomprobante").val("");
        // Llamando al ajax que obtiene el numero de document
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos_test/getDocumento/",
            type: "POST",
            dataType: "json",
            data: {vidnemo: vnemo, vTipo: tipo, flag: 1},
            beforeSend: function () {
                //  $('.loading').show();
            },
            success: function (data) {
                $("#lblcomprobante").html(data['gencod']);
                $("#numcomprobante").val(data['gencod']);
            },
            complete: function () {
                //$('.loading').hide();
            }});
    }

    function js_editarMatricula(vId) {
        numeroTab = 0;

        $('#myForm')[0].reset();
        $('#accion').val("E");
        $('#htipoalu').val("M");
        // Consultamos la Pestaña de Pagos
        //$("#li-Resumen").html("");
        //$("#step-7").hide();
        //$("#smartwizard ul li:eq(6)").remove();
        //$("#smartwizard ul li:eq(6)").attr('style', 'display: none');
        //$("#smartwizardPasos div:eq(6)").attr('style', 'display: none');     
        // Reiniciamos los tabs de los Wizard
        //$('#smartwizard').smartWizard("reset");
        $('#smartwizard').smartWizard("reset");
        $('#smartwizard').smartWizard("stepState", [6], "disable");

        var parametros = {
            vId: vId,
            vflg: 1
        };
        var url = "<?= BASE_URL ?>sga_matricula/getDatosAlumnoMatriculado";
        $.ajax({
            type: "POST",
            url: url,
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                $('#modalAlumnoFiltro').modal('hide');
                $('#myModal').modal('show');
            },
            success: function (dataJson) {
                //console.log("Longitud :" + Object.keys(dataJson).length);
                //console.log("Data :" + JSON.stringify(dataJson));
                if ($('#accion').val() === 'M') { // mostrar aula anterior
                    $("#lblaulanterior").show();
                    $("#aulaant").val(dataJson.NEMODES);
                }
                // ========= DATOS SELECCIONADOS ================
                $("#hdnemo").val(dataJson.NEMO);
                $("#aulacod").val(dataJson.AULACOD);
                $("#haula").val(dataJson.AULADES);
                var codSalon = dataJson.AULACOD + "*" + dataJson.NEMO;
                $("#txtcomentarios").text(dataJson.observacion);
                $("#txtdsc").text(dataJson.obsdocumentos);
                //==================================================

                $("#famcod").val(dataJson.FAMCOD);
                // ====== CARGAMOS DATOS DEL ALUMNO =============                
                $("#alucod").val(dataJson.ALUCOD);
                // $("#haccion").val(vEstado);
                $("#tipodoc").val(dataJson.TIPODOC);
                $("#dni").val(dataJson.DNI);
                //$("#hestado").val(dataJson.ESTADO);
                $("#apepat").val(dataJson.APEPAT);
                $("#aluemail").val(dataJson.ALUEMAIL);
                $("#apemat").val(dataJson.APEMAT);
                $("#nombres").val(dataJson.NOMBRES);
                $("#fecnac").val(dataJson.FECNAC);
                $("#telefono").val(dataJson.TELEFONO);
                $("#procede").val(dataJson.PROCEDE);
                $("#sexo").val(dataJson.SEXO);
                // ===== CARGAMOS DATOS DEL PADRE =============
                $("#tipodocpad").val(dataJson.TIPODOCPAD);
                $("#dnipater").val(dataJson.DNIPATER);
                $("#padfecnac").val(dataJson.PADFECNAC);
                $("#padpater").val(dataJson.PADPATER);
                $("#padmater").val(dataJson.PADMATER);
                $("#padnom").val(dataJson.PADNOM);
                $("#padparentesco").val(dataJson.PADPARENTESCO);
                $("#pademail").val(dataJson.PADEMAIL);
                $("#padtelefono").val(dataJson.PADTELEFONO);
                $("#padcelu").val(dataJson.PADCELU);
                $("#paddireccion").val(dataJson.PADDIRECCION);
                $("#padruc").val(dataJson.PADRUC);
                $("#padrazon").val(dataJson.PADRAZON);
                if (dataJson.FLGPADCORREO === 'S')
                    $("#chkpapa").attr("checked", true);
                // ===== CARGAMOS DATOS DE LA MADRE =============
                $("#tipodocmad").val(dataJson.TIPODOCMAD);
                $("#dnimater").val(dataJson.DNIMATER);
                $("#madfecnac").val(dataJson.MADFECNAC);
                $("#madpater").val(dataJson.MADPATER);
                $("#madmater").val(dataJson.MADMATER);
                $("#madnom").val(dataJson.MADNOM);
                $("#madparentesco").val(dataJson.MADPARENTESCO);
                $("#mademail").val(dataJson.MADEMAIL);
                $("#madtelefono").val(dataJson.MADTELEFONO);
                $("#madcelu").val(dataJson.MADCELU);
                $("#maddireccion").val(dataJson.MADDIRECCION);
                $("#madruc").val(dataJson.MADRUC);
                $("#madrazon").val(dataJson.MADRAZON);
                if (dataJson.FLGMADCORREO === 'S')
                    $("#chkmama").attr("checked", true);
                // ===== CARGAMOS DATOS DE LA APODERADO =============
                $("#tipodocapo").val(dataJson.TIPODOCAPO);
                $("#dniapo").val(dataJson.DNIAPO);
                $("#apofecnac").val(dataJson.APOFECNAC);
                $("#apopater").val(dataJson.APOPATER);
                $("#apomater").val(dataJson.APOMATER);
                $("#aponom").val(dataJson.APONOM);
                $("#apoparentesco").val(dataJson.APOPARENTESCO);
                $("#apoemail").val(dataJson.APOEMAIL);
                $("#apotelefono").val(dataJson.APOTELEFONO);
                $("#apocelu").val(dataJson.APOCELU);
                $("#apodireccion").val(dataJson.APODIRECCION);
                $("#aporuc").val(dataJson.APORUC);
                $("#aporazon").val(dataJson.APORAZON);
                if (dataJson.FLGAPOCORREO === 'S')
                    $("#chkapoderado").attr("checked", true);
                // ===== CARGAMOS DATOS DEL AULA =============
                $("#cb_nivel").val(dataJson.INSTRUCOD);
                // ===== Deshabilitamos los campos llaves =============
                $("#dni").attr("readonly", true);

                cargaGrado();
                setTimeout(function () {
                    $("#cb_grado").val(dataJson.GRADOCOD);
                    cargarListarAulas();
                    setTimeout(function () {
                        // ========= DATOS SELECCIONADOS ================
                        //$("#cb_nivel").attr("disabled", true);
                        //$("#cb_grado").attr("disabled", true);

                        $("#list-group li").each(function () {
                            console.log("Comparando : " + $(this).attr('value') + " === " + codSalon);
                            if ($(this).attr('value') === codSalon) {
                                $(this).addClass("marca");
                            }
                        });
                    }, 1000);
                }, 2000);

                // ===== CARGAR DATOS DEL RESUMEN ==========      
                js_cargarCursoCargoAlumno(vId);
                js_cargaPagosAlumno(vId);
                js_cargarDocumentosAlumno(vId, 'chkapoderado');

            }
        });
    }

    function fn_editarMatricula(vId, vFlag)
    {
        jalar_LocalStorage();
        numeroTab = 0;
        //$("#li-Resumen").html('<a href="#step-7" >Paso 7<br /><small><b>PAGO MATRICULA</b></small></a>');
        //$("#step-7").show();
        $('#smartwizard').smartWizard("reset");
        $('#myForm')[0].reset();
        $('#accion').val("M");
        $('#htipoalu').val("M");
        //$('#flgerror').val("N");
        $('#list-group').html("");
        var parametros = {
            vId: vId, //'20200024' hay que tener todas las ualas cargadas en salon
            vflg: vFlag
        };
        var url = "<?= BASE_URL ?>sga_matricula/getDatosAlumno";
        $.ajax({
            type: "POST",
            url: url,
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                $('#modalAlumnoFiltro').modal('hide');
                $('#myModal').modal('show');
            },
            success: function (dataJson) {
                //console.log("Longitud :" + Object.keys(dataJson).length);
                //console.log("Data :" + JSON.stringify(dataJson));
                $("#dni").attr("readonly", false);
                if ($('#accion').val() === 'M' && vFlag === '0') { // mostrar aula anterior
                    $("#lblaulanterior").show();
                    $("#aulaant").val(dataJson.NEMODES);
                }
                $("#famcod").val(dataJson.FAMCOD);
                // ====== CARGAMOS DATOS DEL ALUMNO =============                
                $("#alucod").val(dataJson.ALUCOD);
                //$("#haula").val(dataJson.AULADES);
                // $("#haccion").val(vEstado);
                $("#tipodoc").val(dataJson.TIPODOC);
                $("#dni").val(dataJson.DNI);
                //$("#hestado").val(dataJson.ESTADO);
                $("#apepat").val(dataJson.APEPAT);
                $("#aluemail").val(dataJson.ALUEMAIL);
                $("#apemat").val(dataJson.APEMAT);
                $("#nombres").val(dataJson.NOMBRES);
                $("#fecnac").val(dataJson.FECNAC);
                $("#telefono").val(dataJson.TELEFONO);
                $("#procede").val(dataJson.PROCEDE);
                $("#sexo").val(dataJson.SEXO);
                // ===== CARGAMOS DATOS DEL PADRE =============
                $("#tipodocpad").val(dataJson.TIPODOCPAD);
                $("#dnipater").val(dataJson.DNIPATER);
                $("#padfecnac").val(dataJson.PADFECNAC);
                $("#padpater").val(dataJson.PADPATER);
                $("#padmater").val(dataJson.PADMATER);
                $("#padnom").val(dataJson.PADNOM);
                $("#padparentesco").val(dataJson.PADPARENTESCO);
                $("#pademail").val(dataJson.PADEMAIL);
                $("#padtelefono").val(dataJson.PADTELEFONO);
                $("#padcelu").val(dataJson.PADCELU);
                $("#paddireccion").val(dataJson.PADDIRECCION);
                $("#padruc").val(dataJson.PADRUC);
                $("#padrazon").val(dataJson.PADRAZON);
                if (dataJson.FLGPADCORREO === 'S')
                    $("#chkpapa").attr("checked", true);
                // ===== CARGAMOS DATOS DE LA MADRE =============
                $("#tipodocmad").val(dataJson.TIPODOCMAD);
                $("#dnimater").val(dataJson.DNIMATER);
                $("#madfecnac").val(dataJson.MADFECNAC);
                $("#madpater").val(dataJson.MADPATER);
                $("#madmater").val(dataJson.MADMATER);
                $("#madnom").val(dataJson.MADNOM);
                $("#madparentesco").val(dataJson.MADPARENTESCO);
                $("#mademail").val(dataJson.MADEMAIL);
                $("#madtelefono").val(dataJson.MADTELEFONO);
                $("#madcelu").val(dataJson.MADCELU);
                $("#maddireccion").val(dataJson.MADDIRECCION);
                $("#madruc").val(dataJson.MADRUC);
                $("#madrazon").val(dataJson.MADRAZON);
                if (dataJson.FLGMADCORREO === 'S')
                    $("#chkmama").attr("checked", true);
                // ===== CARGAMOS DATOS DE LA APODERADO =============
                $("#tipodocapo").val(dataJson.TIPODOCAPO);
                $("#dniapo").val(dataJson.DNIAPO);
                $("#apofecnac").val(dataJson.APOFECNAC);
                $("#apopater").val(dataJson.APOPATER);
                $("#apomater").val(dataJson.APOMATER);
                $("#aponom").val(dataJson.APONOM);
                $("#apoparentesco").val(dataJson.APOPARENTESCO);
                $("#apoemail").val(dataJson.APOEMAIL);
                $("#apotelefono").val(dataJson.APOTELEFONO);
                $("#apocelu").val(dataJson.APOCELU);
                $("#apodireccion").val(dataJson.APODIRECCION);
                $("#aporuc").val(dataJson.APORUC);
                $("#aporazon").val(dataJson.APORAZON);
                if (dataJson.FLGAPOCORREO === 'S')
                    $("#chkapoderado").attr("checked", true);
                // ===== CARGAMOS DATOS DEL AULA =============
                $("#cb_nivel").val(dataJson.INSTRUCODP);
                cargaGrado();
                setTimeout(function () {
                    $("#cb_grado").val(dataJson.GRADOCODP);
                    cargarListarAulas()
                }, 2000);
                // ===== CARGAR DATOS DEL RESUMEN ==========
                js_cargaConducta(dataJson.DNI);
                js_cargarCursoCargoAlumno(vId);
                js_cargaPagosAlumno(vId);
                js_cargarDocumentosAlumno(vId, 'chkapoderado');
            }
        });
    }


    function cambiaResponsable() {
        var resp = $("#cmbResponsablePago").val();
        if (resp != '') {
            $("#nomcliente").val("");
            if (resp == 'P') {
                if ($("#dnipater").val() != "") {
                    $("#nomcliente").val($("#dnipater").val() + " - " + $("#padpater").val() + " " + $("#padmater").val() + ", " + $("#padnom").val());
                } else {
                    $("#nomcliente").val("NO TIENE INFORMACION.");
                }
            }
            if (resp == 'M') {
                if ($("#dnimater").val() != "") {
                    $("#nomcliente").val($("#dnimater").val() + " - " + $("#madpater").val() + " " + $("#madmater").val() + ", " + $("#madnom").val());
                } else {
                    $("#nomcliente").val("NO TIENE INFORMACION.");
                }
            }
            if (resp == 'A') {
                if ($("#dniapo").val() != "") {
                    $("#nomcliente").val($("#dniapo").val() + " - " + $("#apopater").val() + " " + $("#apomater").val() + ", " + $("#aponom").val());
                } else {
                    $("#nomcliente").val("NO TIENE INFORMACION.");
                }
            }
        }
    }
    function js_jalarDatos(tipo) {
        if (tipo === 'P') {
            $("#dniapo").val($("#dnipater").val());
            $("#apofecnac").val($("#padfecnac").val());
            $("#apopater").val($("#padpater").val());
            $("#apomater").val($("#padmater").val());
            $("#aponom").val($("#padnom").val());
            $("#apoparentesco").val($("#padparentesco").val());
            $("#apoemail").val($("#pademail").val());
            $("#apotelefono").val($("#padtelefono").val());
            $("#apocelu").val($("#padcelu").val());
            $("#apodireccion").val($("#paddireccion").val());
            $("#aporuc").val($("#padruc").val());
            $("#aporazon").val($("#padrazon").val());
        }
        if (tipo === 'M') {
            $("#dniapo").val($("#dnimater").val());
            $("#apofecnac").val($("#madfecnac").val());
            $("#apopater").val($("#madpater").val());
            $("#apomater").val($("#madmater").val());
            $("#aponom").val($("#madnom").val());
            $("#apoparentesco").val($("#madparentesco").val());
            $("#apoemail").val($("#mademail").val());
            $("#apotelefono").val($("#madtelefono").val());
            $("#apocelu").val($("#madcelu").val());
            $("#apodireccion").val($("#maddireccion").val());
            $("#aporuc").val($("#madruc").val());
            $("#aporazon").val($("#madrazon").val());
        }
    }
    function cargaListaGrado() {
        $("#cb_grado").empty();
        $("#cb_grado").append('<option value="">Cargando....</option>');
        $('#cb_grado').attr("disabled", false);
        $("#list-group").empty();
        /*$("#cb_aula").empty();
         $("#cb_aula").append('<option value="">NINGUNO</option>');
         $('#cb_aula').attr("disabled", true);*/
        // -- Se agrega desde el 2022
        if($("#cb_nivel").val()!=""){
            if($("#cb_nivel").val()=="I"){
                $("#totalcomp").val("370");
            } else {
                $("#totalcomp").val("395");
            }
        }
        
        $.getJSON('<?= BASE_URL ?>sga_alumnos/getGrado/' + $("#cb_nivel").val(),
                function (json) {
                    $("#cb_grado").empty();
                    $("#cb_grado").append('<option value="">:: Seleccione ::</option>');
                    $.each(json, function (id, value) {
                        $("#cb_grado").append('<option value="' + id + '">' + value + '</option>');
                    });
                });
    }

    function cargarListarAulas() {
        $("#list-group").empty();
        $("#list-group").html('<center><b>Cargando...</b></center>');
        $.getJSON('<?= BASE_URL ?>sga_alumnos/getSeccionMatricula/' + $("#cb_nivel").val() + '/' + $("#cb_grado").val() + '/1',
                function (json) {
                    var lista = "";
                    $("#list-group").empty();
                    $.each(json, function (id, value) {
                        var parte = value.split("*");
                        lista += '<li class="list-group-item d-flex justify-content-between align-items-center" valaula="' + parte[1] + '" value="' + id + "*" + parte[0] + '" >';
                        lista += parte[0] + ' - ' + parte[1];
                        if (parte[2] == parte[3]) {
                            lista += ' <span class="label label-danger" style="float: right;font-size:12px"><b>EL AULA ESTA LLENA</b></span>';
                        } else {
                            lista += ' <span class="label label-danger" style="float: right;font-size:12px"><b>Límite (' + parte[3] + ')</b></span>';
                            lista += ' <span class="label label-warning" style="float: right;font-size:12px"><b>Matriculados (' + parte[2] + ')</b></span>';
                        }
                        lista += ' </li>';
                    });
                    $("#list-group").html(lista);
                });
    }


    function js_cargarCursoCargoAlumno(vId) {
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
                console.log(JSON.stringify(data));
                console.log(JSON.stringify(data['arrBody']));
                console.log("Total data Cursos : " + data['arrBody'].length);

                if (data['arrBody'].length > 0) {
                    var vreg = data['arrBody'];
                    var html = '';
                    var fila = 1;
                    vtotalCcargo = vreg.length;

                    $("#lstMarcacurso").html('');
                    html += '<table class="table table-striped table-bordered"    id="xxx" style="width: 100%">';
                    html += '   <thead>';
                    html += '   <tr class="tableheader">';
                    html += '   <th style="width: 10%;text-align: center">Codigo</th>';
                    html += '   <th style="width: 35%;text-align: center">Descripcion Curso</th>';
                    html += '   <th style="width: 10%;text-align: center">Promedio</th>';
                    html += '    </tr>';
                    html += '   <thead>';
                    html += '<tbody>';

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

                    /*else {
                     html += '   <tr>';
                     html += '   <td colspan="3"><CENTER>NO SE ENCONTRARON CURSOS A CARGO.</CENTER></td>';
                     html += '   </tr>';
                     }*/
                    html += '</tbody>';
                    html += '</table>';
                    $("#lstMarcacurso").html(html);
                } else {
                    $("#lstMarcacurso").html("NO SE ENCONTRARON CURSOS A CARGO.");
                }
                console.log("vtotalCcargo = " + vtotalCcargo);
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

    function js_cargarDocumentosAlumno(vId, objchk) {
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
                        for (i = 1; i <= 7; i++) {
                            if ($("#" + objchk + "_" + i).val() == dataJson[x].IDDOCU) {
                                $("#" + objchk + "_" + i).attr('checked', true);
                            }
                        }
                    }
                }
            }
        });
    }

    function js_cargaConducta(vDni) {
        var parametros = {
            vDni: vDni
        };
        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_matricula/verificaConducta",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
            },
            success: function (data) {
                if (data.length > 0) {
                    var html = "";
                    var data = data[0];  
                   var txtRojo =  (data.rendimiento==="REPITE") ? "<span style='color:red; font-weight:bold;font-size;16px;'>[ :::: "+data.rendimiento+" :::: ]</span>" : data.rendimiento;
                    html += '<div style="text-align:left;font-size:15px;margin-left:15px" ><ul>';
                    html += '<li><b>Comportamiento : </b>' + data.comportamiento + '</li>';
                    html += '<li><b>Rendimiento Académico : </b>' + txtRojo + '</li>';
                    html += '<li><b>Padre Situación : </b>' + data.padre + '</li>';
                    html += '<li><b>Lista de Utiles : </b>' + data.utiles + '</li>';
                    html += '<li><b>Matrícula : </b>' + data.matricula + '</li>';
                    html += '</ul></div>';
                    swal({
                        type: 'warning',
                        title: 'ADVERTENCIA:',
                        text: '<p style="text-align:left;font-size:15px;">El alumno(a) <b>' + data.alumno + '</b> Tiene el siguiente Informe Pedagógico y Conductual :</p><br>' + html
                    });
                    /*console.log("Alumno : " + data.alumno);
                     console.log("Comportamiento : " + data.comportamiento);
                     console.log("Rendimiento : " + data.rendimiento);
                     console.log("Padre : " + data.padre);
                     console.log("Utiles : " + data.utiles);
                     console.log("Matricula : " + data.matricula);*/
                }
            }
        });
    }

    function js_cargaPagosAlumno(vId) {
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
                //$("#resultados_ajax").html("");
                var data = dataJson['data'];
                // console.log("Data : " + JSON.stringify(data));
                var totalPago = 0;
                if (data.length > 0) {
                    //vtotalPagos = data.length;
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
                        } else {
                            vfecha = '-';
                            totalPago++;
                        }
                        if (parseInt(item.montocob) === 0) {
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
                        }
                    });
                    vtotalPagos = totalPago;
                    console.log("vtotalPagos = " + vtotalPagos);
                    html += '</tbody></table>';
                    $("#lstMarcadeuda").html(html);
                    // $('#table_pagos').dataTable({searching: false, paging: false});
                } else {
                    $("#lstMarcadeuda").html("NO SE ENCONTRARON PAGOS PENDIENTES.");
                }
            }
        });
    }

    function js_exonerar(chk) {
        if (chk === true) {
            /*$('#chkComprobante1').removeAttr('checked');
            $('#chkComprobante2').removeAttr('checked');
            $('#chkComprobante3').removeAttr('checked');
            $('#cmbResponsablePago').val("");
            $('#lblcomprobante').val("");
            $("#numcomprobante").val("");
            $('#nomcliente').val("");*/
            $('#totalcomp').val("0");
            $('#totalcomp').attr("disabled",true);
            //$('#lblcomprobante').html("");
            //$("#numcomprobante").val("");
            $('#txtobsExoneracion').attr("placeholder", "INGRESE EL MOTIVO DE LA EXONERACIÓN DEL PAGO DE LA MATRICULA.");
        } else {
            //$('#lblcomprobante').html("");
            //$("#numcomprobante").val("");
            $('#totalcomp').attr("disabled",false);
           // $('#totalcomp').val("300");
            if($("#cb_nivel").val()=="I"){
                $("#totalcomp").val("370");
            } else {
                $("#totalcomp").val("395");
            }           
            $('#txtobsExoneracion').attr("placeholder", "");
        }
    }
    
    function js_printEtiqueta(vId){
        window.open('<?= BASE_URL ?>sga_matricula/printEtiqueta/'+vId, '_blank');
    }
    
    function js_eliminar(vId, vNombres) {
        var msg = window.confirm("Esta seguro de Eliminar la matricula del Alumno :  \n" + vNombres + " ?");
        if (msg) {
            var arrdata = {
                vId: vId
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_matricula/eliminaMatricula/",
                type: "POST",
                dataType: "json",
                data: arrdata,
                success: function (data)
                {
                    if (data['flg'] == 0) {
                        alert(data['msg']);
                        gridTable.ajax.reload(null, false);
                    } else {
                        alert(data['msg']);
                        console.log("Error : " + data['error']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error interno, Comuniquese con el Administrador : \nE-mail : info@sistemas-dev.com');
                }
            });
        }
    }

    function js_listar(vaula) {
        var vanio = $('#cbano').val();
        var idfiltro = $('#cbfiltro').val();
        var txtsearch = $.trim($('#txtsearch').val());
        //var flagMarca = (($("#chkBusqueda").is(':checked')) ? 1 : 0);
        gridTable = $('#viewMatricula').DataTable({
            "ordering": false,
            "bInfo": true,
            "searching": false,
            "bFilter": false,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            // "iDisplayLength": 20,
            "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "Todos"]],
            "bLengthChange": false,
            "ajax": {
                "url": "<?= BASE_URL ?>sga_matricula/lista/",
                "type": "POST",
                data: {idfiltro: idfiltro, txtsearch: txtsearch, vanio: vanio, vaula: vaula}
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
                {"className": "dt-center", "targets": [0, 1, 2, 4, 5, 6, 7, 8]}
            ],
            "columns": [
                {"data": "periodo"},
                {"data": "alucod"},
                {"data": "codigo"},
                {"data": "nomcomp"},
                {"data": "ngs"},
                {"data": "aula"},
                {"data": "estado"},
                {"data": "fecmat"},
                {"data": "conf"}
            ]
        });
    }

    $(document).on('change', '#idAula', function(){
            var aula = $(this).val();
            $('#viewMatricula').DataTable().destroy();
            if(aula != '')
            {
                    js_listar(aula);
            }
            else
            {
                    js_listar('');
            }
    });
        
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

    function convertDateFormat(string, pipe) {
        var info = string.split(pipe);
        var info = info[2] + "-" + info[1] + "-" + info[0];
        return info;
    }

    function consultar_dni(vdni, vtipo, vidObjeto) {
        console.log("Validando longitudes..........");
        if (vdni.length == 8 && vdni != '') {
            console.log("Ingresando a consultar..........");
            $.ajax({
                url: '<?= BASE_URL ?>swp_pagos_test/srvreniec',
                data: {num_documento: vdni, tipo: 'dni'},
                method: 'POST',
                dataType: "json",
                beforeSend: function () {
                    $("#" + vidObjeto).val("Cargando....");
                },
                success: function (data) {
                    console.log(data);
                    $("#" + vidObjeto).val("");
                    if (data.respuesta == 'ok') {
                        if(data.estado === 'SU SUSCRIPCIÓN A CADUCADO'){
                            console.log("Modificando valores a NULL");
                            console.log("Tipo : "+vtipo);
                            if (vtipo == 'A') {
                                $("#dni").val(vdni);
                                $("#apepat").val("***************************");
                                $("#apemat").val("***************************");
                                $("#nombres").val("***************************");
                            }                            
                            if (vtipo == 'P') {
                                $("#dnipater").val("");
                                $("#padpater").val("***************************");
                                $("#padmater").val("***************************");
                                $("#padnom").val("***************************");
                            }
                            if (vtipo == 'M') {
                                $("#dnimater").val("");
                                $("#madpater").val("***************************");
                                $("#madmater").val("***************************");
                                $("#madnom").val("***************************");
                            }
                            if (vtipo == 'AP') {
                                $("#dniapo").val("");
                                $("#apopater").val("***************************");
                                $("#apomater").val("***************************");
                                $("#aponom").val("***************************");
                            }                            
                        } else {
                            console.log("Servicio RENIEC Activo");
                            if (vtipo == 'A') {
                                $("#dni").val(vdni);
                                $("#apepat").val(data.ap_paterno);
                                $("#apemat").val(data.ap_materno);
                                $("#nombres").val(data.nombres);
                            }
                            if (vtipo == 'P') {
                                $("#dnipater").val(vdni);
                                $("#padpater").val(data.ap_paterno);
                                $("#padmater").val(data.ap_materno);
                                $("#padnom").val(data.nombres);
                                $("#padfecnac").val(convertDateFormat(data.fecha_nacimiento, "/"));
                            }
                            if (vtipo == 'M') {
                                $("#dnimater").val(vdni);
                                $("#madpater").val(data.ap_paterno);
                                $("#madmater").val(data.ap_materno);
                                $("#madnom").val(data.nombres);
                                $("#madfecnac").val(convertDateFormat(data.fecha_nacimiento, "/"));
                            }
                            if (vtipo == 'AP') {
                                $("#dniapo").val(vdni);
                                $("#apopater").val(data.ap_paterno);
                                $("#apomater").val(data.ap_materno);
                                $("#aponom").val(data.nombres);
                                $("#apofecnac").val(convertDateFormat(data.fecha_nacimiento, "/"));
                            }
                        }
                    } else {
                        if (vtipo == 'P') {
                            $("#dnipater").val("");
                            $("#padpater").val(data.mensaje);
                            $("#padmater").val(data.mensaje);
                            $("#padnom").val(data.mensaje);
                        }
                        if (vtipo == 'M') {
                            $("#dnimater").val("");
                            $("#madpater").val(data.mensaje);
                            $("#madmater").val(data.mensaje);
                            $("#madnom").val(data.mensaje);
                        }
                        if (vtipo == 'AP') {
                            $("#dniapo").val("");
                            $("#apopater").val(data.mensaje);
                            $("#apomater").val(data.mensaje);
                            $("#aponom").val(data.mensaje);
                        }
                    }
                },
                complete: function () {
                    // $('.loading').hide();
                }});
        } else {
            alert("DEBE DE INGRESAR UN DNI VALIDO");
            return false;
        }
    }

    function consultar_ruc(ruc, vtipo, vidObjeto) {
        if (ruc.length == 11 && ruc != '') {
            $.ajax({
                url: '<?= BASE_URL ?>swp_pagos_test/srvreniec',
                data: {num_documento: ruc, tipo: 'ruc'},
                method: 'POST',
                dataType: "json",
                beforeSend: function () {
                    $("#" + vidObjeto).val("Cargando....");
                },
                success: function (data) {
                    if (data.respuesta == 'ok') {
                        if (vtipo == 'P') {
                            $("#padruc").val(ruc);
                            $("#padrazon").val(data.razon_social);
                        }
                        if (vtipo == 'M') {
                            $("#madruc").val(ruc);
                            $("#madrazon").val(data.razon_social);
                        }
                        if (vtipo == 'A') {
                            $("#aporuc").val(ruc);
                            $("#aporazon").val(data.razon_social);
                        }
                    } else {
                        if (vtipo == 'P') {
                            $("#padruc").val("");
                            $("#padrazon").val(data.mensaje);
                        }
                        if (vtipo == 'M') {
                            $("#madruc").val("");
                            $("#madrazon").val(data.mensaje);
                        }
                        if (vtipo == 'A') {
                            $("#aporuc").val("");
                            $("#aporazon").val(data.mensaje);
                        }
                    }
                },
                complete: function () {
                    // $('.loading').hide();
                }});
        } else {
            alert("DEBE DE INGRESAR UN RUC VALIDO");
            return false;
        }
    }

    function jalar_LocalStorage() {
        // ========== Verificando los datos en el LOCALSTORAGE ========
        var dpadre = localStorage.getItem("datosPadre");
        var dmadre = localStorage.getItem("datosMadre");

        if (dpadre) {
            $("#btnMemoriaPapaCopy").hide();
            $("#btnMemoriaPapaPaste").show();
        } else {
            $("#btnMemoriaPapaCopy").show();
            $("#btnMemoriaPapaPaste").hide();
        }

        if (dmadre) {
            $("#btnMemoriaMamaCopy").hide();
            $("#btnMemoriaMamaPaste").show();
        } else {
            $("#btnMemoriaMamaCopy").show();
            $("#btnMemoriaMamaPaste").hide();
        }
        // =============================================================    
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
    // ============ Timer Automatico para que actualiace las Matriculas ========
    setInterval('js_listar("")', 30000);
    // ========================================================================
</script>