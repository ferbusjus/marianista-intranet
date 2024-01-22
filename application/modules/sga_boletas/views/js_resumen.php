<script type="text/javascript" >
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });

        $("#cbnivel").change(function ()
        {
            var nivel = $(this).val();
            var data = {
                nivel: nivel
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_boletas/getGrado/",
                type: "POST",
                dataType: "json",
                data: data,
                beforeSend: function () {
                    $("#cbgrado").empty();
                    $("#cbgrado").append("<option value=''>CARGANDO... </option>");
                },
                success: function (json)
                {
                    if (json.data.length > 0) {
                        $('#cbgrado').attr("disabled", false);
                        $('#cbbimestre').attr("disabled", false);

                        $("#cbgrado").empty();
                        $("#cbgrado").append("<option value=''>::: SELECCIONE :::</option>");
                        $.each(json.data, function (i, item) {
                            $("#cbgrado").append("<option value=\"" + item.id + "\">" + item.value + "</option>");
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error interno. Comuniquese con el Administrador.');
                }
            });

        });

        $("#btnGenerar").click(function ()
        {
            if ($("#cbbimestre").val() >3) {
                alert("Aún no esta Habilitado el Bimestre Seleccionado.");
                return false;
            }
            if ($("#cbnivel").val() === "") {
                alert("Debe de Seleccionar el Nivel");
                return false;
            }
            if ($("#cbgrado").val() === "") {
                alert("Debe de Seleccionar el Grado");
                return false;
            }
            if ($("#cbbimestre").val() === "") {
                alert("Debe de Seleccionar el Bimestre");
                return false;
            }


            $("#frmResumen").attr("action", "<?= BASE_URL ?>sga_boletas/generarResumenBimestre");
            $("#frmResumen").submit();
        });

    });



</script>