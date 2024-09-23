<?php
// Incluir clases y funciones necesarias
require_once 'clases/Cliente.php';
require_once 'clases/Producto.php';
require_once 'clases/Factura.php';
require_once 'clases/Boleta.php';
require_once 'funciones/crudClientes.php';
require_once 'funciones/crudProductos.php';

session_start();

// Si no existen los arrays en la sesión, inicializarlos
if (!isset($_SESSION['clientes'])) {
    $_SESSION['clientes'] = [];
}
if (!isset($_SESSION['productos'])) {
    $_SESSION['productos'] = [];
}

$clientes = &$_SESSION['clientes'];
$productos = &$_SESSION['productos'];

$facturaGenerada = false;
$documento = null;

// Procesar el formulario de agregar cliente o editar cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // EDITAR CLIENTE
    if (isset($_POST['editar_cliente'])) {
        $nombreActualCliente = $_POST['nombre_actual_cliente'];
        $nuevoNombreCliente = $_POST['nuevo_nombre_cliente'];
        $nuevoEmailCliente = $_POST['nuevo_email_cliente'];

        editarCliente($clientes, $nombreActualCliente, $nuevoNombreCliente, $nuevoEmailCliente);
    }

    // EDITAR PRODUCTO
    if (isset($_POST['editar_producto'])) {
        $nombreActualProducto = $_POST['nombre_actual_producto'];
        $nuevoNombreProducto = $_POST['nuevo_nombre_producto'];
        $nuevoPrecioProducto = $_POST['nuevo_precio_producto'];

        editarProducto($productos, $nombreActualProducto, $nuevoNombreProducto, $nuevoPrecioProducto);
    }

    // AGREGAR CLIENTE
    if (isset($_POST['agregar_cliente'])) {
        $nombreCliente = $_POST['nombre_cliente'];
        $emailCliente = $_POST['email_cliente'];

        $existeCliente = false;
        foreach ($clientes as $cliente) {
            if ($cliente->getNombre() === $nombreCliente) {
                $existeCliente = true;
                break;
            }
        }

        if (!$existeCliente) {
            agregarCliente($clientes, $nombreCliente, $emailCliente);
        } else {
            echo "Error: El cliente ya existe.";
        }
    }

    // AGREGAR PRODUCTO
    if (isset($_POST['agregar_producto'])) {
        $nombreProducto = $_POST['nombre_producto'];
        $precioProducto = $_POST['precio_producto'];

        $existeProducto = false;
        foreach ($productos as $producto) {
            if ($producto->getNombre() === $nombreProducto) {
                $existeProducto = true;
                break;
            }
        }

        if (!$existeProducto) {
            agregarProducto($productos, $nombreProducto, $precioProducto);
        } else {
            echo "Error: El producto ya existe.";
        }
    }

    // ELIMINAR CLIENTE
    if (isset($_POST['eliminar_cliente'])) {
        $nombreClienteEliminar = $_POST['nombre_cliente_eliminar'];
        eliminarCliente($clientes, $nombreClienteEliminar);
    }

    // ELIMINAR PRODUCTO
    if (isset($_POST['eliminar_producto'])) {
        $nombreProductoEliminar = $_POST['nombre_producto_eliminar'];
        eliminarProducto($productos, $nombreProductoEliminar);
    }

    // PROCESAR LA GENERACIÓN DE FACTURA O BOLETA
    if (isset($_POST['generar_documento'])) {
        $clienteSeleccionado = $_POST['cliente'];
        $tipoDocumento = $_POST['tipo_documento'];
        $productosSeleccionados = $_POST['productos'];

        // Buscar el cliente seleccionado
        foreach ($clientes as $cliente) {
            if ($cliente->getNombre() === $clienteSeleccionado) {
                $clienteActual = $cliente;
                break;
            }
        }

        // Crear el documento adecuado según el tipo seleccionado
        switch ($tipoDocumento) {
            case 'factura':
                $documento = new Factura($clienteActual);
                break;
            case 'boleta':
                $documento = new Boleta($clienteActual);
                break;
            default:
                echo "Tipo de documento no válido.\n";
                exit;
        }

        // Agregar los productos seleccionados al documento
        foreach ($productos as $producto) {
            if (in_array($producto->getNombre(), $productosSeleccionados)) {
                $documento->agregarProducto($producto);
            }
        }

        $facturaGenerada = true;
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación Electrónica</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Sistema de Facturación Electrónica</h1>
</header>

<div class="container">

    <!-- Columna izquierda: Formularios -->
    <div class="formulario">
        <!-- Formulario para agregar cliente -->
        <form action="index.php" method="POST">
            <h2>Agregar Cliente</h2>
            <input type="text" name="nombre_cliente" placeholder="Nombre del Cliente" required>
            <input type="email" name="email_cliente" placeholder="Email del Cliente" required>
            <input type="submit" name="agregar_cliente" value="Agregar Cliente">
        </form>

        <!-- Formulario para agregar producto -->
        <form action="index.php" method="POST">
            <h2>Agregar Producto</h2>
            <input type="text" name="nombre_producto" placeholder="Nombre del Producto" required>
            <input type="number" name="precio_producto" placeholder="Precio del Producto" required>
            <input type="submit" name="agregar_producto" value="Agregar Producto">
        </form>

        <!-- Formulario para generar Factura o Boleta -->
        <form action="index.php" method="POST">
            <h2>Generar Documento (Factura o Boleta)</h2>
            <label>Seleccionar Cliente:</label>
            <select name="cliente" required>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo $cliente->getNombre(); ?>">
                        <?php echo $cliente->getNombre(); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Seleccionar Productos:</label>
            <select name="productos[]" multiple required>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?php echo $producto->getNombre(); ?>">
                        <?php echo $producto->getNombre() . " - $" . $producto->getPrecio(); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tipo de Documento:</label>
            <select name="tipo_documento" required>
                <option value="factura">Factura</option>
                <option value="boleta">Boleta</option>
            </select>
            <input type="submit" name="generar_documento" value="Generar Documento">
        </form>

        <?php if ($facturaGenerada && $documento): ?>
            <h3>Documento Generado:</h3>
            <p>Tipo: <?php echo ucfirst($tipoDocumento); ?></p>
            <p>Cliente: <?php echo $documento->getCliente()->getNombre(); ?></p>
            <p>Total: $<?php echo $documento->getTotal(); ?></p>
        <?php endif; ?>
    </div>

    <!-- Columna derecha: Tablas -->
    <div class="tablas">
        <!-- Tabla de clientes -->
        <h2>Clientes</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente->getNombre(); ?></td>
                    <td><?php echo $cliente->getEmail(); ?></td>
                    <td>
                        <!-- Botón de Editar -->
                        <form action="editar_cliente.php" method="GET" style="display:inline;">
                            <input type="hidden" name="nombre_cliente" value="<?php echo $cliente->getNombre(); ?>">
                            <button type="submit">Editar</button>
                        </form>

                        <!-- Botón de Eliminar -->
                        <form action="index.php" method="POST" style="display:inline;">
                            <input type="hidden" name="nombre_cliente_eliminar" value="<?php echo $cliente->getNombre(); ?>">
                            <button class="eliminar" type="submit" name="eliminar_cliente">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Tabla de productos -->
        <h2>Productos</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?php echo $producto->getNombre(); ?></td>
                    <td><?php echo $producto->getPrecio(); ?></td>
                    <td>
                        <!-- Botón de Editar -->
                        <form action="editar_producto.php" method="GET" style="display:inline;">
                            <input type="hidden" name="nombre_producto" value="<?php echo $producto->getNombre(); ?>">
                            <button type="submit">Editar</button>
                        </form>

                        <!-- Botón de Eliminar -->
                        <form action="index.php" method="POST" style="display:inline;">
                            <input type="hidden" name="nombre_producto_eliminar" value="<?php echo $producto->getNombre(); ?>">
                            <button class="eliminar" type="submit" name="eliminar_producto">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>

<script>
// Funciones de mostrar formularios de edición
function mostrarEditarCliente(nombre, email) {
    document.getElementById('agregar_cliente_form').style.display = 'none';
    document.getElementById('editar_cliente_form').style.display = 'block';
    document.querySelector('[name="nombre_actual_cliente"]').value = nombre;
    document.querySelector('[name="nuevo_nombre_cliente"]').value = nombre;
    document.querySelector('[name="nuevo_email_cliente"]').value = email;
}

function mostrarEditarProducto(nombre, precio) {
    document.getElementById('agregar_producto_form').style.display = 'none';
    document.getElementById('editar_producto_form').style.display = 'block';
    document.querySelector('[name="nombre_actual_producto"]').value = nombre;
    document.querySelector('[name="nuevo_nombre_producto"]').value = nombre;
    document.querySelector('[name="nuevo_precio_producto"]').value = precio;
}
</script>

</body>
</html>
