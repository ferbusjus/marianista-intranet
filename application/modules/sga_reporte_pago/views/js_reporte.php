

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
            $("#cbaula").append("<option value='0'>::::: Todos :::::</option>");
            $.getJSON("<?= BASE_URL ?>sga_reporte_pago/lstgrado/" + this.value, function (data) {
                $("#cbgrado").append("<option value='0'>::::: Todos :::::</option>");
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
            $.getJSON("<?= BASE_URL ?>sga_reporte_pago/lstaula/" + vnivel + "/" + this.value, function (data) {
                $("#cbaula").append("<option value='0'>::::: Todos :::::</option>");
                $.each(data, function (i, item) {
                    $("#cbaula").append("<option value=\"" + item.id + "\">" + item.id + " : " + item.value + "</option>");
                });
            });
        });
        // ================================================================================================
        $("#btnPrint").click(function ()
        {
            var vnivel = $("#cbnivel").val();
            var vmes = $("#cbmes").val();
            
            if (vnivel == '0') {
                alert("Seleccione el Nivel");
                return false;
            }
            if (vmes == '0') {
                alert("Seleccione el Mes");
                return false;
            }            
            $("#frmReportePagos").attr("action", "<?= BASE_URL ?>sga_reporte_pago/printpagos/");
            $("#frmReportePagos").submit();
        });

        $("#btnExcel").click(function ()
        {
            var vnivel = $("#cbnivel").val();
            var vmes = $("#cbmes").val();
            
            if (vnivel == '0') {
                alert("Seleccione el Nivel");
                return false;
            }
            if (vmes == '0') {
                alert("Seleccione el Mes");
                return false;
            }            
            $("#frmReportePagos").attr("action", "<?= BASE_URL ?>sga_reporte_pago/printExcel/");
            $("#frmReportePagos").submit();
        });
        
    });
</script>