<?php
require_once 'DocumentoFiscal.php';

class Boleta extends DocumentoFiscal {
    public function calcularTotal() {
        $total = 0;
        foreach ($this->productos as $producto) {
            $total += $producto->getPrecio();
        }
        return $total * 0.95; // Ejemplo de descuento
    }
    public function getCliente() {
        return $this->cliente;
    }
    public function getTotal() {
        return $this->calcularTotal();
    }
}
