

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        /*$.fn.datepicker.dates['es'] = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
            daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy"
        };

        $("#finicial").datepicker({
            dateFormat: 'dd/mm/yy',
            language: 'es',
            showToday: true,
            autoclose: true
        });*/
        // ================================================================================================
        $("#btnPrintPdf").click(function ()
        {
            if ($.trim($("#cbmes").val()) == '') {
                alert("Seleccione la Mes");
                return false;
            }
            $("#frmReporteCaja").attr("action", "<?= BASE_URL ?>sga_reporte_periodo/printpagos/");
            $("#frmReporteCaja").submit();
        });
        
        $("#btnPrintXls").click(function ()
        {
            if ($.trim($("#cbmes").val()) == '') {
                alert("Seleccione la Mes");
                return false;
            }
           // $("#frmReporteCaja").attr("action", "<?= BASE_URL ?>sga_reporte_periodo/printcartascobro/");
            $("#frmReporteCaja").attr("action", "<?= BASE_URL ?>sga_reporte_periodo/printexcel/");
            $("#frmReporteCaja").submit();
        });
        
        
    });
</script>