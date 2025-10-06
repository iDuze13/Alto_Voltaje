<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
// Use employee-specific header to avoid CSS conflicts with tienda template
require_once(__DIR__ . '/../Template/headerEmpleado.php');
$empleado = isset($data['empleado']) ? $data['empleado'] : null;
$nombre = $empleado ? ($empleado['Nombre_Usuario'] . ' ' . ($empleado['Apellido_Usuario'] ?? '')) : 'Empleado';
$idEmpleado = $empleado['id_Empleado'] ?? ($_SESSION['empleado']['id'] ?? '');
$cuil = $empleado['CUIL'] ?? ($_SESSION['empleado']['cuil'] ?? '');
?>

<link rel="stylesheet" href="<?= media() ?>/css/empleado.css">

<div class="emp-side-menu">
  <div class="brand"><h1>Alto Voltaje</h1></div>
  <ul>
    <li onclick="loadSection('ventas')"><ion-icon name="logo-usd"></ion-icon> Ventas</li>
    <li onclick="loadSection('productos')"><ion-icon name="pricetags"></ion-icon> Inventario</li>
    <li onclick="loadSection('recibos')"><ion-icon name="paper"></ion-icon> Reportes</li>
    <li onclick="loadSection('quejas')"><ion-icon name="filing"></ion-icon> Atención al Cliente</li>
  </ul>
  <div class="emp-logout-btn" onclick="logout()"><ion-icon name="log-out"></ion-icon> Cerrar Sesión</div>
</div>

<div class="emp-container">
  <div class="emp-header">
    <div class="nav">
      <div class="emp-search">
        <input type="text" placeholder="Buscar..." id="searchInput" />
        <button type="button" onclick="performSearch()"><ion-icon name="search"></ion-icon></button>
      </div>
      <div class="emp-user">
        <a class="emp-back-link" href="<?= BASE_URL ?>/home/home">← Volver a la tienda</a>
        <div class="emp-user-info" style="margin-left:12px;"><ion-icon name="person"></ion-icon><span><?= htmlspecialchars($nombre) ?></span><span class="emp-badge">Empleado</span></div>
      </div>
    </div>
  </div>

  <div class="emp-content" id="mainContent">
    <div class="emp-welcome">
      <h2>Bienvenido <?= htmlspecialchars($nombre) ?></h2>
      <p>Selecciona una opción del menú lateral para comenzar a trabajar. Aquí podrás gestionar ventas, productos, reportes y quejas.</p>
      <div class="emp-box">
        <p><strong>ID de Empleado:</strong> <?= htmlspecialchars($idEmpleado) ?></p>
        <p><strong>CUIL:</strong> <?= htmlspecialchars($cuil) ?></p>
        <?php if ($empleado): ?>
          <p><strong>Usuario ID:</strong> <?= htmlspecialchars($empleado['id_Usuario']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="emp-grid">
      <div class="emp-card emp-click" onclick="irAVentas()"><h3>Ventas</h3><p>Gestiona ventas del día.</p></div>
      <div class="emp-card emp-click" onclick="irAProductos()"><h3>Inventario</h3><p>Control de stock.</p></div>
      <div class="emp-card"><h3>Reportes</h3><p>Genera reportes y recibos.</p></div>
      <div class="emp-card"><h3>Atención al Cliente</h3><p>Gestiona quejas y sugerencias.</p></div>
    </div>
  </div>
</div>

<script>
function loadSection(section){const c=document.getElementById('mainContent');
 if(section==='ventas'){c.innerHTML=`<div class="emp-welcome"><h2>Gestión de Ventas</h2><p>Ver y gestionar ventas.</p></div>`}
 else if(section==='productos'){c.innerHTML=`<div class="emp-welcome"><h2>Gestión de Productos</h2><p>Administra inventario.</p></div>`}
 else if(section==='recibos'){c.innerHTML=`<div class="emp-welcome"><h2>Gestión de Recibos</h2><p>Consulta y administra recibos.</p></div>`}
 else if(section==='quejas'){c.innerHTML=`<div class="emp-welcome"><h2>Gestión de Quejas</h2><p>Atiende y resuelve quejas.</p></div>`}
}
function performSearch(){const v=document.getElementById('searchInput').value;if(v.trim()!==''){alert('Buscando: '+v)}}
function logout(){window.location.href='<?= BASE_URL ?>/auth/logout'}
function irAProductos(){window.location.href='<?= BASE_URL ?>/productos/listar'}
function irAVentas(){window.location.href='<?= BASE_URL ?>/ventas/ventas'}
document.getElementById('searchInput').addEventListener('keypress',e=>{if(e.key==='Enter'){performSearch()}})
</script>

<?php require_once(__DIR__ . '/../Template/footerEmpleado.php'); ?>
