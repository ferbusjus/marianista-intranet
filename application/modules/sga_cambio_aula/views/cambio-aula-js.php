<script src="<?php echo base_url() ?>assets/jquery/jquery-2.1.4.min.js"></script>
<script src="<?php echo base_url() ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url() ?>assets/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/datatables/js/dataTables.bootstrap.js"></script>

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });

        $("#cbsalon").change(function ()
        {
            $("#divAlerta").hide();
            var field = this.value;
            if (field != 0) {
                var campo = field.split("-");
                var vnemo = campo[0];
                var vnivel = campo[1];
                var vgrado = campo[2];

                $.getJSON("<?= BASE_URL ?>sga_cambio_aula/lstalumno/" + vnemo, function (data) {
                    var nuevaFila = "";
                    $("#viewListaMigracion tbody").html("");
                    if (data.length > 0) {
                        $.each(data, function (i, item) {
                            cargarAulasMigrar(vnivel, vgrado, i, item.ALUCOD);
                            var htmlSelect = '';
                            htmlSelect += '<select name="cbsalonDestino_' + i + '" id="cbsalonDestino_' + i + '" style="width: 100%" class="form-control input-sm" onchange="js_cambioSalon(this.value,' + i + ',this.id);">';
                            htmlSelect += ' <option value="0">:::::::::::::::::::::::: Seleccione Salon Origen  :::::::::::::::::::::::::</option>';
                            htmlSelect += ' </select>';
                            nuevaFila += '<tr >';
                            nuevaFila += '<td style="width: 5%;text-align: center">' + item.DNI + '</td>';
                            nuevaFila += '<td style="width: 25%;text-align: left">' + item.NOMCOMP + '</td>';
                            nuevaFila += ' <td style="width: 30%;text-align: left">' + item.OBSERVACION + '</td> ';
                            nuevaFila += ' <td style="width: 30%;text-align: center">' + htmlSelect + '</td> ';
                            nuevaFila += ' <td style="width: 10%;text-align: center"><label id="divEstado_' + i + '" style="font-weight:bold; color:black"></label></td> ';
                            nuevaFila += '</tr>';
                        });
                    } else {
                        nuevaFila += '<tr >';
                        nuevaFila += '<td  colspan="5" style="width: 100%;text-align: center">NO EXISTEN REGISTROS PARA ESTA AULA.</td>';
                        nuevaFila += '</tr>';
                    }
                    $(nuevaFila).appendTo("#viewListaMigracion tbody");
                    // var htmlSelect = cargarAulasMigrar(vnivel, vgrado);
                    // alert(htmlSelect);
                });
            }
        });

        $("#btnProcesar").click(function ()
        {

            var valucod = $("#cbalumno").val();
            var vnemo = $("#cbsalon").val();

            if (vnemo == '0') {
                alert("Seleccione el Salon Origen.");
                return false;
            }
            if (valucod == '0') {
                alert("Seleccione el Alumno.");
                return false;
            }
            if ($("#cbsalonDestino").val() == '0') {
                alert("Seleccione el Salon Destino.");
                return false;
            }

            var msg = window.confirm("EL ALUMNO : " + $('#cbalumno option:selected').text() + "\nDEL AULA : " + $('#cbsalon option:selected').text() + "\nPASARA AL AULA : " + $('#cbsalonDestino option:selected').text() + "\nESTA SEGURO DE CONTINUAR CON EL PROCESO ?\n\nNOTA: SE MIGRARA AL ALUMNO CON TODO SUS NOTAS.");
            if (msg) {
                var arrdata = {
                    vnemOrg: vnemo,
                    vnemDes: $("#cbsalonDestino").val(),
                    valucod: valucod
                };
                $.ajax({
                    url: "<?= BASE_URL ?>swe_cambio_salon/cambioAula/",
                    type: "POST",
                    dataType: "json",
                    data: arrdata,
                    success: function (data)
                    {
                        if (data['flg'] == 1) {
                            alert(data['msg'] + $('#cbsalonDestino option:selected').text());
                            //$("cbsalon option[value='0']").attr('selected', 'selected');
                            $('#cbsalon').val('0');
                            $('#cbalumno').val('0');
                            $('#cbsalonDestino').val('0');
                        } else {
                            alert(data['msg']);
                        }
                    }
                });

            }
        });

    });

    function js_cambioSalon(value, div, id) {
        if (value != '0') {
            var msg = window.confirm("ESTA SEGURO DE MOVER AL ALUMNO?.");
            if (msg) {
                var parte = value.split(":");
                var arrdata = {
                    valucod: parte[0],
                    vnemo: parte[1]
                };
                $.ajax({
                    url: "<?= BASE_URL ?>sga_cambio_aula/processCambioAula/",
                    type: "POST",
                    dataType: "json",
                    data: arrdata,
                    success: function (data)
                    {
                        if (data['flg'] == 1) {
                            alert(data['msg']);
                            $('#divEstado_' + div).html(data['estado']);
                        } else {
                            console.log("ERROR :" + data['msg']);
                        }
                    }
                });
            } else {
                $('#'+id).val("0");
            }
        }
    }

    function cargarAulasMigrar(vnivel, vgrado, indice, alucod)
    {
        var arrdata = {
            vnivel: vnivel,
            vgrado: vgrado
        };
        $.ajax({
            url: "<?= BASE_URL ?>sga_cambio_aula/cargaAulaMigra/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                $("#cbsalonDestino_" + indice).empty();
                $("#cbsalonDestino_" + indice).append("<option value='0'>:::::::::::::::::::::::: Seleccione Salon Destino  :::::::::::::::::::::::::</option>");
                $.each(data, function (i, item) {
                    $("#cbsalonDestino_" + indice).append("<option value=\"" + alucod + ":" + item.NEMO + "\">" + item.NEMO + " : " + item.NEMODES + "</option>");
                });
            }
        });
    }
</script>