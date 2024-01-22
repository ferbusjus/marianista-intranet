
<script>
    /*
     * ====================================================================================
     * Estado : Estado del Alumno
     *              V : Vigente
     *              N : Nuevo
     *              P : Promovido
     *              RR : Req. Recuperacion
     *              R : Repitente
     * -----------------------------------------------------
     * Matricula : Estado de la Matricula del Alumno
     *              S : Matriculado 
     *              N : No Matriculado              
     * ====================================================================================
     */

    $(document).ready(function () {
         console.log("vtotalPagos1 = "+vtotalPagos);
        //  mayuscula("input#txtbuscar");
        $("#btnclose").click(function ()
        {
            $('#modalMatricula').modal('hide');
            $('#frmAlumno')[0].reset();
            $('#accion').val("nuevo");
            /*
             $('#viewAlumnosFiltro').DataTable().destroy();
             $("#viewAlumnosFiltro").html("");
             var html = '<thead>';
             html += '< tr class = "tableheader" > ';
             html += '<th style="width: 10%;text-align: center">DNI</th>';
             html += '<th style="width: 60%;text-align: center">Apellidos y Nombres</th>      ';
             html += '<th style="width: 10%;text-align: center">NGS</th>';
             html += '<th style="width: 10%;text-align: center">Aula </th> ';
             html += ' <th style="width: 10%;text-align: center">Config.</th>';
             html += '</tr>';
             html += '</thead>';
             html += '<tbody>';
             html += '</tbody> ';
             $("#viewAlumnosFiltro").html(html);
             $('#viewAlumnosFiltro').DataTable({
             "ordering": false,
             "bInfo": true,
             "searching": false,
             "bFilter": false,
             "bDestroy": true,
             // "iDisplayLength": 20,
             "lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "Todos"]],
             "bLengthChange": false
             });
             */
            $('#txtbuscar').focus();
            $('#modalAlumnoFiltro').modal('show');
        });


        $("#frmMatricula").submit(function (event) {
            event.preventDefault();
            // -------------- Validando Datos : Documentos -----------------
            // debugger;            
            var vaula = $("#cb_aula").val();
            var dni = $("#lbldni").val();
            var libro = $("#lbllibro").val();
            
            var vestado = $("#hestado").val(); // Estado
            if (vestado == 'N') {
                if ($("#cb_nivel").val() == "") {
                    alert("Seleccione el Nivel.");
                    return false;
                }
                if ($("#cb_grado").val() == "") {
                    alert("Seleccione el Grado.");
                    return false;
                }
            }
            var vconfirma1 = false;
            var vconfirma2 = false;
            var checkboxes = $("input:checkbox[alias='chkDocumentos']").getCheckboxValues();
            //alert(checkboxes);

            if ($.trim(dni) == "") {
                alert("INGRESE EL NUMERO DE DNI.");
                $('#lbldni').attr("disabled", false);
                $("#lbldni").focus();
                return false;
            }
            if ($.trim(libro) == "") {
                alert("INGRESE EL NUMERO DE LIBRO.");
                $('#lbllibro').attr("disabled", false);
                $("#lbllibro").focus();
                return false;
            }            
            if (vaula == "") {
                alert("TIENE QUE SELECCIONAR EL AULA.");
                return false;
            }

            if (checkboxes < 2) {
                alert("EL ALUMNO TIENE QUE ENTREGAR COMO MINIMO 2 DOCUMENTOS\nFAVOR DE SELECCIONAR ALGUN DOCUMENTO.");
                return false;
            }
            if (checkboxes >= 2) {
                vconfirma1 = true;
            }

            if (vestado == 'P' || vestado == 'RR' || vestado == 'R') {
                // ------------------ Validando Datos : Cursos a Cargo  --------------------
               /* if (vtotalCcargo >= 3) {
                    alert("EL ALUMNO NO PUEDE MATRICULARSE YA QUE TIENE " + vtotalCcargo + " CURSOS JALADOS.");
                    return false;
                }*/
                /*if (vtotalCcargo > 0 && vtotalCcargo < 3) {
                    vconfirma2 = window.confirm("EL ALUMNO TIENE " + vtotalCcargo + " CURSO(S) A CARGO Y DEBE DE MATRICULARSE A VACACIONAL.\nESTA SEGURO DE GRABAR LA MATRICULA. ?");
                }*/
                // ------------------ Validando Datos : Pagos  --------------------
               /* var tpagos = (11 - vtotalPagos);
                if (tpagos > 0) {
                    alert("EL ALUMNO NO PUEDE MATRICULARSE YA QUE TIENE  " + tpagos + " PENSION(ES) PENDIENTE(S) .");
                    return false;
                }*/
            
            }

            /* if (!vconfirma1 && !vconfirma2) {
             alert("ALUMNO MATRICULADO....");
             return false;
             }
             return false;*/
            //vconfirma2 = true;
            //  alert(vconfirma1 + "*" + vconfirma2);

            if (vconfirma1) { // Restringo solo los documentos                
                var parametros = $(this).serialize();
                $('#btngrabar').attr("disabled", true);
                $("#btngrabar").text("Grabando ...");
                $.ajax({
                    type: "POST",
                    url: "<?= BASE_URL ?>sga_matricula/saveMatricula",
                    data: parametros,
                    dataType: "json",
                    beforeSend: function (objeto) {

                    },
                    success: function (data) {
                        $("#btngrabar").text("Grabar Datos");
                        $('#btngrabar').attr("disabled", false);
                        if (data['flg'] == 0) {
                            alert(data['msg']);
                            $('#modalMatricula').modal('hide');
                            gridTable.ajax.reload(null, false);
                        } else {
                            alert(data['msg']);
                            console.log("Error : " + data['error']);
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error interno, Comuniquese con el Administrador : \nE-mail : info@sistemas-dev.com');
                        $("#btngrabar").text("Grabar Datos");
                        $('#btngrabar').attr("disabled", false);
                    }
                });
            }
        });

    });


    jQuery.fn.getCheckboxValues = function () {
        //var values = [];
        var i = 0;
        this.each(function () {
            if ($(this).is(":checked")) {
                i++;
            }
            /*  if ($(this).is(":checked")) {
             values[i++] = $(this).val() + "*1";
             } else {
             values[i++] = $(this).val() + "*0";
             }*/

        });
        return i;
    }
</script>