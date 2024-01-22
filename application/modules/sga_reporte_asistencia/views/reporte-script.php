<script type="text/javascript">
    $(function () {
        $("#finicial").datepicker({dateFormat: 'dd/mm/yy'});
        $("#ffinal").datepicker({dateFormat: 'dd/mm/yy'});

        $("#BtnReporte").click(function ()
        {
            var finicial = $('input#finicial').val();
            var ffinal = $('input#ffinal').val();
            if (finicial == "") {
                alert("Elige la Fecha Inicial");
                $("#finicial").focus();
                return false;
            } else if (ffinal == "") {
                alert("Elige la Fecha Final");
                $("#ffinal").focus();
                return false;
            } else if ((Date.parse(finicial)) > (Date.parse(ffinal))) {
                alert("La Fecha Inicial no puede ser mayor que la Fecha Final");
                $("#finicial").focus();
                return false;
            } else if ($("#cbsalon").val() == '0') {
                alert("Seleccione el Salon.");
                return false;
            } else {
                $("#frmReporte").submit();
            }

        });


    });
</script> 