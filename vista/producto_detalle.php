<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Detalles del Producto'); ?></title>
    <!-- Incluir Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins de Google Fonts (incluye Regular, Medium, SemiBold, Bold) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .product-image {
            max-width: 100%; height: 300px; object-fit: contain; border-radius: 0.75rem; border: 2px solid #FFD93D;
        }
        .btn-action {
            background-color: #FF4747; color: white; font-weight: 600; border-radius: 0.5rem; padding: 0.75rem 1.5rem; transition: background-color 0.3s ease;
        }
        .btn-action:hover { background-color: #DC3545; }
        .text-price { color: #FF4747; }
    </style>
</head>
<body class="bg-[#D6F0FF] min-h-screen flex flex-col text-[#333333] font-normal"> 
    <header class="bg-[#BFEFFF] shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-[#333333] font-poppins">
                <?php echo htmlspecialchars($titulo_pagina ?? 'Detalles del Producto'); ?>
            </h1>
            <nav>
                <ul class="flex space-x-6 text-base">
                    <!-- CORRECCIÓN: Enlaces a index.php con la acción -->
                    <li><a href="index.php?action=catalogo" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Catálogo</a></li>
                    <li><a href="index.php?action=login" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Iniciar Sesión</a></li>
                    <li><a href="index.php?action=registro" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Registrarse</a></li>
                    <li>
                        <a href="index.php?action=ver_carrito" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">
                            Carrito 
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">
        <?php if (!empty($producto_detalle)) { ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row p-6">
                <div class="md:w-1/2 flex justify-center items-center p-4">
                    <img src="../<?php echo htmlspecialchars($producto_detalle[3]); ?>" 
                         alt="<?php echo htmlspecialchars($producto_detalle[1]); ?>" 
                         class="product-image">
                </div>
                <div class="md:w-1/2 p-4 flex flex-col justify-center">
                    <h1 class="text-3xl font-bold text-[#333333] font-poppins mb-4">
                        <?php echo htmlspecialchars($producto_detalle[1]); ?>
                    </h1>
                    <p class="text-4xl font-bold text-price mb-6">
                        <?php echo htmlspecialchars(number_format($producto_detalle[2], 2)); ?> €
                    </p>
                    <p class="text-base text-[#333333] font-normal leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($producto_detalle[4])); ?>
                    </p>
                    <!-- CORRECCIÓN: Form action a index.php -->
                    <form action="index.php" method="post" class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <input type="hidden" name="action" value="anadir_a_carrito">
                        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto_detalle[0]); ?>">
                        <label for="cantidad-producto" class="sr-only">Cantidad:</label>
                        <input type="number" id="cantidad-producto" name="cantidad" value="1" min="1" max="99" 
                               class="w-full sm:w-24 p-3 border border-[#FFD93D] rounded-md text-center text-base focus:ring-[#FF4747] focus:border-[#FF4747] shadow-sm">
                        <button type="submit" class="btn-action w-full sm:w-auto text-base font-semibold">
                            Añadir al Carrito
                        </button>
                    </form>
                    <!-- CORRECCIÓN: Enlace a index.php con la acción -->
                    <a href="index.php?action=catalogo" class="mt-8 text-center text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">
                        &larr; Volver al Catálogo
                    </a>
                </div>
            </div>
        <?php } else { ?>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <p class="text-2xl text-error font-semibold mb-4">Producto no encontrado.</p>
                <p class="text-base text-[#333333]">Lo sentimos, el juguete que buscas no está disponible o no existe.</p>
                <!-- CORRECCIÓN: Enlace a index.php con la acción -->
                <a href="index.php?action=catalogo" class="mt-4 inline-block text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">
                    Volver al Catálogo
                </a>
            </div>
        <?php } ?>
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
