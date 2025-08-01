<?php
namespace Utils;

/**
 * Clase para la conexión a la base de datos
 */
class Database {
    private $pdo;

    public function __construct($config) {
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8";
        $this->pdo = new \PDO($dsn, $config['db_user'], $config['db_pass']);
    }

    public function getConnection() {
        return $this->pdo;
    }
}
