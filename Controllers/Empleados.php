<?php
class Empleados extends Controllers {
    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/../Models/EmpleadosModel.php';
        $this->model = new EmpleadosModel();
    }

    public function dashboard() {
        if (empty($_SESSION['empleado'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
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
}
?>
