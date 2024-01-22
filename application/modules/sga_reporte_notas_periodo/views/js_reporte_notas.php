<script type="text/javascript" >
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });
                
       $("#cbaula").change(function ()
        {
            if($(this).val() !=""){
                $('#cbperiodo').attr("disabled", false);
            } else {
                $('#cbperiodo').val("");
                $('#cbperiodo').attr("disabled", true);
            }
            
        });
                                
        $("#btnGenerar").click(function ()
        {
            var ano = $('#anio').val();
            if ($("#cbperiodo").val() === "") {
                alert("Debe de Seleccionar el Periodo");
                return false;
            }      
            if ($("#cbperiodo").val() > 4) {
                alert("Aún no esta Habilitado el periodo Seleccionado.");
                return false;
            }            
            $("#flgGenerar").val("1");
             $("#frmReporteBoletas").attr("action", "<?= BASE_URL ?>sga_reporte_notas_periodo/reporte_bimestre/");
             $("#frmReporteBoletas").submit();
        });
        
         setInterval(function () {
            $("#divComunicado").hide();
        }, 12000);
});
</script>