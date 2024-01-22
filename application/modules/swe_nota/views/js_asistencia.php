

<script >
    var save_method;
    var table;
            var vbimestre =3;
            var vunidad = 6;
    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });

        $("#btnDetalles").click(function ()
        {
            var cadCombo = $('#cbalumno').val().split("|");
            var vIdBimestre = $('#cbbimestre').val();
            var vIdUnidad = $('#cbunidad').val();
            var vIdNemo = cadCombo[0]; // Nemo
            var vIdAlumno = cadCombo[1]; // Alumno

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "<?= BASE_URL ?>swe_nota/getNotasBimestre",
                data: "vIdBimestre=" + vIdBimestre + "&vIdUnidad=" + vIdUnidad + "&vIdNemo=" + vIdNemo + "&vIdAlumno=" + vIdAlumno,
                success: function (dataJson) {
                    var data = dataJson['dataNotas'].total;
                    $("#viewResumen tbody").html("");
                    if (data > 0) {
                        $.each(data, function (i, row) {
                            var prom = ((data[i].n1 + data[i].n2) / 2);
                            var nuevaFila =
                                    "<tr>"
                                    + "<td >" + data[i].cursocod + "</td>"
                                    + "<td >" + data[i].cursonom + "</td>"
                                    + "<td >" + data[i].n1 + "</td>"
                                    + "<td >" + data[i].n2 + "</td>"
                                    + "<td >" + prom + "</td>"
                                    + "</tr>";
                            $(nuevaFila).appendTo("#viewResumen tbody");
                        });
                       
                    } else {
                        var nuevaFila =
                                "<tr>"
                                + "<td colspan='5'><center>No se encontraron Registros</center></td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#viewResumen tbody");
                    }
                     $('#ModalVerDetalleNotas').modal('show');
                }
            });

        });

        $("#btnBuscar").click(function ()
        {
            if ($("#cbalumno").val() == '0') {
                alert("Seleccione el Alumno.");
                return false;
            }

            /*if ($("#cbbimestre").val() == '0') {
                alert("Seleccione el Bimestre.");
                return false;
            }

            if ($("#cbunidad").val() == '0') {
                alert("Seleccione el Unidad.");
                return false;
            }*/
        
            // <INI> CMB001 : Se agrega validacion para la unidad 4 y 8 
            /*
            console.log("Bimestre : " + $("#cbbimestre").val());
            console.log("Unidad : " + $("#cbunidad").val());
            if ($("#cbbimestre").val() <= 2 && $("#cbunidad").val() < 4) {
                var vMes = parseInt($("#cbunidad").val()) + 2;
            } else if ($("#cbbimestre").val() == 2 && $("#cbunidad").val() == 4) { // Deben de pagar Junio y Julio
                var vMes = parseInt($("#cbunidad").val()) + 3;
            } else if ($("#cbbimestre").val() == 4 && $("#cbunidad").val() == 8) { // Deben de pagar Noviembre y Diciembre
                var vMes = parseInt($("#cbunidad").val()) + 4;
            } else {
                var vMes = parseInt($("#cbunidad").val()) + 3;
            }
            var vunidad = $("#cbunidad").val();
            */
            // <FIN> CMB001 
            // 

            if (vbimestre <= 2 && vunidad < 4) {
                var vMes = vunidad + 2;
            } else if (vbimestre == 2 && vunidad== 4) { // Deben de pagar Junio y Julio
                var vMes = vunidad + 3;
            } else if (vbimestre == 4 && vunidad == 8) { // Deben de pagar Noviembre y Diciembre
                var vMes = vunidad + 4;
            } else {
                var vMes = vunidad + 3;
            }
            //alert(vMes); return false;
            $("#divConducta").css("display", "none");
            var desMes = verMesPago(vMes);
            var cadCombo = $('#cbalumno').val().split("|");
            var vId = cadCombo[1]; // campo idalumno
            var vNivel = cadCombo[3]; // campo instrucod
            console.log("Codigo :" + vId);
            var token = $('#token').val()
            /*if (vId == '') {
             alert("NO SE ENCONTRO CODIGO DE PAGO ASIGNADO AL ALUMNO");
             return false;
             }*/
            console.log("Mes : " + vMes);
            // ---- Enviando parametros por ajax ---->
            $.ajax({
                //type: "GET",
                type: "POST",
                dataType: "json",
                //dataType: "jsonp",
                // jsonp: 'callback',
                url: "<?= BASE_URL ?>swe_pago/getpagos",
                //url: "http://www.marianista.pe/getpagos/getpagos.php",
                data: "vid=" + vId + "&vMes=" + vMes + "&vAcc=2&token="+token,
                //data: "id=201500011&vMes="+vMes+"&vAcc=2",
                success: function (dataJson) {
                    var data = dataJson['data'][0].total;
                    console.log("Total :" + data);
                    if (data >= 1 /*|| vId == '1276'*/) { // Si trae data ó es codigo de Mafer
                        //alert("+++++ EL ALUMNO YA PAGO +++++");
                        //alert(vunidad+"*"+vNivel);
                        console.log("Nivel :" + vNivel);
                        if (vNivel == 'I' || vNivel == 'P' || vNivel == 'S') {
                            // if((vunidad>=4 && vNivel=='S') || vNivel=='I' || vNivel=='P'){  /*vId=='1276' ||*/                                 
                            verBoleta();
                        } else {
                            listarNotas(vNivel);
                        }

                    } else if (data == 0) {
                        $("#divConducta").css("display", "none");
                        $("#viewListado tbody").html("");
                        var nuevaFila =
                                "<tr>"
                               + "<td colspan='4'><center style='font-size:14px'><b>Para poder ver la Boleta de Notas Online debe de estar al dia en sus Pagos . GRACIAS!</b></center></td>";
                                + "</tr>";
                        $(nuevaFila).appendTo("#viewListado tbody");
                        $('#divMes').html(desMes);
                        $('#ModalAlert').modal('show');
                    } else {
                        alert("Ocurrio un error Consultando al servicio de Pagos\nSalga del sistema y vuelva a ingresar.");
                    }
                }
            });
        });

    });

    // =============================================================>>>
    function verBoleta() {
        var cadCombo = $('#cbalumno').val().split("|");
        var objAsistencia = new Object();
        objAsistencia.vbimestre =vbimestre; //$('#cbbimestre').val();
        objAsistencia.vunidad =  vunidad; //$('#cbunidad').val();
        objAsistencia.vsalon = cadCombo[0];
        objAsistencia.valumno = cadCombo[1];
        $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Consultando Notas...</center></div></div>");
        var DatosJson = JSON.stringify(objAsistencia);
        $.post("<?= BASE_URL ?>swe_nota/listar",
                {
                    dataForm: DatosJson,
                    token: $('#token').val()
                },
                function (data, textStatus) {
                    var log = data['msg'];
                    var data = data['data'];
                    if (data.length > 0) {
                        $("#viewListado thead").empty();
                        $("#viewListado tbody").empty();
                        var nuevaFila = '';
                        $(nuevaFila).appendTo("#viewListado tbody");
                        nuevaFila =
                                "<tr>"
                                + "<td colspan='4'> <div style='width:100%; height: 1050;'><iframe width='100%' height='1000'  src='<?= BASE_URL ?>swe_nota/verBoleta' /> </div></td>"
                                + "</tr>";
                        $(nuevaFila).appendTo("#viewListado tbody");
                    } else {
                        if (log == 2) {
                            alert("La sesion a Expirado. Vuelva a ingresar al Sistema.");
                            window.location = "<?= BASE_URL ?>login";
                        } else {
                            var nuevaFila = '';
                            //$(nuevaFila).appendTo("#viewListado tbody");
                            nuevaFila =
                                    "<tr>"
                                    + "<td colspan='4'><center>No se encontraron Registros</center></td>"
                                    + "</tr>";
                            $(nuevaFila).appendTo("#viewListado tbody");
                        }
                    }
                    $("#mensaje").text("");
                },
                "json"
                );

        return false;
    }

    function listarNotas(vNivel) {
        var cadCombo = $('#cbalumno').val().split("|");
        var objAsistencia = new Object();
        objAsistencia.vbimestre = $('#cbbimestre').val();
        objAsistencia.vunidad = $('#cbunidad').val();
        objAsistencia.vsalon = cadCombo[0];
        objAsistencia.valumno = cadCombo[1];
        $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Consultando Notas...</center></div></div>");
        var DatosJson = JSON.stringify(objAsistencia);
        $.post("<?= BASE_URL ?>swe_nota/listar",
                {
                    dataForm: DatosJson,
                    token: $('#token').val()
                },
                function (data, textStatus) {
                    var log = data['msg'];
                    var data = data['data'];
                    if (data.length > 0) {
                        var nuevaFila = '';
                        $(nuevaFila).appendTo("#viewListado tbody");
                        /*if($('#cbunidad').val() > 4 && (vNivel='I')){
                         $("#viewListado tbody").html("");
                         nuevaFila =
                         "<tr>"
                         + "<td colspan='4'><center style='font-size:16px'><b>Ud. Se encuentra al día en sus pagos pero las Notas de la 5º Unidad se publicarán <br> después de la Reunión. GRACIAS!</b></center></td>"
                         + "</tr>";
                         $(nuevaFila).appendTo("#viewListado tbody");                             
                         }else*/
                        if ($('#cbunidad').val() > 6) {
                            $("#viewListado tbody").html("");
                            nuevaFila =
                                    "<tr>"
                                    + "<td colspan='4'><center style='font-size:16px'><b>Ud. Se encuentra al día en sus pagos pero las Notas de la 7º Unidad se publicarán <br> después de la Reunión. GRACIAS!</b></center></td>"
                                    + "</tr>";
                            $(nuevaFila).appendTo("#viewListado tbody");
                        } else {
                            $("#divConducta").css("display", "block");
                            $("#viewListado tbody").html("");
                            $.each(data, function (i, item) {
                                var vprom = ((item.instrucod == 'S') ? item.pb : item.pf);
                                var prom = ((vprom == null) ? '-' : vprom);
                                var colProm = '';
                                if (item.pf != null && item.pf > 10) {
                                    colProm = 'style="color:blue"';
                                } else if (item.pf != null && item.pf <= 10) {
                                    colProm = 'style="color:red"';
                                }
                                var colCond = '';
                                if (item.conducta != null && item.conducta > 10) {
                                    colCond = 'style="color:blue"';
                                } else if (item.conducta != null && item.conducta <= 10) {
                                    colCond = 'style="color:red"';
                                }

                                var cond = ((item.conducta == null) ? '-' : item.conducta);
                                $("#divNota").html("&nbsp;" + cond + "&nbsp;");
                                var nuevaFila =
                                        "<tr>"
                                        + "<td style='width: 10%;text-align: center'>" + item.cursocod + "</td>"
                                        + "<td  style='width: 40%;text-align: left'><span class='glyphicon glyphicon-user' title='PROFESOR(A)'></span>&nbsp;" + item.nomcomp + "</td>"
                                        + "<td  style='width: 40%;text-align: center'><span class='glyphicon glyphicon-book' title='CURSO'></span>&nbsp;" + item.cursonom + "</td>"
                                        + "<td  style='width: 10%;text-align: center'><b><label " + colProm + " >" + prom + "</label></b></td>"

                                        + "</tr>";
                                $(nuevaFila).appendTo("#viewListado tbody");
                            });
                        }
                    } else {
                        if (log == 2) {
                            alert("La sesion a Expirado. Vuelva a ingresar al Sistema.");
                            window.location = "<?= BASE_URL ?>login";
                        } else {
                            var nuevaFila = '';
                            //$(nuevaFila).appendTo("#viewListado tbody");
                            nuevaFila =
                                    "<tr>"
                                    + "<td colspan='4'><center>No se encontraron Registros</center></td>"
                                    + "</tr>";
                            $(nuevaFila).appendTo("#viewListado tbody");
                        }
                    }
                    $("#mensaje").text("");
                },
                "json"
                );

        return false;

    }

    function cargaUnidades(val) {
        if (val != 0) {
            $.ajax({
                async: true,
                type: "POST",
                dataType: "json",
                url: "<?= BASE_URL ?>swe_nota/lstUnidad",
                data: "bimecod=" + val,
                success: function (data) {
                    var $comboUnidad = $("#cbunidad");
                    $comboUnidad.empty();
                    $comboUnidad.append("<option value='0'>::::: Seleccione Unidad :::::</option>");
                    $.each(data, function (index, field) {
                        $comboUnidad.append("<option value='" + field.id + "'  >&nbsp; - " + field.valor + " - </option>");
                    });
                }
            });
        }
    }

    function validaPost() {
        if ($("#cbalumno").val() == '0') {
            alert("Seleccione el Alumno");
            return false;
        }

        if ($("#cbmes").val() == '0') {
            alert("Seleccione el Mes");
            return false;
        }
    }

    function verMes(vmes) {
        var dsc = '';
        switch (vmes) {
            case 1 :
                dsc = 'ENERO';
                break;
            case 2 :
                dsc = 'FEBRERO';
                break;
            case 3 :
                dsc = 'MARZO';
                break;
            case 4 :
                dsc = 'ABRIL';
                break;
            case 5 :
                dsc = 'MAYO';
                break;
            case 6 :
                dsc = 'JUNIO';
                break;
            case 7 :
                dsc = 'JULIO';
                break;
            case 8 :
                dsc = 'AGOSTO';
                break;
            case 9 :
                dsc = 'SETIEMBRE';
                break;
            case 10 :
                dsc = 'OCTUBRE';
                break;
            case 11 :
                dsc = 'NOVIEMBRE';
                break;
            case 12 :
                dsc = 'DICIEMBRE';
                break;
        }
        return dsc;
    }

    function verMesPago(vmes) {
        var dsc = '';
        switch (vmes) {
            case 3 :
                dsc = 'MARZO';
                break;
            case 4 :
                dsc = 'ABRIL';
                break;
            case 5 :
                dsc = 'MAYO';
                break;
            case 7 :
                dsc = 'JUNIO / JULIO';
                break;
            case 8 :
                dsc = 'AGOSTO';
                break;
            case 9 :
                dsc = 'SETIEMBRE';
                break;
            case 10 :
                dsc = 'OCTUBRE';
                break;
            case 12 :
                dsc = 'NOVIEMBRE / DICIEMBRE';
                break;
        }
        return dsc;
    }

</script>