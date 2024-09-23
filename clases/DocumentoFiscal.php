<?php
require_once 'Cliente.php';
require_once 'Producto.php';

abstract class DocumentoFiscal {
    protected $cliente;
    protected $productos = [];

    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
    }

    public function agregarProducto(Producto $producto) {
        $this->productos[] = $producto;
    }

    abstract public function calcularTotal();

    public function mostrarDocumento() {
        echo "Documento para: " . $this->cliente->mostrarInfo() . "<br>";
        echo "Productos:<br>";
        foreach ($this->productos as $producto) {
            echo "- " . $producto->getNombre() . ": $" . $producto->getPrecio() . "<br>";
        }
        echo "Total: $" . $this->calcularTotal() . "<br>";
    }
}
