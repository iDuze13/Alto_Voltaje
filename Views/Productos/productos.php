<?php headerAdmin($data); 
      getModal('modalProductos',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/productos-modern.css">

<main class="app-content">
  <div class="productos-header">
    <div class="header-left">
      <h1><i class="fa-solid fa-box"></i> Productos</h1>
    </div>
    <div class="header-right">
      <button class="btn btn-export" onclick="exportProducts()">
        <i class="fa-solid fa-download"></i> Exportar
      </button>
      <button class="btn btn-primary" onclick="openModal()">
        <i class="fa-solid fa-plus"></i> Añadir productos
      </button>
    </div>
  </div>

  <div class="productos-controls">
    <div class="search-container">
      <div class="search-box">
        <i class="fa-solid fa-search"></i>
        <input type="text" id="searchProducts" placeholder="Buscar">
      </div>
    </div>
    <div class="filter-container">
      <button class="btn btn-filter" onclick="toggleFilters()">
        <i class="fa-solid fa-filter"></i> Filtro
      </button>
    </div>
  </div>

  <div class="productos-table-container">
    <div class="table-wrapper">
      <table class="productos-table" id="tableProductos">
        <thead>
          <tr>
            <th>
              <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            </th>
            <th>
              <div class="th-content">
                <span>PRODUCTO</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>PRECIO</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>CANTIDAD</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>VENTAS</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data will be populated by DataTables -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Filters Panel (Hidden by default) -->
  <div class="filters-panel" id="filtersPanel" style="display: none;">
    <div class="filter-group">
      <label>Categoría</label>
      <select id="filterCategory" class="filter-select">
        <option value="">Todas las Categorías</option>
      </select>
    </div>
    <div class="filter-group">
      <label>Estado</label>
      <select id="filterStatus" class="filter-select">
        <option value="">Todos los Estados</option>
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
      </select>
    </div>
    <div class="filter-group">
      <label>Rango de Precio</label>
      <div class="price-range">
        <input type="number" id="minPrice" placeholder="Min" class="price-input">
        <span>-</span>
        <input type="number" id="maxPrice" placeholder="Max" class="price-input">
      </div>
    </div>
    <div class="filter-actions">
      <button class="btn btn-secondary" onclick="clearFilters()">Limpiar</button>
      <button class="btn btn-primary" onclick="applyFilters()">Aplicar</button>
    </div>
  </div>
</main>

<script src="<?= media(); ?>/js/functions_productos.js"></script>
<?php footerAdmin($data); ?>