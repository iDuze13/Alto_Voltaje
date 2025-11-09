<?php
    headerTienda($data);
?>

<style>
    body {
        background-color: #f5f7fa;
    }
    
    .account-settings-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .settings-header {
        margin-bottom: 30px;
    }
    
    .settings-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }
    
    .settings-layout {
        display: flex;
        gap: 30px;
        margin-top: 20px;
    }
    
    /* Sidebar Menu */
    .settings-sidebar {
        flex: 0 0 250px;
        background: white;
        border-radius: 12px;
        padding: 20px 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        height: fit-content;
    }
    
    .settings-menu-item {
        display: flex;
        align-items: center;
        padding: 15px 25px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        font-size: 15px;
    }
    
    .settings-menu-item i {
        width: 24px;
        margin-right: 12px;
        font-size: 16px;
    }
    
    .settings-menu-item:hover {
        background-color: #f8f9fa;
        color: #333;
    }
    
    .settings-menu-item.active {
        background-color: #fff8e1;
        color: #FFA500;
        border-left-color: #FFD700;
        font-weight: 500;
    }
    
    /* Main Content */
    .settings-content {
        flex: 1;
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .content-section {
        margin-bottom: 40px;
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e8e8e8;
    }
    
    /* Profile Photo Section */
    .profile-photo-section {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid #e8e8e8;
    }
    
    .profile-photo-wrapper {
        position: relative;
    }
    
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        font-size: 48px;
        font-weight: bold;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .profile-photo img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .photo-upload-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 36px;
        height: 36px;
        background: #FFD700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        cursor: pointer;
        border: 3px solid white;
        transition: all 0.3s ease;
    }
    
    .photo-upload-icon:hover {
        background: #FFA500;
        transform: scale(1.05);
    }
    
    .photo-actions {
        display: flex;
        gap: 15px;
    }
    
    .btn-upload {
        background: #FFD700;
        color: #333;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .btn-upload:hover {
        background: #FFA500;
        color: #333;
    }
    
    .btn-delete {
        background: white;
        color: #666;
        border: 1px solid #ddd;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .btn-delete:hover {
        background: #f5f5f5;
        border-color: #999;
        color: #333;
    }
    
    /* Form Styles */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 25px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }
    
    .form-group label .required {
        color: #ff4444;
        margin-left: 2px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #fafafa;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #FFD700;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
    }
    
    .form-control::placeholder {
        color: #999;
    }
    
    /* Save Button */
    .btn-save {
        background: #FFD700;
        color: #333;
        border: none;
        padding: 14px 40px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }
    
    .btn-save:hover {
        background: #FFA500;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }
    
    /* Alert Messages */
    .alert {
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 25px;
        border: none;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    /* Password Section */
    .password-section {
        margin-top: 50px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .settings-layout {
            flex-direction: column;
        }
        
        .settings-sidebar {
            flex: 1;
            display: flex;
            overflow-x: auto;
            padding: 10px;
        }
        
        .settings-menu-item {
            flex: 0 0 auto;
            padding: 12px 20px;
            white-space: nowrap;
            border-left: none;
            border-bottom: 3px solid transparent;
        }
        
        .settings-menu-item.active {
            border-left-color: transparent;
            border-bottom-color: #FFD700;
        }
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .profile-photo-section {
            flex-direction: column;
            text-align: center;
        }
        
        .settings-content {
            padding: 25px 20px;
        }
        
        .account-settings-container {
            margin: 20px auto;
        }
    }
</style>

<div class="account-settings-container">
    <div class="settings-header">
        <h1>Configuración de Cuenta</h1>
    </div>

    <div class="settings-layout">
        <!-- Sidebar Menu -->
        <aside class="settings-sidebar">
            <a href="#profile" class="settings-menu-item active" data-section="profile">
                <i class="fa fa-user"></i>
                <span>Datos del Perfil</span>
            </a>
            <a href="#password" class="settings-menu-item" data-section="password">
                <i class="fa fa-lock"></i>
                <span>Contraseña</span>
            </a>
            <a href="#notifications" class="settings-menu-item" data-section="notifications">
                <i class="fa fa-bell"></i>
                <span>Notificaciones</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="settings-content">
            <!-- Alert Messages -->
            <div id="alertMessage"></div>

            <!-- Profile Settings Section -->
            <div id="profile-section" class="content-section">
                <!-- Profile Photo -->
                <div class="profile-photo-section">
                    <div class="profile-photo-wrapper">
                        <div class="profile-photo" id="profilePhotoDisplay">
                            <?= strtoupper(substr($data['usuario']['nombre'], 0, 1)) ?>
                        </div>
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                    </div>
                    <div class="photo-actions">
                        <button type="button" class="btn-upload">
                            <i class="fa fa-upload me-2"></i>Subir Nueva
                        </button>
                        <button type="button" class="btn-delete">
                            <i class="fa fa-trash me-2"></i>Eliminar avatar
                        </button>
                    </div>
                </div>

                <!-- Profile Form -->
                <form id="formActualizarPerfil">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">
                                Nombre <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($data['usuario']['nombre']) ?>" 
                                   placeholder="Nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">
                                Apellido <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                   value="<?= htmlspecialchars($data['usuario']['apellido']) ?>" 
                                   placeholder="Apellido" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">
                                Correo Electrónico <span class="required">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($data['usuario']['email']) ?>" 
                                   placeholder="ejemplo@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">
                                Número de Teléfono
                            </label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?= isset($data['usuario']['telefono']) ? htmlspecialchars($data['usuario']['telefono']) : '' ?>"
                                   placeholder="0805 123 7890">
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fa fa-save me-2"></i>Guardar Cambios
                    </button>
                </form>
            </div>

            <!-- Password Section -->
            <div id="password-section" class="content-section password-section" style="display: none;">
                <h2 class="section-title">Cambiar Contraseña</h2>
                
                <form id="formCambiarPassword">
                    <div class="form-group">
                        <label for="passwordActual">
                            Contraseña Actual <span class="required">*</span>
                        </label>
                        <input type="password" class="form-control" id="passwordActual" 
                               name="passwordActual" placeholder="••••••••" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="passwordNueva">
                                Nueva Contraseña <span class="required">*</span>
                            </label>
                            <input type="password" class="form-control" id="passwordNueva" 
                                   name="passwordNueva" placeholder="••••••••" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                        <div class="form-group">
                            <label for="passwordConfirmar">
                                Confirmar Nueva Contraseña <span class="required">*</span>
                            </label>
                            <input type="password" class="form-control" id="passwordConfirmar" 
                                   name="passwordConfirmar" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fa fa-lock me-2"></i>Actualizar Contraseña
                    </button>
                </form>
            </div>

            <!-- Notifications Section -->
            <div id="notifications-section" class="content-section" style="display: none;">
                <h2 class="section-title">Preferencias de Notificaciones</h2>
                <p class="text-muted">Próximamente: Configura cómo quieres recibir notificaciones sobre tus pedidos y promociones.</p>
            </div>
        </main>
    </div>
</div>

<script>
    const base_url = '<?= base_url(); ?>';

    // Sidebar Navigation
    document.querySelectorAll('.settings-menu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            document.querySelectorAll('.settings-menu-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding section
            const section = this.getAttribute('data-section');
            document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
            document.getElementById(section + '-section').style.display = 'block';
            
            // Update URL hash
            window.location.hash = section;
        });
    });

    // Load section from URL hash
    window.addEventListener('load', function() {
        const hash = window.location.hash.substring(1);
        if (hash) {
            const menuItem = document.querySelector(`[data-section="${hash}"]`);
            if (menuItem) {
                menuItem.click();
            }
        }
    });

    // Show alert messages
    function mostrarAlerta(mensaje, tipo) {
        const alertDiv = document.getElementById('alertMessage');
        alertDiv.innerHTML = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        setTimeout(() => {
            alertDiv.innerHTML = '';
        }, 5000);
    }

    // Update profile form
    document.getElementById('formActualizarPerfil').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const url = base_url + '/Perfil/actualizar';
            console.log('Enviando a:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            const text = await response.text();
            console.log('Response text:', text);
            
            const result = JSON.parse(text);
            
            if (result.status === 'success') {
                mostrarAlerta(result.message, 'success');
                // Update display name
                const inicial = formData.get('nombre').charAt(0).toUpperCase();
                document.getElementById('profilePhotoDisplay').textContent = inicial;
            } else {
                mostrarAlerta(result.message || 'Error al actualizar el perfil', 'danger');
            }
        } catch (error) {
            mostrarAlerta('Error de conexión. Revisa la consola del navegador para más detalles.', 'danger');
            console.error('Error completo:', error);
        }
    });

    // Change password form
    document.getElementById('formCambiarPassword').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const passwordNueva = document.getElementById('passwordNueva').value;
        const passwordConfirmar = document.getElementById('passwordConfirmar').value;
        
        if (passwordNueva !== passwordConfirmar) {
            mostrarAlerta('Las contraseñas no coinciden', 'danger');
            return;
        }
        
        const formData = new FormData(this);
        
        try {
            const url = base_url + '/Perfil/cambiarPassword';
            console.log('Enviando a:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            const text = await response.text();
            console.log('Response text:', text);
            
            const result = JSON.parse(text);
            
            if (result.status === 'success') {
                mostrarAlerta(result.message, 'success');
                this.reset();
            } else {
                mostrarAlerta(result.message || 'Error al cambiar la contraseña', 'danger');
            }
        } catch (error) {
            mostrarAlerta('Error de conexión. Revisa la consola del navegador para más detalles.', 'danger');
            console.error('Error completo:', error);
        }
    });
</script>

<?php footerTienda($data); ?>
