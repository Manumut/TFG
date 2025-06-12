<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Catálogo de Juguetes'); ?></title>
    <!-- Incluir Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .producto-card img {
            max-width: 100%; height: 200px; object-fit: contain; border-radius: 0.5rem;
        }
        .text-error { color: #DC3545; }
    </style>
</head>
<body class="bg-[#D6F0FF] min-h-screen flex flex-col text-[#333333] font-normal"> 
    <header class="bg-[#BFEFFF] shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <img src="imagenes/logo.png" alt="All in Toys" style="height:48px;">
            <h1 class="text-3xl font-bold text-[#333333] font-poppins">
                <?php echo htmlspecialchars($titulo_pagina ?? 'Tienda de Juguetes'); ?>
            </h1>
            <nav>
                <ul class="flex space-x-6 text-base">
                    <li><a href="index.php?action=catalogo" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Catálogo</a></li>
                    <li><a href="index.php?action=login" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Iniciar Sesión</a></li>
                    <li><a href="index.php?action=registro" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Registrarse</a></li>
                    <li>
                        <a href="index.php?action=ver_carrito" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">
                            Carrito 
                        </a>
                    </li>
                    <li><a href="index.php?action=cerrar_sesion" class="text-[#FF4747] hover:text-[#333333] font-medium transition duration-300">Cerrar Sesión</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">
        <section class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-4">Filtros y Búsqueda</h2>
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6">
                <div class="flex-1">
                    <!-- CORRECCIÓN: Form action a index.php -->
                    <form action="index.php" method="get" class="flex items-center space-x-2">
                        <input type="hidden" name="action" value="filtrar_por_marca">
                        <label for="marca-select" class="text-[#333333] font-medium text-sm">Filtrar por Marca:</label>
                        <select id="marca-select" name="id_marca" onchange="this.form.submit()"
                                class="mt-1 block w-full md:w-auto p-2 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                            <option value="">Todas las Marcas</option>
                            <?php foreach ($marcas ?? [] as $id => $marca) { ?>
                                <option value="<?php echo htmlspecialchars($marca[0]); ?>">
                                    <?php echo htmlspecialchars($marca[1]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </form>
                </div>
                <div class="flex-1">
                    <!-- CORRECCIÓN: Form action a index.php -->
                    <form action="index.php" method="get" class="flex items-center space-x-2">
                        <input type="hidden" name="action" value="buscar_productos">
                        <label for="search-input" class="text-[#333333] font-medium text-sm">Buscar:</label>
                        <input type="text" id="search-input" name="busqueda" placeholder="Buscar productos..."
                               class="flex-grow p-2 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                        <button type="submit" class="px-4 py-2 bg-[#FF4747] text-white font-semibold rounded-md hover:bg-red-600 transition duration-300 text-base">
                            Buscar
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php 
            if (empty($productos)) {
            ?>
                <p class="col-span-full text-center text-[#333333] text-lg">No se encontraron productos.</p>
            <?php
            } else {
                foreach ($productos as $producto) {
            ?>
                    <div class="producto-card bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transform transition duration-300 hover:scale-105">
                        <img src="../<?php echo htmlspecialchars($producto[3]); ?>" 
                             alt="<?php echo htmlspecialchars($producto[1]); ?>" 
                             class="w-full h-48 object-contain">
                        <div class="p-4 flex-grow flex flex-col">
                            <h3 class="text-xl font-semibold text-[#333333] mb-2 font-poppins"><?php echo htmlspecialchars($producto[1]); ?></h3>
                            <p class="text-[#333333] text-lg font-bold mb-4"><?php echo htmlspecialchars(number_format($producto[2], 2)); ?> €</p>
                            <div class="mt-auto flex flex-col space-y-3">
                                <!-- CORRECCIÓN: Enlace a index.php con la acción -->
                                <a href="index.php?action=ver_detalle_producto&id_producto=<?php echo htmlspecialchars($producto[0]); ?>" 
                                   class="block text-center px-4 py-2 bg-[#FF4747] text-white font-semibold rounded-md hover:bg-red-600 transition duration-300 text-base">
                                    Ver Detalles
                                </a>
                                <!-- CORRECCIÓN: Form action a index.php -->
                                <form action="/TFG/index.php" method="post" class="flex space-x-2">
                                    <input type="hidden" name="action" value="anadir_a_carrito">
                                    <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto[0]); ?>">
                                    <input type="number" name="cantidad" value="1" min="1" max="50" 
                                           class="w-1/3 p-2 border border-[#FFD93D] rounded-md text-center focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                                    <button type="submit" 
                                            class="flex-grow px-4 py-2 bg-[#FF4747] text-white font-semibold rounded-md hover:bg-red-600 transition duration-300 text-base">
                                        Añadir al Carrito
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </section>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Tu Tienda. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function updateCartCount(count) {
                const cartCountElement = document.getElementById('num-productos-carrito');
                if (cartCountElement) {
                    cartCountElement.textContent = count;
                }
            }
        });
    </script>
</body>
</html>
