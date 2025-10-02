<?php
class Auth extends Controllers {
    
    public function __construct() {
        parent::__construct();
        session_start();
    }

    /**
     * Display login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            $this->redirect('dashboard/dashboard');
            return;
        }
        
        $data['page_tag'] = "Login - Alto Voltaje";
        $data['page_title'] = "Iniciar Sesión";
        $data['page_name'] = "login";
        $this->views->getView($this, "login", $data);
    }

    /**
     * Process login
     */
    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate credentials (simplified version)
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // TODO: Add proper authentication logic here
            // For now, just check if fields are not empty
            if (!empty($username) && !empty($password)) {
                // Simulate successful login
                $_SESSION['login'] = true;
                $_SESSION['userData'] = [
                    'idUser' => 1,
                    'username' => $username,
                    'rol' => 'admin'
                ];
                
                $this->redirect('dashboard/dashboard');
            } else {
                $this->redirect('auth/login');
            }
        } else {
            $this->redirect('auth/login');
        }
    }

    /**
     * Logout user and redirect to login page
     */
    public function logout() {
        // Clear all session variables
        $_SESSION = array();
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        $this->redirect('auth/login');
    }

    /**
     * Helper method to redirect
     */
    private function redirect($route) {
        header('Location: ' . BASE_URL . '/' . $route);
        exit();
    }
}
