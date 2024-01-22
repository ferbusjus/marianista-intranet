
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <script language="Javascript" type="text/javascript" src="/intranet/js/countdown/jquery-1.4.1.js"></script>
        <script language="Javascript" type="text/javascript" src="/intranet/js/countdown/jquery.lwtCountdown-1.0.js"></script>
        <script language="Javascript" type="text/javascript" src="/intranet/js/countdown/misc.js"></script>
        <link rel="Stylesheet" type="text/css" href="/intranet/assets/main.css"></link>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>COLEGIO MARIANISTA</title>
    </head>

    <body>
<?php
$tiempo = explode("*",$fechahora);
?>
        <div id="container">

            <h1>BOLETAS VIII UNIDAD <?=$txtPantalla?></h1>
            <h2 class="subtitle">Publicación <?=str_replace("-","/",$fechafin)?> PM</h2>

            <div id="countdown_dashboard">
                <div class="dash weeks_dash">
                    <span class="dash_title">Mes(es)</span>
                    <div class="digit">0</div>
                    <div class="digit">0</div>
                </div>

                <div class="dash days_dash">
                    <span class="dash_title">Dia(s)</span>
                    <div class="digit">0</div>
                    <div class="digit">0</div>
                </div>

                <div class="dash hours_dash">
                    <span class="dash_title">Hora(s)</span>
                    <div class="digit">0</div>
                    <div class="digit">0</div>
                </div>

                <div class="dash minutes_dash">
                    <span class="dash_title">Minuto(s)</span>
                    <div class="digit">0</div>
                    <div class="digit">0</div>
                </div>

                <div class="dash seconds_dash">
                    <span class="dash_title">Segundo(s)</span>
                    <div class="digit">0</div>
                    <div class="digit">0</div>
                </div>

            </div>

            <script language="javascript" type="text/javascript">
                document.oncontextmenu = function () {
                    return false
                }


                jQuery(document).ready(function () {
                    $('#countdown_dashboard').countDown({
                        targetDate: {
                            'day': <?=$tiempo[2]?>,
                            'month': <?=$tiempo[1]?>,
                            'year': <?=$tiempo[0]?>,
                            'hour': <?=$tiempo[3]?>,
                            'min': <?=$tiempo[4]?>,
                            'sec': <?=$tiempo[5]?>
                        }
                    });
                });
            </script>

        </div>
    </body>

</html>
