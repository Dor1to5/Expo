<?php
namespace Models;

/**
 * Modelo de Exposición
 */
class Exposicion {
    private $id;
    private $titulo;
    private $descripcion;
    private $fecha;

    public function __construct($id, $titulo, $descripcion, $fecha) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
    }

    // Métodos getters y setters
    public function getId() { return $this->id; }
    public function getTitulo() { return $this->titulo; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFecha() { return $this->fecha; }
}
