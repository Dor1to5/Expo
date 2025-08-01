<?php
/**
 * Modelo Articulo - Gestión de artículos del blog
 * 
 * Esta clase maneja todas las operaciones relacionadas con los artículos
 * del blog, tanto para el área pública como para la administración.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Modelos;

use Exception;

/**
 * Clase Articulo - Modelo para la gestión de artículos del blog
 */
class Articulo extends ModeloBase {
    
    /**
     * Nombre de la tabla en la base de datos
     * @var string
     */
    protected string $tabla = 'articulos';
    
    /**
     * Campos que se pueden llenar masivamente
     * @var array
     */
    protected array $camposLlenables = [
        'titulo',
        'slug',
        'contenido',
        'resumen',
        'imagen_destacada',
        'publicado',
        'destacado',
        'categoria',
        'etiquetas',
        'fecha_publicacion',
        'usuario_autor_id',
        'visitas',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    /**
     * Reglas de validación para los campos
     * @var array
     */
    protected array $reglasValidacion = [
        'titulo' => ['requerido', 'min:5', 'max:255'],
        'slug' => ['requerido', 'min:5', 'max:255', 'unico'],
        'contenido' => ['requerido', 'min:50'],
        'resumen' => ['max:500'],
        'usuario_autor_id' => ['requerido']
    ];
    
    /**
     * Categorías disponibles para artículos
     * @var array
     */
    private static array $categorias = [
        'noticias' => 'Noticias',
        'eventos' => 'Eventos',
        'arte' => 'Arte',
        'cultura' => 'Cultura',
        'exposiciones' => 'Exposiciones',
        'entrevistas' => 'Entrevistas',
        'reseñas' => 'Reseñas',
        'opinion' => 'Opinión',
        'educacion' => 'Educación',
        'historia' => 'Historia'
    ];
    
    /**
     * Obtiene artículos públicos (publicados)
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @param array $filtros Filtros adicionales
     * @return array Lista de artículos públicos
     */
    public function obtenerPublicos(int $limite = 0, int $offset = 0, array $filtros = []): array {
        $sql = "SELECT a.*, u.nombre_completo as nombre_autor
                FROM {$this->tabla} a
                LEFT JOIN usuarios u ON a.usuario_autor_id = u.id
                WHERE a.publicado = 1";
        
        $parametros = [];
        
        // Filtrar por categoría
        if (!empty($filtros['categoria'])) {
            $sql .= " AND a.categoria = :categoria";
            $parametros['categoria'] = $filtros['categoria'];
        }
        
        // Filtrar solo destacados
        if (!empty($filtros['destacados'])) {
            $sql .= " AND a.destacado = 1";
        }
        
        // Filtrar por fecha de publicación (solo publicados)
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND a.fecha_publicacion >= :fecha_desde";
            $parametros['fecha_desde'] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND a.fecha_publicacion <= :fecha_hasta";
            $parametros['fecha_hasta'] = $filtros['fecha_hasta'];
        }
        
        // Búsqueda por texto
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (a.titulo LIKE :busqueda OR a.contenido LIKE :busqueda OR a.resumen LIKE :busqueda)";
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        // Solo artículos con fecha de publicación pasada o actual
        $sql .= " AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())";
        
        // Ordenar por fecha de publicación descendente
        $sql .= " ORDER BY a.destacado DESC, a.fecha_publicacion DESC";
        
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
     * Obtiene un artículo público por slug
     * @param string $slug Slug del artículo
     * @return array|null Datos del artículo o null si no existe/no es público
     */
    public function obtenerPublicoPorSlug(string $slug): ?array {
        $sql = "SELECT a.*, u.nombre_completo as nombre_autor
                FROM {$this->tabla} a
                LEFT JOIN usuarios u ON a.usuario_autor_id = u.id
                WHERE a.slug = :slug AND a.publicado = 1
                AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                LIMIT 1";
        
        $articulo = $this->bd->seleccionarUno($sql, ['slug' => $slug]);
        
        // Incrementar contador de visitas si el artículo existe
        if ($articulo) {
            $this->incrementarVisitas($articulo['id']);
        }
        
        return $articulo;
    }
    
    /**
     * Obtiene un artículo público por ID
     * @param int $id ID del artículo
     * @return array|null Datos del artículo o null si no existe/no es público
     */
    public function obtenerPublicoPorId(int $id): ?array {
        $sql = "SELECT a.*, u.nombre_completo as nombre_autor
                FROM {$this->tabla} a
                LEFT JOIN usuarios u ON a.usuario_autor_id = u.id
                WHERE a.id = :id AND a.publicado = 1
                AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                LIMIT 1";
        
        $articulo = $this->bd->seleccionarUno($sql, ['id' => $id]);
        
        // Incrementar contador de visitas si el artículo existe
        if ($articulo) {
            $this->incrementarVisitas($id);
        }
        
        return $articulo;
    }
    
    /**
     * Obtiene artículos para administración con información del autor
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @param array $filtros Filtros adicionales
     * @return array Lista de artículos con datos del autor
     */
    public function obtenerParaAdmin(int $limite = 0, int $offset = 0, array $filtros = []): array {
        $sql = "SELECT a.*, u.nombre_completo as nombre_autor
                FROM {$this->tabla} a
                LEFT JOIN usuarios u ON a.usuario_autor_id = u.id";
        
        $parametros = [];
        $condicionesWhere = [];
        
        // Filtrar por estado de publicación
        if (isset($filtros['publicado'])) {
            $condicionesWhere[] = "a.publicado = :publicado";
            $parametros['publicado'] = $filtros['publicado'];
        }
        
        // Filtrar por categoría
        if (!empty($filtros['categoria'])) {
            $condicionesWhere[] = "a.categoria = :categoria";
            $parametros['categoria'] = $filtros['categoria'];
        }
        
        // Filtrar por autor
        if (!empty($filtros['usuario_autor_id'])) {
            $condicionesWhere[] = "a.usuario_autor_id = :usuario_autor_id";
            $parametros['usuario_autor_id'] = $filtros['usuario_autor_id'];
        }
        
        // Búsqueda por texto
        if (!empty($filtros['busqueda'])) {
            $condicionesWhere[] = "(a.titulo LIKE :busqueda OR a.contenido LIKE :busqueda)";
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        // Agregar condiciones WHERE si existen
        if (!empty($condicionesWhere)) {
            $sql .= " WHERE " . implode(' AND ', $condicionesWhere);
        }
        
        // Ordenar por fecha de creación descendente
        $sql .= " ORDER BY a.fecha_creacion DESC";
        
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
     * Obtiene artículos destacados para mostrar en página principal
     * @param int $limite Límite de artículos destacados
     * @return array Lista de artículos destacados
     */
    public function obtenerDestacados(int $limite = 3): array {
        return $this->obtenerPublicos($limite, 0, ['destacados' => true]);
    }
    
    /**
     * Obtiene artículos recientes
     * @param int $limite Límite de artículos recientes
     * @return array Lista de artículos recientes
     */
    public function obtenerRecientes(int $limite = 5): array {
        return $this->obtenerPublicos($limite);
    }
    
    /**
     * Obtiene artículos relacionados basados en categoría y etiquetas
     * @param int $idArticulo ID del artículo actual
     * @param int $limite Límite de artículos relacionados
     * @return array Lista de artículos relacionados
     */
    public function obtenerRelacionados(int $idArticulo, int $limite = 3): array {
        $articuloActual = $this->obtenerPorId($idArticulo);
        if (!$articuloActual) {
            return [];
        }
        
        $sql = "SELECT a.*, u.nombre_completo as nombre_autor
                FROM {$this->tabla} a
                LEFT JOIN usuarios u ON a.usuario_autor_id = u.id
                WHERE a.publicado = 1 AND a.id != :id_actual
                AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                AND a.categoria = :categoria
                ORDER BY a.fecha_publicacion DESC
                LIMIT {$limite}";
        
        return $this->bd->seleccionar($sql, [
            'id_actual' => $idArticulo,
            'categoria' => $articuloActual['categoria']
        ]);
    }
    
    /**
     * Cambia el estado de publicación de un artículo
     * @param int $id ID del artículo
     * @param bool $publicado Estado de publicación
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoPublicacion(int $id, bool $publicado): bool {
        $datos = ['publicado' => $publicado ? 1 : 0];
        
        // Si se está publicando y no hay fecha de publicación, establecer la actual
        if ($publicado) {
            $articulo = $this->obtenerPorId($id);
            if (!$articulo['fecha_publicacion']) {
                $datos['fecha_publicacion'] = date('Y-m-d H:i:s');
            }
        }
        
        return $this->actualizar($id, $datos);
    }
    
    /**
     * Marca un artículo como destacado o no destacado
     * @param int $id ID del artículo
     * @param bool $destacado Estado destacado
     * @return bool True si se actualizó correctamente
     */
    public function cambiarEstadoDestacado(int $id, bool $destacado): bool {
        return $this->actualizar($id, [
            'destacado' => $destacado ? 1 : 0
        ]);
    }
    
    /**
     * Genera un slug único basado en el título
     * @param string $titulo Título del artículo
     * @param int|null $idExcluir ID a excluir en la verificación de unicidad
     * @return string Slug único generado
     */
    public function generarSlugUnico(string $titulo, ?int $idExcluir = null): string {
        // Generar slug base
        $slug = $this->generarSlugBase($titulo);
        $slugOriginal = $slug;
        $contador = 1;
        
        // Verificar unicidad y agregar número si es necesario
        while ($this->existeSlug($slug, $idExcluir)) {
            $slug = $slugOriginal . '-' . $contador;
            $contador++;
        }
        
        return $slug;
    }
    
    /**
     * Verifica si existe un slug en la base de datos
     * @param string $slug Slug a verificar
     * @param int|null $idExcluir ID a excluir de la verificación
     * @return bool True si el slug existe
     */
    private function existeSlug(string $slug, ?int $idExcluir = null): bool {
        $sql = "SELECT COUNT(*) as total FROM {$this->tabla} WHERE slug = :slug";
        $parametros = ['slug' => $slug];
        
        if ($idExcluir !== null) {
            $sql .= " AND id != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }
        
        $resultado = $this->bd->seleccionarUno($sql, $parametros);
        return ($resultado['total'] ?? 0) > 0;
    }
    
    /**
     * Genera un slug base a partir del título
     * @param string $titulo Título del artículo
     * @return string Slug base generado
     */
    private function generarSlugBase(string $titulo): string {
        // Convertir a minúsculas
        $slug = strtolower($titulo);
        
        // Reemplazar caracteres especiales españoles
        $equivalencias = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ü' => 'u', 'ç' => 'c'
        ];
        $slug = strtr($slug, $equivalencias);
        
        // Reemplazar espacios y caracteres especiales con guiones
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Eliminar guiones al inicio y final
        $slug = trim($slug, '-');
        
        // Limitar longitud
        return substr($slug, 0, 200);
    }
    
    /**
     * Incrementa el contador de visitas de un artículo
     * @param int $id ID del artículo
     */
    private function incrementarVisitas(int $id): void {
        try {
            $sql = "UPDATE {$this->tabla} SET visitas = visitas + 1 WHERE id = :id";
            $this->bd->ejecutar($sql, ['id' => $id]);
        } catch (Exception $e) {
            // Log del error pero no fallar la consulta principal
            error_log("Error al incrementar visitas: " . $e->getMessage());
        }
    }
    
    /**
     * Busca artículos por texto en título y contenido
     * @param string $termino Término de búsqueda
     * @param int $limite Límite de resultados
     * @return array Resultados de la búsqueda
     */
    public function buscar(string $termino, int $limite = 10): array {
        return $this->obtenerPublicos($limite, 0, ['busqueda' => $termino]);
    }
    
    /**
     * Obtiene artículos por categoría
     * @param string $categoria Categoría a filtrar
     * @param int $limite Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de artículos de la categoría
     */
    public function obtenerPorCategoria(string $categoria, int $limite = 0, int $offset = 0): array {
        return $this->obtenerPublicos($limite, $offset, ['categoria' => $categoria]);
    }
    
    /**
     * Obtiene estadísticas de artículos
     * @return array Estadísticas de artículos
     */
    public function obtenerEstadisticas(): array {
        $stats = [];
        
        // Total de artículos
        $stats['total'] = $this->contar();
        
        // Artículos publicados
        $stats['publicados'] = $this->contar(['publicado' => 1]);
        
        // Artículos destacados
        $stats['destacados'] = $this->contar(['destacado' => 1]);
        
        // Artículos borradores
        $stats['borradores'] = $this->contar(['publicado' => 0]);
        
        // Artículo más visitado
        $sql = "SELECT titulo, visitas FROM {$this->tabla} 
                WHERE publicado = 1 
                ORDER BY visitas DESC 
                LIMIT 1";
        $resultado = $this->bd->seleccionarUno($sql);
        $stats['mas_visitado'] = $resultado ?? ['titulo' => 'N/A', 'visitas' => 0];
        
        // Total de visitas
        $sql = "SELECT SUM(visitas) as total_visitas FROM {$this->tabla} WHERE publicado = 1";
        $resultado = $this->bd->seleccionarUno($sql);
        $stats['total_visitas'] = (int)($resultado['total_visitas'] ?? 0);
        
        // Artículos por categoría
        $sql = "SELECT categoria, COUNT(*) as total
                FROM {$this->tabla}
                WHERE publicado = 1
                GROUP BY categoria
                ORDER BY total DESC";
        $stats['por_categoria'] = $this->bd->seleccionar($sql);
        
        return $stats;
    }
    
    /**
     * Obtiene categorías disponibles
     * @return array Array de categorías
     */
    public static function obtenerCategorias(): array {
        return self::$categorias;
    }
    
    /**
     * Obtiene los artículos más populares por visitas
     * @param int $limite Límite de artículos populares
     * @return array Lista de artículos más visitados
     */
    public function obtenerMasPopulares(int $limite = 5): array {
        $sql = "SELECT a.*, u.nombre_completo as nombre_autor
                FROM {$this->tabla} a
                LEFT JOIN usuarios u ON a.usuario_autor_id = u.id
                WHERE a.publicado = 1
                AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                ORDER BY a.visitas DESC
                LIMIT {$limite}";
        
        return $this->bd->seleccionar($sql);
    }
    
    /**
     * Crear artículo específico con datos completos
     * @param array $datos Datos del artículo
     * @return int ID del artículo creado
     * @throws Exception Si hay error en la creación
     */
    public function crear(array $datos): int {
        // Generar slug si no se proporciona
        if (empty($datos['slug']) && !empty($datos['titulo'])) {
            $datos['slug'] = $this->generarSlugUnico($datos['titulo']);
        }
        
        // Establecer valores por defecto
        $datos['publicado'] = $datos['publicado'] ?? 0;
        $datos['destacado'] = $datos['destacado'] ?? 0;
        $datos['visitas'] = 0;
        $datos['fecha_creacion'] = $datos['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $datos['fecha_actualizacion'] = $datos['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        
        return parent::crear($datos);
    }
    
    /**
     * Actualizar artículo con fecha de actualización automática
     * @param int $id ID del artículo
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     * @throws Exception Si hay error en la actualización
     */
    public function actualizar(int $id, array $datos): bool {
        // Actualizar slug si se cambia el título
        if (!empty($datos['titulo']) && empty($datos['slug'])) {
            $datos['slug'] = $this->generarSlugUnico($datos['titulo'], $id);
        }
        
        // Establecer fecha de actualización automáticamente
        $datos['fecha_actualizacion'] = date('Y-m-d H:i:s');
        
        return parent::actualizar($id, $datos);
    }
}
