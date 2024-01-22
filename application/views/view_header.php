<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/<?php echo EXTENSION_IMAGEN_FAVICON; ?>" href="<?php echo base_url() ?>img/<?php echo NOMBRE_IMAGEN_FAVICON; ?>" />
        <title><?php echo TITULO_PAGINA . $this->nativesession->get('S_ANO_VIG'); ?></title>
        <!-- Bootstrap core CSS -->
       <link href="<?php echo base_url('css/jquery-ui.css') ?>" rel="stylesheet"> 
        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/sweetalert2/sweetalert2.css') ?>" rel="stylesheet" type="text/css">    
        <link href="<?php echo base_url('assets/sweetalert2/animate.css') ?>" rel="stylesheet" type="text/css">   

        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">
        <!--<link href="<?php //echo base_url('assets/easyWizard.css')  ?>" rel="stylesheet">-->

        <script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js') ?>"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>       
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
         
         <script src="<?php echo base_url('assets/sweetalert2/popper/popper.min.js') ?>"></script>  
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/sweetalert2/sweetalert2.min.js') ?>"></script>  

        <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>
        <!--<script src="<?php //echo base_url('assets/easyWizard.js')  ?>"></script> -->

        <script src="<?php echo base_url('assets/bootbox.min.js') ?>"></script>        
       <!-- <link href="<?php //echo base_url ()     ?>css/bootstrap.min.css" rel="stylesheet" media="screen">-->
       <link href="<?php echo base_url ('assets/font-awesome/css/font-awesome.min.css')     ?>" rel="stylesheet">
        <script type="text/javascript" src="<?php echo base_url(); ?>js/JsValidacion.js"></script>
       <!-- <script type="text/javascript" src="<?php //echo base_url ()     ?>js/treeMenu.js"></script> -->
        <link href="<?php echo base_url() ?>css/dashboard.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>css/menu.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>css/Tablas.css"> 

        <script language="Javascript">
            document.oncontextmenu = function () {
                return false
            }
        </script>
    </head> 
    <?php
    $datasession = $this->nativesession->get('arrDataSesion');
    ?>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Menu</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url() ?>"><?php echo NOMBRE_EMPRESA; ?></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">

                        <?php if ($datasession['IDPERFIL'] == 4) { ?>
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
                        <?php } elseif ($datasession['IDPERFIL'] == 100 || $datasession['IDPERFIL'] == 200) { ?>
                            <?php
                            $ArrayMenu = $datasession ['vMenu'];
                            $cont = 0;
                            foreach ($ArrayMenu as $menup) {
                                $arrMenuHijo = $ArrayMenu[$cont]['arrhijo'];
                                echo ' <li class="dropdown">';
                                echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . strtoupper($menup["arrpadre"]->dsc_menu);
                                echo '   <span class="caret"></span></a>';
                                foreach ($arrMenuHijo as $menuh) {
                                    echo '<ul class="dropdown-menu">';
                                    echo '    <li><a  href="' . $menuh->url_menu . '">&nbsp;' . $menuh->dsc_menu . '</a></li>';
                                    echo '</ul> ';
                                }
                                echo '</li>';
                                $cont++;
                            }
                            ?>                         
                        <?php } else { ?>  
                            <li><a href="#"><strong style="color: #ffffff; font-weight: bold;">AÑO : <?= $this->nativesession->get('S_ANO_VIG'); ?></strong></a></li>
                            <li><a href="#">
                                    <?php
                                    echo ' <strong>' . $datasession ['NOMBRE'] . ' ' . $datasession ['APELLIDOS'] . '</strong>&nbsp;';
                                    // echo 'Perfil: <strong>' . $this->session->userdata ('PERFIL') . '</strong>&nbsp;|&nbsp;';
                                    ?>
                                </a> </li>
                            <!-- <li><a href="<?php //echo base_url () . "usuarios/Editar/" . base64_encode ($datasession ['ID']);     ?>">Mis Datos</a></li>-->
                            <li><a href="<?php echo base_url() . 'login/CerrarSesion'; ?>">Salir</a></li>
                        <?php } ?>
                    </ul>
                </div>


            </div>
        </nav>
        <br><br><br>
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-lg-2"> <!-- hidden-xs hidden-sm hidden-md  -->
                    <div class="panel-group"  id="accordion">
                        <?php
                        $ArrayMenu = $datasession ['vMenu'];
                        $cont = 0;
                        foreach ($ArrayMenu as $menup) {
                            $arrMenuHijo = $ArrayMenu[$cont]['arrhijo'];
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne_<?= $cont ?>">
                                            <span class="glyphicon glyphicon-th">
                                            </span>&nbsp;<?php echo $menup['arrpadre']->dsc_menu; ?></a>
                                    </h4>
                                </div>

                                <div id="collapseOne_<?= $cont ?>" class="panel-collapse collapse <?= (($cont == 0) ? 'in' : '') ?>">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table ">
                                                <?php foreach ($arrMenuHijo as $menuh) { ?>
                                                    <tr>
                                                        <td>
                                                            <span class="glyphicon glyphicon-tasks"></span><a href="<?php echo $menuh->url_menu ?>">&nbsp;<?php echo $menuh->dsc_menu ?></a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $cont++;
                        }
                        ?>
                    </div>

                </div>
                <div class="col-sm-12 col-md-12 col-lg-10">
                    <br/><br/>
