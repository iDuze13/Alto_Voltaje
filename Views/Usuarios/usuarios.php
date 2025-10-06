<?php headerAdmin($data); 
      getModal('modalUsuarios',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/usuarios-modern.css">

<main class="app-content">
  <div class="usuarios-header">
    <div class="header-left">
      <h1><i class="fa-solid fa-users"></i> Usuarios</h1>
    </div>
    <div class="header-right">
      <button class="btn btn-export" onclick="exportUsuarios()">
        <i class="fa-solid fa-download"></i> Export
      </button>
      <button class="btn btn-primary" onclick="openModal()">
        <i class="fa-solid fa-plus"></i> Add usuarios
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
            <input type="text" id="customSearchInput" placeholder="Buscar usuarios..." onkeyup="customSearch()">
          </div>
        </div>
        <div class="filter-container">
          <select id="filterRole" class="modern-select" onchange="filterByRole()">
            <option value="">Todos los roles</option>
            <option value="Admin">Admin</option>
            <option value="Empleado">Empleado</option>
            <option value="Cliente">Cliente</option>
          </select>
        </div>
      </div>

      <!-- Table Section -->
      <div class="tile table-section">
        <div class="table-header">
          <div class="table-title">
            <h2>Lista de Usuarios</h2>
          </div>
        </div>    <div class="modern-table-container">
      <table class="modern-table" id="tableUsuarios">
        <thead>
          <tr>
            <th>
              <div class="th-content">
                <span>CUIL</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>Nombre</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>Apellido</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>Correo Electrónico</span>
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
                <span>Estado</span>
                <i class="fa-solid fa-sort"></i>
              </div>
            </th>
            <th>
              <div class="th-content">
                <span>Rol</span>
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
</main>

<?php footerAdmin($data); ?>