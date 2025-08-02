<?php
/**
 * Controlador público para artículos del blog
 * 
 * Maneja la visualización pública de artículos para todos los usuarios,
 * incluyendo invitados.
 * 
 * @author Sistema de Gestión de Exposiciones
 * @version 1.0
 */

namespace Controladores;

use Exception;
use Modelos\Articulo;

/**
 * Clase BlogControlador
 * 
 * Controlador para la gestión pública de artículos del blog
 */
class BlogControlador extends ControladorBase {
    
    /**
     * Muestra la lista paginada de artículos publicados
     * 
     * @param int $pagina Número de página para la paginación
     * @return void
     */
    public function listarArticulos(int $pagina = 1): void {
        try {
            $limite = 8;
            $offset = ($pagina - 1) * $limite;
            
            // Obtener solo artículos publicados
            $articulos = $this->obtenerArticulosPublicados($limite, $offset);
            $totalArticulos = $this->contarArticulosPublicados();
            $totalPaginas = ceil($totalArticulos / $limite);
            
            // Obtener artículos destacados para sidebar
            $articulosDestacados = $this->obtenerArticulosDestacados(3);
            
            $datos = [
                'articulos' => $articulos,
                'articulosDestacados' => $articulosDestacados,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalArticulos' => $totalArticulos,
                'titulo' => 'Blog - Artículos'
            ];
            
            $this->renderizar('publicas/blog/lista', $datos);
            
        } catch (Exception $e) {
            error_log("Error al mostrar lista de artículos: " . $e->getMessage());
            $this->mostrarError404();
        }
    }
    
    /**
     * Muestra un artículo individual por su slug
     * 
     * @param string $slug Slug del artículo
     * @return void
     */
    public function mostrarArticulo(string $slug): void {
        try {
            $articulo = $this->obtenerArticuloPorSlug($slug);
            
            if (!$articulo) {
                $this->mostrarError404();
                return;
            }
            
            // Verificar que el artículo esté publicado o sea visible
            if ($articulo['estado'] !== 'publicado') {
                $this->mostrarError404();
                return;
            }
            
            // Incrementar contador de visitas
            $this->incrementarVisitas($articulo['id']);
            
            // Obtener artículos relacionados
            $articulosRelacionados = $this->obtenerArticulosRelacionados(
                $articulo['categoria'], 
                $articulo['id'], 
                3
            );
            
            // Procesar tags para mostrar
            $tags = [];
            if (!empty($articulo['tags'])) {
                $tags = json_decode($articulo['tags'], true) ?? [];
            }
            
            $datos = [
                'articulo' => $articulo,
                'tags' => $tags,
                'articulosRelacionados' => $articulosRelacionados,
                'titulo' => $articulo['titulo']
            ];
            
            $this->renderizar('publicas/blog/articulo', $datos);
            
        } catch (Exception $e) {
            error_log("Error al mostrar artículo: " . $e->getMessage());
            $this->mostrarError404();
        }
    }
    
    /**
     * Muestra artículos por categoría
     * 
     * @param string $categoria Categoría de los artículos
     * @param int $pagina Número de página para la paginación
     * @return void
     */
    public function mostrarPorCategoria(string $categoria, int $pagina = 1): void {
        try {
            // Verificar que la categoría sea válida
            $categoriasValidas = $this->obtenerCategoriasDisponibles();
            if (!array_key_exists($categoria, $categoriasValidas)) {
                $this->mostrarError404();
                return;
            }
            
            $limite = 8;
            $offset = ($pagina - 1) * $limite;
            
            $articulos = $this->obtenerArticulosPorCategoria($categoria, $limite, $offset);
            $totalArticulos = $this->contarArticulosPorCategoria($categoria);
            $totalPaginas = ceil($totalArticulos / $limite);
            
            $datos = [
                'articulos' => $articulos,
                'categoria' => $categoria,
                'nombreCategoria' => $categoriasValidas[$categoria],
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalArticulos' => $totalArticulos,
                'titulo' => "Blog - {$categoriasValidas[$categoria]}"
            ];
            
            $this->renderizar('publicas/blog/categoria', $datos);
            
        } catch (Exception $e) {
            error_log("Error al mostrar categoría: " . $e->getMessage());
            $this->mostrarError404();
        }
    }
    
    /**
     * Búsqueda de artículos
     * 
     * @param string $termino Término de búsqueda
     * @param int $pagina Número de página
     * @return void
     */
    public function buscar(string $termino, int $pagina = 1): void {
        try {
            if (empty(trim($termino))) {
                $this->redirigir('/blog');
                return;
            }
            
            $limite = 8;
            $offset = ($pagina - 1) * $limite;
            
            $articulos = $this->buscarArticulos($termino, $limite, $offset);
            $totalArticulos = $this->contarResultadosBusqueda($termino);
            $totalPaginas = ceil($totalArticulos / $limite);
            
            $datos = [
                'articulos' => $articulos,
                'termino' => $termino,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalArticulos' => $totalArticulos,
                'titulo' => "Búsqueda: {$termino}"
            ];
            
            $this->renderizar('publicas/blog/busqueda', $datos);
            
        } catch (Exception $e) {
            error_log("Error en búsqueda: " . $e->getMessage());
            $this->mostrarError404();
        }
    }
    
    /**
     * Obtiene artículos publicados con información del autor
     * 
     * @param int $limite Número de artículos
     * @param int $offset Desplazamiento
     * @return array Array de artículos
     */
    private function obtenerArticulosPublicados(int $limite, int $offset): array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    WHERE a.estado = 'publicado' 
                    AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                    ORDER BY a.fecha_publicacion DESC, a.fecha_creacion DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$limite, $offset]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener artículos publicados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cuenta el total de artículos publicados
     * 
     * @return int Número de artículos publicados
     */
    private function contarArticulosPublicados(): int {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT COUNT(*) FROM articulos 
                    WHERE estado = 'publicado' 
                    AND (fecha_publicacion IS NULL OR fecha_publicacion <= NOW())";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
            
        } catch (Exception $e) {
            error_log("Error al contar artículos publicados: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtiene artículos destacados
     * 
     * @param int $limite Número de artículos destacados
     * @return array Array de artículos destacados
     */
    private function obtenerArticulosDestacados(int $limite): array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    WHERE a.estado = 'publicado' 
                    AND a.destacado = 1
                    AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                    ORDER BY a.fecha_publicacion DESC, a.fecha_creacion DESC 
                    LIMIT ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$limite]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener artículos destacados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un artículo por su slug
     * 
     * @param string $slug Slug del artículo
     * @return array|null Datos del artículo o null si no existe
     */
    private function obtenerArticuloPorSlug(string $slug): ?array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    WHERE a.slug = ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$slug]);
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $resultado ?: null;
            
        } catch (Exception $e) {
            error_log("Error al obtener artículo por slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Incrementa el contador de visitas de un artículo
     * 
     * @param int $articuloId ID del artículo
     * @return void
     */
    private function incrementarVisitas(int $articuloId): void {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "UPDATE articulos SET contador_visitas = contador_visitas + 1 WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$articuloId]);
            
        } catch (Exception $e) {
            error_log("Error al incrementar visitas: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene artículos relacionados por categoría
     * 
     * @param string $categoria Categoría del artículo actual
     * @param int $articuloActualId ID del artículo actual para excluir
     * @param int $limite Número de artículos relacionados
     * @return array Array de artículos relacionados
     */
    private function obtenerArticulosRelacionados(string $categoria, int $articuloActualId, int $limite): array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    WHERE a.categoria = ? 
                    AND a.id != ? 
                    AND a.estado = 'publicado'
                    AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                    ORDER BY a.fecha_publicacion DESC, a.fecha_creacion DESC 
                    LIMIT ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$categoria, $articuloActualId, $limite]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener artículos relacionados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene artículos por categoría
     * 
     * @param string $categoria Categoría de los artículos
     * @param int $limite Número de artículos
     * @param int $offset Desplazamiento
     * @return array Array de artículos
     */
    private function obtenerArticulosPorCategoria(string $categoria, int $limite, int $offset): array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    WHERE a.categoria = ? 
                    AND a.estado = 'publicado'
                    AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                    ORDER BY a.fecha_publicacion DESC, a.fecha_creacion DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$categoria, $limite, $offset]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener artículos por categoría: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cuenta artículos por categoría
     * 
     * @param string $categoria Categoría de los artículos
     * @return int Número de artículos
     */
    private function contarArticulosPorCategoria(string $categoria): int {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT COUNT(*) FROM articulos 
                    WHERE categoria = ? 
                    AND estado = 'publicado'
                    AND (fecha_publicacion IS NULL OR fecha_publicacion <= NOW())";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$categoria]);
            return (int) $stmt->fetchColumn();
            
        } catch (Exception $e) {
            error_log("Error al contar artículos por categoría: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Busca artículos por término
     * 
     * @param string $termino Término de búsqueda
     * @param int $limite Número de artículos
     * @param int $offset Desplazamiento
     * @return array Array de artículos
     */
    private function buscarArticulos(string $termino, int $limite, int $offset): array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $terminoBusqueda = "%{$termino}%";
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    WHERE (a.titulo LIKE ? OR a.contenido_texto LIKE ? OR a.resumen LIKE ?)
                    AND a.estado = 'publicado'
                    AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
                    ORDER BY a.fecha_publicacion DESC, a.fecha_creacion DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$terminoBusqueda, $terminoBusqueda, $terminoBusqueda, $limite, $offset]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al buscar artículos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cuenta resultados de búsqueda
     * 
     * @param string $termino Término de búsqueda
     * @return int Número de resultados
     */
    private function contarResultadosBusqueda(string $termino): int {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $terminoBusqueda = "%{$termino}%";
            
            $sql = "SELECT COUNT(*) FROM articulos 
                    WHERE (titulo LIKE ? OR contenido_texto LIKE ? OR resumen LIKE ?)
                    AND estado = 'publicado'
                    AND (fecha_publicacion IS NULL OR fecha_publicacion <= NOW())";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            return (int) $stmt->fetchColumn();
            
        } catch (Exception $e) {
            error_log("Error al contar resultados de búsqueda: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtiene las categorías disponibles
     * 
     * @return array Array de categorías
     */
    private function obtenerCategoriasDisponibles(): array {
        return [
            'noticias' => 'Noticias',
            'exposiciones' => 'Exposiciones',
            'arte' => 'Arte',
            'cultura' => 'Cultura',
            'historia' => 'Historia',
            'educacion' => 'Educación',
            'eventos' => 'Eventos',
            'entrevistas' => 'Entrevistas',
            'opinion' => 'Opinión',
            'tecnica' => 'Técnica',
            'otros' => 'Otros'
        ];
    }
}
