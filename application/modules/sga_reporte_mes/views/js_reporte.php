

<script  >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        // ================================================================================================

        $("#btnPrint").click(function ()
        { 
            var vmes = $("#cbmes").val();
            if (vmes == '') {
                alert("Seleccione el Mes");
                return false;
            }
            $("#frmReporteMes").attr("action", "<?= BASE_URL ?>sga_reporte_mes/print_rmes/");
            $("#frmReporteMes").submit();
        });
        
        
    });
</script>