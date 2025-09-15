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
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3498db;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .form-actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e1e8ed;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 5px solid;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        .required {
            color: #e74c3c;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
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