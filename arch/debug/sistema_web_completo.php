<?php
/**
 * Test de sistema web simple
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f8f8f8; padding: 10px; border-left: 3px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test del Sistema</h1>
        
        <?php
        try {
            echo "<h2>✅ Configuración Base</h2>";
            
            // Incluir archivos necesarios
            require_once __DIR__ . '/../config/configuracion.php';
            require_once __DIR__ . '/../config/basedatos.php';
            require_once __DIR__ . '/../config/rutas.php';
            require_once __DIR__ . '/../src/Utilidades/BaseDatos.php';
            require_once __DIR__ . '/../src/Controladores/ControladorBase.php';
            require_once __DIR__ . '/../src/Servicios/ServicioAutenticacion.php';
            require_once __DIR__ . '/../src/Modelos/ModeloBase.php';
            
            echo "<p class='success'>✅ Todos los archivos base cargados correctamente</p>";
            
            // Configurar autoloader
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
            
            echo "<p class='success'>✅ Autoloader configurado</p>";
            
            // Test de ruta
            echo "<h2>🛣️ Test de Rutas</h2>";
            $rutaActual = Config\Rutas::obtenerRutaActual();
            echo "<p class='info'>Ruta actual: <strong>$rutaActual</strong></p>";
            
            $resolucion = Config\Rutas::resolverRuta($rutaActual);
            echo "<p class='info'>Resolución:</p>";
            echo "<pre>" . print_r($resolucion, true) . "</pre>";
            
            // Test de controlador
            echo "<h2>🎮 Test de Controlador</h2>";
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $controladorClase = $resolucion['controlador'];
            $metodo = $resolucion['metodo'];
            
            echo "<p class='info'>Controlador: <strong>$controladorClase</strong></p>";
            echo "<p class='info'>Método: <strong>$metodo</strong></p>";
            
            $controlador = new $controladorClase();
            echo "<p class='success'>✅ Controlador creado exitosamente</p>";
            
            // Ejecutar método
            echo "<h2>⚡ Ejecutando Método</h2>";
            if (method_exists($controlador, $metodo)) {
                echo "<p class='info'>Ejecutando $controladorClase::$metodo()...</p>";
                ob_start();
                $controlador->$metodo();
                $salida = ob_get_clean();
                echo "<p class='success'>✅ Método ejecutado</p>";
                echo "<div>$salida</div>";
            } else {
                echo "<p class='error'>❌ Método $metodo no existe en $controladorClase</p>";
            }
            
        } catch (Exception $e) {
            echo "<h2 class='error'>❌ Error</h2>";
            echo "<p class='error'>Mensaje: " . $e->getMessage() . "</p>";
            echo "<p class='error'>Archivo: " . $e->getFile() . "</p>";
            echo "<p class='error'>Línea: " . $e->getLine() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        ?>
        
        <hr>
        <p><em>Test realizado el <?= date('Y-m-d H:i:s') ?></em></p>
    </div>
</body>
</html>
