<?php
class Producto {
    private $nombre;
    private $precio;

    public function __construct($nombre, $precio) {
        if ($precio <= 0) {
            throw new Exception("El precio debe ser positivo.");
        }
        $this->nombre = $nombre;
        $this->precio = $precio;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getPrecio() {
        return $this->precio;
    }

    // Métodos setter para modificar los atributos
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setPrecio($precio) {
        if ($precio <= 0) {
            throw new Exception("El precio debe ser positivo.");
        }
        $this->precio = $precio;
    }
}