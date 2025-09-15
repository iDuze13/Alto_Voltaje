<?php
session_start();
require_once 'Producto.php';

// Verificar que el usuario esté logueado como empleado o admin
if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] != 'empleado' && $_SESSION['tipo_usuario'] != 'administrador')) {
    header("Location: index.php");
    exit();
}

// Verificar que lleguen datos por POST
if (!$_POST) {
    header("Location: listarProducto.php");
    exit();
}

$producto = new Producto();
$mensaje = '';
$tipo_mensaje = '';

// Validar datos requeridos
$campos_requeridos = [
    'Nombre_Producto',
    'SKU',
    'Marca',
    'Precio_Costo',
    'Precio_Venta',
    'Margen_Ganancia',
    'Stock_Actual',
    'Estado_Producto'
];

$errores = [];

foreach ($campos_requeridos as $campo) {
    if (empty($_POST[$campo])) {
        $errores[] = "El campo " . str_replace('_', ' ', $campo) . " es obligatorio.";
    }
}

// Validar que los precios sean números válidos
if (!empty($_POST['Precio_Costo']) && !is_numeric($_POST['Precio_Costo'])) {
    $errores[] = "El precio de costo debe ser un número válido.";
}

if (!empty($_POST['Precio_Venta']) && !is_numeric($_POST['Precio_Venta'])) {
    $errores[] = "El precio de venta debe ser un número válido.";
}

if (!empty($_POST['Precio_Oferta']) && !is_numeric($_POST['Precio_Oferta'])) {
    $errores[] = "El precio de oferta debe ser un número válido.";
}

// Validar que el stock sea un número entero
if (!empty($_POST['Stock_Actual']) && (!is_numeric($_POST['Stock_Actual']) || intval($_POST['Stock_Actual']) != $_POST['Stock_Actual'])) {
    $errores[] = "El stock debe ser un número entero.";
}

// Validar SKU único
$id_producto = !empty($_POST['idProducto']) ? intval($_POST['idProducto']) : null;
if (!empty($_POST['SKU'])) {
    if ($producto->existeSKU($_POST['SKU'], $id_producto)) {
        $errores[] = "El SKU ya existe. Debe ser único.";
    }
}

// Si hay errores, volver al formulario
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_formulario'] = $_POST;
    
    if ($id_producto) {
        header("Location: crear.php?id=" . $id_producto);
    } else {
        header("Location: crear.php");
    }
    exit();
}

// Preparar datos para guardar
$datos = [
    'Nombre_Producto' => trim($_POST['Nombre_Producto']),
    'Descripcion_Producto' => trim($_POST['Descripcion_Producto'] ?? ''),
    'SKU' => trim($_POST['SKU']),
    'Marca' => trim($_POST['Marca']),
    'Precio_Costo' => floatval($_POST['Precio_Costo']),
    'Precio_Venta' => floatval($_POST['Precio_Venta']),
    'Precio_Oferta' => !empty($_POST['Precio_Oferta']) ? floatval($_POST['Precio_Oferta']) : null,
    'Margen_Ganancia' => floatval($_POST['Margen_Ganancia']),
    'Stock_Actual' => intval($_POST['Stock_Actual']),
    'Estado_Producto' => $_POST['Estado_Producto'],
    'En_Oferta' => isset($_POST['En_Oferta']) ? 1 : 0,
    'Es_Destacado' => isset($_POST['Es_Destacado']) ? 1 : 0,
    'SubRubro_idSubRubro' => 1, // Por defecto
    'Inventario_id_Inventario' => 1, // Por defecto
    'Proveedor_id_Proveedor' => 1 // Por defecto
];

try {
    if ($id_producto) {
        // Actualizar producto existente
        if ($producto->actualizar($id_producto, $datos)) {
            $_SESSION['mensaje'] = 'Producto actualizado correctamente.';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar el producto.';
            $_SESSION['tipo_mensaje'] = 'error';
        }
    } else {
        // Crear nuevo producto
        if ($producto->crear($datos)) {
            $_SESSION['mensaje'] = 'Producto creado correctamente.';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al crear el producto.';
            $_SESSION['tipo_mensaje'] = 'error';
        }
    }
} catch (Exception $e) {
    $_SESSION['mensaje'] = 'Error del sistema: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
}

// Redirigir al listado
header("Location: listarProducto.php");
exit();
?>