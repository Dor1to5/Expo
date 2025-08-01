<?php
/**
 * Configuración general del sistema
 * 
 * Este archivo contiene todas las configuraciones globales del sistema
 * de gestión, incluyendo rutas, seguridad y configuraciones de aplicación.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

// Configuración de la aplicación
define('NOMBRE_APLICACION', 'Sistema de Gestión');
define('VERSION_APLICACION', '1.0.0');
define('DESCRIPCION_APLICACION', 'Sistema completo de gestión con área pública y panel de administración');

// Configuración de rutas
define('RUTA_BASE', '/');
define('RUTA_PUBLICA', __DIR__ . '/../publico/');
define('RUTA_VISTAS', __DIR__ . '/../vistas/');
define('RUTA_SRC', __DIR__ . '/../src/');
define('RUTA_CONFIG', __DIR__ . '/');

// Configuración de URL
define('URL_BASE', 'http://localhost/');
define('URL_PUBLICA', URL_BASE . 'publico/');
define('URL_ADMIN', URL_BASE . 'admin/');

// Configuración de sesiones
define('NOMBRE_SESION', 'SISTEMA_GESTION_SESION');
define('TIEMPO_EXPIRACION_SESION', 3600); // 1 hora en segundos
define('TIEMPO_EXPIRACION_RECORDAR', 604800); // 1 semana en segundos

// Configuración de seguridad
define('CLAVE_CIFRADO', 'mi_clave_secreta_muy_segura_2025');
define('SALT_CONTRASENA', 'mi_salt_personalizado_para_contrasenas');

// Configuración de paginación
define('ELEMENTOS_POR_PAGINA', 10);
define('ELEMENTOS_POR_PAGINA_ADMIN', 20);

// Configuración de subida de archivos
define('TAMANO_MAXIMO_ARCHIVO', 5242880); // 5MB en bytes
define('TIPOS_ARCHIVO_PERMITIDOS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Configuración de email (para futuras implementaciones)
define('EMAIL_SERVIDOR', 'smtp.gmail.com');
define('EMAIL_PUERTO', 587);
define('EMAIL_USUARIO', '');
define('EMAIL_CONTRASENA', '');
define('EMAIL_REMITENTE', 'noreply@sistemagestion.com');

// Modo de depuración
define('MODO_DEBUG', true);
define('MOSTRAR_ERRORES', true);

// Configuración de zona horaria
date_default_timezone_set('Europe/Madrid');

// Configuración de logs
define('RUTA_LOGS', __DIR__ . '/../logs/');
define('NIVEL_LOG', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Autoload de clases
spl_autoload_register(function ($nombreClase) {
    // Convertir namespace a ruta de archivo
    $archivo = str_replace('\\', DIRECTORY_SEPARATOR, $nombreClase);
    $rutaCompleta = RUTA_SRC . $archivo . '.php';
    
    if (file_exists($rutaCompleta)) {
        require_once $rutaCompleta;
    }
});

// Inicializar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_name(NOMBRE_SESION);
    session_start();
}

// Configurar manejo de errores si está en modo debug
if (MODO_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', MOSTRAR_ERRORES ? 1 : 0);
}
