<script>
    var gridTable;
    $(document).ready(function () {
        //==================== Cargamos los registros ==============
        js_listar();
        mayuscula("input#txtpaterno");
        mayuscula("input#txtmaterno");
        mayuscula("input#txtnombre");
        mayuscula("input#txtlibro");
        $('#divmodal').hide();
        //========================================================
        /*$("#txtfecha").datepicker({
         dateFormat: 'yy-mm-dd',
         language: 'es',
         showToday: true,
         autoclose: true
         });*/
        //========================================================

        /*  $("#idgrupo,#idcomp").change(function ()
         {
         $('#viewEgresos').DataTable().destroy();
         js_listar();
         });
         $("#btnRefresh").click(function ()
         {
         $('#idgrupo > option[value=""]').attr("selected", true);
         $('#idcomp > option[value=""]').attr("selected", true);
         gridTable.ajax.reload(null, false);
         })*/
        mayuscula("input#txtsearch");
        mayuscula("input#txtdireccion");
        mayuscula("input#txtprocede");
        $('[data-toggle="tooltip"]').tooltip();
        
        
        $("#txtdni").blur(function (){
            //alert("hola");
        });
        
        $("#txtpaterno").keyup(function () {
            var texto = $(this).val().toUpperCase();
            $("#txtfamilia").val(texto);
        });
        $("#txtmaterno").keyup(function () {
            var texto = $(this).val().toUpperCase();
            var txtpaterno = $("#txtpaterno").val();
            $("#txtfamilia").val(txtpaterno + " " + texto);
        });
        $("#cbnivel").change(function ()
        {
            $("#cbgrado").empty();
            $("#cbgrado").append("<option value=''>Cargando......</option>");
            $.getJSON("<?= BASE_URL ?>sga_reporte_pago/lstgrado/" + this.value, function (data) {
                $("#cbgrado").empty();
                $("#cbgrado").append("<option value=''>::::::: Todos :::::::</option>");
                $.each(data, function (i, item) {
                    $("#cbgrado").append("<option value=\"" + item.id + "\">" + item.id + " : " + item.value + "</option>");
                });
            });
        });
        $("#cbfiltro").change(function ()
        {
            var tipo = $(this).val();
            if (tipo == 1) {
                $("#txtsearch").attr('maxlength', '8');
                $("#txtsearch").attr('placeholder', 'Ingrese el numero de DNI');
                $("#txtsearch").focus();
            } else if (tipo == 2) {
                $("#txtsearch").attr('maxlength', '8');
                $("#txtsearch").attr('placeholder', 'Ingrese Codigo del Alumno');
                $("#txtsearch").focus();
            } else if (tipo == 3) {
                $("#txtsearch").attr('maxlength', '30');
                $("#txtsearch").attr('placeholder', 'Ingrese Apellidos del Alumno');
                $("#txtsearch").focus();
            } else {
                $("#txtsearch").attr('maxlength', '10');
                $("#txtsearch").attr('placeholder', 'Seleccione el Filtro');
            }
        });
        $("#btnRefresh").click(function ()
        {
            $('#viewAlumnos').DataTable().destroy();
            js_listar();
        });
        $("#btnReniec").click(function ()
        {
            var vdni = $('#txtdni').val();
            if ($.trim(vdni) == "") {
                alert("Ingrese el numero de DNI.");
                return false;
            }
            if (vdni.length < 8) {
                alert("Ingrese los 8 digitos del DNI.");
                return false;
            }

            $.ajax({
                type: 'POST',
                url: 'http://sistemas-dev.com/ws-reniec/ws-reniec.php',
                dataType: "json",
                data: {dni: vdni},
                beforeSend: function (objeto) {
                    $('#divmodal').show();
                },
                success: function (data) {
                    $('#divmodal').hide();
                    if (typeof data.nombres != 'undefined') {
                        $('#txtnombre').val(data.nombres);
                        $('#txtpaterno').val(data.apellido_paterno);
                        $('#txtmaterno').val(data.apellido_materno);
                        $('#txtfamilia').val(data.apellido_paterno + " " + data.apellido_materno);
                    } else {
                        alert(data.toUpperCase());
                    }
                }
            });
        });
        $("#btnMostrarAll").click(function ()
        {
            $('#cbnivel').val('');
            $('#cbgrado').val('');
            $('#cbfiltro').val('');
            $('#txtsearch').val('');
            $("#txtsearch").attr('maxlength', '10');
            $("#txtsearch").attr('placeholder', 'Seleccione el Filtro');
            js_listar();
        });
        $("#btnAgregar").click(function ()
        {
            $('#frmAlumno')[0].reset();
            $('#accion').val("insert");
            cargaFamilias();
            $('#divlabel').hide();
            //$('#divcombo').show();
            $('#divcbfamilia').hide();
            $('#divtxtfamilia').show();
            $('#modalAlumno').modal('show');
            $('.modal-title').html("<i class='glyphicon glyphicon-edit'></i> Agregar Alumno");
            //$("#cb_estado > option[value='V']").attr('selected', 'selected')
        });

        $("#frmAlumno").submit(function (event)
        {
            event.preventDefault();
            if ($('#accion').val() == "insert") {
                var chk = $('#hcombo').val();
                if (chk == '1' && $('#cb_familia').val() == "") {
                    alert("SELECCIONE LA FAMILIA A LA QUE PERTENECE EL ALUMNO.");
                    return false;
                }
                // ======== Validar si existe familia ==================           
                if (chk == '0') {
                    var param = {
                        'vpat': $.trim($('#txtpaterno').val()),
                        'vmat': $.trim($('#txtmaterno').val())
                    };
                    $.ajax({
                        type: "POST",
                        url: "<?= BASE_URL ?>sga_alumnos/validaFamilia",
                        data: param,
                        dataType: "json",
                        success: function (data) {
                            if (data['flgtotal'] > 0) {
                                alert(data['msg'].replace("*", "\n").replace("*", "\n"));
                                return false;
                            } else {
                                saveAlumno();
                            }
                        }
                    });
                } else {
                    saveAlumno();
                }
            } else {
                saveAlumno();
            }
            // ===============================================

        });

    });

    function saveAlumno() {
        var parametros = $("#frmAlumno").serialize();
        // alert(parametros);
        $('#btngrabar').attr("disabled", true);
        $("#btngrabar").text("Grabando ...");

        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_alumnos/saveUpdate",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {

            },
            success: function (data) {

                if (data['flg'] == 0) {
                    alert(data['msg']);
                    $("#btngrabar").text("Grabar Datos");
                    $('#btngrabar').attr("disabled", false);
                    $('#modalAlumno').modal('hide');
                    gridTable.ajax.reload(null, false);
                } else {
                    $("#btngrabar").text("Grabar Datos");
                    $('#btngrabar').attr("disabled", false);
                    alert(data['msg']);
                    console.log("Error : " + data['error']);
                }

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error interno, Comuniquese con el Administrador : \nE-mail : info@sistemas-dev.com');
                $("#btngrabar").text("Grabar Datos");
                $('#btngrabar').attr("disabled", false);
            }
        });
    }

    function js_carga(chk) {
        if (chk) {
            $('#divcbfamilia').show();
            $('#divtxtfamilia').hide();
            $('#cb_familia').attr("disabled", false);
            $('#hcombo').val('1');
        } else {
            $('#divcbfamilia').hide();
            $('#divtxtfamilia').show();
            $('#hcombo').val('0');
        }
    }

    function js_activar(vId, vNemo) {
        var msg = window.confirm("Esta seguro de Activar al Alumno con Codigo :  " + vId + " ?");
        if (msg) {
            var arrdata = {
                vId: vId,
                vNemo: vNemo
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_alumnos/activar/",
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

    function js_boqueo(vId, vNemo) {
        var msg = window.confirm("Esta seguro de Bloquear al Alumno con Codigo :  " + vId + " ?");
        if (msg) {
            var arrdata = {
                vId: vId,
                vNemo: vNemo
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_alumnos/eliminaAlumno/",
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

    function js_editar(vId)
    {
        $('#frmAlumno')[0].reset();
        $('#accion').val("update");
        $('#divcheck').hide();
        mayuscula("input#txtpaterno");
        mayuscula("input#txtmaterno");
        mayuscula("input#txtnombre");
        var parametros = {
            vId: vId
        };
        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_alumnos/getDatosAlumno",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                //  $('#divcombo').hide();
                $('#divlabel').show();
                $('#divcbfamilia').show();
                $('#divtxtfamilia').hide();
                cargaFamilias();
                $('#modalAlumno').modal('show');
                $('.modal-title').html("<i class='glyphicon glyphicon-edit'></i> Editar Alumno");
            },
            success: function (dataJson) {
                $("#lblaula").html(dataJson.NEMODES);
                $("#txtlibro").val(dataJson.NUMLIBRO);
                $("#txtcodigo").val(dataJson.ALUCOD);
                $("#txtdni").val(dataJson.DNI);
                $("#txtpaterno").val(dataJson.APEPAT);
                $("#txtmaterno").val(dataJson.APEMAT);
                $("#txtnombre").val(dataJson.NOMBRES);
                $("#txtdireccion").val(dataJson.DIRECCION);
                $("#hcb_familia").val(dataJson.FAMCOD);
                $("#txtprocede").val(dataJson.PROCEDE);
                $("#txttelefono").val(dataJson.TELEFONO);
                $("#txttelefono2").val(dataJson.TELEFONO2);
                // $("#cb_estado > option[value='" + dataJson.ESTADO + "']").attr('selected', 'selected');
                if ($.trim(dataJson.FAMCOD) == '') {
                    $("#cb_familia").removeAttr("disabled");
                } else {
                    setTimeout(function () {
                        $("#cb_familia > option[value='" + dataJson.FAMCOD + "']").attr('selected', 'selected');
                    }, 1000);
                }
                /*$("#cb_nivel > option[value='" + dataJson.INSTRUCOD + "']").attr('selected', 'selected');
                 $("#cb_nivel").trigger("change");
                 setTimeout(function () {
                 $("#cb_grado > option[value='" + dataJson.GRADOCOD + "']").attr('selected', 'selected');
                 $("#cb_grado").trigger("change");
                 setTimeout(function () {
                 $("#cb_seccion > option[value='" + dataJson.SECCIONCOD + "']").attr('selected', 'selected');
                 }, 1000);
                 }, 1000);*/

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });
    }

    //$("#cbnivel").change(function () {
    function cargaGrado() {
        $("#cb_grado").empty();
        $("#cb_grado").append('<option value="">Cargando....</option>');
        //$('#cbgrado').attr("disabled", false);
        $.getJSON('<?= BASE_URL ?>sga_alumnos/getGrado/' + $("#cb_nivel").val(),
                function (json) {
                    $("#cb_grado").empty();
                    $("#cb_grado").append('<option value="">:: Seleccione ::</option>');
                    $.each(json, function (id, value) {
                        $("#cb_grado").append('<option value="' + id + '">' + value + '</option>');
                    });
                });
    }
    //});
    function cargaFamilias() {
        $("#cb_familia").empty();
        $("#cb_familia").append('<option value="">Cargando....</option>');
        $.getJSON('<?= BASE_URL ?>sga_alumnos/getFamilias',
                function (json) {
                    $("#cb_familia").empty();
                    $("#cb_familia").append('<option value="">::::::::::::::::::: NINGUNO :::::::::::::::::::</option>');
                    $.each(json, function (id, value) {
                        $("#cb_familia").append('<option value="' + id + '">' + value + '</option>');
                    });
                });
    }

    //$("#cbgrado").change(function () {
    function cargaAula(vobjNivel, vobjGrado, objCarga) {
        $("#" + objCarga).empty();
        $("#" + objCarga).append('<option value="">Cargando....</option>');
        //$('#cbseccion').attr("disabled", false);
        $.getJSON('<?= BASE_URL ?>sga_alumnos/getSeccion/' + vobjNivel + '/' + vobjGrado,
                function (json) {
                    $("#" + objCarga).empty();
                    $("#" + objCarga).append('<option value="">:: Seleccione ::</option>');
                    $.each(json, function (id, value) {
                        $("#" + objCarga).append('<option value="' + id + '">' + value + '</option>');
                    });
                });
    }

    function js_listar() {
        var idnivel = $('#cbnivel').val();
        var idgrado = $('#cbgrado').val();
        var idfiltro = $('#cbfiltro').val();
        var txtsearch = $.trim($('#txtsearch').val());
        gridTable = $('#viewAlumnos').DataTable({
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
                "url": "<?= BASE_URL ?>sga_alumnos/lista/",
                "type": "POST",
                data: {idnivel: idnivel, idgrado: idgrado, idfiltro: idfiltro, txtsearch: txtsearch}
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
                {"className": "dt-center", "targets": [0, 2, 3, 4, 5, 6, 7]}
            ],
            "columns": [
                {"data": "codigo"},
                {"data": "nomcomp"},
                {"data": "aula"},
                {"data": "nivel"},
                {"data": "grado"},
                {"data": "matricula"},
                {"data": "estado"},
                {"data": "conf"}
            ]
        });
    }

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
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