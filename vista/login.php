<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Iniciar Sesión'); ?></title>
    <!-- Incluir Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins de Google Fonts (incluye Regular, Medium, SemiBold, Bold) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .text-error { color: #DC3545; }
        .btn-action {
            background-color: #FF4747; color: white; font-weight: 600; border-radius: 0.5rem; padding: 0.75rem 1.5rem; transition: background-color 0.3s ease;
        }
        .btn-action:hover { background-color: #DC3545; }
    </style>
</head>
<body class="bg-[#D6F0FF] min-h-screen flex flex-col text-[#333333] font-normal"> 
    <header class="bg-[#BFEFFF] shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-[#333333] font-poppins">
                Iniciar sesion
            </h1>
            <nav>
                <ul class="flex space-x-6 text-base">
                    <!-- CORRECCIÓN: Enlaces a index.php con la acción -->
                    <li><a href="index.php?action=catalogo" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Catálogo</a></li>
                    <li><a href="index.php?action=login" class="text-[#FF4747] font-medium transition duration-300">Iniciar Sesión</a></li>
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

    <main class="flex-grow flex items-center justify-center p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-6 text-center">Inicia Sesión</h2>

            <?php if (isset($_GET['error'])) { ?>
                <p class="text-error text-center mb-4 font-medium text-base">
                    <?php 
                        if ($_GET['error'] === 'credenciales_invalidas') {
                            echo "Correo o contraseña incorrectos. Por favor, inténtalo de nuevo.";
                        } elseif ($_GET['error'] === 'tipo_usuario_invalido') {
                            echo "Tipo de usuario no reconocido. Contacta al soporte.";
                        } else {
                            echo "Ocurrió un error al iniciar sesión.";
                        }
                    ?>
                </p>
            <?php } ?>
            <?php if (isset($_GET['registro_exitoso']) && $_GET['registro_exitoso'] === 'true') { ?>
                <p class="text-green-600 text-center mb-4 font-medium text-base">
                    ¡Registro exitoso! Por favor, inicia sesión.
                </p>
            <?php } ?>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'necesitas_login_carrito'): ?>
                <p class="text-error text-center mb-4 font-medium text-base">
                    Debes iniciar sesión para añadir productos al carrito.
                </p>
            <?php endif; ?>

            <!-- CORRECCIÓN: Form action a index.php -->
            <form action="index.php" method="post" class="space-y-4">
                <input type="hidden" name="action" value="procesar_login">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-[#333333] mb-1">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base"
                           value="<?php echo htmlspecialchars($_COOKIE['usuario'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-[#333333] mb-1">Contraseña:</label>
                    <input type="password" id="password" name="password" required 
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="recuerdame" name="recuerdame"
                           class="h-4 w-4 text-[#FF4747] border-[#FFD93D] rounded focus:ring-[#FF4747]">
                    <label for="recuerdame" class="ml-2 block text-sm font-medium text-[#333333]">Recordarme</label>
                </div>
                
                <button type="submit" class="btn-action w-full text-base">
                    Iniciar Sesión
                </button>
            </form>

            <p class="mt-6 text-center text-base text-[#333333]">
                ¿No tienes cuenta? 
                <!-- CORRECCIÓN: Enlace a index.php con la acción -->
                <a href="index.php?action=registro" class="text-[#FF4747] font-medium hover:text-red-700 transition duration-300">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Tu Tienda. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // JS para esta vista (ej. validaciones de formulario)
        });
    </script>
</body>
</html>
