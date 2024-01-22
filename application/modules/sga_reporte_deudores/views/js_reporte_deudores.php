

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        
        $("#btnPrintXls").click(function ()
        {
            if ($.trim($("#cbmes").val()) == '') {
                alert("Seleccione la Mes");
                return false;
            }
            $("#frmReporteCaja").attr("action", "<?= BASE_URL ?>sga_reporte_deudores/printexceldeudores/");
            $("#frmReporteCaja").submit();
        });
        
        
    });
</script>