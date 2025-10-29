<?php
class Empleados extends Controllers {
    private $productosModel;
    
    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/../Models/EmpleadosModel.php';
        require_once __DIR__ . '/../Models/ProductosModel.php';
        $this->model = new EmpleadosModel();
        $this->productosModel = new ProductosModel();
    }

    private function requireEmpleado() {
        if (empty($_SESSION['empleado'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    public function dashboard() {
        $this->requireEmpleado();
        $idEmpleado = (int)$_SESSION['empleado']['id'];
        /** @var EmpleadosModel $this->model */
        $empleado = $this->model->getEmpleadoById($idEmpleado);
        $data = [
            'page_tag' => 'Dashboard Empleado',
            'page_title' => 'Dashboard Empleado - Alto Voltaje',
            'page_name' => 'empleado_dashboard',
            'empleado' => $empleado,
        ];
        $this->views->getView($this, 'dashboard', $data);
    }

    // Métodos para gestión de productos (misma funcionalidad que admin)
    public function productos() {
        $this->requireEmpleado();
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        $data['page_tag'] = "Productos";
        $data['page_name'] = "productos_empleado";
        $data['page_title'] = "GESTIÓN DE INVENTARIO";
        $data['page_functions_js'] = "functions_productos.js";
        $this->views->getView($this, "productos", $data);
    }

    public function getProductos() {
        $this->requireEmpleado();
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        try {
            $arrData = $this->productosModel->obtenerTodos();
            
            // Add action buttons to each product
            for($i = 0; $i < count($arrData); $i++) {
                $btnView = '<button class="btn btn-info btn-sm" onclick="viewItem('.$arrData[$i]['idProducto'].')" title="Ver"><i class="far fa-eye"></i></button>';
                $btnEdit = '<button class="btn btn-warning btn-sm" onclick="editItem('.$arrData[$i]['idProducto'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                $btnDelete = '<button class="btn btn-danger btn-sm" onclick="deleteItem('.$arrData[$i]['idProducto'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
                
                // Asegurar que todos los campos existan con valores por defecto
                $arrData[$i]['Codigo_Barras'] = $arrData[$i]['Codigo_Barras'] ?? '';
                $arrData[$i]['Marca'] = $arrData[$i]['Marca'] ?? 'Sin marca';
                $arrData[$i]['Precio_Costo'] = $arrData[$i]['Precio_Costo'] ?? '0.00';
                $arrData[$i]['Precio_Oferta'] = $arrData[$i]['Precio_Oferta'] ?? '0.00';
                $arrData[$i]['Es_Oferta'] = $arrData[$i]['Es_Oferta'] ?? '0';
                $arrData[$i]['Es_Destacado'] = $arrData[$i]['Es_Destacado'] ?? '0';
                $arrData[$i]['Imagen_Principal'] = $arrData[$i]['Imagen_Principal'] ?? '';
                $arrData[$i]['Estado_Producto'] = $arrData[$i]['Estado_Producto'] ?? 'Activo';
                $arrData[$i]['SKU'] = $arrData[$i]['SKU'] ?? 'N/A';
                $arrData[$i]['Stock_Actual'] = $arrData[$i]['Stock_Actual'] ?? '0';
                $arrData[$i]['Precio_Venta'] = $arrData[$i]['Precio_Venta'] ?? '0.00';
            }
            
            header('Content-Type: application/json');
            echo json_encode(['data' => $arrData], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'data' => [],
                'error' => 'Error al cargar productos: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setProducto() {
        $this->requireEmpleado();
        // Reutilizar la misma lógica del controlador de productos
        require_once 'Productos.php';
        $productosController = new Productos();
        $productosController->setProducto();
    }

    public function getProducto($idProducto) {
        $this->requireEmpleado();
        require_once 'Productos.php';
        $productosController = new Productos();
        $productosController->getProducto($idProducto);
    }

    public function delProducto() {
        $this->requireEmpleado();
        require_once 'Productos.php';
        $productosController = new Productos();
        $productosController->delProducto();
    }

    public function eliminarImagen() {
        $this->requireEmpleado();
        require_once 'Productos.php';
        $productosController = new Productos();
        $productosController->eliminarImagen();
    }

    // Método para cargar el contenido de productos via AJAX
    public function loadProductosContent() {
        $this->requireEmpleado();
        
        // Initialize session permissions
        if (empty($_SESSION['permisosMod'])) {
            $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
        }
        
        // Incluir el modal de productos
        ob_start();
        include __DIR__ . '/../Views/Template/Modals/modalProductos.php';
        $modalHtml = ob_get_clean();
        
        $html = '
        <div class="emp-welcome">
            <h2>Gestión de Inventario</h2>
            <p>Administra los productos de la tienda desde aquí.</p>
        </div>
        
        <div class="emp-productos-container">
            <div class="emp-productos-header">
                <h3><i class="fas fa-box"></i> Lista de Productos</h3>
                <button class="emp-btn emp-btn-primary" type="button" onclick="openModal();">
                    <i class="fas fa-plus-circle"></i> Nuevo Producto
                </button>
            </div>
            
            <div class="emp-productos-table-container">
                <table class="emp-table table-striped" id="tableProductos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>SKU</th>
                            <th>Código Barras</th>
                            <th>Nombre</th>
                            <th>Marca</th>
                            <th>P. Costo</th>
                            <th>P. Venta</th>
                            <th>P. Oferta</th>
                            <th>Margen %</th>
                            <th>Stock</th>
                            <th>Oferta</th>
                            <th>Destacado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <style>
        /* Contenedor principal que imita el estilo del admin */
        .emp-productos-container {
            background: white;
            border-radius: 0;
            border: none;
            padding: 0;
            margin-top: 20px;
            color: #333;
            box-shadow: none;
        }
        
        .emp-productos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 0;
            border-bottom: 2px solid #F5A623;
            background: transparent;
        }
        
        .emp-productos-header h3 {
            margin: 0;
            color: #F5A623;
            font-size: 24px;
            font-weight: 600;
        }
        
        .emp-productos-header h3 i {
            margin-right: 10px;
        }
        
        /* Botón estilo admin */
        .emp-btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .emp-btn-primary {
            color: white;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        
        .emp-btn-primary:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
        
        /* Contenedor de tabla estilo admin */
        .emp-productos-table-container {
            background: white;
            border-radius: 0;
            padding: 0;
            border: 1px solid #dee2e6;
            overflow-x: auto;
        }
        
        /* Tabla estilo admin exacto */
        .emp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            color: #333;
            background: white;
            margin-bottom: 0;
        }
        
        .emp-table th {
            padding: 8px 6px;
            text-align: center;
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border: 1px solid #dee2e6;
            white-space: nowrap;
            font-size: 12px;
        }
        
        .emp-table td {
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            font-size: 12px;
            white-space: nowrap;
        }
        
        .emp-table tbody tr {
            background-color: white;
        }
        
        .emp-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .emp-table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        /* Estilos para badges de estado */
        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
        
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        
        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }
        
        /* Botones de acción estilo admin */
        .emp-table .btn {
            padding: 4px 8px;
            margin: 1px;
            font-size: 11px;
            border-radius: 3px;
            border: none;
            cursor: pointer;
            color: white;
        }
        
        .emp-table .btn-info {
            background-color: #17a2b8;
        }
        
        .emp-table .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .emp-table .btn-danger {
            background-color: #dc3545;
        }
        
        /* DataTables styling para coincidir con admin */
        .dataTables_wrapper {
            color: #333 !important;
        }
        
        .dataTables_filter input {
            background: white !important;
            color: #333 !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
            padding: 5px 10px !important;
        }
        
        .dataTables_length select {
            background: white !important;
            color: #333 !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }
        
        .dataTables_info, .dataTables_paginate {
            color: #6c757d !important;
        }
        
        .paginate_button {
            background: white !important;
            color: #007bff !important;
            border: 1px solid #dee2e6 !important;
            margin: 0 2px !important;
            border-radius: 4px !important;
        }
        
        .paginate_button:hover {
            background: #e9ecef !important;
            color: #0056b3 !important;
        }
        
        .paginate_button.current {
            background: #007bff !important;
            color: white !important;
        }
        
        /* Imagen en tabla */
        .product-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        </style>
        
        <script>
        // Asegurar que jQuery esté disponible
        function ensureJQuery(callback) {
            if (typeof $ !== "undefined") {
                callback();
            } else {
                var script = document.createElement("script");
                script.src = "https://code.jquery.com/jquery-3.6.0.min.js";
                script.onload = callback;
                document.head.appendChild(script);
            }
        }
        
        // Asegurar que DataTables esté disponible
        function ensureDataTables(callback) {
            if (typeof $.fn.DataTable !== "undefined") {
                callback();
            } else {
                var link = document.createElement("link");
                link.rel = "stylesheet";
                link.href = "https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css";
                document.head.appendChild(link);
                
                var script = document.createElement("script");
                script.src = "https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js";
                script.onload = function() {
                    var script2 = document.createElement("script");
                    script2.src = "https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js";
                    script2.onload = callback;
                    document.head.appendChild(script2);
                };
                document.head.appendChild(script);
            }
        }
        
        // Asegurar que Bootstrap esté disponible
        function ensureBootstrap(callback) {
            if (typeof $.fn.modal !== "undefined") {
                callback();
            } else {
                var link = document.createElement("link");
                link.rel = "stylesheet";
                link.href = "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css";
                document.head.appendChild(link);
                
                var script = document.createElement("script");
                script.src = "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js";
                script.onload = callback;
                document.head.appendChild(script);
            }
        }
        
        // Cargar el CSS de productos
        if (!document.getElementById("productos-css")) {
            var link = document.createElement("link");
            link.id = "productos-css";
            link.rel = "stylesheet";
            link.href = "' . BASE_URL . '/Assets/css/productos-modern.css";
            document.head.appendChild(link);
        }
        
        // Inicializar todo paso a paso
        ensureJQuery(function() {
            ensureBootstrap(function() {
                ensureDataTables(function() {
                    setTimeout(function() {
                        initProductsForEmpleados();
                    }, 500);
                });
            });
        });
        
        function initProductsForEmpleados() {
            // Verificar que la tabla existe
            if (!document.getElementById("tableProductos")) {
                console.error("Table #tableProductos not found");
                return;
            }
            
            // Interceptar todas las llamadas AJAX para redirigirlas al controlador de empleados
            var originalAjax = $.ajax;
            $.ajax = function(options) {
                if (typeof options === "string") {
                    var url = options;
                    options = arguments[1] || {};
                    options.url = url;
                }
                
                // Redirigir URLs del controlador de productos al de empleados
                if (options.url) {
                    if (options.url.includes("/Productos/setProducto")) {
                        options.url = options.url.replace("/Productos/setProducto", "/Empleados/setProducto");
                    } else if (options.url.includes("/Productos/getProducto/")) {
                        options.url = options.url.replace("/Productos/getProducto/", "/Empleados/getProducto/");
                    } else if (options.url.includes("/Productos/delProducto")) {
                        options.url = options.url.replace("/Productos/delProducto", "/Empleados/delProducto");
                    } else if (options.url.includes("/Productos/getProductos")) {
                        options.url = options.url.replace("/Productos/getProductos", "/Empleados/getProductos");
                    } else if (options.url.includes("/Productos/eliminarImagen")) {
                        options.url = options.url.replace("/Productos/eliminarImagen", "/Empleados/eliminarImagen");
                    }
                }
                
                return originalAjax.call(this, options);
            };
            
            // Destruir DataTable si ya existe
            if ($.fn.DataTable.isDataTable("#tableProductos")) {
                $("#tableProductos").DataTable().destroy();
            }
            
            // Inicializar DataTable directamente
            var table = $("#tableProductos").DataTable({
                "ajax": {
                    "url": "' . BASE_URL . '/Empleados/getProductos",
                    "type": "GET",
                    "dataSrc": "data",
                    "error": function(xhr, error, thrown) {
                        console.error("Error loading products:", error, thrown);
                    }
                },
                "columns": [
                    {"data": "idProducto", "title": "ID"},
                    {"data": "Imagen_Principal", "title": "Imagen", "render": function(data, type, row) {
                        if (data && data !== "" && data !== null) {
                            return "<img src=\"' . BASE_URL . '/Assets/images/uploads/" + data + "\" class=\"product-img\" alt=\"Producto\">";
                        } else {
                            return "<img src=\"' . BASE_URL . '/Assets/images/product-placeholder.png\" class=\"product-img\" alt=\"Sin imagen\">";
                        }
                    }, "orderable": false},
                    {"data": "SKU", "title": "SKU"},
                    {"data": "Codigo_Barras", "title": "Código Barras", "render": function(data) {
                        return data || "-";
                    }},
                    {"data": "Nombre_Producto", "title": "Nombre"},
                    {"data": "Marca", "title": "Marca", "render": function(data) {
                        return data || "-";
                    }},
                    {"data": "Precio_Costo", "title": "P. Costo", "render": function(data) { 
                        return data && data > 0 ? "$" + parseFloat(data).toLocaleString("es-AR", {minimumFractionDigits: 2}) : "-"; 
                    }},
                    {"data": "Precio_Venta", "title": "P. Venta", "render": function(data) { 
                        return "$" + parseFloat(data || 0).toLocaleString("es-AR", {minimumFractionDigits: 2}); 
                    }},
                    {"data": "Precio_Oferta", "title": "P. Oferta", "render": function(data) { 
                        return data && data > 0 ? "$" + parseFloat(data).toLocaleString("es-AR", {minimumFractionDigits: 2}) : "-"; 
                    }},
                    {"data": null, "title": "Margen %", "render": function(data, type, row) {
                        if (row.Precio_Costo && row.Precio_Venta && row.Precio_Costo > 0) {
                            var margen = ((row.Precio_Venta - row.Precio_Costo) / row.Precio_Venta * 100);
                            return margen.toFixed(1) + "%";
                        }
                        return "-";
                    }, "orderable": false},
                    {"data": "Stock_Actual", "title": "Stock"},
                    {"data": "Es_Oferta", "title": "Oferta", "render": function(data) {
                        return data == 1 ? "<span class=\"badge badge-warning\">Sí</span>" : "<span class=\"badge badge-secondary\">No</span>";
                    }, "orderable": false},
                    {"data": "Es_Destacado", "title": "Destacado", "render": function(data) {
                        return data == 1 ? "<span class=\"badge badge-warning\">Sí</span>" : "<span class=\"badge badge-secondary\">No</span>";
                    }, "orderable": false},
                    {"data": "Estado_Producto", "title": "Estado", "render": function(data) {
                        if (data === "Activo") {
                            return "<span class=\"badge badge-success\">Activo</span>";
                        } else if (data === "Inactivo") {
                            return "<span class=\"badge badge-danger\">Inactivo</span>";
                        } else if (data === "Descontinuado") {
                            return "<span class=\"badge badge-warning\">Descontinuado</span>";
                        }
                        return data || "-";
                    }, "orderable": false},
                    {"data": "options", "title": "Acciones", "orderable": false}
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "processing": true,
                "serverSide": false,
                "pageLength": 10,
                "order": [[0, "desc"]],
                "drawCallback": function(settings) {
                    // DataTable drawn successfully
                }
            });
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "processing": true,
                "serverSide": false,
                "pageLength": 10,
                "order": [[0, "desc"]]
            });
            
            // Cargar el JS de productos para funciones adicionales
            if (!document.getElementById("productos-js")) {
                var script = document.createElement("script");
                script.id = "productos-js";
                script.src = "' . BASE_URL . '/Assets/js/functions_productos.js";
                document.head.appendChild(script);
            }
        }
        
        // Funciones globales necesarias para los botones del modal
        window.openModal = function() {
            if (typeof $ !== "undefined" && $.fn.modal) {
                $("#modalFormProductos").modal("show");
                $("#titleModal").text("Nuevo Producto");
                $("#btnActionForm").text("Guardar");
                if (document.getElementById("formProductos")) {
                    document.getElementById("formProductos").reset();
                }
                $("#idProducto").val("");
                $("#imgPreview").hide();
                $("#imagenesAdicionales").empty();
            } else {
                console.error("jQuery or Bootstrap modal not available");
            }
        };
        
        window.viewItem = function(id) {
            $.ajax({
                url: "' . BASE_URL . '/Empleados/getProducto/" + id,
                type: "GET",
                success: function(response) {
                    if (response.status) {
                        // Implementar vista de producto
                        alert("Ver producto ID: " + id);
                    }
                }
            });
        };
        
        window.editItem = function(id) {
            $.ajax({
                url: "' . BASE_URL . '/Empleados/getProducto/" + id,
                type: "GET",
                success: function(response) {
                    if (response.status) {
                        $("#modalFormProductos").modal("show");
                        $("#titleModal").text("Editar Producto");
                        $("#btnActionForm").text("Actualizar");
                        
                        // Llenar el formulario con los datos
                        var data = response.data;
                        $("#idProducto").val(data.idProducto);
                        $("#txtNombre").val(data.Nombre_Producto);
                        $("#txtSKU").val(data.SKU);
                        $("#txtPrecio").val(data.Precio_Venta);
                        $("#txtStock").val(data.Stock_Actual);
                        $("#listCategoria").val(data.idCategoria);
                        $("#listEstado").val(data.Estado_Producto);
                        $("#txtDescripcion").val(data.Descripcion);
                        
                        // Mostrar imagen principal si existe
                        if (data.Imagen_Principal) {
                            $("#imgPreview").attr("src", "' . BASE_URL . '/Assets/images/uploads/" + data.Imagen_Principal).show();
                        }
                    }
                }
            });
        };
        
        window.deleteItem = function(id) {
            if (confirm("¿Está seguro de eliminar este producto?")) {
                $.ajax({
                    url: "' . BASE_URL . '/Empleados/delProducto",
                    type: "POST",
                    data: { idProducto: id },
                    success: function(response) {
                        if (response.status) {
                            $("#tableProductos").DataTable().ajax.reload();
                            alert("Producto eliminado correctamente");
                        } else {
                            alert("Error al eliminar el producto");
                        }
                    }
                });
            }
        };
        </script>
        ';
        
        echo json_encode([
            'status' => true,
            'html' => $html,
            'modal' => $modalHtml
        ]);
        die();
    }
}
?>
