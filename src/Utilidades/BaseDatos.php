<?php
/**
 * Clase para la gestión de la base de datos
 * 
 * Esta clase proporciona una interfaz segura y eficiente para todas
 * las operaciones de base de datos del sistema utilizando PDO.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Utilidades;

use PDO;
use PDOException;
use Exception;

/**
 * Clase BaseDatos - Maneja todas las conexiones y operaciones de BD
 */
class BaseDatos {
    
    /**
     * Instancia única de la conexión (Singleton)
     * @var PDO|null
     */
    private static ?PDO $instancia = null;
    
    /**
     * Instancia actual de la clase
     * @var BaseDatos|null
     */
    private static ?BaseDatos $instanciaClase = null;
    
    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct() {
        // Constructor privado para evitar instanciación directa
    }
    
    /**
     * Obtiene la instancia única de la clase BaseDatos
     * @return BaseDatos Instancia única
     */
    public static function obtenerInstancia(): BaseDatos {
        if (self::$instanciaClase === null) {
            self::$instanciaClase = new self();
        }
        
        return self::$instanciaClase;
    }
    
    /**
     * Obtiene la conexión PDO a la base de datos
     * @return PDO Conexión activa a la base de datos
     * @throws Exception Si no se puede conectar a la base de datos
     */
    public function obtenerConexion(): PDO {
        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
                    \BD_DSN,
                    \BD_USUARIO,
                    \BD_CONTRASENA,
                    \BD_OPCIONES
                );
                
                // Configurar el modo de error para que lance excepciones
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new Exception("No se pudo conectar a la base de datos");
            }
        }
        
        return self::$instancia;
    }
    
    /**
     * Ejecuta una consulta SELECT y devuelve todos los resultados
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros para la consulta preparada
     * @return array Resultados de la consulta
     * @throws Exception Si hay error en la consulta
     */
    public function seleccionar(string $sql, array $parametros = []): array {
        try {
            $sentencia = $this->obtenerConexion()->prepare($sql);
            $sentencia->execute($parametros);
            return $sentencia->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en consulta SELECT: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Error al ejecutar la consulta de selección");
        }
    }
    
    /**
     * Ejecuta una consulta SELECT y devuelve un solo resultado
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros para la consulta preparada
     * @return array|null Resultado de la consulta o null si no hay resultados
     * @throws Exception Si hay error en la consulta
     */
    public function seleccionarUno(string $sql, array $parametros = []): ?array {
        try {
            $sentencia = $this->obtenerConexion()->prepare($sql);
            $sentencia->execute($parametros);
            $resultado = $sentencia->fetch();
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("Error en consulta SELECT (uno): " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Error al ejecutar la consulta de selección");
        }
    }
    
    /**
     * Ejecuta una consulta INSERT
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros para la consulta preparada
     * @return int ID del último registro insertado
     * @throws Exception Si hay error en la consulta
     */
    public function insertar(string $sql, array $parametros = []): int {
        try {
            $sentencia = $this->obtenerConexion()->prepare($sql);
            $sentencia->execute($parametros);
            return (int) $this->obtenerConexion()->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en consulta INSERT: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Error al insertar el registro");
        }
    }
    
    /**
     * Ejecuta una consulta UPDATE
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros para la consulta preparada
     * @return int Número de filas afectadas
     * @throws Exception Si hay error en la consulta
     */
    public function actualizar(string $sql, array $parametros = []): int {
        try {
            $sentencia = $this->obtenerConexion()->prepare($sql);
            $sentencia->execute($parametros);
            return $sentencia->rowCount();
        } catch (PDOException $e) {
            error_log("Error en consulta UPDATE: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Error al actualizar el registro");
        }
    }
    
    /**
     * Ejecuta una consulta DELETE
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros para la consulta preparada
     * @return int Número de filas eliminadas
     * @throws Exception Si hay error en la consulta
     */
    public function eliminar(string $sql, array $parametros = []): int {
        try {
            $sentencia = $this->obtenerConexion()->prepare($sql);
            $sentencia->execute($parametros);
            return $sentencia->rowCount();
        } catch (PDOException $e) {
            error_log("Error en consulta DELETE: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Error al eliminar el registro");
        }
    }
    
    /**
     * Ejecuta cualquier consulta SQL (para casos especiales)
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros para la consulta preparada
     * @return bool True si se ejecutó correctamente
     * @throws Exception Si hay error en la consulta
     */
    public function ejecutar(string $sql, array $parametros = []): bool {
        try {
            $sentencia = $this->obtenerConexion()->prepare($sql);
            return $sentencia->execute($parametros);
        } catch (PDOException $e) {
            error_log("Error en consulta ejecutar: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Error al ejecutar la consulta");
        }
    }
    
    /**
     * Inicia una transacción
     * @return bool True si se inició correctamente
     * @throws Exception Si no se puede iniciar la transacción
     */
    public function iniciarTransaccion(): bool {
        try {
            return $this->obtenerConexion()->beginTransaction();
        } catch (PDOException $e) {
            error_log("Error al iniciar transacción: " . $e->getMessage());
            throw new Exception("Error al iniciar la transacción");
        }
    }
    
    /**
     * Confirma una transacción
     * @return bool True si se confirmó correctamente
     * @throws Exception Si no se puede confirmar la transacción
     */
    public function confirmarTransaccion(): bool {
        try {
            return $this->obtenerConexion()->commit();
        } catch (PDOException $e) {
            error_log("Error al confirmar transacción: " . $e->getMessage());
            throw new Exception("Error al confirmar la transacción");
        }
    }
    
    /**
     * Revierte una transacción
     * @return bool True si se revirtió correctamente
     * @throws Exception Si no se puede revertir la transacción
     */
    public function revertirTransaccion(): bool {
        try {
            return $this->obtenerConexion()->rollBack();
        } catch (PDOException $e) {
            error_log("Error al revertir transacción: " . $e->getMessage());
            throw new Exception("Error al revertir la transacción");
        }
    }
    
    /**
     * Verifica si hay una transacción activa
     * @return bool True si hay una transacción activa
     */
    public function hayTransaccionActiva(): bool {
        return $this->obtenerConexion()->inTransaction();
    }
    
    /**
     * Escapa una cadena para uso seguro en consultas SQL
     * @param string $valor Valor a escapar
     * @return string Valor escapado
     */
    public function escapar(string $valor): string {
        return $this->obtenerConexion()->quote($valor);
    }
    
    /**
     * Obtiene información sobre la última consulta ejecutada
     * @return array Información de la consulta
     */
    public function obtenerInfoUltimaConsulta(): array {
        $conexion = $this->obtenerConexion();
        return [
            'ultimo_id' => $conexion->lastInsertId(),
            'filas_afectadas' => 0, // Se actualiza en cada método
            'estado_conexion' => $conexion->getAttribute(PDO::ATTR_CONNECTION_STATUS)
        ];
    }
    
    /**
     * Cierra la conexión a la base de datos
     */
    public function cerrarConexion(): void {
        self::$instancia = null;
    }
    
    /**
     * Destructor para cerrar la conexión automáticamente
     */
    public function __destruct() {
        $this->cerrarConexion();
    }
    
    /**
     * Previene la clonación de la instancia
     */
    public function __clone() {
        throw new Exception("No se puede clonar una instancia Singleton");
    }
    
    /**
     * Previene la deserialización de la instancia
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar una instancia Singleton");
    }
}
