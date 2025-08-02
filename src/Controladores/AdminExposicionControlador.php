<?php
/**
 * Controlador de administración de exposiciones
 * 
 * Maneja todas las operaciones CRUD para exposiciones en el panel administrativo,
 * incluyendo validaciones, mensajes flash y verificación de permisos.
 * 
 * @author Sistema de Gestión de Exposiciones
 * @version 1.0
 */

namespace Controladores;

use Exception;
use Modelos\Exposicion;
use Modelos\Usuario;

/**
 * Clase AdminExposicionControlador
 * 
 * Controlador especializado en la gestión administrativa de exposiciones
 */
class AdminExposicionControlador extends ControladorBase {
    
    /**
     * Muestra la lista paginada de exposiciones
     * 
     * @param int $pagina Número de página para la paginación
     * @return void
     */
    public function listar(int $pagina = 1): void {
        try {
            $this->verificarAutenticacion();
            $this->verificarPermiso('exposiciones.listar');
            
            $exposicionModelo = new Exposicion();
            $limite = 10;
            $offset = ($pagina - 1) * $limite;
            
            // Obtener exposiciones con información del creador
            $exposiciones = $exposicionModelo->obtenerConCreador([], $limite, $offset);
            $totalExposiciones = $exposicionModelo->contarTodos();
            $totalPaginas = ceil($totalExposiciones / $limite);
            
            $datos = [
                'exposiciones' => $exposiciones,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalExposiciones' => $totalExposiciones
            ];
            
            $this->renderizar('admin/exposiciones/listar', $datos);
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al cargar exposiciones: ' . $e->getMessage());
            $this->redirigir('/admin');
        }
    }
    
    /**
     * Muestra el formulario para crear una nueva exposición
     * 
     * @return void
     */
    public function mostrarCrear(): void {
        try {
            $this->verificarAutenticacion();
            $this->verificarPermiso('exposiciones.crear');
            
            // Obtener categorías disponibles
            $categorias = $this->obtenerCategoriasExposicion();
            
            $datos = [
                'categorias' => $categorias,
                'accion' => 'crear'
            ];
            
            $this->renderizar('admin/exposiciones/crear', $datos);
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al mostrar formulario: ' . $e->getMessage());
            $this->redirigir('/admin/exposiciones');
        }
    }
    
    /**
     * Procesa la creación de una nueva exposición
     * 
     * @return void
     */
    public function procesarCrear(): void {
        try {
            $this->verificarAutenticacion();
            $this->verificarPermiso('exposiciones.crear');
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'titulo',
                'descripcion',
                'descripcion_corta',
                'categoria',
                'ubicacion', 
                'direccion_completa',
                'fecha_inicio',
                'fecha_fin',
                'precio_entrada',
                'imagen_principal',
                'video_promocional',
                'enlace_compra',
                'destacada',
                'activa',
                'visible'
            ]);
            
            // Validar datos
            $this->validarDatosExposicion($datos);
            
            // Agregar datos adicionales
            $datos['slug'] = $this->generarSlug($datos['titulo']);
            $datos['usuario_creador_id'] = $_SESSION['usuario_id'];
            $datos['destacada'] = isset($datos['destacada']) ? 1 : 0;
            $datos['activa'] = isset($datos['activa']) ? 1 : 0;
            $datos['visible'] = isset($datos['visible']) ? 1 : 0;
            
            // Crear exposición
            if ($this->crearExposicion($datos)) {
                $this->añadirMensajeFlash('exito', "✅ Exposición '{$datos['titulo']}' creada exitosamente");
                $this->añadirMensajeFlash('info', "🏛️ Ubicación: {$datos['ubicacion']} | 📅 Desde: {$datos['fecha_inicio']}");
                $this->redirigir('/admin/exposiciones');
            } else {
                throw new Exception('No se pudo crear la exposición en la base de datos');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al crear exposición: ' . $e->getMessage());
            $this->redirigir('/admin/exposiciones/crear');
        }
    }
    
    /**
     * Muestra el formulario para editar una exposición existente
     * 
     * @param int $id ID de la exposición a editar
     * @return void
     */
    public function mostrarEditar(int $id): void {
        try {
            $this->verificarAutenticacion();
            $this->verificarPermiso('exposiciones.editar');
            
            $exposicionModelo = new Exposicion();
            $exposicion = $exposicionModelo->obtenerPorId($id);
            
            if (!$exposicion) {
                throw new Exception('Exposición no encontrada');
            }
            
            $categorias = $this->obtenerCategoriasExposicion();
            
            $datos = [
                'exposicion' => $exposicion,
                'categorias' => $categorias,
                'accion' => 'editar'
            ];
            
            $this->renderizar('admin/exposiciones/editar', $datos);
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al cargar exposición: ' . $e->getMessage());
            $this->redirigir('/admin/exposiciones');
        }
    }
    
    /**
     * Procesa la actualización de una exposición
     * 
     * @param int $id ID de la exposición a actualizar
     * @return void
     */
    public function procesarEditar(int $id): void {
        try {
            $this->verificarAutenticacion();
            $this->verificarPermiso('exposiciones.editar');
            
            // Verificar que la exposición existe
            $exposicionModelo = new Exposicion();
            $exposicionExistente = $exposicionModelo->obtenerPorId($id);
            
            if (!$exposicionExistente) {
                throw new Exception('Exposición no encontrada');
            }
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'titulo',
                'descripcion',
                'descripcion_corta',
                'categoria',
                'ubicacion',
                'direccion_completa',
                'fecha_inicio',
                'fecha_fin',
                'precio_entrada',
                'imagen_principal',
                'video_promocional',
                'enlace_compra',
                'destacada',
                'activa',
                'visible'
            ]);
            
            // Validar datos
            $this->validarDatosExposicion($datos, $id);
            
            // Actualizar slug si cambió el título
            if ($datos['titulo'] !== $exposicionExistente['titulo']) {
                $datos['slug'] = $this->generarSlug($datos['titulo']);
            }
            
            $datos['destacada'] = isset($datos['destacada']) ? 1 : 0;
            $datos['activa'] = isset($datos['activa']) ? 1 : 0;
            $datos['visible'] = isset($datos['visible']) ? 1 : 0;
            
            // Actualizar exposición
            if ($exposicionModelo->actualizar($id, $datos)) {
                $this->añadirMensajeFlash('exito', "✅ Exposición '{$datos['titulo']}' actualizada exitosamente");
                $this->redirigir('/admin/exposiciones');
            } else {
                throw new Exception('No se pudo actualizar la exposición');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al actualizar exposición: ' . $e->getMessage());
            $this->redirigir("/admin/exposiciones/{$id}/editar");
        }
    }
    
    /**
     * Elimina una exposición tras confirmación
     * 
     * @param int $id ID de la exposición a eliminar
     * @return void
     */
    public function eliminar(int $id): void {
        try {
            $this->verificarAutenticacion();
            $this->verificarPermiso('exposiciones.eliminar');
            
            $exposicionModelo = new Exposicion();
            $exposicion = $exposicionModelo->obtenerPorId($id);
            
            if (!$exposicion) {
                throw new Exception('Exposición no encontrada');
            }
            
            if ($exposicionModelo->eliminar($id)) {
                $this->añadirMensajeFlash('exito', "✅ Exposición '{$exposicion['titulo']}' eliminada exitosamente");
            } else {
                throw new Exception('No se pudo eliminar la exposición');
            }
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', '❌ Error al eliminar exposición: ' . $e->getMessage());
        }
        
        $this->redirigir('/admin/exposiciones');
    }
    
    /**
     * Valida los datos de una exposición
     * 
     * @param array $datos Datos a validar
     * @param int|null $idExcluir ID a excluir de la validación de slug único
     * @return void
     * @throws Exception Si los datos no son válidos
     */
    private function validarDatosExposicion(array $datos, ?int $idExcluir = null): void {
        // Validar campos requeridos
        if (empty($datos['titulo'])) {
            throw new Exception('🏛️ El título es obligatorio');
        }
        
        if (empty($datos['descripcion'])) {
            throw new Exception('📝 La descripción es obligatoria');
        }
        
        if (empty($datos['ubicacion'])) {
            throw new Exception('📍 La ubicación es obligatoria');
        }
        
        if (empty($datos['fecha_inicio'])) {
            throw new Exception('📅 La fecha de inicio es obligatoria');
        }
        
        if (empty($datos['fecha_fin'])) {
            throw new Exception('📅 La fecha de fin es obligatoria');
        }
        
        // Validar fechas
        $fechaInicio = strtotime($datos['fecha_inicio']);
        $fechaFin = strtotime($datos['fecha_fin']);
        
        if ($fechaInicio === false || $fechaFin === false) {
            throw new Exception('📅 Las fechas no tienen un formato válido');
        }
        
        if ($fechaFin < $fechaInicio) {
            throw new Exception('📅 La fecha de fin debe ser posterior a la fecha de inicio');
        }
        
        // Validar precio
        if (isset($datos['precio_entrada']) && $datos['precio_entrada'] < 0) {
            throw new Exception('💰 El precio no puede ser negativo');
        }
        
        // Validar longitud de campos
        if (strlen($datos['titulo']) > 255) {
            throw new Exception('🏛️ El título no puede exceder 255 caracteres');
        }
        
        if (isset($datos['descripcion_corta']) && strlen($datos['descripcion_corta']) > 500) {
            throw new Exception('📝 La descripción corta no puede exceder 500 caracteres');
        }
        
        // Validar slug único
        $slug = $this->generarSlug($datos['titulo']);
        if ($this->slugExiste($slug, $idExcluir)) {
            throw new Exception('🏛️ Ya existe una exposición con un título similar');
        }
    }
    
    /**
     * Crea una nueva exposición en la base de datos
     * 
     * @param array $datos Datos de la exposición
     * @return bool True si se creó exitosamente
     */
    private function crearExposicion(array $datos): bool {
        try {
            $exposicionModelo = new Exposicion();
            return $exposicionModelo->crear($datos);
        } catch (Exception $e) {
            error_log("Error al crear exposición: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene las categorías disponibles para exposiciones
     * 
     * @return array Array de categorías
     */
    private function obtenerCategoriasExposicion(): array {
        return [
            'arte_contemporaneo' => 'Arte Contemporáneo',
            'arte_clasico' => 'Arte Clásico',
            'fotografia' => 'Fotografía',
            'escultura' => 'Escultura',
            'historia' => 'Historia',
            'ciencias' => 'Ciencias',
            'tecnologia' => 'Tecnología',
            'cultura_popular' => 'Cultura Popular',
            'otros' => 'Otros'
        ];
    }
    
    /**
     * Genera un slug único a partir del título
     * 
     * @param string $titulo Título de la exposición
     * @return string Slug generado
     */
    private function generarSlug(string $titulo): string {
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
    private function slugExiste(string $slug, ?int $idExcluir = null): bool {
        try {
            $exposicionModelo = new Exposicion();
            return $exposicionModelo->slugExiste($slug, $idExcluir);
        } catch (Exception $e) {
            error_log("Error al verificar slug: " . $e->getMessage());
            return false;
        }
    }
}
