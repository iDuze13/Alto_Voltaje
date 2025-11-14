<?php 
    headerAdmin($data); 
    getModal('modalProductos',$data);
?>
    <script>
        // Pasar permisos de eliminación al JavaScript
        window.permisos_productos_eliminar = <?= isset($data['permiso_eliminar']) && $data['permiso_eliminar'] ? 'true' : 'false' ?>;
    </script>
    <main class="app-content">
      <div class="app-title">
        <div>
            <h1><i class="fas fa-box"></i> <?= $data['page_title'] ?>
              <?php if(isset($_SESSION['admin']) || (isset($_SESSION['permisos_modulos'][4]) && $_SESSION['permisos_modulos'][4]['w'] == 1)) { ?>
                <button class="btn btn-primary" type="button" onclick="openModal();" ><i class="fas fa-plus-circle"></i> Nuevo</button>
              <?php } ?> 
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/productos"><?= $data['page_title'] ?></a></li>
        </ul>
      </div>
        <div class="row">
            <div class="col-md-12">
              <div class="tile">
                <div class="tile-body">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="tableProductos" style="width: 100%;">
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
        </div>
    </main>
<?php footerAdmin($data); ?>