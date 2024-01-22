<script src="<?php echo base_url () ?>assets/jquery/jquery-2.1.4.min.js"></script>
<script src="<?php echo base_url () ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url () ?>assets/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url () ?>assets/datatables/js/dataTables.bootstrap.js"></script>

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
                alert("Seleccione el Alumno");
                return false;
            }
            if ($("#cbtipo").val() == '0') {
                alert("Seleccione la Semana");
                return false;
            }
            
            var cadCombo = $('#cbalumno').val().split("|");
            var objAsistencia = new Object();
            objAsistencia.vsemana = $("#cbtipo").val(),
            objAsistencia.vnemo = cadCombo[0];
            objAsistencia.valumno = cadCombo[1];
            $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Listando Silabos...</center></div></div>");
            var DatosJson = JSON.stringify(objAsistencia);
            $.post("<?= BASE_URL ?>swe_silabo/listar",
                    {
                        dataForm: DatosJson,
                        token: $('#token').val()
                    },
                    function (data, textStatus) {
                        var log = data['msg'];
                        var data = data['data'];
                        if (data.length > 0) {
                            $("#viewListado tbody").html("");
                            $.each(data, function (i, item) {
                                var vLunes = (item.txtdia1.length>50)?(item.txtdia1.substring(0,50) +' ...'+' <img style="cursor:pointer;" src="images/vermas.png" onclick="verDetalle(\''+ item.idsilabo +'\', 1)" title="Ver Mas" width="22px" heigth="22px" />') : item.txtdia1;
                                var vMartes = (item.txtdia2.length>50)?(item.txtdia2.substring(0,50) +' ...'+' <img style="cursor:pointer;" src="images/vermas.png" onclick="verDetalle(\''+ item.idsilabo +'\', 2)" title="Ver Mas" width="22px" heigth="22px" />'):item.txtdia2;
                                var vMiercoles = (item.txtdia3.length>50)?(item.txtdia3.substring(0,50) +' ...'+' <img style="cursor:pointer;" src="images/vermas.png" onclick="verDetalle(\''+ item.idsilabo +'\', 3)" title="Ver Mas" width="22px" heigth="22px" />'):item.txtdia3;
                                var vJueves = (item.txtdia4.length>50)?(item.txtdia4.substring(0,50) +' ...'+' <img style="cursor:pointer;" src="images/vermas.png" onclick="verDetalle(\''+ item.idsilabo +'\', 4)" title="Ver Mas" width="22px" heigth="22px" />'):item.txtdia4;
                                var vViernes = (item.txtdia5.length>50)?(item.txtdia5.substring(0,50) +' ...'+' <img style="cursor:pointer;" src="images/vermas.png" onclick="verDetalle(\''+ item.idsilabo +'\', 5)" title="Ver Mas" width="22px" heigth="22px" />'):item.txtdia5;
                                var nuevaFila =
                                        "<tr>"
                                        + "<td style='width: 2%;text-align: center;vertical-align:middle;'>" + item.idcurso + "</td>"
                                        + "<td  style='width: 20%;text-align: center;background-color:#FFFAAE;cursor:pointer;vertical-align:middle;'><span class='glyphicon glyphicon-book' title='CURSO'></span>&nbsp;" + item.cursonom + "</td>"
                                        + "<td  style='width: 18%;text-align: left;background-color:#DFE7FF;cursor:pointer;vertical-align:middle;'><span class='glyphicon glyphicon-user'  title='PROFESOR(A)'></span>&nbsp;" + item.usunom + "</td>"
                                        + "<td  style='width: 12%;text-align: left'>" + vLunes + "</td>"
                                        + "<td  style='width: 12%;text-align: left'>" + vMartes + "</td>"
                                        + "<td  style='width: 12%;text-align: left'>" + vMiercoles + "</td>"
                                        + "<td  style='width: 12%;text-align: left'>" + vJueves + "</td>"
                                        + "<td  style='width: 12%;text-align: left'>" + vViernes + "</td>"
                                        + "</tr>";
                                $(nuevaFila).appendTo("#viewListado tbody");
                            });
                        } else {
                            if (log == 2) {
                                alert("La sesion a Expirado. Vuelva a ingresar al Sistema.");
                                window.location = "<?= BASE_URL ?>login";
                            } else {
                                var nuevaFila = '';
                                //$(nuevaFila).appendTo("#viewListado tbody");
                                nuevaFila =
                                        "<tr>"
                                        + "<td colspan='8'><center>No se encontraron Registros</center></td>"
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

        $("#ModalVerDetalle").on("hidden", function() { 
           $("#ModalVerDetalle").remove();
        });
     
    });


    function verDetalle(idsilabo, txtdia){
        $('#divcontenido').html('');        
           if(txtdia==1) txtdia = 'txtdia1';
           if(txtdia==2) txtdia = 'txtdia2';
           if(txtdia==3) txtdia = 'txtdia3';
           if(txtdia==4) txtdia = 'txtdia4';
           if(txtdia==5) txtdia = 'txtdia5';
          $.ajax({
            url : "<?= BASE_URL ?>swe_silabo/verDetalle",
            data: 'vIdsilabo=' + idsilabo+'&vCampo=' + txtdia,
            type: "POST",
            dataType: "html",
            success: function(data)
            {
                $('#divcontenido').html(data); 
                $('#ModalVerDetalle').modal('show');                  
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
         });
         
              
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
</script>