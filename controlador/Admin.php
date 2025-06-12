<?php
// controlador/Admin.php

// Incluir los modelos y utilidades necesarios
require_once __DIR__ . '/../modelo/Usuario.php';  // Para gestionar usuarios y obtener datos del admin
require_once __DIR__ . '/../modelo/Producto.php'; // Para gestionar productos
require_once __DIR__ . '/../modelo/Marca.php';    // Para gestionar marcas
require_once __DIR__ . '/../modelo/Pedido.php';   // Para gestionar pedidos
require_once __DIR__ . '/../modelo/cookies_sessiones.php'; // Para manejar sesiones y cookies

// --- Comprobación de Autenticación y Rol (¡CRÍTICO!) ---
// Asegurarse de que la sesión esté iniciada y que el usuario sea un administrador.
start_session(); // Inicia la sesión si no está iniciada

// Si el ID de usuario NO está en sesión o el tipo de usuario NO es 'administrador', redirigir al login
if (!is_session('id_us') || get_session('tipo_us') !== 'administrador') { // CAMBIO CLAVE: 'admin' a 'administrador'
    // Si no está logueado o no es administrador, redirigir al login con un mensaje de acceso denegado
    header("Location: ../index.php?action=login&error=acceso_denegado_admin");
    exit(); // Detener la ejecución del script
}

// Obtener datos del usuario administrador de la sesión
$id_admin_logueado = get_session('id_us');
$nombre_admin_logueado = get_session('nom_us');
$tipo_admin_logueado = get_session('tipo_us'); // Debería ser 'administrador'

// Instancias globales de modelos para acceder a ellos dentro de las funciones
$usuario_model = new Usuario();
$producto_model = new Producto();
$marca_model = new Marca();
$pedido_model = new Pedido();


// --- Funciones del Controlador ---

/**
 * Muestra el dashboard principal para el administrador.
 */
function mostrarDashboardAdmin() {
    global $nombre_admin_logueado, $titulo_pagina;

    $titulo_pagina = "Panel de Administración - " . htmlspecialchars($nombre_admin_logueado);

    // Cargar la vista del dashboard de administración.
    require_once __DIR__ . '/../vista/admin_dashboard.php';
}

/**
 * Cierra la sesión del administrador.
 */
function cerrarSesionAdmin() {
    unset_session(); // Destruye toda la sesión
    unset_cookie("usuario");
    session_destroy();
    
    header("Location: ../index.php?action=login&mensaje=sesion_cerrada_admin");
    exit();
}


// --- Funciones de gestión (se desarrollarán más adelante) ---

/**
 * Muestra la lista de usuarios para la gestión del administrador.
 */
function gestionarUsuarios() {
    global $titulo_pagina, $usuario_model;
    $titulo_pagina = "Gestión de Usuarios";
    $usuarios = $usuario_model->getAllUsuarios(); // Obtener todos los usuarios no admin/administrador
    require_once __DIR__ . '/../vista/admin_usuarios.php';
}

/**
 * Muestra la lista de productos para la gestión del administrador.
 */
function gestionarProductos() {
    global $titulo_pagina, $producto_model;
    $titulo_pagina = "Gestión de Productos";
    // Si hay búsqueda, filtra productos
    if (isset($_GET['busqueda']) && trim($_GET['busqueda']) !== '') {
        $busqueda = trim($_GET['busqueda']);
        // Debes tener este método en tu modelo Producto
        $productos = $producto_model->buscarProductos($busqueda);
        $titulo_pagina = "Gestión de Productos - Resultados para: '" . htmlspecialchars($busqueda) . "'";
    } else {
        $productos = $producto_model->getAllProductos();
        $titulo_pagina = "Gestión de Productos";
    }
    require_once __DIR__ . '/../vista/admin_productos.php';
}

/**
 * Muestra la lista de marcas para la gestión del administrador.
 */
function gestionarMarcas() {
    global $titulo_pagina, $marca_model;
    $titulo_pagina = "Gestión de Marcas";
    $marcas = $marca_model->getAllMarcas();
    require_once __DIR__ . '/../vista/admin_marcas.php'; // Necesitas crear esta vista si no existe
}

/**
 * Muestra la lista de pedidos para la gestión del administrador.
 */
function gestionarPedidos() {
    global $titulo_pagina, $pedido_model;
    $titulo_pagina = "Gestión de Pedidos";
    $pedidos = $pedido_model->getAllPedidos();
    require_once __DIR__ . '/../vista/admin_pedidos.php'; // Necesitas crear esta vista si no existe
}


// --- Dispatcher (Router Simple) ---
$action = $_GET['action'] ?? $_POST['action'] ?? 'administracion'; // 'administracion' es la acción por defecto para el admin

switch ($action) {
    case 'administracion':
    case 'admin_dashboard': 
        mostrarDashboardAdmin();
        break;
    case 'gestionar_usuarios':
        // Mostrar la lista de usuarios o la búsqueda
        if (isset($_GET['busqueda'])) {
            // Buscar usuarios según el término
            $usuarios = $usuario_model->buscarUsuarios($_GET['busqueda']);
        } else {
            $usuarios = $usuario_model->getAllUsuarios();
        }
        $titulo_pagina = "Gestión de Usuarios";
        require_once __DIR__ . '/../vista/admin_usuarios.php';
        break;
    case 'eliminar_usuario':
        if (isset($_GET['id'])) {
            $usuario_model->eliminarUsuario((int)$_GET['id']);
        }
        header("Location: /TFG/index.php?action=gestionar_usuarios");
        exit();
    case 'editar_usuario':
        // Aquí tu lógica para editar usuario
        // ...
        require_once __DIR__ . '/../vista/admin_editar_usuario.php';
        break;
    case 'anadir_usuario':
        // Aquí tu lógica para añadir usuario
        // ...
        require_once __DIR__ . '/../vista/admin_anadir_usuario.php';
        break;
    case 'gestionar_productos':
        gestionarProductos();
        break;
    case 'gestionar_marcas':
        gestionarMarcas();
        break;
    case 'gestionar_pedidos':
        gestionarPedidos();
        break;
    case 'cerrar_sesion_admin':
        cerrarSesionAdmin();
        break;
    case 'editar_producto':
        if (isset($_GET['id_producto'])) {
            $id_producto = (int)$_GET['id_producto'];
            $producto_a_editar = $producto_model->getProductoById($id_producto);
            $marcas = $marca_model->getAllMarcas();
            $titulo_pagina = "Editar Producto";
            require_once __DIR__ . '/../vista/admin_editar_producto.php';
        } else {
            header("Location: /TFG/index.php?action=gestionar_productos&error=producto_no_encontrado");
            exit();
        }
        break;
    case 'procesar_editar_producto':
        if (
            isset($_POST['id_producto'], $_POST['titulo'], $_POST['precio'], $_POST['descripcion'], $_POST['id_marca'], $_POST['cantidad'])
            && $_POST['titulo'] !== "" && $_POST['precio'] !== "" && $_POST['descripcion'] !== "" && $_POST['id_marca'] !== "" && $_POST['cantidad'] !== ""
        ) {
            $id_producto = (int)$_POST['id_producto'];
            $titulo = $_POST['titulo'];
            $precio = (float)$_POST['precio'];
            $descripcion = $_POST['descripcion'];
            $id_marca = (int)$_POST['id_marca'];
            $cantidad = (int)$_POST['cantidad'];
            $imagen_actual = $_POST['imagen_actual'] ?? "";

            // Procesar imagen nueva si se sube
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                $nombre_img = basename($_FILES['imagen_producto']['name']);
                $ruta_destino = __DIR__ . '/../imagenes/' . $nombre_img;
                if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $ruta_destino)) {
                    $imagen_producto = $nombre_img;
                } else {
                    header("Location: /TFG/index.php?action=editar_producto&id_producto=$id_producto&error=error_subir_nueva_imagen");
                    exit();
                }
            } else {
                $imagen_producto = $imagen_actual;
            }

            $ok = $producto_model->actualizarProducto($id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad);
            if ($ok) {
                header("Location: /TFG/index.php?action=gestionar_productos&mensaje=producto_actualizado");
                exit();
            } else {
                header("Location: /TFG/index.php?action=editar_producto&id_producto=$id_producto&error=error_actualizar_producto");
                exit();
            }
        } else {
            header("Location: /TFG/index.php?action=editar_producto&id_producto=" . ($_POST['id_producto'] ?? '') . "&error=campos_vacios");
            exit();
        }
        break;
    case 'anadir_producto':
        // Mostrar formulario para añadir producto
        $marcas = $marca_model->getAllMarcas();
        $titulo_pagina = "Añadir Producto";
        require_once __DIR__ . '/../vista/admin_anadir_producto.php';
        break;

    case 'procesar_crear_producto':
        if (
            isset($_POST['titulo'], $_POST['precio'], $_POST['descripcion'], $_POST['id_marca'], $_POST['cantidad'])
            && $_POST['titulo'] !== "" && $_POST['precio'] !== "" && $_POST['descripcion'] !== "" && $_POST['id_marca'] !== "" && $_POST['cantidad'] !== ""
        ) {
            $titulo = $_POST['titulo'];
            $precio = (float)$_POST['precio'];
            $descripcion = $_POST['descripcion'];
            $id_marca = (int)$_POST['id_marca'];
            $cantidad = (int)$_POST['cantidad'];

            // Procesar imagen si se sube
            $imagen_producto = "imagenes/default.png"; // Imagen por defecto
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                $nombre_img = basename($_FILES['imagen_producto']['name']);
                $ruta_destino = __DIR__ . '/../imagenes/' . $nombre_img;
                if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $ruta_destino)) {
                    $imagen_producto = "imagenes/" . $nombre_img;
                } else {
                    header("Location: /TFG/index.php?action=anadir_producto&error=error_subir_imagen");
                    exit();
                }
            }

            $ok = $producto_model->insertarProducto($titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad);
            if ($ok) {
                header("Location: /TFG/index.php?action=gestionar_productos&mensaje=producto_creado");
                exit();
            } else {
                header("Location: /TFG/index.php?action=anadir_producto&error=error_crear_producto");
                exit();
            }
        } else {
            header("Location: /TFG/index.php?action=anadir_producto&error=campos_vacios");
            exit();
        }
        break;
    default:
        // Si la acción no es reconocida para el admin, redirigir al dashboard del admin
        header("Location: ../index.php?action=administracion");
        exit();
}

?>
