<script >
    var gridTable;
    $(document).ready(function () {
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
    });

    function js_addconcepto() {
        $('#form2')[0].reset(); // reset form on modals
        mayuscula("input#txtpaterno");
        mayuscula("input#txtmaterno");
        mayuscula("input#txtnombres");
        mayuscula("input#txtrecibo");
        
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos_test/getAddConcepto/",
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                //$("#resultado").html("Procesando, espere por favor...");
                spinnerShow();
            },
            success: function (data)
            {
                spinnerHide();
                if (data['data'].length > 0) {
                    var txt = $("#cbalumno option:selected").text();
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
        var vfecha = $("#txtfecha").val();
        var vnumrecibo = $("#txtrecibo").val();
        var vpaterno = $("#txtpaterno").val();
        var vmaterno = $("#txtmaterno").val();
        var vnombres = $("#txtnombres").val();

        if (vconcepto == '0') {
            alert("Seleccione un Concepto de Pago");
            return false;
        }

        if ($.trim(vpaterno) == '') {
            alert("Ingrese el Apellido Paterno del Alumno");
            return false;
        }

        if ($.trim(vmaterno) == '') {
            alert("Ingrese el Apellido Materno del Alumno");
            return false;
        }

        if ($.trim(vnombres) == '') {
            alert("Ingrese los Nombres del Alumno");
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

        var arrdata = {
            vfecha: vfecha,
            vapepat: vpaterno,
            vapemat: vmaterno,
            vnom: vnombres,
            vmonto: vmonto,
            vconcepto: vconcepto,
            vnumrecibo: vnumrecibo
        };

        $.ajax({
            url: "<?= BASE_URL ?>swp_pagos_test/grabarConceptoAdic/",
            type: "POST",
            dataType: "json",
            data: arrdata,
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
            "searching": false,
            "bFilter": false,
            "bInfo": true,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 20,
            "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "All"]],
            "bLengthChange": false,
            "ajax": {
                "url": "<?= BASE_URL ?>swp_pagos_test/lstPagosAdicional/",
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
                {"className": "dt-center", "targets": [0, 1, 3, 4, 5]}
            ],
            "columns": [
                {"data": "id"},
                {"data": "fecreg"},
                {"data": "nomcomp"},
                {"data": "concepto"},
                {"data": "recibo"},
                {"data": "monto"}
            ]
        });
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