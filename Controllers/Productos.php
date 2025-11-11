<?php
class Productos extends Controllers {
    /** @var ProductosModel */
    public $model;

    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/../Models/ProductosModel.php';
        require_once __DIR__ . '/../Helpers/Helpers.php';
        $this->model = new ProductosModel();
    }

    private function requireEmpleadoOrAdmin() {
        if (empty($_SESSION['empleado']) && empty($_SESSION['admin'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
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
        // Initialize session permissions if not set
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        $data['page_tag'] = "Productos";
        $data['page_name'] = "productos";
        $data['page_title'] = "PRODUCTOS <small> Tienda Online</small>";
        $data['page_functions_js'] = "functions_productos.js";
        $this->views->getView($this, "productos", $data);
    }

    // Método por defecto que se llama cuando se accede a /productos sin método específico
    public function index() {
        $this->Productos();
    }

    // Alias para listar productos (usado en redirecciones)
    public function listar() {
        $this->Productos();
    }

    public function getProductos() {
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        try {
            $arrData = $this->model->obtenerTodos();
            
            // Emergency fallback - If model fails, get data directly
            if (empty($arrData)) {
                error_log("ProductosController::getProductos - Model returned empty, trying direct query");
                
                // Direct database query as fallback
                require_once 'Libraries/Core/Conexion.php';
                $conexion = new Conexion();
                $pdo = $conexion->connect();
                
                if ($pdo) {
                    $stmt = $pdo->query("SELECT p.*, 
                                               c.nombre as Nombre_Categoria, 
                                               sc.Nombre_SubCategoria, 
                                               pr.Nombre_Proveedor
                                        FROM producto p
                                        LEFT JOIN subcategoria sc ON p.SubCategoria_idSubCategoria = sc.idSubCategoria
                                        LEFT JOIN categoria c ON sc.categoria_idcategoria = c.idcategoria
                                        LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                                        ORDER BY p.idProducto DESC");
                    $arrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    error_log("ProductosController::getProductos - Direct query returned: " . count($arrData) . " products");
                }
            }
            
            // Debug log
            error_log("ProductosController::getProductos - Productos obtenidos: " . count($arrData));
            if (count($arrData) > 0) {
                error_log("ProductosController::getProductos - Primer producto: " . print_r($arrData[0], true));
            }
            
            // Format data for DataTables
            for($i = 0; $i < count($arrData); $i++) {
                // Ensure all expected fields exist
                $arrData[$i]['codigo_barras'] = $arrData[$i]['codigo_barras'] ?? '';
                $arrData[$i]['Marca'] = $arrData[$i]['Marca'] ?? '';
                $arrData[$i]['Stock'] = $arrData[$i]['Stock_Actual'] ?? 0; // Mapeo correcto desde Stock_Actual
                $arrData[$i]['En_Oferta'] = $arrData[$i]['En_Oferta'] ?? 0;
                $arrData[$i]['Destacado'] = $arrData[$i]['Es_Destacado'] ?? 0; // Mapeo correcto desde Es_Destacado
                $arrData[$i]['Estado_Producto'] = $arrData[$i]['Estado_Producto'] ?? 'Activo';
                
                // Para compatibilidad con el DataTable, mapear imagen_blob a imagen
                if (!empty($arrData[$i]['imagen_blob'])) {
                    // Usar nueva URL que sirve desde BLOB
                    $arrData[$i]['imagen'] = BASE_URL . '/productos/obtenerImagen/' . $arrData[$i]['idProducto'];
                    $arrData[$i]['ruta'] = 'blob'; // Indicador de que viene de BLOB
                } else {
                    $arrData[$i]['imagen'] = null;
                    $arrData[$i]['ruta'] = null;
                }
                
                // Remove BLOB data from output (can't be JSON encoded)
                unset($arrData[$i]['imagen_blob']);
                unset($arrData[$i]['imagen_tipo']);
                unset($arrData[$i]['imagen_nombre']);
                
                // Calculate margin percentage
                $precioCosto = floatval($arrData[$i]['Precio_Costo'] ?? 0);
                $precioVenta = floatval($arrData[$i]['Precio_Venta'] ?? 0);
                if($precioCosto > 0 && $precioVenta > 0) {
                    $margen = (($precioVenta - $precioCosto) / $precioCosto) * 100;
                    $arrData[$i]['margen_porcentaje'] = number_format($margen, 1) . '%';
                } else {
                    $arrData[$i]['margen_porcentaje'] = '0%';
                }
                
                // Format prices
                $arrData[$i]['precio_formateado'] = SMONEY . number_format(floatval($arrData[$i]['Precio_Venta'] ?? 0), 2, '.', ',');
                $arrData[$i]['precio_costo_formateado'] = SMONEY . number_format(floatval($arrData[$i]['Precio_Costo'] ?? 0), 2, '.', ',');
                
                $precioOferta = floatval($arrData[$i]['Precio_Oferta'] ?? 0);
                $arrData[$i]['precio_oferta_formateado'] = $precioOferta > 0 ? SMONEY . number_format($precioOferta, 2, '.', ',') : null;
                
                // Add action buttons
                $btnView = '<button class="btn btn-info btn-sm" onclick="fntViewInfo('.$arrData[$i]['idProducto'].')" title="Ver"><i class="far fa-eye"></i></button>';
                $btnEdit = '<button class="btn btn-primary btn-sm" onclick="fntEditInfo('.$arrData[$i]['idProducto'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                $btnDelete = '<button class="btn btn-danger btn-sm" onclick="fntDelInfo('.$arrData[$i]['idProducto'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                $arrData[$i]['options'] = '<div class="btn-group" role="group">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
            }
            
            error_log("ProductosController::getProductos - About to send JSON with " . count($arrData) . " products");
            
            header('Content-Type: application/json');
            $jsonResponse = json_encode(['data' => $arrData], JSON_UNESCAPED_UNICODE);
            
            error_log("ProductosController::getProductos - JSON length: " . strlen($jsonResponse));
            
            echo $jsonResponse;
            
        } catch (Exception $e) {
            error_log("Error in getProductos: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['data' => [], 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setProducto() {
        // Limpiar cualquier output buffer previo
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Set proper JSON content type and CORS headers
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        try {
            if ($_POST) {
                if (empty($_POST['txtNombre']) || empty($_POST['txtSKU']) || empty($_POST['txtPrecioCosto']) || empty($_POST['txtPrecio']) || empty($_POST['txtStock'])) {
                    $arrResponse = array("status" => false, "msg" => 'Required fields are missing.');
                } else {
                    $idProducto = intval($_POST['idProducto']);
                    $strNombre = strClean($_POST['txtNombre']);
                    $strSKU = strClean($_POST['txtSKU']);
                    $strCodigoBarras = strClean($_POST['txtCodigoBarras'] ?? '');
                    $strDescripcion = strClean($_POST['txtDescripcion'] ?? '');
                    $fltPrecioCosto = floatval($_POST['txtPrecioCosto']);
                    $fltPrecio = floatval($_POST['txtPrecio']);
                    $fltPrecioOferta = floatval($_POST['txtPrecioOferta'] ?? 0);
                    $fltMargenGanancia = floatval($_POST['txtMargenGanancia'] ?? 0);
                    $intStock = intval($_POST['txtStock']);
                    $intStatus = intval($_POST['listStatus'] ?? 1);
                    $strStatus = ($intStatus == 1) ? 'Activo' : (($intStatus == 3) ? 'Descontinuado' : 'Inactivo');
                    $strMarca = strClean($_POST['txtMarca'] ?? '');
                    $intCategoria = intval($_POST['listCategoriaPrincipal'] ?? 0);
                    $intSubcategoria = intval($_POST['listCategoria'] ?? 0);
                    $intEnOferta = intval($_POST['chkEnOferta'] ?? 0);
                    $intDestacado = intval($_POST['chkDestacado'] ?? 0);
                    
                    // Procesar imágenes como BLOB en lugar de guardar archivos
                    $imagenBlob = null;
                    $imagenTipo = '';
                    $imagenNombre = '';
                    $totalImages = intval($_POST['totalImages'] ?? 0);
                    
                    // Debug logging
                    error_log("DEBUG: Total images to process: " . $totalImages);
                    error_log("DEBUG: Files received: " . print_r(array_keys($_FILES), true));
                    
                    if ($totalImages > 0) {
                        // Procesar solo la primera imagen por ahora
                        $fileKey = "imagen_0";
                        error_log("DEBUG: Looking for file key: " . $fileKey);
                        
                        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == 0) {
                            error_log("DEBUG: Processing file: " . $_FILES[$fileKey]['name']);
                            $imageResult = $this->processImageToBlob($_FILES[$fileKey]);
                            
                            if ($imageResult['status']) {
                                $imagenBlob = $imageResult['blob'];
                                $imagenTipo = $imageResult['tipo'];
                                $imagenNombre = $imageResult['nombre'];
                                error_log("DEBUG: Imagen procesada exitosamente como BLOB. Tamaño: " . strlen($imagenBlob) . " bytes");
                            } else {
                                $arrResponse = array('status' => false, 'msg' => $imageResult['msg']);
                                ob_clean();
                                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                                die();
                            }
                        }
                    }
                    
                    if($idProducto == 0) {
                        // Create new product using BLOB method
                        try {
                            $request_producto = $this->model->insertarConImagenBlob($strNombre, $strSKU, $strCodigoBarras, $strDescripcion, $fltPrecioCosto, $fltPrecio, $fltPrecioOferta, $fltMargenGanancia, $intStock, $strStatus, $strMarca, $intSubcategoria, $intEnOferta, $intDestacado, $imagenBlob, $imagenTipo, $imagenNombre);
                            if ($request_producto > 0) {
                                $arrResponse = array('status' => true, 'msg' => 'Producto creado exitosamente.');
                            } else if ($request_producto == 'exist') {
                                $arrResponse = array('status' => false, 'msg' => 'El SKU ya existe.');
                            } else {
                                $arrResponse = array("status" => false, "msg" => 'No se pudo guardar el producto.');
                            }
                        } catch (Exception $e) {
                            $arrResponse = array('status' => false, 'msg' => 'Error de base de datos: ' . $e->getMessage());
                        }
                    } else {
                        // Update existing product using BLOB method
                        $request_producto = $this->model->actualizarConImagenBlob($idProducto, $strNombre, $strSKU, $strCodigoBarras, $strDescripcion, $fltPrecioCosto, $fltPrecio, $fltPrecioOferta, $fltMargenGanancia, $intStock, $strStatus, $strMarca, $intSubcategoria, $intEnOferta, $intDestacado, $imagenBlob, $imagenTipo, $imagenNombre);
                        if ($request_producto == 1) {
                            $arrResponse = array('status' => true, 'msg' => 'Producto actualizado correctamente.');
                        } else if ($request_producto == 'exist') {
                            $arrResponse = array('status' => false, 'msg' => 'SKU ya existe.');
                        } else {
                            $arrResponse = array("status" => false, "msg" => 'No se pudo actualizar el producto.');
                        }
                    }
                }
            } else {
                $arrResponse = array("status" => false, "msg" => 'No se recibieron datos POST.');
            }
            
        } catch (Exception $e) {
            error_log("Error en setProducto: " . $e->getMessage());
            $arrResponse = array('status' => false, 'msg' => 'Error interno del servidor: ' . $e->getMessage());
        }
        
        // Enviar respuesta JSON limpia
        ob_clean();
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getProducto($idProducto) {
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        $intIdProducto = intval($idProducto);
        
        if($intIdProducto > 0) {
            try {
                $arrData = $this->model->obtener($intIdProducto);
                
                if(!empty($arrData)) {
                    // Remove BLOB data to avoid JSON encoding issues (same fix as getProductos)
                    unset($arrData['imagen_blob']);
                    unset($arrData['imagen_tipo']);
                    unset($arrData['imagen_nombre']);
                    
                    // Add image URL if BLOB exists
                    if (!empty($arrData['ruta']) && $arrData['ruta'] !== 'blob') {
                        // Legacy file system image
                        $arrData['imagen_url'] = $arrData['imagen'] ? BASE_URL . '/' . $arrData['ruta'] . $arrData['imagen'] : null;
                    } else {
                        // Check if it has BLOB (we removed it but can check if there was content)
                        $arrData['imagen_url'] = BASE_URL . '/productos/obtenerImagen/' . $intIdProducto;
                    }
                    
                    $arrResponse = array('status' => true, 'data' => $arrData);
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Product not found.');
                }
            } catch (Exception $e) {
                $arrResponse = array('status' => false, 'msg' => 'Database error: ' . $e->getMessage());
            }
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Invalid product ID.');
        }
        
        header('Content-Type: application/json');
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delProducto() {
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        if($_POST) {
            try {
                $intIdProducto = intval($_POST['idProducto']);
                if ($intIdProducto <= 0) {
                    $arrResponse = array('status' => false, 'msg' => 'ID de producto inválido.');
                } else {
                    $requestDelete = $this->model->eliminar($intIdProducto);
                    if($requestDelete) {
                        $arrResponse = array('status' => true, 'msg' => 'Producto eliminado exitosamente.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No se pudo eliminar el producto. Puede tener registros relacionados.');
                    }
                }
            } catch (Exception $e) {
                $arrResponse = array('status' => false, 'msg' => 'Error en la base de datos: ' . $e->getMessage());
            }
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Método no permitido.');
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    private function uploadImage($file) {
        $uploadDir = 'Assets/images/uploads/';
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        error_log("DEBUG: Uploading file: " . $file['name'] . " Type: " . $file['type'] . " Size: " . $file['size']);

        // Verificar tipo de archivo
        if (!in_array(strtolower($file['type']), $allowedTypes)) {
            error_log("DEBUG: File type not allowed: " . $file['type']);
            return ['status' => false, 'msg' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP.'];
        }

        // Verificar tamaño
        if ($file['size'] > $maxSize) {
            return ['status' => false, 'msg' => 'El archivo es muy grande. Máximo 2MB.'];
        }

        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'producto_' . uniqid() . '.' . $extension;
        $fullPath = $uploadDir . $filename;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            error_log("DEBUG: File moved successfully to: " . $fullPath);
            return [
                'status' => true, 
                'filename' => $filename,
                'path' => $uploadDir
            ];
        } else {
            error_log("DEBUG: Failed to move file from " . $file['tmp_name'] . " to " . $fullPath);
            return ['status' => false, 'msg' => 'Error al subir el archivo.'];
        }
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

    public function eliminarImagen() {
        // Limpiar cualquier output buffer previo
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Set proper JSON content type and CORS headers
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        try {
            if ($_POST) {
                $idProducto = intval($_POST['idProducto']);
                
                if ($idProducto > 0) {
                    // Obtener información del producto
                    $producto = $this->model->obtener($idProducto);
                    
                    if ($producto) {
                        // Para sistema BLOB, solo necesitamos limpiar los campos de imagen en la base de datos
                        $updateResult = $this->model->eliminarImagenBlob($idProducto);
                        
                        if ($updateResult) {
                            $arrResponse = array('status' => true, 'msg' => 'Imagen eliminada correctamente');
                        } else {
                            $arrResponse = array('status' => false, 'msg' => 'Error al eliminar la imagen de la base de datos');
                        }
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Producto no encontrado');
                    }
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'ID de producto inválido');
                }
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Método no permitido');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error en el servidor: ' . $e->getMessage());
        }
        
        // Limpiar output buffer y enviar respuesta JSON limpia
        ob_clean();
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Muestra los detalles de un producto específico
     * GET /productos/detalle/{id}
     */
    public function detalle($id = null) {
        if (empty($id) || !ctype_digit((string)$id)) {
            header('Location: ' . BASE_URL . '/tienda');
            exit();
        }

        $productId = (int)$id;
        
        // Obtener el producto
        $producto = $this->model->obtener($productId);
        if (!$producto) {
            header('Location: ' . BASE_URL . '/tienda');
            exit();
        }

        // Obtener productos relacionados (misma categoría, excluyendo el actual)
        $productosRelacionados = [];
        if (!empty($producto['SubCategoria_idSubCategoria'])) {
            $productosRelacionados = $this->model->obtenerPorSubcategoria(
                $producto['SubCategoria_idSubCategoria'], 
                $productId, 
                4 // Límite de 4 productos relacionados
            );
        }

        // Cargar modelo de reseñas
        require_once 'Models/ResenasModel.php';
        $resenasModel = new ResenasModel();
        
        // Obtener reseñas del producto
        $resenas = $resenasModel->getResenasByProducto($productId, 10, 0);
        $estadisticas = $resenasModel->getEstadisticasResenas($productId);
        
        // Verificar si el usuario puede reseñar (logueado y compró el producto)
        $puedeResenar = false;
        $mensajeResenar = '';
        $usuarioLogueado = isset($_SESSION['usuario']['id']);
        
        if ($usuarioLogueado) {
            $usuarioId = $_SESSION['usuario']['id'];
            
            // Verificar si ya dejó reseña
            if ($resenasModel->usuarioYaReseno($usuarioId, $productId)) {
                $mensajeResenar = 'Ya has dejado una reseña para este producto';
            }
            // Verificar si compró el producto
            else if ($resenasModel->usuarioComproProducto($usuarioId, $productId)) {
                $puedeResenar = true;
            } else {
                $mensajeResenar = 'Solo puedes reseñar productos que hayas comprado';
            }
        } else {
            $mensajeResenar = 'Debes iniciar sesión para dejar una reseña';
        }

        $data = [
            'page_tag' => htmlspecialchars($producto['Nombre_Producto']),
            'page_title' => htmlspecialchars($producto['Nombre_Producto']) . ' - Alto Voltaje',
            'page_name' => 'producto_detalle',
            'producto' => $producto,
            'productos_relacionados' => $productosRelacionados,
            'resenas' => $resenas,
            'estadisticas_resenas' => $estadisticas,
            'puede_resenar' => $puedeResenar,
            'mensaje_resenar' => $mensajeResenar,
            'usuario_logueado' => $usuarioLogueado
        ];

        $this->views->getView($this, 'detalle', $data);
    }

    // Método para procesar imagen y convertir a BLOB
    private function processImageToBlob($imageFile) {
        if (!$imageFile || $imageFile['error'] !== UPLOAD_ERR_OK) {
            return ['status' => false, 'msg' => 'Error en el archivo de imagen'];
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $imageFile['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            return ['status' => false, 'msg' => 'Tipo de archivo no permitido. Solo se permiten imágenes.'];
        }

        // Validar tamaño (máximo 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($imageFile['size'] > $maxSize) {
            return ['status' => false, 'msg' => 'El archivo es muy grande. Máximo 5MB permitido.'];
        }

        // Leer el archivo como binario
        $imagenBlob = file_get_contents($imageFile['tmp_name']);
        if ($imagenBlob === false) {
            return ['status' => false, 'msg' => 'Error al procesar la imagen'];
        }

        return [
            'status' => true,
            'blob' => $imagenBlob,
            'tipo' => $fileType,
            'nombre' => $imageFile['name'],
            'size' => $imageFile['size']
        ];
    }

    // Método para servir imagen desde BLOB
    public function obtenerImagen($id) {
        if (empty($id) || !ctype_digit((string)$id)) {
            http_response_code(400);
            echo 'ID de producto inválido';
            return;
        }

        $imagen = $this->model->obtenerImagenBlob((int)$id);
        
        if (!$imagen || empty($imagen['imagen_blob'])) {
            http_response_code(404);
            echo 'Imagen no encontrada';
            return;
        }

        // Configurar headers apropiados
        header('Content-Type: ' . ($imagen['imagen_tipo'] ?? 'image/jpeg'));
        header('Content-Length: ' . strlen($imagen['imagen_blob']));
        header('Cache-Control: max-age=3600'); // Cache por 1 hora
        
        // Enviar imagen
        echo $imagen['imagen_blob'];
        exit();
    }

    private function flash(string $msg, string $type = 'info') { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; }
    private function consumeFlash() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
    private function flashFormErrors(array $errors) { $_SESSION['form_errors'] = $errors; }
    private function consumeFormErrors(): array { $e = $_SESSION['form_errors'] ?? []; unset($_SESSION['form_errors']); return $e; }
}
?>
