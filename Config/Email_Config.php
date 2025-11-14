<?php
/**
 * CONFIGURACIÓN DE EMAIL SMTP
 * 
 * OPCIÓN 1: Gmail (RECOMENDADO para producción)
 * Pasos: https://myaccount.google.com/security → Verificación en 2 pasos → Contraseñas de aplicaciones
 * 
 * OPCIÓN 2: Mailtrap.io (SOLO DESARROLLO - emails no se envían realmente)
 * Crea cuenta gratis en https://mailtrap.io y usa las credenciales de tu inbox
 * 
 * OPCIÓN 3: SendGrid, Mailgun, etc. (Servicios profesionales)
 */

// ===== CONFIGURACIÓN ACTUAL: MAILTRAP (DESARROLLO) =====
// Los emails NO se envían realmente, pero puedes verlos en mailtrap.io
const SMTP_HOST = 'sandbox.smtp.mailtrap.io';
const SMTP_PORT = 2525;
const SMTP_SECURE = 'tls';
const SMTP_USERNAME = '62fdb0c99d9b5a'; // Usuario de Mailtrap
const SMTP_PASSWORD = '00abf75b39ff01'; // Password de Mailtrap

// ===== CONFIGURACIÓN GMAIL (PRODUCCIÓN) =====
// Descomenta estas líneas cuando tengas la App Password correcta
/*
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_SECURE = 'tls';
const SMTP_USERNAME = 'altovoltaje025@gmail.com';
const SMTP_PASSWORD = 'TU_APP_PASSWORD_AQUI'; // Genera uno nuevo en Gmail
*/
