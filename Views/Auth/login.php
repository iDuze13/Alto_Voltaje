<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
headerTienda($data);
$flash = isset($data['flash']) ? $data['flash'] : null;
?>

<div class="container p-t-80 p-b-50">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if ($flash): ?>
                <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>" role="alert">
                    <?= htmlspecialchars($flash['msg']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab">Login</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="registro-tab" data-toggle="tab" href="#registro" role="tab">Registro</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="empleado-tab" data-toggle="tab" href="#empleado" role="tab">Empleados</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab">Admin</a>
                        </li>
                    </ul>
                    <div class="tab-content p-t-20">
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <?php if (!empty($data['googleUrl'])): ?>
                            <div class="mb-3">
                                <a class="btn btn-light w-100 border" href="<?= htmlspecialchars($data['googleUrl']) ?>">
                                    Continuar con Google
                                </a>
                            </div>
                            <?php endif; ?>
                            <form method="POST" action="<?= BASE_URL ?>/auth/doLogin">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" name="password" id="loginPassword" required />
                                        </button>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">Ingresar</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="registro" role="tabpanel">
                            <?php if (!empty($data['googleUrl'])): ?>
                            <div class="mb-3">
                                <a class="btn btn-light w-100 border" href="<?= htmlspecialchars($data['googleUrl']) ?>">
                                    Registrarse con Google
                                </a>
                            </div>
                            <?php endif; ?>
                            <form method="POST" action="<?= BASE_URL ?>/auth/register">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input class="form-control" type="text" name="nombre" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contraseña (mín. 6 caracteres)</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" name="password" id="registerPassword" minlength="6" required />
                                        </button>
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="terminos" id="terminos" required />
                                    <label class="form-check-label" for="terminos">Acepto los términos y condiciones</label>
                                </div>
                                <button class="btn btn-success w-100" type="submit">Registrarme</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="empleado" role="tabpanel">
                            <form method="POST" action="<?= BASE_URL ?>/auth/empleado">
                                <div class="mb-3">
                                    <label class="form-label">ID de Empleado</label>
                                    <input class="form-control" type="text" name="id_Empleado" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">CUIL</label>
                                    <input class="form-control" type="text" name="cuil" maxlength="20" required />
                                </div>
                                <button class="btn btn-warning w-100" type="submit">Ingresar</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="admin" role="tabpanel">
                            <form method="POST" action="<?= BASE_URL ?>/auth/admin">
                                <div class="mb-3">
                                    <label class="form-label">Usuario (email)</label>
                                    <input class="form-control" type="text" name="usuario" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" name="password" id="adminPassword" required />
                                    </div>
                                </div>
                                <button class="btn btn-dark w-100" type="submit">Ingresar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.input-group .btn-outline-secondary {
    border-color: #ced4da;
}

.input-group .btn-outline-secondary:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
}

.input-group .btn-outline-secondary:focus {
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
}

.input-group .btn-outline-secondary i {
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para alternar visibilidad de contraseña
    function togglePasswordVisibility(inputId, iconId, toggleId) {
        const passwordInput = document.getElementById(inputId);
        const passwordIcon = document.getElementById(iconId);
        const toggleButton = document.getElementById(toggleId);
        
        if (passwordInput && passwordIcon && toggleButton) {
            toggleButton.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            });
        }
    }
    
    // Aplicar la funcionalidad a todos los campos de contraseña
    togglePasswordVisibility('loginPassword', 'loginPasswordIcon');
    togglePasswordVisibility('registerPassword', 'registerPasswordIcon');
    togglePasswordVisibility('adminPassword', 'adminPasswordIcon');
});
</script>

<?php footerTienda($data); ?>
