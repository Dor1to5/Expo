<?php
/**
 * Modelo Rol - Gestión de roles y permisos del sistema
 * 
 * Esta clase maneja todas las operaciones relacionadas con los roles
 * de usuario y sus permisos asociados.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Modelos;

use Exception;

/**
 * Clase Rol - Modelo para la gestión de roles y permisos
 */
class Rol extends ModeloBase {
    
    /**
     * Nombre de la tabla en la base de datos
     * @var string
     */
    protected string $tabla = 'roles';
    
    /**
     * Campos que se pueden llenar masivamente
     * @var array
     */
    protected array $camposLlenables = [
        'nombre',
        'descripcion',
        'permisos',
        'activo',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    /**
     * Reglas de validación para los campos
     * @var array
     */
    protected array $reglasValidacion = [
        'nombre' => ['requerido', 'min:3', 'max:50', 'unico'],
        'descripcion' => ['max:255'],
        'permisos' => ['requerido']
    ];
    
    /**
     * Permisos disponibles en el sistema
     * @var array
     */
    private static array $permisosDisponibles = [
        // Permisos de dashboard
        'dashboard.ver' => 'Ver panel de administración',
        
        // Permisos de usuarios
        'usuarios.listar' => 'Listar usuarios',
        'usuarios.ver' => 'Ver detalles de usuario',
        'usuarios.crear' => 'Crear usuarios',
        'usuarios.editar' => 'Editar usuarios',
        'usuarios.eliminar' => 'Eliminar usuarios',
        'usuarios.cambiar_estado' => 'Cambiar estado de usuarios',
        
        // Permisos de roles
        'roles.listar' => 'Listar roles',
        'roles.ver' => 'Ver detalles de rol',
        'roles.crear' => 'Crear roles',
        'roles.editar' => 'Editar roles',
        'roles.eliminar' => 'Eliminar roles',
        
        // Permisos de exposiciones
        'exposiciones.listar' => 'Listar exposiciones',
        'exposiciones.ver' => 'Ver detalles de exposición',
        'exposiciones.crear' => 'Crear exposiciones',
        'exposiciones.editar' => 'Editar exposiciones',
        'exposiciones.eliminar' => 'Eliminar exposiciones',
        'exposiciones.publicar' => 'Publicar/despublicar exposiciones',
        
        // Permisos de artículos/blog
        'articulos.listar' => 'Listar artículos',
        'articulos.ver' => 'Ver detalles de artículo',
        'articulos.crear' => 'Crear artículos',
        'articulos.editar' => 'Editar artículos',
        'articulos.eliminar' => 'Eliminar artículos',
        'articulos.publicar' => 'Publicar/despublicar artículos',
        
        // Permisos de configuración
        'configuracion.ver' => 'Ver configuración del sistema',
        'configuracion.editar' => 'Editar configuración del sistema',
        
        // Permisos especiales
        'sistema.acceso_completo' => 'Acceso completo al sistema (Super Administrador)',
        'reportes.generar' => 'Generar reportes del sistema',
        'logs.ver' => 'Ver logs del sistema'
    ];
    
    /**
     * Obtiene todos los permisos disponibles en el sistema
     * @return array Array con permisos disponibles
     */
    public static function obtenerPermisosDisponibles(): array {
        return self::$permisosDisponibles;
    }
    
    /**
     * Obtiene roles con estadísticas de usuarios asignados
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de roles con estadísticas
     */
    public function obtenerConEstadisticas(int $limite = 0, int $offset = 0): array {
        $sql = "SELECT r.*, COUNT(u.id) as total_usuarios
                FROM {$this->tabla} r
                LEFT JOIN usuarios u ON r.id = u.rol_id
                GROUP BY r.id
                ORDER BY r.nombre ASC";
        
        if ($limite > 0) {
            $sql .= " LIMIT {$limite}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $roles = $this->bd->seleccionar($sql);
        
        // Decodificar permisos JSON para cada rol
        foreach ($roles as &$rol) {
            $rol['permisos_array'] = json_decode($rol['permisos'] ?? '[]', true);
            $rol['total_permisos'] = count($rol['permisos_array']);
        }
        
        return $roles;
    }
    
    /**
     * Verifica si un rol tiene un permiso específico
     * @param int $idRol ID del rol
     * @param string $permiso Nombre del permiso
     * @return bool True si el rol tiene el permiso
     */
    public function tienePermiso(int $idRol, string $permiso): bool {
        $rol = $this->obtenerPorId($idRol);
        
        if (!$rol || !$rol['activo']) {
            return false;
        }
        
        $permisos = json_decode($rol['permisos'] ?? '[]', true);
        
        // Verificar acceso completo (super administrador)
        if (in_array('sistema.acceso_completo', $permisos)) {
            return true;
        }
        
        return in_array($permiso, $permisos);
    }
    
    /**
     * Asigna permisos a un rol
     * @param int $idRol ID del rol
     * @param array $permisos Array de permisos a asignar
     * @return bool True si se asignaron correctamente
     * @throws Exception Si hay permisos inválidos o error en actualización
     */
    public function asignarPermisos(int $idRol, array $permisos): bool {
        // Validar que todos los permisos existen
        foreach ($permisos as $permiso) {
            if (!array_key_exists($permiso, self::$permisosDisponibles)) {
                throw new Exception("El permiso '{$permiso}' no es válido");
            }
        }
        
        // Codificar permisos como JSON
        $permisosJson = json_encode(array_values($permisos));
        
        return $this->actualizar($idRol, [
            'permisos' => $permisosJson
        ]);
    }
    
    /**
     * Obtiene los permisos de un rol
     * @param int $idRol ID del rol
     * @return array Array de permisos del rol
     */
    public function obtenerPermisos(int $idRol): array {
        $rol = $this->obtenerPorId($idRol);
        
        if (!$rol) {
            return [];
        }
        
        return json_decode($rol['permisos'] ?? '[]', true);
    }
    
    /**
     * Crea un nuevo rol con permisos
     * @param array $datosRol Datos del rol
     * @param array $permisos Permisos a asignar
     * @return int ID del rol creado
     * @throws Exception Si hay error en la creación
     */
    public function crearConPermisos(array $datosRol, array $permisos = []): int {
        // Validar permisos
        foreach ($permisos as $permiso) {
            if (!array_key_exists($permiso, self::$permisosDisponibles)) {
                throw new Exception("El permiso '{$permiso}' no es válido");
            }
        }
        
        // Codificar permisos como JSON
        $datosRol['permisos'] = json_encode(array_values($permisos));
        $datosRol['activo'] = $datosRol['activo'] ?? 1;
        $datosRol['fecha_creacion'] = date('Y-m-d H:i:s');
        $datosRol['fecha_actualizacion'] = date('Y-m-d H:i:s');
        
        return $this->crear($datosRol);
    }
    
    /**
     * Obtiene usuarios asignados a un rol específico
     * @param int $idRol ID del rol
     * @param int $limite Límite de resultados
     * @return array Lista de usuarios con el rol
     */
    public function obtenerUsuarios(int $idRol, int $limite = 0): array {
        $sql = "SELECT u.id, u.nombre_usuario, u.email, u.nombre_completo, u.activo, u.ultimo_acceso
                FROM usuarios u
                WHERE u.rol_id = :rol_id
                ORDER BY u.nombre_completo ASC";
        
        if ($limite > 0) {
            $sql .= " LIMIT {$limite}";
        }
        
        return $this->bd->seleccionar($sql, ['rol_id' => $idRol]);
    }
    
    /**
     * Verifica si un rol se puede eliminar (no tiene usuarios asignados)
     * @param int $idRol ID del rol
     * @return bool True si se puede eliminar
     */
    public function puedeEliminar(int $idRol): bool {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE rol_id = :rol_id";
        $resultado = $this->bd->seleccionarUno($sql, ['rol_id' => $idRol]);
        
        return ($resultado['total'] ?? 0) == 0;
    }
    
    /**
     * Elimina un rol solo si no tiene usuarios asignados
     * @param int $id ID del rol a eliminar
     * @return bool True si se eliminó correctamente
     * @throws Exception Si el rol tiene usuarios asignados
     */
    public function eliminar(int $id): bool {
        if (!$this->puedeEliminar($id)) {
            throw new Exception("No se puede eliminar el rol porque tiene usuarios asignados");
        }
        
        return parent::eliminar($id);
    }
    
    /**
     * Activa o desactiva un rol
     * @param int $idRol ID del rol
     * @param bool $activo Estado activo/inactivo
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoActivo(int $idRol, bool $activo): bool {
        return $this->actualizar($idRol, [
            'activo' => $activo ? 1 : 0
        ]);
    }
    
    /**
     * Obtiene roles activos para selección
     * @return array Roles activos en formato para select
     */
    public function obtenerParaSelect(): array {
        $roles = $this->obtenerTodos(0, 0, ['activo' => 1], 'nombre', 'ASC');
        
        $opciones = [];
        foreach ($roles as $rol) {
            $opciones[$rol['id']] = $rol['nombre'];
        }
        
        return $opciones;
    }
    
    /**
     * Obtiene estadísticas de roles
     * @return array Estadísticas de roles
     */
    public function obtenerEstadisticas(): array {
        $stats = [];
        
        // Total de roles
        $stats['total'] = $this->contar();
        
        // Roles activos
        $stats['activos'] = $this->contar(['activo' => 1]);
        
        // Roles inactivos
        $stats['inactivos'] = $this->contar(['activo' => 0]);
        
        // Rol con más usuarios
        $sql = "SELECT r.nombre, COUNT(u.id) as total_usuarios
                FROM {$this->tabla} r
                LEFT JOIN usuarios u ON r.id = u.rol_id
                GROUP BY r.id, r.nombre
                ORDER BY total_usuarios DESC
                LIMIT 1";
        
        $resultado = $this->bd->seleccionarUno($sql);
        $stats['rol_mas_usado'] = $resultado ?? ['nombre' => 'N/A', 'total_usuarios' => 0];
        
        return $stats;
    }
    
    /**
     * Inicializa roles básicos del sistema
     * @return bool True si se inicializaron correctamente
     * @throws Exception Si hay error en la inicialización
     */
    public function inicializarRolesBasicos(): bool {
        // Verificar si ya existen roles
        if ($this->contar() > 0) {
            return true; // Ya están inicializados
        }
        
        $this->bd->iniciarTransaccion();
        
        try {
            // Rol Invitado (ID: 1)
            $this->crearConPermisos([
                'nombre' => 'Invitado',
                'descripcion' => 'Usuario sin cuenta, solo acceso público'
            ], []);
            
            // Rol Usuario (ID: 2)
            $this->crearConPermisos([
                'nombre' => 'Usuario',
                'descripcion' => 'Usuario registrado con acceso básico'
            ], []);
            
            // Rol Editor (ID: 3)
            $this->crearConPermisos([
                'nombre' => 'Editor',
                'descripcion' => 'Usuario con permisos de edición de contenido'
            ], [
                'exposiciones.listar',
                'exposiciones.ver',
                'exposiciones.crear',
                'exposiciones.editar',
                'articulos.listar',
                'articulos.ver',
                'articulos.crear',
                'articulos.editar',
                'articulos.publicar'
            ]);
            
            // Rol Administrador (ID: 4)
            $this->crearConPermisos([
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador con acceso completo al sistema'
            ], [
                'sistema.acceso_completo'
            ]);
            
            $this->bd->confirmarTransaccion();
            return true;
            
        } catch (Exception $e) {
            $this->bd->revertirTransaccion();
            throw new Exception("Error al inicializar roles básicos: " . $e->getMessage());
        }
    }
    
    /**
     * Crear rol específico con datos completos
     * @param array $datos Datos del rol
     * @return int ID del rol creado
     * @throws Exception Si hay error en la creación
     */
    public function crear(array $datos): int {
        // Asegurar que las fechas estén establecidas
        $datos['fecha_creacion'] = $datos['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $datos['fecha_actualizacion'] = $datos['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        
        return parent::crear($datos);
    }
    
    /**
     * Actualizar rol con fecha de actualización automática
     * @param int $id ID del rol
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
