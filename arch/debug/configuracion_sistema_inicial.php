<?php
/**
 * Script de depuración para diagnosticar problemas del sistema
 */

echo "<h1>🔍 Diagnóstico del Sistema</h1>";

// 1. Verificar configuración PHP
echo "<h2>📋 Información PHP</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

// 2. Verificar archivos principales
echo "<h2>📁 Verificación de Archivos</h2>";
$archivos = [
    'config/configuracion.php',
    'config/basedatos.php', 
    'config/rutas.php',
    'src/Controladores/ControladorBase.php',
    'src/Controladores/InicioControlador.php',
    'src/Controladores/AdminExposicionControlador.php',
    'src/Controladores/AdminArticuloControlador.php',
    'src/Controladores/BlogControlador.php'
];

foreach ($archivos as $archivo) {
    $rutaCompleta = __DIR__ . '/../' . $archivo;
    if (file_exists($rutaCompleta)) {
        echo "<p>✅ <strong>$archivo</strong> - Existe</p>";
    } else {
        echo "<p>❌ <strong>$archivo</strong> - NO EXISTE</p>";
    }
}

// 3. Verificar conexión a base de datos
echo "<h2>🗃️ Conexión a Base de Datos</h2>";
try {
    if (file_exists(__DIR__ . '/../config/basedatos.php')) {
        require_once __DIR__ . '/../config/basedatos.php';
        
        echo "<p><strong>Host:</strong> " . (defined('BD_SERVIDOR') ? BD_SERVIDOR : 'NO DEFINIDO') . "</p>";
        echo "<p><strong>Base de Datos:</strong> " . (defined('BD_NOMBRE') ? BD_NOMBRE : 'NO DEFINIDO') . "</p>";
        echo "<p><strong>Usuario:</strong> " . (defined('BD_USUARIO') ? BD_USUARIO : 'NO DEFINIDO') . "</p>";
        
        if (defined('BD_DSN')) {
            $pdo = new PDO(BD_DSN, BD_USUARIO, BD_CONTRASENA);
            echo "<p>✅ <strong>Conexión a BD:</strong> Exitosa</p>";
            
            // Verificar tablas
            $stmt = $pdo->query("SHOW TABLES");
            $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Tablas encontradas:</strong> " . implode(', ', $tablas) . "</p>";
        } else {
            echo "<p>❌ <strong>BD_DSN no definido</strong></p>";
        }
    } else {
        echo "<p>❌ <strong>Archivo de configuración de BD no encontrado</strong></p>";
    }
} catch (Exception $e) {
    echo "<p>❌ <strong>Error de BD:</strong> " . $e->getMessage() . "</p>";
}

// 4. Verificar rutas
echo "<h2>🛣️ Sistema de Rutas</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NO DEFINIDO') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'NO DEFINIDO') . "</p>";
echo "<p><strong>Query String:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'VACÍO') . "</p>";

if (isset($_GET['ruta'])) {
    echo "<p><strong>Ruta GET:</strong> " . $_GET['ruta'] . "</p>";
}

// 5. Pruebas de enlaces
echo "<h2>🔗 Enlaces de Prueba</h2>";
echo "<div style='margin: 10px 0;'>";
echo "<a href='?ruta=/' style='display: block; margin: 5px 0; padding: 10px; background: #f0f0f0; text-decoration: none;'>🏠 Inicio (?ruta=/)</a>";
echo "<a href='?ruta=/blog' style='display: block; margin: 5px 0; padding: 10px; background: #f0f0f0; text-decoration: none;'>📖 Blog (?ruta=/blog)</a>";
echo "<a href='?ruta=/admin' style='display: block; margin: 5px 0; padding: 10px; background: #f0f0f0; text-decoration: none;'>⚙️ Admin (?ruta=/admin)</a>";
echo "<a href='?ruta=/admin/exposiciones' style='display: block; margin: 5px 0; padding: 10px; background: #f0f0f0; text-decoration: none;'>🏛️ Admin Exposiciones (?ruta=/admin/exposiciones)</a>";
echo "<a href='?ruta=/admin/articulos' style='display: block; margin: 5px 0; padding: 10px; background: #f0f0f0; text-decoration: none;'>📝 Admin Artículos (?ruta=/admin/articulos)</a>";
echo "</div>";

echo "<hr>";
echo "<p><em>Archivo de diagnóstico creado el " . date('Y-m-d H:i:s') . "</em></p>";
?>
