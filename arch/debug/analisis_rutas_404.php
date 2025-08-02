<?php
/**
 * Debug específico para problema de rutas 404
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Rutas 404</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .warning { color: orange; }
        pre { background: #f8f8f8; padding: 10px; border-left: 3px solid #ddd; overflow-x: auto; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; }
        .menu { margin: 20px 0; }
        .menu a { display: inline-block; margin: 5px; padding: 8px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; }
        .menu a:hover { background: #005a8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Problema 404</h1>
        
        <div class="menu">
            <h3>Tests directos:</h3>
            <a href="index.php">index.php directo</a>
            <a href="index.php?ruta=admin">admin via GET</a>
            <a href="index.php?ruta=admin/exposiciones">exposiciones via GET</a>
            <a href="index.php?ruta=admin/articulos">articulos via GET</a>
            <a href="index.php?ruta=blog">blog via GET</a>
        </div>
        
        <div class="section">
            <h2>📊 Información del Servidor</h2>
            <?php
            echo "<p><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'No definido') . "</p>";
            echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "</p>";
            echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "</p>";
            echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'No definido') . "</p>";
            echo "<p><strong>REQUEST_METHOD:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'No definido') . "</p>";
            echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "</p>";
            ?>
        </div>
        
        <div class="section">
            <h2>🌐 Variables GET y POST</h2>
            <h3>GET:</h3>
            <pre><?php print_r($_GET); ?></pre>
            <h3>POST:</h3>
            <pre><?php print_r($_POST); ?></pre>
        </div>
        
        <div class="section">
            <h2>📁 Archivos del Sistema</h2>
            <?php
            $archivos = [
                '.htaccess' => __DIR__ . '/.htaccess',
                'index.php' => __DIR__ . '/index.php',
                'config/rutas.php' => __DIR__ . '/../config/rutas.php',
                'config/basedatos.php' => __DIR__ . '/../config/basedatos.php'
            ];
            
            foreach ($archivos as $nombre => $ruta) {
                if (file_exists($ruta)) {
                    echo "<p class='success'>✅ $nombre: Existe</p>";
                } else {
                    echo "<p class='error'>❌ $nombre: NO EXISTE</p>";
                }
            }
            ?>
        </div>
        
        <div class="section">
            <h2>🛠️ Test del Sistema de Rutas</h2>
            <?php
            try {
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
                
                echo "<p class='success'>✅ Sistema cargado correctamente</p>";
                
                // Test de rutas específicas
                $rutasPrueba = [
                    '/',
                    '/admin',
                    '/admin/exposiciones', 
                    '/admin/articulos',
                    '/blog'
                ];
                
                foreach ($rutasPrueba as $ruta) {
                    echo "<h3>Test ruta: $ruta</h3>";
                    $resolucion = Config\Rutas::resolverRuta($ruta);
                    
                    if ($resolucion) {
                        echo "<p class='success'>✅ Resuelta correctamente</p>";
                        echo "<p class='info'>Controlador: " . $resolucion['controlador'] . "</p>";
                        echo "<p class='info'>Método: " . $resolucion['metodo'] . "</p>";
                        
                        // Verificar si el controlador existe
                        $controladorClase = 'Controladores\\' . $resolucion['controlador'];
                        if (class_exists($controladorClase)) {
                            echo "<p class='success'>✅ Controlador existe</p>";
                        } else {
                            echo "<p class='error'>❌ Controlador no encontrado</p>";
                        }
                    } else {
                        echo "<p class='error'>❌ No se pudo resolver</p>";
                    }
                }
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>🔧 Test de Funcionamiento Directo</h2>
            <?php
            if (isset($_GET['test_ruta'])) {
                $rutaTest = $_GET['test_ruta'];
                echo "<h3>Probando ruta: $rutaTest</h3>";
                
                try {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    $resolucion = Config\Rutas::resolverRuta($rutaTest);
                    if ($resolucion) {
                        $controladorClase = 'Controladores\\' . $resolucion['controlador'];
                        $metodo = $resolucion['metodo'];
                        
                        $controlador = new $controladorClase();
                        echo "<p class='info'>Ejecutando $controladorClase::$metodo()...</p>";
                        
                        ob_start();
                        $controlador->$metodo();
                        $output = ob_get_clean();
                        
                        echo "<p class='success'>✅ Ejecutado correctamente</p>";
                        echo "<details><summary>Ver HTML generado</summary>";
                        echo "<pre>" . htmlspecialchars(substr($output, 0, 2000)) . "...</pre>";
                        echo "</details>";
                    } else {
                        echo "<p class='error'>❌ Ruta no resuelta</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>Agregar ?test_ruta=/admin/exposiciones para probar una ruta específica</p>";
            }
            ?>
        </div>
        
        <div class="menu">
            <h3>Tests de ejecución:</h3>
            <a href="?test_ruta=/admin">Test /admin</a>
            <a href="?test_ruta=/admin/exposiciones">Test /admin/exposiciones</a>
            <a href="?test_ruta=/admin/articulos">Test /admin/articulos</a>
            <a href="?test_ruta=/blog">Test /blog</a>
        </div>
        
        <hr>
        <p><em>Debug realizado el <?= date('Y-m-d H:i:s') ?></em></p>
    </div>
</body>
</html>
