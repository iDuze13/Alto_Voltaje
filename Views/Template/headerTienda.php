<!DOCTYPE html>
<html lang="es">
<head>
	<title>Alto Voltaje</title>
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
	
	/* User Dropdown Menu */
	.dropdown {
		position: relative;
	}
	
	.dropdown-toggle::after {
		margin-left: 8px;
	}
	
	.dropdown-menu {
		position: absolute;
		top: 100%;
		right: 0;
		z-index: 1000;
		display: none;
		margin-top: 8px;
		border-radius: 10px;
		box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		border: 1px solid #e0e0e0;
		padding: 8px 0;
		min-width: 200px;
		background-color: white;
		list-style: none;
	}
	
	.dropdown-menu.show {
		display: block;
	}
	
	.dropdown-item {
		display: block;
		width: 100%;
		padding: 10px 20px;
		font-size: 14px;
		color: #333;
		text-decoration: none;
		transition: all 0.2s ease;
		border: none;
		background: none;
		text-align: left;
	}
	
	.dropdown-item:hover {
		background-color: #f8f9fa;
		padding-left: 25px;
		color: #333;
	}
	
	.dropdown-item i {
		width: 20px;
		text-align: center;
	}
	
	.dropdown-divider {
		margin: 8px 0;
		border-top: 1px solid #e0e0e0;
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

	/* Mobile Menu Styles - Initial state is hidden */
	
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
		background: none;
		border: 2px solid #ddd;
		border-radius: 8px;
		font-size: 18px;
		color: #2c3e50;
		cursor: pointer;
		padding: 0;
		transition: all 0.3s ease;
		width: 42px;
		height: 42px;
		display: flex;
		align-items: center;
		justify-content: center;
		position: relative;
		overflow: hidden;
	}
	
	.mobile-menu-btn::before {
		content: '';
		position: absolute;
		inset: 0;
		background: #FFD700;
		opacity: 0;
		transition: opacity 0.3s ease;
		z-index: -1;
	}
	
	.mobile-menu-btn:hover::before {
		opacity: 0.1;
	}
	
	.mobile-menu-btn:active {
		transform: scale(0.95);
	}
	
	.mobile-menu-btn[aria-expanded="true"] {
		border-color: #FFD700;
		background: #fff8e1;
	}
	
	.mobile-menu-btn i {
		transition: transform 0.3s ease;
	}
	
	.mobile-menu-btn[aria-expanded="true"] i {
		transform: rotate(90deg);
	}
	
	.mobile-menu-btn:focus {
		outline: none;
		border-color: #FFD700;
		box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3);
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
		visibility: hidden;
		transform: translateY(-10px);
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		max-height: 0;
		overflow-y: auto;
		overflow-x: hidden;
	}
	
	.mobile-menu.active {
		opacity: 1;
		visibility: visible;
		transform: translateY(0);
		max-height: calc(100vh - 150px);
		padding: 15px 0;
	}
	
	/* Scrollbar personalizado para mobile menu */
	.mobile-menu::-webkit-scrollbar {
		width: 6px;
	}
	
	.mobile-menu::-webkit-scrollbar-track {
		background: #f1f1f1;
	}
	
	.mobile-menu::-webkit-scrollbar-thumb {
		background: #FFD700;
		border-radius: 3px;
	}
	
	.mobile-menu::-webkit-scrollbar-thumb:hover {
		background: #E6B800;
	}
	
	.mobile-nav-menu {
		list-style: none;
		padding: 0 10px;
		margin: 0;
	}
	
	.mobile-nav-menu li {
		margin-bottom: 2px;
	}
	
	/* Animación de entrada solo cuando el menú está activo */
	.mobile-menu.active .mobile-nav-menu li {
		animation: slideInMenu 0.3s ease forwards;
	}
	
	.mobile-menu.active .mobile-nav-menu li:nth-child(1) {
		animation-delay: 0.05s;
	}
	
	.mobile-menu.active .mobile-nav-menu li:nth-child(2) {
		animation-delay: 0.1s;
	}
	
	.mobile-menu.active .mobile-nav-menu li:nth-child(3) {
		animation-delay: 0.15s;
	}
	
	.mobile-menu.active .mobile-nav-menu li:nth-child(4) {
		animation-delay: 0.2s;
	}
	
	@keyframes slideInMenu {
		from {
			opacity: 0;
			transform: translateX(-20px);
		}
		to {
			opacity: 1;
			transform: translateX(0);
		}
	}
	
	.mobile-nav-menu li a {
		display: flex;
		align-items: center;
		padding: 14px 15px;
		color: #2c3e50;
		text-decoration: none;
		font-weight: 500;
		font-size: 15px;
		transition: all 0.2s ease;
		border-radius: 8px;
		position: relative;
	}
	
	.mobile-nav-menu li a:hover, 
	.mobile-nav-menu li a:active,
	.mobile-nav-menu li a:focus {
		color: #000;
		background: #fff8e1;
		padding-left: 20px;
	}
	
	.mobile-nav-menu li a i {
		margin-right: 12px;
		width: 24px;
		text-align: center;
		color: #FFD700;
		font-size: 16px;
		flex-shrink: 0;
	}
	
	/* Mobile Search */
	.mobile-search {
		padding: 0 10px 10px;
		border-bottom: 1px solid #f0f0f0;
	}
	
	/* Mobile User Actions */
	.mobile-user-actions {
		padding: 0 10px;
	}
	
	.mobile-user-actions .btn {
		font-size: 14px;
		padding: 10px 15px;
		border-radius: 8px;
		transition: all 0.2s ease;
	}
	
	.mobile-user-actions .btn:active {
		transform: scale(0.98);
	}
	
	.mobile-cart-summary {
		cursor: pointer;
		transition: all 0.2s ease;
		border: 1px solid #e0e0e0;
	}
	
	.mobile-cart-summary:hover,
	.mobile-cart-summary:active {
		background: #f8f9fa !important;
		transform: translateY(-2px);
		box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
	
	/* Responsive Mobile Menu Styles */
	
	/* Extra Small Devices (max 375px) */
	@media (max-width: 375px) {
		.mobile-menu.active {
			max-height: calc(100vh - 120px);
			padding: 10px 0;
		}
		
		.mobile-nav-menu {
			padding: 0 5px;
		}
		
		.mobile-nav-menu li a {
			padding: 12px 10px;
			font-size: 14px;
		}
		
		.mobile-nav-menu li a i {
			font-size: 14px;
			margin-right: 8px;
			width: 20px;
		}
		
		.mobile-user-actions {
			padding: 0 5px;
		}
		
		.mobile-user-actions .btn {
			font-size: 13px;
			padding: 8px 12px;
		}
		
		.mobile-cart-summary {
			padding: 10px !important;
			font-size: 13px;
		}
		
		.mobile-search {
			padding: 0 5px 10px;
		}
	}
	
	/* Small Mobile Devices (376px to 575px) */
	@media (min-width: 376px) and (max-width: 575px) {
		.mobile-menu.active {
			max-height: calc(100vh - 130px);
			padding: 12px 0;
		}
		
		.mobile-nav-menu li a {
			padding: 13px 12px;
			font-size: 15px;
		}
		
		.mobile-user-actions .btn {
			font-size: 14px;
		}
	}
	
	/* Tablets and Medium Devices (576px to 991px) */
	@media (min-width: 576px) and (max-width: 991px) {
		.mobile-menu.active {
			max-height: calc(100vh - 140px);
			padding: 15px 0;
		}
		
		.mobile-nav-menu {
			padding: 0 15px;
		}
		
		.mobile-nav-menu li a {
			padding: 15px 15px;
			font-size: 16px;
		}
		
		.mobile-nav-menu li a i {
			font-size: 18px;
			margin-right: 15px;
		}
		
		.mobile-user-actions {
			padding: 0 15px;
		}
		
		.mobile-user-actions .btn {
			font-size: 15px;
			padding: 12px 18px;
		}
		
		.mobile-cart-summary {
			padding: 15px !important;
		}
		
		.mobile-search {
			padding: 0 15px 15px;
		}
		
		/* Mostrar búsqueda en el header, ocultar en menú móvil */
		.mobile-menu .mobile-search {
			display: none;
		}
	}
	
	/* Landscape Mobile Devices */
	@media (max-height: 500px) and (orientation: landscape) {
		.mobile-menu.active {
			max-height: calc(100vh - 80px);
			padding: 8px 0;
		}
		
		.mobile-nav-menu li a {
			padding: 10px 12px;
			font-size: 14px;
		}
		
		.mobile-user-actions .btn {
			padding: 8px 12px;
			font-size: 13px;
		}
		
		.mobile-cart-summary {
			padding: 10px !important;
		}
		
		/* Ocultar elementos menos importantes en landscape */
		.mobile-search {
			display: none !important;
		}
		
		.mobile-user-actions .row {
			margin-bottom: 5px !important;
		}
	}
	
	/* Touch Device Optimization */
	@media (hover: none) and (pointer: coarse) {
		.mobile-nav-menu li a {
			padding: 16px 15px;
			min-height: 48px; /* Tamaño mínimo táctil recomendado */
		}
		
		.mobile-menu-btn {
			min-width: 48px;
			min-height: 48px;
		}
		
		.mobile-user-actions .btn {
			min-height: 44px;
		}
		
		/* Mejorar feedback táctil */
		.mobile-nav-menu li a:active {
			background: #ffe082;
			transform: scale(0.98);
		}
		
		.mobile-menu-btn:active {
			transform: scale(0.95);
			background: #f0f0f0;
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
	
	/* Custom Notification Styles */
	.notification-toast {
		position: fixed;
		top: 20px;
		right: 20px;
		z-index: 9999;
		max-width: 400px;
		min-width: 300px;
		background: white;
		border-radius: 12px;
		box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
		padding: 16px 20px;
		display: flex;
		align-items: center;
		gap: 12px;
		animation: slideInRight 0.4s ease-out;
		border-left: 4px solid #28a745;
	}
	
	.notification-toast.success {
		border-left-color: #28a745;
	}
	
	.notification-toast.error {
		border-left-color: #dc3545;
	}
	
	.notification-toast.info {
		border-left-color: #17a2b8;
	}
	
	.notification-toast.warning {
		border-left-color: #ffc107;
	}
	
	.notification-toast .notification-icon {
		font-size: 24px;
		flex-shrink: 0;
	}
	
	.notification-toast.success .notification-icon {
		color: #28a745;
	}
	
	.notification-toast.error .notification-icon {
		color: #dc3545;
	}
	
	.notification-toast .notification-content {
		flex: 1;
	}
	
	.notification-toast .notification-title {
		font-weight: 600;
		font-size: 15px;
		margin-bottom: 2px;
		color: #333;
	}
	
	.notification-toast .notification-message {
		font-size: 14px;
		color: #666;
	}
	
	.notification-toast .notification-close {
		background: none;
		border: none;
		color: #999;
		font-size: 20px;
		cursor: pointer;
		padding: 0;
		width: 24px;
		height: 24px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		transition: all 0.2s ease;
		flex-shrink: 0;
	}
	
	.notification-toast .notification-close:hover {
		background: #f0f0f0;
		color: #333;
	}
	
	@keyframes slideInRight {
		from {
			transform: translateX(400px);
			opacity: 0;
		}
		to {
			transform: translateX(0);
			opacity: 1;
		}
	}
	
	@keyframes slideOutRight {
		from {
			transform: translateX(0);
			opacity: 1;
		}
		to {
			transform: translateX(400px);
			opacity: 0;
		}
	}
	
	.notification-toast.removing {
		animation: slideOutRight 0.3s ease-out forwards;
	}
	
	/* Responsive notifications */
	@media (max-width: 576px) {
		.notification-toast {
			top: 10px;
			right: 10px;
			left: 10px;
			max-width: none;
			min-width: auto;
		}
		
		@keyframes slideInRight {
			from {
				transform: translateY(-100px);
				opacity: 0;
			}
			to {
				transform: translateY(0);
				opacity: 1;
			}
		}
		
		@keyframes slideOutRight {
			from {
				transform: translateY(0);
				opacity: 1;
			}
			to {
				transform: translateY(-100px);
				opacity: 0;
			}
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
					<div class="action-icon d-none d-lg-flex" onclick="toggleCompare()" title="Comparar productos">
						<i class="fa fa-exchange"></i>
					</div>
					
					<!-- Wishlist Icon -->
					<div class="action-icon d-none d-lg-flex" onclick="toggleWishlist()" title="Lista de deseos">
						<i class="fa fa-heart-o"></i>
					</div>
					
					<!-- Cart Button with Price -->
					<button class="cart-button d-none d-lg-flex" onclick="toggleCart()" title="Carrito de compras">
						<i class="fa fa-shopping-cart"></i>
						<span class="cart-price">$0,00</span>
						<span class="cart-count-main" style="display: none;">0</span>
					</button>

					<!-- Login Button / User Menu -->
					<?php if (!empty($_SESSION['admin']) || !empty($_SESSION['empleado']) || !empty($_SESSION['usuario'])): ?>
						<div class="dropdown d-none d-lg-block">
							<button class="login-button dropdown-toggle" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-user"></i>
								<?php 
									if (!empty($_SESSION['admin'])) echo $_SESSION['admin']['nombre'];
									elseif (!empty($_SESSION['empleado'])) echo $_SESSION['empleado']['nombre'];
									else echo $_SESSION['usuario']['nombre'];
								?>
							</button>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
								<?php if (!empty($_SESSION['usuario'])): ?>
									<li><a class="dropdown-item" href="<?= BASE_URL ?>/perfil"><i class="fa fa-user-circle me-2"></i>Mi Perfil</a></li>
									<li><a class="dropdown-item" href="<?= BASE_URL ?>/pedidos"><i class="fa fa-shopping-bag me-2"></i>Mis Pedidos</a></li>
									<li><hr class="dropdown-divider"></li>
								<?php endif; ?>
								<?php if (!empty($_SESSION['admin']) || !empty($_SESSION['empleado'])): ?>
									<li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard"><i class="fa fa-tachometer me-2"></i>Dashboard</a></li>
									<li><hr class="dropdown-divider"></li>
								<?php endif; ?>
								<li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout"><i class="fa fa-sign-out me-2"></i>Cerrar Sesión</a></li>
							</ul>
						</div>
					<?php else: ?>
						<a href="<?= BASE_URL ?>/auth/login" class="login-button d-none d-lg-flex">
							<i class="fa fa-sign-in"></i>
							Iniciar Sesión
						</a>
					<?php endif; ?>						<!-- Mobile Menu Button -->
						<button class="mobile-menu-btn d-lg-none" onclick="toggleMobileMenu()">
							<i class="fa fa-bars"></i>
						</button>
					</div>
				</div>
			</div>
			
			<!-- Mobile Menu -->
			<div id="mobile-menu" class="mobile-menu" role="navigation" aria-label="Menú móvil">
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
					<?php if (!empty($_SESSION['admin']) || !empty($_SESSION['empleado']) || !empty($_SESSION['usuario'])): ?>
						<!-- User Profile Links (only for logged in users) -->
						<?php if (!empty($_SESSION['usuario'])): ?>
							<div class="mb-2">
								<a href="<?= BASE_URL ?>/perfil" class="btn btn-outline-primary w-100">
									<i class="fa fa-user-circle me-1"></i>
									Mi Perfil
								</a>
							</div>
							<div class="mb-2">
								<a href="<?= BASE_URL ?>/pedidos" class="btn btn-outline-primary w-100">
									<i class="fa fa-shopping-bag me-1"></i>
									Mis Pedidos
								</a>
							</div>
						<?php endif; ?>
						
						<?php if (!empty($_SESSION['admin']) || !empty($_SESSION['empleado'])): ?>
							<div class="mb-2">
								<a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-info w-100">
									<i class="fa fa-tachometer me-1"></i>
									Dashboard
								</a>
							</div>
						<?php endif; ?>
						
						<!-- Logout Button -->
						<div class="mb-2">
							<a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-danger w-100">
								<i class="fa fa-sign-out me-1"></i>
								Cerrar Sesión
							</a>
						</div>
					<?php else: ?>
						<!-- Login Button -->
						<div class="mb-2">
							<a href="<?= BASE_URL ?>/auth/login" class="btn btn-warning w-100">
								<i class="fa fa-sign-in me-1"></i>
								Iniciar Sesión
							</a>
						</div>
					<?php endif; ?>						<!-- Quick Actions -->
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
		</div>
	</header>

	<script>
		// Global state
		let isMobileMenuOpen = false;
		let cartItems = [];
		let wishlistItems = [];

		// Mobile Menu Toggle
		function toggleMobileMenu() {
			console.log('toggleMobileMenu called');
			const mobileMenu = document.getElementById('mobile-menu');
			const menuBtn = document.querySelector('.mobile-menu-btn');
			
			console.log('mobileMenu:', mobileMenu);
			console.log('menuBtn:', menuBtn);
			
			if (!mobileMenu || !menuBtn) {
				console.error('Mobile menu elements not found');
				return;
			}
			
			isMobileMenuOpen = !isMobileMenuOpen;
			console.log('isMobileMenuOpen:', isMobileMenuOpen);
			
			if (isMobileMenuOpen) {
				// Remover la clase primero para resetear animaciones
				mobileMenu.classList.remove('active');
				
				// Forzar reflow para reiniciar las animaciones
				void mobileMenu.offsetHeight;
				
				// Mostrar menú con un pequeño delay
				requestAnimationFrame(() => {
					mobileMenu.classList.add('active');
					console.log('Menu opened, classes:', mobileMenu.className);
				});
				
				// Actualizar botón
				menuBtn.setAttribute('aria-expanded', 'true');
				menuBtn.innerHTML = '<i class="fa fa-times"></i>';
				
				// Prevenir scroll del body
				document.body.style.overflow = 'hidden';
			} else {
				// Ocultar menú
				mobileMenu.classList.remove('active');
				console.log('Menu closed, classes:', mobileMenu.className);
				
				// Actualizar botón
				menuBtn.setAttribute('aria-expanded', 'false');
				menuBtn.innerHTML = '<i class="fa fa-bars"></i>';
				
				// Restaurar scroll del body
				document.body.style.overflow = '';
			}
		}

		// Close mobile menu when clicking a navigation link
		document.addEventListener('DOMContentLoaded', function() {
			const mobileNavLinks = document.querySelectorAll('.mobile-nav-menu a');
			mobileNavLinks.forEach(link => {
				link.addEventListener('click', function() {
					if (isMobileMenuOpen) {
						toggleMobileMenu();
					}
				});
			});
			
			// Close mobile menu when clicking outside
			document.addEventListener('click', function(event) {
				const mobileMenu = document.getElementById('mobile-menu');
				const menuBtn = document.querySelector('.mobile-menu-btn');
				
				if (isMobileMenuOpen && mobileMenu && menuBtn) {
					// Check if click is outside menu and button
					if (!mobileMenu.contains(event.target) && !menuBtn.contains(event.target)) {
						toggleMobileMenu();
					}
				}
			});
		});

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

		// Show notification with custom styling
		function showNotification(message, type = 'success') {
			const notification = document.createElement('div');
			notification.className = `notification-toast ${type}`;
			
			const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : 'i';
			
			notification.innerHTML = `
				<div class="notification-icon">${icon}</div>
				<div class="notification-content">
					<div class="notification-title">${type === 'success' ? 'Éxito' : type === 'error' ? 'Error' : 'Información'}</div>
					<div class="notification-message">${message}</div>
				</div>
				<button class="notification-close" onclick="this.parentElement.remove()">×</button>
			`;
			
			document.body.appendChild(notification);
			
			// Auto remove after 5 seconds with animation
			setTimeout(() => {
				if (notification.parentNode) {
					notification.classList.add('removing');
					setTimeout(() => {
						if (notification.parentNode) {
							notification.remove();
						}
					}, 300);
				}
			}, 5000);
		}

		// Check for login/register success messages
		document.addEventListener('DOMContentLoaded', function() {
			<?php if (isset($_SESSION['login_success'])): ?>
				showNotification('<?= addslashes($_SESSION['login_success']) ?>', 'success');
				<?php unset($_SESSION['login_success']); ?>
			<?php endif; ?>
			
			<?php if (isset($_SESSION['register_success'])): ?>
				showNotification('<?= addslashes($_SESSION['register_success']) ?>', 'success');
				<?php unset($_SESSION['register_success']); ?>
			<?php endif; ?>
		});

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
		
		// Initialize user dropdown menu
		const userDropdownBtn = document.getElementById('userMenuDropdown');
		if (userDropdownBtn) {
			userDropdownBtn.addEventListener('click', function(e) {
				e.preventDefault();
				const dropdownMenu = this.nextElementSibling;
				const isExpanded = this.getAttribute('aria-expanded') === 'true';
				
				// Toggle dropdown
				if (isExpanded) {
					dropdownMenu.classList.remove('show');
					this.setAttribute('aria-expanded', 'false');
				} else {
					dropdownMenu.classList.add('show');
					this.setAttribute('aria-expanded', 'true');
				}
			});
			
			// Close dropdown when clicking outside
			document.addEventListener('click', function(event) {
				const dropdown = document.querySelector('.dropdown');
				if (dropdown && !dropdown.contains(event.target)) {
					const dropdownMenu = dropdown.querySelector('.dropdown-menu');
					const dropdownBtn = dropdown.querySelector('.dropdown-toggle');
					if (dropdownMenu && dropdownBtn) {
						dropdownMenu.classList.remove('show');
						dropdownBtn.setAttribute('aria-expanded', 'false');
					}
				}
			});
		}
	});		// Touch gestures for mobile menu (optional enhancement)
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