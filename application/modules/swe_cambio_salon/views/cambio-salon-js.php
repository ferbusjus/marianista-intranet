<script src="<?php echo base_url () ?>assets/jquery/jquery-2.1.4.min.js"></script>
<script src="<?php echo base_url () ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url () ?>assets/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url () ?>assets/datatables/js/dataTables.bootstrap.js"></script>

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });

        $("#cbsalon").change(function ()
        {
            var field = this.value;
            if (field != 0) {
                var campo = field.split("-");
                var vnemo = campo[0];
                var vnivel = campo[1];
                var vgrado = campo[2];
                $("#cbalumno").empty();
                $.getJSON("<?= BASE_URL ?>sga_asistencia/lstalumno/" + vnemo, function (data) {
                    $("#cbalumno").append("<option value='0'>::::::::::::::::::::::::::: Seleccione Alumno ::::::::::::::::::::::::::::</option>");
                    $.each(data, function (i, item) {
                        $("#cbalumno").append("<option value=\"" + item.ALUCOD + "\">" + item.ALUCOD + " : " + item.NOMCOMP + "</option>");
                    });
                    cargarAulasMigrar(vnivel, vgrado);
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

    function cargarAulasMigrar(vnivel, vgrado)
    {

        var arrdata = {
            vnivel: vnivel,
            vgrado: vgrado
        };
        $.ajax({
            url: "<?= BASE_URL ?>swe_cambio_salon/cargaAulaMigra/",
            type: "POST",
            dataType: "json",
            data: arrdata,
            success: function (data)
            {
                //$("#cbsalonDestino").removeAttr("disabled");
                $("#cbsalonDestino").empty();
                $("#cbsalonDestino").append("<option value='0'>:::::::::::::::::::::::: Seleccione Salon Destino  :::::::::::::::::::::::::</option>");
                $.each(data, function (i, item) {
                    $("#cbsalonDestino").append("<option value=\"" + item.NEMO + "\">" + item.NEMO + " : " + item.NEMODES + "</option>");
                });
            }
        });

    }
</script>