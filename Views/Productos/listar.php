<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
require_once(__DIR__ . '/../Template/headerEmpleado.php');
/** @var array $data */
$productos = $data['productos'] ?? [];
$flash = $data['flash'] ?? null;
// Derive current user name if available
$nombreUsuario = current_user_name() ?: 'Usuario';
?>

<div class="container" style="max-width:1400px;margin:20px auto;background:#1a1a1a;border-radius:15px;box-shadow:0 10px 30px rgba(245,166,35,.2);padding:30px;border:2px solid rgba(245,166,35,.3);position:relative;">
  <div class="header-info" style="background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;padding:15px 20px;border-radius:10px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;font-weight:bold;">
    <div class="user-info" style="font-size:14px;color:#1a1a1a;">
      <strong>Bienvenido, <?= htmlspecialchars($nombreUsuario) ?>!</strong><br>
      <small>Área: Inventario</small>
    </div>
    <a href="<?= BASE_URL ?>/empleados/dashboard" class="home-link" style="color:#1a1a1a;text-decoration:none;padding:8px 16px;background-color:rgba(26,26,26,0.2);border-radius:5px;font-weight:600;">Volver al Inicio</a>
  </div>

  <h1 style="color:#F5A623;margin-bottom:20px;text-align:center;font-size:2.2em;">Gestión de Productos</h1>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="padding:15px;margin-bottom:20px;border-radius:10px;border-left:5px solid;<?= $flash['type']==='success' ? 'background:rgba(40,167,69,.1);color:#51cf66;border-left-color:#28a745;border:1px solid rgba(40,167,69,.3);' : 'background:rgba(220,53,69,.1);color:#ff6b6b;border-left-color:#dc3545;border:1px solid rgba(220,53,69,.3);' ?>">
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  <?php endif; ?>

  <?php
    $total = count($productos);
    $activos = array_filter($productos, fn($p)=> ($p['Estado_Producto'] ?? '') === 'Activo');
    $oferta = array_filter($productos, fn($p)=> !empty($p['En_Oferta']));
    $dest = array_filter($productos, fn($p)=> !empty($p['Es_Destacado']));
  ?>

  <div class="stats-cards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:20px;">
    <div class="stat-card" style="background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;padding:20px;border-radius:12px;text-align:center;border:2px solid rgba(245,166,35,.5);"><span class="stat-number" style="font-size:2em;font-weight:bold;display:block;"><?= $total ?></span><span class="stat-label" style="opacity:.8;">Total</span></div>
    <div class="stat-card" style="background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;padding:20px;border-radius:12px;text-align:center;border:2px solid rgba(245,166,35,.5);"><span class="stat-number" style="font-size:2em;font-weight:bold;display:block;"><?= count($activos) ?></span><span class="stat-label" style="opacity:.8;">Activos</span></div>
    <div class="stat-card" style="background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;padding:20px;border-radius:12px;text-align:center;border:2px solid rgba(245,166,35,.5);"><span class="stat-number" style="font-size:2em;font-weight:bold;display:block;"><?= count($oferta) ?></span><span class="stat-label" style="opacity:.8;">En Oferta</span></div>
    <div class="stat-card" style="background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;padding:20px;border-radius:12px;text-align:center;border:2px solid rgba(245,166,35,.5);"><span class="stat-number" style="font-size:2em;font-weight:bold;display:block;"><?= count($dest) ?></span><span class="stat-label" style="opacity:.8;">Destacados</span></div>
  </div>

  <div class="header-actions" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;gap:10px;flex-wrap:wrap;">
    <a href="<?= BASE_URL ?>/productos/crear" class="btn btn-primary" style="padding:12px 24px;border-radius:8px;background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;text-decoration:none;font-weight:700;">➕ Nuevo Producto</a>
    <div class="search-filter-container" style="display:flex;gap:10px;align-items:center;">
      <input type="text" id="searchInput" class="search-box" placeholder="Buscar..." style="padding:10px 14px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
      <select id="estadoFilter" class="filter-select" style="padding:10px 14px;border:2px solid rgba(245,166,35,.3);border-radius:8px;background:#2c2c2c;color:#fff;">
        <option value="">Todos</option>
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
        <option value="Descontinuado">Descontinuado</option>
      </select>
    </div>
  </div>

  <?php if (empty($productos)): ?>
    <div class="empty-state" style="text-align:center;padding:60px 20px;color:#6c757d;">
      <div style="font-size:48px;">📦</div>
      <h3>No hay productos registrados</h3>
      <p>Haz clic en "Nuevo Producto" para agregar el primero</p>
      <a href="<?= BASE_URL ?>/productos/crear" class="btn btn-primary" style="margin-top: 20px;padding:12px 24px;border-radius:8px;background:linear-gradient(135deg,#F5A623 0%,#E09500 100%);color:#1a1a1a;text-decoration:none;font-weight:700;">Crear Primer Producto</a>
    </div>
  <?php else: ?>
    <div class="table-container" style="overflow-x:auto;border-radius:12px;border:2px solid rgba(245,166,35,.3);background:#2c2c2c;">
      <table id="productosTable" style="width:100%;border-collapse:collapse;background:#1a1a1a;">
        <thead>
          <tr>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">ID</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Producto</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">SKU</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Marca</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Categoría</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Precio Venta</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Stock</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Estado</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Especiales</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Proveedor</th>
            <th style="background:#222;color:#F5A623;padding:12px;text-align:left;">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
          <?php 
            $stock = (int)$p['Stock_Actual'];
            $stockClass = $stock < 10 ? 'color:#ff6b6b' : ($stock < 50 ? 'color:#ffc107' : 'color:#51cf66');
          ?>
          <tr style="border-bottom:1px solid rgba(245,166,35,.1);">
            <td style="padding:12px;color:#fff;"><strong><?= htmlspecialchars($p['idProducto']) ?></strong></td>
            <td style="padding:12px;color:#fff;">
              <strong><?= htmlspecialchars($p['Nombre_Producto']) ?></strong><br>
            <?php $desc = $p['Descripcion_Producto'] ?? ''; $snip = (strlen($desc) > 50) ? substr($desc,0,50).'...' : $desc; ?>
            <small style="color:#6c757d;"><?= htmlspecialchars($snip) ?></small>
            </td>
            <td style="padding:12px;color:#fff;"><code><?= htmlspecialchars($p['SKU']) ?></code></td>
            <td style="padding:12px;color:#fff;"><?= htmlspecialchars($p['Marca']) ?></td>
            <td style="padding:12px;color:#fff;">
              <strong><?= htmlspecialchars($p['Nombre_Categoria'] ?? 'N/A') ?></strong><br>
              <small><?= htmlspecialchars($p['Nombre_SubCategoria'] ?? 'N/A') ?></small>
            </td>
            <td style="padding:12px;color:#51cf66;font-weight:600;">$<?= number_format((float)$p['Precio_Venta'], 2) ?></td>
            <td style="padding:12px;<?= $stockClass ?>;font-weight:500;"><?= $stock ?></td>
            <td style="padding:12px;color:#fff;">
              <span style="padding:6px 12px;border-radius:20px;font-size:12px;border:1px solid #888;">
                <?= htmlspecialchars($p['Estado_Producto']) ?>
              </span>
            </td>
            <td style="padding:12px;color:#fff;">
              <?php if (!empty($p['En_Oferta'])): ?><span style="display:inline-block;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;background:linear-gradient(135deg,#ff6b6b 0%,#ee5a52 100%);color:#fff;">OFERTA</span><?php endif; ?>
              <?php if (!empty($p['Es_Destacado'])): ?><span style="display:inline-block;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;background:linear-gradient(135deg,#4ecdc4 0%,#44a08d 100%);color:#fff;">DESTACADO</span><?php endif; ?>
            </td>
            <td style="padding:12px;color:#fff;"><?= htmlspecialchars($p['Nombre_Proveedor'] ?? 'N/A') ?></td>
            <td style="padding:12px;white-space:nowrap;">
              <a href="<?= BASE_URL ?>/productos/crear/<?= (int)$p['idProducto'] ?>" class="btn btn-success" style="background:linear-gradient(135deg,#51cf66 0%,#28a745 100%);color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-size:12px;">✏️ Editar</a>
              <a href="<?= BASE_URL ?>/productos/eliminar/<?= (int)$p['idProducto'] ?>" class="btn btn-danger" style="background:linear-gradient(135deg,#dc3545 0%,#c82333 100%);color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-size:12px;" onclick="return confirm('¿Eliminar producto: <?= htmlspecialchars(addslashes($p['Nombre_Producto'])) ?>?');">🗑️ Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
function filterTable(){
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const estadoFilter = document.getElementById('estadoFilter').value;
  const rows = document.querySelectorAll('#productosTable tbody tr');
  rows.forEach(row=>{
    const text = row.textContent.toLowerCase();
    const estado = row.cells[7]?.textContent.trim();
    const matches = text.includes(searchTerm) && (!estadoFilter || estado === estadoFilter);
    row.style.display = matches ? '' : 'none';
  });
}
document.getElementById('searchInput').addEventListener('keyup', filterTable);
document.getElementById('estadoFilter').addEventListener('change', filterTable);
</script>

<?php require_once(__DIR__ . '/../Template/footerEmpleado.php'); ?>
