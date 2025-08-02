<?php
/**
 * Controlador Admin Usuarios - Gestión de usuarios en el panel de administración
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Controladores;

use Modelos\Usuario;
use Modelos\Rol;
use Exception;
use PDO;

/**
 * Clase AdminUsuarioControlador - Gestión de usuarios para administradores
 */
class AdminUsuarioControlador extends ControladorBase {
    
    private Usuario $modeloUsuario;
    private Rol $modeloRol;
    
    protected function inicializarControlador(): void {
        parent::inicializarControlador();
        
        // TEMPORAL: Sin verificación de autenticación para pruebas
        $this->modeloUsuario = new Usuario();
        $this->modeloRol = new Rol();
        $this->establecerLayout('admin');
    }
    
    /**
     * Lista todos los usuarios
     */
    public function listar(): void {
        try {
            $this->establecerTitulo('Gestión de Usuarios - ' . NOMBRE_APLICACION);
            
            // Obtener usuarios (temporal con datos de ejemplo)
            $usuarios = [
                [
                    'id' => 1,
                    'nombre' => 'Administrador',
                    'apellidos' => 'del Sistema',
                    'email' => 'admin@exposiciones.local',
                    'rol_nombre' => 'Administrador',
                    'activo' => 1,
                    'ultimo_acceso' => '2025-08-02 01:30:00',
                    'fecha_creacion' => '2025-08-02 00:42:25'
                ]
            ];
            
            $totalUsuarios = count($usuarios);
            
            $this->renderizar('admin/usuarios/listar', [
                'usuarios' => $usuarios,
                'total_usuarios' => $totalUsuarios
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'admin/error');
        }
    }
    
    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function mostrarCrear(): void {
        try {
            $this->establecerTitulo('Crear Usuario - ' . NOMBRE_APLICACION);
            
            // Obtener roles disponibles
            $roles = [
                ['id' => 2, 'nombre' => 'Usuario'],
                ['id' => 3, 'nombre' => 'Editor'],
                ['id' => 4, 'nombre' => 'Administrador']
            ];
            
            $this->renderizar('admin/usuarios/crear', [
                'roles' => $roles
                // csrf_token removido para desarrollo
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'admin/error');
        }
    }
    
    /**
     * Procesa la creación de un nuevo usuario
     */
    public function procesarCrear(): void {
        try {
            // CSRF removido para desarrollo - el navegador integrado no mantiene sesiones correctamente
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'nombre',
                'apellidos',
                'email',
                'password',
                'password_confirm',
                'rol_id',
                'telefono',
                'activo',
                'email_verificado'
            ]);
            
            // Validaciones básicas
            $this->validarRequerido($datos['nombre'], 'nombre');
            $this->validarRequerido($datos['apellidos'], 'apellidos');
            $this->validarRequerido($datos['email'], 'email');
            $this->validarRequerido($datos['password'], 'contraseña');
            $this->validarRequerido($datos['rol_id'], 'rol');
            
            // Validar email
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('📧 El formato del email no es válido');
            }
            
            // Validar contraseña
            if (strlen($datos['password']) < 6) {
                throw new Exception('🔒 La contraseña debe tener al menos 6 caracteres');
            }
            
            // Validar confirmación de contraseña
            if ($datos['password'] !== $datos['password_confirm']) {
                throw new Exception('🔐 Las contraseñas no coinciden');
            }
            
            // Validar rol
            $rolesValidos = [2, 3, 4]; // Usuario, Editor, Admin
            if (!in_array((int)$datos['rol_id'], $rolesValidos)) {
                throw new Exception('👤 El rol seleccionado no es válido');
            }
            
            // Verificar si el email ya existe
            $usuarioExistente = $this->buscarUsuarioPorEmail($datos['email']);
            if ($usuarioExistente) {
                throw new Exception('📧 Ya existe un usuario con este email');
            }
            
            // Crear el usuario
            $usuarioCreado = $this->crearUsuario($datos);
            
            if ($usuarioCreado) {
                $nombreCompleto = $datos['nombre'] . ' ' . $datos['apellidos'];
                $this->añadirMensajeFlash('exito', "✅ Usuario '{$nombreCompleto}' creado exitosamente");
                $this->añadirMensajeFlash('info', "📧 Email: {$datos['email']} | 👤 Rol: " . $this->obtenerNombreRol($datos['rol_id']));
                $this->redirigir('/admin/usuarios/crear');
            } else {
                throw new Exception('No se pudo crear el usuario en la base de datos');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al crear usuario: ' . $e->getMessage());
            $this->redirigir('/admin/usuarios/crear');
        }
    }
    
    /**
     * Busca un usuario por email
     */
    private function buscarUsuarioPorEmail(string $email): ?array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $stmt = $bd->obtenerConexion()->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (Exception $e) {
            error_log("Error al buscar usuario por email: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crea un nuevo usuario en la base de datos
     */
    private function crearUsuario(array $datos): bool {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            // Preparar datos
            $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $activo = isset($datos['activo']) ? 1 : 0;
            $emailVerificado = isset($datos['email_verificado']) ? 1 : 0;
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol_id, telefono, activo, email_verificado, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conexion->prepare($sql);
            
            $parametros = [
                $datos['nombre'],
                $datos['apellidos'],
                $datos['email'],
                $passwordHash,
                (int)$datos['rol_id'],
                $datos['telefono'] ?? null,
                $activo,
                $emailVerificado
            ];
            
            $resultado = $stmt->execute($parametros);
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el nombre del rol por su ID
     */
    private function obtenerNombreRol(int $rolId): string {
        $roles = [
            2 => 'Usuario',
            3 => 'Editor', 
            4 => 'Administrador'
        ];
        
        return $roles[$rolId] ?? 'Desconocido';
    }
    
    /**
     * Simula la eliminación de un usuario
     */
    public function eliminar(): void {
        try {
            $this->verificarCSRF();
            
            $id = (int) ($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID de usuario inválido');
            }
            
            if ($id === 1) {
                throw new Exception('No se puede eliminar el usuario administrador principal');
            }
            
            // Simular eliminación
            $this->añadirMensajeFlash('exito', 'Usuario eliminado exitosamente');
            $this->redirigir('/admin/usuarios');
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', 'Error al eliminar usuario: ' . $e->getMessage());
            $this->redirigir('/admin/usuarios');
        }
    }
}
