<?php
/**
 * Controlador Inicio - Gestión de páginas públicas principales
 * 
 * Este controlador maneja las páginas principales del área pública
 * como la página de inicio, acerca de, etc.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Controladores;

use Modelos\Exposicion;
use Modelos\Articulo;
use Exception;

/**
 * Clase InicioControlador - Controlador para páginas públicas principales
 */
class InicioControlador extends ControladorBase {
    
    /**
     * Modelo de exposiciones
     * @var Exposicion
     */
    private Exposicion $modeloExposicion;
    
    /**
     * Modelo de artículos
     * @var Articulo
     */
    private Articulo $modeloArticulo;
    
    /**
     * Inicializa el controlador
     */
    protected function inicializarControlador(): void {
        parent::inicializarControlador();
        
        $this->modeloExposicion = new Exposicion();
        $this->modeloArticulo = new Articulo();
        $this->establecerLayout('publico');
    }
    
    /**
     * Muestra la página principal del sitio
     */
    public function mostrarInicio(): void {
        try {
            $this->establecerTitulo('Inicio - ' . NOMBRE_APLICACION);
            
            // Datos temporales para probar el sistema
            $exposicionesDestacadas = [];
            $exposicionesActuales = [];
            $articulosRecientes = [];
            $articulosDestacados = [];
            
            // Estadísticas básicas temporales
            $estadisticas = [
                'total_exposiciones' => 0,
                'exposiciones_actuales' => 0,
                'total_articulos' => 0
            ];
            
            $this->renderizar('publicas/inicio', [
                'exposiciones_destacadas' => $exposicionesDestacadas,
                'exposiciones_actuales' => $exposicionesActuales,
                'articulos_recientes' => $articulosRecientes,
                'articulos_destacados' => $articulosDestacados,
                'estadisticas' => $estadisticas
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra la página "Acerca de"
     */
    public function mostrarAcercaDe(): void {
        try {
            $this->establecerTitulo('Acerca de - ' . NOMBRE_APLICACION);
            
            // Obtener algunas estadísticas para mostrar en la página
            $estadisticas = [
                'años_funcionamiento' => date('Y') - 2020, // Año de fundación ejemplo
                'total_exposiciones' => $this->modeloExposicion->contar(['publicada' => 1]),
                'total_articulos' => $this->modeloArticulo->contar(['publicado' => 1]),
                'categorias_exposiciones' => count(Exposicion::obtenerCategorias())
            ];
            
            $this->renderizar('publicas/acerca-de', [
                'estadisticas' => $estadisticas
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra la página de contacto
     */
    public function mostrarContacto(): void {
        try {
            $this->establecerTitulo('Contacto - ' . NOMBRE_APLICACION);
            
            $this->renderizar('publicas/contacto');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Procesa el formulario de contacto
     */
    public function procesarContacto(): void {
        try {
            $this->verificarCSRF();
            
            // Obtener datos del formulario
            $datos = $this->obtenerDatosFormulario([
                'nombre',
                'email',
                'asunto',
                'mensaje'
            ]);
            
            // Validar datos
            $this->validarRequerido($datos['nombre'], 'nombre');
            $this->validarRequerido($datos['email'], 'email');
            $this->validarEmail($datos['email']);
            $this->validarRequerido($datos['asunto'], 'asunto');
            $this->validarRequerido($datos['mensaje'], 'mensaje');
            
            // Validar longitud del mensaje
            if (strlen($datos['mensaje']) < 10) {
                throw new Exception("El mensaje debe tener al menos 10 caracteres");
            }
            
            // Aquí se enviaría el email o se guardaría en base de datos
            // Por simplicidad, solo se simula el procesamiento
            
            $this->añadirMensajeFlash('success', 'Tu mensaje ha sido enviado correctamente. Te responderemos pronto.');
            $this->redirigir('/contacto');
            
        } catch (Exception $e) {
            $this->añadirMensajeFlash('error', $e->getMessage());
            $this->redirigir('/contacto');
        }
    }
    
    /**
     * Muestra página de búsqueda general
     */
    public function mostrarBusqueda(): void {
        try {
            $terminoBusqueda = $this->obtenerParametroGET('q', '');
            $this->establecerTitulo('Búsqueda - ' . NOMBRE_APLICACION);
            
            $resultados = [];
            
            if (!empty($terminoBusqueda)) {
                // Buscar en exposiciones
                $exposicionesEncontradas = $this->modeloExposicion->buscar($terminoBusqueda, 10);
                
                // Buscar en artículos
                $articulosEncontrados = $this->modeloArticulo->buscar($terminoBusqueda, 10);
                
                $resultados = [
                    'exposiciones' => $exposicionesEncontradas,
                    'articulos' => $articulosEncontrados,
                    'total' => count($exposicionesEncontradas) + count($articulosEncontrados)
                ];
            }
            
            $this->renderizar('publicas/busqueda', [
                'termino_busqueda' => $terminoBusqueda,
                'resultados' => $resultados
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra página de archivo/histórico
     */
    public function mostrarArchivo(): void {
        try {
            $this->establecerTitulo('Archivo - ' . NOMBRE_APLICACION);
            
            // Obtener parámetros de filtrado
            $año = $this->obtenerParametroGET('año');
            $mes = $this->obtenerParametroGET('mes');
            $categoria = $this->obtenerParametroGET('categoria');
            
            // Obtener exposiciones pasadas
            $filtros = [];
            if ($categoria && in_array($categoria, array_keys(Exposicion::obtenerCategorias()))) {
                $filtros['categoria'] = $categoria;
            }
            
            $paginacion = $this->obtenerParametrosPaginacion();
            $exposicionesPasadas = $this->modeloExposicion->obtenerParaAdmin(
                $paginacion['limite'],
                $paginacion['offset'],
                array_merge($filtros, ['publicada' => 1])
            );
            
            // Obtener total para paginación
            $totalExposiciones = $this->modeloExposicion->contar(array_merge($filtros, ['publicada' => 1]));
            
            // Generar paginación
            $htmlPaginacion = $this->generarPaginacion(
                $totalExposiciones,
                $paginacion['limite'],
                $paginacion['pagina'],
                '/archivo'
            );
            
            $this->renderizar('publicas/archivo', [
                'exposiciones' => $exposicionesPasadas,
                'categorias_disponibles' => Exposicion::obtenerCategorias(),
                'categoria_seleccionada' => $categoria,
                'paginacion' => $htmlPaginacion,
                'total_exposiciones' => $totalExposiciones
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra página de políticas de privacidad
     */
    public function mostrarPrivacidad(): void {
        try {
            $this->establecerTitulo('Política de Privacidad - ' . NOMBRE_APLICACION);
            
            $this->renderizar('publicas/privacidad');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra página de términos y condiciones
     */
    public function mostrarTerminos(): void {
        try {
            $this->establecerTitulo('Términos y Condiciones - ' . NOMBRE_APLICACION);
            
            $this->renderizar('publicas/terminos');
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * API para obtener exposiciones destacadas (AJAX)
     */
    public function apiExposicionesDestacadas(): void {
        try {
            $limite = min(10, max(1, (int)$this->obtenerParametroGET('limite', 3)));
            
            $exposiciones = $this->modeloExposicion->obtenerDestacadas($limite);
            
            $this->renderizarJson([
                'exito' => true,
                'exposiciones' => $exposiciones,
                'total' => count($exposiciones)
            ]);
            
        } catch (Exception $e) {
            $this->renderizarJson([
                'exito' => false,
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API para obtener artículos recientes (AJAX)
     */
    public function apiArticulosRecientes(): void {
        try {
            $limite = min(10, max(1, (int)$this->obtenerParametroGET('limite', 5)));
            
            $articulos = $this->modeloArticulo->obtenerRecientes($limite);
            
            $this->renderizarJson([
                'exito' => true,
                'articulos' => $articulos,
                'total' => count($articulos)
            ]);
            
        } catch (Exception $e) {
            $this->renderizarJson([
                'exito' => false,
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }
}
