<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>MARIANISTA - <?=$anio?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <script >
            $(document).ready(function () {
                mayuscula("input#txtfiltro");
                $('[data-toggle="tooltip"]').tooltip();
                $("#txtfiltro").focus();
                $("#form").submit(function (e)
                {
                    e.preventDefault();
                    var form = $(this).serialize();
                    console.log(form);
                    var datos = {
                        opcion: $('input:radio[name=opcion]:checked').val(),
                        txtfiltro: $("#txtfiltro").val()
                    };
                    $.ajax({
                        url: "<?= BASE_URL ?>consultas/filtro/",
                        type: "POST",
                        dataType: "json",
                        data: datos,
                        beforeSend: function () {

                        },
                        success: function (resp)
                        {
                            var html = "";
                            var fila = 1;
                            $("#viewLista tbody").html("");
                            if (resp.total > 0) {
                                $.each(resp.data, function (i, item) {
                                    if (item.ESTADO == 'V') {
                                        var estado = "VIGENTE";
                                        var aula = item.AULADES;
                                    } else if (item.ESTADO == 'R') {
                                        var estado = "RETIRADO";
                                        var aula = item.AULADES;
                                    } else if (item.ESTADO == 'P') {
                                        var estado = "PROMOVIDO";
                                        var aula = "<b>PENDIENTE</p>";
                                    }
                                    html += '<tr >';
                                    html += '<th scope="row" style="text-align: center">' + fila + '</th>';
                                    html += '<td style="text-align: center">' + item.DNI + ' </td>';
                                    html += '<td style="text-align: left">' + item.NOMCOMP + '</td>';
                                    html += '<td style="text-align: center">' + aula + '</td>';
                                    html += '<td style="text-align: center"><b>' + estado + '</b></td>';
                                    html += '<td style="text-align: center">';
                                    html += '<i class="fa fa-address-card-o" aria-hidden="true" style="cursor:pointer" data-toggle="tooltip"  title="Datos Familiares" onclick="verTelefonos(' + item.ALUCOD + ')"></i>';
                                    html += '&nbsp;<i class="fa fa-phone" aria-hidden="true" style="cursor:pointer" data-toggle="tooltip" Title="Datos Celulares" ></i>';
                                    html += '&nbsp;<i class="fa fa-envelope" aria-hidden="true" style="cursor:pointer" data-toggle="tooltip"  Title="Enviar Correo"></i>';
                                    html += '</td>';
                                    html += '</tr >';
                                    fila++;
                                });
                                $(html).appendTo("#viewLista tbody");
                            }
                            $("#txtfiltro").val("");
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert('Error interno. Comuniquese con el Administrador.');
                        }
                    });
                });
            });

            function verTelefonos(alucod) {
                var parametros = {
                    valucod: alucod
                };
                var url = "<?= BASE_URL ?>consultas/getDatos";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: parametros,
                    dataType: "json",
                    beforeSend: function () {

                    },
                    success: function (dataJson) {
                        $("#padre").val(dataJson.data.padre);
                        $("#padreemail").val(dataJson.data.pademail);
                        $("#padrecelular1").val(dataJson.data.padcelu);
                        $("#padrecelular2").val(dataJson.data.padcelu2);
                        $("#padredireccion").val(dataJson.data.paddireccion);
                        $("#madre").val(dataJson.data.madre);
                        $("#madreemail").val(dataJson.data.mademail);
                        $("#madrecelular1").val(dataJson.data.madcelu);
                         $("#madrecelular2").val(dataJson.data.madcelu2);
                        $("#madredireccion").val(dataJson.data.paddireccion);
                        $('#modal_telefonos').modal('show');
                    }
                });

            }
            function mayuscula(campo) {
                $(campo).keyup(function () {
                    $(this).val($(this).val().toUpperCase());
                });
            }
        </script>
        <style>
            select {
                font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;
                font-size: 11px;
            }
            /* ================= Para Datatable ===================*/
            th.dt-center, td.dt-center { text-align: center; }    
            th.dt-center, td.dt-right { text-align: right; } 
            th.dt-center, td.dt-left { text-align: left; } 
            /* ===================================================*/   
            body {
                /* background-color: #fbf9ee; */
            }
        </style>        

        <style>
            .bs-example{
                margin: 20px;
            }
            /* .card-body{
                 background-color: #00BCD4;
             }*/
            #modal_telefonos{
             /*   background-color: #fff8e1!important;*/
                opacity: 1.1;
            }
        </style>        
    </head>
    <body>
        <!--<div class="media">
            <img src="images/fondo.png" style="width: 75; height: 75px" class="rounded-circle mr-3" alt="Sample Image">
            <div class="media-body">
                <h5 class="mt-0">Jhon Carter <small><i>Posted on January 10, 2019</i></small></h5>
                <p>This is really an excellent feature! I love it. One day I'm definitely going to use this Bootstrap media object component into my application.</p>
            </div>
        </div>
         <hr>   
        <div class="media">
            <img src="images/fondo.png" class="rounded-circle mr-3" alt="Sample Image">
            <div class="media-body">
                <h5 class="mt-0">Jhon Carter <small><i>Posted on January 10, 2019</i></small></h5>
                <p>This is really an excellent feature! I love it. One day I'm definitely going to use this Bootstrap media object component into my application.</p>
            </div>
        </div>
         <hr>  
        -->



        <div class="container p-3">
            <!--
        <div class="bs-example"> 
            <div class="alert alert-warning alert-dismissible fade show">
                <h4 class="alert-heading"><i class="fa fa-warning"></i> Advertencia!</h4>
                <p>Estamos en mantenimiento de esta opcion. Favor de Ingresar mas tarde.</p>
                <hr>
                <p class="mb-0">Precione F11 para Continuar.</p>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        </div>            
            -->
            <div class="card mt-5 ">
                <div class="card-header">
                    <h5>CONSULTA DE ALUMNOS - <?=$anio?></h5>
                </div>
                <div class="card-body">
                    <form id="form" name="form" onsubmit="return false;" >
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="row ml-1">
                                        <div class="col-sm-3 ">
                                            <input class="form-check-input" type="radio" name="opcion" id="rb1" value="1" checked>
                                            <label class="form-check-label" for="rb1">
                                                Por Apellidos
                                            </label>
                                        </div>
                                        <div class="col-sm-3">
                                            <input class="form-check-input" type="radio" name="opcion" id="rb2" value="2">
                                            <label class="form-check-label" for="rb2">
                                                Por Nombres
                                            </label>       
                                        </div>
                                        <div class="col-sm-3">
                                            <input class="form-check-input" type="radio" name="opcion" id="rb3" value="3">
                                            <label class="form-check-label" for="rb3">
                                                Por DNI
                                            </label>         
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Texto a Buscar</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="txtfiltro" name="txtfiltro" placeholder="Ingrese el texto a Buscar" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-light btn-xl btn-block"  id="btnSubmit"><i class="fa fa-search"></i>Consultar</button>
                            </div>
                        </div>
                    </form>
                    <div class="form-group row">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm"  id="viewLista"   style="width: 100%;">
                                <thead>
                                <th scope="col" style="width: 5%;text-align: center;border-bottom: 2px solid #000;">#</th>
                                <th scope="col" style="width: 10%;text-align: center;border-bottom: 2px solid #000;">DNI </th>
                                <th scope="col" style="width: 50%;text-align: center;border-bottom: 2px solid #000;">APELLIDOS Y NOMBRES</th>
                                <th scope="col" style="width: 10%;text-align: center;border-bottom: 2px solid #000;">AULA </th>
                                <th scope="col" style="width: 10%;text-align: center;border-bottom: 2px solid #000;">ESTADO </th>
                                <th scope="col" style="width: 15%;text-align: center;border-bottom: 2px solid #000;">CONF</th>
                                <thead>
                                <tbody>                          
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>


    <!-- Modal -->
    <!--<button type="button" class="btn btn-primary" id="modal_telefonos" data-toggle="modal" data-target="#exampleModalCenter">
        Launch demo modal
    </button>-->

    <!-- Modal -->
    <div class="modal fade" id="modal_telefonos" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">DATOS DE FAMILIA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Padre (Nombres)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  readonly="" id="padre">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Padre (Direccion)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  readonly="" id="padredireccion">
                        </div>
                    </div>                          

                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Padre (Celular 1)</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control"  readonly="" id="padrecelular1">
                        </div>
                        <label  class="col-sm-3 col-form-label">Padre (Celular 2)</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control"  readonly="" id="padrecelular2">
                        </div>                        
                    </div>  
                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Padre (Email)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  readonly="" id="padreemail">
                        </div>                        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <hr>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Madre (Nombres)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  readonly="" id="madre">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Madre (Direccion)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  readonly="" id="madredireccion">
                        </div>
                    </div>                          

                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Madre (Celular 1)</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control"  readonly="" id="madrecelular1">
                        </div>
                        <label  class="col-sm-3 col-form-label">Madre (Celular 2)</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control"  readonly="" id="madrecelular2">
                        </div>                        
                    </div>  
                    <div class="form-group row">
                        <label  class="col-sm-3 col-form-label">Madre (Email)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  readonly="" id="madreemail">
                        </div>                        
                    </div>                    
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <hr>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>