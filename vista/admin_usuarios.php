<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Gestión de Usuarios'); ?></title>
    <!-- Incluir Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins de Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Estilos adicionales para la tabla si es necesario */
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
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-6">Gestión de Usuarios</h2>

            <!-- Sección de Búsqueda y Añadir Usuario -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0 md:space-x-4">
                <!-- Formulario de Búsqueda de Usuarios -->
                <form action="/TFG/index.php" method="get" class="flex-grow flex items-center space-x-2 w-full md:w-auto">
                    <input type="hidden" name="action" value="gestionar_usuarios">
                    <label for="search-user" class="sr-only">Buscar Usuario:</label>
                    <input type="text" id="search-user" name="busqueda" placeholder="Buscar por nombre, correo..."
                           class="flex-grow p-2 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                    <button type="submit" class="px-4 py-2 bg-[#FF4747] text-white font-semibold rounded-md hover:bg-red-600 transition duration-300 text-base">
                        Buscar
                    </button>
                </form>
                
                <!-- Botón para Añadir Nuevo Usuario -->
                
            </div>

            <!-- Tabla de Listado de Usuarios -->
            <?php
            
            $usuarios_a_mostrar = $usuarios ;

            if (empty($usuarios_a_mostrar)) {
                echo "<p class='text-center text-[#333333] text-lg'>No se encontraron usuarios.</p>";
            } else {
            ?>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead>
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Nombre</th>
                                <th class="py-3 px-4">Apellidos</th>
                                <th class="py-3 px-4">Correo</th>
                                <th class="py-3 px-4">Tipo</th>
                                <th class="py-3 px-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios_a_mostrar as $usuario) { ?>
                                <tr>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($usuario[0]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($usuario[1]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($usuario[2]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($usuario[3]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($usuario[4]); ?></td>
                                    <td class="py-3 px-4 whitespace-nowrap">              
                                        <a href="/TFG/index.php?action=eliminar_usuario&id=<?php echo htmlspecialchars($usuario[0]); ?>" 
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar a este usuario?');"
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
