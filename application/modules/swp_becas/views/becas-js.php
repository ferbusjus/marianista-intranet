<script>
    var gridTable;
    $(document).ready(function () {
        //==================== Cargamos los registros ==============
        js_listar();
        //========================================================
        $("#txtfecha").datepicker({
            dateFormat: 'yy-mm-dd',
            language: 'es',
            showToday: true,
            autoclose: true
        });
        //========================================================
        $("#idbeca").change(function ()
        {
            $('#viewBecas').DataTable().destroy();
            js_listar();
        });

        $("#cbtipobeca").change(function ()
        {
            var pension = 340;
            var value = $(this).val();
            $("#txtmonto").val("");
            if (value != 0) {
                value = value.split("*");
                console.log(typeof parseInt(value[1]));
                var total = ((pension * parseInt(value[1])) / 100);
                total = pension - total;
                $("#txtmonto").val(total.toString().concat('.00'));
            }
        });


        $("#btnRefresh").click(function ()
        {
            $("#idgrupo").val("");
            $("#idcomp").val("");
            $('#viewBecas').DataTable().destroy();
            js_listar();
        });

        $("#btnAgregar").click(function ()
        {
            $('#form2')[0].reset(); // reset form on modals
            // mayuscula("input#txtdescripcion");
            // mayuscula("input#txtcomprobante");
            //Ajax Load data from ajax
            $.ajax({
                url: "<?= BASE_URL ?>swp_becas/getdatos/",
                type: "POST",
                dataType: "json",
                beforeSend: function () {
                    //$("#resultado").html("Procesando, espere por favor...");
                    spinnerShow();
                },
                success: function (data)
                {

                    if (data['alumno'].length > 0) {
                        $("#cbalumno").empty();
                        $("#cbalumno").append("<option value='0'>:::::::::::: Seleccione ::::::::::::</option>");
                        $.each(data['alumno'], function (i, item) {
                            $("#cbalumno").append("<option value=\"" + item.id + "\">" + item.value + "</option>");
                        });
                    }

                    if (data['tipobeca'].length > 0) {
                        $("#cbtipobeca").empty();
                        $("#cbtipobeca").append("<option value='0'>:::::::::::: Seleccione ::::::::::::</option>");
                        $.each(data['tipobeca'], function (i, item) {
                            $("#cbtipobeca").append("<option value=\"" + item.id + "\">" + item.value + "</option>");
                        });
                    }

                    if (data['motbeca'].length > 0) {
                        $("#cbmotbeca").empty();
                        $("#cbmotbeca").append("<option value='0'>:::::::::::: Seleccione ::::::::::::</option>");
                        $.each(data['motbeca'], function (i, item) {
                            $("#cbmotbeca").append("<option value=\"" + item.id + "\">" + item.value.toUpperCase() + "</option>");
                        });
                    }


                    spinnerHide();
                    $('#modal_becas').modal('show'); // show bootstrap modal when complete loaded
                    $('.modal-title').text('AGREGAR NUEVA BECA'); // Set title to Bootstrap modal title       
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                    //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
                }
            });
        });


    });

    function js_grabar()
    {
        var vidAlumno = $("#cbalumno").val();
        var vmesini = $("#cbmes1").val();
        var vmesfin = $("#cbmes2").val();
        var vmotbeca = $("#cbmotbeca").val();
        var vtipbeca = $("#cbtipobeca").val();
        var vmonto = $("#txtmonto").val();

        // ============== Validando los datos con seleccion ===================
        if (vidAlumno == '0') {
            alert("Seleccione el Alumno.");
            return false;
        }
        if (vmesini == '0') {
            alert("Seleccione el Mes de Inicio.");
            return false;
        }
        if (vmesfin == '0') {
            alert("Seleccione el Mes Fin.");
            return false;
        }
        if (vmotbeca == '0') {
            alert("Seleccione el Motivo de la Beca.");
            return false;
        }
        if (vtipbeca == '0') {
            alert("Seleccione el Tipo de la Beca.");
            return false;
        }
        // ================= Validando los rangos de mes =======================
        var ini = parseInt(vmesini);
        var fin = parseInt(vmesfin);
        if (ini >= fin) {
            alert("El Mes Fin debe ser Mayor al Mes de Inicio")
            return false;
        }
        if (vtipbeca != 0) {
            var value = vtipbeca.split("*");
            vtipbeca = value[0];
        }

        var arrdata = {
            vidAlumno: vidAlumno,
            vmesini: vmesini,
            vmesfin: vmesfin,
            vmotbeca: vmotbeca,
            vtipbeca: vtipbeca,
            vmonto: vmonto,
            vaccion: 1
        };

        $.ajax({
            url: "<?= BASE_URL ?>swp_becas/saveUpdate/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                alert(data['msg']);
                if (data['flg'] == 0) {
                    $('#modal_becas').modal('hide');
                    gridTable.ajax.reload(null, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });


    }

    function js_listar() {
        var idbeca = $("#idbeca").val();
        gridTable = $('#viewBecas').DataTable({
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
                "url": "<?= BASE_URL ?>swp_becas/lista/",
                "type": "POST",
                data: {idbeca: idbeca}
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
                {"className": "dt-center", "targets": [0, 2, 3, 4, 5, 6]}
            ],
            "columns": [
                {"data": "dni"},
                {"data": "nomcomp"},
                {"data": "ngs"},
                {"data": "mesini"},
                {"data": "mesfin"},
                {"data": "beca"},
                {"data": "conf"}
            ]
        });
    }

    function js_eliminar(vId, vIdBeca) {
        var msg = window.confirm("¿Esta seguro de Eliminar la Beca?");
        if (msg) {
            var arrdata = {
                vId: vId,
                vIdBeca: vIdBeca
            };
            $.ajax({
                url: "<?= BASE_URL ?>swp_becas/eliminaBeca/",
                type: "POST",
                dataType: "json",
                data: arrdata,
                success: function (data)
                {
                    alert(data['msg']);
                    if (data['flg'] == 0) {
                        $('#modal_becas').modal('hide');
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