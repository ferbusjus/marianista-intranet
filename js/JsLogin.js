var frecuencia = 0;
$(document).ready(function () {
    //Guardamos Formulario
    $("#email").focus();
    $("#loginform").submit(function (e)
    {
         e.preventDefault();
        var Login = new Object();
        Login.UserName = $('input#email').val();
        Login.Password = $('input#password').val();
        Login.Perfil = 0; //$('#cbtipo').val();

        if ($.trim($('#email').val()) == '') {
            $("#mensaje").html("<div style='text-align:center;font-weight:bold;'>Ingrese su Usuario.</div>");
            $('#email').focus();
            frecuencia = setInterval(ajaxMensaje, 4000);
            return false;
        }
        if ($.trim($('#password').val()) == '') {
            $("#mensaje").html("<div style='text-align:center;font-weight:bold;'>Ingrese su Contraseña.</div>");
            $('#password').focus();
            frecuencia = setInterval(ajaxMensaje, 4000);
            return false;
        }

        $("#mensaje").append("<div style='text-align:center;font-weight:bold;'>Validando Credenciales...</div>");
        var DatosJson = JSON.stringify(Login);
        
       
        $.post(baseurl + 'login/ValidaAcceso',
                {
                    LoginPost: DatosJson
                },
                function (data, textStatus) {
                    if (data.success == 1) {
                        $("#mensaje").html("");
                        $("#mensaje").append("<div style='text-align:center;font-weight:bold;'>Accediendo al Sistema...</div>");
                        //$("#mensaje").html(data.error);
                        setInterval(ajaxCall, 3000);

                    } else {
                        $("#" + data.campo + "").focus();
                        $("#mensaje").html(data.error);
                    }
                },
                "json"
                );
        return false;

    });

});

function ajaxMensaje() {
    $("#mensaje").html("");
    clearTimeout(frecuencia);
}
function ajaxCall() {
    location.reload();
}