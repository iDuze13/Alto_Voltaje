<?php
class Productos extends Controllers {
    /** @var ProductosModel */
    public $model;

    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/../Models/ProductosModel.php';
        $this->model = new ProductosModel();
    }

    private function requireEmpleadoOrAdmin() {
        if (empty($_SESSION['empleado']) && empty($_SESSION['admin'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    // GET /productos/listar
    public function listar() {
        $this->requireEmpleadoOrAdmin();
        $productos = $this->model->obtenerTodos();
        $data = [
            'page_tag' => 'Gestión de Productos',
            'page_title' => 'Gestión de Productos - Alto Voltaje',
            'page_name' => 'productos_listar',
            'productos' => $productos,
            'flash' => $this->consumeFlash(),
        ];
        $this->views->getView($this, 'listar', $data);
    }

    // GET /productos/crear or /productos/editar/{id}
    public function crear($id = null) {
        $this->requireEmpleadoOrAdmin();
        $producto = null;
        if (!empty($id) && ctype_digit((string)$id)) {
            $producto = $this->model->obtener((int)$id);
            if (!$producto) {
                $this->flash('Producto no encontrado.', 'error');
                header('Location: ' . BASE_URL . '/productos/listar');
                exit();
            }
        }
        $data = [
            'page_tag' => $producto ? 'Editar Producto' : 'Crear Producto',
            'page_title' => ($producto ? 'Editar Producto' : 'Crear Producto') . ' - Alto Voltaje',
            'page_name' => 'productos_form',
            'producto' => $producto,
            'errores' => $this->consumeFormErrors(),
        ];
        $this->views->getView($this, 'form', $data);
    }

    // POST /productos/guardar
    public function guardar() {
        $this->requireEmpleadoOrAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . BASE_URL . '/productos/listar'); exit(); }

        $id = isset($_POST['idProducto']) && $_POST['idProducto'] !== '' ? (int)$_POST['idProducto'] : null;
        $d = [
            'SubCategoria_idSubCategoria' => $_POST['SubCategoria_idSubCategoria'] ?? 1,
            'Nombre_Producto' => trim($_POST['Nombre_Producto'] ?? ''),
            'Descripcion_Producto' => trim($_POST['Descripcion_Producto'] ?? ''),
            'SKU' => trim($_POST['SKU'] ?? ''),
            'Marca' => trim($_POST['Marca'] ?? ''),
            'Precio_Costo' => $_POST['Precio_Costo'] ?? '',
            'Precio_Venta' => $_POST['Precio_Venta'] ?? '',
            'Precio_Oferta' => $_POST['Precio_Oferta'] ?? '',
            'Margen_Ganancia' => $_POST['Margen_Ganancia'] ?? '',
            'Stock_Actual' => $_POST['Stock_Actual'] ?? '',
            'Estado_Producto' => $_POST['Estado_Producto'] ?? 'Activo',
            'En_Oferta' => isset($_POST['En_Oferta']) ? 1 : 0,
            'Es_Destacado' => isset($_POST['Es_Destacado']) ? 1 : 0,
            'Inventario_id_Inventario' => $_POST['Inventario_id_Inventario'] ?? 1,
            'Proveedor_id_Proveedor' => $_POST['Proveedor_id_Proveedor'] ?? 1,
        ];

        $errores = $this->validar($d, $id);
        if (!empty($errores)) {
            $this->flashFormErrors($errores);
            $redir = 'productos/crear' . ($id ? '/' . $id : '');
            header('Location: ' . BASE_URL . '/' . $redir);
            exit();
        }

        if ($id) {
            $ok = $this->model->actualizar($id, $d);
            $this->flash($ok ? 'Producto actualizado correctamente.' : 'Error al actualizar el producto.', $ok ? 'success' : 'error');
        } else {
            $newId = $this->model->crear($d);
            $this->flash($newId ? 'Producto creado correctamente.' : 'Error al crear el producto.', $newId ? 'success' : 'error');
        }
        header('Location: ' . BASE_URL . '/productos/listar');
        exit();
    }

    // GET /productos/eliminar/{id}
    public function eliminar($id = null) {
        $this->requireEmpleadoOrAdmin();
        if (empty($id) || !ctype_digit((string)$id)) {
            $this->flash('ID de producto inválido.', 'error');
            header('Location: ' . BASE_URL . '/productos/listar');
            exit();
        }
        $id = (int)$id;
        $producto = $this->model->obtener($id);
        if (!$producto) {
            $this->flash('Producto no encontrado.', 'error');
            header('Location: ' . BASE_URL . '/productos/listar');
            exit();
        }
        $ok = $this->model->eliminar($id);
        $this->flash($ok ? 'Producto eliminado correctamente.' : 'Error al eliminar el producto.', $ok ? 'success' : 'error');
        header('Location: ' . BASE_URL . '/productos/listar');
        exit();
    }

    // Admin interface methods
    public function Productos() {
        $data['page_tag'] = "Products";
        $data['page_name'] = "productos";
        $data['page_title'] = "PRODUCTS <small> Management</small>";
        $data['page_functions_js'] = "functions_productos.js";
        $this->views->getView($this, "productos", $data);
    }

    public function getProductos() {
        $arrData = $this->model->obtenerTodos();
        
        // Add action buttons to each product
        for($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '<button class="btn-action btn-edit" onclick="btnEditProducto('.$arrData[$i]['idProducto'].')" title="Edit Product"><i class="fa-solid fa-pen"></i></button>';
            $btnDelete = '<button class="btn-action btn-delete" onclick="btnDelProducto('.$arrData[$i]['idProducto'].')" title="Delete Product"><i class="fa-solid fa-trash"></i></button>';
            $arrData[$i]['options'] = '<div class="action-buttons">'.$btnEdit.' '.$btnDelete.'</div>';
        }
        
        echo json_encode(['data' => $arrData], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function setProducto() {
        if ($_POST) {
            if (empty($_POST['txtNombre']) || empty($_POST['txtSKU']) || empty($_POST['txtPrecio']) || empty($_POST['txtStock'])) {
                $arrResponse = array("status" => false, "msg" => 'Required fields are missing.');
            } else {
                $idProducto = intval($_POST['idProducto']);
                $strNombre = strClean($_POST['txtNombre']);
                $strSKU = strClean($_POST['txtSKU']);
                $strDescripcion = strClean($_POST['txtDescripcion'] ?? '');
                $fltPrecio = floatval($_POST['txtPrecio']);
                $intStock = intval($_POST['txtStock']);
                $strStatus = strClean($_POST['listStatus'] ?? 'Activo');
                $strMarca = strClean($_POST['txtMarca'] ?? '');
                
                if($idProducto == 0) {
                    // Create new product
                    $request_producto = $this->model->insertarBasico($strNombre, $strSKU, $strDescripcion, $fltPrecio, $intStock, $strStatus, $strMarca);
                    if ($request_producto > 0) {
                        $arrResponse = array('status' => true, 'msg' => 'Product created successfully.');
                    } else if ($request_producto == 'exist') {
                        $arrResponse = array('status' => false, 'msg' => 'SKU already exists.');
                    } else {
                        $arrResponse = array("status" => false, "msg" => 'Unable to save product.');
                    }
                } else {
                    // Update existing product
                    $request_producto = $this->model->actualizarBasico($idProducto, $strNombre, $strSKU, $strDescripcion, $fltPrecio, $intStock, $strStatus, $strMarca);
                    if ($request_producto == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Product updated successfully.');
                    } else if ($request_producto == 'exist') {
                        $arrResponse = array('status' => false, 'msg' => 'SKU already exists.');
                    } else {
                        $arrResponse = array("status" => false, "msg" => 'Unable to update product.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getProducto($idProducto) {
        $intIdProducto = intval($idProducto);
        if($intIdProducto > 0) {
            $arrData = $this->model->obtener($intIdProducto);
            if(!empty($arrData)) {
                $arrResponse = array('status' => true, 'data' => $arrData);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Product not found.');
            }
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Invalid product ID.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delProducto() {
        if($_POST) {
            $intIdProducto = intval($_POST['idProducto']);
            $requestDelete = $this->model->eliminar($intIdProducto);
            if($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Product deleted successfully.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Unable to delete product.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    private function validar(array $d, ?int $id = null): array {
        $e = [];
        if ($d['Nombre_Producto'] === '') $e[] = 'El nombre es obligatorio';
        if ($d['SKU'] === '') $e[] = 'El SKU es obligatorio';
        if ($d['Marca'] === '') $e[] = 'La marca es obligatoria';
        if ($d['Precio_Costo'] === '' || (float)$d['Precio_Costo'] < 0) $e[] = 'Precio de costo inválido';
        if ($d['Precio_Venta'] === '' || (float)$d['Precio_Venta'] <= 0) $e[] = 'Precio de venta inválido';
        if ((float)$d['Precio_Venta'] <= (float)$d['Precio_Costo']) $e[] = 'El precio de venta debe ser mayor al costo';
        if ($this->model->existeSKU($d['SKU'], $id)) $e[] = 'El SKU ya existe';
        return $e;
    }

    private function flash(string $msg, string $type = 'info') { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; }
    private function consumeFlash() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
    private function flashFormErrors(array $errors) { $_SESSION['form_errors'] = $errors; }
    private function consumeFormErrors(): array { $e = $_SESSION['form_errors'] ?? []; unset($_SESSION['form_errors']); return $e; }
}
?>
