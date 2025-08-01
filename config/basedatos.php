<?php
/**
 * Configuración de la base de datos
 * 
 * Este archivo contiene todos los parámetros necesarios para la conexión
 * con la base de datos MySQL del sistema de gestión.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

// Configuración de la base de datos
define('BD_SERVIDOR', 'localhost');
define('BD_NOMBRE', 'sistema_gestion');
define('BD_USUARIO', 'root');
define('BD_CONTRASENA', '');
define('BD_CHARSET', 'utf8mb4');

// Configuración de PDO
define('BD_OPCIONES', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . BD_CHARSET
]);

/**
 * Cadena de conexión DSN para PDO
 */
define('BD_DSN', 'mysql:host=' . BD_SERVIDOR . ';dbname=' . BD_NOMBRE . ';charset=' . BD_CHARSET);
