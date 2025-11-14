<?php

//define("BASE_URL", "http://localhost/AltoVoltaje");
const BASE_URL = "http://localhost/AltoVoltaje";

//Zona horaria
//date_default_timezone_set('America/Argentina/Buenos_Aires');

// Datos de conexion a la base de datos
const DB_HOST = "localhost";

const DB_NAME = "mydb";

const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "utf8mb4";
//Deliminadores decimal y millar Ej. 24,1989.00
const SPD = ".";
const SPM = ",";

//Simbolo de moneda
const SMONEY = "$";

//Datos envio de correo
const NOMBRE_REMITENTE = "Alto Voltaje";
const EMAIL_REMITENTE = "altovoltaje025@gmail.com";
const NOMBRE_EMPESA = "Alto Voltaje";
const WEB_EMPRESA = "www.altovoltaje.site";

//Para envío de correo
const ENVIRONMENT = 1; // Local: 0, Produccón: 1;

//Datos Empresa
const DIRECCION = "Av. Arturo Frondizi 4566, Formosa Capital, Argentina";
const TELEMPRESA = "+(54) 3704-804704";
const WHATSAPP = "+(54) 3704-804704";
const EMAIL_EMPRESA = "altovoltaje025@gmail.com";
const EMAIL_PEDIDOS = "altovoltaje025@gmail.com"; 
const EMAIL_SUSCRIPCION = "altovoltaje025@gmail.com";
const EMAIL_CONTACTO = "altovoltaje025@gmail.com";

//Categorias para home
const CAT_SLIDER = "1,2,3,4,5,6,7,8";
const CAT_BANNER = "1,2,3,4,5,6";

//Modulos
const MDASHBOARD = 1;
const MUSUARIOS = 2;
const MCLIENTES = 3;
const MPRODUCTOS = 4;
const MPEDIDOS = 5;
const MCATEGORIAS = 6;
const MSUSCRIPTORES = 7;
const MDCONTACTOS = 8;
const MDPAGINAS = 9;

//Roles
const RADMINISTRADOR = 1;
const RSUPERVISOR = 2;
const RCLIENTES = 3;
const RUSUARIOS = 4;

//Configuración SMTP
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_SECURE = 'tls';
const SMTP_USERNAME = 'proyectos.sof.d@gmail.com';
const SMTP_PASSWORD = 'azwmqievnqxhxuqb'; // TU NUEVA APP PASSWORD AQUÍ

//Configuración MercadoPago - Cargar desde mercadopago_config.php
require_once __DIR__ . '/mercadopago_config.php';
define('MP_ACCESS_TOKEN', getMercadoPagoAccessToken());
define('MP_ENVIRONMENT', esModoTest() ? 'sandbox' : 'production');
define('MP_PUBLIC_KEY', getMercadoPagoPublicKey());
