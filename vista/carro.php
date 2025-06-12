<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Carrito de Compras'); ?></title>
    <!-- Incluir Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins de Google Fonts (incluye Regular, Medium, SemiBold, Bold) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .product-image {
            width: 80px; /* Tamaño de la imagen en el carrito */
            height: 80px;
            object-fit: contain;
            border-radius: 0.25rem;
            border: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            overflow: hidden; /* Para que los bordes redondeados se apliquen a la tabla */
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #BFEFFF;
            color: #333333;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tfoot td {
            font-size: 1.125rem; /* text-lg */
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            border-top: 2px solid #00AEEF;
        }
        .text-error { color: #DC3545; }
    </style>
</head>
<body class="bg-[#D6F0FF] min-h-screen flex flex-col text-[#333333] font-normal">
    <header class="bg-[#BFEFFF] shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <img src="imagenes/logo.png" alt="All in Toys" style="height:48px;">
            <h1 class="text-3xl font-bold text-[#333333] font-poppins">
                <?php echo htmlspecialchars($titulo_pagina ?? 'Tu Carrito de Compras'); ?>
            </h1>
            <nav>
                <ul class="flex space-x-6 text-base">
                    <!-- Enlaces a index.php con la acción -->
                    <li><a href="index.php?action=catalogo_registrado" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Volver al Catálogo</a></li>
                    <?php if (isset($nombre_usuario_logueado)): ?>
                        <li>Bienvenido, <?php echo htmlspecialchars($nombre_usuario_logueado); ?></li>
                        <li><a href="index.php?action=cerrar_sesion" class="text-[#FF4747] hover:text-[#333333] font-medium transition duration-300">Cerrar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">
        <section class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-6 text-center">Contenido de tu Carrito</h2>

            <?php 
            // Mostrar mensajes de éxito o error
            if (isset($_GET['mensaje'])) { ?>
                <p class="text-green-600 text-center mb-4 font-medium text-base">
                    <?php 
                        if ($_GET['mensaje'] === 'producto_anadido') {
                            echo "¡Producto añadido al carrito con éxito!";
                        } elseif ($_GET['mensaje'] === 'producto_eliminado') {
                            echo "Producto eliminado del carrito.";
                        }
                    ?>
                </p>
            <?php } ?>

            <?php if (isset($_GET['error'])) { ?>
                <p class="text-error text-center mb-4 font-medium text-base">
                    <?php 
                        if ($_GET['error'] === 'error_anadir_carrito') {
                            echo "Hubo un error al añadir el producto al carrito. Inténtalo de nuevo.";
                        } elseif ($_GET['error'] === 'datos_invalidos_carrito') {
                            echo "Datos inválidos para la operación del carrito.";
                        } elseif ($_GET['error'] === 'error_actualizar_cantidad') {
                            echo "Hubo un error al actualizar la cantidad del producto.";
                        } elseif ($_GET['error'] === 'error_eliminar_producto') {
                            echo "Hubo un error al eliminar el producto del carrito.";
                        } elseif ($_GET['error'] === 'error_procesar_pedido') {
                            echo "Hubo un error al procesar el pedido. Inténtalo de nuevo.";
                        } elseif ($_GET['error'] === 'carrito_vacio') {
                            echo "No puedes procesar un pedido con el carrito vacío.";
                        }
                    ?>
                </p>
            <?php } ?>

            <?php if (!empty($items_carrito)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Imagen</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_global = 0;
                        foreach ($items_carrito as $item): 
                            // $item es un array numérico: [0 => id_producto, 1 => titulo, 2 => imagen_producto, 3 => cantidad, 4 => precio_actual, 5 => subtotal]
                            $total_global += $item[5]; // Suma el subtotal al total global
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item[1]); ?></td>
                                <td>
                                    <!-- CORRECCIÓN CLAVE: Eliminar el echo del texto de la ruta y solo usar la imagen -->
                                    <img src="../<?php echo htmlspecialchars($item[2]); ?>" alt="<?php echo htmlspecialchars($item[1]); ?>" class="product-image">
                                </td>
                                <td>
                                    <!-- Formulario para actualizar cantidad - Apuntando a index.php -->
                                    <form action="/TFG/index.php?action=actualizar_cantidad_carrito" method="post" class="flex items-center space-x-2">
                                        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($item[0]); ?>">
                                        <input type="number" name="nueva_cantidad" value="<?php echo htmlspecialchars($item[3]); ?>" min="0" 
                                               class="w-20 p-2 border border-[#FFD93D] rounded-md text-center text-base focus:ring-[#FF4747] focus:border-[#FF4747]">
                                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition duration-300 text-sm">
                                            Actualizar
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars(number_format($item[4], 2)); ?> €</td>
                                <td><?php echo htmlspecialchars(number_format($item[5], 2)); ?> €</td>
                                <td>
                                    <!-- Enlace para eliminar producto - Apuntando a index.php -->
                                    <a href="/TFG/index.php?action=eliminar_producto_carrito&id_producto=<?php echo htmlspecialchars($item[0]); ?>" class="text-red-500 hover:text-red-700 transition duration-300 text-sm">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right font-bold text-lg">Total del Carrito:</td>
                            <td colspan="2" class="font-bold text-lg text-[#FF4747]"><?php echo htmlspecialchars(number_format($total_global, 2)); ?> €</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="mt-8 text-center">
                    <!-- Formulario para procesar pedido - Apuntando a index.php -->
                    <form action="/TFG/index.php?action=procesar_pedido" method="post">
                        <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg text-lg hover:bg-green-600 transition duration-300 shadow-md">
                            Procesar Pedido
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <p class="text-center text-xl mt-10">Tu carrito está vacío.</p>
                <div class="text-center mt-5">
                    <a href="index.php?action=catalogo_registrado" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300 shadow-sm">
                        Volver al Catálogo
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Tu Tienda. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
