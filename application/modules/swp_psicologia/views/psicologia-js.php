<script>
    var gridTable;
    $(document).ready(function () {
        //==================== Cargamos los registros ==============
        js_listar();
        //========================================================
        $('#cbmotivo').multiselect({
            includeSelectAllOption: true,
            buttonWidth: '400px',
            dropRight: true
        });

        $("#txtfecha").datepicker({
            dateFormat: 'yy-mm-dd',
            language: 'es',
            showToday: true,
            autoclose: true
        });
        $('#txthora').timepicker(/*{
         showSeconds: true,
         showMeridian: false
         }*/);
        //========================================================
        $("#idestado").change(function ()
        {
            // var grupo = $(this).val();
            $('#viewPsicologia').DataTable().destroy();
            js_listar();
        });

        $("#btnRefresh").click(function ()
        {
            $("#idmotivo").val("");
            $('#viewPsicologia').DataTable().destroy();
            js_listar();
        });

        $("#btnGrafico").click(function ()
        {
            $('#form3')[0].reset();
            $.ajax({
                url: "<?= BASE_URL ?>swp_psicologia/grafico/",
                beforeSend: function () {
                    //$("#resultado").html("Procesando, espere por favor...");
                    spinnerShow();
                },
                success: function (data)
                {
                    spinnerHide();
                    $('#modal_grafico').modal('show'); // show bootstrap modal when complete loaded
                    $('.modal-title').text('MOSTRAR GRAFICOS'); // Set title to Bootstrap modal title   
                    //$("#viewgrafico").html(data);
                     //$('#viewgrafico').html('<img src="data:image/png;base64,' + data + '" />');
                }
            });

        });

        mayuscula("input#txtAlumnoSearch");
        mayuscula("input#txtasiste");
        mayuscula("textarea#txtinteligencia");
        mayuscula("textarea#txtemocional");
        mayuscula("textarea#txtrecomendaciones");

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

        $("#btnReset").click(function ()
        {
            $('#htxtalumno').val('');
            $('#htxtsalon').val('');
            $("#txtAlumnoSearch").val('');
            $("#txtAlumnoSearch").attr("disabled", false);
            $("#txtAlumnoSearch").focus();
        });

    });

    function js_addEgreso() {
        $('#form2')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $('#htxtalumno').val('');
        $('#htxtsalon').val('');
        $('#hidnemo').val('');
        $("#txtAlumnoSearch").val('');
        $("#txtAlumnoSearch").attr("disabled", false);
        $('#cbmotivo').multiselect("deselectAll", false);
        $('#cbmotivo').multiselect("refresh");

        $.ajax({
            url: "<?= BASE_URL ?>swp_psicologia/getdatos/",
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                //$("#resultado").html("Procesando, espere por favor...");
                spinnerShow();
            },
            success: function (data)
            {
                spinnerHide();
                $("#divasiste").hide();
                $("#htxtaccion").val(0);
                /* if (data['lstMotivo'].length > 0) {
                 $("#cbmotivo").empty();
                 $("#cbmotivo").append("<option value='0'>:::::::::::: Seleccione ::::::::::::</option>");
                 $.each(data['lstMotivo'], function (i, item) {
                 $("#cbmotivo").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                 });
                 }*/
                $('#modal_egresos').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('REGISTRAR CITA'); // Set title to Bootstrap modal title       
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_grabar()
    {

        var strmotivos = '';
        var selected = $("#cbmotivo option:selected");
        selected.each(function () {
            strmotivos += $(this).val() + ",";
        });
        if (strmotivos != '') {
            strmotivos = strmotivos.substring(0, strmotivos.length - 1);
        }
        //alert(strmotivos); return false;

        var cbmotivo = strmotivos; //$("#cbmotivo").val();
        var txtalumno = $("#txtAlumnoSearch").val();
        var txtfecha = $("#txtfecha").val();
        var txtintel = $("#txtinteligencia").val();
        var txtemo = $("#txtemocional").val();
        var txtreco = $("#txtrecomendaciones").val();
        var hnemo = $("#htxtsalon").val();
        var hdni = $("#htxtalumno").val();
        var txtacude = $('#txtasiste').val();
        var txthora = $('#txthora').val();
        var txtaccion = $('#htxtaccion').val();
        var txtidcita = $('#htxtidcita').val();
        var vcolor = $('#color').val();
        var ckhasistio = (($('#ckhasistio').is(':checked')) ? 1 : 0);
        //alert(ckhasistio); return false;
        if (vcolor == '') {
            alert("Seleccione la Prioridad.");
            return false;
        }
        if (txthora == '') {
            alert("Seleccione la Hora.");
            $('#txthora').focus();
            return false;
        }

        if (cbmotivo == '') {
            alert("Seleccione el Motivo.");
            return false;
        }

        if ($.trim(txtalumno) == '') {
            alert("Elija al Alumno Atendido");
            $("#txtAlumnoSearch").focus();
            return false;
        }

        if ($.trim(txtfecha) == '') {
            alert("Seleccione la fecha de la Atencion");
            return false;
        }

        /* if ($.trim(txtreco) == '') {
         alert("Ingrese la Recomendaciones al Alumno");
         return false;
         }*/

        var arrdata = {
            vmotivo: cbmotivo,
            valumno: txtalumno,
            vfecha: txtfecha + ' 00:00:00',
            vintel: txtintel,
            vemo: txtemo,
            vreco: txtreco,
            vdni: hdni,
            vnemo: hnemo,
            vtxtacude: txtacude,
            txthora: txthora,
            txtaccion: txtaccion,
            txtidcita: txtidcita,
            flgasiste: ckhasistio,
            vcolor: vcolor
        };

        $.ajax({
            url: "<?= BASE_URL ?>swp_psicologia/grabarUpdate/",
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
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });


    }

    function js_listar() {
        var idestado = $("#idestado").val();
        gridTable = $('#viewPsicologia').DataTable({
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
                "url": "<?= BASE_URL ?>swp_psicologia/lista/",
                "type": "POST",
                data: {idestado: idestado}
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
                {"className": "dt-center", "targets": [0, 1, 4, 5, 6, 7]}
            ],
            "columns": [
                {"data": "dni"},
                {"data": "fecreg"},
                {"data": "alumno"},
                {"data": "motivo"},
                {"data": "estado"},
                {"data": "alerta"},
                {"data": "ngs"},
                {"data": "conf"}
            ]
        });
    }

    function js_imprimir(vId) {
        $("#idreporte").val(vId);
        $("#formPrincipal").attr("action", "<?= BASE_URL ?>swp_psicologia/printCita/");
        $("#formPrincipal").submit();
    }

    function js_editar(vId) {
        $('#form2')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $('#htxtalumno').val('');
        $('#htxtsalon').val('');
        $('#hidnemo').val('');
        $("#txtAlumnoSearch").val('');
        $("#txtAlumnoSearch").attr("disabled", false);
        $('#cbmotivo').multiselect("deselectAll", false);
        $('#cbmotivo').multiselect("refresh");
        $('#txtinteligencia').attr("disabled", false);
        $('#txtemocional').attr("disabled", false);
        $('#txtrecomendaciones').attr("disabled", false);
        var arrdata = {
            idcita: vId
        };
        $.ajax({
            url: "<?= BASE_URL ?>swp_psicologia/getdatosCita/",
            data: arrdata,
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                //$("#resultado").html("Procesando, espere por favor...");
                spinnerShow();
            },
            success: function (data)
            {
                spinnerHide();
                $("#divasiste").show();
                $("#htxtaccion").val(1);
                if (data.dataCita.length > 0) {
                    var datojson = data.dataCita[0];
                    // ======= Imprimiendo valores en los inputs del Formulario =============
                    var fechalarga = datojson.feciniatencion;
                    $("#htxtidcita").val(datojson.idcita);
                    $("#txtAlumnoSearch").val(datojson.nomcomp);
                    $("#htxtalumno").val(datojson.alucod);
                    $("#htxtsalon").val(datojson.nemo);
                    $("#txtasiste").val(datojson.str_acudieron);
                    $("#txtfecha").val(fechalarga.substring(0, 10));
                    $("#txthora").val(datojson.hora);
                    $("#txtinteligencia").val(datojson.str_inteligencia);
                    $("#txtemocional").val(datojson.str_emocional);
                    $("#txtrecomendaciones").val(datojson.str_recomendacion);
                    // ======== Seleccionando los optios del combo motivos =============
                    $.each(data.datamotivo, function (row, item) {
                        $('#cbmotivo option[value="' + item.id + '"]').attr('selected', 'selected');
                    });
                    $('#cbmotivo').multiselect("refresh");
                } /*else {
                 alert("DATA ERROR");
                 }*/
                $('#modal_egresos').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('MODIFICAR CITA'); // Set title to Bootstrap modal title       
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }
    function js_eliminar(vId) {
        var msg = window.confirm("Esta seguro de Eliminar el Registro ?");
        if (msg) {
            var arrdata = {
                vId: vId
            };
            $.ajax({
                url: "<?= BASE_URL ?>swp_psicologia/eliminaEgreso/",
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