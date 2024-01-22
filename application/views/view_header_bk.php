<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/<?php echo EXTENSION_IMAGEN_FAVICON; ?>" href="<?php echo base_url () ?>img/<?php echo NOMBRE_IMAGEN_FAVICON; ?>" />

        <title><?php echo TITULO_PAGINA; ?></title>
        <!-- Bootstrap core CSS -->

        <link href="<?php echo base_url ('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url ('assets/datatables/css/dataTables.bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url ('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">


        <script src="<?php echo base_url ('assets/jquery/jquery-2.1.4.min.js') ?>"></script>
        <script src="<?php echo base_url ('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url ('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url ('assets/datatables/js/dataTables.bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url ('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>

        <!--    
              <script type="text/javascript" src="<?php echo base_url () ?>js/jquery.min.js"></script>
              <script type="text/javascript" src="<?php echo base_url () ?>js/bootstrap.min.js"></script>
              <script type="text/javascript" src="<?php echo base_url (); ?>js/jquery.dataTables.js"></script>
      
             <link href="<?php echo base_url () ?>css/bootstrap.min.css" rel="stylesheet" media="screen">
        -->      
        <link href="<?php echo base_url () ?>css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="<?php echo base_url ('assets/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">

        <script type="text/javascript" src="<?php echo base_url (); ?>js/JsValidacion.js"></script>
        <script type="text/javascript" src="<?php echo base_url () ?>js/treeMenu.js"></script> 

        <link href="<?php echo base_url () ?>css/dashboard.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url () ?>css/menu.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url () ?>css/Tablas.css"> 

    </head>
    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Menu</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url () ?>"><?php echo NOMBRE_EMPRESA; ?></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">

                        <?php if ($this->session->userdata ('IDPERFIL') == 4) { ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">CONSULTAS
                                    <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="swe_asistencia">Asistencias</a></li>
                                    <li><a href="swe_silabo">Silabos</a></li>
                                    <li><a href="swe_nota">Notas</a></li>
                                    <li><a href="swe_pago">Pagos</a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <li><a><?php
                                //  'Bienvenida: 
                                echo ' <strong>' . $this->session->userdata ('NOMBRE') . ' ' . $this->session->userdata ('APELLIDOS') . '</strong>&nbsp;|&nbsp;';
                                // echo 'Perfil: <strong>' . $this->session->userdata ('PERFIL') . '</strong>&nbsp;|&nbsp;';
                                ?></a></li>
                       <!-- <li><a href="<?php echo base_url () . "usuarios/Editar/" . base64_encode ($this->session->userdata ('ID')); ?>">Mis Datos</a></li>-->

                        <li><a href="<?php echo base_url () . 'login/CerrarSesion'; ?>">Salir</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">

                    <div id="treeMenu">
                        <br/>
                        <h2>Men&uacute; Principal</h2>
                        <hr/>
                        <ul>
                            <?php
                            $contador = 0;
                            $LineaTemp = 0;
                            $IdMenu = 0;
                            //session_start ();
                            $ArrayMenu = $this->session->userdata ('vMenu');
                            $cont = 0;
                            foreach ($ArrayMenu as $key => $value) {
                                $linea = $value->tipo;
                                $url = $value->url_menu;

                                if ($cont > 0 && $linea == 'M') {
                                    echo '</ul>';
                                    echo '</div>';
                                    echo '</li>';
                                    $cont = 0;
                                }

                                if ($linea == 'M') {
                                    $cont ++;
                                    echo '<li>';
                                    echo '<a href="#" class="parent">' . $value->dsc_menu . '</a><span></span>';
                                    echo '<div id="demo_' . $value->id_menu . '">';
                                    echo '<ul>';
                                }
                                if ($linea == 'O') {
                                    echo '<li><span></span><a href="' . base_url () . $url . '">' . $value->dsc_menu . '</a></li>';
                                }
                            }
                            echo '</ul>';
                            echo '</div>';
                            echo '</li>';
                            ?>
                        </ul>
                        <hr/>
                        <br/>
                    </div>


                </div>
                <div class="col-md-offset-2 main">
                    <br/><br/>
