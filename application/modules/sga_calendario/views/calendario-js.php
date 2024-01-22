<script>
    var gridTable;
    $(document).ready(function () {

        $('#cbmotivo').multiselect({
            includeSelectAllOption: true,
            buttonWidth: '470px',
            dropRight: true            
        });
        
        $('#txthora').timepicker(/*{
         showSeconds: true,
         showMeridian: false
         }*/);
        
        /*$('#btnSelected').click(function () {
         var selected = $("#lstFruits option:selected");
         var message = "";
         selected.each(function () {
         message += $(this).text() + " " + $(this).val() + "\n";
         });
         alert(message);
         });*/

        mayuscula("input#txtAlumnoSearch");
        mayuscula("input#txtasiste");

        $("#txtAlumnoSearch").keypress(function (key) {
            console.log(key.charCode)
            if ((key.charCode < 97 || key.charCode > 122)//letras mayusculas
                    && (key.charCode < 65 || key.charCode > 90) //letras minusculas
                    && (key.charCode != 45) //retroceso
                    && (key.charCode != 241) //ñ
                    && (key.charCode != 209) //Ñ
                    && (key.charCode != 32) //espacio
                    && (key.charCode != 225) //á
                    && (key.charCode != 233) //é
                    && (key.charCode != 237) //í
                    && (key.charCode != 243) //ó
                    && (key.charCode != 250) //ú
                    && (key.charCode != 193) //Á
                    && (key.charCode != 201) //É
                    && (key.charCode != 205) //Í
                    && (key.charCode != 211) //Ó
                    && (key.charCode != 218) //Ú
                    && (key.charCode != 0) //Ú
                    )
                return false;
        });

        $("#txtAlumnoSearch").autocomplete({
            source: "<?= BASE_URL ?>swp_pagos/filtroAlumno",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.alucod != '') {
                    var vnemosdes = ui.item.nemodes;
                    vnemosdes = vnemosdes.split("-");
                    $('#txtalucod').val(ui.item.alucod);
                    $('#txtnemo').val(ui.item.nemo);
                    $("#txtAlumnoSearch").val(ui.item.nomcomp + " (" + $.trim(vnemosdes[2]) + ")");
                    $("#txtAlumnoSearch").attr("disabled", true);
                } else {
                    $('#txtalucod').val('');
                    $('#txtnemo').val('');
                    $("#txtAlumnoSearch").val('');
                    $("#txtAlumnoSearch").attr("disabled", false);
                }
                //  return false;
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        var date = new Date();
        var yyyy = date.getFullYear().toString();
        var mm = (date.getMonth() + 1).toString().length == 1 ? "0" + (date.getMonth() + 1).toString() : (date.getMonth() + 1).toString();
        var dd = (date.getDate()).toString().length == 1 ? "0" + (date.getDate()).toString() : (date.getDate()).toString();

        $('#calendar').fullCalendar({
            header: {
                language: 'es',
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay',

            },
            defaultDate: yyyy + "-" + mm + "-" + dd,
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            selectable: true,
            selectHelper: true,
            select: function (start, end) {
                $('#txtalucod').val('');
                $('#txtnemo').val('');
                $("#txtAlumnoSearch").val('');
                $("#txtAlumnoSearch").attr("disabled", false);
                $('#cbmotivo').multiselect("deselectAll", false);
                $('#cbmotivo').multiselect("refresh");
                $('#ModalAdd #start').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
                $('#ModalAdd #end').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
                //$('#ModalAdd').modal('show');
                modal({
                    // Available buttons when adding
                    buttons: {
                        add: {
                            id: 'add-event', // Buttons id
                            css: 'btn-success', // Buttons class
                            label: 'Grabar' // Buttons label
                        }
                    },
                    title: ' :::::::::::: AGREGAR CITA :::::::::::::' // Modal title
                });

            },
            eventRender: function (event, element) {
                element.bind('dblclick', function () {
                    $('#ModalEdit #id').val(event.id);
                    $('#ModalEdit #txttitulo').val(event.title);
                    $('#ModalEdit #color').val(event.color);
                    $('#ModalEdit').modal('show');
                });
            },
            // Event Mouseover
            eventMouseover: function (calEvent, jsEvent, view) {
                var tooltip = '<div class="event-tooltip">' + calEvent.description + '</div>';
                $("body").append(tooltip);

                $(this).mouseover(function (e) {
                    $(this).css('z-index', 10000);
                    $('.event-tooltip').fadeIn('500');
                    $('.event-tooltip').fadeTo('10', 1.9);
                }).mousemove(function (e) {
                    $('.event-tooltip').css('top', e.pageY + 10);
                    $('.event-tooltip').css('left', e.pageX + 20);
                });
            },
            eventMouseout: function (calEvent, jsEvent) {
                $(this).css('z-index', 8);
                $('.event-tooltip').remove();
            },
            eventDrop: function (event, delta, revertFunc, start, end) {
                start = event.start.format('YYYY-MM-DD HH:mm:ss');
                if (event.end) {
                    end = event.end.format('YYYY-MM-DD HH:mm:ss');
                } else {
                    end = start;
                }
                $.post('<?= BASE_URL ?>sga_calendario/dragUpdateEvent', {
                    id: event.id,
                    start: start,
                    end: end
                }, function (result) {
                    $('.alert').addClass('alert-success').text('CITA MODIFICADA CORRECTAMENTE. !');
                    hide_notify();
                });
            },
            eventResize: function (event, dayDelta, minuteDelta, revertFunc) {
                start = event.start.format('YYYY-MM-DD HH:mm:ss');
                if (event.end) {
                    end = event.end.format('YYYY-MM-DD HH:mm:ss');
                } else {
                    end = start;
                }
                $.post('<?= BASE_URL ?>sga_calendario/dragUpdateEvent', {
                    id: event.id,
                    start: start,
                    end: end
                }, function (result) {
                    $('.alert').addClass('alert-success').text('CITA MODIFICADA CORRECTAMENTE. !');
                    hide_notify();

                });
            },
            events: [
<?php
foreach ($events as $event) {

    $start = explode(" ", $event->start);
    $end = explode(" ", $event->end);
    if ($start[1] == '00:00:00') {
        $start = $start[0];
    } else {
        $start = $event->start;
    }
    if ($end[1] == '00:00:00') {
        $end = $end[0];
    } else {
        $end = $event->end;
    }
    ?>
                    {
                        id: '<?php echo $event->id; ?>',
                        title: 'Estado : <?php echo $event->title; ?>',
                        start: '<?php echo $start; ?>',
                        end: '<?php echo $end; ?>',
                        color: '<?php echo $event->color; ?>',
                        description: '<b>ALUMNO : </b><?php echo $event->nomcomp; ?><br><b>AULA : </b><?php echo $event->aula; ?>'
                    },
<?php } ?>
            ]
        });

        function edit(event) {
            start = event.start.format('YYYY-MM-DD HH:mm:ss');
            if (event.end) {
                end = event.end.format('YYYY-MM-DD HH:mm:ss');
            } else {
                end = start;
            }

            id = event.id;
            Event = [];
            Event[0] = id;
            Event[1] = start;
            Event[2] = end;

            $.ajax({
                url: '<?= BASE_URL ?>sga_calendario/editEvento',
                type: "POST",
                data: {Event: Event},
                success: function (rep) {
                    if (rep == 'OK') {
                        alert('Evento se ha guardado correctamente');
                    } else {
                        alert('No se pudo guardar. Inténtalo de nuevo.');
                    }
                }
            });
        }

        // Handle Click on Add Button
        $('.modal').on('click', '#add-event', function (e) {
            if (validator(['txtalucod', 'txtnemo'])) {
                var strmotivos = '';
                var selected = $("#cbmotivo option:selected");
                selected.each(function () {
                    strmotivos += $(this).val() + ",";
                });
                if (strmotivos == "") {
                    $('.alert').addClass('alert-success').text('Seleccione algun Motivo de la Cita. !');
                    hide_notify();
                    return false;
                }

                if ($('#txtalucod').val() == "") {
                    $('.alert').addClass('alert-success').text('Seleccione un Alumno Valido.');
                    hide_notify();
                    return false;
                } 
                if ($('#color').val() == "") {
                    $('.alert').addClass('alert-success').text('Seleccione la Prioridad.');
                    hide_notify();
                    return false;
                }                
                if ($('#txthora').val() == "") {
                    $('.alert').addClass('alert-success').text('Seleccione la Hora.');
                    hide_notify();
                    return false;
                }  
                console.log("Titulo : "+$('#txttitulo').val());
                $.post('<?= BASE_URL ?>sga_calendario/addEvent', {
                    txtnemo: $('#txtnemo').val(),
                    txtalucod: $('#txtalucod').val(),
                    txtobs: $('#description').val(),
                    txtacude: $('#txtasiste').val(),
                    idmotivos: strmotivos,
                    titulo: $('#txttitulo').val(),
                    color: $('#color').val(),
                    start: $('#start').val(),
                    end: $('#end').val(),
                    hora : $('#txthora').val()
                }, function (data, textStatus, jQxhr) {
                    if (textStatus == 'success') {
                        if (data['flg'] == 0) {
                            alert(data['msg']);
                            $('.alert').addClass('alert-success').text(data['msg']);
                            hide_notify();
                            $('.modal').modal('hide');
                           // $('#calendar').fullCalendar('removeEventSource');
                            $('#calendar').fullCalendar('refetchEvents');
                        } else {
                            alert(data['msg']);
                            hide_notify();
                            $('.alert').addClass('alert-danger').text(data['msg']);
                            console.log("Error :" + data['errorlog']);
                        }
                    } else {
                        console.log("Error :" + jQxhr); 
                    }
                }, 'json');
            }
        });

        // Handle click on Update Button
        $('.modal').on('click', '#update-event', function (e) {
            if (validator(['title', 'description'])) {
                $.post('<?= BASE_URL ?>sga_calendario/updateEvent', {
                    id: currentEvent._id,
                    title: $('#title').val(),
                    description: $('#description').val(),
                    color: $('#color').val()
                }, function (result) {
                    $('.alert').addClass('alert-success').text('CITA MODIFICADA CORRECTAMENTE. !');
                    $('.modal').modal('hide');
                    $('#calendar').fullCalendar("refetchEvents");
                    hide_notify();

                });
            }
        });

        // Handle Click on Delete Button
        $('.modal').on('click', '#delete-event', function (e) {
            $.get('<?= BASE_URL ?>sga_calendario/deleteEvent?id=' + currentEvent._id, function (result) {
                $('.alert').addClass('alert-success').text('CITA ELIMINADA CORRECTAMENTE. !');
                $('.modal').modal('hide');
                $('#calendar').fullCalendar("refetchEvents");
                hide_notify();
            });
        });

    });

    function hide_notify()
    {
        setTimeout(function () {
            $('.alert').removeClass('alert-success').text('');
        }, 2000);
    }

    // Dead Basic Validation For Inputs
    function validator(elements) {
        var errors = 0;
        $.each(elements, function (index, element) {
            if ($.trim($('#' + element).val()) == '')
                errors++;
        });
        // alert(errors);
        if (errors) {
            $('.error').html('Por favor Ingrese los valores de los Campos.');
            return false;
        }
        return true;
    }

    function modal(data) {
        // Set modal title
        $('.modal-title').html(data.title);
        // Clear buttons except Cancel
        $('.modal-footer button:not(".btn-danger")').remove();
        // Set input values
       //  $('#txttitulo').val(data.event ? data.event.title : '');
        $('#description').val(data.event ? data.event.description : '');
        $('#color').val(data.event ? data.event.color : '#3a87ad');
        // Create Butttons
        $.each(data.buttons, function (index, button) {
            $('.modal-footer').prepend('<button type="button" id="' + button.id + '" class="btn ' + button.css + '">' + button.label + '</button>')
        })
        //Show Modal
        $('.modal').modal('show');
    }
    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }
    function minuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toLowerCase());
        });
    }


</script>