<script type="text/javascript" >
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });
        $('[data-toggle="tooltip"]').tooltip();
        mayuscula("input#txtSearch");

        $("#cbaula").change(function ()
        {
            var nemo = $(this).val();
            console.log(nemo);
            var data = {
                nemo: nemo
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_boletas/getAlumnos/",
                type: "POST",
                dataType: "json",
                data: data,
                beforeSend: function () {
                    $("#cbalumno").empty();
                    $("#cbalumno").append("<option value=''>CARGANDO... </option>");
                },
                success: function (json)
                {
                    if (json.data.length > 0) {
                        $('#cbalumno').attr("disabled", false);
                        $('#cbperiodo').attr("disabled", false);
                        $("#cbalumno").empty();
                        $("#cbalumno").append("<option value='T'>::: TODOS :::</option>");
                        $.each(json.data, function (i, item) {
                            $("#cbalumno").append("<option value=\"" + item.id + "\">" + item.id + " - " + item.value + "</option>");
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error interno. Comuniquese con el Administrador.');
                }
            });

        });

        $("#cbperiodo").change(function ()
        {
            var ano = $('#anio').val();
            if (ano < 2021)
                return false;
            var bimestre = $(this).val();
            var data = {
                bimestre: bimestre
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_boletas/getUnidad/",
                type: "POST",
                dataType: "json",
                data: data,
                beforeSend: function () {
                    $("#cbunidad").empty();
                    $("#cbunidad").append("<option value=''>CARGANDO... </option>");
                },
                success: function (json)
                {
                    if (json.data.length > 0) {
                        $('#cbunidad').attr("disabled", false);
                        $("#cbunidad").empty();
                        $("#cbunidad").append("<option value=''>::: SELECCIONE :::</option>");
                        $.each(json.data, function (i, item) {
                            $("#cbunidad").append("<option value=\"" + item.id + "\">" + item.value + "</option>");
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
            var ano = $('#anio').val();
            if ($("#cbaula").val() === "") {
                alert("Debe de Seleccionar el Aula");
                return false;
            }
            /* if ($("#cbalumno").val() === "") {
             alert("Debe de Seleccionar el Alumno");
             return false;
             }*/
            if ($("#cbperiodo").val() === "") {
                alert("Debe de Seleccionar el Bimestre");
                return false;
            }
            if (ano >= 2023) {
                if ($("#cbunidad").val() === "") {
                    alert("Debe de Seleccionar la Unidad"); 
                    return false;
                }
                if ($("#cbperiodo").val() > 4) {
                    alert("Aún no esta Habilitado el Bimestre Seleccionado.");
                    return false;
                }
                console.log("================================>>>>>>>>>>"+$("#cbunidad").val());
                
                if ($("#cbunidad").val() > 8) {
                    alert("Aún no esta Habilitado la Unidad Seleccionado.");
                    return false;
                }
            }

            $("#flgGenerar").val("0");
            if (ano < 2021) {
                if ($("#cbaula").val() >= 2020001 && $("#cbaula").val() <= 2020004 && $("#cbaula").val() != 2020002) { // Inicial 3 años
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial3/");
                } else if ($("#cbaula").val() >= 2020005 && $("#cbaula").val() <= 2020007) { // Inicial 4 años
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial4/");
                } else if ($("#cbaula").val() >= 2020009 && $("#cbaula").val() <= 2020011) { // Inicial 5 años
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial5/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboleta/");
                }
            } else if (ano == 2021) {
                if ($("#cbaula").val() == '2021004') { // Inicial 3 años
                    //$("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial3unidad/");
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else if ($("#cbaula").val() == '2021005' || $("#cbaula").val() == '2021006') { // Inicial 4 años  
                    //$("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial4unidad/");
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else if ($("#cbaula").val() == '2021009' || $("#cbaula").val() == '2021011') { // Inicial 5 años       
                    //$("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial5unidad/");
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletaunidades/");
                }
            } else if (ano == 2022) {
                if ($("#cbaula").val() <= '2022009' || $("#cbaula").val()=='2022067') {
                     $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletaunidades/");
                }
			} else {
				if ($("#cbaula").val() <= '2023013' ) {
                     $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicialGeneric/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletaunidades/");
                }
			}
            
            $("#frmBoletas").submit();

        });

        $("#btnPublicar").click(function ()
        {
            var ano = $('#anio').val();
            if ($("#cbaula").val() === "") {
                alert("Debe de Seleccionar el Aula");
                return false;
            }
            /* if ($("#cbalumno").val() === "") {
             alert("Debe de Seleccionar el Alumno");
             return false;
             }*/
            if ($("#cbperiodo").val() === "") {
                alert("Debe de Seleccionar el Bimestre");
                return false;
            }
            if (ano >= 2023) {
                if ($("#cbunidad").val() === "") {
                    alert("Debe de Seleccionar la Unidad");
                    return false;
                }
                if ($("#cbperiodo").val() > 4) {
                    alert("Aún no esta Habilitado el Bimestre Seleccionado.");
                    return false;
                }
                if ($("#cbunidad").val() > 8) {
                    alert("Aún no esta Habilitado la Unidad Seleccionado.");
                    return false;
                }
            }

            $("#flgGenerar").val("1");
            if (ano < 2021) {
                if ($("#cbaula").val() >= 2020001 && $("#cbaula").val() <= 2020004 && $("#cbaula").val() != 2020002) { // Inicial 3 años
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial3/");
                } else if ($("#cbaula").val() >= 2020005 && $("#cbaula").val() <= 2020007) { // Inicial 4 años
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial4/");
                } else if ($("#cbaula").val() >= 2020009 && $("#cbaula").val() <= 2020011) { // Inicial 5 años
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial5/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboleta/");
                }
            } else if (ano == 2021) {
                if ($("#cbaula").val() == '2021004') { // Inicial 3 años
                    //$("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial3unidad/");
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else if ($("#cbaula").val() == '2021005' || $("#cbaula").val() == '2021006') { // Inicial 4 años  
                    //$("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial4unidad/");
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else if ($("#cbaula").val() == '2021009' || $("#cbaula").val() == '2021011') { // Inicial 5 años       
                    //$("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletainicial5unidad/");
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletaunidades/");
                }
            } else if (ano == 2022){
                if ($("#cbaula").val() <= '2022009' || $("#cbaula").val()=='2022067') {
                     $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicial/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletaunidades/");
                }
            }else {
				if ($("#cbaula").val() <= '2023013' ) {
                     $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/plantillaboletainicialGeneric/");
                } else {
                    $("#frmBoletas").attr("action", "<?= BASE_URL ?>sga_boletas/generarboletaunidades/");
                }
			}
            $("#frmBoletas").submit();
        });


        setInterval(function () {
            $("#divComunicado").hide();
        }, 12000);

    });

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }

</script>