<script type="text/javascript" >
    $(document).ready(function () {
        cargaInicial();
    });

    function cargaInicial() {
        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_aulas/cargaInit",
            dataType: "json",
            beforeSend: function (objeto) {
                $("#tbl_lista_aulas tbody").html("");
                $("#tbl_lista_aulas tbody").append("<tr><td colspan='4'align='center'><img src='<?= BASE_URL ?>images/preloader.gif' /></td></tr>");
            },
            success: function (dataJson) {
                var dataTotal = dataJson['total'];
                if (dataTotal > 0) {
                    var data = dataJson['dataAulas'];
                    $("#tbl_lista_aulas tbody").html("");
                    $.each(data, function (i, row) {
                        var nuevaFila =
                                "<tr>"
                                + "<td style='text-align:center'>" + row.nemo + "</td>"
                                + "<td>" + row.nemodes + " </td>"
                                + "<td style='text-align:center'>" + row.total + "</td>"
                                + "<td style='text-align:center'>"
                                + "<a class='btn' title='Ver Listado' onclick='jsVerAlumnos(\"" + row.nemo + "\")' ><i class='glyphicon glyphicon-list'></i></a>"
                                + "<a class='btn' title='Imprimir' onclick='jsPrint(\"" + row.nemo + "\")' ><i class='glyphicon glyphicon-print'></i></a>"
                                + "</td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#tbl_lista_aulas tbody");
                    });
                } else {
                    var nuevaFila = '';
                    nuevaFila =
                            "<tr>"
                            + "<td colspan='4' align='center'><b>No se encontraron Registros</b></td>"
                            + "</tr>";
                    $(nuevaFila).appendTo("#tbl_lista_aulas tbody");
                }
                datatableCompleto("tbl_lista_aulas");
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });
    }

    function jsPrint(vnemo) {
        if (vnemo != '') {
            window.open("<?= BASE_URL ?>sga_aulas/printLista/" + vnemo, "_blank");
        }
    }

    function jsVerAlumnos(vnemo) {

        $('#accion').val("update");
        $('#popAlumnos').modal('show');
        var parametros = {
            vnemo: vnemo
        };

        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>sga_aulas/getListadoPorNemo",
            data: parametros,
            dataType: "json",
            beforeSend: function (objeto) {
                $("#tbl_lista_alumnos tbody").html("");
                //$("#tbl_lista_alumnos tbody").append("<tr><td colspan='3'align='center'><img src='<?= BASE_URL ?>assets/imagenes/loading.gif' /></td></tr>");
            },
            success: function (dataJson) {
                var dataTotal = dataJson['total'];
                if (dataTotal > 0) {
                    var data = dataJson['dataAlumnos'];
                    $("#tbl_lista_alumnos tbody").html("");
                    $.each(data, function (i, row) {
                        var nuevaFila =
                                "<tr>"
                                + "<td style='text-align:center'>" + row.alucod + "</td>"
                                + "<td>" + row.nomcomp + " </td>"
                                + "<td style='text-align:center'>" + row.estado + "</td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#tbl_lista_alumnos tbody");
                    });
                } else {
                    var nuevaFila = '';
                    nuevaFila =
                            "<tr>"
                            + "<td colspan='3' align='center'><b>No se encontraron Registros</b></td>"
                            + "</tr>";
                    $(nuevaFila).appendTo("#tbl_lista_alumnos tbody");
                }
                datatableSimple("tbl_lista_alumnos");
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Estatus :' + textStatus + ' Error :' + errorThrown);
            }
        });
    }

</script>