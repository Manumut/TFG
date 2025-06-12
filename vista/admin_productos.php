<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Gestión de Productos'); ?></title>
    <!-- Incluir Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins de Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Estilos adicionales para la tabla */
        .table-auto {
            width: 100%;
            border-collapse: collapse;
        }
        .table-auto th, .table-auto td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0; /* tailwind's gray-200 */
        }
        .table-auto th {
            background-color: #F8F8F8; /* Un gris muy claro para el encabezado */
            font-weight: 600; /* SemiBold */
            color: #333333;
        }
        .table-auto tbody tr:nth-child(even) {
            background-color: #F8F8F8; /* Fondo claro para filas pares */
        }
        .table-auto tbody tr:hover {
            background-color: #E6F7FF; /* Azul suave al pasar el ratón */
            transition: background-color 0.2s ease-in-out;
        }
        /* Estilo para las imágenes de producto en la tabla */
        .product-table-image {
            width: 60px; /* Ancho fijo para las miniaturas */
            height: 60px; /* Altura fija */
            object-fit: contain; /* Asegura que la imagen se vea completa */
            border-radius: 0.25rem; /* Bordes ligeramente redondeados */
            border: 1px solid #e2e8f0; /* Borde sutil */
        }
    </style>
</head>
<body class="bg-[#D6F0FF] min-h-screen flex flex-col text-[#333333] font-normal">
    <!-- Cabecera compartida con el dashboard de admin -->
    <header class="bg-[#BFEFFF] shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-[#333333] font-poppins">
                Panel de Administración
            </h1>
            <nav>
                <ul class="flex space-x-6 text-base">
                    <li><a href="/TFG/index.php?action=administracion" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Dashboard</a></li>
                    <li><a href="/TFG/index.php?action=gestionar_usuarios" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Usuarios</a></li>
                    <li><a href="/TFG/index.php?action=gestionar_productos" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Productos</a></li>
                    <li><a href="/TFG/index.php?action=cerrar_sesion_admin" class="text-[#FF4747] hover:text-[#333333] font-medium transition duration-300">Cerrar Sesión</a></li>
            </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-6">Gestión de Productos</h2>

            <!-- Sección de Búsqueda y Añadir Producto -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0 md:space-x-4">
                <!-- Formulario de Búsqueda de Productos -->
                <form action="/TFG/index.php" method="get" class="flex-grow flex items-center space-x-2 w-full md:w-auto">
                    <input type="hidden" name="action" value="gestionar_productos">
                    <label for="search-product" class="sr-only">Buscar Producto:</label>
                    <input type="text" id="search-product" name="busqueda" placeholder="Buscar por título..."
                           class="flex-grow p-2 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                    <button type="submit" class="px-4 py-2 bg-[#FF4747] text-white font-semibold rounded-md hover:bg-red-600 transition duration-300 text-base">
                        Buscar
                    </button>
                </form>
                
                <!-- Botón para Añadir Nuevo Producto -->
                <a href="/TFG/index.php?action=anadir_producto" class="px-4 py-2 bg-[#00AEEF] text-white font-semibold rounded-md hover:bg-blue-600 transition duration-300 text-base flex-shrink-0">
                    + Añadir Nuevo Producto
                </a>
            </div>

            <!-- Tabla de Listado de Productos -->
            <?php
            // La variable $productos se pasará desde el controlador Admin.php
            
            // Si el controlador pasa $productos, usarlo. Si no, usar el ejemplo.
            $productos_a_mostrar = $productos ?? $productos_ejemplo;

            if (empty($productos_a_mostrar)) {
                echo "<p class='text-center text-[#333333] text-lg'>No se encontraron productos.</p>";
            } else {
            ?>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead>
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Imagen</th>
                                <th class="py-3 px-4">Título</th>
                                <th class="py-3 px-4">Precio</th>
                                <th class="py-3 px-4">Marca</th>
                                <th class="py-3 px-4">Stock</th>
                                <th class="py-3 px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_a_mostrar as $producto) { ?>
                                <tr>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($producto[0]); ?></td>
                                    <td class="py-3 px-4">
                                        <img src="/<?php echo htmlspecialchars($producto[3]); ?>" 
                                             alt="<?php echo htmlspecialchars($producto[1]); ?>" 
                                             class="product-table-image">
                                    </td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($producto[1]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars(number_format($producto[2], 2)); ?> €</td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($producto[7]); ?></td> <!-- Nombre de la marca -->
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($producto[6]); ?></td> <!-- Cantidad/Stock -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <!-- Botones de Acción (Editar/Eliminar) -->
                                        <a href="/TFG/index.php?action=editar_producto&id_producto=<?php echo htmlspecialchars($producto[0]); ?>" class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition duration-300 text-sm">Editar</a>
                                        <a href="/TFG/index.php?action=eliminar_producto&id=<?php echo htmlspecialchars($producto[0]); ?>" 
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');"
                                           class="inline-block px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300 text-sm ml-2">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Panel de Administración.</p>
    </footer>
</body>
</html>
