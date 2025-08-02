<?php
/**
 * Test específico de rutas
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Rutas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .warning { color: orange; }
        pre { background: #f8f8f8; padding: 10px; border-left: 3px solid #ddd; }
        .ruta-test { margin: 10px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; }
        .menu { margin: 20px 0; }
        .menu a { display: inline-block; margin: 5px; padding: 8px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; }
        .menu a:hover { background: #005a8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛣️ Test de Rutas del Sistema</h1>
        
        <div class="menu">
            <h3>Enlaces de prueba:</h3>
            <a href="?ruta=">Inicio</a>
            <a href="?ruta=admin">Admin Dashboard</a>
            <a href="?ruta=admin/usuarios">Usuarios</a>
            <a href="?ruta=admin/exposiciones">Exposiciones</a>
            <a href="?ruta=admin/articulos">Artículos</a>
            <a href="?ruta=blog">Blog Público</a>
            <a href="?ruta=login">Login</a>
        </div>
        
        <?php
        // Incluir sistema
        require_once __DIR__ . '/../config/configuracion.php';
        require_once __DIR__ . '/../config/basedatos.php';
        require_once __DIR__ . '/../config/rutas.php';
        require_once __DIR__ . '/../src/Utilidades/BaseDatos.php';
        require_once __DIR__ . '/../src/Controladores/ControladorBase.php';
        require_once __DIR__ . '/../src/Servicios/ServicioAutenticacion.php';
        require_once __DIR__ . '/../src/Modelos/ModeloBase.php';
        
        // Autoloader
        spl_autoload_register(function ($clase) {
            $archivo = str_replace('\\', DIRECTORY_SEPARATOR, $clase);
            $rutasPosibles = [
                __DIR__ . '/../src/' . $archivo . '.php',
                __DIR__ . '/../' . $archivo . '.php'
            ];
            
            foreach ($rutasPosibles as $ruta) {
                if (file_exists($ruta)) {
                    require_once $ruta;
                    return;
                }
            }
        });
        
        echo "<h2>📊 Información del Request</h2>";
        echo "<div class='ruta-test'>";
        echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "</p>";
        echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "</p>";
        echo "<p><strong>Parámetro 'ruta':</strong> " . ($_GET['ruta'] ?? 'No definido') . "</p>";
        echo "</div>";
        
        try {
            // Test de obtención de ruta
            echo "<h2>🔍 Test de Obtención de Ruta</h2>";
            $rutaActual = Config\Rutas::obtenerRutaActual();
            echo "<div class='ruta-test'>";
            echo "<p class='info'><strong>Ruta obtenida:</strong> '$rutaActual'</p>";
            echo "</div>";
            
            // Test de resolución de ruta
            echo "<h2>⚙️ Test de Resolución de Ruta</h2>";
            $resolucion = Config\Rutas::resolverRuta($rutaActual);
            
            if ($resolucion) {
                echo "<div class='ruta-test success'>";
                echo "<p class='success'>✅ Ruta resuelta exitosamente</p>";
                echo "<p><strong>Controlador:</strong> " . $resolucion['controlador'] . "</p>";
                echo "<p><strong>Método:</strong> " . $resolucion['metodo'] . "</p>";
                if (isset($resolucion['parametros'])) {
                    echo "<p><strong>Parámetros:</strong></p>";
                    echo "<pre>" . print_r($resolucion['parametros'], true) . "</pre>";
                }
                echo "</div>";
                
                // Test de existencia del controlador
                echo "<h2>🎮 Test de Controlador</h2>";
                $controladorClase = 'Controladores\\' . $resolucion['controlador'];
                
                if (class_exists($controladorClase)) {
                    echo "<p class='success'>✅ Clase $controladorClase existe</p>";
                    
                    $controlador = new $controladorClase();
                    $metodo = $resolucion['metodo'];
                    
                    if (method_exists($controlador, $metodo)) {
                        echo "<p class='success'>✅ Método $metodo existe</p>";
                        echo "<p class='info'>🚀 Todo listo para ejecutar</p>";
                    } else {
                        echo "<p class='error'>❌ Método $metodo no existe en $controladorClase</p>";
                    }
                } else {
                    echo "<p class='error'>❌ Clase $controladorClase no existe</p>";
                }
                
            } else {
                echo "<div class='ruta-test error'>";
                echo "<p class='error'>❌ No se pudo resolver la ruta '$rutaActual'</p>";
                
                // Mostrar rutas disponibles
                echo "<h3>Rutas disponibles:</h3>";
                $reflectionClass = new ReflectionClass('Config\Rutas');
                $rutasPublicas = $reflectionClass->getStaticPropertyValue('rutasPublicas');
                $rutasAdmin = $reflectionClass->getStaticPropertyValue('rutasAdmin');
                
                echo "<h4>Rutas Públicas:</h4>";
                echo "<pre>" . print_r(array_keys($rutasPublicas), true) . "</pre>";
                
                echo "<h4>Rutas Admin:</h4>";
                echo "<pre>" . print_r(array_keys($rutasAdmin), true) . "</pre>";
                
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='ruta-test error'>";
            echo "<h2 class='error'>❌ Error en el Test</h2>";
            echo "<p class='error'>Mensaje: " . $e->getMessage() . "</p>";
            echo "<p class='error'>Archivo: " . $e->getFile() . "</p>";
            echo "<p class='error'>Línea: " . $e->getLine() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
        ?>
        
        <hr>
        <p><em>Test realizado el <?= date('Y-m-d H:i:s') ?></em></p>
    </div>
</body>
</html>
