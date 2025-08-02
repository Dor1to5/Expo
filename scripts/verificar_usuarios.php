<?php
/**
 * Script para verificar usuarios en la base de datos
 */

// Cargar configuración
if (!defined('BD_HOST')) {
    require_once __DIR__ . '/../config/basedatos.php';
}

try {
    $conexion = new PDO(
        "mysql:host=localhost;dbname=_exposiciones;charset=utf8mb4",
        "desarrollo", 
        "_desarrollo",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    echo "=== USUARIOS EN LA BASE DE DATOS ===\n\n";
    
    $stmt = $conexion->query("SELECT id, nombre, apellidos, email, rol_id, activo, email_verificado, fecha_creacion FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll();
    
    if (empty($usuarios)) {
        echo "No hay usuarios en la base de datos.\n";
    } else {
        printf("%-4s %-15s %-15s %-30s %-7s %-7s %-7s %-20s\n", 
               'ID', 'Nombre', 'Apellidos', 'Email', 'Rol ID', 'Activo', 'Verif.', 'Fecha Creación');
        echo str_repeat('-', 120) . "\n";
        
        foreach ($usuarios as $usuario) {
            printf("%-4d %-15s %-15s %-30s %-7d %-7s %-7s %-20s\n",
                   $usuario['id'],
                   $usuario['nombre'],
                   $usuario['apellidos'],
                   $usuario['email'],
                   $usuario['rol_id'],
                   $usuario['activo'] ? 'Sí' : 'No',
                   $usuario['email_verificado'] ? 'Sí' : 'No',
                   $usuario['fecha_creacion']
            );
        }
        
        echo "\nTotal de usuarios: " . count($usuarios) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
