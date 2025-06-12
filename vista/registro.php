<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina ?? 'Registrarse'); ?></title>
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
                <?php echo htmlspecialchars($titulo_pagina ?? 'All in Toys'); ?>
            </h1>
            <nav>
                <ul class="flex space-x-6 text-base">
                    <!-- CORRECCIÓN: Enlaces a index.php con la acción -->
                    <li><a href="index.php?action=catalogo" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Catálogo</a></li>
                    <li><a href="index.php?action=login" class="text-[#333333] hover:text-[#FF4747] font-medium transition duration-300">Iniciar Sesión</a></li>
                    <li><a href="index.php?action=registro" class="text-[#FF4747] font-medium transition duration-300">Registrarse</a></li>
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
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-6 text-center">Registra una nueva cuenta</h2>

            <?php if (isset($_GET['error'])) { ?>
                <p class="text-error text-center mb-4 font-medium text-base">
                    <?php 
                        if ($_GET['error'] === 'campos_vacios') {
                            echo "Por favor, rellena todos los campos.";
                        } elseif ($_GET['error'] === 'passwords_no_coinciden') {
                            echo "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
                        } elseif ($_GET['error'] === 'registro_fallido') {
                            echo "No se pudo registrar la cuenta. El correo electrónico ya podría estar en uso.";
                        } else {
                            echo "Ocurrió un error al registrar la cuenta.";
                        }
                    ?>
                </p>
            <?php } ?>

            <!-- CORRECCIÓN: Form action a index.php -->
            <form action="index.php" method="post" class="space-y-4">
                <input type="hidden" name="action" value="procesar_registro">
                
                <div>
                    <label for="nombre" class="block text-sm font-medium text-[#333333] mb-1">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required autocomplete="given-name"
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                </div>

                <div>
                    <label for="apellidos" class="block text-sm font-medium text-[#333333] mb-1">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required autocomplete="family-name"
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-[#333333] mb-1">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required autocomplete="email"
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-[#333333] mb-1">Contraseña:</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-[#333333] mb-1">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password"
                           class="mt-1 block w-full p-3 border border-[#FFD93D] rounded-md shadow-sm focus:ring-[#FF4747] focus:border-[#FF4747] text-base">
                </div>

                <!-- Añade este bloque justo después del campo de confirmar contraseña -->
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="togglePassword" class="mr-2">
                    <label for="togglePassword" class="text-sm text-[#333333] cursor-pointer">Mostrar contraseñas</label>
                </div>
                
                <button type="submit" class="btn-action w-full text-base">
                    Registrarse
                </button>
            </form>

            <p class="mt-6 text-center text-base text-[#333333]">
                ¿Ya tienes una cuenta? 
                <!-- CORRECCIÓN: Enlace a index.php con la acción -->
                <a href="index.php?action=login" class="text-[#FF4747] font-medium hover:text-red-700 transition duration-300">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Tu Tienda. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const togglePassword = document.getElementById('togglePassword');

            form.addEventListener('submit', function(event) {
                if (passwordField.value !== confirmPasswordField.value) {
                    event.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, verifica.');
                }
            });

            // Mostrar/ocultar contraseñas
            togglePassword.addEventListener('change', function() {
                const type = this.checked ? 'text' : 'password';
                passwordField.type = type;
                confirmPasswordField.type = type;
            });
        });
    </script>
</body>
</html>
