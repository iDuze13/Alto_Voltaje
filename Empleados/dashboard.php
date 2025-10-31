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

<style>
.loading, .error {
  text-align: center;
  padding: 40px;
  font-size: 18px;
}

.loading {
  color: #007bff;
}

.error {
  color: #dc3545;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 5px;
  margin: 20px 0;
}
</style>

<div class="emp-side-menu">
  <div class="brand"><h1>Alto Voltaje</h1></div>
  <ul>
    <li onclick="window.location.href='<?= BASE_URL ?>/ventas/ventas'">
      <ion-icon name="logo-usd"></ion-icon> Ventas
    </li>
    <li onclick="loadSection('productos')">
      <ion-icon name="pricetags"></ion-icon> Inventario
    </li>
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
      <div class="welcome-content">
        <h1>Bienvenido <?= htmlspecialchars($nombre) ?></h1>
        <p>Selecciona una opción del menú lateral para comenzar a trabajar</p>
      </div>
      <div class="welcome-stats">
        <div class="quick-stat">
          <div class="quick-stat-value"><?= htmlspecialchars($idEmpleado) ?></div>
          <div class="quick-stat-label">ID Empleado</div>
        </div>
        <div class="quick-stat">
          <div class="quick-stat-value"><?= htmlspecialchars($cuil) ?></div>
          <div class="quick-stat-label">CUIL</div>
        </div>
        <?php if ($empleado): ?>
        <div class="quick-stat">
          <div class="quick-stat-value"><?= htmlspecialchars($empleado['id_Usuario']) ?></div>
          <div class="quick-stat-label">Usuario ID</div>
        </div>
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
function loadSection(section){
  const mainContent = document.getElementById('mainContent');
  
  if(section==='ventas'){
    mainContent.innerHTML=`<div class="emp-welcome"><h2>Gestión de Ventas</h2><p>Ver y gestionar ventas.</p></div>`
  }
  else if(section==='productos'){
    // Cargar el contenido de productos vía AJAX
    mainContent.innerHTML = '<div class="loading">Cargando gestión de productos...</div>';
    
    $.ajax({
      url: '<?= BASE_URL ?>/empleados/loadProductosContent',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.status) {
          mainContent.innerHTML = response.html;
          
          // Agregar el modal al body si no existe
          if (!document.getElementById('modalFormProductos') && response.modal) {
            document.body.insertAdjacentHTML('beforeend', response.modal);
          }
        } else {
          mainContent.innerHTML = '<div class="error">Error al cargar la gestión de productos.</div>';
        }
      },
      error: function() {
        mainContent.innerHTML = '<div class="error">Error de conexión al cargar productos.</div>';
      }
    });
  }
  else if(section==='recibos'){
    mainContent.innerHTML=`<div class="emp-welcome"><h2>Gestión de Recibos</h2><p>Consulta y administra recibos.</p></div>`
  }
  else if(section==='quejas'){
    mainContent.innerHTML=`<div class="emp-welcome"><h2>Gestión de Quejas</h2><p>Atiende y resuelve quejas.</p></div>`
  }
}

function performSearch(){const v=document.getElementById('searchInput').value;if(v.trim()!==''){alert('Buscando: '+v)}}
function logout(){window.location.href='<?= BASE_URL ?>/auth/logout'}
function irAProductos(){loadSection('productos');}
function irAVentas(){window.location.href='<?= BASE_URL ?>/ventas/ventas'}
document.getElementById('searchInput').addEventListener('keypress',e=>{if(e.key==='Enter'){performSearch()}})

// Asegurar que jQuery esté disponible
if (typeof $ === 'undefined') {
  var script = document.createElement('script');
  script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
  document.head.appendChild(script);
}
</script>

<?php require_once(__DIR__ . '/../Template/footerEmpleado.php'); ?>
