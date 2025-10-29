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
        // Verificar autenticación de administrador
        if (empty($_SESSION['admin'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
        
        $data['page_id'] = 2;
        $data['page_tag'] = "Dashboard - AltoVoltaje";
        $data['page_title'] = "Dashboard - AltoVoltaje";
        $data['page_name'] = "dashboard";
        
        // Fetch dashboard metrics and statistics
        $data['metrics'] = $this->dashModel->getDashboardMetrics();
        
        // Fetch providers for the Providers table
        $data['providers'] = $this->dashModel->getProviders(12);
        
        // Fetch top products for the Top Products section
        $data['top_products'] = $this->dashModel->getTopProducts(6);
        
        // Fetch recent reviews for the Reviews section
        $data['recent_reviews'] = $this->dashModel->getRecentReviews(6);
        
        // Fetch recent orders
        $data['recent_orders'] = $this->dashModel->getRecentPedidos(10);
        
        $this->views->getView($this, "dashboard", $data);
    }

    // Método por defecto
    public function index() {
        $this->dashboard();
    }
}