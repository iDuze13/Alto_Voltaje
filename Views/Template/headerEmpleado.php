<?php require_once(__DIR__ . '/../../Helpers/Helpers.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($data['page_title']) ? $data['page_title'] : 'Empleado - Alto Voltaje'; ?></title>
  <link rel="icon" type="image/png" href="<?= media() ?>/images/altovoltaje_logo.png">
  <link rel="stylesheet" href="<?= media() ?>/css/empleado.css">
</head>
<body class="emp-body">
