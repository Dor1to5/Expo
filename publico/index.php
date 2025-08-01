<?php
/**
 * Punto de entrada principal de la aplicación
 * 
 * Este archivo maneja todas las peticiones del área pública
 * y las dirige al controlador correspondiente.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

// Incluir configuración principal
require_once __DIR__ . '/../config/configuracion.php';
require_once __DIR__ . '/../config/basedatos.php';

use Config\Rutas;

try {
    // Obtener ruta actual
    $rutaActual = Rutas::obtenerRutaActual();
    
    // Resolver la ruta
    $resolucion = Rutas::resolverRuta($rutaActual);
    
    if (!$resolucion) {
        // Página no encontrada
        http_response_code(404);
        include __DIR__ . '/../vistas/publicas/404.php';
        exit;
    }
    
    // Extraer información de la ruta
    $nombreControlador = $resolucion['controlador'];
    $metodo = $resolucion['metodo'];
    $parametros = $resolucion['parametros'] ?? [];
    
    // Construir nombre completo de la clase controlador
    $claseControlador = "Controladores\\{$nombreControlador}";
    
    // Verificar que la clase existe
    if (!class_exists($claseControlador)) {
        throw new Exception("El controlador {$claseControlador} no existe");
    }
    
    // Crear instancia del controlador
    $controlador = new $claseControlador();
    
    // Verificar que el método existe
    if (!method_exists($controlador, $metodo)) {
        throw new Exception("El método {$metodo} no existe en {$claseControlador}");
    }
    
    // Ejecutar el método del controlador
    if (!empty($parametros)) {
        // Pasar parámetros como argumentos
        call_user_func_array([$controlador, $metodo], array_values($parametros));
    } else {
        // Sin parámetros
        $controlador->$metodo();
    }
    
} catch (Exception $e) {
    // Manejar errores de forma general
    error_log("Error en index.php: " . $e->getMessage() . " - Archivo: " . $e->getFile() . " - Línea: " . $e->getLine());
    
    // Mostrar página de error
    http_response_code(500);
    
    if (MODO_DEBUG) {
        // En modo debug, mostrar detalles del error
        echo "<h1>Error del Sistema</h1>";
        echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
        
        if (method_exists($e, 'getTraceAsString')) {
            echo "<h2>Trace:</h2>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    } else {
        // En producción, mostrar página de error genérica
        include __DIR__ . '/../vistas/publicas/error.php';
    }
}
