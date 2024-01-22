

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });
        $.fn.datepicker.dates['es'] = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
            daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy"
        };

        $("#finicial,#ffinal").datepicker({
            dateFormat: 'dd/mm/yy',
            language: 'es',
            showToday: true,
            autoclose: true
        });
        // ================================================================================================
        $("#btnPrint").click(function ()
        {
            if ($.trim($("#finicial").val()) == '') {
                alert("Seleccione la Fecha Inicial");
                return false;
            }
            if ($.trim($("#ffinal").val()) == '') {
                alert("Seleccione la Fecha Final");
                return false;
            }
            if ($.trim($("#cbrazon").val()) == '') {
                alert("Seleccione la Razon Social");
                return false;
            }
            if ($("#cbrazon").val() == 'T') {
                alert("La Generacion del Reporte de las 2 Razones Sociales esta solo Permitido en Formato Excel.");
                return false;
            }

            $("#frmReporteGeneral").attr("action", "<?= BASE_URL ?>sga_reporte_caja/printResumen");
            $("#frmReporteGeneral").submit();
        });

        $("#btnExcel").click(function ()
        {
            if ($.trim($("#finicial").val()) == '') {
                alert("Seleccione la Fecha Inicial");
                return false;
            }
            if ($.trim($("#ffinal").val()) == '') {
                alert("Seleccione la Fecha Final");
                return false;
            }
            if ($.trim($("#cbrazon").val()) == '') {
                alert("Seleccione la Razon Social");
                return false;
            }

            $("#frmReporteGeneral").attr("action", "<?= BASE_URL ?>sga_reporte_caja/printExcelResumen");
            $("#frmReporteGeneral").submit();
        });

    });
</script>