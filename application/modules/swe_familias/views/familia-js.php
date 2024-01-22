<script type="text/javascript" >
    var gridTable;
    $(document).ready(function () {
        js_listar();
        mayuscula("input#txtsearch");

        $("#txtpaterno_p").keyup(function () {
            var texto = $(this).val().toUpperCase();
            $("#lblfamdes").html(texto);
        });

        $("#txtpaterno_m").keyup(function () {
            var texto = $(this).val().toUpperCase();
            var txtpaterno = $("#txtpaterno_p").val();
            $("#lblfamdes").html(txtpaterno + " " + texto);
        });

        $("#btnRefresh").click(function ()
        {
            $('#viewFamilias').DataTable().destroy();
            js_listar();
        });

        $("#cbhijo").change(function ()
        {
            $('#viewFamilias').DataTable().destroy();
            js_listar();
        });

        $("#btnTodos").click(function ()
        {
            $('#cbhijo').val('');
            $('#txtsearch').val('');
            js_listar();
        });

        $("#btnAgregar").click(function ()
        {
            $('#frmProceso')[0].reset();
            mayuscula("input#txtpaterno_p");
            mayuscula("input#txtmaterno_p");
            mayuscula("input#txtnombre_p");
            mayuscula("input#txtdireccion_p");
            //mayuscula("input#txtemail_p");
            mayuscula("input#txtcelular_p");

            mayuscula("input#txtpaterno_m");
            mayuscula("input#txtmaterno_m");
            mayuscula("input#txtnombre_m");
            mayuscula("input#txtdireccion_m");
            // mayuscula("input#txtemail_m");
            mayuscula("input#txtcelular_m");

            $("#lblfamdes").html("");
            $('#accion').val("insert");
            $('#modalFamilia').modal('show');
            $('.modal-title').html("<i class='glyphicon glyphicon-edit'></i> Agregar Familia");
        });

        $("#frmProceso").submit(function (event)
        {
            event.preventDefault();
            var parametros = $(this).serialize();
            $('#btngrabar').attr("disabled", true);
            $("#btngrabar").text("Grabando ...");
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>swe_familias/saveUpdate",
                data: parametros,
                dataType: "json",
                beforeSend: function (objeto) {

                },
                success: function (data) {

                    if (data['flg'] == 0) {
                        alert(data['msg']);
                        $("#btngrabar").text("Grabar Datos");
                        $('#btngrabar').attr("disabled", false);
                        $('#modalFamilia').modal('hide');
                        gridTable.ajax.reload(null, false);
                    } else {
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
        });

        $("#frmgenerador").submit(function (event) {
            event.preventDefault();
            var marca = $("#txtflag").val();
            var vmsj = true;
            if (marca == '1') {
                vmsj = window.confirm("LA FAMILIA YA TIENE CLAVE TOKEN. DESEA RESETEARLE LA CLAVE ?");
            }
            if (vmsj) {
                var parametros = {
                    vFamdesc: $("#txtfamdes").val(),
                    vFamcod: $("#txtfamcod").val()
                };
                $.ajax({
                    type: "POST",
                    url: "<?= BASE_URL ?>swe_familias/getGeneraClave",
                    data: parametros,
                    dataType: "json",
                    beforeSend: function (objeto) {
                        //$("#tbl_lista_alumnos tbody").append("<tr><td colspan='3'align='center'><img src='<?= BASE_URL ?>assets/imagenes/loading.gif' /></td></tr>");
                    },
                    success: function (dataJson) {
                        $("#txtclave").val(dataJson.token);
                        alert(dataJson.msg);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        //alert('Estatus :' + textStatus + ' Error :' + errorThrown);
                        alert(dataJson.error);
                    }
                });
            }
        });

    });

    function js_listar() {
        var txtsearch = $.trim($('#txtsearch').val());
        var cbhijo = $('#cbhijo').val();
        gridTable = $('#viewFamilias').DataTable({
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
                "url": "<?= BASE_URL ?>swe_familias/lista/",
                "type": "POST",
                data: {cbhijo: cbhijo, txtsearch: txtsearch}
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
                {"className": "dt-center", "targets": [0, 2, 3, 4]}
            ],
            "columns": [
                {"data": "famcod"},
                {"data": "famdes"},
                {"data": "email"},
                {"data": "total"},
                {"data": "conf"}
            ]
        });
    }




    function js_editar(vfamcod) {
        $('#frmProceso')[0].reset();
        mayuscula("input#txtpaterno_p");
        mayuscula("input#txtmaterno_p");
        mayuscula("input#txtnombre_p");
        mayuscula("input#txtdireccion_p");
        mayuscula("input#txtemail_p");
        mayuscula("input#txtcelular_p");
        mayuscula("input#txtpaterno_m");
        mayuscula("input#txtmaterno_m");
        mayuscula("input#txtnombre_m");
        mayuscula("input#txtdireccion_m");
        mayuscula("input#txtemail_m");
        mayuscula("input#txtcelular_m");

        var parametros = {
            vId: vfamcod
        };

        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>swe_familias/getDatosFamilia",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                $('.modal-title').html("<i class='glyphicon glyphicon-edit'></i> Editar Familia");
            },
            success: function (dataJson) {
                //alert(dataJson[0].FAMDES);
                $("#accion").val('update');
                $("#lblfamdes").html(dataJson[0].FAMDES);
                $("#txtcodigo").val(dataJson[0].FAMCOD);
                $("#txtpaterno_p").val(dataJson[0].PADAPEPAT);
                $("#txtmaterno_p").val(dataJson[0].PADAPEMAT);
                $("#txtnombre_p").val(dataJson[0].PADNOMBRE);
                $("#txtdireccion_p").val(dataJson[0].PADDIR);
                $("#txtemail_p").val(dataJson[0].PADMAIL);
                $("#txtdni_p").val(dataJson[0].PADDNI);
                $("#txtcelular_p").val(dataJson[0].PADTEL);
                $("#txtpaterno_m").val(dataJson[0].MADAPEPAT);
                $("#txtmaterno_m").val(dataJson[0].MADAPEMAT);
                $("#txtnombre_m").val(dataJson[0].MADNOMBRE);
                $("#txtdireccion_m").val(dataJson[0].MADDIR);
                $("#txtemail_m").val(dataJson[0].MADMAIL);
                $("#txtdni_m").val(dataJson[0].MADDNI);
                $("#txtcelular_m").val(dataJson[0].MADTEL);
                $('#modalFamilia').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });

    }

    function js_boqueo(vfamcod) {

    }

    function js_mail(vfamcod) {

    }

    function js_verhijos(vfamcod) {

        var parametros = {
            vId: vfamcod
        };

        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>swe_familias/getDatosHijos",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                $('.modal-title').html("<i class='glyphicon glyphicon-edit'></i> Lista de Hijos");
            },
            success: function (data) {
                var nuevaFila = "";
                // var data = data['data'];
                //alert(data.length);
                $("#viewHijos tbody").html("");
                if (data.length > 0) {
                    $.each(data, function (i, item) {
                        nuevaFila += "<tr>";
                        nuevaFila += "<td style='width: 20%;text-align: center'>" + item.ALUCOD + "</td>";
                        nuevaFila += "<td  style='width: 60%;text-align: left'>" + item.NOMCOMP + "</td>";
                        nuevaFila += "<td  style='width: 20%;text-align: center'>" + item.INSTRUCOD + " " + item.GRADOCOD + " " + item.SECCIONCOD + "</td>";
                        nuevaFila += "</tr>";
                    });
                }
                $(nuevaFila).appendTo("#viewHijos tbody");
                $('#popupHijos').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });
    }

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }

    function js_generar(vfamcod, vfamdes) {

        var parametros = {
            vFamcod: vfamcod
        };
        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>swe_familias/getVerifica",
            data: parametros,
            dataType: "json",
            success: function (dataJson) {
                $("#txtfamcod").val(vfamcod);
                $("#txtfamdes").val(vfamdes);
                $("#divEstado").html(dataJson.estado);
                $("#txtflag").val(dataJson.flag);
                if (dataJson.flag == '1') {
                    $("#txtclave").val("");
                }
                $('#popAlumnos').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Hubo un problema, Comunique al Administrador.");
            }
        });



    }

</script>