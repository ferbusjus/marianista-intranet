<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <script>
            var frecuencia =0;
            $(function () {
                $("#txtcodigo").focus();
                    $(document).bind("contextmenu",function(e){
                        return false;
                    });                
            });
            
            document.addEventListener("keydown", function(e) {
                //alert(e.keyCode);
                if (e.keyCode == 112) {
                  toggleFullScreen();
                }
              }, false);
              
            function toggleFullScreen() {
              if (!document.fullscreenElement) {
                  document.documentElement.requestFullscreen();
              } else {
                if (document.exitFullscreen) {
                  document.exitFullscreen();
                }
              }
            }
            
            function show() {
                var Digital = new Date()
                var hours = Digital.getHours()
                var minutes = Digital.getMinutes()
                var seconds = Digital.getSeconds()
                var dn = "AM"
                if (hours > 12) {
                    dn = "PM"
                }
                if (hours == 0)
                    hours = 12
                if (hours <= 9)
                    hours = "0" + hours

                if (minutes <= 9)
                    minutes = "0" + minutes
                if (seconds <= 9)
                    seconds = "0" + seconds
                $("#div_hora").html(hours + ":" + minutes + ":" + seconds + " " + dn);
                $("#txtHora").val(hours + ":" + minutes + ":" + seconds);
                setTimeout("show()", 1000)
            }
            function fecha() {
                var mydate = new Date()
                var year = mydate.getYear()
                if (year < 1000)
                    year += 1900
                var day = mydate.getDay()
                var month = mydate.getMonth()
                var daym = mydate.getDate()
                if (daym < 10)
                    daym = "0" + daym
                var dayarray = new Array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado")
                var montharray = new Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre")
                $("#div_texto").html(" Hoy es " + dayarray[day] + " " + daym + " " + montharray[month] + " del " + year);
            }
            //====================================
            function Presionar(evt) {
                if (evt.keyCode == 13) {
                    var vHora = $("#txtHora").val();
                    var vCodigo = $("#txtcodigo").val();
                    var chk = $('input:radio[name=chkAsis]:checked').val();
                    if (vCodigo == '') {
                        alert("Ingrese su Codigo ..!!");
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "<?= BASE_URL ?>sga_marcacion/ajax",
                        data: 'vCodigo=' + vCodigo + '&vHora=' + vHora+ '&vchk=' + chk,
                        success: function (qry) {
                            $("#txtcodigo").val('');
                            $("#txtcodigo").focus();
                            if ($.trim(qry) == 'Err') {
                                $("#divfoto").addClass("divfoto");
                                $("#divfoto").html('<img src="<?= BASE_URL ?>images/insignia.png" width="220" height="200" />');
                                $("#divMensaje").html("<center><h1>NO EXISTE EL C&Oacute;DIGO</h1></center>");
                            }
                            else {
                                $("#divfoto").addClass("divfoto");
                                $("#divfoto").html('<img src="<?= BASE_URL ?>images/insignia.png"  width="220" height="200" />');
                                $("#divMensaje").html(qry);
                            }
                            frecuencia = setTimeout("Rcapa()", 4000);
                        }
                    });
                }
            }

            function Rcapa() {
                $("#divMensaje").html('');
                clearTimeout(frecuencia);
                $("#divfoto").addClass("divfoto");
                $("#divfoto").html('<img src="<?= BASE_URL ?>images/insignia.png" width="220" height="200" />');
            }

        </script>
        <style>


            #div_hora{
                width:305px;
                height:65px;
                padding:3px 5px 3px 5px;
                text-align:center;
                font-weight:bold;
                font-size:46px;
                color:#FFF;
            }

            #div_texto{
                width:100%;
                height:65px;
                padding-top:15px;
                text-align:center;
                font-weight:bold;
                font-size:34px;
            }

            #divMensaje{
                width:100%;
                height:120px;
                padding-top:15px;
                padding-bottom:15px;
                text-align:center;
                font-size: 16px;
            }

            #divfoto{
                width:220px; 
                height:200px;
                margin:5px 5px 5px 5px;
                /*border:1px solid #999;*/
            }
            .efecto1{
                -webkit-box-shadow: 0 10px 6px -6px #777;
                -moz-box-shadow: 0 10px 6px -6px #777;
                box-shadow: 0 10px 6px -6px #777;
}

        </style>
    </head>
    <body onload="javascript:show();
            fecha();"> 
        <!--<h3 class="page-header"><span class="glyphicon glyphicon-th-list"></span> Asistencia de Alumnos</h3>-->
        <h3 class="page-header"></h3>
        <div id="mensaje"></div>
        <table width="100%" height="100%" valign="top" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="75%" valign="top">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="2" height="26" ></td>
                            <td width="" bgcolor="white">
                                <table width="100%">
                                    <tr>
                                        <td colspan="4" align="center" height="700px" >
                                            <input type="hidden" id="txtHora" name="txtHora" value="" />
                                            <table width="980" border="0" class="efecto1" cellspacing="0" cellpadding="0" style="border:1px solid #999">
                                                <tr>
                                                    <td height="70" colspan="3" align="center" bgcolor="#CCCCCC"><div id="div_texto"></div></td>
                                                </tr>
                                                <tr>
                                                    <td width="190" height="300" align="center"><img src="<?= BASE_URL ?>images/img_personal.jpg" width="220" height="200" /></td>
                                                    <td width="315" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td height="21" align="center"><strong>INGRESE C&Oacute;DIGO</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="42" align="center"><label>
                                                                        <input type="text" name="txtcodigo" id="txtcodigo" style="text-align:center; text-transform:uppercase; height: 40px;font-size: 20px;" onkeypress="javascript:Presionar(event);" />
                                                                    </label></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="122" align="center" valign="top">
                                                                    <div id="divMensaje"></div>
                                                                </td>
                                                            </tr>
                                                        </table></td>
                                                    <td width="195" align="center" valign="middle"><div id="divfoto"><img src="<?= BASE_URL ?>images/insignia.png" width="220" height="200"  /></div></td>
                                                </tr>
                                                <tr>
                                                    <td height="80" align="center">&nbsp;</td>
                                                    <td height="80" align="center"  background="<?= BASE_URL ?>images/marco_hora.jpg"><div id="div_hora"></div></td>
                                                    <td height="80" align="right">                    
                                                    	<table border="0" width="90%" style="text-align:center;">
                                                    	<tr>
                                                    		<td style="text-align:left;"><input type="radio" name="chkAsis" checked="checked" id="chkN" value="N" />&nbsp; Asistencia Normal </td>
                                                    	</tr>                                                    	
                                                    	<tr>
                                                    		<td style="text-align:left;"><input type="radio" name="chkAsis" id="chkP" value="P" />&nbsp; Marcar Puntual </td>
                                                    	</tr>
                                                    	<tr>
                                                    		<td style="text-align:left;"><input type="radio" name="chkAsis" id="chkT" value="T" />&nbsp; Marcar Tardanza </td>
                                        				</tr>
                                                    	</table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="15" colspan="3" align="center">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>									  
                            </td>
                            <td width="28" background="../images/a5.png"></td>
                        </tr>
                    </table>
                </td>
            </tr>		
        </table>
    </body>
</html>