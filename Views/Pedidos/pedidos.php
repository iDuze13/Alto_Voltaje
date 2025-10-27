<?php 
require_once(__DIR__ . '/../../Helpers/DashboardHelpers.php');
headerAdmin($data); 
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-shopping-cart"></i> Pedidos</h1>
      <p>Gestión de pedidos de la tienda</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="#">Pedidos</a></li>
    </ul>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="tile-title-w-btn">
            <h3 class="title">Lista de Pedidos</h3>
          </div>
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tablePedidos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Cliente</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="6" class="text-center">No hay pedidos registrados</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php footerAdmin($data); ?>