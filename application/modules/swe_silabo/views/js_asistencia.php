

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
            if ($("#cbbimestre").val() == '0') {
                alert("Seleccione el Bimestre");
                return false;
            }
            if ($("#cbunidad").val() == '0') {
                alert("Seleccione la Unidad");
                return false;
            }            
            
            var cadCombo = $('#cbalumno').val().split("|");
            var objAsistencia = new Object();
            objAsistencia.vbimestre = $('#cbbimestre').val();
            objAsistencia.vunidad =    $('#cbunidad').val();
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
                        $("#viewListado tbody").html("");
                        if (data.length > 0) {
                            var nuevaFila ='';
                            //$("#viewListado tbody").html("");
                            $.each(data, function (i, item) {
                                var vIcono = ' <img style="cursor:pointer;" src="images/vermas.png" onclick="verDetalle(\''+ item.idsilabo +'\')" title="Ver Silabo" width="22px" heigth="22px" />';
                                nuevaFila +=
                                        "<tr>"
                                        + "<td style='width: 2%;text-align: center;vertical-align:middle;'>" + item.idcurso + "</td>"
                                        + "<td  style='width: 30%;text-align: center;background-color:#FFFAAE;cursor:pointer;vertical-align:middle;'><span class='glyphicon glyphicon-book' title='CURSO'></span>&nbsp;" + item.cursonom + "</td>"
                                        + "<td  style='width: 50%;text-align: left;background-color:#DFE7FF;cursor:pointer;vertical-align:middle;'><span class='glyphicon glyphicon-user'  title='PROFESOR(A)'></span>&nbsp;" + item.usunom + "</td>"
                                        + "<td  style='width: 18%;text-align: center'>" + vIcono + "</td>"
                                        + "</tr>";
                                //$(nuevaFila).appendTo("#viewListado tbody");
                            });
                             $("#viewListado tbody").html(nuevaFila);
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

    function cargaUnidades(val) {
        if (val != 0) {
            $.ajax({
                async: true,
                type: "POST",
                dataType: "json",
                url: "<?= BASE_URL ?>swe_nota/lstUnidad",
                data: "bimecod=" + val ,
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


    function verDetalle(idsilabo){
        $('#divcontenido').html('');        
          $.ajax({
            url : "<?= BASE_URL ?>swe_silabo/verDetalle",
            data: 'vIdsilabo=' + idsilabo ,
            type: "POST",
            dataType: "html",
            success: function(data)
            {
                var vhtml = ' <iframe width="900px" height="500px"  src="https://docs.google.com/viewer?url=http://marianista.sistemas-dev.com/'+data+'&embedded=true" />';
               //var vhtml='http://marianista.sistemas-dev.com/'+data;
                $('#divcontenido').html(vhtml); 
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