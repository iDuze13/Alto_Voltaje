<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class DashboardModel extends Msql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProviders(int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT id_Proveedor, Nombre_Proveedor, Email_Proveedor, Telefono_Proveedor, Ciudad_Proveedor, Provincia_Proveedor
                FROM proveedor
                ORDER BY Nombre_Proveedor ASC
                LIMIT {$limit}";
        return $this->select_all($sql) ?: [];
    }

    public function getTopProducts(int $limit = 6): array
    {
        $limit = max(1, min(20, $limit));
        $sql = "SELECT p.idProducto, 
                       p.Nombre_Producto, 
                       p.SKU,
                       p.Precio_Venta,
                       p.Stock_Actual,
                       p.Es_Destacado,
                       p.En_Oferta,
                       c.nombre as Nombre_Categoria,
                       sc.Nombre_SubCategoria,
                       pr.Nombre_Proveedor
                FROM producto p
                LEFT JOIN subcategoria sc ON p.SubCategoria_idSubCategoria = sc.idSubCategoria
                LEFT JOIN categoria c ON sc.categoria_idcategoria = c.idcategoria
                LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                WHERE p.Estado_Producto = 'Activo'
                ORDER BY p.Es_Destacado DESC, p.Stock_Actual DESC, RAND()
                LIMIT {$limit}";
        
        $products = $this->select_all($sql) ?: [];
        
        // Add simulated sales data and growth percentages
        foreach ($products as &$product) {
            $product['Sales_Count'] = rand(500, 2000);
            $product['Growth_Percentage'] = rand(-5, 25) + (rand(0, 99) / 100);
            $product['Product_Image'] = $this->getProductImage($product['Nombre_Producto'], $product['idProducto']);
        }
        
        // Sort by sales count descending
        usort($products, function($a, $b) {
            return $b['Sales_Count'] - $a['Sales_Count'];
        });
        
        return $products;
    }

    private function getProductImage(string $productName, int $productId): string
    {
        // Check if product has uploaded image
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $uploadsPath = $_SERVER['DOCUMENT_ROOT'] . '/AltoVoltaje/Assets/images/uploads/';
        
        foreach ($imageExtensions as $ext) {
            $fileName = "producto_{$productId}.{$ext}";
            if (file_exists($uploadsPath . $fileName)) {
                return base_url() . "Assets/images/uploads/{$fileName}";
            }
        }
        
        // Fallback to category-based images based on product name
        $productLower = strtolower($productName);
        $categoryImages = [
            'electronico' => 'sample-electronics.svg',
            'electronic' => 'sample-electronics.svg',
            'poster' => 'sample-poster.svg',
            'cuadro' => 'sample-poster.svg',
            'arte' => 'sample-poster.svg',
            'collar' => 'sample-jewelry.svg',
            'anillo' => 'sample-jewelry.svg',
            'joya' => 'sample-jewelry.svg',
            'necklace' => 'sample-jewelry.svg',
            'ring' => 'sample-jewelry.svg',
            'jewelry' => 'sample-jewelry.svg'
        ];
        
        foreach ($categoryImages as $keyword => $imageName) {
            if (strpos($productLower, $keyword) !== false) {
                return base_url() . "Assets/images/{$imageName}";
            }
        }
        
        // Generate a unique color for each product based on ID
        $colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4'];
        $colorIndex = $productId % count($colors);
        $color = $colors[$colorIndex];
        
        // Return a data URL with SVG for a unique colored placeholder
        // Final fallback to a generic product image
        return "data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='40' height='40' rx='8' fill='{$color}' fill-opacity='0.1'/%3E%3Ccircle cx='20' cy='20' r='8' fill='{$color}' fill-opacity='0.3'/%3E%3Ctext x='20' y='25' text-anchor='middle' fill='{$color}' font-size='12' font-weight='bold'%3E" . substr($productName, 0, 1) . "%3C/text%3E%3C/svg%3E";
    }

    public function getRecentReviews(int $limit = 6): array
    {
        // For now, return simulated review data
        // In the future, this would connect to a reviews table
        $reviews = [
            [
                'product_name' => 'Wiper Blades Brandix ML2',
                'reviewer_name' => 'Ryan Ford',
                'rating' => 3,
                'product_image' => 'sample-electronics.svg',
                'review_date' => '2024-10-01'
            ],
            [
                'product_name' => 'Electric Planer Brandix KL370090G 300 Watts',
                'reviewer_name' => 'Adam Taylor',
                'rating' => 5,
                'product_image' => 'sample-electronics.svg',
                'review_date' => '2024-09-30'
            ],
            [
                'product_name' => 'Water Tap',
                'reviewer_name' => 'Jessica Moore',
                'rating' => 3,
                'product_image' => 'sample-jewelry.svg', 
                'review_date' => '2024-09-29'
            ],
            [
                'product_name' => 'Brandix Router Power Tool 201TRXPK',
                'reviewer_name' => 'Helena Garcia',
                'rating' => 3,
                'product_image' => 'sample-electronics.svg',
                'review_date' => '2024-09-28'
            ],
            [
                'product_name' => 'Undefined Tool Readix DPS3000SY 2700 Watts',
                'reviewer_name' => 'Ryan Ford',
                'rating' => 5,
                'product_image' => 'sample-electronics.svg',
                'review_date' => '2024-09-27'
            ],
            [
                'product_name' => 'Brandix Screwdriver SCREW150',
                'reviewer_name' => 'Charlotte Jones',
                'rating' => 5,
                'product_image' => 'sample-electronics.svg',
                'review_date' => '2024-09-26'
            ]
        ];

        return array_slice($reviews, 0, $limit);
    }

    public function getDashboardMetrics(): array
    {
        $metrics = [];
        
        // Get total products count
        $sql = "SELECT COUNT(*) as total_products FROM producto WHERE Estado_Producto = 'Activo'";
        $result = $this->select($sql);
        $metrics['total_products'] = $result['total_products'] ?? 0;
        
        // Get products by category
        $sql = "SELECT c.nombre as categoria_nombre, COUNT(p.idProducto) as cantidad
                FROM categoria c
                LEFT JOIN subcategoria sc ON c.idcategoria = sc.categoria_idcategoria
                LEFT JOIN producto p ON sc.idSubCategoria = p.SubCategoria_idSubCategoria
                WHERE p.Estado_Producto = 'Activo' OR p.Estado_Producto IS NULL
                GROUP BY c.idcategoria, c.nombre
                ORDER BY cantidad DESC";
        $metrics['products_by_category'] = $this->select_all($sql) ?: [];
        
        // Get low stock products
        $sql = "SELECT COUNT(*) as low_stock_count FROM producto p 
                JOIN inventario i ON p.Inventario_id_Inventario = i.id_Inventario
                WHERE p.Estado_Producto = 'Activo' AND p.Stock_Actual <= i.Stock_Minimo";
        $result = $this->select($sql);
        $metrics['low_stock_products'] = $result['low_stock_count'] ?? 0;
        
        // Get total inventory value
        $sql = "SELECT SUM(p.Precio_Costo * p.Stock_Actual) as total_inventory_value
                FROM producto p
                WHERE p.Estado_Producto = 'Activo'";
        $result = $this->select($sql);
        $metrics['total_inventory_value'] = $result['total_inventory_value'] ?? 0;
        
        // Get users statistics
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN Rol_Usuario = 'Cliente' THEN 1 ELSE 0 END) as total_clients,
                    SUM(CASE WHEN Rol_Usuario = 'Empleado' THEN 1 ELSE 0 END) as total_employees,
                    SUM(CASE WHEN Rol_Usuario = 'Admin' THEN 1 ELSE 0 END) as total_admins
                FROM usuario 
                WHERE Estado_Usuario = 'Activo'";
        $result = $this->select($sql);
        $metrics['users_stats'] = $result ?: [
            'total_users' => 0, 
            'total_clients' => 0, 
            'total_employees' => 0, 
            'total_admins' => 0
        ];
        
        // Get recent activity (simulated for now since we don't have activity logs)
        $metrics['recent_activity'] = $this->getRecentActivity();
        
        return $metrics;
    }

    public function getRecentActivity(): array
    {
        $activities = [];
        
        // Get recently added products
        $sql = "SELECT p.Nombre_Producto, p.idProducto, 'Nuevo producto agregado' as activity_type, 
                       NOW() as activity_date
                FROM producto p
                WHERE p.Estado_Producto = 'Activo'
                ORDER BY p.idProducto DESC
                LIMIT 3";
        $recentProducts = $this->select_all($sql) ?: [];
        
        foreach ($recentProducts as $product) {
            $activities[] = [
                'type' => 'product',
                'message' => "Nuevo producto: " . $product['Nombre_Producto'],
                'date' => $product['activity_date'],
                'icon' => 'fa-box'
            ];
        }
        
        // Get user registrations
        $sql = "SELECT u.Nombre_Usuario, u.Apellido_Usuario, u.Rol_Usuario, u.id_Usuario
                FROM usuario u
                WHERE u.Estado_Usuario = 'Activo'
                ORDER BY u.id_Usuario DESC
                LIMIT 2";
        $recentUsers = $this->select_all($sql) ?: [];
        
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'message' => "Nuevo {$user['Rol_Usuario']}: {$user['Nombre_Usuario']} {$user['Apellido_Usuario']}",
                'date' => date('Y-m-d H:i:s'),
                'icon' => 'fa-user'
            ];
        }
        
        // Sort by date (most recent first)
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($activities, 0, 5);
    }

    public function getRecentPedidos(int $limit = 10): array
    {
        $sql = "SELECT p.idPedido, p.Estado_Pedido, p.Total_Pedido, p.Fecha_Pedido, p.Metodo_Pago,
                       dc.Domicilio_Cliente, dc.Ciudad_Cliente,
                       u.Nombre_Usuario, u.Apellido_Usuario
                FROM pedido p
                JOIN direccion_cliente dc ON p.Direccion_Cliente_id_Direccion_Cliente = dc.id_Direcciones_Clientes
                JOIN cliente c ON dc.Cliente_id_Cliente = c.id_Cliente
                JOIN usuario u ON c.Usuario_id_Usuario = u.id_Usuario
                ORDER BY p.Fecha_Pedido DESC
                LIMIT {$limit}";
        
        $pedidos = $this->select_all($sql) ?: [];
        
        // If no real orders, return simulated data
        if (empty($pedidos)) {
            return [
                [
                    'idPedido' => '#90210',
                    'Estado_Pedido' => 'ENTREGADO',
                    'Fecha_Pedido' => '2025-09-28 14:30:00',
                    'Nombre_Usuario' => 'María',
                    'Apellido_Usuario' => 'González',
                    'Total_Pedido' => 1250.50
                ],
                [
                    'idPedido' => '#90211',
                    'Estado_Pedido' => 'ENVIADO',
                    'Fecha_Pedido' => '2025-09-29 10:15:00',
                    'Nombre_Usuario' => 'Carlos',
                    'Apellido_Usuario' => 'Rodríguez',
                    'Total_Pedido' => 890.00
                ],
                [
                    'idPedido' => '#90212',
                    'Estado_Pedido' => 'PENDIENTE',
                    'Fecha_Pedido' => '2025-10-01 16:45:00',
                    'Nombre_Usuario' => 'Ana',
                    'Apellido_Usuario' => 'Martínez',
                    'Total_Pedido' => 567.25
                ]
            ];
        }
        
        return $pedidos;
    }
}
?>
