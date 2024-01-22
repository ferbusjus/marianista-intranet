<script  >
    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        // ================================================================================================
        /*$('#cbPeriodo').multiselect({
         nonSelectedText: 'SELECCIONE',
         enableFiltering: true,
         enableCaseInsensitiveFiltering: true,
         buttonWidth: '100%'
         });*/

        $("#btnDownload").click(function ()
        {
            var vIdBanco = $('#cbBanco').val();
            var vIdRazon = $('#cbRazon').val();
            var vIdTipo = $('#cbTipo').val();
            var vCadena = $('#hcadena').val();
            var vIdPeriodo = $('#cbPeriodo').val();

            if (vIdBanco == '') {
                alert("Seleccione el Modelo del Banco a Generar.");
                return false;
            }
            if (vIdRazon == '') {
                alert("Seleccione la Razon Social.");
                return false;
            }
            if (vIdTipo == '') {
                alert("Seleccione el Tipo.");
                return false;
            }

            var parametros = {
                vBanco: vIdBanco,
                vCadena: vCadena,
                vIdTipo: vIdTipo,
                vIdPeriodo:vIdPeriodo
            };
            $('#btnDownload').attr("disabled", true);
            $("#btnDownload").text('Descargando..');
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>sga_envio_banco/generarFile",
                data: parametros,
                dataType: "html",
                beforeSend: function (objeto) {
                    $('#divMensaje').show();
                    $('#divMensaje').html('&nbsp;&nbsp;<img src="<?= BASE_URL ?>images/loader.gif" width="25px" height="20px"  />&nbsp;<b>Descargando.....</b>');
                },
                success: function (data) {
                    $("#btnDownload").text('Descargar');
                    $('#btnDownload').attr("disabled", true);
                    $('#btnGenerar').attr("disabled", false);
                    $("#hcadena").val("");
                    $('#divMensaje').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error interno, Comuniquese con el Administrador : \nE-mail : info@sistemas-dev.com');
                    $("#btnDownload").text('Descargar');
                    $('#btnDownload').attr("disabled", false);
                }
            });

        });
        
        $("#cbTipo").change(function ()
        {
            $('#cbPeriodo').val("");
            //$('#fini').val("");
            //$('#ffin').val("");
            $('#cbPeriodo').val("");
            if($(this).val()=="01"){
                $('#cbPeriodo').attr("disabled",true);
                $('#fini').attr("disabled",false);
                $('#ffin').attr("disabled",false);
            } 
            if($(this).val()=="03"){
                $('#cbPeriodo').attr("disabled",false);
                $('#fini').attr("disabled",true);
                $('#ffin').attr("disabled",true);
            }             
        });

        $("#btnGenerar").click(function ()
        {
            var vIdBanco = $('#cbBanco').val();
            var vIdRazon = $('#cbRazon').val();
            var vIdTipo = $('#cbTipo').val();
            var vIdPeriodo = $('#cbPeriodo').val();
            var vfini = $('#fini').val();
            var vffin = $('#ffin').val();

            if (vIdBanco == '') {
                alert("Seleccione el Modelo del Banco a Generar.");
                return false;
            }
            if (vIdBanco == 'B2') { // Banco permitido para Marianista

                if (vIdRazon == '') {
                    alert("Seleccione la Rozon Social.");
                    return false;
                }
                if (vIdRazon == '02') {
                    alert("Rozon Social no permitida para este Banco");
                    return false;
                }
                if (vIdTipo == '') {
                    alert("Seleccione el Tipo de Transaccion.");
                    return false;
                }
                if (vIdTipo == '03' && vIdPeriodo == '') {
                    alert("Seleccione el  Periodo a Generar el Cobro.");
                    return false;
                }
                //console.log(vIdPeriodo); return false;
                var parametros = {
                    vIdBanco: vIdBanco,
                    vIdRazon: vIdRazon,
                    vIdTipo: vIdTipo,
                    vfini: vfini,
                    vffin: vffin,
                    vIdPeriodo: vIdPeriodo
                };
                $('#btnGenerar').attr("disabled", true);
                $("#btnGenerar").text('Generando..');
                $.ajax({
                    type: "POST",
                    url: "<?= BASE_URL ?>sga_envio_banco/procesarFile",
                    data: parametros,
                    dataType: "json",
                    beforeSend: function (objeto) {
                        $('#divMensaje').show();
                        $('#divMensaje').html("");
                        $('#divMensaje').html('&nbsp;&nbsp;<img src="<?= BASE_URL ?>images/loader.gif" width="25px" height="20px"  />&nbsp;<b>Procesando.....</b>');
                    },
                    success: function (data) {
                        $("#btnGenerar").text('Generar');
                        $('#btnGenerar').attr("disabled", false);
                        if (data['flg'] == 1) { // Genero archivo correctamente
                            alert(data['msg']);
                            $("#hcadena").val(data['cadena']);
                            $('#btnGenerar').attr("disabled", true);
                            $('#btnDownload').attr("disabled", false);
                        } else {
                            alert(data['msg']);
                            console.log("Error : " + data['error']);
                        }
                        $('#divMensaje').html("");
                        $('#divMensaje').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $('#divMensaje').show();
                        alert('Error interno, Comuniquese con el Administrador : \nE-mail : info@sistemas-dev.com');
                        $("#btnGenerar").text('Generar');
                        $('#btnGenerar').attr("disabled", false);
                    }
                });
            } else {
                alert("El Tipo de Banco Seleccionado no esta Habilitado para MARIANISTA.");
            }
        });

    });
</script>