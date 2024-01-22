

<script >
    var save_method;
    var table;

    $(document).ready(function () {

        $(document).bind("contextmenu", function (e) {
            return false;
        });

        /*$(".clsoptalumno").click(function()
         {
         if(this.checked && this.value==1){
         $("#cbalumno").removeAttr("disabled");
         } else if(this.checked && this.value==2) {
         $("#cbalumno option[value='0']").attr("selected","selected");
         $("#cbalumno").attr("disabled","disabled");
         }
         });*/

        $("#cbsalon").change(function ()
        {
            $("#cbalumno").empty();
            $.getJSON("<?= BASE_URL ?>sga_asistencia/lstalumno/" + this.value, function (data) {
                $("#cbalumno").append("<option value='0'>::::::::::::::::::::::::::: Seleccione Alumno ::::::::::::::::::::::::::::</option>");
                $.each(data, function (i, item) {
                    $("#cbalumno").append("<option value=\"" + item.ALUCOD + "\">" + item.DNI + " : " + item.NOMCOMP + "</option>");
                });
            });

        });

        $("#btnBuscar").click(function ()
        {
            var finicial = $('input#txtdesde').val();
            var ffinal = $('input#txthasta').val();
            if ($("#cbsalon").val() == '0') {
                alert("Seleccione el Salon");
                return false;
            }
            if ($("#cbalumno").val() == '0') {
                alert("Seleccione el Alumno");
                return false;
            }

            if ($("#cbmes").val() == '0') {
                alert("Seleccione el Mes");
                return false;
            }
            /* if (finicial == "") {
             alert("Seleccione la Fecha Inicial");
             $("#txtdesde").focus();
             return false;
             } else if (ffinal == "") {
             alert("Seleccione  la Fecha Final");
             $("#txthasta").focus();
             return false;
             } else if ((Date.parse(finicial)) > (Date.parse(ffinal))) {
             alert("La Fecha Inicial no puede ser mayor que la Fecha Final");
             $("#txtdesde").focus();
             return false;
             } else {*/
            var objAsistencia = new Object();
            var nuevaFila = "";
            //objAsistencia.vdesde = $('input#txtdesde').val();
            //objAsistencia.vhasta = $('input#txthasta').val();
            objAsistencia.vtipo = $('select#cbtipo').val();
            objAsistencia.vmes = $('select#cbmes').val();
            objAsistencia.vsalon = $('select#cbsalon').val();
            objAsistencia.valumno = $('select#cbalumno').val();
            $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Listando Asistencias...</center></div></div>");
            var DatosJson = JSON.stringify(objAsistencia);
            $.post("<?= BASE_URL ?>sga_asistencia/listar",
                    {
                        dataForm: DatosJson,
                        token: $('#token').val()
                    },
                    function (data, textStatus) {
                        var log = data['msg'];
                        var data = data['data'];
                        $('#divTblAsistencia').html("");
                        nuevaFila += ' <table class="table table-striped table-bordered"    id="viewListado" style="width: 100%">';
                        nuevaFila += '  <thead>';
                        nuevaFila += '           <tr class="tableheader">';
                        nuevaFila += '               <th style="width: 10%;text-align: center">Codigo</th>';
                        nuevaFila += '     <th style="width: 10%;text-align: center">Fecha</th>';
                        nuevaFila += '            <th style="width: 10%;text-align: center">Hora</th>';
                        nuevaFila += '           <th style="width: 10%;text-align: center">Asis.</th>';
                        nuevaFila += '            <th style="width: 10%;text-align: center">Evento</th>';
                        nuevaFila += '          <th style="width: 45%;text-align: center">Observacion</th>';
                        nuevaFila += '          <th style="width: 5%;text-align: center">Conf.</th>';
                        nuevaFila += '     </tr>';
                        nuevaFila += '   <thead>      ';
                        nuevaFila += '   <tbody>      ';


                        if (data.length > 0) {
                            // $("#viewListado").DataTable(/*{"bDestroy":true}*/);
                            //$("#viewListado tbody").html("");
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
                                    color = "style='background:#FF9933;'";
                                } else if (item.t_asist == "E" && item.evento == "I") {
                                    vasis = 'EVENTO';
                                     if (item.fecha == '17-10-2017' || item.fecha == '18-10-2017')
                                        conducta = 'OLIMPIADAS MARIANISTA';
                                    color = "style='background:#f7f957;'";
                                } else if (item.t_asist == '' && item.evento == "S") {
                                    vasis = 'P';
                                } else {
                                    vasis = item.t_asist;
                                }
                                nuevaFila += "<tr " + color + ">";
                                nuevaFila += "<td style='width: 10%;text-align: center'>" + item.alucod + "</td>";
                                nuevaFila += "<td  style='width: 10%;text-align: center'>" + item.fecha_formato + "</td>";
                                nuevaFila += "<td  style='width: 10%;text-align: center'>" + item.hora + "</td>";
                                nuevaFila += "<td  style='width: 10%;text-align: center'><b>" + item.tipo + "</b></td>";
                                nuevaFila += "<td  style='width: 10%;text-align: center'>" + evento + "</td>";
                                nuevaFila += "<td  style='width: 45%;text-align: left'>" + item.observacion + "</td>";
                                nuevaFila += "<td  style='width: 5%;text-align: center'>";
                                nuevaFila += "<a class='btn btn-sm btn-primary' href='javascript:void();' title='Edit'  onclick='javascript:edit_observacion(\"" + item.alucod + "\", \"" + item.fecha + "\", \"" + item.evento + "\")'> ";
                                nuevaFila += "<i class='glyphicon glyphicon-pencil'></i>Edit";
                                nuevaFila += "</a></td>";
                                nuevaFila += "</tr>";

                            });
                            //$('#viewListado').css({"table table-striped table-bordered"});
                            nuevaFila += '   </tbody>';
                            nuevaFila += '   </table>';
                            $('#divTblAsistencia').html(nuevaFila);
                            datatableCompleto('viewListado');
                        } else {
                            if (log == 2) {
                                alert("El tiempo de la sesion ha Expirado\n Vuelva a ingresar al Sistema");
                                window.location = "<?= BASE_URL ?>login";
                            } else {
                                nuevaFila += '   </tbody>';
                                nuevaFila += '   </table>';
                            }
                            $('#divTblAsistencia').html(nuevaFila);
                            datatableCompleto('viewListado');
                        }

                        $("#mensaje").text("");
                    },
                    "json"
                    );

            //return false;
            /*}*/

            //$("#viewListado").dataTable();

        });

    });

    function edit_observacion(idalumno, fecha, evento)
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        //$('#idreg').val(idreg);
        $('#fecha').val(fecha);
        $('#alucod').val(idalumno);
        $('#evento').val(evento);
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= BASE_URL ?>sga_asistencia/verobs",
            data: 'vIdalumno=' + idalumno + '&vFecha=' + fecha + '&vEvento=' + evento,
            type: "POST",
            dataType: "JSON",
            success: function (data)
            {
                var data = data['arrData'];
                //$('input[name="chkConducta[]"]').removeAttr('checked');
                if (data.length > 0) {
                    $.each(data, function (i, item) {
                        $('input[name="chkConducta[]"]').each(function () {
                            if ($(this).val() == item["rows"].id_conducta) {
                                $(this).attr('checked', true);
                                if (item["rows"].id_conducta == '09') {
                                    $('#txtotros').removeAttr('disabled');
                                    $('#txtotros').val(item["rows"].otros);
                                }
                                $('#idreg').val(item["rows"].id);
                            }
                        });
                    });
                    $('#acc').val(data[0]["flag"]);
                }
                /*
                 $('[name="id"]').val(data.id);
                 $('[name="firstName"]').val(data.firstName);
                 $('[name="lastName"]').val(data.lastName);
                 $('[name="gender"]').val(data.gender);
                 $('[name="address"]').val(data.address);
                 $('[name="dob"]').val(data.dob);*/

                $('#ModalObs').modal('show'); // show bootstrap modal when complete loaded
                //$('.modal-title').text('Edit Person'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function save()
    {
        var url;
        var checkboxValues = '';
        url = "<?= BASE_URL ?>sga_asistencia/saveupdate";

        $('input[name="chkConducta[]"]:checked').each(function () {
            checkboxValues += $(this).val() + "|";
        });
        //eliminamos la última coma.
        if (checkboxValues != '') {
            checkboxValues = checkboxValues.substring(0, checkboxValues.length - 1);
        }

        if (checkboxValues == '') {
            alert("SELECCIONE ALGUN TIPO DE CONDUCTA.");
            return false;
        }

        var arrData = {
            arrconducta: checkboxValues,
            acc: $("#acc").val(),
            fecha: $("#fecha").val(),
            alucod: $("#alucod").val(),
            otros: $("#txtotros").val(),
            tipo: $("#evento").val(),
            id: $("#idreg").val()
        };

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: arrData,
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#btnBuscar').trigger('click');
                $('#ModalObs').modal('hide');
                //reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
            }
        });
    }

    function activaChk(chk) {
        if (chk == true) {
            $("#txtotros").val("");
            $("#txtotros").removeAttr('disabled');
        } else {
            $("#txtotros").val("");
            $("#txtotros").attr("disabled", "disabled");
        }
    }

    function validaPost() {
        if ($("#cbsalon").val() == '0') {
            alert("Seleccione el Salon");
            return false;
        }
        if ($("#cbalumno").val() == '0') {
            alert("Seleccione el Alumno");
            return false;
        }

        if ($("#cbmes").val() == '0') {
            alert("Seleccione el Mes");
            return false;
        }
    }
</script>