<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
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
                    <li>
                      <a href="/TFG/index.php?action=cerrar_sesion_admin" class="text-[#FF4747] hover:text-[#333333] font-medium transition duration-300">
                        Cerrar Sesión
                      </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-[#333333] font-poppins mb-4">
                Bienvenido, <?php echo htmlspecialchars($nombre_admin_logueado ?? 'Administrador'); ?>!
            </h2>
            <p class="text-base text-[#333333]">Este es tu panel de administración. Utiliza el menú superior para navegar por las diferentes secciones de gestión.</p>
            <!-- Aquí puedes añadir widgets o resúmenes si los tienes -->
        </div>
    </main>

    <footer class="bg-[#00AEEF] text-white p-6 text-center mt-8">
        <p class="text-base font-normal font-poppins">&copy; 2025 Panel de Administración.</p>
    </footer>
</body>
</html>
