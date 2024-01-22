<?php
$datasession = $this->nativesession->get ('arrDataSesion');
/*if($datasession ['FLG_CAMBIO']) {
    echo "<script> alert('Hola'); </script>";
} 
if(isset($this->nativesession->get ('FLG_CAMBIO_PASS'))){
    echo "<script> alert('No existe la variable : FLG_CAMBIO_PASS'); </script>";
} else {
    echo "<script> alert('Exist la variable : FLG_CAMBIO_PASS'); </script>";
}*/
    
if($this->nativesession->verifica ('FLG_CAMBIO_PASS')==false) {
    $vflg_cambio = $datasession ['FLG_CAMBIO'];
} else {
     $vflg_cambio = $this->nativesession->verifica ('FLG_CAMBIO_PASS');
}
// Obteniendo informacion de la Estadistica por año
$vDataEstadistica = $datasession['ESTADISTICA'];
//print_r($vDataEstadistica); exit;
//echo "<script> alert('".$vflg_cambio." ** ".$vflg_cambio1."'); </script>";
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
<style>
.card-counter{
    box-shadow: 2px 2px 10px #DADADA;
    margin: 5px;
    padding: 20px 10px;
    background-color: #fff;
    height: 100px;
    border-radius: 5px;
    transition: .3s linear all;
  }

  .card-counter:hover{
    box-shadow: 4px 4px 20px #DADADA;
    transition: .3s linear all;
  }

  .card-counter.primary{
    background-color: #007bff;
    color: #FFF;
  }

  .card-counter.danger{
    background-color: #ef5350;
    color: #FFF;
  }  

  .card-counter.success{
    background-color: #66bb6a;
    color: #FFF;
  }  

  .card-counter.info{
    background-color: #26c6da;
    color: #FFF;
  }  

  .card-counter i{
    font-size: 5em;
    opacity: 0.2;
  }

  .card-counter .count-numbers{
    position: absolute;
    right: 35px;
    top: 20px;
    font-size: 32px;
    display: block;
  }

  .card-counter .count-name{
    position: absolute;
    right: 35px;
    top: 65px;
    font-style: italic;
    text-transform: capitalize;
    opacity: 0.5;
    display: block;
    font-size: 18px;
  }
 </style> 
<h3 class="page-header"><span class="glyphicon glyphicon-indent-left"></span> Dashboard </h3>
<center>

<div class="container">
   
    <!--   
    <div class="col-md-4">
      <div class="card-counter success">
        <i class="fa fa-ticket"></i>
        <span class="count-numbers">1,850.00</span>
        <span class="count-name">MARIANISTA S.A.C</span>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card-counter success">
        <i class="fa fa-ticket"></i>
        <span class="count-numbers">850.00</span>
        <span class="count-name">MARIANISTA</span>
      </div>
    </div>
    --> 

         <div class="row">
            <div class="col-md-4">
              <div class="card-counter success">
                <i class="fa fa-users"></i>
                <span class="count-numbers"><?=((int)$vDataEstadistica[0]->totalRazon1 + (int)$vDataEstadistica[1]->totalRazon1 ) ?></span>
                <span class="count-name">ALUMNOS MATRICULADOS</span>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card-counter info">
                <i class="fa fa-users"></i>
                <span class="count-numbers"><?=(int)$vDataEstadistica[0]->totalRazon1 ?></span>
                <span class="count-name">INICIAL / PRIMARIA</span>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card-counter info">
                <i class="fa fa-users"></i>
                <span class="count-numbers"><?=(int)$vDataEstadistica[1]->totalRazon1 ?></span>
                <span class="count-name">SECUNDARIA</span>
              </div>
            </div>   
         </div>
    </div>




    <!-- <img src="<?php echo base_url () ?>images/fondo.png"  class="img-responsive" /> -->
    <BR/>
    Todo los derechos reservados : <a href="http://www.sistemas-dev.com">Sistemas-Dev</a>
	
	
	
	
	
    <?php if ($datasession ['IDPERFIL'] == 2 && $vflg_cambio == 0) : ?>
        <div class="modal fade bs-example-modal-lg" id="cambio_clave_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                        <h4 class="modal-title">CAMBIO DE CLAVE</h4>
                    </div>           
                    <div class="modal-body">
                        <div id="divCambioClave" style="text-align: left;padding-left: 25px;padding-right: 25px;">                  
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: left;">
                        <span style="font-size: 12px;text-align: left;">
                            Estimad@ <b><?= $datasession ['APELLIDOS'] ?></b>,<br>Le damos la Bienvenida al Sistema de Pagos. <br>   
                            Por ser la primera ves que ingresa al sistema y por seguridad debes de cambiar la clave.<br>
                            Cualquier inconveniente comunicarse al correo : <b>info@sistemas-dev.com</b>
                        </span>                    
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($datasession ['IDPERFIL'] == 4 && $vflg_cambio == 0) : ?>
        <div class="modal fade bs-example-modal-lg" id="cambio_clave_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                        <h4 class="modal-title">CAMBIO DE CLAVE</h4>
                    </div>           
                    <div class="modal-body">
                        <div id="divCambioClave" style="text-align: left;padding-left: 25px;padding-right: 25px;">                  
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: left;">
                        <span style="font-size: 12px;text-align: left;">
                            Estimada Familia <b><?= $datasession ['APELLIDOS'] ?></b>,<br>Le damos la Bienvenida a la Intranet de Padres en nombre del Colegio <b>MARIANISTA</b> <br>   
                            Por ser la primera ves que ingresa al sistema y por seguridad debes de cambiar la clave.<br>
                            Cualquier inconveniente comunicarse al correo : <b>info@sistemas-dev.com</b>
                        </span>                    
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($datasession ['IDPERFIL'] == 4 && $vflg_cambio == 1) : ?>
        <div class="modal fade bs-example-modal-lg" id="test_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <center>
                        <br/><br/>
                        <h3 style="font-family: 'Trebuchet MS';"><b>BIENVENIDO(A) A LA EXTRANET DE PADRES</b></h3><br/>  
                        <hr>
                        <br/><br/>
                    </center>
                    <div style="text-align: left;padding-left: 25px;padding-right: 25px;">
                        <img style="float: right;" src="img/Logophp.png" />
                        Aqu&iacute; podra consultar informaci&oacute;n de sus Hijos tales como  : <br><br> 
                        <ul style="padding-left: 25px;">
                            <li>Asistencias de sus Hijos</li>
                            <li>Silabos por Unidad</li> 
                            <li>Notas de sus Hijos</li>
                            <li>Pagos Realizados</li>
                        </ul>
                        <br><br> 
                        Cualquier inconveniente comunicarse al correo : <b>info@sistemas-dev.com</b>
                    </div>
                    <br/><br/>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        function prueba_notificacion() {
            if (Notification) {
                if (Notification.permission !== "granted") {
                    Notification.requestPermission()
                }
                var title = "SISTEMAS-DEV"
                var extra = {
                    icon: "http://sistemas-dev.com/intranet/images/fondo.png",
                    body: "Colegio Marianista, Les da la Bienvenida." //\nSe les comunica que el sistema  ya está disponible para Celulares.
                }
                var noti = new Notification(title, extra)
                noti.onclick = {
                    // Al hacer click
                }
                noti.onclose = {
                    // Al cerrar
                }
                setTimeout(function () {
                    noti.close()
                }, 50000)
            }
        }

        $(document).ready(function () {
            prueba_notificacion();
        });

    </script>

    <?php if (($datasession ['IDPERFIL'] == 4 || $datasession ['IDPERFIL'] == 2) && $vflg_cambio == 0) : ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#demo_70").css('display', 'block');
                $.ajax({
                    async: true,
                    type: "POST",
                    dataType: "html",
                    url: "<?= BASE_URL ?>sweb_clave/form",
                    success: function (data) {
                        $('#divCambioClave').html(data);
                        $('#cambio_clave_modal').modal('show');

                    }
                });
            });
        </script>
    <?php endif; ?>

    <?php if ($datasession ['IDPERFIL'] == 1): ?>
        <script type="text/javascript">
            $(document).ready(function () {
                //$("#demo_10").css('display', 'block');
            });
        </script>
    <?php endif; ?>

    <?php if ($datasession ['IDPERFIL'] == 4 && $vflg_cambio == 1) : ?>
        <script type="text/javascript">
            $(document).ready(function () {
                //$('#test_modal').modal('show');
                //$("#demo_70").css('display', 'block');
            });
        </script>
    <?php endif; ?>

