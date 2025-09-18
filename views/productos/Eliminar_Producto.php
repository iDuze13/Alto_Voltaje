<?php
session_start();
require_once 'Producto.php';

// Verificar que el usuario esté logueado como empleado o admin
if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] != 'empleado' && $_SESSION['tipo_usuario'] != 'administrador')) {
    header("Location: index.php");
    exit();
}

// Verificar que llegue un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de producto inválido.';
    $_SESSION['tipo_mensaje'] = 'error';
    header("Location: listarProducto.php");
    exit();
}

$id = intval($_GET['id']);
$producto = new Producto();

// Verificar que el producto existe
$producto_data = $producto->obtener($id);
if (!$producto_data) {
    $_SESSION['mensaje'] = 'Producto no encontrado.';
    $_SESSION['tipo_mensaje'] = 'error';
    header("Location: listarProducto.php");
    exit();
}

try {
    if ($producto->eliminar($id)) {
        $_SESSION['mensaje'] = 'Producto "' . $producto_data['Nombre_Producto'] . '" eliminado correctamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar el producto.';
        $_SESSION['tipo_mensaje'] = 'error';
    }
} catch (Exception $e) {
    $_SESSION['mensaje'] = 'Error del sistema: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
}

header("Location: listarProducto.php");
exit();
?>