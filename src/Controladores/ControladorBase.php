<?php
/**
 * Controlador Base - Clase base para todos los controladores
 * 
 * Esta clase proporciona funcionalidades comunes para todos los controladores
 * del sistema, incluyendo manejo de vistas, validaciones y respuestas.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Controladores;

use Servicios\ServicioAutenticacion;
use Exception;

/**
 * Clase abstracta ControladorBase - Base para todos los controladores
 */
abstract class ControladorBase {
    
    /**
     * Servicio de autenticación
     * @var ServicioAutenticacion
     */
    protected ServicioAutenticacion $auth;
    
    /**
     * Datos que se pasan a las vistas
     * @var array
     */
    protected array $datosVista = [];
    
    /**
     * Layout por defecto para las vistas
     * @var string
     */
    protected string $layout = 'principal';
    
    /**
     * Título de la página
     * @var string
     */
    protected string $tituloPagina = '';
    
    /**
     * Constructor base del controlador
     */
    public function __construct() {
        $this->auth = new ServicioAutenticacion();
        $this->inicializarControlador();
    }
    
    /**
     * Método de inicialización que pueden sobrescribir los controladores hijos
     */
    protected function inicializarControlador(): void {
        // Limpiar sesión si ha expirado
        $this->auth->limpiarSesionSiExpirada();
        
        // Establecer datos básicos para todas las vistas
        $this->establecerDatosBasicos();
    }
    
    /**
     * Renderiza una vista con los datos proporcionados
     * @param string $vista Nombre de la vista (sin extensión .php)
     * @param array $datos Datos adicionales para la vista
     * @param string|null $layoutPersonalizado Layout personalizado (opcional)
     */
    protected function renderizar(string $vista, array $datos = [], ?string $layoutPersonalizado = null): void {
        // Combinar datos
        $this->datosVista = array_merge($this->datosVista, $datos);
        
        // Usar layout personalizado si se proporciona
        $layout = $layoutPersonalizado ?? $this->layout;
        
        // Construir rutas de archivos
        $rutaVista = RUTA_VISTAS . $vista . '.php';
        $rutaLayout = RUTA_VISTAS . 'plantillas/' . $layout . '.php';
        
        // Verificar que los archivos existen
        if (!file_exists($rutaVista)) {
            throw new Exception("La vista '{$vista}' no existe");
        }
        
        if (!file_exists($rutaLayout)) {
            throw new Exception("El layout '{$layout}' no existe");
        }
        
        // Extraer variables para las vistas
        extract($this->datosVista);
        
        // Capturar contenido de la vista
        ob_start();
        include $rutaVista;
        $contenido = ob_get_clean();
        
        // Renderizar con layout
        include $rutaLayout;
    }
    
    /**
     * Renderiza una vista JSON
     * @param array $datos Datos a devolver en JSON
     * @param int $codigoEstado Código de estado HTTP
     */
    protected function renderizarJson(array $datos, int $codigoEstado = 200): void {
        http_response_code($codigoEstado);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Redirige a una URL específica
     * @param string $url URL de destino
     * @param int $codigoEstado Código de estado HTTP
     */
    protected function redirigir(string $url, int $codigoEstado = 302): void {
        header("Location: $url", true, $codigoEstado);
        exit;
    }
    
    /**
     * Verifica el token CSRF
     * @throws Exception Si el token CSRF no es válido
     */
    protected function verificarCSRF(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenEnviado = $_POST['csrf_token'] ?? '';
            $tokenSesion = $_SESSION['csrf_token'] ?? '';
            
            error_log("CSRF DEBUG: Token enviado longitud: " . strlen($tokenEnviado));
            error_log("CSRF DEBUG: Token sesión longitud: " . strlen($tokenSesion));
            error_log("CSRF DEBUG: Tokens iguales: " . ($tokenEnviado === $tokenSesion ? 'SÍ' : 'NO'));
            
            if (empty($tokenEnviado)) {
                error_log("CSRF DEBUG: Token enviado está vacío");
                throw new Exception("Token CSRF enviado está vacío");
            }
            
            if (empty($tokenSesion)) {
                error_log("CSRF DEBUG: Token de sesión está vacío");
                throw new Exception("Token CSRF de sesión está vacío");
            }
            
            if (!hash_equals($tokenSesion, $tokenEnviado)) {
                error_log("CSRF DEBUG: Los tokens no coinciden");
                throw new Exception("Token CSRF inválido - no coinciden");
            }
            
            error_log("CSRF DEBUG: Verificación exitosa");
        }
    }
    
    /**
     * Genera un token CSRF único
     * @return string Token CSRF generado
     */
    protected function generarTokenCSRF(): string {
        // Solo generar nuevo token si no existe uno en la sesión
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica si el usuario tiene un permiso específico
     * @param string $permiso Permiso a verificar
     * @throws Exception Si no tiene el permiso
     */
    protected function verificarPermiso(string $permiso): void {
        $this->auth->requerirPermiso($permiso);
    }
    
    /**
     * Verifica si el usuario está autenticado
     * @throws Exception Si no está autenticado
     */
    protected function requerirAutenticacion(): void {
        $this->auth->requerirAutenticacion();
    }
    
    /**
     * Verifica si el usuario está autenticado (alias)
     * @throws Exception Si no está autenticado
     */
    protected function verificarAutenticacion(): void {
        $this->requerirAutenticacion();
    }
    
    /**
     * Verifica si el usuario es administrador
     * @throws Exception Si no es administrador
     */
    protected function requerirAdmin(): void {
        $this->auth->requerirAdmin();
    }
    
    /**
     * Obtiene datos del formulario POST de forma segura
     * @param array $campos Campos esperados del formulario
     * @return array Datos sanitizados del formulario
     */
    protected function obtenerDatosFormulario(array $campos): array {
        $datos = [];
        
        foreach ($campos as $campo) {
            $valor = $_POST[$campo] ?? '';
            
            // Sanitizar valor
            if (is_string($valor)) {
                $valor = trim($valor);
                $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            }
            
            $datos[$campo] = $valor;
        }
        
        return $datos;
    }
    
    /**
     * Obtiene parámetros GET de forma segura
     * @param string $parametro Nombre del parámetro
     * @param mixed $valorPorDefecto Valor por defecto si no existe
     * @return mixed Valor del parámetro sanitizado
     */
    protected function obtenerParametroGET(string $parametro, $valorPorDefecto = null) {
        $valor = $_GET[$parametro] ?? $valorPorDefecto;
        
        if (is_string($valor)) {
            $valor = trim($valor);
            $valor = filter_var($valor, FILTER_SANITIZE_STRING);
        }
        
        return $valor;
    }
    
    /**
     * Valida que una cadena no esté vacía
     * @param string $valor Valor a validar
     * @param string $nombreCampo Nombre del campo para el mensaje de error
     * @throws Exception Si el valor está vacío
     */
    protected function validarRequerido(string $valor, string $nombreCampo): void {
        if (empty(trim($valor))) {
            throw new Exception("El campo {$nombreCampo} es requerido");
        }
    }
    
    /**
     * Valida que un email tenga formato correcto
     * @param string $email Email a validar
     * @throws Exception Si el email no es válido
     */
    protected function validarEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del email no es válido");
        }
    }
    
    /**
     * Valida que un número esté dentro de un rango
     * @param int $numero Número a validar
     * @param int $min Valor mínimo
     * @param int $max Valor máximo
     * @param string $nombreCampo Nombre del campo
     * @throws Exception Si el número está fuera del rango
     */
    protected function validarRango(int $numero, int $min, int $max, string $nombreCampo): void {
        if ($numero < $min || $numero > $max) {
            throw new Exception("El campo {$nombreCampo} debe estar entre {$min} y {$max}");
        }
    }
    
    /**
     * Obtiene parámetros de paginación desde GET
     * @return array Array con 'limite' y 'offset' para paginación
     */
    protected function obtenerParametrosPaginacion(): array {
        $pagina = max(1, (int)$this->obtenerParametroGET('pagina', 1));
        $limite = ELEMENTOS_POR_PAGINA;
        $offset = ($pagina - 1) * $limite;
        
        return [
            'pagina' => $pagina,
            'limite' => $limite,
            'offset' => $offset
        ];
    }
    
    /**
     * Genera HTML para paginación
     * @param int $totalElementos Total de elementos
     * @param int $elementosPorPagina Elementos por página
     * @param int $paginaActual Página actual
     * @param string $urlBase URL base para los enlaces
     * @return string HTML de paginación
     */
    protected function generarPaginacion(int $totalElementos, int $elementosPorPagina, int $paginaActual, string $urlBase): string {
        $totalPaginas = ceil($totalElementos / $elementosPorPagina);
        
        if ($totalPaginas <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Paginación"><ul class="pagination">';
        
        // Enlace anterior
        if ($paginaActual > 1) {
            $paginaAnterior = $paginaActual - 1;
            $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$urlBase}?pagina={$paginaAnterior}\">Anterior</a></li>";
        }
        
        // Enlaces de páginas
        $inicio = max(1, $paginaActual - 2);
        $fin = min($totalPaginas, $paginaActual + 2);
        
        for ($i = $inicio; $i <= $fin; $i++) {
            $claseActiva = ($i === $paginaActual) ? ' active' : '';
            $html .= "<li class=\"page-item{$claseActiva}\"><a class=\"page-link\" href=\"{$urlBase}?pagina={$i}\">{$i}</a></li>";
        }
        
        // Enlace siguiente
        if ($paginaActual < $totalPaginas) {
            $paginaSiguiente = $paginaActual + 1;
            $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$urlBase}?pagina={$paginaSiguiente}\">Siguiente</a></li>";
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Añade un mensaje flash a la sesión
     * @param string $tipo Tipo de mensaje (success, error, warning, info)
     * @param string $mensaje Mensaje a mostrar
     */
    protected function añadirMensajeFlash(string $tipo, string $mensaje): void {
        if (!isset($_SESSION['mensajes_flash'])) {
            $_SESSION['mensajes_flash'] = [];
        }
        
        $_SESSION['mensajes_flash'][] = [
            'tipo' => $tipo,
            'mensaje' => $mensaje
        ];
    }
    
    /**
     * Obtiene y limpia mensajes flash de la sesión
     * @return array Mensajes flash
     */
    protected function obtenerMensajesFlash(): array {
        $mensajes = $_SESSION['mensajes_flash'] ?? [];
        unset($_SESSION['mensajes_flash']);
        return $mensajes;
    }
    
    /**
     * Establece datos básicos que están disponibles en todas las vistas
     */
    private function establecerDatosBasicos(): void {
        $this->datosVista = [
            'titulo_pagina' => $this->tituloPagina,
            'usuario_actual' => $this->auth->obtenerUsuarioAutenticado(),
            'esta_autenticado' => $this->auth->estaAutenticado(),
            'es_admin' => $this->auth->puedeAccederAdmin(),
            'csrf_token' => $this->generarTokenCSRF(),
            'mensajes_flash' => $this->obtenerMensajesFlash(),
            'url_actual' => $_SERVER['REQUEST_URI'] ?? '/',
            'metodo_http' => $_SERVER['REQUEST_METHOD'] ?? 'GET'
        ];
    }
    
    /**
     * Maneja errores de forma consistente
     * @param Exception $e Excepción a manejar
     * @param string $vistaError Vista de error personalizada (opcional)
     */
    protected function manejarError(Exception $e, string $vistaError = 'error'): void {
        // Log del error
        error_log("Error en controlador: " . $e->getMessage() . " - Archivo: " . $e->getFile() . " - Línea: " . $e->getLine());
        
        // Si es una petición AJAX, devolver JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->renderizarJson([
                'exito' => false,
                'mensaje' => $e->getMessage(),
                'codigo_error' => $e->getCode()
            ], 500);
        }
        
        // Renderizar vista de error
        $this->renderizar($vistaError, [
            'mensaje_error' => $e->getMessage(),
            'codigo_error' => $e->getCode()
        ]);
    }
    
    /**
     * Establece el título de la página
     * @param string $titulo Título de la página
     */
    protected function establecerTitulo(string $titulo): void {
        $this->tituloPagina = $titulo;
        $this->datosVista['titulo_pagina'] = $titulo;
    }
    
    /**
     * Establece el layout a usar
     * @param string $layout Nombre del layout
     */
    protected function establecerLayout(string $layout): void {
        $this->layout = $layout;
    }
    
    /**
     * Muestra página de error 404
     * @param string $mensaje Mensaje personalizado (opcional)
     */
    protected function mostrarError404(string $mensaje = 'Página no encontrada'): void {
        // Establecer código de respuesta HTTP 404
        http_response_code(404);
        
        // Si es una petición AJAX, devolver JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->renderizarJson([
                'exito' => false,
                'mensaje' => $mensaje,
                'codigo_error' => 404
            ], 404);
            return;
        }
        
        // Renderizar página de error 404
        $this->renderizar('publicas/404', [
            'titulo_pagina' => 'Página no encontrada',
            'mensaje_error' => $mensaje
        ], 'publico');
    }
}
