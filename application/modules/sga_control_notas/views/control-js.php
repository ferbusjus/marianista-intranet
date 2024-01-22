<script>
    var gridTable = '';
    $(document).ready(function () {
        //==================== Cargamos los registros ==============
        //js_listar();
        //========================================================
        $('#idnivel').change(function () {
            // $(this).val() will work here
            if ($(this).val() != '0') {
                $('#idbimestre').attr("disabled", false);
                $("#idbimestre").val(0);
                $('#idunidad').attr("disabled", true);
                $('#idunidad').empty();
                $('#idunidad').append("<option value='0'>SELECCIONE</option>");
                if (gridTable != "") {
                    //gridTable.clear();
                   // gridTable.destroy();
                  //  alert("1");
                    $("#viewControl tbody").empty();
                     $("#viewControl_paginate").empty();
                     gridTable.clear().draw();
                    //gridTable.draw();
                    //gridTable.ajax.reload();
                }
                //$("#viewControl tbody").html("");
                // limpiar divTblPagos, Luego crear head html y luego llamar a listar
            } else {
                $('#idbimestre').attr("disabled", true);
                $("#idbimestre").val(0);
                $('#idunidad').attr("disabled", true);
                $('#idunidad').empty();
                $('#idunidad').append("<option value='0'>SELECCIONE</option>");
            }
        });
        $('#idbimestre').change(function () {
            // $(this).val() will work here
            if ($(this).val() != '0') {
                var bim = $(this).val();
                $('#idunidad').attr("disabled", false);
                $('#idunidad').empty();
                $('#idunidad').append("<option value='0'>SELECCIONE</option>");
                if (bim == '1') {
                    $('#idunidad').append('<option value="1">UNIDAD 1</option>');
                    $('#idunidad').append('<option value="2">UNIDAD 2</option>');
                } else if (bim == '2') {
                    $('#idunidad').append('<option value="3">UNIDAD 3</option>');
                    $('#idunidad').append('<option value="4">UNIDAD 4</option>');
                } else if (bim == '3') {
                    $('#idunidad').append('<option value="5">UNIDAD 5</option>');
                    $('#idunidad').append('<option value="6">UNIDAD 6</option>');
                } else if (bim == '4') {
                    $('#idunidad').append('<option value="7">UNIDAD 7</option>');
                    $('#idunidad').append('<option value="8">UNIDAD 8</option>');
                }
            } else {
                $('#idunidad').attr("disabled", true);
                $('#idunidad').empty();
                $('#idunidad').append("<option value=''>SELECCIONE</option>");
            }
        });

        $('#btnBuscar').click(function () {
            if ($('#idunidad').val() != '0') {
                js_listar();
            } else {
                bootbox.alert({
                    title: "AVISO!",
                    message: "Seleccione la Unidad.",
                    size: 'small'
                });
                return false;
            }
        });

    });


    function js_listar() {
        var idnivel = $("#idnivel").val();
        var idbimestre = $("#idbimestre").val();
        var idunidad = $("#idunidad").val();
        if (idunidad != '0' && idnivel != '0') {
            gridTable = $('#viewControl').DataTable({
                "ordering": false,
                "searching": false,
                //"dom": 'rt',
                // "bFilter": false,
                "bInfo": true,
                "bDestroy": true,
                "processing": true,
                "serverSide": true,
                // "iDisplayLength": 20,
                "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "Todos"]],
                "bLengthChange": false,
                "ajax": {
                    "url": "<?= BASE_URL ?>sga_control_notas/lista/",
                    "type": "POST",
                    data: {idnivel: idnivel, idbimestre: idbimestre, idunidad: idunidad}
                },
                "language": {
                    "emptyTable": "No hay datos disponibles en la tabla.",
                    "info": "Del _START_ al _END_ de _TOTAL_ ",
                    "infoEmpty": "Mostrando 0 registros de un total de 0.",
                    "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    "infoPostFix": "(actualizados)",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing": "<img src='http://sistemas-dev.com/intranet/img/gif-load.gif' ><br><center>Cargando...</center>",
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
                    {"className": "dt-center", "targets": [0, 3, 4, 5, 6]}
                ],
                "columns": [
                    {"data": "fila"},
                    {"data": "aula"},
                    {"data": "tutor"},
                    {"data": "treg"},
                    {"data": "tcarga"},
                    {"data": "avance"},
                    {"data": "conf"}
                ]
            });
        }
    }

    function js_detalle_salon(vnemo = '', vdescaula = '') {
        $('#form2')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        var idnivel = $("#idnivel").val();
        var idbimestre = $("#idbimestre").val();
        var idunidad = $("#idunidad").val();
        var arrdata = {
            vnemo: vnemo,
            idbimestre: idbimestre,
            idunidad: idunidad,
            idnivel: idnivel
        };
        $.ajax({
            url: "<?= BASE_URL ?>sga_control_notas/getdetallenemo/",
            data: arrdata,
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                //$("#resultado").html("Procesando, espere por favor...");
                spinnerShow();
            },
            success: function (json)
            {
                spinnerHide();
                $("#viewdetalle tbody").html("");
                if (json.data.length > 0) {
                    var nuevaFila = '';
                    var arrData = json.data;
                    $.each(arrData, function (i, item) {
                        nuevaFila +=
                                "<tr >"
                                + "<td style='width: 5%;text-align: center'>" + item.fila + "</td>"
                                + "<td  style='width: 20%;text-align: left'>" + item.curso + "</td>"
                                + "<td  style='width: 25%;text-align: left'>" + item.profe + "</td>"
                                + "<td  style='width: 5%;text-align: center'>" + item.talum + "</td>"
                                + "<td  style='width: 5%;text-align: center'>" + item.llenado + "</td>"
                                + "<td  style='width: 5%;text-align: center'>" + item.falta + "</td>"
                                + "<td  style='width: 35%;text-align: center'>" + item.avance + "</td>"
                                + "</tr>";
                        //$(nuevaFila).appendTo("#viewListado tbody");
                    });
                    $("#viewdetalle tbody").html(nuevaFila);
                }
                $('#modal_detalle').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('AULA : ' + vdescaula);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
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