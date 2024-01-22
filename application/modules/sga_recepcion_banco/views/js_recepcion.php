<script  >
    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        // ================================================================================================
        $("#frmAsistencia").submit(function (e) {
            // e.preventDefault();
            var form = $(this);
            var vIdBanco = $('#cbBanco').val();
            var vIdRazon = $('#cbRazon').val();
            var vimg = $('#txtfile').val();

            if (vIdBanco == '') {
                alert("Seleccione el Modelo del Banco a Generar.");
                return false;
            }
            if (vIdRazon == '') {
                alert("Seleccione la Rozon Social.");
                return false;
            }
            if (vimg == '') {
                alert("Seleccione el archivo a cargar.");
                return false;
            }

            if (vIdBanco == 'B2') {
                var msg = window.confirm("El archivo cargado se validará con los pagos pendientes del año " +<?= $ano ?>+".\nSi los pagos corresponden a otro año cambielo en Configuración/Cambio de Año.\n\nRecuerde que al dar Aceptar se generarán las boletas por cada registro y el proceso sera irreversible.\nEsta Seguro de continuar con el proceso?");
                if (msg) {
                    $('#btnProcesar').attr("disabled", true);
                    $("#btnProcesar").text('Generando..');
                    activarcarga();
                } else {
                    e.preventDefault();
                }
            } else {
                alert("El Tipo de Banco Seleccionado no esta Habilitado para este Colegio.");
            }
        });
    });

    function activarcarga() {
        $("#divLoading").css("display", "block")
        $("#divLoading").html("");
        $("#divLoading").html("<b>Validando y procesando información. Un momento por favor...</b><img style='width:26px;height:18px;'  src='images/loader.gif' />");
    }

    function terminoCarga(ret, caderrors) {
        $("#divLoading").html("");
        /*  console.log(ret);
         console.log(caderrors);*/
        var html = "";
        if (ret == 1) {
            html += '<table class="table table-striped table-bordered no-footer dataTable"    id="viewPagos" style="width: 100%">';
            html += '<thead>';
            html += '<tr class="tableheader">';
            html += '<th style="width: 5%;text-align: center">Item</th>';
            html += ' <th style="width: 25%;text-align: center">Dato del Alumno</th>';
            html += ' <th style="width: 10%;text-align: center">Fecha Pago</th>';
            html += ' <th style="width: 10%;text-align: center">Voucher</th>';
            html += ' <th style="width: 10%;text-align: center">Periodo</th>';
            html += '<th style="width: 10%;text-align: center">Boleta</th>';
            html += '<th style="width: 20%;text-align: center">Mensaje</th>';
            html += '<th style="width: 10%;text-align: center">Status</th>';
            html += '</tr>';
            html += '<thead>';
            html += '<tbody>';
            if (caderrors.logAuditoria.length > 0) {
                // console.log("Total :" + caderrors.logAuditoria.length);
                for (var i = 0; i < caderrors.logAuditoria.length; i++) {
                    if (caderrors.logAuditoria[i].status == 'ERROR') {
                        var color = "red";
                        var bold = "bold";
                    } else {
                        var color = "";
                         var bold ="";
                    }
                    html += '<tr style="color:' + color + '; font-weight:'+bold+';">';
                    html += '<td style="text-align: center">' + (i + 1) + '</td>';
                    html += ' <td style="text-align: left"> ' + caderrors.logAuditoria[i].alumno + '</td>';
                    html += ' <td style="text-align: center"> ' + caderrors.logAuditoria[i].fecha + ' </td>';
                    html += ' <td style="text-align: center"> ' + caderrors.logAuditoria[i].voucher + ' </td>';
                    html += ' <td style="text-align: center"> ' + caderrors.logAuditoria[i].periodo + ' </td>';
                    html += '<td style="text-align: center"> ' + caderrors.logAuditoria[i].recibo + ' </td>';
                    html += '<td style="text-align: left"> ' + caderrors.logAuditoria[i].mensaje + ' </td>';
                    html += '<td style="text-align: center"><b> ' + caderrors.logAuditoria[i].status + '</b></td>';
                    html += '</tr>';
                }
            } else {
                html += '<tr >';
                html += '<td style="text-align: center" colspan="6">NO EXISTE NINGUN REGISTRO.</td>';
                html += '</tr>';
            }
            html += ' </tbody>';
            html += '</table>';
            $("#divTblPagos").html(html);
            /* if (caderrors !== '') {
             var arrerror = caderrors.split("*");
             var cadena = "";
             for (i = 0; i < (arrerror.length - 1); i++) {
             cadena += "- " + arrerror[i] + "\n";
             }
             $("#divLoading").html("<b>Archivo cargado pero tiene algunas observaciones </b><img style='width:25px;height:25px;'  src='img/advertencia.jpg' />");
             alert("Archivo procesado pero contiene los siguientes observaciones :\n" + cadena);
             } else {
             $("#divLoading").html("<b>Archivo cargado y procesado Correctamente. </b><img style='width:25px;height:25px;'  src='img/pagado.png' />");
             alert("Archivo procesado Correctamente ..!!");
             }*/
            // location.reload();
        } else if (ret == '2') {
            $("#divLoading").html("<b>El archivo que intenta procesar corresponde a otro Nivel. Vuelva intentarlo </b><img style='width:25px;height:25px;'  src='img/delete.png' />");
        } else {
            $("#divLoading").html("<b>Hubo un error al Cargar el archivo. Vuelva intentarlo </b><img style='width:25px;height:25px;'  src='img/delete.png' />");
        }
        $('#txtfile').val('');
        $('#btnProcesar').attr("disabled", false);
        $("#btnProcesar").text('Procesar');
    }
</script>