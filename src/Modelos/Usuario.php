<?php
/**
 * Modelo Usuario - Gestión de usuarios del sistema
 * 
 * Esta clase maneja todas las operaciones relacionadas con los usuarios,
 * incluyendo autenticación, registro y gestión de perfiles.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Modelos;

use DateTime;
use Exception;

/**
 * Clase Usuario - Modelo para la gestión de usuarios
 */
class Usuario extends ModeloBase {
    
    /**
     * Nombre de la tabla en la base de datos
     * @var string
     */
    protected string $tabla = 'usuarios';
    
    /**
     * Campos que se pueden llenar masivamente
     * @var array
     */
    protected array $camposLlenables = [
        'nombre_usuario',
        'email',
        'contrasena_hash',
        'nombre_completo',
        'telefono',
        'activo',
        'rol_id',
        'ultimo_acceso',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    /**
     * Campos que están ocultos en las respuestas
     * @var array
     */
    protected array $camposOcultos = [
        'contrasena_hash',
        'token_recordar',
        'token_recuperacion'
    ];
    
    /**
     * Reglas de validación para los campos
     * @var array
     */
    protected array $reglasValidacion = [
        'nombre_usuario' => ['requerido', 'min:3', 'max:50', 'unico'],
        'email' => ['requerido', 'email', 'max:255', 'unico'],
        'nombre_completo' => ['requerido', 'min:2', 'max:255'],
        'contrasena_hash' => ['requerido', 'min:6'],
        'rol_id' => ['requerido']
    ];
    
    /**
     * Autentica a un usuario con sus credenciales
     * @param string $nombreUsuario Nombre de usuario o email
     * @param string $contrasena Contraseña en texto plano
     * @return array|null Datos del usuario autenticado o null si falla
     * @throws Exception Si hay error en la consulta
     */
    public function autenticar(string $nombreUsuario, string $contrasena): ?array {
        // Buscar usuario por nombre de usuario o email
        $sql = "SELECT u.*, r.nombre as nombre_rol, r.permisos as permisos_rol
                FROM {$this->tabla} u
                LEFT JOIN roles r ON u.rol_id = r.id
                WHERE (u.nombre_usuario = :identificador OR u.email = :identificador)
                AND u.activo = 1
                LIMIT 1";
        
        $usuario = $this->bd->seleccionarUno($sql, ['identificador' => $nombreUsuario]);
        
        if (!$usuario) {
            return null;
        }
        
        // Verificar contraseña
        if (!password_verify($contrasena, $usuario['contrasena_hash'])) {
            return null;
        }
        
        // Actualizar último acceso
        $this->actualizarUltimoAcceso($usuario['id']);
        
        // Ocultar campos sensibles
        return $this->ocultarCamposSensibles($usuario);
    }
    
    /**
     * Registra un nuevo usuario en el sistema
     * @param array $datosUsuario Datos del nuevo usuario
     * @return int ID del usuario creado
     * @throws Exception Si hay error en el registro
     */
    public function registrar(array $datosUsuario): int {
        // Hashear la contraseña antes de guardar
        if (isset($datosUsuario['contrasena'])) {
            $datosUsuario['contrasena_hash'] = $this->hashearContrasena($datosUsuario['contrasena']);
            unset($datosUsuario['contrasena']);
        }
        
        // Establecer valores por defecto
        $datosUsuario['activo'] = $datosUsuario['activo'] ?? 1;
        $datosUsuario['fecha_creacion'] = date('Y-m-d H:i:s');
        $datosUsuario['fecha_actualizacion'] = date('Y-m-d H:i:s');
        
        // Si no se especifica rol, asignar rol de usuario básico (ID 2)
        if (!isset($datosUsuario['rol_id'])) {
            $datosUsuario['rol_id'] = 2; // Usuario básico
        }
        
        return $this->crear($datosUsuario);
    }
    
    /**
     * Cambia la contraseña de un usuario
     * @param int $idUsuario ID del usuario
     * @param string $contrasenaActual Contraseña actual
     * @param string $contrasenaNueva Nueva contraseña
     * @return bool True si se cambió correctamente
     * @throws Exception Si la contraseña actual es incorrecta o hay error
     */
    public function cambiarContrasena(int $idUsuario, string $contrasenaActual, string $contrasenaNueva): bool {
        // Obtener usuario actual
        $usuario = $this->obtenerPorId($idUsuario);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }
        
        // Verificar contraseña actual
        if (!password_verify($contrasenaActual, $usuario['contrasena_hash'])) {
            throw new Exception("La contraseña actual es incorrecta");
        }
        
        // Actualizar con nueva contraseña
        return $this->actualizar($idUsuario, [
            'contrasena_hash' => $this->hashearContrasena($contrasenaNueva),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Restablece la contraseña de un usuario (para administradores)
     * @param int $idUsuario ID del usuario
     * @param string $contrasenaNueva Nueva contraseña
     * @return bool True si se restableció correctamente
     * @throws Exception Si hay error en la actualización
     */
    public function restablecerContrasena(int $idUsuario, string $contrasenaNueva): bool {
        return $this->actualizar($idUsuario, [
            'contrasena_hash' => $this->hashearContrasena($contrasenaNueva),
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
            'token_recuperacion' => null // Limpiar token de recuperación
        ]);
    }
    
    /**
     * Obtiene un usuario por su nombre de usuario
     * @param string $nombreUsuario Nombre de usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public function obtenerPorNombreUsuario(string $nombreUsuario): ?array {
        return $this->obtenerUnoPor(['nombre_usuario' => $nombreUsuario]);
    }
    
    /**
     * Obtiene un usuario por su email
     * @param string $email Email del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public function obtenerPorEmail(string $email): ?array {
        return $this->obtenerUnoPor(['email' => $email]);
    }
    
    /**
     * Obtiene usuarios con información de rol
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @param array $filtros Filtros adicionales
     * @return array Lista de usuarios con información de rol
     */
    public function obtenerConRol(int $limite = 0, int $offset = 0, array $filtros = []): array {
        $sql = "SELECT u.*, r.nombre as nombre_rol
                FROM {$this->tabla} u
                LEFT JOIN roles r ON u.rol_id = r.id";
        
        $parametros = [];
        $condicionesWhere = [];
        
        // Aplicar filtros
        if (!empty($filtros['activo'])) {
            $condicionesWhere[] = "u.activo = :activo";
            $parametros['activo'] = $filtros['activo'];
        }
        
        if (!empty($filtros['rol_id'])) {
            $condicionesWhere[] = "u.rol_id = :rol_id";
            $parametros['rol_id'] = $filtros['rol_id'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $condicionesWhere[] = "(u.nombre_usuario LIKE :busqueda OR u.email LIKE :busqueda OR u.nombre_completo LIKE :busqueda)";
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        // Agregar condiciones WHERE si existen
        if (!empty($condicionesWhere)) {
            $sql .= " WHERE " . implode(' AND ', $condicionesWhere);
        }
        
        // Ordenar por fecha de creación descendente
        $sql .= " ORDER BY u.fecha_creacion DESC";
        
        // Agregar límite y offset
        if ($limite > 0) {
            $sql .= " LIMIT {$limite}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $usuarios = $this->bd->seleccionar($sql, $parametros);
        
        // Ocultar campos sensibles de todos los usuarios
        return array_map([$this, 'ocultarCamposSensibles'], $usuarios);
    }
    
    /**
     * Activa o desactiva un usuario
     * @param int $idUsuario ID del usuario
     * @param bool $activo Estado activo/inactivo
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoActivo(int $idUsuario, bool $activo): bool {
        return $this->actualizar($idUsuario, [
            'activo' => $activo ? 1 : 0,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Verifica si un usuario tiene un permiso específico
     * @param int $idUsuario ID del usuario
     * @param string $permiso Nombre del permiso
     * @return bool True si tiene el permiso
     */
    public function tienePermiso(int $idUsuario, string $permiso): bool {
        $sql = "SELECT r.permisos
                FROM {$this->tabla} u
                JOIN roles r ON u.rol_id = r.id
                WHERE u.id = :id_usuario AND u.activo = 1";
        
        $resultado = $this->bd->seleccionarUno($sql, ['id_usuario' => $idUsuario]);
        
        if (!$resultado) {
            return false;
        }
        
        $permisos = json_decode($resultado['permisos'] ?? '[]', true);
        return in_array($permiso, $permisos);
    }
    
    /**
     * Obtiene estadísticas básicas de usuarios
     * @return array Estadísticas de usuarios
     */
    public function obtenerEstadisticas(): array {
        $stats = [];
        
        // Total de usuarios
        $stats['total'] = $this->contar();
        
        // Usuarios activos
        $stats['activos'] = $this->contar(['activo' => 1]);
        
        // Usuarios inactivos
        $stats['inactivos'] = $this->contar(['activo' => 0]);
        
        // Usuarios registrados en el último mes
        $fechaMesAtras = date('Y-m-d H:i:s', strtotime('-1 month'));
        $sql = "SELECT COUNT(*) as total FROM {$this->tabla} WHERE fecha_creacion >= :fecha";
        $resultado = $this->bd->seleccionarUno($sql, ['fecha' => $fechaMesAtras]);
        $stats['nuevos_mes'] = (int)($resultado['total'] ?? 0);
        
        // Usuarios por rol
        $sql = "SELECT r.nombre, COUNT(u.id) as total
                FROM roles r
                LEFT JOIN {$this->tabla} u ON r.id = u.rol_id
                GROUP BY r.id, r.nombre";
        $stats['por_rol'] = $this->bd->seleccionar($sql);
        
        return $stats;
    }
    
    /**
     * Actualiza la fecha del último acceso del usuario
     * @param int $idUsuario ID del usuario
     */
    private function actualizarUltimoAcceso(int $idUsuario): void {
        try {
            $this->actualizar($idUsuario, [
                'ultimo_acceso' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            // Log del error pero no fallar la autenticación
            error_log("Error al actualizar último acceso: " . $e->getMessage());
        }
    }
    
    /**
     * Hashea una contraseña de forma segura
     * @param string $contrasena Contraseña en texto plano
     * @return string Contraseña hasheada
     */
    private function hashearContrasena(string $contrasena): string {
        return password_hash($contrasena, PASSWORD_DEFAULT);
    }
    
    /**
     * Crear usuario específico con datos completos
     * @param array $datos Datos del usuario
     * @return int ID del usuario creado
     * @throws Exception Si hay error en la creación
     */
    public function crear(array $datos): int {
        // Asegurar que las fechas estén establecidas
        $datos['fecha_creacion'] = $datos['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $datos['fecha_actualizacion'] = $datos['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        
        return parent::crear($datos);
    }
    
    /**
     * Actualizar usuario con fecha de actualización automática
     * @param int $id ID del usuario
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     * @throws Exception Si hay error en la actualización
     */
    public function actualizar(int $id, array $datos): bool {
        // Establecer fecha de actualización automáticamente
        $datos['fecha_actualizacion'] = date('Y-m-d H:i:s');
        
        return parent::actualizar($id, $datos);
    }
}
