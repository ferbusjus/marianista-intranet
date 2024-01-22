

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        // ================================== Filtramos los Grados =========================================
        $("#cbnivel").change(function ()
        {
            $("#cbgrado").empty();
            $("#cbaula").empty();
            $("#cbaula").append("<option value=''>::::: Todos :::::</option>");
            $.getJSON("<?= BASE_URL ?>sga_extractor/lstgrado/" + this.value, function (data) {
                $("#cbgrado").append("<option value=''>::::: Todos :::::</option>");
                $.each(data, function (i, item) {
                    $("#cbgrado").append("<option value=\"" + item.id + "\">" + item.id + " : " + item.value + "</option>");
                });
            });
        });
        // ================================== Filtramos los Salones =========================================
        $("#cbgrado").change(function ()
        {
            var vnivel = $("#cbnivel").val();
            if (vnivel == '0') {
                alert("Seleccione el Nivel");
                return false;
            }
            $("#cbaula").empty();
            $.getJSON("<?= BASE_URL ?>sga_extractor/lstaula/" + vnivel + "/" + this.value, function (data) {
                $("#cbaula").append("<option value=''>::::: Todos :::::</option>");
                $.each(data, function (i, item) {
                    $("#cbaula").append("<option value=\"" + item.id + "\">" + item.id + " : " + item.value + "</option>");
                });
            });
        });

        $("#btnImprimir").click(function ()
        {
            var vnivel = $("#cbnivel").val();
            //var vreporte = $("#cbtiporeporte").val();
            /*  if (vnivel === '') {
             alert("Seleccione el Nivel");
             return false;
             }*/
            $("#frmReporteMatriculas").attr("action", "<?= BASE_URL ?>sga_extractor/printExcel/");
            $("#frmReporteMatriculas").submit();
            //alert("Imprimiendo reporte : "+vtipo);
        });

    });
    // ================== Funciones Generale ==============================

</script>