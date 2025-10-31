    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media() ?>/images/avatar_default.png" alt="User Image">
        <div>
          <p class="app-sidebar__user-name">Mimi Miminson</p>
          <p class="app-sidebar__user-designation">Administrador</p>
        </div>
      </div>
      <ul class="app-menu">
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/dashboard">
            <i class="app-menu__icon fa fa-pie-chart" aria-hidden="true"></i>
            <span class="app-menu__label">Dashboard</span>
          </a>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-users" aria-hidden="true"></i></i><span class="app-menu__label">Usuarios</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="<?= base_url(); ?>/usuarios"><i class="icon fa fa-circle-o"></i> Usuarios</a></li>
            <li><a class="treeview-item" href="<?= base_url(); ?>/proveedores"><i class="icon fa fa-circle-o"></i>Proveedores</a></li>
          </ul>
        </li>
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/productos">
            <i class="app-menu__icon fa fa-shopping-bag" aria-hidden="true"></i>
            <span class="app-menu__label">Productos</span>
          </a>
        </li>

        <li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-bag" aria-hidden="true"></i></i><span class="app-menu__label">Categorias</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="<?= base_url(); ?>/categorias"><i class="icon fa fa-circle-o"></i> Categorias</a></li>
            <li><a class="treeview-item" href="<?= base_url(); ?>/subcategorias"><i class="icon fa fa-circle-o"></i>Subcategorias</a></li>
          </ul>
        </li>
          
        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/pedidos">
            <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
            <span class="app-menu__label">Pedidos</span>
          </a>
        </li>

        <li>
          <a class="app-menu__item" href="<?= base_url(); ?>/auth/logout">
            <i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i>
            <span class="app-menu__label">Cerrar sesión</span>
          </a>
        </li>
        
      </ul>
    </aside>