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
const CAT_SLIDER = "1,2,3,4,5,6,7,8";
const CAT_BANNER = "1,2,3,4,5,6";

//Configuración MercadoPago - MODO PRODUCCIÓN
const MP_ACCESS_TOKEN = "APP_USR-6979613042199572-103017-4903865f8ad1621bb9f0261f2b589562-185819159"; // Token de PRODUCCIÓN
const MP_ENVIRONMENT = "production"; // sandbox o production
const MP_PUBLIC_KEY = "APP_USR-aed31d69-1244-4a2f-9f2b-f1283e1bb727"; // Clave pública de PRODUCCIÓN
