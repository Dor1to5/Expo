<?php
/**
 * Controlador Exposiciones - Gestión de exposiciones públicas
 * 
 * Este controlador maneja las páginas públicas relacionadas con exposiciones
 * como listado, detalles, búsqueda, etc.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Controladores;

use Modelos\Exposicion;
use Exception;

/**
 * Clase ExposicionControlador - Controlador para exposiciones públicas
 */
class ExposicionControlador extends ControladorBase {
    
    /**
     * Modelo de exposiciones
     * @var Exposicion
     */
    private Exposicion $modeloExposicion;
    
    /**
     * Inicializa el controlador
     */
    protected function inicializarControlador(): void {
        parent::inicializarControlador();
        
        $this->modeloExposicion = new Exposicion();
        $this->establecerLayout('publico');
    }
    
    /**
     * Lista las exposiciones públicas
     */
    public function listarPublicas(): void {
        try {
            $this->establecerTitulo('Exposiciones - ' . NOMBRE_APLICACION);
            
            // Parámetros de paginación
            $pagina = (int) ($_GET['pagina'] ?? 1);
            $limite = 12; // Exposiciones por página
            $offset = ($pagina - 1) * $limite;
            
            // Filtros de búsqueda
            $filtros = [];
            
            if (!empty($_GET['categoria'])) {
                $filtros['categoria'] = $_GET['categoria'];
            }
            
            if (!empty($_GET['busqueda'])) {
                $filtros['busqueda'] = $_GET['busqueda'];
            }
            
            // Obtener exposiciones (temporalmente con datos vacíos)
            $exposiciones = [];
            $totalExposiciones = 0;
            $totalPaginas = 1;
            
            // Obtener categorías disponibles
            $categorias = [
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
            
            $this->renderizar('publicas/exposiciones', [
                'exposiciones' => $exposiciones,
                'categorias' => $categorias,
                'filtros_actuales' => $filtros,
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_exposiciones' => $totalExposiciones
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Muestra los detalles de una exposición específica
     * @param int $id ID de la exposición
     */
    public function mostrarDetalles(int $id): void {
        try {
            // Obtener exposición (temporalmente datos vacíos)
            $exposicion = null;
            
            if (!$exposicion) {
                // Redirigir a 404 si la exposición no existe
                http_response_code(404);
                $this->renderizar('publicas/404');
                return;
            }
            
            $this->establecerTitulo($exposicion['titulo'] . ' - ' . NOMBRE_APLICACION);
            
            // Obtener exposiciones relacionadas
            $exposicionesRelacionadas = [];
            
            $this->renderizar('publicas/exposicion-detalles', [
                'exposicion' => $exposicion,
                'exposiciones_relacionadas' => $exposicionesRelacionadas
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
    
    /**
     * Busca exposiciones por término de búsqueda
     */
    public function buscar(): void {
        try {
            $this->establecerTitulo('Buscar Exposiciones - ' . NOMBRE_APLICACION);
            
            $termino = $_GET['q'] ?? '';
            $categoria = $_GET['categoria'] ?? '';
            
            $exposiciones = [];
            $totalResultados = 0;
            
            if (!empty($termino)) {
                // Aquí iría la lógica de búsqueda real
                // $exposiciones = $this->modeloExposicion->buscar($termino, $categoria);
                // $totalResultados = count($exposiciones);
            }
            
            $this->renderizar('publicas/exposiciones-buscar', [
                'termino_busqueda' => $termino,
                'categoria_filtro' => $categoria,
                'exposiciones' => $exposiciones,
                'total_resultados' => $totalResultados
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'publicas/error');
        }
    }
}
