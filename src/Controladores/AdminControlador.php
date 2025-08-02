<?php
/**
 * Controlador Admin - Panel de administración principal
 * 
 * Este controlador maneja el dashboard y funciones principales del panel de administración
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Controladores;

use Modelos\Usuario;
use Modelos\Exposicion;
use Modelos\Articulo;
use Exception;

/**
 * Clase AdminControlador - Controlador para el panel de administración
 */
class AdminControlador extends ControladorBase {
    
    /**
     * Inicializa el controlador
     */
    protected function inicializarControlador(): void {
        parent::inicializarControlador();
        
        // TEMPORAL: Comentar verificación de autenticación para pruebas
        /*
        // Verificar que el usuario esté autenticado y sea administrador
        if (!$this->auth->estaAutenticado()) {
            $this->redirigir('/login');
            return;
        }
        
        $usuario = $this->auth->obtenerUsuarioAutenticado();
        if (!$usuario || $usuario['rol_id'] < 3) { // Solo editores y admin
            $this->redirigir('/');
            return;
        }
        */
        
        $this->establecerLayout('admin');
    }
    
    /**
     * Muestra el dashboard principal de administración
     */
    public function mostrarDashboard(): void {
        try {
            $this->establecerTitulo('Panel de Administración - ' . NOMBRE_APLICACION);
            
            // Estadísticas básicas temporales
            $estadisticas = [
                'total_usuarios' => 1, // Temporal
                'total_exposiciones' => 0,
                'total_articulos' => 0,
                'exposiciones_activas' => 0,
                'usuarios_activos' => 1,
                'comentarios_pendientes' => 0
            ];
            
            // Actividad reciente temporal
            $actividadReciente = [];
            
            $this->renderizar('admin/dashboard', [
                'estadisticas' => $estadisticas,
                'actividad_reciente' => $actividadReciente
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'admin/error');
        }
    }
    
    /**
     * Muestra la configuración del sistema
     */
    public function mostrarConfiguracion(): void {
        try {
            $this->establecerTitulo('Configuración del Sistema - ' . NOMBRE_APLICACION);
            
            // Configuración temporal
            $configuracion = [
                'nombre_aplicacion' => NOMBRE_APLICACION,
                'modo_debug' => MODO_DEBUG,
                'email_administrador' => 'admin@exposiciones.local',
                'timezone' => 'America/Mexico_City'
            ];
            
            $this->renderizar('admin/configuracion', [
                'configuracion' => $configuracion
            ]);
            
        } catch (Exception $e) {
            $this->manejarError($e, 'admin/error');
        }
    }
}
