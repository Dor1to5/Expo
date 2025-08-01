<?php
namespace Models;

/**
 * Modelo de Rol
 */
class Rol {
    private $id;
    private $nombre;

    public function __construct($id, $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    // Métodos getters y setters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
}
