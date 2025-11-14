<?php 
    headerAdmin($data); 
    getModal('modalProductos',$data);
?>
    <!-- CSS Adicional para Productos -->
    <link rel="stylesheet" href="<?= media(); ?>/css/productos-ventas-pedidos.css">
    
    <main class="app-content">
      <!-- Header Mejorado -->
      <div class="productos-header">
        <h1><i class="fas fa-box"></i> Productos</h1>
        <div>
          <?php if(isset($_SESSION['admin'])) { ?>
            <button class="btn-nuevo" type="button" onclick="openModal();">
              <i class="fas fa-plus-circle"></i> Nuevo Producto
            </button>
          <?php } ?>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12">
          <div class="table-card">
            <div class="table-responsive">
              <table class="styled-table" id="tableProductos" style="width: 100%;">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>SKU</th>
                    <th>Código Barras</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>P. Costo</th>
                    <th>P. Venta</th>
                    <th>P. Oferta</th>
                    <th>Margen %</th>
                    <th>Stock</th>
                    <th>Oferta</th>
                    <th>Destacado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php footerAdmin($data); ?>