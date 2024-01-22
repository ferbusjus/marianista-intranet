<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="SISTEMAS-F�DEV - 2016">
        <link rel="icon" type="image/<?php echo EXTENSION_IMAGEN_FAVICON; ?>" href="<?php echo base_url() ?>img/<?php echo NOMBRE_IMAGEN_FAVICON; ?>" />
        <title><?php echo TITULO_PAGINA.$this->nativesession->get ('S_ANO_VIG');  ?></title>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo base_url('css/jquery-ui.css') ?>" rel="stylesheet"> 
          <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
          <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>css/menu.css">
        <script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js') ?>"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>    
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <link href="<?php echo base_url ('assets/font-awesome/css/font-awesome.min.css')     ?>" rel="stylesheet">
        <script type="text/javascript" src="<?php echo base_url(); ?>js/JsValidacion.js"></script>
        <link href="<?php echo base_url() ?>css/dashboard.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>css/menu.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>css/Tablas.css">         
    </head>
    <?php
    $datasession = $this->nativesession->get ('arrDataSesion');
    ?>
    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Menus</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url() ?>"><?php echo NOMBRE_EMPRESA; ?></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a><?php
                                echo 'Bienvenido: <strong>' . $datasession['NOMBRE'] . ' ' . $datasession['APELLIDOS'] . '</strong>&nbsp;';
                                //echo 'Tipo Usuario: <strong>' . $datasession['TIPOUSUARIOMS'] . '</strong>&nbsp;|&nbsp;';
                                ?></a></li>
                       <!-- <li><a href="<?php echo base_url() . "usuarios/Editar/" . base64_encode($datasession['ID']); ?>">Mis Datos</a></li>-->
                        <li><a href="<?php echo base_url() . 'login/CerrarSesion'; ?>">Salir</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">

                <div style="margin-left:15px">
                    <br/><br/>
