<?php
//Set no caching
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msapplication-tap-highlight" content="no">
        <title>SISTEMAS-DEV</title>
        <link rel="icon" href="<?php echo base_url() ?>md/images/favicon-32x32.png" sizes="32x32">
        <link rel="apple-touch-icon-precomposed" href="<?php echo base_url() ?>md/images/apple-touch-icon-152x152.png">
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">  
        <meta name="msapplication-TileColor" content="#00bcd4">
        <meta name="msapplication-TileImage" content="<?php echo base_url() ?>md/images/mstile-144x144.png">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/css/materialize.min.css">
        <link href="<?php echo base_url() ?>md/css/style.css" type="text/css" rel="stylesheet" media="screen,projection">
        <link href="<?php echo base_url() ?>md/css/page-center.css" type="text/css" rel="stylesheet" media="screen,projection">

    </head>
    <!--<body class="light-blue">-->
    <body  >
        <!-- Start Page Loading -->
        <div id="loader-wrapper">
            <div id="loader"></div>        
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
        <!-- End Page Loading -->



        <div id="login-page" class="row">
            <div class="col s12 z-depth-4 card-panel" style="width: 300px;">
                <form id="loginform" name="loginform" class="form-horizontal" autocomplete="off" role="form" method="post">
                    <div class="row">
                        <div class="input-field col s12 center">
                            <img src="<?php echo base_url() ?>md/images/login-logo.png" alt="" class="circle responsive-img valign profile-image-login">
                            <p class="center login-form-text">EXTRANET MARIANISTA</p>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="input-field col s12">
                            <i class="mdi-social-person-outline prefix"></i>
                            <input id="email" name="email" type="text">
                            <label for="username" class="center-align">Usuario</label>
                        </div>
                    </div>
                    <div class="row margin">
                        <div class="input-field col s12">
                            <i class="mdi-action-lock-outline prefix"></i>
                            <input id="password" name="password" type="password">
                            <label for="password">Contraseña</label>
                        </div>
                    </div>
                    <div class="row">          
                        <div class="input-field col s12 m12 l12  login-text">
                            <input type="checkbox" id="remember-me" />
                            <label for="remember-me">Recordarme</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <!-- <a href="javascript:void();" class="btn waves-effect waves-light col s12">Ingresar</a>-->
                            <center>
                                <button type="submit" id="btingresar" name="btingresar" class="btn btn-primary"><span class="glyphicon glyphicon-log-in"></span> Ingresar</button>
                            </center>		   
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m12 l12">
                            <p class="margin medium-small">
                            <div id="mensaje"></div>
                            </p>
                        </div>
                    </div>

                    <!--
                       <div class="row">
                       <div class="input-field col s6 m6 l6">
                         <p class="margin medium-small"><a href="register.html">Registrarse</a></p>
                       </div>
                       <div class="input-field col s6 m6 l6">
                           <p class="margin right-align medium-small"><a href="forgot.html">Recuperar Clave</a></p>
                       </div>          
                     </div>
                    -->
                </form>
            </div>
        </div>



        <!-- ================================================
          Scripts
          ================================================ -->
        <script type="text/javascript">
            var baseurl = "<?php echo base_url(); ?>";
        </script>
        <script language="Javascript">
            document.oncontextmenu = function () {
                return false
            }
        </script>            
        <!-- jQuery Library -->
        <script type="text/javascript" src="<?php echo base_url() ?>md/js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url() ?>js/JsLogin.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/JsValidacion.js"></script>
        <!--materialize js-->
        <script type="text/javascript" src="<?php echo base_url() ?>md/js/materialize.js"></script>
        <!--prism-->
        <script type="text/javascript" src="<?php echo base_url() ?>md/js/prism.js"></script>
        <!--scrollbar-->
        <script type="text/javascript" src="<?php echo base_url() ?>md/js/perfect-scrollbar.min.js"></script>
        <!--plugins.js - Some Specific JS codes for Plugin Settings-->
        <script type="text/javascript" src="<?php echo base_url() ?>md/js/plugins.js"></script>

    </body>

</html>