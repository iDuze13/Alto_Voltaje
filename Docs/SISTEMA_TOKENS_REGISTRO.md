# Sistema de Tokens para Registro de Empleados y Administradores

## Concepto
Los empleados y administradores solo pueden registrarse usando un **token único** generado por un administrador existente.

## Implementación

### 1. Crear tabla de tokens

```sql
CREATE TABLE tokens_registro (
    id_Token INT AUTO_INCREMENT PRIMARY KEY,
    Token_Codigo VARCHAR(64) UNIQUE NOT NULL,
    Rol_Asignado ENUM('Empleado', 'Admin') NOT NULL,
    Estado_Token ENUM('Activo', 'Usado', 'Expirado') DEFAULT 'Activo',
    Creado_Por INT NOT NULL,
    Fecha_Creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Fecha_Expiracion DATETIME,
    Usado_Por INT NULL,
    Fecha_Uso DATETIME NULL,
    FOREIGN KEY (Creado_Por) REFERENCES usuario(id_Usuario),
    FOREIGN KEY (Usado_Por) REFERENCES usuario(id_Usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Evento para limpiar tokens expirados o usados automáticamente cada día
-- (Requiere que el Event Scheduler esté activo: SET GLOBAL event_scheduler = ON;)
CREATE EVENT IF NOT EXISTS limpiar_tokens_antiguos
ON SCHEDULE EVERY 1 DAY
DO
  DELETE FROM tokens_registro 
  WHERE Estado_Token = 'Usado' 
     OR (Estado_Token = 'Activo' AND Fecha_Expiracion < NOW())
     OR Fecha_Creacion < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### 2. Modificar la vista de login

Agregar un campo de token que solo aparece cuando el usuario selecciona "Registrarse como Empleado/Admin":

```html
<!-- En Views/Auth/login.php -->
<div class="form-group" id="tokenField" style="display:none;">
    <label for="registerToken">Token de registro</label>
    <input class="form-control clean-input" type="text" name="token" id="registerToken" 
           placeholder="Ingresa el token proporcionado por un administrador" />
    <small class="form-help">Solo empleados y administradores necesitan un token</small>
</div>
```

### 3. Crear método para generar tokens

```php
// En Models/TokensModel.php
class TokensModel extends Conexion {
    
    public function generarToken(int $adminId, string $rol, int $diasValidez = 7): string {
        $token = bin2hex(random_bytes(32)); // Genera token seguro de 64 caracteres
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+{$diasValidez} days"));
        
        $query = "INSERT INTO tokens_registro (Token_Codigo, Rol_Asignado, Creado_Por, Fecha_Expiracion)
                  VALUES (?, ?, ?, ?)";
        $arr = [$token, $rol, $adminId, $fechaExpiracion];
        
        $this->db->insert($query, $arr);
        return $token;
    }
    
    public function validarToken(string $token): array|false {
        $sql = "SELECT id_Token, Rol_Asignado, Estado_Token, Fecha_Expiracion 
                FROM tokens_registro 
                WHERE Token_Codigo = '{$token}' 
                AND Estado_Token = 'Activo'
                AND Fecha_Expiracion > NOW()
                LIMIT 1";
        return $this->db->select($sql);
    }
    
    public function marcarTokenUsado(int $tokenId, int $usuarioId): bool {
        $query = "UPDATE tokens_registro 
                  SET Estado_Token = 'Usado', 
                      Usado_Por = ?, 
                      Fecha_Uso = NOW()
                  WHERE id_Token = ?";
        $result = $this->db->update($query, [$usuarioId, $tokenId]);
        
        // Eliminar el token inmediatamente después de usarlo
        $this->eliminarToken($tokenId);
        
        return $result;
    }
    
    public function eliminarToken(int $tokenId): bool {
        $query = "DELETE FROM tokens_registro WHERE id_Token = ?";
        return $this->db->delete($query, [$tokenId]);
    }
    
    public function limpiarTokensExpirados(): int {
        // Elimina tokens usados o expirados
        $query = "DELETE FROM tokens_registro 
                  WHERE Estado_Token = 'Usado' 
                     OR (Estado_Token = 'Activo' AND Fecha_Expiracion < NOW())";
        return $this->db->delete($query, []);
    }
    
    public function listarTokens(int $adminId): array {
        $sql = "SELECT t.*, u.Nombre_Usuario as Creador, uu.Nombre_Usuario as Usuario_Uso
                FROM tokens_registro t
                LEFT JOIN usuario u ON t.Creado_Por = u.id_Usuario
                LEFT JOIN usuario uu ON t.Usado_Por = uu.id_Usuario
                WHERE t.Creado_Por = {$adminId}
                ORDER BY t.Fecha_Creacion DESC";
        return $this->db->selectAll($sql);
    }
}
```

### 4. Modificar el registro en Auth.php

```php
// En Controllers/Auth.php
public function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = clean($_POST['nombre'] ?? '');
        $email = clean($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $token = clean($_POST['token'] ?? ''); // Token opcional
        
        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($password)) {
            $this->redirect('auth/login', ['type' => 'error', 'msg' => 'Todos los campos son obligatorios']);
            return;
        }
        
        // Si hay token, validarlo
        $rolAsignado = 'Cliente'; // Por defecto es cliente
        if (!empty($token)) {
            require_once 'Models/TokensModel.php';
            $tokenModel = new TokensModel();
            $tokenData = $tokenModel->validarToken($token);
            
            if (!$tokenData) {
                $this->redirect('auth/login', ['type' => 'error', 'msg' => 'Token inválido o expirado']);
                return;
            }
            
            $rolAsignado = $tokenData['Rol_Asignado'];
        }
        
        // Verificar si el email ya existe
        if ($this->model->findUserIdByEmail($email)) {
            $this->redirect('auth/login', ['type' => 'error', 'msg' => 'El email ya está registrado']);
            return;
        }
        
        // Crear usuario con el rol correspondiente
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($this->model->createUsuarioConRol($nombre, $email, $passwordHash, $rolAsignado)) {
            // Si se usó un token, marcarlo como usado
            if (!empty($token) && isset($tokenData)) {
                $nuevoUserId = $this->model->findUserIdByEmail($email)['id_Usuario'];
                $tokenModel->marcarTokenUsado($tokenData['id_Token'], $nuevoUserId);
            }
            
            $this->redirect('auth/login', ['type' => 'success', 'msg' => 'Registro exitoso. Ya puedes iniciar sesión']);
        } else {
            $this->redirect('auth/login', ['type' => 'error', 'msg' => 'Error al crear la cuenta']);
        }
    }
}
```

### 5. Crear vista para administradores generen tokens

```php
// En Views/Dashboard/generarTokens.php
<div class="card">
    <div class="card-header">
        <h3>Generar Token de Registro</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/dashboard/crearToken">
            <div class="form-group">
                <label>Rol del nuevo usuario</label>
                <select name="rol" class="form-control" required>
                    <option value="Empleado">Empleado</option>
                    <option value="Admin">Administrador</option>
                </select>
            </div>
            <div class="form-group">
                <label>Días de validez</label>
                <input type="number" name="dias" class="form-control" value="7" min="1" max="30">
            </div>
            <button type="submit" class="btn btn-primary">Generar Token</button>
        </form>
    </div>
</div>

<!-- Lista de tokens generados -->
<div class="card mt-4">
    <div class="card-header">
        <h3>Tokens Generados</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Token</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Expira</th>
                    <th>Usado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['tokens'] as $token): ?>
                <tr>
                    <td>
                        <code class="token-code"><?= substr($token['Token_Codigo'], 0, 16) ?>...</code>
                        <button class="btn btn-sm btn-link" onclick="copiarToken('<?= $token['Token_Codigo'] ?>')">
                            <i class="fa fa-copy"></i>
                        </button>
                    </td>
                    <td><span class="badge badge-info"><?= $token['Rol_Asignado'] ?></span></td>
                    <td>
                        <?php if($token['Estado_Token'] == 'Activo'): ?>
                            <span class="badge badge-success">Activo</span>
                        <?php elseif($token['Estado_Token'] == 'Usado'): ?>
                            <span class="badge badge-secondary">Usado</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Expirado</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($token['Fecha_Expiracion'])) ?></td>
                    <td><?= $token['Usuario_Uso'] ?? '-' ?></td>
                    <td>
                        <?php if($token['Estado_Token'] == 'Activo'): ?>
                            <a href="<?= BASE_URL ?>/dashboard/revocarToken/<?= $token['id_Token'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Revocar este token?')">
                                Revocar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function copiarToken(token) {
    navigator.clipboard.writeText(token);
    alert('Token copiado al portapapeles');
}
</script>
```

### 6. JavaScript para mostrar/ocultar campo de token

```javascript
// En Views/Auth/login.php
// Detectar si el usuario quiere registrarse como empleado/admin
$('#rolSelector').on('change', function() {
    const rol = $(this).val();
    if (rol === 'Empleado' || rol === 'Admin') {
        $('#tokenField').show();
        $('#registerToken').prop('required', true);
    } else {
        $('#tokenField').hide();
        $('#registerToken').prop('required', false);
    }
});
```

## Ventajas de este sistema

1. **Seguro**: Solo quien tiene el token puede registrarse como empleado/admin
2. **Auditable**: Se registra quién creó cada token y quién lo usó (antes de eliminarse)
3. **Temporal**: Los tokens expiran automáticamente
4. **Revocable**: Los admins pueden revocar tokens antes de que expiren
5. **Único**: Cada token solo se puede usar una vez
6. **Limpio**: Los tokens se eliminan automáticamente después de ser usados o cada día mediante evento MySQL
7. **Eficiente**: No sobrecarga la base de datos con registros innecesarios

## Limpieza automática de tokens

El sistema implementa **dos niveles de limpieza**:

1. **Limpieza inmediata**: Cuando un token se usa, se elimina automáticamente de la BD
2. **Limpieza programada**: Un evento MySQL elimina diariamente:
   - Tokens ya usados (por si falló la limpieza inmediata)
   - Tokens expirados
   - Tokens con más de 30 días de antigüedad

### Activar el Event Scheduler de MySQL

Para que la limpieza automática funcione, ejecuta en phpMyAdmin:

```sql
-- Verificar si está activo
SHOW VARIABLES LIKE 'event_scheduler';

-- Activarlo
SET GLOBAL event_scheduler = ON;

-- Para hacerlo permanente, agregar en my.ini o my.cnf:
-- [mysqld]
-- event_scheduler=ON
```

### Verificar que el evento esté funcionando

```sql
-- Ver eventos activos
SHOW EVENTS;

-- Ver cuándo fue la última ejecución
SELECT * FROM information_schema.events WHERE event_name = 'limpiar_tokens_antiguos';
```

## Flujo de uso

1. Admin genera un token desde el dashboard
2. Admin envía el token al nuevo empleado/admin (email, WhatsApp, etc.)
3. El nuevo usuario va a la página de registro
4. Ingresa sus datos + el token
5. El sistema valida el token y crea la cuenta con el rol correspondiente
6. El token se marca como "Usado"
