<?php 
require_once(__DIR__ . '/../../Helpers/DashboardHelpers.php');
headerAdmin($data); 
?>
<main class="app-content admin-dash">
  <!-- Welcome Section -->
  <div class="welcome-section">
    <div class="welcome-content">
      <h1>¡Bienvenido Empleado <?= htmlspecialchars($data['empleado']['Nombre_Usuario']); ?>!</h1>
      <p>Panel de control de ventas y operaciones</p>
    </div>
    <div class="welcome-stats">
      <div class="quick-stat">
        <span class="quick-stat-label">Hoy</span>
        <span class="quick-stat-value"><?= date('d/m/Y'); ?></span>
      </div>
      <div class="quick-stat">
        <span class="quick-stat-label">Hora</span>
        <span class="quick-stat-value" id="currentTime"><?= date('H:i'); ?></span>
      </div>
    </div>
  </div>
  
  <div class="dashboard-container">
    <!-- Main Content Area -->
    <div class="main-content">
      <div class="overview-section">
        <h2>Acciones Rápidas</h2>
        <div class="quick-actions-grid">
          <a href="<?= base_url(); ?>/empleados/productos" class="action-card">
            <i class="fa-solid fa-box"></i>
            <h3>Gestionar Productos</h3>
            <p>Ver y administrar inventario</p>
          </a>
          <a href="<?= base_url(); ?>/pedidos/listar" class="action-card">
            <i class="fa-solid fa-shopping-cart"></i>
            <h3>Ver Pedidos</h3>
            <p>Consultar pedidos de clientes</p>
          </a>
          <a href="<?= base_url(); ?>/ventas/nueva" class="action-card">
            <i class="fa-solid fa-plus-circle"></i>
            <h3>Nueva Venta</h3>
            <p>Registrar una nueva venta</p>
          </a>
          <a href="<?= base_url(); ?>/perfil" class="action-card">
            <i class="fa-solid fa-user"></i>
            <h3>Mi Perfil</h3>
            <p>Ver información personal</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<style>
.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.action-card {
  background: white;
  border-radius: 8px;
  padding: 30px;
  text-align: center;
  text-decoration: none;
  color: inherit;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-card i {
  font-size: 48px;
  color: #3b82f6;
  margin-bottom: 15px;
}

.action-card h3 {
  margin: 10px 0;
  color: #1e293b;
}

.action-card p {
  color: #64748b;
  font-size: 14px;
}
</style>

<?php footerAdmin($data); ?>
