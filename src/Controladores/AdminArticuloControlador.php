<?php
/**
 * Controlador de administración de artículos
 * 
 * Maneja todas las operaciones CRUD para artículos en el panel administrativo,
 * incluyendo validaciones, mensajes flash y verificación de permisos.
 * 
 * @author Sistema de Gestión de Exposiciones
 * @version 1.0
 */

namespace Controladores;

use Exception;
use Modelos\Articulo;
use Modelos\Usuario;

/**
 * Clase AdminArticuloControlador
 * 
 * Controlador especializado en la gestión administrativa de artículos
 */
class AdminArticuloControlador extends ControladorBase {
    
    /**
     * Muestra la lista paginada de artículos
     * 
     * @param int $pagina Número de página para la paginación
     * @return void
     */
    public function listar(int $pagina = 1): void {
        try {
            $this->verificarPermiso('articulos.listar');
            
            $articuloModelo = new Articulo();
            $limite = 10;
            $offset = ($pagina - 1) * $limite;
            
            // Obtener artículos con información del autor
            $articulos = $this->obtenerArticulosConAutor($limite, $offset);
            $totalArticulos = $articuloModelo->contarTodos();
            $totalPaginas = ceil($totalArticulos / $limite);
            
            $datos = [
                'articulos' => $articulos,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalArticulos' => $totalArticulos
            ];
            
            $this->renderizar('admin/articulos/listar', $datos);
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al cargar artículos: ' . $e->getMessage());
            $this->redirigir('/admin');
        }
    }
    
    /**
     * Muestra el formulario para crear un nuevo artículo
     * 
     * @return void
     */
    public function mostrarCrear(): void {
        try {
            $this->verificarPermiso('articulos.crear');
            
            // Obtener categorías y estados disponibles
            $categorias = $this->obtenerCategoriasArticulo();
            $estados = $this->obtenerEstadosArticulo();
            
            $datos = [
                'categorias' => $categorias,
                'estados' => $estados,
                'accion' => 'crear'
            ];
            
            $this->renderizar('admin/articulos/crear', $datos);
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al mostrar formulario: ' . $e->getMessage());
            $this->redirigir('/admin/articulos');
        }
    }
    
    /**
     * Procesa la creación de un nuevo artículo
     * 
     * @return void
     */
    public function procesarCrear(): void {
        try {
            $this->verificarPermiso('articulos.crear');
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'titulo',
                'resumen',
                'contenido',
                'categoria',
                'tags',
                'imagen_destacada',
                'estado',
                'destacado',
                'permitir_comentarios',
                'fecha_publicacion',
                'autor_invitado'
            ]);
            
            // Validar datos
            $this->validarDatosArticulo($datos);
            
            // Agregar datos adicionales
            $datos['slug'] = $this->generarSlugArticulo($datos['titulo']);
            $datos['autor_id'] = $_SESSION['usuario_id'];
            $datos['contenido_texto'] = strip_tags($datos['contenido']);
            $datos['tiempo_lectura'] = $this->calcularTiempoLectura($datos['contenido']);
            $datos['destacado'] = isset($datos['destacado']) ? 1 : 0;
            $datos['permitir_comentarios'] = isset($datos['permitir_comentarios']) ? 1 : 0;
            
            // Procesar tags
            if (!empty($datos['tags'])) {
                $datos['tags'] = json_encode(array_map('trim', explode(',', $datos['tags'])));
            }
            
            // Crear artículo
            if ($this->crearArticulo($datos)) {
                $this->añadirMensajeFlash('exito', "✅ Artículo '{$datos['titulo']}' creado exitosamente");
                $this->añadirMensajeFlash('info', "📝 Categoría: {$this->obtenerNombreCategoria($datos['categoria'])} | 📊 Estado: {$this->obtenerNombreEstado($datos['estado'])}");
                $this->redirigir('/admin/articulos');
            } else {
                throw new Exception('No se pudo crear el artículo en la base de datos');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al crear artículo: ' . $e->getMessage());
            $this->redirigir('/admin/articulos/crear');
        }
    }
    
    /**
     * Muestra el formulario para editar un artículo existente
     * 
     * @param int $id ID del artículo a editar
     * @return void
     */
    public function mostrarEditar(int $id): void {
        try {
            $this->verificarPermiso('articulos.editar');
            
            $articuloModelo = new Articulo();
            $articulo = $articuloModelo->obtenerPorId($id);
            
            if (!$articulo) {
                throw new Exception('Artículo no encontrado');
            }
            
            // Procesar tags para mostrar en el formulario
            if (!empty($articulo['tags'])) {
                $tags = json_decode($articulo['tags'], true);
                $articulo['tags_string'] = implode(', ', $tags);
            } else {
                $articulo['tags_string'] = '';
            }
            
            $categorias = $this->obtenerCategoriasArticulo();
            $estados = $this->obtenerEstadosArticulo();
            
            $datos = [
                'articulo' => $articulo,
                'categorias' => $categorias,
                'estados' => $estados,
                'accion' => 'editar'
            ];
            
            $this->renderizar('admin/articulos/editar', $datos);
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al cargar artículo: ' . $e->getMessage());
            $this->redirigir('/admin/articulos');
        }
    }
    
    /**
     * Procesa la actualización de un artículo
     * 
     * @param int $id ID del artículo a actualizar
     * @return void
     */
    public function procesarEditar(int $id): void {
        try {
            $this->verificarPermiso('articulos.editar');
            
            // Verificar que el artículo existe
            $articuloModelo = new Articulo();
            $articuloExistente = $articuloModelo->obtenerPorId($id);
            
            if (!$articuloExistente) {
                throw new Exception('Artículo no encontrado');
            }
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'titulo',
                'resumen',
                'contenido',
                'categoria',
                'tags',
                'imagen_destacada',
                'estado',
                'destacado',
                'permitir_comentarios',
                'fecha_publicacion',
                'autor_invitado'
            ]);
            
            // Validar datos
            $this->validarDatosArticulo($datos, $id);
            
            // Actualizar slug si cambió el título
            if ($datos['titulo'] !== $articuloExistente['titulo']) {
                $datos['slug'] = $this->generarSlugArticulo($datos['titulo']);
            }
            
            $datos['contenido_texto'] = strip_tags($datos['contenido']);
            $datos['tiempo_lectura'] = $this->calcularTiempoLectura($datos['contenido']);
            $datos['destacado'] = isset($datos['destacado']) ? 1 : 0;
            $datos['permitir_comentarios'] = isset($datos['permitir_comentarios']) ? 1 : 0;
            
            // Procesar tags
            if (!empty($datos['tags'])) {
                $datos['tags'] = json_encode(array_map('trim', explode(',', $datos['tags'])));
            } else {
                $datos['tags'] = null;
            }
            
            // Actualizar artículo
            if ($articuloModelo->actualizar($id, $datos)) {
                $this->añadirMensajeFlash('exito', "✅ Artículo '{$datos['titulo']}' actualizado exitosamente");
                $this->redirigir('/admin/articulos');
            } else {
                throw new Exception('No se pudo actualizar el artículo');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al actualizar artículo: ' . $e->getMessage());
            $this->redirigir("/admin/articulos/{$id}/editar");
        }
    }
    
    /**
     * Elimina un artículo tras confirmación
     * 
     * @param int $id ID del artículo a eliminar
     * @return void
     */
    public function eliminar(int $id): void {
        try {
            $this->verificarPermiso('articulos.eliminar');
            
            $articuloModelo = new Articulo();
            $articulo = $articuloModelo->obtenerPorId($id);
            
            if (!$articulo) {
                throw new Exception('Artículo no encontrado');
            }
            
            if ($articuloModelo->eliminar($id)) {
                $this->añadirMensajeFlash('exito', "✅ Artículo '{$articulo['titulo']}' eliminado exitosamente");
            } else {
                throw new Exception('No se pudo eliminar el artículo');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al eliminar artículo: ' . $e->getMessage());
        }
        
        $this->redirigir('/admin/articulos');
    }
    
    /**
     * Valida los datos de un artículo
     * 
     * @param array $datos Datos a validar
     * @param int|null $idExcluir ID a excluir de la validación de slug único
     * @return void
     * @throws Exception Si los datos no son válidos
     */
    private function validarDatosArticulo(array $datos, ?int $idExcluir = null): void {
        // Validar campos requeridos
        if (empty($datos['titulo'])) {
            throw new Exception('📝 El título es obligatorio');
        }
        
        if (empty($datos['contenido'])) {
            throw new Exception('📝 El contenido es obligatorio');
        }
        
        if (empty($datos['categoria'])) {
            throw new Exception('📂 La categoría es obligatoria');
        }
        
        if (empty($datos['estado'])) {
            throw new Exception('📊 El estado es obligatorio');
        }
        
        // Validar longitud de campos
        if (strlen($datos['titulo']) > 255) {
            throw new Exception('📝 El título no puede exceder 255 caracteres');
        }
        
        if (isset($datos['resumen']) && strlen($datos['resumen']) > 500) {
            throw new Exception('📝 El resumen no puede exceder 500 caracteres');
        }
        
        // Validar fecha de publicación si está programada
        if ($datos['estado'] === 'programado' && empty($datos['fecha_publicacion'])) {
            throw new Exception('📅 La fecha de publicación es obligatoria para artículos programados');
        }
        
        if (!empty($datos['fecha_publicacion'])) {
            $fecha = strtotime($datos['fecha_publicacion']);
            if ($fecha === false) {
                throw new Exception('📅 La fecha de publicación no tiene un formato válido');
            }
        }
        
        // Validar categoría
        $categoriasValidas = array_keys($this->obtenerCategoriasArticulo());
        if (!in_array($datos['categoria'], $categoriasValidas)) {
            throw new Exception('📂 La categoría seleccionada no es válida');
        }
        
        // Validar estado
        $estadosValidos = array_keys($this->obtenerEstadosArticulo());
        if (!in_array($datos['estado'], $estadosValidos)) {
            throw new Exception('📊 El estado seleccionado no es válido');
        }
        
        // Validar slug único
        $slug = $this->generarSlugArticulo($datos['titulo']);
        if ($this->slugArticuloExiste($slug, $idExcluir)) {
            throw new Exception('📝 Ya existe un artículo con un título similar');
        }
    }
    
    /**
     * Crea un nuevo artículo en la base de datos
     * 
     * @param array $datos Datos del artículo
     * @return bool True si se creó exitosamente
     */
    private function crearArticulo(array $datos): bool {
        try {
            $articuloModelo = new Articulo();
            return $articuloModelo->crear($datos);
        } catch (Exception $e) {
            error_log("Error al crear artículo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene artículos con información del autor
     * 
     * @param int $limite Número de registros por página
     * @param int $offset Desplazamiento para paginación
     * @return array Array de artículos con datos del autor
     */
    private function obtenerArticulosConAutor(int $limite, int $offset): array {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT a.*, u.nombre, u.apellidos 
                    FROM articulos a 
                    LEFT JOIN usuarios u ON a.autor_id = u.id 
                    ORDER BY a.fecha_creacion DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$limite, $offset]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener artículos con autor: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene las categorías disponibles para artículos
     * 
     * @return array Array de categorías
     */
    private function obtenerCategoriasArticulo(): array {
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
    
    /**
     * Obtiene los estados disponibles para artículos
     * 
     * @return array Array de estados
     */
    private function obtenerEstadosArticulo(): array {
        return [
            'borrador' => 'Borrador',
            'revision' => 'En Revisión',
            'programado' => 'Programado',
            'publicado' => 'Publicado',
            'archivado' => 'Archivado'
        ];
    }
    
    /**
     * Obtiene el nombre de una categoría por su clave
     * 
     * @param string $categoria Clave de la categoría
     * @return string Nombre de la categoría
     */
    private function obtenerNombreCategoria(string $categoria): string {
        $categorias = $this->obtenerCategoriasArticulo();
        return $categorias[$categoria] ?? $categoria;
    }
    
    /**
     * Obtiene el nombre de un estado por su clave
     * 
     * @param string $estado Clave del estado
     * @return string Nombre del estado
     */
    private function obtenerNombreEstado(string $estado): string {
        $estados = $this->obtenerEstadosArticulo();
        return $estados[$estado] ?? $estado;
    }
    
    /**
     * Genera un slug único a partir del título
     * 
     * @param string $titulo Título del artículo
     * @return string Slug generado
     */
    private function generarSlugArticulo(string $titulo): string {
        // Convertir a minúsculas y reemplazar caracteres especiales
        $slug = strtolower($titulo);
        $slug = preg_replace('/[áàäâ]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöô]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/[ñ]/u', 'n', $slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Verifica si un slug ya existe
     * 
     * @param string $slug Slug a verificar
     * @param int|null $idExcluir ID a excluir de la búsqueda
     * @return bool True si el slug existe
     */
    private function slugArticuloExiste(string $slug, ?int $idExcluir = null): bool {
        try {
            $bd = \Utilidades\BaseDatos::obtenerInstancia();
            $conexion = $bd->obtenerConexion();
            
            $sql = "SELECT id FROM articulos WHERE slug = ?";
            $params = [$slug];
            
            if ($idExcluir !== null) {
                $sql .= " AND id != ?";
                $params[] = $idExcluir;
            }
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error al verificar slug de artículo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calcula el tiempo estimado de lectura en minutos
     * 
     * @param string $contenido Contenido del artículo
     * @return int Tiempo de lectura en minutos
     */
    private function calcularTiempoLectura(string $contenido): int {
        $palabras = str_word_count(strip_tags($contenido));
        $palabrasPorMinuto = 200; // Promedio de lectura
        return max(1, ceil($palabras / $palabrasPorMinuto));
    }
}
