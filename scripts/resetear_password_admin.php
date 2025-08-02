<?php
/**
 * Script para resetear la contraseña del administrador
 * 
 * Este script cambia la contraseña del usuario administrador
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

// Incluir configuración
require_once __DIR__ . '/../config/basedatos.php';

echo "=== RESETEO DE CONTRASEÑA ADMINISTRADOR ===\n";
echo "Conectando a la base de datos...\n";

try {
    // Conectar a la base de datos
    $pdo = new PDO(BD_DSN, BD_USUARIO, BD_CONTRASENA, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✓ Conexión exitosa\n";
    
    // Nueva contraseña
    $nuevaContrasena = 'contraseña';
    $hashContrasena = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
    
    echo "Generando hash de la nueva contraseña...\n";
    echo "✓ Hash generado: " . substr($hashContrasena, 0, 30) . "...\n";
    
    // Buscar el usuario administrador
    $stmt = $pdo->prepare("SELECT id, email, nombre, apellidos FROM usuarios WHERE email = 'admin@exposiciones.local' OR rol_id = 4 LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "❌ No se encontró el usuario administrador\n";
        echo "Creando usuario administrador...\n";
        
        // Crear usuario administrador si no existe
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol_id, activo, email_verificado, fecha_creacion) 
            VALUES ('Administrador', 'del Sistema', 'admin@exposiciones.local', ?, 4, 1, 1, NOW())
        ");
        $stmt->execute([$hashContrasena]);
        
        echo "✓ Usuario administrador creado\n";
        echo "  Email: admin@exposiciones.local\n";
        echo "  Contraseña: $nuevaContrasena\n";
        
    } else {
        echo "✓ Usuario administrador encontrado:\n";
        echo "  ID: {$admin['id']}\n";
        echo "  Email: {$admin['email']}\n";
        echo "  Nombre: {$admin['nombre']} {$admin['apellidos']}\n";
        
        // Actualizar contraseña
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ?, fecha_modificacion = NOW() WHERE id = ?");
        $stmt->execute([$hashContrasena, $admin['id']]);
        
        echo "✓ Contraseña actualizada exitosamente\n";
        echo "  Nueva contraseña: $nuevaContrasena\n";
    }
    
    // Verificar que la contraseña se puede validar
    $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE email = 'admin@exposiciones.local'");
    $stmt->execute();
    $hashAlmacenado = $stmt->fetchColumn();
    
    if (password_verify($nuevaContrasena, $hashAlmacenado)) {
        echo "✓ Verificación de contraseña exitosa\n";
    } else {
        echo "❌ Error en la verificación de contraseña\n";
    }
    
    echo "\n=== PROCESO COMPLETADO ===\n";
    echo "Credenciales de acceso:\n";
    echo "Email: admin@exposiciones.local\n";
    echo "Contraseña: $nuevaContrasena\n";
    echo "\nPuedes acceder al sistema en: http://localhost:8000/login\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
