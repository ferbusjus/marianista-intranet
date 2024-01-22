<script type="text/javascript" >

    var gridTable = '';
    $(document).ready(function () {
        mayuscula("input#txtAlumnoSearch");
        $('#btnClear').click(function () {
            $('#txtAlumnoSearch').val("");
            $("#idanio").val("");
            js_listar();
        });
            
        $('#btnBuscar').click(function () {
            js_listar();
        }); 
     
        $('#btnImprimir').click(function () {
            $("div#divTblAlumnos").printArea();
        });

        $('#btnAgregar').click(function () {
             $('#form3')[0].reset();
             $('#modal_puntaje').modal('show'); 
        });

        $('#btnprint').click(function () {
            /* var originalContents = document.body.innerHTML; 
             var printContents = document.getElementById('PrintDivcontent').innerHTML;
             document.body.innerHTML = "<html><head><title></title></head><body style='background-color:#FFFFFF;'>"+ printContents + "</body>";
             window.print(); */
            /*
             var divContents = $("#PrintDivModal").html();
             var printWindow = window.open('', '', 'height=600,width=800');
             printWindow.document.write('<html><head><title>NOTAS TERCIOS POR AÑO</title>');
             printWindow.document.write('</head><body>');            
             printWindow.document.write(divContents);
             printWindow.document.write('</body >');
             printWindow.document.close();
             printWindow.print();
             printWindow.close();
             */
            $("div#PrintDivModal").printArea();
        });

    });



    function js_detalle(idAlumno)
    {
        $('#form2')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        var arrdata = {
            idAlumno: idAlumno
        };
        $.ajax({
            url: "<?= BASE_URL ?>sga_notas_tercio/getanios/",
            data: arrdata,
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                //$("#resultado").html("Procesando, espere por favor...");
                spinnerShow();
            },
            success: function (json)
            {
                spinnerHide();
                $("#viewdetalle tbody").html("");
                $("#divcodigo").html("");
                $("#divnomcomp").html("");
                $("#divaula").html("");
                if (json.dataAlumno) {
                    var arrData = json.dataAlumno;
                    $("#divcodigo").html(arrData.dni);
                    $("#divnomcomp").html(arrData.nomcomp);
                    $("#divaula").html(arrData.aula);
                }
                if (json.dataNotas.length > 0) {
                    var nuevaFila = '';
                    var vfila = 1;
                    var arrData = json.dataNotas;
                    $.each(arrData, function (i, item) {
                        nuevaFila +=
                                "<tr >"
                                + "<td style='width: 10%;text-align: center'>" + vfila + "</td>"
                                + "<td  style='width: 50%;text-align: center'>Año " + item.aula + "</td>"
                                + "<td  style='width: 20%;text-align: center'>" + item.punt + "</td>"
                                + "<td  style='width: 20%;text-align: center'>" + item.prom + "</td>"
                                + "</tr>";
                        //$(nuevaFila).appendTo("#viewListado tbody");
                        vfila++;
                    });
                    $("#viewdetalle tbody").html(nuevaFila);
                }
                $('#modal_detalle').modal('show'); // show bootstrap modal when complete loaded
                // $('.modal-title').text('AULA : ' + vdescaula);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
                //alert(jqXHR + "-" + textStatus + "-" + errorThrown);
            }
        });
    }

    function js_listar() {
        var txtalu = $('#txtAlumnoSearch').val();
        gridTable = $('#viewControl').DataTable({
            "ordering": false,
            "searching": false,
            "bInfo": true,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            // "iDisplayLength": 20,
            "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "Todos"]],
            "bLengthChange": false,
            "ajax": {
                "url": "<?= BASE_URL ?>sga_notas_tercio/lista/",
                "type": "POST",
                data: {txtalu: txtalu}
            },
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla.",
                "info": "Del _START_ al _END_ de _TOTAL_ ",
                "infoEmpty": "Mostrando 0 registros de un total de 0.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "infoPostFix": "(actualizados)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "<img src='http://sistemas-dev.com/intranet/img/gif-load.gif' ><br><center>Cargando...</center>",
                "search": "Buscar:",
                "searchPlaceholder": "Descripcion a buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {"className": "dt-center", "targets": [0, 1, 3, 4, 5, 6]}
            ],
            "columns": [
                {"data": "fila"},
                {"data": "codigo"},
                {"data": "nomcomp"},
                {"data": "anios"},
                {"data": "punt"},
                {"data": "prom"},
                {"data": "conf"}
            ]
        });
    }

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }
    function spinnerShow() {
        $("#modal").css("display", "block");
        $("#fade").css("display", "block");
    }

    function spinnerHide() {
        $("#modal").css("display", "none");
        $("#fade").css("display", "none");
    }
</script>    
