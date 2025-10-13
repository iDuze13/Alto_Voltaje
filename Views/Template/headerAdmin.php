<?php require_once(__DIR__ . '/../../Helpers/Helpers.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="Alto Voltaje - Tienda de Electronica (Panel Administrativo)">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#e9a736ff"> <!-- Para cambiar el color de la barra de navegacion en dispositivos moviles -->
  <link rel="shortcut icon" href="<?php echo media(); ?>/images/altovoltaje_logo.png">
    <title><?= $data['page_tag']; ?></title>
    <!-- Main CSS-->
  <link rel="stylesheet" type="text/css" href="<?php echo media(); ?>/css/main.css">
  <link rel="stylesheet" type="text/css" href="<?php echo media(); ?>/css/bootstrap-select.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo media(); ?>/css/style.css">
  <link rel="stylesheet" type="text/css" href="<?php echo media(); ?>/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  </head>
  <body class="app sidebar-mini">
    <!-- Navbar-->
    <header class="app-header"><a class="app-header__logo" href="<?= base_url(); ?>/dashboard">Alto Voltaje</a>
      <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="<?= base_url(); ?>/configuraciones"><i class="fa-solid fa-gear fa-lg"></i> Confuraciones</a></li>
            <li><a class="dropdown-item" href="<?= base_url(); ?>/perfil"><i class="fa fa-user fa-lg"></i> Perfil</a></li>
            <li><a class="dropdown-item" href="<?= base_url(); ?>/auth/logout"><i class="fa fa-sign-out fa-lg"></i> Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </header>
    <?php require_once("navAdmin.php"); ?>