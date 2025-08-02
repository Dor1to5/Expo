<?php
/**
 * Test diagnóstico del sistema - Verificar qué está roto
 */

echo "<h1>🔍 Diagnóstico del Sistema</h1>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px;'>";

// 1. Verificar configuración básica
echo "<h2>1. ✅ Verificando configuración...</h2>";
try {
    require_once __DIR__ . '/config/configuracion.php';
    echo "<p>✅ configuracion.php: OK</p>";
} catch (Exception $e) {
    echo "<p>❌ configuracion.php: " . $e->getMessage() . "</p>";
}

try {
    require_once __DIR__ . '/config/basedatos.php';
    echo "<p>✅ basedatos.php: OK</p>";
} catch (Exception $e) {
    echo "<p>❌ basedatos.php: " . $e->getMessage() . "</p>";
}

try {
    require_once __DIR__ . '/config/rutas.php';
    echo "<p>✅ rutas.php: OK</p>";
} catch (Exception $e) {
    echo "<p>❌ rutas.php: " . $e->getMessage() . "</p>";
}

// 2. Verificar archivos críticos
echo "<h2>2. 📁 Verificando archivos críticos...</h2>";
$archivosCriticos = [
    '/src/Utilidades/BaseDatos.php',
    '/src/Controladores/ControladorBase.php', 
    '/src/Controladores/InicioControlador.php',
    '/src/Controladores/AdminControlador.php',
    '/src/Controladores/AdminUsuarioControlador.php',
    '/src/Servicios/ServicioAutenticacion.php',
    '/src/Modelos/ModeloBase.php'
];

foreach ($archivosCriticos as $archivo) {
    $rutaCompleta = __DIR__ . $archivo;
    if (file_exists($rutaCompleta)) {
        echo "<p>✅ $archivo: Existe</p>";
    } else {
        echo "<p>❌ $archivo: NO EXISTE</p>";
    }
}

// 3. Configurar autoloader y probar clases
echo "<h2>3. 🔄 Configurando autoloader...</h2>";
spl_autoload_register(function ($clase) {
    $archivo = str_replace('\\', DIRECTORY_SEPARATOR, $clase);
    $rutasPosibles = [
        __DIR__ . '/src/' . $archivo . '.php',
        __DIR__ . '/' . $archivo . '.php'
    ];
    
    foreach ($rutasPosibles as $ruta) {
        if (file_exists($ruta)) {
            require_once $ruta;
            echo "<p>📦 Cargada clase: $clase</p>";
            return;
        }
    }
    echo "<p>⚠️ No se pudo cargar: $clase</p>";
});

// 4. Test de conexión a base de datos
echo "<h2>4. 🗄️ Test de base de datos...</h2>";
try {
    $db = new Utilidades\BaseDatos();
    echo "<p>✅ Conexión a BD: OK</p>";
} catch (Exception $e) {
    echo "<p>❌ Error BD: " . $e->getMessage() . "</p>";
}

// 5. Test de rutas
echo "<h2>5. 🛣️ Test de sistema de rutas...</h2>";
try {
    $resultado = Config\Rutas::resolverRuta('/');
    echo "<p>✅ Sistema de rutas funciona</p>";
    echo "<p>Ruta '/' resuelve a: <pre>" . print_r($resultado, true) . "</pre></p>";
} catch (Exception $e) {
    echo "<p>❌ Error en rutas: " . $e->getMessage() . "</p>";
}

// 6. Test de controladores
echo "<h2>6. 🎮 Test de controladores...</h2>";
try {
    session_start();
    echo "<p>✅ Sesión iniciada</p>";
    
    // Test InicioControlador
    $inicio = new Controladores\InicioControlador();
    echo "<p>✅ InicioControlador: OK</p>";
    
    // Test AdminControlador  
    $admin = new Controladores\AdminControlador();
    echo "<p>✅ AdminControlador: OK</p>";
    
    // Test AdminUsuarioControlador
    $adminUsuario = new Controladores\AdminUsuarioControlador();
    echo "<p>✅ AdminUsuarioControlador: OK</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error en controladores: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
    $_POST['email'] = 'test@test.com';
    $_POST['password'] = 'password123';
// 6. Test de controladores
echo "<h2>6. 🎮 Test de controladores...</h2>";
try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    echo "<p>✅ Sesión iniciada</p>";
    
    // Test InicioControlador
    $inicio = new Controladores\InicioControlador();
    echo "<p>✅ InicioControlador: OK</p>";
    
    // Test AdminControlador  
    $admin = new Controladores\AdminControlador();
    echo "<p>✅ AdminControlador: OK</p>";
    
    // Test AdminUsuarioControlador
    $adminUsuario = new Controladores\AdminUsuarioControlador();
    echo "<p>✅ AdminUsuarioControlador: OK</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error en controladores: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></p>";
}

echo "</div>";
echo "<hr>";
echo "<p><em>Diagnóstico completado el " . date('Y-m-d H:i:s') . "</em></p>";
?>
