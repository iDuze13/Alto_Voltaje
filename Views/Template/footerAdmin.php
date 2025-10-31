    <!-- Essential javascripts for application to work-->
    <script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?= media(); ?>/js/popper.min.js"></script>
    <script src="<?= media(); ?>/js/bootstrap.min.js"></script>
    <script src="<?= media(); ?>/js/main.js"></script>
    <script src="<?= media(); ?>/js/fontawesome.js"></script>
  <script src="<?= media(); ?>/js/functionsAdmin.js"></script>
  <?php if($data['page_name'] == "dashboard" || $data['page_name'] == "home"){ ?>
  <script src="<?= media(); ?>/js/plugins/chart.js"></script>
  <!-- Fallback Chart.js CDN -->
  <script>
    if (typeof Chart === 'undefined') {
      console.log('Loading Chart.js from CDN...');
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
      script.onload = () => console.log('Chart.js loaded from CDN');
      document.head.appendChild(script);
    } else {
      console.log('Chart.js loaded from local file');
    }
  </script>
  <script src="<?= media(); ?>/js/admin-dashboard.js"></script>
  <script src="<?= media(); ?>/js/simple-chart.js"></script>
  <script src="<?= media(); ?>/js/dashboard.js"></script>
  <script src="<?= media(); ?>/js/responsive-dashboard.js"></script>
  <?php } ?>
  <!-- The javascript plugin to display page loading on top-->
    <script src="<?= media(); ?>/js/plugins/pace.min.js"></script>
  
    <!-- Data table plugin-->
    <script type="text/javascript" src="<?= media(); ?>/js/plugins/jquery.dataTables.min.js"></script>

    <!-- Bootstrap Select plugin -->
    <script src="<?= media(); ?>/js/plugins/bootstrap-select.min.js"></script>
    <!-- Page specific javascripts-->
    <?php if($data['page_name'] == "usuarios"){ ?>
  <script src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
  <script src="<?= media(); ?>/js/functions_usuarios.js"></script>
    <?php } ?>
    <?php if($data['page_name'] == "categorias"){ ?>
  <script src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
  <script src="<?= media(); ?>/js/functions_categorias.js"></script>
    <?php } ?>
    <?php if($data['page_name'] == "proveedores"){ ?>
  <script src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
  <script src="<?= media(); ?>/js/functions_proveedores.js"></script>
    <?php } ?>
    <?php if($data['page_name'] == "subcategorias"){ ?>
  <script src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
  <script src="<?= media(); ?>/js/functions_subcategorias.js"></script>
    <?php } ?>
    <?php if($data['page_name'] == "productos"){ ?>
  <script src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
  <script src="<?= media(); ?>/js/functions_productos.js"></script>
    <?php } ?>
  </body>
</html>