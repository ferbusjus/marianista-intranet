<script>
var baseurl = "<?php echo base_url(); ?>";    
$(document).ready(function () {
    $("#email").focus();
    $("form#formulario").submit(function ()
    {
        var Mail = $('input#validamail').val();
        var IdUser = $('input#id').val();
        if (Mail == "1" && IdUser == "") {
            $("#mensaje").html("<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Correo es Incorrecto</div>");
            $("#email").focus();
            return false;
        } else {
            var Usuarios = new Object();
            Usuarios.Id = $('input#id').val();
            Usuarios.Nombre = $('input#Nombre').val();
            Usuarios.Apellidos = $('input#Apellidos').val();
            Usuarios.Email = $('input#email').val();
            Usuarios.Password1 = $('input#password1').val();
            Usuarios.Password2 = $('input#password2').val();
            $("#mensaje").append("<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Guardando Informacion...</center></div></div>");
            var DatosJson = JSON.stringify(Usuarios);
            $.post(baseurl + 'sweb_clave/save',
                    {
                        UsuariosPost: DatosJson
                    },
                    function (data, textStatus) {
                        if(data.campo =='SUCCES') {
                            $("#mensaje").html(data.error_msg);
                            setTimeout(function(){
                                    $('#cambio_clave_modal').modal('hide');
                                },2000);                            
                        } else {
                            $("#mensaje").html(data.error_msg);
                            $("#" + data.campo + "").focus();
                            
                        }
                    },
                    "json"
                    );
            return false;
        }

    });
});

</script>