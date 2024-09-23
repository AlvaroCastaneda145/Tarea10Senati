<?php
require_once __DIR__ . '/../clases/Producto.php'; // Corregir la ruta

$productos = [];

function agregarProducto(&$productos, $nombre, $precio) {
    try {
        $productos[] = new Producto($nombre, $precio);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

function listarProductos($productos) {
    foreach ($productos as $producto) {
        echo "Producto: " . $producto->getNombre() . ", Precio: $" . $producto->getPrecio() . "\n";
    }
}

function editarProducto(&$productos, $nombreActual, $nuevoNombre, $nuevoPrecio) {
    foreach ($productos as $producto) {
        if ($producto->getNombre() === $nombreActual) {
            $producto->setNombre($nuevoNombre);
            $producto->setPrecio($nuevoPrecio);
            return;
        }
    }
    echo "Producto no encontrado.\n";
}

function eliminarProducto(&$productos, $nombre) {
    foreach ($productos as $key => $producto) {
        if ($producto->getNombre() === $nombre) {
            unset($productos[$key]);
            break;
        }
    }
}
