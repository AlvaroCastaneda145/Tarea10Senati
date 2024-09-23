<?php
require_once 'Persona.php';

class Cliente extends Persona {
    public function __construct($nombre, $email) {
        if (empty($nombre) || empty($email)) {
            throw new Exception("El nombre y el email no pueden estar vacíos.");
        }
        parent::__construct($nombre, $email);
    }

    public function mostrarInfo() {
        return "Cliente: {$this->nombre}, Email: {$this->email}";
    }

    // Métodos setter para modificar los atributos
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setEmail($email) {
        $this->email = $email;
    }
}