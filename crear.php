<?php
session_start();
require_once 'Producto.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] != 'empleado' && $_SESSION['tipo_usuario'] != 'administrador')) {
    header("Location: index.php");
    exit();
}

$prod = new Producto();
$producto = [];

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
if ($id) {
    $producto = $prod->obtener($id);
}

// Obtener datos del formulario si hay errores
$datos_formulario = $_SESSION['datos_formulario'] ?? [];
$errores = $_SESSION['errores'] ?? [];

// Limpiar mensajes de sesión
unset($_SESSION['datos_formulario'], $_SESSION['errores']);

// Mezclar datos del producto con datos del formulario
if (!empty($datos_formulario)) {
    $producto = array_merge($producto ?: [], $datos_formulario);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Editar Producto' : 'Crear Producto' ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #2c2c2c;
            margin: 0;
            padding: 20px;
            color: #ffffff;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #1a1a1a;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(245, 166, 35, 0.2);
            padding: 30px;
            border: 2px solid rgba(245, 166, 35, 0.3);
        }

        h2 {
            text-align: center;
            color: #F5A623;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(245, 166, 35, 0.3);
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #F5A623;
            font-size: 14px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(245, 166, 35, 0.3);
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            background: #2c2c2c;
            color: #ffffff;
            font-family: 'Times New Roman', Times, serif;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #F5A623;
            box-shadow: 0 0 15px rgba(245, 166, 35, 0.3);
            background: #333333;
        }

        input::placeholder,
        textarea::placeholder {
            color: #cccccc;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        select {
            cursor: pointer;
        }

        select option {
            background: #2c2c2c;
            color: #ffffff;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 15px;
            padding: 10px;
            background: rgba(245, 166, 35, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(245, 166, 35, 0.2);
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 12px;
            transform: scale(1.2);
            accent-color: #F5A623;
        }

        .checkbox-group label {
            margin-bottom: 0;
            color: #ffffff;
            cursor: pointer;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 8px;
            transition: all 0.3s ease;
            font-family: 'Times New Roman', Times, serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F5A623 0%, #E09500 100%);
            color: #1a1a1a;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            color: #F5A623;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 166, 35, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: 2px solid transparent;
        }

        .btn-secondary:hover {
            background: transparent;
            color: #6c757d;
            border-color: #6c757d;
            transform: translateY(-2px);
        }

        .form-actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid rgba(245, 166, 35, 0.3);
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            border-left: 5px solid;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b6b;
            border-left-color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .alert-error strong {
            color: #ff8a8a;
        }

        .alert-error ul {
            margin-top: 10px;
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-error li {
            margin-bottom: 5px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #51cf66;
            border-left-color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .required {
            color: #ff6b6b;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Estilos para números */
        input[type="number"] {
            text-align: right;
        }

        /* Estilos especiales para campos de precio */
        input[name*="Precio"],
        input[name="Margen_Ganancia"] {
            background: rgba(245, 166, 35, 0.1);
            font-weight: bold;
        }

        input[name*="Precio"]:focus,
        input[name="Margen_Ganancia"]:focus {
            background: rgba(245, 166, 35, 0.15);
        }

        /* Efectos adicionales */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1.5" fill="%23F5A623" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="%23F5A623" opacity="0.1"/><circle cx="40" cy="80" r="1.2" fill="%23F5A623" opacity="0.1"/></svg>');
            pointer-events: none;
            border-radius: 15px;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #2c2c2c;
        }

        ::-webkit-scrollbar-thumb {
            background: #F5A623;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ffffff;
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .container {
                margin: 10px;
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2><?= $id ? 'Editar Producto' : 'Crear Nuevo Producto' ?></h2>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-error">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul style="margin-top: 10px;">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="guardarProducto.php" method="post">
            <input type="hidden" name="idProducto" value="<?= htmlspecialchars($producto['idProducto'] ?? '') ?>">

            <div class="form-group">
                <label for="nombre">Nombre del Producto <span class="required">*</span></label>
                <input type="text" id="nombre" name="Nombre_Producto"
                    value="<?= htmlspecialchars($producto['Nombre_Producto'] ?? '') ?>"
                    required maxlength="100">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="Descripcion_Producto"
                    maxlength="450"><?= htmlspecialchars($producto['Descripcion_Producto'] ?? '') ?></textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="sku">SKU <span class="required">*</span></label>
                    <input type="text" id="sku" name="SKU"
                        value="<?= htmlspecialchars($producto['SKU'] ?? '') ?>"
                        required maxlength="50">
                </div>

                <div class="form-group">
                    <label for="marca">Marca <span class="required">*</span></label>
                    <input type="text" id="marca" name="Marca"
                        value="<?= htmlspecialchars($producto['Marca'] ?? '') ?>"
                        required maxlength="45">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="precio_costo">Precio Costo <span class="required">*</span></label>
                    <input type="number" id="precio_costo" name="Precio_Costo"
                        step="0.01" min="0" max="99999999.99"
                        value="<?= htmlspecialchars($producto['Precio_Costo'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="precio_venta">Precio Venta <span class="required">*</span></label>
                    <input type="number" id="precio_venta" name="Precio_Venta"
                        step="0.01" min="0" max="99999999.99"
                        value="<?= htmlspecialchars($producto['Precio_Venta'] ?? '') ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="precio_oferta">Precio Oferta</label>
                    <input type="number" id="precio_oferta" name="Precio_Oferta"
                        step="0.01" min="0" max="99999999.99"
                        value="<?= htmlspecialchars($producto['Precio_Oferta'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="margen">Margen Ganancia (%) <span class="required">*</span></label>
                    <input type="number" id="margen" name="Margen_Ganancia"
                        step="0.01" min="0" max="999.99"
                        value="<?= htmlspecialchars($producto['Margen_Ganancia'] ?? '') ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="stock">Stock Actual <span class="required">*</span></label>
                    <input type="number" id="stock" name="Stock_Actual"
                        min="0" step="1"
                        value="<?= htmlspecialchars($producto['Stock_Actual'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="estado">Estado <span class="required">*</span></label>
                    <select id="estado" name="Estado_Producto" required>
                        <?php
                        $estados = ['Activo', 'Inactivo', 'Descontinuado'];
                        foreach ($estados as $estado) {
                            $selected = ($producto['Estado_Producto'] ?? '') === $estado ? 'selected' : '';
                            echo "<option value=\"$estado\" $selected>$estado</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="oferta" name="En_Oferta" value="1"
                        <?= !empty($producto['En_Oferta']) ? 'checked' : '' ?>>
                    <label for="oferta">Producto en Oferta</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="destacado" name="Es_Destacado" value="1"
                        <?= !empty($producto['Es_Destacado']) ? 'checked' : '' ?>>
                    <label for="destacado">Producto Destacado</label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $id ? 'Actualizar Producto' : 'Crear Producto' ?>
                </button>
                <a href="listarProducto.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // Calcular margen automáticamente
        document.getElementById('precio_costo').addEventListener('input', calcularMargen);
        document.getElementById('precio_venta').addEventListener('input', calcularMargen);

        function calcularMargen() {
            const costo = parseFloat(document.getElementById('precio_costo').value) || 0;
            const venta = parseFloat(document.getElementById('precio_venta').value) || 0;

            if (costo > 0 && venta > 0) {
                const margen = ((venta - costo) / costo * 100).toFixed(2);
                document.getElementById('margen').value = margen;
            }
        }

        // Validar que precio de venta sea mayor al costo
        document.querySelector('form').addEventListener('submit', function(e) {
            const costo = parseFloat(document.getElementById('precio_costo').value) || 0;
            const venta = parseFloat(document.getElementById('precio_venta').value) || 0;

            if (venta <= costo) {
                alert('El precio de venta debe ser mayor al precio de costo.');
                e.preventDefault();
                return false;
            }
        });
    </script>

</body>

</html>