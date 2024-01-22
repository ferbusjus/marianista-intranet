

<script >
    var save_method;
    var table;
    var fechaDate = new Date();
    var ano = fechaDate.getFullYear();

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });


        $("#btnBoletas").click(function ()
        {
            if ($("#cbalumno").val() == '0') {
                alert("Seleccione el Alumno.");
                return false;
            }
            var cadCombo = $('#cbalumno').val().split("|");
            var vId = cadCombo[1]; // 0 : SALON / 1:ALUMNO / 2:IDPAGO
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "<?= BASE_URL ?>swp_pagos_test/getComprobantes",
                data: "vIdAlumno=" + vId,
                success: function (dataJson) {
                    var data = dataJson['data'];
                    $("#viewResumen tbody").html("");
                    if (data.length > 0) {
                        $.each(data, function (i, row) {
                            var opcBoleta = "<img src='<?= BASE_URL ?>img/boleta.png' title='Ver Boleta' width='25px' heigth='25px' onclick=\"javascript:printBoleta('" + $.trim(data[i].numrecibo) + "');\" />";
                            var nuevaFila =
                                    "<tr>"
                                    + "<td style='text-align:center;' >" + data[i].numrecibo + "</td>"
                                    + "<td style='text-align:left;' >" + data[i].fecmod + "</td>"
                                    + "<td style='text-align:right;' >S/. " + data[i].monto + "</td>"
                                    + "<td style='text-align:center;' >" + opcBoleta + "</td>"
                                    + "</tr>";
                            $(nuevaFila).appendTo("#viewResumen tbody");
                        });
                        
                    } else {
                        var nuevaFila =
                                "<tr>"
                                + "<td colspan='4'><center>No se encontraron Registros</center></td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#viewResumen tbody");
                    }
                    $('#ModalVerPagos').modal('show');
                }
            });
        });

        $("#btnBuscar").click(function ()
        {
            if ($("#cbalumno").val() == '0') {
                alert("Seleccione el Alumno.");
                return false;
            }
            var token = $('#token').val()
            var cadCombo = $('#cbalumno').val().split("|");
            var vId = cadCombo[1]; // 0 : SALON / 1:ALUMNO / 2:IDPAGO
            console.log("ID : " + vId);
            /*  if(vId=='') {
             alert("NO SE ENCONTRO CODIGO DE PAGO ASIGNADO AL ALUMNO");
             return false;
             }                 */
            var vMes = 0;
            $.ajax({
                type: "POST",
                dataType: "json",
                //dataType: "jsonp",
                // jsonp: 'callback',
                url: "<?= BASE_URL ?>swe_pago/getpagos",
                //url: "http://www.marianista.pe/getpagos/getpagos.php",
                data: "vid=" + vId + "&vMes=" + vMes + "&vAcc=1&token=" + token,
                beforeSend: function () {
                    $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Consultando Pagos...</center></div></div>");
                },
                success: function (dataJson) {
                    var data = dataJson['data'];
                    if (data.length > 0) {
                        $("#viewListado tbody").html("");
                        var fila = 1;
                        $.each(data, function (i, item) {
                            var vfecha = $.trim(item.fecmod);
                            console.log("1:" + vfecha);
                            if (vfecha != '') {
                                vfecha = vfecha.split(" ");
                                vfecha = vfecha[0];
                            } else {
                                vfecha = '';
                            }
                            console.log("2:" + vfecha);
                            //alert(item.estado);
                            var opcBoleta = "";
                            if (item.estado == 'C') {
                                var msgpago = "CANCELADO";
                                var estado = "<?= BASE_URL ?>img/pagado.png";
                                //var opcBoleta = "<img src='<?= BASE_URL ?>img/boleta.png' title='Ver Boleta' width='25px' heigth='25px' onclick=\"javascript:printBoleta('" + $.trim(item.numrecibo) + "');\" />";
                            } else {
                                var msgpago = "PENDIENTE";
                                var estado = "<?= BASE_URL ?>img/Pendientes.png";
                                //var opcBoleta = "";
                            }
                            var concepto = (item.condes + " - " + item.mesdes + " - " + ano);
                            var nuevaFila =
                                    "<tr>"
                                    + "<td  style='width: 10%;text-align: center'>" + fila + "</td>"
                                    + "<td  style='width: 45%;text-align: left'>" + concepto.toUpperCase() + "</td>"
                                    + "<td  style='width: 10%;text-align: center'><b><label> S/. " + item.montopen + "</label></b></td>"
                                    + "<td  style='width: 10%;text-align: center'><b><label> S/. " + item.montocob + "</label></b></td>"
                                    + "<td style='width: 15%;text-align: center'>" + fechaFormat(vfecha) + "</td>"
                                    + "<td  style='width: 10%;text-align: center'><img src='" + estado + "' title='" + msgpago + "' width='25px' heigth='25px' /> " + opcBoleta + "  </td>"
                                    + "</tr>";
                            $(nuevaFila).appendTo("#viewListado tbody");
                            fila++;
                        });
                    } else {
                        var nuevaFila = '';
                        nuevaFila =
                                "<tr>"
                                + "<td colspan='6'><center>No se encontraron Registros</center></td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#viewListado tbody");
                    }
                    $("#mensaje").html("");
                }
            });
        });

    });

    function printBoleta(vBoleta) {
        //alert("Generando Boleta Nº "+vBoleta);
        var cadCombo = $('#cbalumno').val().split("|");
        $("#htxtsalon").val(cadCombo[0]);
        $("#htxtalumno").val(cadCombo[1]);
        $("#htxtnumrecibo").val(vBoleta);
        $("#frmAsistencia").attr("action", "<?= BASE_URL ?>swp_pagos/printTicketV2/");
        $("#frmAsistencia").submit();
    }

    function validaPost() {
        if ($("#cbalumno").val() == '0') {
            alert("Seleccione el Alumno");
            return false;
        }
    }

    function cambiaHijo(valor) {
        if (valor == 0)
        {
            $("#viewListado tbody").html("");
            var nuevaFila = '';
            nuevaFila =
                    "<tr>"
                    + "<td colspan='6'><center>No se encontraron Registros</center></td>"
                    + "</tr>";
            $(nuevaFila).appendTo("#viewListado tbody");
        }
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
</script>