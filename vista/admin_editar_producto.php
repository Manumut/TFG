<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Editar Producto'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .btn-action {
            background-color: #FF4747; color: white; font-weight: 600; border-radius: 0.5rem; padding: 0.75rem 1.5rem; transition: background-color 0.3s ease;
        }
        .btn-action:hover { background-color: #DC3545; }
        .alert-error {
            @apply bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4;
        }
    </style>
</head>
<body class="bg-[#D6F0FF] min-h-screen flex flex-col text-[#333333] font-normal">
    <header class="bg-[#BFEFFF] shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <img src="imagenes/logo.png" alt="All in Toys" style="height:48px;">
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

    <main class="container mx-auto p-6 flex-grow flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-6 text-center">
                <?php echo htmlspecialchars($titulo_pagina); ?>
            </h2>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error text-center">
                    <?php 
                        if ($_GET['error'] === 'campos_vacios') {
                            echo "Todos los campos obligatorios deben rellenarse.";
                        } elseif ($_GET['error'] === 'producto_no_encontrado') {
                            echo "Producto no encontrado.";
                        } elseif ($_GET['error'] === 'error_actualizar_producto') {
                            echo "Error al actualizar el producto. Asegúrate de que el título no sea duplicado o verifica los datos.";
                        } elseif ($_GET['error'] === 'error_subir_nueva_imagen') {
                            echo "Error al subir la nueva imagen. Por favor, inténtalo de nuevo.";
                        } elseif ($_GET['error'] === 'archivo_no_imagen') {
                            echo "El archivo subido no es una imagen válida.";
                        } else {
                            echo htmlspecialchars($_GET['error']);
                        }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($producto_a_editar) && $producto_a_editar): ?>
            <form action="/TFG/index.php?action=procesar_editar_producto" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto_a_editar[0]); ?>">
                <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto_a_editar[3]); ?>"> <!-- Para mantener la imagen actual si no se sube una nueva -->

                <div>
                    <label for="titulo" class="block text-sm font-medium text-[#333333]">Título</label>
                    <input type="text" id="titulo" name="titulo" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#00AEEF] focus:border-[#00AEEF]"
                           value="<?php echo htmlspecialchars($producto_a_editar[1]); ?>">
                </div>
                <div>
                    <label for="precio" class="block text-sm font-medium text-[#333333]">Precio (€)</label>
                    <input type="number" step="0.01" id="precio" name="precio" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#00AEEF] focus:border-[#00AEEF]"
                           value="<?php echo htmlspecialchars($producto_a_editar[2]); ?>">
                </div>
                <div>
                    <label for="imagen_producto" class="block text-sm font-medium text-[#333333]">Cambiar Imagen del Producto</label>
                    <input type="file" id="imagen_producto" name="imagen_producto" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Imagen actual: <img src="/<?php echo htmlspecialchars($producto_a_editar[3]); ?>" alt="Imagen actual" class="inline-block h-12 w-12 object-contain ml-2 border border-gray-200 rounded-md"></p>
                </div>
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-[#333333]">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required
                              class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#00AEEF] focus:border-[#00AEEF]"><?php echo htmlspecialchars($producto_a_editar[4]); ?></textarea>
                </div>
                <div>
                    <label for="id_marca" class="block text-sm font-medium text-[#333333]">Marca</label>
                    <select id="id_marca" name="id_marca" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#00AEEF] focus:border-[#00AEEF]">
                        <option value="">Selecciona una marca</option>
                        <?php foreach ($marcas ?? [] as $marca) { ?>
                            <option value="<?php echo htmlspecialchars($marca[0]); ?>"
                                <?php echo ((int)$producto_a_editar[6] === (int)$marca[0]) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($marca[1]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="cantidad" class="block text-sm font-medium text-[#333333]">Cantidad (Stock)</label>
                    <input type="number" id="cantidad" name="cantidad" required min="0"
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#00AEEF] focus:border-[#00AEEF]"
                           value="<?php echo htmlspecialchars($producto_a_editar[5]); ?>">
                </div>
                
                <button type="submit" class="btn-action w-full">
                    Actualizar Producto
                </button>
                <a href="/TFG/index.php?action=gestionar_productos" class="block text-center mt-4 text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">
                    Cancelar y Volver
                </a>
            </form>
            <?php else: ?>
                <p class="text-center text-lg text-red-500">No se pudo cargar la información del producto.</p>
                <a href="/TFG/index.php?action=gestionar_productos" class="block text-center mt-4 text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">
                    Volver a Gestión de Productos
                </a>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Panel de Administración. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
