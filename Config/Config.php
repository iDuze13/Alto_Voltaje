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

//Categorias para home
const CAT_SLIDER = "1,2,3";
const CAT_BANNER = "4,5,6";

// Configuración de MercadoPago Directo
const MP_ENVIRONMENT = "sandbox"; // "sandbox" o "production"

// Tokens de Sandbox (para desarrollo)
// Usa tus propios tokens de sandbox desde tu panel de MercadoPago
const MP_SANDBOX_ACCESS_TOKEN = "TEST-8438516161691264-101821-4e55cbdbc3c45bff8fc3a38bd80b5322-1334691313";
const MP_SANDBOX_PUBLIC_KEY = "TEST-b4d9d29d-fa06-4f06-a03e-0f6b9c0b3e57";

// Tokens de Producción 
const MP_ACCESS_TOKEN = "APP_USR-6979613042199572-103017-4903865f8ad1621bb9f0261f2b589562-185819159";
const MP_PUBLIC_KEY = "APP_USR-aed31d69-1244-4a2f-9f2b-f1283e1bb727";

// Client ID y Secret (para OAuth si es necesario)
const MP_CLIENT_ID = "6979613042199572";
const MP_CLIENT_SECRET = "EHdKnhGp50p0MaQoeCRbg5bnJzoDfoPe";

// URLs de retorno
const MP_SUCCESS_URL = BASE_URL . "/checkout/success";
const MP_FAILURE_URL = BASE_URL . "/checkout/failure";
const MP_PENDING_URL = BASE_URL . "/checkout/pending";
const MP_WEBHOOK_URL = BASE_URL . "/checkout/webhook";
?>
