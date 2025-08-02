<?php
/**
 * Sistema de enrutado del aplicación
 * 
 * Este archivo gestiona todas las rutas del sistema, tanto para el área
 * pública como para el panel de administración.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Config;

/**
 * Clase para gestionar el sistema de rutas
 */
class Rutas {
    
    /**
     * Rutas públicas del sistema
     * @var array
     */
    private static array $rutasPublicas = [
        '/' => ['controlador' => 'InicioControlador', 'metodo' => 'mostrarInicio'],
        '/inicio' => ['controlador' => 'InicioControlador', 'metodo' => 'mostrarInicio'],
        '/acerca-de' => ['controlador' => 'InicioControlador', 'metodo' => 'mostrarAcercaDe'],
        '/blog' => ['controlador' => 'BlogControlador', 'metodo' => 'listarArticulos'],
        '/blog/{slug}' => ['controlador' => 'BlogControlador', 'metodo' => 'mostrarArticulo'],
        '/blog/categoria/{categoria}' => ['controlador' => 'BlogControlador', 'metodo' => 'mostrarPorCategoria'],
        '/blog/buscar' => ['controlador' => 'BlogControlador', 'metodo' => 'buscar'],
        '/exposiciones' => ['controlador' => 'ExposicionControlador', 'metodo' => 'listarPublicas'],
        '/exposicion/{slug}' => ['controlador' => 'ExposicionControlador', 'metodo' => 'mostrarDetalles'],
        '/contacto' => ['controlador' => 'ContactoControlador', 'metodo' => 'mostrarFormulario'],
        '/contacto/enviar' => ['controlador' => 'ContactoControlador', 'metodo' => 'procesarFormulario'],
        '/login' => ['controlador' => 'AutenticacionControlador', 'metodo' => 'mostrarLogin'],
        '/login/procesar' => ['controlador' => 'AutenticacionControlador', 'metodo' => 'procesarLogin'],
        '/registro' => ['controlador' => 'AutenticacionControlador', 'metodo' => 'mostrarRegistro'],
        '/registro/procesar' => ['controlador' => 'AutenticacionControlador', 'metodo' => 'procesarRegistro'],
        '/logout' => ['controlador' => 'AutenticacionControlador', 'metodo' => 'cerrarSesion'],
    ];
    
    /**
     * Rutas del panel de administración
     * @var array
     */
    private static array $rutasAdmin = [
        '/admin' => ['controlador' => 'AdminControlador', 'metodo' => 'mostrarDashboard'],
        '/admin/dashboard' => ['controlador' => 'AdminControlador', 'metodo' => 'mostrarDashboard'],
        
        // Gestión de usuarios
        '/admin/usuarios' => ['controlador' => 'AdminUsuarioControlador', 'metodo' => 'listar'],
        '/admin/usuarios/crear' => ['controlador' => 'AdminUsuarioControlador', 'metodo' => 'mostrarCrear'],
        '/admin/usuarios/crear/procesar' => ['controlador' => 'AdminUsuarioControlador', 'metodo' => 'procesarCrear'],
        '/admin/usuarios/{id}' => ['controlador' => 'AdminUsuarioControlador', 'metodo' => 'mostrarDetalles'],
        '/admin/usuarios/{id}/editar' => ['controlador' => 'AdminUsuarioControlador', 'metodo' => 'procesarEditar'],
        '/admin/usuarios/{id}/eliminar' => ['controlador' => 'AdminUsuarioControlador', 'metodo' => 'eliminar'],
        
        // Gestión de roles
        '/admin/roles' => ['controlador' => 'AdminRolControlador', 'metodo' => 'listar'],
        '/admin/roles/crear' => ['controlador' => 'AdminRolControlador', 'metodo' => 'mostrarCrear'],
        '/admin/roles/crear/procesar' => ['controlador' => 'AdminRolControlador', 'metodo' => 'procesarCrear'],
        '/admin/roles/{id}' => ['controlador' => 'AdminRolControlador', 'metodo' => 'mostrarDetalles'],
        '/admin/roles/{id}/editar' => ['controlador' => 'AdminRolControlador', 'metodo' => 'procesarEditar'],
        '/admin/roles/{id}/eliminar' => ['controlador' => 'AdminRolControlador', 'metodo' => 'eliminar'],
        
        // Gestión de exposiciones
        '/admin/exposiciones' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'listar'],
        '/admin/exposiciones/crear' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'mostrarCrear'],
        '/admin/exposiciones/crear/procesar' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'procesarCrear'],
        '/admin/exposiciones/{id}' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'mostrarDetalles'],
        '/admin/exposiciones/{id}/editar' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'mostrarEditar'],
        '/admin/exposiciones/{id}/editar/procesar' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'procesarEditar'],
        '/admin/exposiciones/{id}/eliminar' => ['controlador' => 'AdminExposicionControlador', 'metodo' => 'eliminar'],
        
        // Gestión de artículos
        '/admin/articulos' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'listar'],
        '/admin/articulos/crear' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'mostrarCrear'],
        '/admin/articulos/crear/procesar' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'procesarCrear'],
        '/admin/articulos/{id}' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'mostrarDetalles'],
        '/admin/articulos/{id}/editar' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'mostrarEditar'],
        '/admin/articulos/{id}/editar/procesar' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'procesarEditar'],
        '/admin/articulos/{id}/eliminar' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'eliminar'],
        '/admin/articulos/{id}/preview' => ['controlador' => 'AdminArticuloControlador', 'metodo' => 'previsualizarArticulo'],
        
        // Configuración del sistema
        '/admin/configuracion' => ['controlador' => 'AdminConfiguracionControlador', 'metodo' => 'mostrar'],
        '/admin/configuracion/actualizar' => ['controlador' => 'AdminConfiguracionControlador', 'metodo' => 'actualizar'],
    ];
    
    /**
     * Obtiene la ruta actual desde la URL
     * @return string Ruta limpia
     */
    public static function obtenerRutaActual(): string {
        // Si se está usando el parámetro 'ruta' (modo fallback)
        if (isset($_GET['ruta']) && !empty($_GET['ruta'])) {
            $ruta = '/' . trim($_GET['ruta'], '/');
            return $ruta === '/' ? '/' : $ruta;
        }
        
        // Obtener ruta desde REQUEST_URI (rutas limpias)
        $ruta = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remover el directorio base si existe
        $directorioBase = dirname($_SERVER['SCRIPT_NAME']);
        if ($directorioBase !== '/' && str_starts_with($ruta, $directorioBase)) {
            $ruta = substr($ruta, strlen($directorioBase));
        }
        
        // Remover parámetros GET
        $posicionInterrogacion = strpos($ruta, '?');
        if ($posicionInterrogacion !== false) {
            $ruta = substr($ruta, 0, $posicionInterrogacion);
        }
        
        // Asegurar que la ruta comience con /
        if (!str_starts_with($ruta, '/')) {
            $ruta = '/' . $ruta;
        }
        
        return rtrim($ruta, '/') ?: '/';
    }
    
    /**
     * Resuelve una ruta y devuelve el controlador y método correspondiente
     * @param string $ruta Ruta a resolver
     * @return array|null Array con controlador y método, o null si no se encuentra
     */
    public static function resolverRuta(string $ruta): ?array {
        // Primero buscar en rutas exactas
        if (isset(self::$rutasPublicas[$ruta])) {
            return self::$rutasPublicas[$ruta];
        }
        
        if (isset(self::$rutasAdmin[$ruta])) {
            return self::$rutasAdmin[$ruta];
        }
        
        // Buscar rutas con parámetros
        return self::buscarRutaConParametros($ruta);
    }
    
    /**
     * Busca rutas que contengan parámetros dinámicos
     * @param string $ruta Ruta a buscar
     * @return array|null Array con controlador, método y parámetros
     */
    private static function buscarRutaConParametros(string $ruta): ?array {
        $todasLasRutas = array_merge(self::$rutasPublicas, self::$rutasAdmin);
        
        foreach ($todasLasRutas as $patron => $configuracion) {
            if (str_contains($patron, '{')) {
                $parametros = self::extraerParametros($patron, $ruta);
                if ($parametros !== null) {
                    return array_merge($configuracion, ['parametros' => $parametros]);
                }
            }
        }
        
        return null;
    }
    
    /**
     * Extrae parámetros de una ruta con patrones
     * @param string $patron Patrón de la ruta con parámetros
     * @param string $ruta Ruta actual
     * @return array|null Parámetros extraídos o null si no coincide
     */
    private static function extraerParametros(string $patron, string $ruta): ?array {
        // Convertir patrón a expresión regular
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $patron);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $ruta, $coincidencias)) {
            array_shift($coincidencias); // Remover la coincidencia completa
            
            // Extraer nombres de parámetros del patrón
            preg_match_all('/\{([^}]+)\}/', $patron, $nombresParametros);
            $nombres = $nombresParametros[1];
            
            // Combinar nombres con valores
            return array_combine($nombres, $coincidencias);
        }
        
        return null;
    }
    
    /**
     * Genera una URL para una ruta específica
     * @param string $nombreRuta Nombre de la ruta
     * @param array $parametros Parámetros para la ruta
     * @return string URL generada
     */
    public static function generarUrl(string $nombreRuta, array $parametros = []): string {
        $url = URL_BASE . ltrim($nombreRuta, '/');
        
        // Reemplazar parámetros en la URL
        foreach ($parametros as $clave => $valor) {
            $url = str_replace('{' . $clave . '}', $valor, $url);
        }
        
        return $url;
    }
    
    /**
     * Verifica si una ruta requiere autenticación de administrador
     * @param string $ruta Ruta a verificar
     * @return bool True si requiere autenticación de admin
     */
    public static function requiereAuthAdmin(string $ruta): bool {
        return str_starts_with($ruta, '/admin');
    }
    
    /**
     * Redirecciona a una ruta específica
     * @param string $ruta Ruta de destino
     * @param int $codigoEstado Código de estado HTTP
     */
    public static function redirigir(string $ruta, int $codigoEstado = 302): void {
        $url = self::generarUrl($ruta);
        header("Location: $url", true, $codigoEstado);
        exit;
    }
}
