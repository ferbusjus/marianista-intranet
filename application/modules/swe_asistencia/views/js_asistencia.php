
<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });

        $("#btnBuscar").click(function ()
        {
            if ($("#cbalumno").val() == '0') {
                //alert("Seleccione el Alumno");
                bootbox.alert({
                    title: "AVISO!",
                    message: "Seleccione al Alumno!",
                    size: 'small'
                });
                return false;
            }
   
            if ($("#cbmes").val() == '0') {
                //alert("Seleccione el Mes");
                bootbox.alert({
                    title: "AVISO!",
                    message: "Seleccione el Mes!",
                    size: 'small'
                });
                return false;
            }
            var cadCombo = $('#cbalumno').val().split("|");
            var objAsistencia = new Object();
            objAsistencia.vtipo = $('select#cbtipo').val();
            objAsistencia.vmes = $('select#cbmes').val();
            objAsistencia.vsalon = cadCombo[0];
            objAsistencia.valumno = cadCombo[1];
            $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Listando Asistencias...</center></div></div>");
            var DatosJson = JSON.stringify(objAsistencia);
            $.post("<?= BASE_URL ?>swe_asistencia/listar",
                    {
                        dataForm: DatosJson,
                        token: $('#token').val()
                    },
                    function (data, textStatus) {
                        var log = data['msg'];
                        var data = data['data'];
                        $("#viewListado tbody").html("");
                        if (data.length > 0) {
                            var nuevaFila = '';
                            $.each(data, function (i, item) {
                                var evento = ((item.evento == 'I') ? 'Ingreso' : 'Salida');
                                var vasis = "";
                                var conducta = "";
                                var color = '';
                                conducta = item.conducta;
                                if (item.t_asist == '' && item.evento == "I") {
                                    vasis = 'FALTO';
                                } else if (item.t_asist == "V" && item.evento == "I") {
                                    vasis = 'VACACIONES';
                                    if (item.fecha == '15-05-2017' || item.fecha == '16-05-2017' || item.fecha == '17-05-2017' || item.fecha == '18-05-2017' || item.fecha == '19-05-2017')
                                        conducta = 'VACACIONES - II UNIDAD ( I BIMESTRE)';
                                    if (item.fecha == '31-07-2017' || item.fecha == '01-08-2017' || item.fecha == '02-08-2017' || item.fecha == '03-08-2017' || item.fecha == '04-08-2017')
                                        conducta = 'VACACIONES - IV UNIDAD ( II BIMESTRE)';
                                    if (item.fecha == '09-10-2017' || item.fecha == '10-10-2017' || item.fecha == '11-10-2017' || item.fecha == '12-10-2017' || item.fecha == '13-10-2017')
                                        conducta = 'VACACIONES - VI UNIDAD ( III BIMESTRE)';
                                    color = "style='background:#FF9933;'";
                                } else if (item.t_asist == "R" && item.evento == "I") {
                                    vasis = 'FERIADO';
                                    if (item.fecha == '14-04-2017')
                                        conducta = 'FERIADO - VIERNES SANTO';
                                    if (item.fecha == '01-05-2017')
                                        conducta = 'FERIADO - DÍA DEL TRABAJO';
                                    if (item.fecha == '28-07-2017')
                                        conducta = 'FERIADO - 28 DE JULIO';
                                    if (item.fecha == '30-08-2017')
                                        conducta = 'FERIADO - SANTA ROSA DE LIMA';
                                    if (item.fecha == '01-11-2017')
                                        conducta = 'FERIADO - DIA DE LOS SANTOS';    
                                    if (item.fecha == '16-11-2017')
                                        conducta = 'FERIADO';         
                                    if (item.fecha == '08-12-2017')
                                        conducta = 'FERIADO - DÍA DE LA INMACULADA CONCEPCIÓN';                                           
                                    color = "style='background:#FF9933;'";
                                } else if (item.t_asist == "E" && item.evento == "I") {
                                    vasis = 'EVENTO';
                                     if (item.fecha == '17-10-2017' || item.fecha == '18-10-2017')
                                        conducta = 'OLIMPIADAS MARIANISTA';
                                     if (item.fecha == '15-11-2017' || item.fecha == '17-11-2017')
                                        conducta = 'ANIVERSARIO MARIANISTA (ACTUACION )';                                    
                                    color = "style='background:#f7f957;'";
                                } else if (item.t_asist == '' && item.evento == "S") {
                                    vasis = 'P';
                                } else {
                                    vasis = item.t_asist;
                                }

                                nuevaFila +=
                                        "<tr " + color + ">"
                                        + "<td style='width: 10%;text-align: center'>" + item.id_alumno + "</td>"
                                        + "<td  style='width: 10%;text-align: center'>" + item.fecha + "</td>"
                                        + "<td  style='width: 10%;text-align: center'>" + item.hora + "</td>"
                                        + "<td  style='width: 10%;text-align: center'><b>" + vasis + "</b></td>"
                                        + "<td  style='width: 10%;text-align: center'>" + evento + "</td>"
                                        + "<td  style='width: 50%;text-align: left'>" + conducta + "</td>"
                                        + "</tr>";
                                //$(nuevaFila).appendTo("#viewListado tbody");

                            });
                            $("#viewListado tbody").html(nuevaFila);
                        } else {
                            if (log == 2) {
                                //alert("La sesion a Expirado. Vuelva a ingresar al Sistema.");
                                bootbox.alert({
                                    title: "AVISO!",
                                    message: "La sesion a Expirado. Vuelva a ingresar al Sistema!",
                                    size: 'small'
                                });                                
                                window.location = "<?= BASE_URL ?>login";
                            } else {
                                var nuevaFila = '';
                                //$(nuevaFila).appendTo("#viewListado tbody");
                                nuevaFila =
                                        "<tr>"
                                        + "<td colspan='6'><center>No se encontraron Registros</center></td>"
                                        + "</tr>";
                                $(nuevaFila).appendTo("#viewListado tbody");
                            }
                        }
                        $("#mensaje").text("");
                    },
                    "json"
                    );

            return false;
            /*}*/

        });

    });




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

    function fechaFormat(fecha) {
        var vfecha = "";
        var vcadena = "";
        if (fecha != '' && fecha.length == 10) {
            vcadena = fecha.split("-");
            vfecha = vcadena[2] + "-" + vcadena[1] + "-" + vcadena[0];
        }
        return vfecha;
    }

</script>