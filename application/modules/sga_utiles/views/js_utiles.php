<script type="text/javascript" >
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });
        $('[data-toggle="tooltip"]').tooltip();
        mayuscula("input#txtSearch");
        $("#txtSearch").keyup(function (key) {
            var len = $.trim($(this).val()).length;
            console.log("len : " + len);
            var data = {
                'vfiltro': $(this).val()
            };
            if (len >= 2) {
                $.ajax({
                    url: "<?= BASE_URL ?>sga_utiles/filtro",
                    type: "POST",
                    dataType: "html",
                    data: data,
                    beforeSend: function () {
                        //$('.loading').show();
                    },
                    success: function (data) {
                        //  console.log(data);
                        $("#viewUtiles tbody").html("");
                        $("#viewUtiles tbody").html(data);
                    },
                    complete: function () {
                        // $('.loading').hide();
                    }});
            }
        });

        /* $(".optChek").bind("click", function (e) {
         var msg = window.confirm("ESTA SEGURO DE MARCAR COMO RECIBIDO. ?");
         if (msg) {
         var data = {
         'vid': $(this).attr("value")
         };
         $.ajax({
         url: "<?= BASE_URL ?>sga_utiles/marca",
         type: "POST",
         dataType: "json",
         data: data,
         beforeSend: function () {
         //$('.loading').show();
         },
         success: function (data) {
         alert(data['msg']);
         if (data['flg'] == 0) {
         js_cargaCompleta();
         }
         },
         complete: function () {
         // $('.loading').hide();
         }});
         }
         });*/

    });

    function mayuscula(campo) {
        $(campo).keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    }

    function js_marca(idAlu, flg) {
        if (flg == 1)
            return false;
        var msg = window.confirm("ESTA SEGURO DE MARCAR COMO RECIBIDO. ?");
        if (msg) {
            var data = {
                'vid': idAlu
            };
            $.ajax({
                url: "<?= BASE_URL ?>sga_utiles/marca",
                type: "POST",
                dataType: "json",
                data: data,
                beforeSend: function () {
                    //$('.loading').show();
                },
                success: function (data) {
                    alert(data['msg']);
                    if (data['flg'] == 0) {
                        js_cargaCompleta();
                    }
                },
                complete: function () {
                    // $('.loading').hide();
                }});
        }
    }

    function js_cargaCompleta() {
        $("#txtSearch").val("");
        $.ajax({
            url: "<?= BASE_URL ?>sga_utiles/mostrarAll",
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                //$('.loading').show();
            },
            success: function (data) {
                console.log(data);
                $("#viewUtiles tbody").html("");
                $("#viewUtiles tbody").html(data);
            },
            complete: function () {
                // $('.loading').hide();
            }});
    }
</script>