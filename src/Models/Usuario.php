<?php
namespace Models;

/**
 * Modelo de Usuario
 */
class Usuario {
    private $id;
    private $nombre;
    private $rol;

    public function __construct($id, $nombre, $rol) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->rol = $rol;
    }

    // Métodos getters y setters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getRol() { return $this->rol; }
}
