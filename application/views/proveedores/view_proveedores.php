<style type="text/css">
    th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
</style>
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>";
    var currentLocation = window.location;
    function EliminarCliente(Cliente, id) {
        confirmar = confirm("Eliminar a " + Cliente + ", Recuerda Una vez Eliminado No podras Recuperarlo");

        if (confirmar) {
            document.getElementById('mensaje').innerHTML = "<div class='modal1'><div class='center1'> <center> <img src='" + baseurl + "/img/gif-load.gif'> Eliminando Cliente...</center></div></div>";
            var Cliente = new Object();
            Cliente.Id = id;
            Cliente.Codigo = codigo;
            var DatosJson = JSON.stringify(Cliente);
            $.post(currentLocation + '/deletecliente',
                    {
                        MiCliente: DatosJson
                    },
            function (data, textStatus) {
                //
                $("#mensaje").html(data.error_msg);
            },
                    "json"
                    );
        } else {
        }
    }

</script>
<h3 class="page-header"><span class="glyphicon glyphicon-list-alt"></span> Catalogo de Proveedores</h3>
<div id="mensaje"></div>
<p align="right">
    <a href="proveedores/nuevo">
        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Nuevo Proveedor</button>
    </a>  
</p>
<br/>
<table id="proveedores" border="0" cellpadding="0" cellspacing="0" width="100%" class="pretty">
    <thead>
        <tr>
            <th></th>
            <th>Nombre</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>CP</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($proveedores) {
            foreach ($proveedores as $proveedor) {
                $idProveedor = base64_encode($proveedor->id);
                echo '<tr>';
                echo '<td>';
                echo '<a href="proveedores/editarProveedor/' . $idProveedor . '"><button type="button" title="Editar Cliente" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></button></a> &nbsp;';
                ?>
            <button type="button" onclick="EliminarCliente('<?php echo $proveedor->nombre_proveedor; ?>', '<?php echo $idProveedor; ?>');" title="Eliminar Cliente" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
                <?php
                echo '</td>';
                echo '<td>' . $proveedor->nombre_proveedor . '</td>';
                echo '<td>' . $proveedor->direccion . '</td>';
                echo '<td>' . $proveedor->telefono . '</td>';
                echo '<td>' . $proveedor->cp . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan=5><center>No Existe Informacion</center></td></tr>';
        }
        ?>
</tbody>
</table>
<script type="text/javascript">

    $(document).ready(function () {
        $('#proveedores').dataTable({
            "scrollX": false
        });
    });

</script>
