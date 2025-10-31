<!DOCTYPE html>
<html lang="es">
<head>
	<title>Alto Voltaje - Tienda de Electrónica</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Alto Voltaje - Los mejores productos de electrónica al mejor precio">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="<?= media() ?>/tiendaOnline/images/icons/favicon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/MagnificPopup/magnific-popup.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/css/util.css">
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/tiendaOnline/css/main.css?v=1.4">
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/tienda-modern.css?v=1.1">
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/tienda-minimal.css?v=1.0">
	<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/carrito-lateral.css?v=1.0">
	
	<style>
	
	/* Top Bar Amarilla */
	.top-bar-yellow {
		background: #FFD700;
		padding: 8px 0;
		font-size: 12px;
		color: #333;
		border-bottom: 1px solid #E6B800;
	}
	
	.top-bar-content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 20px;
	}
	
	.contact-info {
		display: flex;
		gap: 40px;
		align-items: center;
	}
	
	.contact-info i {
		margin-right: 5px;
	}
	
	.help-link {
		font-size: 12px;
	}
	
	.top-bar-yellow a {
		color: #333;
		text-decoration: none;
		transition: color 0.3s ease;
	}
	
	.top-bar-yellow a:hover {
		color: #000;
	}
	
	/* Header Principal */
	.main-header {
		background: #fff;
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		position: relative;
	}
	
	/* Header con logo, búsqueda e iconos */
	.header-main {
		padding: 15px 0;
		border-bottom: 1px solid #eee;
	}
	
	.header-main-content {
		display: flex;
		align-items: center;
		justify-content: space-between;
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 20px;
		gap: 20px;
	}
	
	/* Navegación debajo - ahora integrada */
	.header-nav {
		display: none; /* Ocultar la navegación separada */
	}
	
	.header-nav-content {
		display: flex;
		align-items: center;
		justify-content: space-between;
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 20px;
	}
	
	/* Logo */
	.logo-section {
		flex-shrink: 0;
	}
	
	.logo-section img {
		height: 22px;
		width: auto;
	}
	
	/* Search Center */
	.search-center {
		flex: 1;
		display: flex;
		justify-content: center;
		margin: 0 30px;
	}
	
	/* Navigation Menu */
	.nav-menu {
		display: flex;
		list-style: none;
		margin: 0;
		padding: 0;
		gap: 30px;
		justify-content: center;
	}
	
	.nav-menu li a {
		color: #666;
		text-decoration: none;
		font-weight: 400;
		font-size: 14px;
		transition: color 0.3s ease;
	}
	
	.nav-menu li a:hover {
		color: #333;
	}
	
	/* Search Bar */
	.search-container {
		position: relative;
		width: 100%;
		max-width: 350px;
	}
	
	.search-input {
		width: 100%;
		padding: 10px 40px 10px 15px;
		border: none;
		border-radius: 20px;
		font-size: 14px;
		background: #FFD700;
		color: #333;
		box-shadow: 0 1px 3px rgba(0,0,0,0.1);
	}
	
	.search-input:focus {
		outline: none;
		box-shadow: 0 2px 5px rgba(0,0,0,0.15);
	}
	
	.search-input::placeholder {
		color: #666;
	}
	
	.search-btn {
		position: absolute;
		right: 6px;
		top: 50%;
		transform: translateY(-50%);
		background: transparent;
		border: none;
		width: 30px;
		height: 30px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		border-radius: 50%;
	}
	
	.search-btn:hover {
		background: rgba(0,0,0,0.1);
	}
	
	.search-btn i {
		color: #333;
		font-size: 14px;
	}
	
	/* Header Actions - ahora incluye navegación y login */
	.header-actions {
		display: flex;
		align-items: center;
		gap: 15px;
		flex-shrink: 0;
	}
	
	/* Navegación integrada en header actions */
	.header-actions .nav-menu {
		display: flex;
		list-style: none;
		margin: 0;
		padding: 0;
		gap: 20px;
	}
	
	.header-actions .nav-menu li a {
		color: #666;
		text-decoration: none;
		font-weight: 400;
		font-size: 14px;
		transition: color 0.3s ease;
		padding: 8px 0;
	}
	
	.header-actions .nav-menu li a:hover {
		color: #333;
	}
	
	.action-icon {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 40px;
		height: 40px;
		cursor: pointer;
		color: #666;
		transition: all 0.3s ease;
		border-radius: 50%;
		background: #f8f9fa;
		border: 1px solid #e9ecef;
	}
	
	.action-icon:hover {
		background: #e9ecef;
		color: #333;
	}
	
	.action-icon i {
		font-size: 16px;
	}
	
	/* Cart Button with Price */
	.cart-button {
		display: flex;
		align-items: center;
		background: #333;
		color: white;
		border: none;
		border-radius: 20px;
		padding: 10px 16px;
		cursor: pointer;
		transition: background-color 0.3s ease;
		font-weight: 500;
		gap: 6px;
		font-size: 14px;
	}
	
	.cart-button:hover {
		background: #555;
	}
	
	.cart-button i {
		font-size: 14px;
	}
	
	/* Login Button */
	.login-button {
		background: #FFD700;
		color: #333;
		border: none;
		border-radius: 20px;
		padding: 10px 20px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.3s ease;
		text-decoration: none;
		display: inline-flex;
		align-items: center;
		gap: 6px;
		font-size: 14px;
	}
	
	.login-button:hover {
		background: #FFC107;
		color: #333;
		text-decoration: none;
	}

	/* Mobile Menu Button */
	.mobile-menu-btn {
		display: none;
		background: none;
		border: none;
		font-size: 18px;
		color: #333;
		cursor: pointer;
		padding: 8px;
		border-radius: 4px;
		transition: background-color 0.3s ease;
	}
	
	.mobile-menu-btn:hover {
		background: rgba(0,0,0,0.1);
	}

	/* Mobile Menu Styles */
	.mobile-menu {
		display: none;
		background: white;
		border-top: 1px solid #eee;
		box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		position: absolute;
		top: 100%;
		left: 0;
		right: 0;
		z-index: 1000;
	}
	
	.mobile-menu.active {
		display: block;
	}
	
	.mobile-nav-menu {
		list-style: none;
		margin: 0;
		padding: 15px;
	}
	
	.mobile-nav-menu li {
		border-bottom: 1px solid #f5f5f5;
	}
	
	.mobile-nav-menu li:last-child {
		border-bottom: none;
	}
	
	.mobile-nav-menu li a {
		display: flex;
		align-items: center;
		padding: 12px 0;
		color: #333;
		text-decoration: none;
		font-size: 14px;
		transition: color 0.3s ease;
	}
	
	.mobile-nav-menu li a:hover {
		color: #666;
	}
	
	.mobile-nav-menu li a i {
		margin-right: 10px;
		width: 20px;
		text-align: center;
	}
	
	.mobile-user-actions {
		padding: 15px;
		border-top: 1px solid #f5f5f5;
		background: #f9f9f9;
	}
	
	.mobile-cart-summary {
		cursor: pointer;
		transition: background-color 0.3s ease;
	}
	
	.mobile-cart-summary:hover {
		background: #e9ecef !important;
	}

	/* Responsive Styles */
	@media (max-width: 1200px) {
		.header-main-content {
			max-width: 100%;
			padding: 0 15px;
			gap: 15px;
		}
		
		.search-container {
			max-width: 300px;
		}
		
		.header-actions .nav-menu {
			gap: 15px;
		}
	}

	@media (max-width: 992px) {
		.mobile-menu-btn {
			display: flex !important;
		}
		
		.header-actions .nav-menu {
			display: none !important;
		}
		
		.header-main-content {
			gap: 12px;
		}
		
		.search-center {
			flex: 1;
			margin: 0 10px;
		}
		
		.search-container {
			max-width: none;
		}
		
		.header-actions {
			gap: 10px;
		}
		
		.action-icon {
			width: 36px;
			height: 36px;
		}
		
		.cart-button {
			padding: 8px 12px;
			font-size: 13px;	
		}
		
		.login-button {
			padding: 8px 12px;
			font-size: 13px;
		}
	}

	@media (max-width: 768px) {
		.top-bar-yellow {
			padding: 6px 0;
			font-size: 11px;
		}
		
		.contact-info {
			gap: 15px;
		}
		
		.contact-info span {
			font-size: 10px;
		}
		
		.header-main {
			padding: 12px 0;
		}
		
		.header-main-content {
			gap: 10px;
			padding: 0 10px;
		}
		
		.logo-section img {
			height: 35px;
		}
		
		.search-center {
			flex: 1;
			margin: 0 8px;
		}
		
		.search-input {
			padding: 10px 35px 10px 12px;
			font-size: 14px;
		}
		
		.search-btn {
			width: 30px;
			height: 30px;
		}
		
		.search-btn i {
			font-size: 13px;
		}
		
		.header-actions {
			gap: 8px;
		}
		
		.action-icon {
			width: 34px;
			height: 34px;
		}
		
		.action-icon i {
			font-size: 14px;
		}
		
		.cart-button {
			padding: 8px 10px;
			font-size: 12px;
			gap: 4px;
		}
		
		.login-button {
			padding: 8px 12px;
			font-size: 12px;
			gap: 4px;
		}
		
		.mobile-menu-btn {
			width: 38px;
			height: 38px;
			font-size: 16px;
		}
	}

	@media (max-width: 576px) {
		.top-bar-yellow {
			padding: 5px 0;
			font-size: 10px;
		}
		
		.top-bar-content {
			flex-direction: column;
			gap: 3px;
			text-align: center;
			padding: 0 10px;
		}
		
		.contact-info {
			flex-direction: row;
			gap: 10px;
			justify-content: center;
		}
		
		.contact-info span {
			font-size: 9px;
		}
		
		.help-link {
			font-size: 9px;
		}
		
		.header-main {
			padding: 10px 0;
		}
		
		.header-main-content {
			gap: 8px;
			padding: 0 8px;
		}
		
		.logo-section img {
			height: 30px;
		}
		
		.search-center {
			flex: 1;
			margin: 0 6px;
		}
		
		.search-input {
			padding: 8px 30px 8px 10px;
			font-size: 13px;
		}
		
		.search-btn {
			width: 28px;
			height: 28px;
			right: 3px;
		}
		
		.search-btn i {
			font-size: 12px;
		}
		
		.header-actions {
			gap: 6px;
		}
		
		.action-icon {
			width: 32px;
			height: 32px;
		}
		
		.action-icon i {
			font-size: 13px;
		}
		
		.cart-button {
			padding: 6px 8px;
			font-size: 11px;
		}
		
		.cart-button span {
			display: none; /* Ocultar precio en pantallas muy pequeñas */
		}
		
		.login-button {
			padding: 6px 10px;
			font-size: 11px;
		}
		
		.mobile-menu-btn {
			width: 36px;
			height: 36px;
			font-size: 15px;
		}
	}

	@media (max-width: 480px) {
		.top-bar-yellow {
			display: none; /* Ocultar barra superior en pantallas extra pequeñas */
		}
		
		.header-main {
			padding: 8px 0;
		}
		
		.header-main-content {
			flex-wrap: wrap;
			gap: 6px;
			padding: 0 6px;
		}
		
		.logo-section {
			order: 1;
		}
		
		.logo-section img {
			height: 28px;
		}
		
		.header-actions {
			order: 2;
			gap: 4px;
		}
		
		.search-center {
			order: 3;
			flex: 1 1 100%;
			margin: 5px 0 0 0;
		}
		
		.search-input {
			padding: 8px 28px 8px 10px;
			font-size: 12px;
		}
		
		.search-btn {
			width: 26px;
			height: 26px;
		}
		
		.action-icon {
			width: 30px;
			height: 30px;
		}
		
		.action-icon i {
			font-size: 12px;
		}
		
		.cart-button {
			padding: 5px 6px;
			font-size: 10px;
		}
		
		.login-button {
			display: none; /* Ocultar botón de login, usar menú móvil */
		}
		
		.mobile-menu-btn {
			width: 34px;
			height: 34px;
			font-size: 14px;
		}
	}
	
	.cart-section:hover {
		background: #555;
		color: white;
		transform: translateY(-1px);
		box-shadow: 0 2px 4px rgba(0,0,0,0.25);
	}
	
	.cart-section:active {
		transform: translateY(0);
		box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	}
	
	.cart-price {
		display: none; /* Ocultar el precio en el botón circular */
	}
	
	.cart-section i {
		font-size: 24px;
		color: white;
	}
	
	/* User Access - ahora en la tercera línea con sombreado y posicionado a la derecha */
	.user-access {
		background: #FFD700;
		color: #333;
		font-size: 13px;
		display: flex;
		align-items: center;
		gap: 6px;
		padding: 6px 14px;
		border-radius: 20px;
		border: 1px solid #E6B800;
		transition: background-color 0.2s ease, border-color 0.2s ease;
		box-shadow: 0 1px 3px rgba(0,0,0,0.1);
		position: absolute;
		right: 20px;
		top: 50%;
		transform: translateY(-50%);
	}
	
	.user-access:hover {
		background: #F5D700;
		border-color: #D4B800;
	}
	
	.user-access i {
		font-size: 16px;
		color: #333;
	}
	
	.user-access a {
		color: #333;
		text-decoration: none;
		font-weight: 600;
		transition: color 0.3s ease;
	}
	
	.user-access a:hover {
		color: #000;
	}
	
	/* Mobile Menu */
	.mobile-menu-btn {
		display: none;
		background: none;
		border: 2px solid #ddd;
		border-radius: 8px;
		font-size: 20px;
		color: #2c3e50;
		cursor: pointer;
		padding: 8px 10px;
		transition: all 0.2s ease;
		width: 44px;
		height: 44px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	
	.mobile-menu-btn:hover {
		background: #f8f9fa;
		border-color: #ccc;
		color: #000;
	}
	
	.mobile-menu-btn:focus {
		outline: none;
		border-color: #FFD700;
		box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.3);
	}
	
	/* Mobile Menu Styles */
	.mobile-menu {
		background: #fff;
		border-top: 2px solid #FFD700;
		padding: 0;
		box-shadow: 0 8px 25px rgba(0,0,0,0.15);
		position: absolute;
		top: 100%;
		left: 0;
		right: 0;
		z-index: 1000;
		opacity: 0;
		transform: translateY(-10px);
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		max-height: 0;
		overflow: hidden;
	}
	
	.mobile-menu.active {
		opacity: 1;
		transform: translateY(0);
		max-height: 600px;
		padding: 20px 0;
	}
	
	.mobile-nav-menu {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	
	.mobile-nav-menu li {
		border-bottom: 1px solid #f0f0f0;
	}
	
	.mobile-nav-menu li:last-child {
		border-bottom: none;
	}
	
	.mobile-nav-menu li a {
		display: flex;
		align-items: center;
		padding: 18px 20px;
		color: #2c3e50;
		text-decoration: none;
		font-weight: 500;
		font-size: 16px;
		transition: all 0.3s ease;
		border-radius: 8px;
		margin: 5px 0;
		position: relative;
	}
	
	.mobile-nav-menu li a::before {
		content: '';
		position: absolute;
		left: 0;
		top: 0;
		bottom: 0;
		width: 4px;
		background: #FFD700;
		transform: scaleY(0);
		transition: transform 0.3s ease;
		border-radius: 0 2px 2px 0;
	}
	
	.mobile-nav-menu li a:hover, .mobile-nav-menu li a:focus {
		color: #000;
		background: #fff8e1;
		transform: translateX(8px);
	}
	
	.mobile-nav-menu li a:hover::before, .mobile-nav-menu li a:focus::before {
		transform: scaleY(1);
	}
	
	.mobile-nav-menu li a i {
		margin-right: 12px;
		width: 20px;
		color: #FFD700;
		font-size: 18px;
		transform: translateX(5px);
	}
	
	.mobile-nav-menu li a i {
		color: #6c757d;
		font-size: 16px;
		width: 20px;
	}
	
	.mobile-search {
		position: relative;
	}
	
	.mobile-search form {
		display: flex;
		gap: 10px;
	}
	
	.mobile-search {
		padding: 0 15px;
	}
	
	.mobile-search .input-group {
		border-radius: 25px;
		overflow: hidden;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}
	
	.mobile-search input {
		border: none;
		padding: 12px 15px;
		font-size: 14px;
		background: #f8f9fa;
	}
	
	.mobile-search input:focus {
		background: #fff;
		box-shadow: none;
		border: none;
	}
	
	.mobile-search button {
		background: #FFD700;
		border: none;
		padding: 12px 15px;
		color: #333;
		cursor: pointer;
		transition: all 0.3s ease;
	}
	
	.mobile-search button:hover, .mobile-search button:focus {
		background: #FFC107;
		outline: none;
	}
	
	/* Mobile User Actions */
	.mobile-user-actions .btn {
		border-radius: 20px;
		font-size: 14px;
		padding: 8px 12px;
		transition: all 0.3s ease;
	}
	
	.mobile-user-actions .btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 8px rgba(0,0,0,0.1);
	}
	
	.mobile-cart-summary {
		border: 1px solid #e1e8ed;
		transition: all 0.3s ease;
		cursor: pointer;
	}
	
	.mobile-cart-summary:hover {
		border-color: #FFD700;
		background: #fffbf0 !important;
		transform: translateY(-1px);
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}

	/* Responsive Design */
	
	/* Extra Large Screens (1400px and up) */
	@media (min-width: 1400px) {
		.header-top-content, .header-bottom-content {
			max-width: 1320px;
		}
		
		.search-input-yellow {
			width: 450px; /* Tamaño máximo para pantallas grandes */
		}
		
		.cart-section {
			padding: 14px 22px;
		}
		
		.cart-price {
			font-size: 17px;
		}
		
		.cart-section i {
			font-size: 22px;
		}
		
		.nav-menu {
			gap: 45px;
		}
		
		.user-access {
			padding: 7px 16px;
			font-size: 14px;
			right: 20px;
		}
		

	}
	
	/* Large Screens (1200px to 1399px) */
	@media (max-width: 1399px) and (min-width: 1200px) {
		.header-top-content, .header-bottom-content {
			max-width: 1140px;
		}
		
		.search-input-yellow {
			width: 350px;
		}
		
		.cart-section {
			padding: 10px 18px;
		}
		
		.cart-price {
			font-size: 15px;
		}
		
		.cart-section i {
			font-size: 19px;
		}
		
		.nav-menu {
			gap: 35px;
		}
	}
	
	/* Medium-Large Screens (992px to 1199px) */
	@media (max-width: 1199px) and (min-width: 992px) {
		.header-main-content {
			max-width: 960px;
			padding: 0 15px;
			gap: 15px;
		}
		
		.search-container {
			max-width: 300px;
		}
		
		.header-actions .nav-menu {
			gap: 15px;
		}
		
		.header-actions .nav-menu li a {
			font-size: 13px;
		}
		
		.header-actions {
			gap: 12px;
		}
		
		.action-icon {
			width: 35px;
			height: 35px;
		}
		
		.cart-button {
			padding: 8px 12px;
			font-size: 13px;
		}
		
		.login-button {
			padding: 8px 15px;
			font-size: 13px;
		}
	}
	
	/* Medium Screens (768px to 991px) */
	@media (max-width: 991px) and (min-width: 768px) {
		.header-main-content {
			padding: 0 15px;
			gap: 10px;
		}
		
		.search-container {
			max-width: 220px;
		}
		
		.search-input {
			padding: 8px 35px 8px 12px;
			font-size: 13px;
		}
		
		/* Ocultar navegación en tablets, mostrar menú móvil */
		.header-actions .nav-menu {
			display: none;
		}
		
		.mobile-menu-btn {
			display: flex !important;
		}
		
		.header-actions {
			gap: 8px;
		}
		
		.action-icon {
			width: 32px;
			height: 32px;
		}
		
		.action-icon i {
			font-size: 14px;
		}
		
		.cart-button {
			padding: 6px 10px;
			font-size: 12px;
		}
		
		.login-button {
			padding: 6px 12px;
			font-size: 12px;
		}
		
		/* Top bar responsive */
		.top-bar-yellow {
			padding: 6px 0;
			font-size: 12px;
		}
		
		.contact-info {
			gap: 20px;
		}
		
		.contact-info span {
			font-size: 11px;
		}
	}
	
	/* Small Screens (576px to 767px) */
	@media (max-width: 767px) and (min-width: 576px) {
		.header-main-content {
			padding: 0 15px;
			gap: 8px;
		}
		
		.search-center {
			flex: 1;
			margin: 0 10px;
		}
		
		.search-container {
			max-width: none;
		}
		
		.search-input {
			padding: 8px 35px 8px 12px;
			font-size: 13px;
		}
		
		/* Ocultar navegación en móviles */
		.header-actions .nav-menu {
			display: none;
		}
		
		.mobile-menu-btn {
			display: flex !important;
		}
		
		.header-actions {
			gap: 6px;
		}
		
		.action-icon {
			width: 30px;
			height: 30px;
		}
		
		.cart-button {
			padding: 6px 8px;
			font-size: 11px;
		}
		
		.cart-button span {
			display: none; /* Ocultar precio en móviles */
		}
		
		.login-button {
			padding: 6px 10px;
			font-size: 11px;
		}
		
		.header-top-content {
			padding: 0 10px;
			gap: 10px;
		}
		
		.header-actions {
			gap: 8px;
		}
		
		.header-icon {
			width: 28px;
			height: 28px;
		}
		
		.header-icon i {
			font-size: 14px;
		}
		
		.cart-section {
			padding: 3px 6px;
			border-radius: 14px;
		}
		
		.cart-price {
			font-size: 10px;
		}
		
		.cart-section i {
			font-size: 12px;
		}
		
		.contact-info {
			gap: 15px;
		}
		
		.contact-info span {
			font-size: 10px;
		}
		
		.help-link {
			font-size: 10px;
		}
		
		.cart-badge {
			width: 18px;
			height: 18px;
			font-size: 10px;
		}
		
		/* Top bar adjustments */
		.top-bar-yellow {
			padding: 5px 0;
			font-size: 11px;
		}
		
		.top-bar-yellow .container {
			padding: 0 15px;
		}
		
		.top-bar-yellow small {
			display: block;
			margin-bottom: 3px;
		}
		
		.top-bar-yellow .ml-3 {
			margin-left: 0 !important;
		}
		
		/* Mobile menu improvements */
		.mobile-menu {
			padding: 15px 0;
		}
		
		.mobile-nav-menu li a {
			padding: 12px 0;
			font-size: 15px;
		}
		
		.mobile-search input {
			padding: 10px 12px;
			font-size: 14px;
		}
		
		.mobile-search button {
			padding: 10px 15px;
		}
	}
	
	/* Extra Small Screens (below 576px) */
	@media (max-width: 575px) {
		.search-container, .header-bottom {
			display: none;
		}
		
		.mobile-menu-btn {
			display: flex !important;
		}
		
		.header-top {
			padding: 8px 0;
		}
		
		.header-top-content {
			padding: 0 8px;
			gap: 8px;
		}
		
		.header-actions {
			gap: 6px;
		}
		
		.header-icon {
			width: 26px;
			height: 26px;
		}
		
		.header-icon i {
			font-size: 12px;
		}
		
		.cart-section {
			padding: 2px 5px;
			border-radius: 12px;
		}
		
		.cart-price {
			font-size: 9px;
		}
		
		.cart-section i {
			font-size: 11px;
		}
		
		/* Top bar - simplificar al máximo */
		.top-bar-yellow {
			padding: 4px 0;
			font-size: 10px;
		}
		
		.contact-info {
			gap: 10px;
		}
		
		.contact-info span {
			font-size: 9px;
		}
		
		.help-link {
			font-size: 9px;
		}
		
		.contact-info i {
			display: none; /* Ocultar iconos en móviles */
		}
		
		.logo-section img {
			max-height: 40px; /* Reducir logo en móviles */
		}
		
		.cart-badge {
			width: 16px;
			height: 16px;
			font-size: 9px;
		}
		
		/* Top bar for very small screens */
		.top-bar-yellow {
			padding: 4px 0;
			font-size: 10px;
		}
		
		.top-bar-yellow .container {
			padding: 0 10px;
		}
		
		.top-bar-yellow .row {
			flex-direction: column;
		}
		
		.top-bar-yellow .col-md-6 {
			width: 100%;
			margin-bottom: 2px;
		}
		
		.top-bar-yellow small {
			display: inline-block;
			margin-right: 10px;
			margin-bottom: 2px;
		}
		
		.top-bar-yellow .ml-3, .top-bar-yellow .ml-2 {
			margin-left: 0 !important;
		}
		
		/* Mobile menu for small screens */
		.mobile-menu {
			padding: 10px 0;
		}
		
		.mobile-nav-menu li a {
			padding: 10px 0;
			font-size: 14px;
		}
		
		.mobile-search {
			padding: 0 10px;
		}
		
		.mobile-search input {
			padding: 8px 10px;
			font-size: 13px;
		}
		
		.mobile-search button {
			padding: 8px 12px;
			font-size: 13px;
		}
		
		.mobile-search form {
			gap: 5px;
		}
	}
	
	/* Landscape Phone Orientation */
	@media (max-width: 767px) and (orientation: landscape) {
		.top-bar-yellow {
			display: none; /* Hide top bar in landscape to save space */
		}
		
		.header-main {
			padding: 8px 0;
		}
		
		.header-main-content {
			gap: 6px;
		}
		
		.logo-section img {
			height: 32px;
		}
		
		.action-icon {
			width: 30px;
			height: 30px;
		}
		
		.mobile-menu {
			max-height: 300px; /* Reducir altura en landscape */
		}
	}
	
	/* Tablet Portrait */
	@media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
		.header-main-content {
			gap: 12px;
		}
		
		.search-container {
			max-width: 250px;
		}
		
		.header-actions .nav-menu {
			display: none;
		}
		
		.mobile-menu-btn {
			display: flex !important;
		}
	}
	
	/* Large Mobile and Small Tablet */
	@media (min-width: 576px) and (max-width: 767px) {
		.header-main-content {
			flex-wrap: nowrap;
		}
		
		.search-center {
			flex: 1;
			margin: 0 10px;
		}
		
		.mobile-search {
			display: none; /* Usar búsqueda principal */
		}
	}
	
	/* High DPI Displays */
	@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
		.logo-section img {
			image-rendering: -webkit-optimize-contrast;
			image-rendering: crisp-edges;
		}
	}
	
	/* Print Styles */
	@media print {
		.top-bar-yellow, .mobile-menu, .header-actions {
			display: none !important;
		}
		
		.main-header {
			box-shadow: none;
			border-bottom: 1px solid #000;
		}
		
		.logo-section img {
			height: 40px !important;
		}
	}
	</style>
<!--===============================================================================================-->
</head>
<body class="animsition">
	
	<!-- Header-->
	<header>
		<!-- Top Bar Amarilla - Línea 1 -->
		<div class="top-bar-yellow">
			<div class="top-bar-content">
				<div class="contact-info">
					<span>
						<i class="fa fa-map-marker me-1"></i>
						Frondizi 4566, Formosa Capital
					</span>
					<span>
						<i class="fa fa-envelope me-1"></i>
						info@altovoltaje.com
					</span>
				</div>
				<div class="help-link">
					<a href="#" class="text-decoration-none">Ayuda</a>
				</div>
			</div>
		</div>

		<!-- Header Principal -->
		<div class="main-header">
			<!-- Header principal: Logo, Búsqueda, Navegación, Iconos, Login - TODO EN UNA LÍNEA -->
			<div class="header-main">
				<div class="header-main-content">
					<!-- Logo Section -->
					<div class="logo-section">
						<a href="<?= BASE_URL ?>/" class="text-decoration-none">
							<img src="<?= media() ?>/tiendaOnline/images/icons/logo-01.png" alt="ALTO VOLTAJE">
						</a>
					</div>

					<!-- Search Bar Center -->
					<div class="search-center">
						<div class="search-container">
							<form method="GET" action="<?= BASE_URL ?>/tienda">
								<input type="text" name="q" class="search-input" placeholder="Buscar productos" value="<?= $_GET['q'] ?? '' ?>">
								<button type="submit" class="search-btn">
									<i class="fa fa-search"></i>
								</button>
							</form>
						</div>
					</div>

					<!-- Action Icons + Navegación + Login -->
					<div class="header-actions">
						<!-- Navigation Menu -->
						<nav class="d-none d-lg-block">
							<ul class="nav-menu">
								<li><a href="<?= BASE_URL ?>/">Inicio</a></li>
								<li><a href="<?= BASE_URL ?>/tienda">Tienda</a></li>
								<li><a href="<?= BASE_URL ?>/nosotros">Nosotros</a></li>
								<li><a href="<?= BASE_URL ?>/contacto">Contacto</a></li>
							</ul>
						</nav>

						<!-- Compare Icon -->
						<div class="action-icon" onclick="toggleCompare()" title="Comparar productos">
							<i class="fa fa-exchange"></i>
						</div>
						
						<!-- Wishlist Icon -->
						<div class="action-icon" onclick="toggleWishlist()" title="Lista de deseos">
							<i class="fa fa-heart-o"></i>
						</div>
						
						<!-- Cart Button with Price -->
						<button class="cart-button" onclick="toggleCart()" title="Carrito de compras">
							<i class="fa fa-shopping-cart"></i>
							<span class="cart-price">$0,00</span>
							<span class="cart-count-main" style="display: none;">0</span>
						</button>

						<!-- Login Button -->
						<?php if (!empty($_SESSION['admin']) || !empty($_SESSION['empleado']) || !empty($_SESSION['usuario'])): ?>
							<a href="<?= BASE_URL ?>/auth/logout" class="login-button">
								<i class="fa fa-user"></i>
								Salir
							</a>
						<?php else: ?>
							<a href="<?= BASE_URL ?>/auth/login" class="login-button">
								<i class="fa fa-sign-in"></i>
								Iniciar Sesión
							</a>
						<?php endif; ?>

						<!-- Mobile Menu Button -->
						<button class="mobile-menu-btn d-lg-none" onclick="toggleMobileMenu()">
							<i class="fa fa-bars"></i>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Mobile Menu -->
		<div id="mobile-menu" class="mobile-menu" style="display: none;" role="navigation" aria-label="Menú móvil">
			<div class="container-fluid">
				<!-- Mobile Search (visible en pantallas muy pequeñas) -->
				<div class="mobile-search d-block d-sm-none mb-3">
					<form method="GET" action="<?= BASE_URL ?>/tienda">
						<div class="input-group">
							<input type="text" name="q" class="form-control" placeholder="Buscar productos..." value="<?= $_GET['q'] ?? '' ?>">
							<button type="submit" class="btn btn-warning">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</form>
				</div>

				<!-- Mobile Navigation -->
				<ul class="mobile-nav-menu list-unstyled">
					<li><a href="<?= BASE_URL ?>/" class="d-flex align-items-center"><i class="fa fa-home me-2"></i>Inicio</a></li>
					<li><a href="<?= BASE_URL ?>/tienda" class="d-flex align-items-center"><i class="fa fa-shopping-bag me-2"></i>Tienda</a></li>
					<li><a href="<?= BASE_URL ?>/nosotros" class="d-flex align-items-center"><i class="fa fa-user me-2"></i>Nosotros</a></li>
					<li><a href="<?= BASE_URL ?>/contacto" class="d-flex align-items-center"><i class="fa fa-envelope me-2"></i>Contacto</a></li>
				</ul>

				<!-- Mobile User Actions -->
				<div class="mobile-user-actions mt-3 pt-3 border-top">
					<!-- Login/Logout Button -->
					<div class="mb-2">
						<?php if (!empty($_SESSION['admin']) || !empty($_SESSION['empleado']) || !empty($_SESSION['usuario'])): ?>
							<a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-danger w-100">
								<i class="fa fa-sign-out me-1"></i>
								Cerrar Sesión
							</a>
						<?php else: ?>
							<a href="<?= BASE_URL ?>/auth/login" class="btn btn-warning w-100">
								<i class="fa fa-sign-in me-1"></i>
								Iniciar Sesión
							</a>
						<?php endif; ?>
					</div>
					
					<!-- Quick Actions -->
					<div class="row g-2 mb-2">
						<div class="col-6">
							<button class="btn btn-outline-secondary w-100" onclick="toggleWishlist()">
								<i class="fa fa-heart-o me-1"></i>
								<span class="d-none d-sm-inline">Favoritos</span>
							</button>
						</div>
						<div class="col-6">
							<button class="btn btn-outline-secondary w-100" onclick="toggleCompare()">
								<i class="fa fa-exchange me-1"></i>
								<span class="d-none d-sm-inline">Comparar</span>
							</button>
						</div>
					</div>
					
					<!-- Cart Summary in Mobile -->
					<div class="mobile-cart-summary p-3 bg-light rounded" onclick="toggleCart()">
						<div class="d-flex justify-content-between align-items-center">
							<span><i class="fa fa-shopping-cart me-2"></i>Carrito de Compras</span>
							<span class="fw-bold" id="mobile-cart-total">$0.00</span>
						</div>
						<small class="text-muted">0 productos</small>
					</div>
				</div>
			</div>
		</div>
	</header>

	<script>
		// Global state
		let isMobileMenuOpen = false;
		let cartItems = [];
		let wishlistItems = [];

		// Mobile Menu Toggle
		function toggleMobileMenu() {
			const mobileMenu = document.getElementById('mobile-menu');
			const menuBtn = document.querySelector('.mobile-menu-btn');
			
			if (!mobileMenu || !menuBtn) {
				console.error('Mobile menu elements not found');
				return;
			}
			
			isMobileMenuOpen = !isMobileMenuOpen;
			
			if (isMobileMenuOpen) {
				// Mostrar menú
				mobileMenu.style.display = 'block';
				// Forzar reflow
				mobileMenu.offsetHeight;
				mobileMenu.classList.add('active');
				
				// Actualizar botón
				menuBtn.setAttribute('aria-expanded', 'true');
				menuBtn.innerHTML = '<i class="fa fa-times"></i>';
				
				// Prevenir scroll del body
				document.body.style.overflow = 'hidden';
			} else {
				// Ocultar menú
				mobileMenu.classList.remove('active');
				
				// Actualizar botón
				menuBtn.setAttribute('aria-expanded', 'false');
				menuBtn.innerHTML = '<i class="fa fa-bars"></i>';
				
				// Restaurar scroll del body
				document.body.style.overflow = '';
				
				// Ocultar después de la animación
				setTimeout(() => {
					if (!isMobileMenuOpen) {
						mobileMenu.style.display = 'none';
					}
				}, 300);
			}
		}

		// Compare Toggle
		function toggleCompare() {
			console.log('Toggle compare');
			// Aquí puedes implementar la lógica de comparación
			showNotification('Comparador abierto', 'info');
		}

		// Cart Toggle
		function toggleCart() {
			console.log('Toggle cart');
			// Aquí puedes implementar la lógica del carrito
			// Por ejemplo, mostrar un offcanvas o modal con los productos
			
			// Ejemplo básico: redirigir a la página del carrito
			// window.location.href = '<?= BASE_URL ?>/carrito';
			
			// O mostrar notificación
			showNotification('Carrito de compras - $0,00 (0 productos)', 'info');
			
			// Puedes agregar aquí la lógica para mostrar un modal del carrito
			// o un dropdown con los productos
		}

		// Wishlist Toggle
		function toggleWishlist() {
			console.log('Toggle wishlist');
			// Aquí puedes implementar la lógica de favoritos
			showNotification('Lista de deseos abierta', 'info');
		}

		// User Menu Toggle
		function toggleUserMenu() {
			console.log('Toggle user menu');
			// Aquí puedes implementar el menú de usuario
			showNotification('Menú de usuario abierto', 'info');
		}

		// Update cart count and total
		function updateCartDisplay(count, total) {
			const cartCountElem = document.getElementById('cart-count');
			const cartTotalElem = document.getElementById('cart-total');
			const mobileCartTotalElem = document.getElementById('mobile-cart-total');
			
			if (cartCountElem) cartCountElem.textContent = count;
			if (cartTotalElem) cartTotalElem.textContent = '$' + total.toFixed(2);
			if (mobileCartTotalElem) mobileCartTotalElem.textContent = '$' + total.toFixed(2);
			
			// Update cart badge visibility
			if (cartCountElem) {
				cartCountElem.style.display = count > 0 ? 'flex' : 'none';
			}
		}

		// Show notification
		function showNotification(message, type = 'info') {
			const notification = document.createElement('div');
			notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
			notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
			notification.innerHTML = `
				<strong>${message}</strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			`;
			
			document.body.appendChild(notification);
			
			// Auto remove after 3 seconds
			setTimeout(() => {
				if (notification.parentNode) {
					notification.remove();
				}
			}, 3000);
		}

		// Close mobile menu when clicking outside
		document.addEventListener('click', function(event) {
			const mobileMenu = document.getElementById('mobile-menu');
			const menuBtn = document.querySelector('.mobile-menu-btn');
			
			if (isMobileMenuOpen && !mobileMenu.contains(event.target) && !menuBtn.contains(event.target)) {
				toggleMobileMenu();
			}
		});

		// Handle escape key to close mobile menu
		document.addEventListener('keydown', function(event) {
			if (event.key === 'Escape' && isMobileMenuOpen) {
				toggleMobileMenu();
			}
		});

		// Window resize handler
		let resizeTimer;
		window.addEventListener('resize', function() {
			// Debounce resize events
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function() {
				// Close mobile menu on desktop sizes
				if (window.innerWidth >= 992 && isMobileMenuOpen) {
					toggleMobileMenu();
				}
				
				// Adjust header on orientation change
				handleOrientationChange();
			}, 250);
		});
		
		// Handle orientation change
		function handleOrientationChange() {
			if (window.innerHeight < window.innerWidth && window.innerWidth <= 767) {
				// Landscape mobile - compact header
				document.querySelector('.main-header').style.padding = '6px 0';
			} else {
				// Reset header padding
				document.querySelector('.main-header').style.padding = '';
			}
		}
		
		// Page visibility change (when switching tabs)
		document.addEventListener('visibilitychange', function() {
			if (document.hidden) {
				// Page is hidden (user switched tab)
				console.log('User switched away from tab');
			} else {
				// Page is visible (user returned to tab)
				console.log('User returned to tab');
				
				// Close mobile menu if open when returning
				if (isMobileMenuOpen) {
					toggleMobileMenu();
				}
			}
		});

		// Search functionality
		document.addEventListener('DOMContentLoaded', function() {
			// Handle search inputs
			const searchInputs = document.querySelectorAll('.search-input, .mobile-search input');
			searchInputs.forEach(input => {
				input.addEventListener('keypress', function(e) {
					if (e.key === 'Enter') {
						e.preventDefault();
						this.closest('form').submit();
					}
				});
			});

			// Add focus/blur effects to search
			const searchContainer = document.querySelector('.search-container');
			if (searchContainer) {
				const searchInput = searchContainer.querySelector('.search-input');
				if (searchInput) {
					searchInput.addEventListener('focus', function() {
						searchContainer.classList.add('focused');
					});
					
					searchInput.addEventListener('blur', function() {
						searchContainer.classList.remove('focused');
					});
				}
			}

			// Add keyboard navigation to action icons
			const actionIcons = document.querySelectorAll('.action-icon[tabindex="0"]');
			actionIcons.forEach(icon => {
				icon.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						this.click();
					}
				});
			});

			// Initialize cart display
			updateCartDisplay(0, 0);
			
			// Add smooth scroll behavior for mobile menu links
			const mobileLinks = document.querySelectorAll('.mobile-nav-menu a');
			mobileLinks.forEach(link => {
				link.addEventListener('click', function() {
					if (isMobileMenuOpen) {
						toggleMobileMenu();
					}
				});
			});
		});

		// Touch gestures for mobile menu (optional enhancement)
		let touchStartX = 0;
		let touchEndX = 0;

		document.addEventListener('touchstart', function(event) {
			touchStartX = event.changedTouches[0].screenX;
		});

		document.addEventListener('touchend', function(event) {
			touchEndX = event.changedTouches[0].screenX;
			handleSwipe();
		});

		function handleSwipe() {
			const swipeThreshold = 50;
			const swipeDistance = touchEndX - touchStartX;
			
			// Swipe right to open menu (from left edge)
			if (swipeDistance > swipeThreshold && touchStartX < 50 && !isMobileMenuOpen) {
				toggleMobileMenu();
			}
			
			// Swipe left to close menu
			if (swipeDistance < -swipeThreshold && isMobileMenuOpen) {
				toggleMobileMenu();
			}
		}
	</script>