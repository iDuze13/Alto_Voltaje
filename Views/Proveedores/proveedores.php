<?php headerAdmin($data); 
      getModal('modalProveedores',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/proveedores-modern.css?v=<?= time(); ?>">

<main class="app-content">
  <div class="proveedores-header">
    <div class="header-left">
      <h1><i class="fa-solid fa-truck"></i> Proveedores</h1>
    </div>
    <div class="header-right">
      <button class="btn btn-export" onclick="exportProveedores()">
        <i class="fa-solid fa-download"></i> Exportar
      </button>
      <button class="btn btn-primary" onclick="openModal()">
        <i class="fa-solid fa-plus"></i> Añadir Proveedores
      </button>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">

      <!-- Search and Filter Section -->
      <div class="search-filter-section">
        <div class="search-container">
          <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="customSearchInput" placeholder="Buscar proveedores..." onkeyup="customSearch()">
          </div>
        </div>
        <div class="filter-container">
          <select id="filterProvincia" class="modern-select" onchange="filterByProvincia()">
            <option value="">Todas las provincias</option>
          </select>
        </div>
      </div>

      <!-- Table Section -->
      <div class="tile table-section">
        <div class="table-header">
          <div class="table-title">
            <h2>Lista de Proveedores</h2>
          </div>
        </div>
        
        <div class="modern-table-container">
          <table class="modern-table" id="tableProveedores">
            <thead>
              <tr>
                <th>
                  <div class="th-content">
                    <span>Nombre</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>CUIT</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Teléfono</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Email</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Dirección</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Ciudad</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Provincia</span>
                    <i class="fa-solid fa-sort"></i>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Acciones</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <!-- Data will be populated by DataTables -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>