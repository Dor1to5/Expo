<?php
/**
 * Controlador Autenticación - Gestión de login, registro y sesiones
 * 
 * Este controlador maneja todas las operaciones relacionadas con
 * la autenticación de usuarios, registro y gestión de sesiones.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Controladores;

use Modelos\Usuario;
use Exception;

/**
 * Clase AutenticacionControlador - Controlador para autenticación de usuarios
 */
class AutenticacionControlador extends ControladorBase {
    
    /**
     * Modelo de usuario
     * @var Usuario
     */
    private Usuario $modeloUsuario;
    
    /**
     * Inicializa el controlador
     */
    protected function inicializarControlador(): void {
        parent::inicializarControlador();
        
        $this->modeloUsuario = new Usuario();
        $this->establecerLayout('publico');
    }
    
    /**
     * Muestra el formulario de inicio de sesión
     */
    public function mostrarLogin(): void {
        try {
            // Si ya está autenticado, redirigir al dashboard o página principal
            if ($this->auth->estaAutenticado()) {
                $url = $this->auth->puedeAccederAdmin() ? '/admin' : '/';
                $this->redirigir($url);
            }
            
            $this->establecerTitulo('Iniciar Sesión - ' . NOMBRE_APLICACION);
            
            // Obtener URL de redirección si existe
            $urlRedireccion = $this->obtenerParametroGET('redirect', '');
            
            $this->renderizar('publicas/login', [
                'url_redireccion' => $urlRedireccion
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Procesa el inicio de sesión
     */
    public function procesarLogin(): void {
        try {
            $this->verificarCSRF();
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'nombre_usuario',
                'contrasena',
                'recordar',
                'url_redireccion'
            ]);
            
            // Validar datos requeridos
            $this->validarRequerido($datos['nombre_usuario'], 'nombre de usuario');
            $this->validarRequerido($datos['contrasena'], 'contraseña');
            
            // Intentar autenticar
            $recordar = !empty($datos['recordar']);
            $exito = $this->auth->autenticar($datos['nombre_usuario'], $datos['contrasena'], $recordar);
            
            if (!$exito) {
                $this->añadirMensajeFlash('error', 'Credenciales incorrectas. Verifique su nombre de usuario y contraseña.');
                $this->redirigir('/login');
            }
            
            // Autenticación exitosa - determinar redirección
            $urlRedireccion = !empty($datos['url_redireccion']) ? $datos['url_redireccion'] : '';
            
            if (!empty($urlRedireccion) && filter_var($urlRedireccion, FILTER_VALIDATE_URL) === false) {
                // URL relativa válida
                $this->redirigir($urlRedireccion);
            } else {
                // Redirigir según el rol del usuario
                $url = $this->auth->puedeAccederAdmin() ? '/admin' : '/';
                $this->redirigir($url);
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', $e->getMessage());
            $this->redirigir('/login');
        }
    }
    
    /**
     * Muestra el formulario de registro
     */
    public function mostrarRegistro(): void {
        try {
            // Si ya está autenticado, redirigir
            if ($this->auth->estaAutenticado()) {
                $url = $this->auth->puedeAccederAdmin() ? '/admin' : '/';
                $this->redirigir($url);
            }
            
            $this->establecerTitulo('Registrarse - ' . NOMBRE_APLICACION);
            
            $this->renderizar('publicas/registro');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Procesa el registro de nuevo usuario
     */
    public function procesarRegistro(): void {
        try {
            $this->verificarCSRF();
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'nombre_usuario',
                'email',
                'contrasena',
                'confirmar_contrasena',
                'nombre_completo',
                'telefono',
                'acepta_terminos'
            ]);
            
            // Validaciones básicas
            $this->validarRequerido($datos['nombre_usuario'], 'nombre de usuario');
            $this->validarRequerido($datos['email'], 'email');
            $this->validarEmail($datos['email']);
            $this->validarRequerido($datos['contrasena'], 'contraseña');
            $this->validarRequerido($datos['confirmar_contrasena'], 'confirmación de contraseña');
            $this->validarRequerido($datos['nombre_completo'], 'nombre completo');
            
            // Validar aceptación de términos
            if (empty($datos['acepta_terminos'])) {
                throw new Exception("Debe aceptar los términos y condiciones");
            }
            
            // Validar longitud de nombre de usuario
            if (strlen($datos['nombre_usuario']) < 3) {
                throw new Exception("El nombre de usuario debe tener al menos 3 caracteres");
            }
            
            // Validar longitud de contraseña
            if (strlen($datos['contrasena']) < 6) {
                throw new Exception("La contraseña debe tener al menos 6 caracteres");
            }
            
            // Validar que las contraseñas coincidan
            if ($datos['contrasena'] !== $datos['confirmar_contrasena']) {
                throw new Exception("Las contraseñas no coinciden");
            }
            
            // Validar longitud de nombre completo
            if (strlen($datos['nombre_completo']) < 2) {
                throw new Exception("El nombre completo debe tener al menos 2 caracteres");
            }
            
            // Verificar disponibilidad de nombre de usuario
            if ($this->modeloUsuario->obtenerPorNombreUsuario($datos['nombre_usuario'])) {
                throw new Exception("El nombre de usuario ya está en uso");
            }
            
            // Verificar disponibilidad de email
            if ($this->modeloUsuario->obtenerPorEmail($datos['email'])) {
                throw new Exception("El email ya está registrado");
            }
            
            // Preparar datos para registro (remover campos no necesarios)
            $datosRegistro = [
                'nombre_usuario' => $datos['nombre_usuario'],
                'email' => $datos['email'],
                'contrasena' => $datos['contrasena'], // Se hashea en el modelo
                'nombre_completo' => $datos['nombre_completo'],
                'telefono' => $datos['telefono'] ?: null,
                'rol_id' => 2 // Rol de usuario básico
            ];
            
            // Registrar usuario
            $idUsuario = $this->auth->registrar($datosRegistro);
            
            // Autenticar automáticamente después del registro
            $this->auth->autenticar($datos['nombre_usuario'], $datos['contrasena']);
            
            $this->añadirMensajeFlash('success', 'Registro completado exitosamente. ¡Bienvenido!');
            $this->redirigir('/');
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', $e->getMessage());
            $this->redirigir('/registro');
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function cerrarSesion(): void {
        try {
            $this->auth->cerrarSesion();
            
            $this->añadirMensajeFlash('info', 'Has cerrado sesión exitosamente');
            $this->redirigir('/');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra el formulario de recuperación de contraseña
     */
    public function mostrarRecuperacion(): void {
        try {
            // Si ya está autenticado, redirigir
            if ($this->auth->estaAutenticado()) {
                $this->redirigir('/');
            }
            
            $this->establecerTitulo('Recuperar Contraseña - ' . NOMBRE_APLICACION);
            
            $this->renderizar('publicas/recuperar-contrasena');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function procesarRecuperacion(): void {
        try {
            $this->verificarCSRF();
            
            // Obtener email del formulario
            $email = $this->obtenerParametroGET('email', '');
            
            $this->validarRequerido($email, 'email');
            $this->validarEmail($email);
            
            // Verificar que el email existe
            $usuario = $this->modeloUsuario->obtenerPorEmail($email);
            if (!$usuario) {
                // Por seguridad, no revelar si el email existe o no
                $this->añadirMensajeFlash('info', 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.');
                $this->redirigir('/recuperar-contrasena');
            }
            
            // Generar token de recuperación (simplificado para este ejemplo)
            $token = bin2hex(random_bytes(32));
            
            // Aquí se enviaría el email con el enlace de recuperación
            // Por simplicidad, solo se simula
            
            $this->añadirMensajeFlash('info', 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.');
            $this->redirigir('/login');
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', $e->getMessage());
            $this->redirigir('/recuperar-contrasena');
        }
    }
    
    /**
     * Muestra el perfil del usuario autenticado
     */
    public function mostrarPerfil(): void {
        try {
            $this->requerirAutenticacion();
            
            $this->establecerTitulo('Mi Perfil - ' . NOMBRE_APLICACION);
            $this->establecerLayout('usuario');
            
            $usuario = $this->auth->obtenerUsuarioAutenticado();
            
            $this->renderizar('usuario/perfil', [
                'usuario' => $usuario
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Procesa la actualización del perfil
     */
    public function procesarActualizarPerfil(): void {
        try {
            $this->requerirAutenticacion();
            $this->verificarCSRF();
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'nombre_completo',
                'telefono',
                'email'
            ]);
            
            // Validaciones
            $this->validarRequerido($datos['nombre_completo'], 'nombre completo');
            $this->validarRequerido($datos['email'], 'email');
            $this->validarEmail($datos['email']);
            
            // Verificar que el email no esté en uso por otro usuario
            $usuarioActual = $this->auth->obtenerUsuarioAutenticado();
            $usuarioConEmail = $this->modeloUsuario->obtenerPorEmail($datos['email']);
            
            if ($usuarioConEmail && $usuarioConEmail['id'] != $usuarioActual['id']) {
                throw new Exception("El email ya está en uso por otro usuario");
            }
            
            // Actualizar perfil
            $this->auth->actualizarPerfil($datos);
            
            $this->añadirMensajeFlash('success', 'Perfil actualizado exitosamente');
            $this->redirigir('/perfil');
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', $e->getMessage());
            $this->redirigir('/perfil');
        }
    }
    
    /**
     * Muestra el formulario de cambio de contraseña
     */
    public function mostrarCambiarContrasena(): void {
        try {
            $this->requerirAutenticacion();
            
            $this->establecerTitulo('Cambiar Contraseña - ' . NOMBRE_APLICACION);
            $this->establecerLayout('usuario');
            
            $this->renderizar('usuario/cambiar-contrasena');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Procesa el cambio de contraseña
     */
    public function procesarCambiarContrasena(): void {
        try {
            $this->requerirAutenticacion();
            $this->verificarCSRF();
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'contrasena_actual',
                'contrasena_nueva',
                'confirmar_contrasena'
            ]);
            
            // Validaciones
            $this->validarRequerido($datos['contrasena_actual'], 'contraseña actual');
            $this->validarRequerido($datos['contrasena_nueva'], 'contraseña nueva');
            $this->validarRequerido($datos['confirmar_contrasena'], 'confirmación de contraseña');
            
            // Validar longitud de nueva contraseña
            if (strlen($datos['contrasena_nueva']) < 6) {
                throw new Exception("La contraseña nueva debe tener al menos 6 caracteres");
            }
            
            // Validar que las contraseñas nuevas coincidan
            if ($datos['contrasena_nueva'] !== $datos['confirmar_contrasena']) {
                throw new Exception("Las contraseñas nuevas no coinciden");
            }
            
            // Cambiar contraseña
            $this->auth->cambiarContrasena($datos['contrasena_actual'], $datos['contrasena_nueva']);
            
            $this->añadirMensajeFlash('success', 'Contraseña cambiada exitosamente');
            $this->redirigir('/perfil');
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', $e->getMessage());
            $this->redirigir('/cambiar-contrasena');
        }
    }
    
    /**
     * API para verificar disponibilidad de nombre de usuario
     */
    public function apiVerificarNombreUsuario(): void {
        try {
            $nombreUsuario = $this->obtenerParametroGET('nombre_usuario', '');
            
            if (strlen($nombreUsuario) < 3) {
                $this->renderizarJson([
                    'disponible' => false,
                    'mensaje' => 'El nombre de usuario debe tener al menos 3 caracteres'
                ]);
            }
            
            $usuario = $this->modeloUsuario->obtenerPorNombreUsuario($nombreUsuario);
            
            $this->renderizarJson([
                'disponible' => $usuario === null,
                'mensaje' => $usuario ? 'El nombre de usuario ya está en uso' : 'Nombre de usuario disponible'
            ]);
            
        } catch (Exception $e) {
            $this->renderizarJson([
                'disponible' => false,
                'mensaje' => 'Error al verificar disponibilidad'
            ], 500);
        }
    }
    
    /**
     * API para verificar disponibilidad de email
     */
    public function apiVerificarEmail(): void {
        try {
            $email = $this->obtenerParametroGET('email', '');
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->renderizarJson([
                    'disponible' => false,
                    'mensaje' => 'El formato del email no es válido'
                ]);
            }
            
            $usuario = $this->modeloUsuario->obtenerPorEmail($email);
            
            $this->renderizarJson([
                'disponible' => $usuario === null,
                'mensaje' => $usuario ? 'El email ya está registrado' : 'Email disponible'
            ]);
            
        } catch (Exception $e) {
            $this->renderizarJson([
                'disponible' => false,
                'mensaje' => 'Error al verificar disponibilidad'
            ], 500);
        }
    }
}
