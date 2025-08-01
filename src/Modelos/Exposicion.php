<?php
/**
 * Modelo Exposicion - Gestión de exposiciones del sistema
 * 
 * Esta clase maneja todas las operaciones relacionadas con las exposiciones,
 * tanto para el área pública como para la administración.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Modelos;

use Exception;

/**
 * Clase Exposicion - Modelo para la gestión de exposiciones
 */
class Exposicion extends ModeloBase {
    
    /**
     * Nombre de la tabla en la base de datos
     * @var string
     */
    protected string $tabla = 'exposiciones';
    
    /**
     * Campos que se pueden llenar masivamente
     * @var array
     */
    protected array $camposLlenables = [
        'titulo',
        'descripcion',
        'descripcion_corta',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'precio_entrada',
        'imagen_principal',
        'galeria_imagenes',
        'activa',
        'publicada',
        'destacada',
        'categoria',
        'etiquetas',
        'usuario_creador_id',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    /**
     * Reglas de validación para los campos
     * @var array
     */
    protected array $reglasValidacion = [
        'titulo' => ['requerido', 'min:5', 'max:255'],
        'descripcion' => ['requerido', 'min:20'],
        'descripcion_corta' => ['max:500'],
        'fecha_inicio' => ['requerido'],
        'fecha_fin' => ['requerido'],
        'ubicacion' => ['requerido', 'max:255'],
        'precio_entrada' => ['requerido'],
        'usuario_creador_id' => ['requerido']
    ];
    
    /**
     * Estados posibles de una exposición
     * @var array
     */
    private static array $estados = [
        'borrador' => 'Borrador',
        'programada' => 'Programada',
        'activa' => 'Activa',
        'finalizada' => 'Finalizada',
        'cancelada' => 'Cancelada'
    ];
    
    /**
     * Categorías disponibles para exposiciones
     * @var array
     */
    private static array $categorias = [
        'arte_contemporaneo' => 'Arte Contemporáneo',
        'arte_clasico' => 'Arte Clásico',
        'fotografia' => 'Fotografía',
        'escultura' => 'Escultura',
        'pintura' => 'Pintura',
        'arte_digital' => 'Arte Digital',
        'historia' => 'Historia',
        'ciencia' => 'Ciencia',
        'tecnologia' => 'Tecnología',
        'cultura' => 'Cultura'
    ];
    
    /**
     * Obtiene exposiciones públicas (publicadas y activas)
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @param array $filtros Filtros adicionales
     * @return array Lista de exposiciones públicas
     */
    public function obtenerPublicas(int $limite = 0, int $offset = 0, array $filtros = []): array {
        $sql = "SELECT e.*, u.nombre_completo as nombre_creador
                FROM {$this->tabla} e
                LEFT JOIN usuarios u ON e.usuario_creador_id = u.id
                WHERE e.publicada = 1 AND e.activa = 1";
        
        $parametros = [];
        
        // Filtrar por categoría
        if (!empty($filtros['categoria'])) {
            $sql .= " AND e.categoria = :categoria";
            $parametros['categoria'] = $filtros['categoria'];
        }
        
        // Filtrar por fechas (exposiciones actuales)
        if (!empty($filtros['actuales'])) {
            $fechaHoy = date('Y-m-d');
            $sql .= " AND e.fecha_inicio <= :fecha_hoy AND e.fecha_fin >= :fecha_hoy";
            $parametros['fecha_hoy'] = $fechaHoy;
        }
        
        // Filtrar por exposiciones futuras
        if (!empty($filtros['futuras'])) {
            $fechaHoy = date('Y-m-d');
            $sql .= " AND e.fecha_inicio > :fecha_hoy";
            $parametros['fecha_hoy'] = $fechaHoy;
        }
        
        // Filtrar solo destacadas
        if (!empty($filtros['destacadas'])) {
            $sql .= " AND e.destacada = 1";
        }
        
        // Búsqueda por texto
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (e.titulo LIKE :busqueda OR e.descripcion LIKE :busqueda OR e.descripcion_corta LIKE :busqueda)";
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        // Ordenar por fecha de inicio descendente
        $sql .= " ORDER BY e.destacada DESC, e.fecha_inicio DESC";
        
        // Agregar límite y offset
        if ($limite > 0) {
            $sql .= " LIMIT {$limite}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->bd->seleccionar($sql, $parametros);
    }
    
    /**
     * Obtiene una exposición pública por ID
     * @param int $id ID de la exposición
     * @return array|null Datos de la exposición o null si no existe/no es pública
     */
    public function obtenerPublicaPorId(int $id): ?array {
        $sql = "SELECT e.*, u.nombre_completo as nombre_creador
                FROM {$this->tabla} e
                LEFT JOIN usuarios u ON e.usuario_creador_id = u.id
                WHERE e.id = :id AND e.publicada = 1 AND e.activa = 1
                LIMIT 1";
        
        return $this->bd->seleccionarUno($sql, ['id' => $id]);
    }
    
    /**
     * Obtiene exposiciones para administración con información del creador
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @param array $filtros Filtros adicionales
     * @return array Lista de exposiciones con datos del creador
     */
    public function obtenerParaAdmin(int $limite = 0, int $offset = 0, array $filtros = []): array {
        $sql = "SELECT e.*, u.nombre_completo as nombre_creador
                FROM {$this->tabla} e
                LEFT JOIN usuarios u ON e.usuario_creador_id = u.id";
        
        $parametros = [];
        $condicionesWhere = [];
        
        // Filtrar por estado de publicación
        if (isset($filtros['publicada'])) {
            $condicionesWhere[] = "e.publicada = :publicada";
            $parametros['publicada'] = $filtros['publicada'];
        }
        
        // Filtrar por estado activo
        if (isset($filtros['activa'])) {
            $condicionesWhere[] = "e.activa = :activa";
            $parametros['activa'] = $filtros['activa'];
        }
        
        // Filtrar por categoría
        if (!empty($filtros['categoria'])) {
            $condicionesWhere[] = "e.categoria = :categoria";
            $parametros['categoria'] = $filtros['categoria'];
        }
        
        // Filtrar por creador
        if (!empty($filtros['usuario_creador_id'])) {
            $condicionesWhere[] = "e.usuario_creador_id = :usuario_creador_id";
            $parametros['usuario_creador_id'] = $filtros['usuario_creador_id'];
        }
        
        // Búsqueda por texto
        if (!empty($filtros['busqueda'])) {
            $condicionesWhere[] = "(e.titulo LIKE :busqueda OR e.descripcion LIKE :busqueda)";
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        // Agregar condiciones WHERE si existen
        if (!empty($condicionesWhere)) {
            $sql .= " WHERE " . implode(' AND ', $condicionesWhere);
        }
        
        // Ordenar por fecha de creación descendente
        $sql .= " ORDER BY e.fecha_creacion DESC";
        
        // Agregar límite y offset
        if ($limite > 0) {
            $sql .= " LIMIT {$limite}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->bd->seleccionar($sql, $parametros);
    }
    
    /**
     * Obtiene exposiciones destacadas para mostrar en página principal
     * @param int $limite Límite de exposiciones destacadas
     * @return array Lista de exposiciones destacadas
     */
    public function obtenerDestacadas(int $limite = 3): array {
        return $this->obtenerPublicas($limite, 0, ['destacadas' => true]);
    }
    
    /**
     * Obtiene exposiciones actuales (en curso)
     * @param int $limite Límite de resultados
     * @return array Lista de exposiciones actuales
     */
    public function obtenerActuales(int $limite = 0): array {
        return $this->obtenerPublicas($limite, 0, ['actuales' => true]);
    }
    
    /**
     * Obtiene exposiciones futuras (próximas a comenzar)
     * @param int $limite Límite de resultados
     * @return array Lista de exposiciones futuras
     */
    public function obtenerFuturas(int $limite = 0): array {
        return $this->obtenerPublicas($limite, 0, ['futuras' => true]);
    }
    
    /**
     * Cambia el estado de publicación de una exposición
     * @param int $id ID de la exposición
     * @param bool $publicada Estado de publicación
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoPublicacion(int $id, bool $publicada): bool {
        return $this->actualizar($id, [
            'publicada' => $publicada ? 1 : 0
        ]);
    }
    
    /**
     * Cambia el estado activo de una exposición
     * @param int $id ID de la exposición
     * @param bool $activa Estado activo
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoActivo(int $id, bool $activa): bool {
        return $this->actualizar($id, [
            'activa' => $activa ? 1 : 0
        ]);
    }
    
    /**
     * Marca una exposición como destacada o no destacada
     * @param int $id ID de la exposición
     * @param bool $destacada Estado destacado
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoDestacada(int $id, bool $destacada): bool {
        return $this->actualizar($id, [
            'destacada' => $destacada ? 1 : 0
        ]);
    }
    
    /**
     * Obtiene el estado actual de una exposición basado en fechas
     * @param array $exposicion Datos de la exposición
     * @return string Estado de la exposición
     */
    public function obtenerEstadoActual(array $exposicion): string {
        $fechaHoy = date('Y-m-d');
        $fechaInicio = $exposicion['fecha_inicio'];
        $fechaFin = $exposicion['fecha_fin'];
        
        if (!$exposicion['activa']) {
            return 'cancelada';
        }
        
        if (!$exposicion['publicada']) {
            return 'borrador';
        }
        
        if ($fechaInicio > $fechaHoy) {
            return 'programada';
        }
        
        if ($fechaInicio <= $fechaHoy && $fechaFin >= $fechaHoy) {
            return 'activa';
        }
        
        if ($fechaFin < $fechaHoy) {
            return 'finalizada';
        }
        
        return 'borrador';
    }
    
    /**
     * Obtiene categorías disponibles
     * @return array Array de categorías
     */
    public static function obtenerCategorias(): array {
        return self::$categorias;
    }
    
    /**
     * Obtiene estados disponibles
     * @return array Array de estados
     */
    public static function obtenerEstados(): array {
        return self::$estados;
    }
    
    /**
     * Busca exposiciones por texto en título y descripción
     * @param string $termino Término de búsqueda
     * @param int $limite Límite de resultados
     * @return array Resultados de la búsqueda
     */
    public function buscar(string $termino, int $limite = 10): array {
        return $this->obtenerPublicas($limite, 0, ['busqueda' => $termino]);
    }
    
    /**
     * Obtiene exposiciones por categoría
     * @param string $categoria Categoría a filtrar
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de exposiciones de la categoría
     */
    public function obtenerPorCategoria(string $categoria, int $limite = 0, int $offset = 0): array {
        return $this->obtenerPublicas($limite, $offset, ['categoria' => $categoria]);
    }
    
    /**
     * Obtiene estadísticas de exposiciones
     * @return array Estadísticas de exposiciones
     */
    public function obtenerEstadisticas(): array {
        $stats = [];
        
        // Total de exposiciones
        $stats['total'] = $this->contar();
        
        // Exposiciones publicadas
        $stats['publicadas'] = $this->contar(['publicada' => 1]);
        
        // Exposiciones activas
        $stats['activas'] = $this->contar(['activa' => 1, 'publicada' => 1]);
        
        // Exposiciones destacadas
        $stats['destacadas'] = $this->contar(['destacada' => 1]);
        
        // Exposiciones actuales (en curso)
        $fechaHoy = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM {$this->tabla} 
                WHERE publicada = 1 AND activa = 1 
                AND fecha_inicio <= :fecha_hoy AND fecha_fin >= :fecha_hoy";
        $resultado = $this->bd->seleccionarUno($sql, ['fecha_hoy' => $fechaHoy]);
        $stats['en_curso'] = (int)($resultado['total'] ?? 0);
        
        // Exposiciones por categoría
        $sql = "SELECT categoria, COUNT(*) as total
                FROM {$this->tabla}
                WHERE publicada = 1 AND activa = 1
                GROUP BY categoria
                ORDER BY total DESC";
        $stats['por_categoria'] = $this->bd->seleccionar($sql);
        
        return $stats;
    }
    
    /**
     * Agregar galería de imágenes a una exposición
     * @param int $id ID de la exposición
     * @param array $imagenes Array de URLs de imágenes
     * @return bool True si se actualizó correctamente
     */
    public function actualizarGalería(int $id, array $imagenes): bool {
        $galeria = json_encode($imagenes);
        return $this->actualizar($id, ['galeria_imagenes' => $galeria]);
    }
    
    /**
     * Obtener galería de imágenes de una exposición
     * @param int $id ID de la exposición
     * @return array Array de URLs de imágenes
     */
    public function obtenerGaleria(int $id): array {
        $exposicion = $this->obtenerPorId($id);
        if (!$exposicion || empty($exposicion['galeria_imagenes'])) {
            return [];
        }
        
        return json_decode($exposicion['galeria_imagenes'], true) ?? [];
    }
    
    /**
     * Crear exposición específica con datos completos
     * @param array $datos Datos de la exposición
     * @return int ID de la exposición creada
     * @throws Exception Si hay error en la creación
     */
    public function crear(array $datos): int {
        // Establecer valores por defecto
        $datos['activa'] = $datos['activa'] ?? 1;
        $datos['publicada'] = $datos['publicada'] ?? 0;
        $datos['destacada'] = $datos['destacada'] ?? 0;
        $datos['precio_entrada'] = $datos['precio_entrada'] ?? 0.00;
        $datos['fecha_creacion'] = $datos['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $datos['fecha_actualizacion'] = $datos['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        
        // Validar fechas
        if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
                throw new Exception("La fecha de inicio no puede ser posterior a la fecha de fin");
            }
        }
        
        return parent::crear($datos);
    }
    
    /**
     * Actualizar exposición con fecha de actualización automática
     * @param int $id ID de la exposición
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     * @throws Exception Si hay error en la actualización
     */
    public function actualizar(int $id, array $datos): bool {
        // Establecer fecha de actualización automáticamente
        $datos['fecha_actualizacion'] = date('Y-m-d H:i:s');
        
        // Validar fechas si se están actualizando
        if (isset($datos['fecha_inicio']) || isset($datos['fecha_fin'])) {
            $exposicionActual = $this->obtenerPorId($id);
            $fechaInicio = $datos['fecha_inicio'] ?? $exposicionActual['fecha_inicio'];
            $fechaFin = $datos['fecha_fin'] ?? $exposicionActual['fecha_fin'];
            
            if ($fechaInicio > $fechaFin) {
                throw new Exception("La fecha de inicio no puede ser posterior a la fecha de fin");
            }
        }
        
        return parent::actualizar($id, $datos);
    }
}
