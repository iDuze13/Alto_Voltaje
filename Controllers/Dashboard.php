<?php
class Dashboard extends Controllers {
    /** @var DashboardModel */
    public $dashModel;
    
    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/../Models/DashboardModel.php';
        $this->dashModel = new DashboardModel();
    }
    
    public function dashboard() {
<<<<<<< Updated upstream
        // Verificar autenticación - permitir tanto admin como empleado
=======
        // Verificar autenticación de administrador o empleado
>>>>>>> Stashed changes
        if (empty($_SESSION['admin']) && empty($_SESSION['empleado'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
        
        // Determinar el rol actual
        $isAdmin = !empty($_SESSION['admin']);
        $isEmpleado = !empty($_SESSION['empleado']);
        
        // Obtener datos del usuario según el rol
        if ($isAdmin) {
            $userData = $_SESSION['admin'];
            $userRole = 'Admin';
            $userName = $userData['nombre'];
        } else {
            $userData = $_SESSION['empleado'];
            $userRole = 'Empleado';
            $userName = $userData['nombre'];
        }
        
        $data['page_id'] = 2;
        $data['page_tag'] = "Dashboard - AltoVoltaje";
        $data['page_title'] = "Dashboard - AltoVoltaje";
        $data['page_name'] = "dashboard";
        
        // Datos del usuario actual
        $data['user_role'] = $userRole;
        $data['user_name'] = $userName;
        $data['is_admin'] = $isAdmin;
        $data['is_empleado'] = $isEmpleado;
        
        // Fetch dashboard metrics and statistics
        $data['metrics'] = $this->dashModel->getDashboardMetrics();
        
        // Solo Admin puede ver proveedores
        if ($isAdmin) {
            $data['providers'] = $this->dashModel->getProviders(12);
        }
        
        // Ambos pueden ver productos top
        $data['top_products'] = $this->dashModel->getTopProducts(6);
        
        // Solo Admin puede ver reseñas
        if ($isAdmin) {
            $data['recent_reviews'] = $this->dashModel->getRecentReviews(6);
        }
        
        // Ambos pueden ver pedidos recientes
        $data['recent_orders'] = $this->dashModel->getRecentPedidos(10);
        
        $this->views->getView($this, "dashboard", $data);
    }

    // Método por defecto
    public function index() {
        $this->dashboard();
    }
}
