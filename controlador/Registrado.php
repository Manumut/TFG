<?php
// controlador/Registrado.php

// Incluir los modelos y utilidades necesarios
// Las rutas deben ser relativas al archivo index.php (el punto de entrada)
// ya que este controlador será accedido directamente desde redirecciones del login.
require_once __DIR__ . '/../modelo/Producto.php';
require_once __DIR__ . '/../modelo/Marca.php';
require_once __DIR__ . '/../modelo/Usuario.php';
require_once __DIR__ . '/../modelo/Carrito.php'; // ¡Necesario para la gestión del carrito!
// require_once '../modelo/Pedido.php';   // Se necesitará para la gestión de pedidos
require_once 'modelo/cookies_sessiones.php'; // Para manejar sesiones y cookies

// --- Comprobación de Autenticación (¡CRÍTICO!) ---
// Asegurarse de que la sesión esté iniciada y que el usuario esté logueado.
start_session(); // Inicia la sesión si no está iniciada

// Si el ID de usuario NO está en sesión, redirigir al login
if (!is_session('id_us')) {
    header("Location: ../index.php?action=login&error=acceso_restringido");
    exit(); // Detener la ejecución del script
}

// Obtener datos del usuario de la sesión para uso en el controlador y las vistas
$id_usuario_logueado = get_session('id_us');
$nombre_usuario_logueado = get_session('nom_us');
$tipo_usuario_logueado = get_session('tipo_us');

// Instancias globales de modelos para acceder a ellos dentro de las funciones
$producto_model = new Producto();
$marca_model = new Marca();
$usuario_model = new Usuario();
$carrito_model = new Carrito(); // Instancia del modelo Carrito
// $pedido_model = new Pedido(); // Instancia del modelo Pedido (cuando esté creado)


// --- Funciones del Controlador ---

/**
 * Muestra el catálogo principal de productos para usuarios registrados.
 * Puede tener opciones adicionales o un layout ligeramente diferente.
 */
function mostrarCatalogoRegistrado() {
    global $producto_model, $marca_model, $nombre_usuario_logueado; 

    $productos = $producto_model->getAllProductos();
    $marcas = $marca_model->getAllMarcas();
    $titulo_pagina = "Bienvenido, " . htmlspecialchars($nombre_usuario_logueado) . " - Catálogo";

    // Cargar la vista del catálogo (podría ser una versión ligeramente modificada para registrados)
    require_once 'vista/catalogo.php'; // Reutilizamos la misma vista por ahora
}

/**
 * Filtra los productos por una marca específica y muestra los resultados para usuarios registrados.
 */
function filtrarProductosRegistrado() {
    global $producto_model, $marca_model, $nombre_usuario_logueado;

    if (isset($_GET['id_marca']) && is_numeric($_GET['id_marca'])) {
        $id_marca = (int)$_GET['id_marca'];
        $productos = $producto_model->getProductosByMarca($id_marca);
        
        $marca_info = $marca_model->getMarcaById($id_marca);
        if ($marca_info) {
            $titulo_pagina = "Productos de " . htmlspecialchars($marca_info[1]) . " - Registrado";
        } else {
            $titulo_pagina = "Productos por Marca Desconocida - Registrado";
        }
    } else {
        header("Location: /TFG/index.php?action=catalogo"); 
        exit();
    }

    $marcas = $marca_model->getAllMarcas(); 
    require_once 'vista/catalogo.php'; 
}

/**
 * Busca productos por un término dado y muestra los resultados para usuarios registrados.
 */
function buscarProductosRegistrado() {
    global $producto_model, $marca_model, $nombre_usuario_logueado;

    if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
        $termino_busqueda = trim($_GET['busqueda']);
        $productos = $producto_model->buscarProductos($termino_busqueda);
        $titulo_pagina = "Resultados para: '" . htmlspecialchars($termino_busqueda) . "'";
    } else {
        header("Location: /TFG/index.php?action=catalogo");
        exit();
    }

    $marcas = $marca_model->getAllMarcas(); 
    require_once 'vista/catalogo.php'; 
}

/**
 * Muestra los detalles de un producto específico para usuarios registrados.
 */
function verDetalleProductoRegistrado() {
    global $producto_model, $marca_model, $nombre_usuario_logueado;

    if (isset($_GET['id_producto']) && is_numeric($_GET['id_producto'])) {
        $id_producto = (int)$_GET['id_producto'];
        $producto_detalle = $producto_model->getProductoById($id_producto);

        if ($producto_detalle) {
            $titulo_pagina = htmlspecialchars($producto_detalle[1]) . " - Detalles"; 
            require_once 'vista/producto_detalle.php'; // Reutilizamos la misma vista
        } else {
            header("Location: /TFG/index.php?action=catalogo&error=producto_no_encontrado");
            exit();
        }
    } else {
        header("Location: /TFG/index.php?action=catalogo");
        exit();
    }
}


/**
 * Añade un producto al carrito para el usuario logueado.
 * Esta función es llamada desde noRegistrado.php cuando el usuario YA está logueado.
 */
function anadirACarritoRegistrado() {
    global $carrito_model, $id_usuario_logueado;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_producto = $_POST['id_producto'] ?? null;
        $cantidad = $_POST['cantidad'] ?? 1;

        // Validación robusta
        if (!$id_producto || !is_numeric($id_producto)) {
            header("Location: /TFG/index.php?action=catalogo&error=producto_invalido");
            exit();
        }

        // Llama al modelo de Carrito para añadir el producto
        $carrito_model->añadirProducto($id_usuario_logueado, $id_producto, $cantidad);

        header("Location: /TFG/index.php?action=ver_carrito&success=producto_anadido");
        exit();
    } else {
        header("Location: /TFG/index.php?action=catalogo");
        exit();
    }
}

/**
 * Muestra el contenido del carrito de compras del usuario logueado.
 */
function verCarrito() {
    global $carrito_model, $id_usuario_logueado, $nombre_usuario_logueado;

    // CORRECCIÓN: Usar getContenidoCarrito
    $items_carrito = $carrito_model->getContenidoCarrito($id_usuario_logueado);
    $titulo_pagina = "Tu Carrito de Compras, " . htmlspecialchars($nombre_usuario_logueado);

    // Cargar la vista del carrito.
    // **IMPORTANTE:** Necesitarás crear este archivo: vista/carrito.php
    require_once 'vista/carro.php'; 
}

/**
 * Actualiza la cantidad de un producto en el carrito.
 */
function actualizarCantidadCarrito() {
    global $carrito_model, $id_usuario_logueado;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_producto = $_POST['id_producto'] ?? null;
        $nueva_cantidad = $_POST['nueva_cantidad'] ?? null;

        if ($id_producto && is_numeric($id_producto) && is_numeric($nueva_cantidad) && $nueva_cantidad >= 0) {
            if ($nueva_cantidad == 0) {
                $carrito_model->eliminarProducto($id_usuario_logueado, (int)$id_producto);
            } else {
                $carrito_model->actualizarCantidadProducto($id_usuario_logueado, (int)$id_producto, (int)$nueva_cantidad);
            }
            header("Location: /TFG/index.php?action=ver_carrito&mensaje=cantidad_actualizada");
            exit();
        }
    }
    header("Location: /TFG/index.php?action=ver_carrito&error=error_actualizar_cantidad");
    exit();
}

/**
 * Elimina un producto específico del carrito.
 */
function eliminarProductoCarrito() {
    global $carrito_model, $id_usuario_logueado;

    if (isset($_GET['id_producto']) && is_numeric($_GET['id_producto'])) {
        $id_producto = (int)$_GET['id_producto'];
        $carrito_model->eliminarProducto($id_usuario_logueado, $id_producto);
        header("Location: /TFG/index.php?action=ver_carrito&mensaje=producto_eliminado");
        exit();
    }
    header("Location: /TFG/index.php?action=ver_carrito&error=error_eliminar_producto");
    exit();
}

/**
 * Procesa el pedido, vacía el carrito y lo guarda como un nuevo pedido.
 */
function procesarPedido() {
    global $carrito_model, $pedido_model, $id_usuario_logueado;

    // Obtener los ítems del carrito
    $items_carrito = $carrito_model->getContenidoCarrito($id_usuario_logueado);

    if (!empty($items_carrito)) {
        // Crear el pedido usando el modelo Pedido
        $id_nuevo_pedido = $pedido_model->crearPedido($id_usuario_logueado);

        if ($id_nuevo_pedido) {
            header("Location: /TFG/index.php?action=confirmacion_pedido&id_pedido=$id_nuevo_pedido");
            exit();
        } else {
            header("Location: /TFG/index.php?action=ver_carrito&error=error_procesar_pedido");
            exit();
        }
    } else {
        header("Location: /TFG/index.php?action=ver_carrito&error=carrito_vacio");
        exit();
    }
}

/**
 * Muestra una página de confirmación de pedido.
 */
function confirmacionPedido() {
    global $nombre_usuario_logueado;
    $titulo_pagina = "Confirmación de Pedido";
    require_once '../vista/confirmacion_pedido.php'; // Necesitas crear esta vista
}


/**
 * Muestra el historial de pedidos del usuario logueado.
 */
function verHistorialPedidos() {
    global $pedido_model, $id_usuario_logueado, $nombre_usuario_logueado;

    // Asegúrate de que tu modelo Pedido tenga una función getPedidosByUsuario($id_usuario)
    // $historial_pedidos = $pedido_model->getPedidosByUsuario($id_usuario_logueado);
    $historial_pedidos = []; // Placeholder por ahora
    $titulo_pagina = "Tu Historial de Pedidos, " . htmlspecialchars($nombre_usuario_logueado);

    require_once '../vista/historial_pedidos.php'; // Necesitas crear esta vista
}

/**
 * Muestra los detalles de un pedido específico del historial.
 */
function verDetallePedido() {
    global $pedido_model, $id_usuario_logueado;

    if (isset($_GET['id_pedido']) && is_numeric($_GET['id_pedido'])) {
        $id_pedido = (int)$_GET['id_pedido'];
        // Asegúrate de que tu modelo Pedido tenga una función getDetallePedido($id_pedido, $id_usuario)
        // para verificar que el pedido pertenece a este usuario.
        // $detalle_pedido = $pedido_model->getDetallePedido($id_pedido, $id_usuario_logueado);
        $detalle_pedido = null; // Placeholder por ahora

        if ($detalle_pedido) {
            $titulo_pagina = "Detalle del Pedido #" . htmlspecialchars($id_pedido);
            require_once '../vista/detalle_pedido.php'; // Necesitas crear esta vista
        } else {
            header("Location: ../controlador/Registrado.php?action=historial_pedidos&error=pedido_no_encontrado");
            exit();
        }
    } else {
        header("Location: ../controlador/Registrado.php?action=historial_pedidos");
        exit();
    }
}

/**
 * Muestra el perfil del usuario logueado.
 */


/**
 * Procesa la actualización del perfil del usuario logueado.
 */



/**
 * Cierra la sesión del usuario.
 */
function cerrarSesion() {
    unset_session(); // Destruye toda la sesión
    unset_cookie("usuario"); // Borra la cookie si existe
    session_destroy();
    
    header("Location: /TFG/index.php?action=login&mensaje=sesion_cerrada");
    exit();
}


// --- Dispatcher (Router Simple) ---
// Este es el punto de entrada que decide qué función ejecutar
// basándose en el parámetro 'action' de la URL o el formulario.

$action = $_GET['action'] ?? $_POST['action'] ?? 'catalogo'; // 'catalogo' es la acción por defecto para registrados

switch ($action) {
    case 'catalogo':
        mostrarCatalogoRegistrado();
        break;
    case 'filtrar_por_marca':
        filtrarProductosRegistrado();
        break;
    case 'buscar_productos':
        buscarProductosRegistrado();
        break;
    case 'ver_detalle_producto':
        verDetalleProductoRegistrado();
        break;
    case 'anadir_a_carrito':
        anadirACarritoRegistrado();
        break;
    case 'ver_carrito':
        verCarrito();
        break;
    case 'actualizar_cantidad_carrito':
        actualizarCantidadCarrito();
        break;

    case 'eliminar_producto_carrito':
        eliminarProductoCarrito();
        break;
    case 'procesar_pedido':
        procesarPedido();
        break;
    case 'confirmacion_pedido':
        confirmacionPedido();
        break;
    case 'historial_pedidos':
        verHistorialPedidos();
        break;
    case 'ver_detalle_pedido':
        verDetallePedido();
        break;
    case 'cerrar_sesion':
        cerrarSesion();
        break;
    default:
        // Si la acción no es reconocida, redirigir al catálogo para registrados
        header("Location: /TFG/index.php?action=catalogo");
        exit();
}
