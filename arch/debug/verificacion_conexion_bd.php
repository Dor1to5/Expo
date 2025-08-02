<?php
/**
 * Script de prueba simple para conectar a la base de datos
 */

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', '_exposiciones');
define('DB_USER', 'desarrollo');
define('DB_PASS', '_desarrollo');

echo "=== PRUEBA DE CONEXIÓN A BASE DE DATOS ===" . PHP_EOL;

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "✅ Conexión a base de datos exitosa" . PHP_EOL;
    
    // Verificar que exista la tabla exposiciones
    $stmt = $pdo->query("SHOW TABLES LIKE 'exposiciones'");
    $tabla = $stmt->fetch();
    
    if ($tabla) {
        echo "✅ Tabla 'exposiciones' encontrada" . PHP_EOL;
        
        // Intentar insertar una exposición de prueba
        $sql = "INSERT INTO exposiciones (titulo, slug, descripcion, categoria, ubicacion, fecha_inicio, fecha_fin, precio_entrada, destacada, activa, visible, usuario_creador_id) 
                VALUES (:titulo, :slug, :descripcion, :categoria, :ubicacion, :fecha_inicio, :fecha_fin, :precio_entrada, :destacada, :activa, :visible, :usuario_creador_id)";
        
        $stmt = $pdo->prepare($sql);
        
        $datos = [
            'titulo' => 'Exposición de Prueba - Arte Digital',
            'slug' => 'exposicion-prueba-arte-digital-' . time(),
            'descripcion' => 'Esta es una exposición de prueba para verificar el funcionamiento del sistema.',
            'categoria' => 'arte_contemporaneo',
            'ubicacion' => 'Museo Virtual de Arte',
            'fecha_inicio' => '2025-08-15',
            'fecha_fin' => '2025-09-15',
            'precio_entrada' => 12.50,
            'destacada' => 1,
            'activa' => 1,
            'visible' => 1,
            'usuario_creador_id' => 1
        ];
        
        $resultado = $stmt->execute($datos);
        
        if ($resultado) {
            $id = $pdo->lastInsertId();
            echo "✅ Exposición creada exitosamente con ID: " . $id . PHP_EOL;
        } else {
            echo "❌ Error al crear la exposición" . PHP_EOL;
        }
        
    } else {
        echo "❌ Tabla 'exposiciones' no encontrada" . PHP_EOL;
    }
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== PRUEBA FINALIZADA ===" . PHP_EOL;
