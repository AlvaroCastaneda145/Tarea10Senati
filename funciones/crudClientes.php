<?php
require_once __DIR__ . '/../clases/Cliente.php'; // Corregir la ruta

$clientes = [];

function agregarCliente(&$clientes, $nombre, $email) {
    try {
        $clientes[] = new Cliente($nombre, $email);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

function listarClientes($clientes) {
    foreach ($clientes as $cliente) {
        echo $cliente->mostrarInfo() . "\n";
    }
}

function editarCliente(&$clientes, $nombreActual, $nuevoNombre, $nuevoEmail) {
    foreach ($clientes as $cliente) {
        if ($cliente->getNombre() === $nombreActual) {
            $cliente->setNombre($nuevoNombre);
            $cliente->setEmail($nuevoEmail);
            return;
        }
    }
    echo "Cliente no encontrado.\n";
}


function eliminarCliente(&$clientes, $nombre) {
    foreach ($clientes as $key => $cliente) {
        if ($cliente->getNombre() === $nombre) {
            unset($clientes[$key]);
            break;
        }
    }
}