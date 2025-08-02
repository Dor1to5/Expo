<?php
/**
 * Script de prueba para crear una exposición
 */

require_once 'config/config.php';
require_once 'src/Utilidades/BaseDatos.php';
require_once 'src/Modelos/ModeloBase.php';
require_once 'src/Modelos/Exposicion.php';

use Modelos\Exposicion;

echo "=== PRUEBA DE CREACIÓN DE EXPOSICIÓN ===" . PHP_EOL;

try {
    $exposicion = new Exposicion();
    
    $datos = [
        'titulo' => 'Exposición de Prueba - Arte Digital',
        'slug' => 'exposicion-prueba-arte-digital',
        'descripcion' => 'Esta es una exposición de prueba para verificar el funcionamiento del sistema de gestión de exposiciones. Incluye obras de arte digital contemporáneo.',
        'descripcion_corta' => 'Exposición de prueba con arte digital contemporáneo',
        'categoria' => 'arte_contemporaneo',
        'ubicacion' => 'Museo Virtual de Arte',
        'direccion_completa' => 'Calle Principal 123, Madrid, España',
        'fecha_inicio' => '2025-08-15',
        'fecha_fin' => '2025-09-15',
        'precio_entrada' => 12.50,
        'destacada' => 1,
        'activa' => 1,
        'visible' => 1,
        'usuario_creador_id' => 1
    ];
    
    echo "Datos a insertar:" . PHP_EOL;
    print_r($datos);
    echo PHP_EOL;
    
    $resultado = $exposicion->crear($datos);
    
    if ($resultado) {
        echo "✅ Exposición creada exitosamente con ID: " . $resultado . PHP_EOL;
        
        // Verificar que se creó correctamente
        $exposicionCreada = $exposicion->obtenerPorId($resultado);
        echo "Datos de la exposición creada:" . PHP_EOL;
        print_r($exposicionCreada);
    } else {
        echo "❌ Error al crear la exposición" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}

echo PHP_EOL . "=== PRUEBA FINALIZADA ===" . PHP_EOL;
