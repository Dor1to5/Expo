<?php
/**
 * Servicio de Autenticación - Gestión de sesiones y autenticación de usuarios
 * 
 * Esta clase proporciona todos los métodos necesarios para la autenticación,
 * gestión de sesiones y verificación de permisos de usuarios.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Servicios;

use Modelos\Usuario;
use Modelos\Rol;
use Exception;

/**
 * Clase ServicioAutenticacion - Maneja autenticación y sesiones
 */
class ServicioAutenticacion {
    
    /**
     * Modelo de usuario
     * @var Usuario
     */
    private Usuario $modeloUsuario;
    
    /**
     * Modelo de rol
     * @var Rol
     */
    private Rol $modeloRol;
    
    /**
     * Nombre de la clave de sesión para usuario
     * @var string
     */
    private const CLAVE_SESION_USUARIO = 'usuario_autenticado';
    
    /**
     * Nombre de la clave de sesión para permisos
     * @var string
     */
    private const CLAVE_SESION_PERMISOS = 'permisos_usuario';
    
    /**
     * Constructor del servicio
     */
    public function __construct() {
        $this->modeloUsuario = new Usuario();
        $this->modeloRol = new Rol();
        
        // Asegurar que la sesión esté iniciada
        $this->iniciarSesionSiNoExiste();
    }
    
    /**
     * Autentica un usuario con sus credenciales
     * @param string $nombreUsuario Nombre de usuario o email
     * @param string $contrasena Contraseña en texto plano
     * @param bool $recordar Si debe recordar la sesión
     * @return bool True si la autenticación fue exitosa
     * @throws Exception Si hay error en la autenticación
     */
    public function autenticar(string $nombreUsuario, string $contrasena, bool $recordar = false): bool {
        try {
            // Limpiar sesión anterior
            $this->cerrarSesion();
            
            // Intentar autenticar usuario
            $usuario = $this->modeloUsuario->autenticar($nombreUsuario, $contrasena);
            
            if (!$usuario) {
                return false;
            }
            
            // Verificar que el usuario esté activo
            if (!$usuario['activo']) {
                throw new Exception("La cuenta de usuario está desactivada");
            }
            
            // Establecer datos de sesión
            $this->establecerSesionUsuario($usuario);
            
            // Configurar recordar sesión si se solicita
            if ($recordar) {
                $this->configurarRecordarSesion($usuario['id']);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Registra un nuevo usuario en el sistema
     * @param array $datosUsuario Datos del nuevo usuario
     * @return int ID del usuario registrado
     * @throws Exception Si hay error en el registro
     */
    public function registrar(array $datosUsuario): int {
        try {
            // Validar datos básicos
            $this->validarDatosRegistro($datosUsuario);
            
            // Registrar usuario con rol básico por defecto
            $datosUsuario['rol_id'] = $datosUsuario['rol_id'] ?? 2; // Usuario básico
            
            return $this->modeloUsuario->registrar($datosUsuario);
            
        } catch (Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verifica si un usuario está autenticado
     * @return bool True si está autenticado
     */
    public function estaAutenticado(): bool {
        return isset($_SESSION[self::CLAVE_SESION_USUARIO]) && !empty($_SESSION[self::CLAVE_SESION_USUARIO]);
    }
    
    /**
     * Obtiene los datos del usuario autenticado
     * @return array|null Datos del usuario o null si no está autenticado
     */
    public function obtenerUsuarioAutenticado(): ?array {
        if (!$this->estaAutenticado()) {
            return null;
        }
        
        return $_SESSION[self::CLAVE_SESION_USUARIO];
    }
    
    /**
     * Obtiene el ID del usuario autenticado
     * @return int|null ID del usuario o null si no está autenticado
     */
    public function obtenerIdUsuarioAutenticado(): ?int {
        $usuario = $this->obtenerUsuarioAutenticado();
        return $usuario ? (int)$usuario['id'] : null;
    }
    
    /**
     * Verifica si el usuario autenticado tiene un permiso específico
     * @param string $permiso Nombre del permiso a verificar
     * @return bool True si tiene el permiso
     */
    public function tienePermiso(string $permiso): bool {
        if (!$this->estaAutenticado()) {
            return false;
        }
        
        $usuario = $this->obtenerUsuarioAutenticado();
        return $this->modeloUsuario->tienePermiso($usuario['id'], $permiso);
    }
    
    /**
     * Verifica si el usuario tiene rol de administrador
     * @return bool True si es administrador
     */
    public function esAdministrador(): bool {
        if (!$this->estaAutenticado()) {
            return false;
        }
        
        $usuario = $this->obtenerUsuarioAutenticado();
        return $this->tienePermiso('sistema.acceso_completo') || 
               in_array($usuario['nombre_rol'], ['Administrador', 'Super Administrador']);
    }
    
    /**
     * Verifica si el usuario puede acceder al panel de administración
     * @return bool True si puede acceder
     */
    public function puedeAccederAdmin(): bool {
        return $this->tienePermiso('dashboard.ver') || $this->esAdministrador();
    }
    
    /**
     * Cambia la contraseña del usuario autenticado
     * @param string $contrasenaActual Contraseña actual
     * @param string $contrasenaNueva Nueva contraseña
     * @return bool True si se cambió correctamente
     * @throws Exception Si hay error en el cambio
     */
    public function cambiarContrasena(string $contrasenaActual, string $contrasenaNueva): bool {
        if (!$this->estaAutenticado()) {
            throw new Exception("Debe estar autenticado para cambiar la contraseña");
        }
        
        $idUsuario = $this->obtenerIdUsuarioAutenticado();
        return $this->modeloUsuario->cambiarContrasena($idUsuario, $contrasenaActual, $contrasenaNueva);
    }
    
    /**
     * Actualiza los datos del perfil del usuario autenticado
     * @param array $datos Nuevos datos del perfil
     * @return bool True si se actualizó correctamente
     * @throws Exception Si hay error en la actualización
     */
    public function actualizarPerfil(array $datos): bool {
        if (!$this->estaAutenticado()) {
            throw new Exception("Debe estar autenticado para actualizar el perfil");
        }
        
        $idUsuario = $this->obtenerIdUsuarioAutenticado();
        
        // Filtrar campos que el usuario puede actualizar
        $camposPermitidos = ['nombre_completo', 'telefono', 'email'];
        $datosPermitidos = array_intersect_key($datos, array_flip($camposPermitidos));
        
        if (empty($datosPermitidos)) {
            throw new Exception("No hay datos válidos para actualizar");
        }
        
        $resultado = $this->modeloUsuario->actualizar($idUsuario, $datosPermitidos);
        
        // Actualizar datos en sesión si se actualizó correctamente
        if ($resultado) {
            $usuarioActualizado = $this->modeloUsuario->obtenerPorId($idUsuario);
            if ($usuarioActualizado) {
                $this->establecerSesionUsuario($usuarioActualizado);
            }
        }
        
        return $resultado;
    }
    
    /**
     * Cierra la sesión del usuario actual
     */
    public function cerrarSesion(): void {
        // Limpiar datos específicos de usuario
        unset($_SESSION[self::CLAVE_SESION_USUARIO]);
        unset($_SESSION[self::CLAVE_SESION_PERMISOS]);
        
        // Limpiar cookie de recordar si existe
        if (isset($_COOKIE['recordar_usuario'])) {
            setcookie('recordar_usuario', '', time() - 3600, '/');
        }
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
    }
    
    /**
     * Destruye completamente la sesión
     */
    public function destruirSesion(): void {
        session_destroy();
        session_start();
    }
    
    /**
     * Requiere autenticación, redirige al login si no está autenticado
     * @param string $urlRedireccion URL a la que redirigir después del login
     */
    public function requerirAutenticacion(string $urlRedireccion = ''): void {
        if (!$this->estaAutenticado()) {
            $url = '/login';
            if (!empty($urlRedireccion)) {
                $url .= '?redirect=' . urlencode($urlRedireccion);
            }
            
            header("Location: $url");
            exit;
        }
    }
    
    /**
     * Requiere permisos específicos, muestra error si no los tiene
     * @param string $permiso Permiso requerido
     * @param string $mensajeError Mensaje de error personalizado
     * @throws Exception Si no tiene el permiso
     */
    public function requerirPermiso(string $permiso, string $mensajeError = ''): void {
        $this->requerirAutenticacion();
        
        if (!$this->tienePermiso($permiso)) {
            $mensaje = $mensajeError ?: "No tienes permisos para realizar esta acción";
            throw new Exception($mensaje);
        }
    }
    
    /**
     * Requiere acceso de administrador
     * @throws Exception Si no es administrador
     */
    public function requerirAdmin(): void {
        $this->requerirAutenticacion();
        
        if (!$this->puedeAccederAdmin()) {
            throw new Exception("Acceso denegado. Se requieren permisos de administrador");
        }
    }
    
    /**
     * Verifica y renueva la sesión basada en recordar usuario
     * @return bool True si se renovó la sesión
     */
    public function verificarSesionRecordada(): bool {
        if ($this->estaAutenticado()) {
            return true;
        }
        
        if (!isset($_COOKIE['recordar_usuario'])) {
            return false;
        }
        
        try {
            $token = $_COOKIE['recordar_usuario'];
            
            // Aquí iría la lógica para verificar el token de recordar
            // Por simplicidad, se omite la implementación completa del token
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error al verificar sesión recordada: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Establece los datos del usuario en la sesión
     * @param array $usuario Datos del usuario autenticado
     */
    private function establecerSesionUsuario(array $usuario): void {
        $_SESSION[self::CLAVE_SESION_USUARIO] = $usuario;
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
    }
    
    /**
     * Configura la funcionalidad de recordar sesión
     * @param int $idUsuario ID del usuario
     */
    private function configurarRecordarSesion(int $idUsuario): void {
        // Generar token único para recordar
        $token = bin2hex(random_bytes(32));
        
        // Configurar cookie (válida por una semana)
        setcookie(
            'recordar_usuario',
            $token,
            time() + TIEMPO_EXPIRACION_RECORDAR,
            '/',
            '',
            true, // Solo HTTPS
            true  // Solo HTTP (no accesible desde JavaScript)
        );
        
        // Aquí se guardaría el token en la base de datos asociado al usuario
        // Por simplicidad se omite esta implementación
    }
    
    /**
     * Valida los datos de registro de usuario
     * @param array $datos Datos a validar
     * @throws Exception Si los datos no son válidos
     */
    private function validarDatosRegistro(array $datos): void {
        $camposRequeridos = ['nombre_usuario', 'email', 'contrasena', 'nombre_completo'];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($datos[$campo])) {
                throw new Exception("El campo {$campo} es requerido");
            }
        }
        
        // Validar formato de email
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del email no es válido");
        }
        
        // Validar longitud de contraseña
        if (strlen($datos['contrasena']) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres");
        }
        
        // Verificar que el nombre de usuario no exista
        if ($this->modeloUsuario->obtenerPorNombreUsuario($datos['nombre_usuario'])) {
            throw new Exception("El nombre de usuario ya está en uso");
        }
        
        // Verificar que el email no exista
        if ($this->modeloUsuario->obtenerPorEmail($datos['email'])) {
            throw new Exception("El email ya está registrado");
        }
    }
    
    /**
     * Inicia la sesión si no existe una activa
     */
    private function iniciarSesionSiNoExiste(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(NOMBRE_SESION);
            session_start();
        }
    }
    
    /**
     * Obtiene información de la sesión actual
     * @return array Información de la sesión
     */
    public function obtenerInfoSesion(): array {
        return [
            'id_sesion' => session_id(),
            'nombre_sesion' => session_name(),
            'usuario_autenticado' => $this->estaAutenticado(),
            'tiempo_inactividad' => time() - ($_SESSION['ultima_actividad'] ?? time()),
            'ip_cliente' => $_SERVER['REMOTE_ADDR'] ?? 'desconocida'
        ];
    }
    
    /**
     * Actualiza la marca de tiempo de última actividad
     */
    public function actualizarActividad(): void {
        $_SESSION['ultima_actividad'] = time();
    }
    
    /**
     * Verifica si la sesión ha expirado por inactividad
     * @return bool True si ha expirado
     */
    public function haSesionExpirado(): bool {
        if (!isset($_SESSION['ultima_actividad'])) {
            $_SESSION['ultima_actividad'] = time();
            return false;
        }
        
        $tiempoInactividad = time() - $_SESSION['ultima_actividad'];
        return $tiempoInactividad > TIEMPO_EXPIRACION_SESION;
    }
    
    /**
     * Limpia sesiones expiradas
     */
    public function limpiarSesionSiExpirada(): void {
        if ($this->haSesionExpirado()) {
            $this->cerrarSesion();
        } else {
            $this->actualizarActividad();
        }
    }
}
