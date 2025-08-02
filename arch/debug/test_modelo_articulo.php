<?php
/**
 * Script de prueba para crear un artículo
 */

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', '_exposiciones');
define('DB_USER', 'desarrollo');
define('DB_PASS', '_desarrollo');

echo "=== PRUEBA DE CREACIÓN DE ARTÍCULO ===" . PHP_EOL;

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
    
    // Verificar que exista la tabla articulos
    $stmt = $pdo->query("SHOW TABLES LIKE 'articulos'");
    $tabla = $stmt->fetch();
    
    if ($tabla) {
        echo "✅ Tabla 'articulos' encontrada" . PHP_EOL;
        
        // Intentar insertar un artículo de prueba
        $sql = "INSERT INTO articulos (titulo, slug, resumen, contenido, categoria, autor_id, estado, destacado, fecha_publicacion) 
                VALUES (:titulo, :slug, :resumen, :contenido, :categoria, :autor_id, :estado, :destacado, :fecha_publicacion)";
        
        $stmt = $pdo->prepare($sql);
        
        $datos = [
            'titulo' => 'Artículo de Prueba - Arte Digital en la Era Moderna',
            'slug' => 'articulo-prueba-arte-digital-' . time(),
            'resumen' => 'Un análisis profundo sobre cómo el arte digital está transformando el panorama cultural contemporáneo.',
            'contenido' => 'Este es el contenido completo del artículo de prueba. Aquí hablaríamos sobre las nuevas tendencias en arte digital, las herramientas emergentes, y cómo los artistas están adaptándose a las nuevas tecnologías. El artículo incluiría ejemplos específicos, análisis crítico y perspectivas futuras sobre el tema.',
            'categoria' => 'arte',
            'autor_id' => 1,
            'estado' => 'publicado',
            'destacado' => 1,
            'fecha_publicacion' => date('Y-m-d H:i:s')
        ];
        
        echo "Datos a insertar:" . PHP_EOL;
        print_r($datos);
        echo PHP_EOL;
        
        $resultado = $stmt->execute($datos);
        
        if ($resultado) {
            $id = $pdo->lastInsertId();
            echo "✅ Artículo creado exitosamente con ID: " . $id . PHP_EOL;
            
            // Verificar los datos insertados
            $stmt = $pdo->prepare("SELECT * FROM articulos WHERE id = ?");
            $stmt->execute([$id]);
            $articulo = $stmt->fetch();
            
            echo "Datos del artículo creado:" . PHP_EOL;
            print_r($articulo);
            
        } else {
            echo "❌ Error al crear el artículo" . PHP_EOL;
        }
        
    } else {
        echo "❌ Tabla 'articulos' no encontrada" . PHP_EOL;
    }
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== PRUEBA FINALIZADA ===" . PHP_EOL;
