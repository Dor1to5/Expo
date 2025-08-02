<?php
/**
 * Test específico para Exposiciones y Artículos
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Exposiciones y Artículos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .warning { color: orange; }
        pre { background: #f8f8f8; padding: 10px; border-left: 3px solid #ddd; overflow-x: auto; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; }
        .menu { margin: 20px 0; }
        .menu a { display: inline-block; margin: 5px; padding: 8px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; }
        .menu a:hover { background: #005a8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Específico: Exposiciones y Artículos</h1>
        
        <div class="menu">
            <h3>Enlaces directos:</h3>
            <a href="?test=exposiciones">Test Exposiciones</a>
            <a href="?test=articulos">Test Artículos</a>
            <a href="?test=blog">Test Blog</a>
            <a href="?test=all">Test Completo</a>
        </div>
        
        <?php
        // Configuración inicial
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
        
        $test = $_GET['test'] ?? 'all';
        
        if ($test === 'exposiciones' || $test === 'all') {
            echo "<div class='test-section'>";
            echo "<h2>🏛️ Test de Exposiciones</h2>";
            
            try {
                // Test 1: Verificar controlador
                echo "<h3>1. Verificando AdminExposicionControlador</h3>";
                if (class_exists('Controladores\\AdminExposicionControlador')) {
                    echo "<p class='success'>✅ Clase AdminExposicionControlador existe</p>";
                    $controlador = new Controladores\AdminExposicionControlador();
                    echo "<p class='success'>✅ Controlador instanciado correctamente</p>";
                } else {
                    echo "<p class='error'>❌ Clase AdminExposicionControlador no encontrada</p>";
                }
                
                // Test 2: Verificar modelo
                echo "<h3>2. Verificando Modelo Exposicion</h3>";
                if (class_exists('Modelos\\Exposicion')) {
                    echo "<p class='success'>✅ Clase Exposicion existe</p>";
                    $exposicion = new Modelos\Exposicion();
                    echo "<p class='success'>✅ Modelo instanciado correctamente</p>";
                } else {
                    echo "<p class='error'>❌ Clase Exposicion no encontrada</p>";
                }
                
                // Test 3: Verificar vistas
                echo "<h3>3. Verificando Vistas</h3>";
                $vistasExposiciones = [
                    __DIR__ . '/../vistas/admin/exposiciones/listar.php',
                    __DIR__ . '/../vistas/admin/exposiciones/crear.php'
                ];
                
                foreach ($vistasExposiciones as $vista) {
                    if (file_exists($vista)) {
                        echo "<p class='success'>✅ Vista existe: " . basename($vista) . "</p>";
                    } else {
                        echo "<p class='error'>❌ Vista no encontrada: " . basename($vista) . "</p>";
                    }
                }
                
                // Test 4: Probar ruta
                echo "<h3>4. Probando Resolución de Ruta</h3>";
                $rutaExposiciones = '/admin/exposiciones';
                $resolucion = Config\Rutas::resolverRuta($rutaExposiciones);
                
                if ($resolucion) {
                    echo "<p class='success'>✅ Ruta '$rutaExposiciones' resuelta correctamente</p>";
                    echo "<p class='info'>Controlador: " . $resolucion['controlador'] . "</p>";
                    echo "<p class='info'>Método: " . $resolucion['metodo'] . "</p>";
                } else {
                    echo "<p class='error'>❌ No se pudo resolver la ruta '$rutaExposiciones'</p>";
                }
                
                // Test 5: Test de BD para exposiciones
                echo "<h3>5. Test de Base de Datos</h3>";
                try {
                    $db = new Utilidades\BaseDatos();
                    $consulta = $db->obtenerConexion()->query("SHOW TABLES LIKE 'exposiciones'");
                    if ($consulta->rowCount() > 0) {
                        echo "<p class='success'>✅ Tabla 'exposiciones' existe</p>";
                        
                        // Contar registros
                        $contador = $db->obtenerConexion()->query("SELECT COUNT(*) as total FROM exposiciones")->fetch();
                        echo "<p class='info'>📊 Registros en la tabla: " . $contador['total'] . "</p>";
                    } else {
                        echo "<p class='error'>❌ Tabla 'exposiciones' no existe</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Error BD: " . $e->getMessage() . "</p>";
                }
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error en test de exposiciones: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
            
            echo "</div>";
        }
        
        if ($test === 'articulos' || $test === 'all') {
            echo "<div class='test-section'>";
            echo "<h2>📝 Test de Artículos</h2>";
            
            try {
                // Test 1: Verificar controlador admin
                echo "<h3>1. Verificando AdminArticuloControlador</h3>";
                if (class_exists('Controladores\\AdminArticuloControlador')) {
                    echo "<p class='success'>✅ Clase AdminArticuloControlador existe</p>";
                    $controlador = new Controladores\AdminArticuloControlador();
                    echo "<p class='success'>✅ Controlador instanciado correctamente</p>";
                } else {
                    echo "<p class='error'>❌ Clase AdminArticuloControlador no encontrada</p>";
                }
                
                // Test 2: Verificar controlador blog
                echo "<h3>2. Verificando BlogControlador</h3>";
                if (class_exists('Controladores\\BlogControlador')) {
                    echo "<p class='success'>✅ Clase BlogControlador existe</p>";
                    $controladorBlog = new Controladores\BlogControlador();
                    echo "<p class='success'>✅ BlogControlador instanciado correctamente</p>";
                } else {
                    echo "<p class='error'>❌ Clase BlogControlador no encontrada</p>";
                }
                
                // Test 3: Verificar modelo
                echo "<h3>3. Verificando Modelo Articulo</h3>";
                if (class_exists('Modelos\\Articulo')) {
                    echo "<p class='success'>✅ Clase Articulo existe</p>";
                    $articulo = new Modelos\Articulo();
                    echo "<p class='success'>✅ Modelo instanciado correctamente</p>";
                } else {
                    echo "<p class='error'>❌ Clase Articulo no encontrada</p>";
                }
                
                // Test 4: Verificar vistas admin
                echo "<h3>4. Verificando Vistas Admin</h3>";
                $vistasArticulos = [
                    __DIR__ . '/../vistas/admin/articulos/listar.php',
                    __DIR__ . '/../vistas/admin/articulos/crear.php'
                ];
                
                foreach ($vistasArticulos as $vista) {
                    if (file_exists($vista)) {
                        echo "<p class='success'>✅ Vista admin existe: " . basename($vista) . "</p>";
                    } else {
                        echo "<p class='error'>❌ Vista admin no encontrada: " . basename($vista) . "</p>";
                    }
                }
                
                // Test 5: Verificar vistas públicas
                echo "<h3>5. Verificando Vistas Públicas</h3>";
                $vistasBlog = [
                    __DIR__ . '/../vistas/publicas/blog/lista.php'
                ];
                
                foreach ($vistasBlog as $vista) {
                    if (file_exists($vista)) {
                        echo "<p class='success'>✅ Vista blog existe: " . basename($vista) . "</p>";
                    } else {
                        echo "<p class='error'>❌ Vista blog no encontrada: " . basename($vista) . "</p>";
                    }
                }
                
                // Test 6: Probar rutas
                echo "<h3>6. Probando Resolución de Rutas</h3>";
                $rutasArticulos = ['/admin/articulos', '/blog'];
                
                foreach ($rutasArticulos as $ruta) {
                    $resolucion = Config\Rutas::resolverRuta($ruta);
                    if ($resolucion) {
                        echo "<p class='success'>✅ Ruta '$ruta' resuelta correctamente</p>";
                        echo "<p class='info'>  → Controlador: " . $resolucion['controlador'] . "</p>";
                        echo "<p class='info'>  → Método: " . $resolucion['metodo'] . "</p>";
                    } else {
                        echo "<p class='error'>❌ No se pudo resolver la ruta '$ruta'</p>";
                    }
                }
                
                // Test 7: Test de BD para artículos
                echo "<h3>7. Test de Base de Datos</h3>";
                try {
                    $db = new Utilidades\BaseDatos();
                    $consulta = $db->obtenerConexion()->query("SHOW TABLES LIKE 'articulos'");
                    if ($consulta->rowCount() > 0) {
                        echo "<p class='success'>✅ Tabla 'articulos' existe</p>";
                        
                        // Contar registros
                        $contador = $db->obtenerConexion()->query("SELECT COUNT(*) as total FROM articulos")->fetch();
                        echo "<p class='info'>📊 Registros en la tabla: " . $contador['total'] . "</p>";
                    } else {
                        echo "<p class='error'>❌ Tabla 'articulos' no existe</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Error BD: " . $e->getMessage() . "</p>";
                }
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error en test de artículos: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
            
            echo "</div>";
        }
        
        // Test de ejecución directa
        if ($test === 'blog' || $test === 'all') {
            echo "<div class='test-section'>";
            echo "<h2>🌐 Test de Ejecución Directa</h2>";
            
            try {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                echo "<h3>Simulando acceso a /admin/exposiciones</h3>";
                $resolucion = Config\Rutas::resolverRuta('/admin/exposiciones');
                if ($resolucion) {
                    $controladorClase = 'Controladores\\' . $resolucion['controlador'];
                    $metodo = $resolucion['metodo'];
                    
                    $controlador = new $controladorClase();
                    echo "<p class='info'>Ejecutando $controladorClase::$metodo()...</p>";
                    
                    ob_start();
                    try {
                        $controlador->$metodo();
                        $output = ob_get_clean();
                        echo "<p class='success'>✅ Método ejecutado sin errores</p>";
                        echo "<details><summary>Ver salida HTML</summary><pre>" . htmlspecialchars(substr($output, 0, 1000)) . "...</pre></details>";
                    } catch (Exception $e) {
                        ob_end_clean();
                        echo "<p class='error'>❌ Error al ejecutar: " . $e->getMessage() . "</p>";
                    }
                }
                
                echo "<h3>Simulando acceso a /blog</h3>";
                $resolucion = Config\Rutas::resolverRuta('/blog');
                if ($resolucion) {
                    $controladorClase = 'Controladores\\' . $resolucion['controlador'];
                    $metodo = $resolucion['metodo'];
                    
                    $controlador = new $controladorClase();
                    echo "<p class='info'>Ejecutando $controladorClase::$metodo()...</p>";
                    
                    ob_start();
                    try {
                        $controlador->$metodo();
                        $output = ob_get_clean();
                        echo "<p class='success'>✅ Método ejecutado sin errores</p>";
                        echo "<details><summary>Ver salida HTML</summary><pre>" . htmlspecialchars(substr($output, 0, 1000)) . "...</pre></details>";
                    } catch (Exception $e) {
                        ob_end_clean();
                        echo "<p class='error'>❌ Error al ejecutar: " . $e->getMessage() . "</p>";
                        echo "<pre>" . $e->getTraceAsString() . "</pre>";
                    }
                }
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error en test de ejecución: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
            
            echo "</div>";
        }
        ?>
        
        <hr>
        <p><em>Test completado el <?= date('Y-m-d H:i:s') ?></em></p>
    </div>
</body>
</html>
