# Sistema de Favoritos con Autenticación

## Descripción
Sistema completo de favoritos que permite a los usuarios guardar productos como favoritos. Incluye un modal de autenticación que se muestra cuando un usuario no autenticado intenta agregar un producto a favoritos.

## Características Implementadas

### 1. Modal de Autenticación (`Views/Components/auth_modal.php`)
- Modal responsivo con diseño moderno
- Opciones claras para iniciar sesión o registrarse
- Lista de beneficios de crear una cuenta
- Animación de corazón latente para engagement visual

### 2. Controlador Actualizado (`Controllers/Favoritos.php`)
- Endpoint `/favoritos/set` para agregar/eliminar favoritos
- Retorna código específico `not_authenticated` cuando el usuario no está logueado
- Endpoint `/favoritos/getUserFavorites` para obtener IDs de favoritos del usuario
- Validaciones de seguridad y autenticación

### 3. Modelo de Favoritos (`Models/FavoritosModel.php`)
- `agregarFavorito()`: Agregar producto a favoritos
- `eliminarFavoritoPorUsuarioProducto()`: Eliminar favorito
- `existeFavorito()`: Verificar si ya existe el favorito
- `getFavoritos()`: Obtener todos los favoritos con detalles
- `getFavoritosIds()`: Obtener solo los IDs para marcar en frontend

### 4. JavaScript de Favoritos (`Assets/js/functions_favoritos.js`)
- Delegación de eventos para botones de favoritos
- Detección automática de usuarios no autenticados
- Muestra modal de autenticación cuando es necesario
- Actualización visual en tiempo real del estado de favoritos
- Sistema de notificaciones toast para feedback al usuario
- Carga automática de favoritos al cargar la página

### 5. Estilos CSS (`Assets/css/auth-modal-favoritos.css`)
- Diseño moderno con gradientes
- Animaciones suaves y atractivas
- Responsivo para móviles
- Estilos para botones de favoritos en productos
- Notificaciones toast animadas

## Uso en Vistas

### En la Tienda (`Views/Tienda/tienda.php`)
```php
<!-- Botón de favorito en el producto -->
<div class="product-favorite" data-product-id="<?= $producto['idProducto'] ?>">
    <i class="fa fa-heart-o"></i>
</div>

<!-- Al final de la página, antes del footer -->
<?php require_once(__DIR__ . '/../Components/auth_modal.php'); ?>

<!-- CSS y JS necesarios -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/auth-modal-favoritos.css">
<script src="<?= media() ?>/js/functions_favoritos.js"></script>
```

### En Detalle de Producto (`Views/Productos/detalle.php`)
```php
<!-- Botón de favorito -->
<button class="btn-wishlist btn-fav" data-id="<?= $producto['idProducto'] ?>">
    <i class="fa fa-heart-o"></i> Favoritos
</button>

<!-- Al final de la página -->
<?php require_once(__DIR__ . '/../Components/auth_modal.php'); ?>
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/auth-modal-favoritos.css">
<script src="<?= media() ?>/js/functions_favoritos.js"></script>
```

## Flujo de Usuario

### Usuario No Autenticado:
1. Usuario hace clic en el corazón de un producto
2. Sistema detecta que no hay sesión activa
3. Se muestra el modal de autenticación
4. Usuario puede elegir:
   - Iniciar sesión (redirige a `/auth/login`)
   - Registrarse (redirige a `/auth/register`)
   - Cerrar el modal y continuar navegando

### Usuario Autenticado:
1. Usuario hace clic en el corazón de un producto
2. Sistema agrega/elimina el favorito automáticamente
3. El ícono cambia de corazón vacío a lleno (o viceversa)
4. Se muestra una notificación toast de éxito
5. Los favoritos se sincronizan automáticamente

## Clases CSS Importantes

### Para Botones de Favoritos:
- `.product-favorite`: Contenedor del botón de favoritos
- `.btn-fav`: Clase para botones de favoritos estándar
- `.is-fav`: Clase añadida cuando el producto es favorito

### Para el Modal:
- `#authModal`: ID del modal de autenticación
- `.auth-modal-content`: Contenedor principal del modal
- `.auth-options`: Contenedor de botones de acción

## Variables JavaScript Necesarias

Asegúrate de definir en cada vista:
```javascript
const BASE_URL_JS = '<?= BASE_URL ?>';
```

## Endpoints API

### POST /favoritos/set
Agregar o eliminar un favorito.

**Parámetros:**
- `productoId` (int): ID del producto
- `action` (string): 'add' o 'remove'

**Respuesta Éxito:**
```json
{
    "status": true,
    "msg": "Agregado a favoritos"
}
```

**Respuesta No Autenticado:**
```json
{
    "status": false,
    "msg": "Usuario no autenticado",
    "code": "not_authenticated"
}
```

### GET /favoritos/getUserFavorites
Obtener IDs de favoritos del usuario actual.

**Respuesta:**
```json
{
    "status": true,
    "favoritos": [1, 5, 12, 23]
}
```

## Estructura de Base de Datos

La tabla `favorito` debe tener:
- `idFAVORITO` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `USUARIO_idUSUARIO` (INT, FOREIGN KEY)
- `DESTINO_TURISTICO_nombre_destino` (INT, FOREIGN KEY al ID del producto)

## Personalización

### Cambiar Colores del Modal:
Edita `Assets/css/auth-modal-favoritos.css`:
```css
.auth-modal-content .modal-header {
    background: linear-gradient(135deg, #TU_COLOR_1 0%, #TU_COLOR_2 100%);
}
```

### Cambiar Mensajes:
Edita `Views/Components/auth_modal.php` para personalizar el texto del modal.

### Personalizar Notificaciones:
Edita la función `showToast()` en `Assets/js/functions_favoritos.js`.

## Testing

Para probar el sistema:
1. Navega a la tienda sin iniciar sesión
2. Haz clic en el corazón de un producto
3. Verifica que aparece el modal
4. Inicia sesión
5. Haz clic en el corazón de un producto
6. Verifica que se agrega a favoritos sin modal
7. Recarga la página y verifica que los favoritos permanecen marcados

## Troubleshooting

### El modal no aparece:
- Verifica que Bootstrap y jQuery están cargados
- Verifica que el archivo `auth_modal.php` está incluido
- Verifica que `BASE_URL_JS` está definido

### Los favoritos no se guardan:
- Verifica la sesión del usuario (`$_SESSION['usuario_id']`)
- Revisa la consola del navegador para errores
- Verifica que la tabla `favorito` existe en la base de datos

### Los íconos no cambian:
- Verifica que Font Awesome está cargado
- Verifica que `functions_favoritos.js` está incluido
- Revisa la consola para errores JavaScript

## Mejoras Futuras Sugeridas

1. Contador de favoritos en el header
2. Página dedicada de favoritos con grid de productos
3. Compartir lista de favoritos
4. Notificaciones cuando un favorito tiene descuento
5. Agregar favoritos desde múltiples vistas (búsqueda, categorías, etc.)
6. Sistema de listas de deseos múltiples (ej: "Para regalo", "Comprar después")
