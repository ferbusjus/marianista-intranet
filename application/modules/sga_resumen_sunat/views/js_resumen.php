
<script >
    var save_method;
    var table;
    var gridTable;

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

        $("#finicial").datepicker({
            dateFormat: 'dd/mm/yy',
            language: 'es',
            showToday: true,
            autoclose: true
        });
        // ================================================================================================
        $("#btnResumen").click(function ()
        {
            if ($.trim($("#finicial").val()) == '') {
                alert("Seleccione la Fecha");
                return false;
            }
            /*$("#frmReporteCaja").attr("action", "<?= BASE_URL ?>sga_reporte_caja/printpagos/");
             $("#frmReporteCaja").submit();*/
        });

    });

    function js_sendResumen()
    {
        var vfecha = $("#finicial").val();
        var vrazon = $("#cbrazon").val();
        if ($.trim(vfecha) == "") {
            alert("Seleccione la Fecha.");
            return false;
        }
        if (vrazon == "") {
            alert("Seleccione la Razon Social.");
            return false;
        }
        $("#btnEnviar").attr("disabled",true);
        $('#loading').show();
        // ==============================
        vfecha = fechaFormat(vfecha);
        $.ajax({
            url: "<?= BASE_URL ?>sga_resumen_sunat/generaDataSunat/",  
            type: "POST",
            dataType: "json", // Cambian por json
            data: {"vfecha": vfecha, "vrazon": vrazon},
            success: function (data)
            {   
                //alert(data); return false;
                console.log(data);
                
              if(data['respuesta']=="ok"){
                  alert("Mensaje SUNAT : "+data['msj_sunat']);   
                  gridTable.ajax.reload(null, false);
              } else if(data['respuesta']=="error"){
                  alert("Mensaje SUNAT : "+data['mensaje']);  
                  gridTable.ajax.reload(null, false);
              } else {
                   alert('Error Interno.\nComuniquese con el Administrador.');
              }                
              $("#btnEnviar").removeAttr('disabled');
              $('#loading').hide();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Hubo un error en procesamiento del Envio a la Sunat.\nComuniquese con el Administrador.');
                $("#btnEnviar").removeAttr('disabled');
                $('#loading').hide();
            }
        });
    }

    function js_verResumen() {
        var vfecha = $("#finicial").val();
        var vrazon = $("#cbrazon").val();
        if ($.trim(vfecha) == "") {
            alert("Seleccione la Fecha.");
            return false;
        }
        if (vrazon == "") {
            alert("Seleccione la Razon Social.");
            return false;
        }
        vfecha = fechaFormat(vfecha);
        gridTable = $('#viewGrilla').DataTable({
            "ordering": false,
            "searching": false,
            "bFilter": false,
            "bInfo": true,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 20,
            "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "All"]],
            // "iDisplayStart": 0,        
            "bLengthChange": false,
            "ajax": {
                "url": "<?= BASE_URL ?>sga_resumen_sunat/lista/",
                "type": "POST",
                "data": {"vfecha": vfecha, "vrazon": vrazon}
            },
            'initComplete': function (settings, json) {
                //$("#checkall").removeAttr("disabled");
            },
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla.",
                "info": "Del _START_ al _END_ de _TOTAL_ ",
                "infoEmpty": "Mostrando 0 registros de un total de 0.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "infoPostFix": "(actualizados)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "<img src='http://sistemas-dev.com/intranet/img/gif-load.gif' >",
                "search": "Buscar:",
                "searchPlaceholder": "Dato para buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columns": [
                {"data": "numrecibo", "className": "dt-center"},
                {"data": "familia", "className": "dt-left"},
                {"data": "alumno", "className": "dt-left"},
                {"data": "concepto", "className": "dt-center"},
                {"data": "fecreg", "className": "dt-fecha"},
                {"data": "cobrado", "className": "dt-center"},
                // {"data": "moneda", "className": "dt-center"},
               // {"data": "aula", "className": "dt-center"},
                {"data": "envio", "className": "dt-center"}
            ]

        });
    }

    function fechaFormat(fecha) {
        var vfecha = "";
        var vcadena = "";
        if (fecha != '' && fecha.length == 10) {
            vcadena = fecha.split("/");
            vfecha = vcadena[2] + "-" + vcadena[1] + "-" + vcadena[0];
        }
        return vfecha;
    }

</script>