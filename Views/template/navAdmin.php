    <?php
// Obtener permisos desde la sesión
$permisos = isset($_SESSION['permisos_modulos']) ? $_SESSION['permisos_modulos'] : [];
$nombreUsuario = '';
$rolUsuario = '';

if (!empty($_SESSION['admin']) && is_array($_SESSION['admin'])) {
    $nombreUsuario = $_SESSION['admin']['nombre'] ?? '';
    $rolUsuario = $_SESSION['admin']['rol'] ?? '';
} elseif (!empty($_SESSION['empleado']) && is_array($_SESSION['empleado'])) {
    $nombreUsuario = $_SESSION['empleado']['nombre'] ?? '';
    $rolUsuario = $_SESSION['empleado']['rol'] ?? '';
} elseif (!empty($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
    $nombreUsuario = $_SESSION['usuario']['nombre'] ?? '';
    $rolUsuario = $_SESSION['usuario']['rol'] ?? '';
}

// Función helper para verificar si tiene permisos de lectura
function tienePermiso($permisos, $moduloid) {
    return isset($permisos[$moduloid]) && $permisos[$moduloid]['r'] == 1;
}
?>
<!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media() ?>/images/avatar_default.png" alt="User Image">
        <div style="max-width: 200px; overflow: hidden;">
          <p class="app-sidebar__user-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($nombreUsuario); ?>"><?= htmlspecialchars($nombreUsuario); ?></p>
          <p class="app-sidebar__user-designation" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($rolUsuario); ?>"><?= htmlspecialchars($rolUsuario); ?></p>
        </div>
      </div>
      <ul class="app-menu">
        <?php if (tienePermiso($permisos, 1) || !empty($_SESSION['admin'])): ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/dashboard">
            <i class="app-menu__icon fa fa-pie-chart" aria-hidden="true"></i>
            <span class="app-menu__label">Dashboard</span>
          </a>
        </li>
        <?php endif; ?>
        
        <?php 
        // Verificar si tiene permiso para ver el módulo de Usuarios (módulo 2)
        $tieneUsuarios = tienePermiso($permisos, 2) || !empty($_SESSION['admin']);
        if ($tieneUsuarios): 
        ?>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-users" aria-hidden="true"></i><span class="app-menu__label">Usuarios</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="<?= base_url(); ?>/usuarios"><i class="icon fa fa-circle-o"></i> Usuarios</a></li>
            <?php if (!empty($_SESSION['admin'])): ?>
            <li><a class="treeview-item" href="<?= base_url(); ?>/roles"><i class="icon fa fa-circle-o"></i> Roles</a></li>
            <li><a class="treeview-item" href="<?= base_url(); ?>/proveedores"><i class="icon fa fa-circle-o"></i>Proveedores</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <?php if (tienePermiso($permisos, 3) || !empty($_SESSION['admin'])): ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/clientes">
            <i class="app-menu__icon fa fa-address-book" aria-hidden="true"></i>
            <span class="app-menu__label">Clientes</span>
          </a>
        </li>
        <?php endif; ?>

        <?php if (tienePermiso($permisos, 4) || !empty($_SESSION['admin'])): ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/productos">
            <i class="app-menu__icon fa fa-shopping-bag" aria-hidden="true"></i>
            <span class="app-menu__label">Productos</span>
          </a>
        </li>
        <?php endif; ?>

        <?php if (tienePermiso($permisos, 6) || !empty($_SESSION['admin'])): ?>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-tags" aria-hidden="true"></i><span class="app-menu__label">Categorías</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="<?= base_url(); ?>/categorias"><i class="icon fa fa-circle-o"></i> Categorías</a></li>
            <li><a class="treeview-item" href="<?= base_url(); ?>/subcategorias"><i class="icon fa fa-circle-o"></i>Subcategorías</a></li>
          </ul>
        </li>
        <?php endif; ?>
          
        <?php if (tienePermiso($permisos, 5) || !empty($_SESSION['admin'])): ?>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/pedidos">
            <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
            <span class="app-menu__label">Pedidos</span>
          </a>
        </li>
        <?php endif; ?>

        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/auth/logout">
            <i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i>
            <span class="app-menu__label">Cerrar sesión</span>
          </a>
        </li>
        
      </ul>
    </aside>