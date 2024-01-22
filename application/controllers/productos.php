<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');

class productos extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('seguridad_model');
        $this->load->model('productos_model');
        $this->load->helper('date');
    }

    public function index()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        /**/
        $this->load->view('constant');
        $this->load->view('view_header');
        $data['productos'] = $this->productos_model->ListarProductos();
        $this->load->view('productos/view_productos', $data);
        $this->load->view('view_footer');
    }

    public function deleteproducto()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $Productos = json_decode($this->input->post('MiProducto'));
        $id = base64_decode($Productos->Id);
        $codigo = base64_decode($Productos->Codigo);
        /* Array de response */
        $response = array(
            "estatus" => false,
            "error_msg" => ""
        );
        $this->productos_model->EliminarProducto($id);
        $response["error_msg"] = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Producto Eliminado Correctamente Clave: <strong>" . $codigo . "</strong>, La Información de Actualizara en 5 Segundos <meta http-equiv='refresh' content='5'></div>";
        echo json_encode($response);
    }

    public function editarProducto($id = NULL)
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $id = base64_decode($id);
        $data["productos"] = $this->productos_model->BuscarProducto($id);
        $data["titulo"] = "Editar Producto";
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('productos/view_nuevo_producto', $data);
        $this->load->view('view_footer');
    }

    public function nuevo()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $this->load->view('constant');
        $this->load->view('view_header');
        $data["titulo"] = "Nuevo Producto";
        $this->load->view('productos/view_nuevo_producto', $data);
        $this->load->view('view_footer');
    }

    public function categorias()
    {
        $categorias = $this->productos_model->Categorias();
        echo json_encode($categorias);
    }

    public function subcategorias()
    {
        $idCategoria = $this->input->get("filtro");
        $subcategorias = $this->productos_model->Subcategorias($idCategoria);
        echo json_encode($subcategorias);
    }

    public function proveedores()
    {
        $proveedores = $this->productos_model->Proveedores();
        echo json_encode($proveedores);
    }

    public function GuardaProductos()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        $Productos = json_decode($this->input->post('Productos'));
        /* Array de response */
        $response = array(
            "estatus" => false,
            "campo" => "",
            "error_msg" => ""
        ); //
        if ($Productos->Codigo == "") {
            $response["campo"] = "codigo";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Codigo de Barras es Obligatorio</div>";
            echo json_encode($response);
        } else if ($Productos->Descripcion == "") {
            $response["campo"] = "descripcion";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Descripcion del Producto es obligatorio</div>";
            echo json_encode($response);
        } else if ($Productos->Pcompra == "") {
            $response["campo"] = "pcompra";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Precio Compra es Obligatorio</div>";
            echo json_encode($response);
        } else if ($Productos->Pventa == "") {
            $response["campo"] = "pventa";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Precio Venta es obligatorio</div>";
            echo json_encode($response);
        } else if ($Productos->unidadmedida == "0") {
            $response["campo"] = "unidadmedida";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Unidad de Medida es Obligatorio</div>";
            echo json_encode($response);
        } else if ($Productos->Categoria == "0") {
            $response["campo"] = "categoria";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>Elige una Categoria</div>";
            echo json_encode($response);
        } else if ($Productos->SubCategoria == "0") {
            $response["campo"] = "subcategoria";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>Elige una Sub-Categoria</div>";
            echo json_encode($response);
        } else if ($Productos->Inventario == "0") {
            $response["campo"] = "inventario";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>Elige una Si es Inventariable el Producto</div>";
            echo json_encode($response);
        } else if ($Productos->Inventario == "1" and ( $Productos->Stock == "" or $Productos->Stock == "0")) {
            $response["campo"] = "stock";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Stock Es Obligatorio</div>";
            echo json_encode($response);
        } else if ($Productos->Proveedor == "0") {
            $response["campo"] = "proveedor";
            $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Proveedor Es Obligatorio</div>";
            echo json_encode($response);
        } else {
            /* Verificamos si Existe el codigo de barras */
            if ($Productos->Id == "") {
                $ExisteProducto = $this->productos_model->ExisteCodigo($Productos->Codigo);
                if ($ExisteProducto == true) {
                    $response["campo"] = "codigo";
                    $response["error_msg"] = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Codigo de Barras Ya esta en Uso</div>";
                    echo json_encode($response);
                } else {
                    $RegistrProducto = array(
                        'codigo' => $Productos->Codigo,
                        'descripcion' => $Productos->Descripcion,
                        'precio_compra' => $Productos->Pcompra,
                        'precio_venta' => $Productos->Pventa,
                        'id_categoria' => $Productos->Categoria,
                        'id_subcategoria' => $Productos->SubCategoria,
                        'inventariable' => $Productos->Inventario,
                        'stock' => $Productos->Stock,
                        'id_proveedor' => $Productos->Proveedor,
                        'unidadmedida' => $Productos->unidadmedida,
                        'fecha' => date('Y-m-j H:i:s')
                    );
                    $this->productos_model->SaveProductos($RegistrProducto);
                    $response["error_msg"] = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Informacion Guardada Correctamente</div>";
                    echo json_encode($response);
                }
            } else {
                $UpdateProductos = array(
                    'codigo' => $Productos->Codigo,
                    'descripcion' => $Productos->Descripcion,
                    'precio_compra' => $Productos->Pcompra,
                    'precio_venta' => $Productos->Pventa,
                    'id_categoria' => $Productos->Categoria,
                    'id_subcategoria' => $Productos->SubCategoria,
                    'inventariable' => $Productos->Inventario,
                    'stock' => $Productos->Stock,
                    'id_proveedor' => $Productos->Proveedor,
                    'unidadmedida' => $Productos->unidadmedida,
                    'FechaEdicion' => date('Y-m-j H:i:s')
                );
                $this->productos_model->UpdateProductos($UpdateProductos, $Productos->Id);
                $response["error_msg"] = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Informacion Actualizada Correctamente</div>";
                echo json_encode($response);
            }
        }
    }

}
