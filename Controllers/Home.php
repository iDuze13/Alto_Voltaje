<?php
class Home extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    public function home() {
        $data['page_id'] = 1;
        $data['page_tag'] = "Home";
        $data['page_title'] = "Pagina Principal";
        $data['page_name'] = "home";
        $data['page_content'] = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.";
        $this->views->getView($this, "home", $data);
    }
    public function insertar() {
        if ($this->model === null) {
            die('Model not loaded! Check model file and naming.');
        }
        $data = $this->model->setUser(1616, 'Juan', 'Perez', 'wawawa@gmail.com', '555', 'Empleado');
        print_r($data);
    }
    public function verUsuario($id_Usuario) {
        $data = $this->model->getUser($id_Usuario);
        print_r($data);
    }
    public function actualizar() {
        $data = $this->model->updateUser(1616, 'Juan', 'Poblano', 'wawawa@gmail.com', '555', 'Empleado');
        print_r($data);
    }
    public function verUsuarios() {
        $data = $this->model->getUsers();
        print_r($data);
    }
    public function eliminarUsuario($id_Usuario) {
        $data = $this->model->delUsers($id_Usuario);
        print_r($data);
    }

}
