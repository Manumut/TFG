<?php
// controlador/noRegistrado.php

// Incluir los modelos y utilidades necesarios
require_once __DIR__ . '/../modelo/Producto.php';
require_once __DIR__ . '/../modelo/Marca.php';
require_once __DIR__ . '/../modelo/Usuario.php'; 
require_once __DIR__ . '/../modelo/cookies_sessiones.php'; // Para manejar sesiones y cookies

// Instancias globales de modelos para acceder a ellos dentro de las funciones
$producto_model = new Producto();
$marca_model = new Marca();
$usuario_model = new Usuario(); 


// --- Funciones del Controlador ---

/**
 * Muestra el catálogo principal de productos (todos los productos).
 * Esta sería la acción por defecto si no se especifica ninguna otra.
 */
function mostrarCatalogo() {
    global $producto_model, $marca_model; 

    $productos = $producto_model->getAllProductos();
    $marcas = $marca_model->getAllMarcas();
    $titulo_pagina = "Nuestro Catálogo de Juguetes";

    require_once __DIR__ . '/../vista/catalogo.php';
}

/**
 * Filtra los productos por una marca específica y muestra los resultados.
 */
function filtrarProductos() {
    global $producto_model, $marca_model;

    if (isset($_GET['id_marca']) && is_numeric($_GET['id_marca'])) {
        $id_marca = (int)$_GET['id_marca'];
        $productos = $producto_model->getProductosByMarca($id_marca);
        
        $marca_info = $marca_model->getMarcaById($id_marca);
        if ($marca_info) {
            $titulo_pagina = "Productos de " . htmlspecialchars($marca_info[1]);
        } else {
            $titulo_pagina = "Productos por Marca Desconocida";
        }
    } else {
        header("Location: index.php?action=catalogo"); 
        exit();
    }

    $marcas = $marca_model->getAllMarcas(); 
    require_once __DIR__ . '/../vista/catalogo.php'; 
}

/**
 * Busca productos por un término dado y muestra los resultados.
 */
function buscarProductos() {
    global $producto_model, $marca_model;

    if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
        $termino_busqueda = trim($_GET['busqueda']);
        $productos = $producto_model->buscarProductos($termino_busqueda);
        $titulo_pagina = "Resultados para: '" . htmlspecialchars($termino_busqueda) . "'";
    } else {
        header("Location: index.php?action=catalogo");
        exit();
    }

    $marcas = $marca_model->getAllMarcas(); 
    require_once __DIR__ . '/../vista/catalogo.php'; 
}

/**
 * Muestra los detalles de un producto específico.
 */
function verDetalleProducto() {
    global $producto_model, $marca_model;

    if (isset($_GET['id_producto']) && is_numeric($_GET['id_producto'])) {
        $id_producto = (int)$_GET['id_producto'];
        $producto_detalle = $producto_model->getProductoById($id_producto);

        if ($producto_detalle) {
            $titulo_pagina = htmlspecialchars($producto_detalle[1]) . " - Detalles"; 
            require_once __DIR__ . '/../vista/producto_detalle.php'; 
        } else {
            header("Location: index.php?action=catalogo&error=producto_no_encontrado");
            exit();
        }
    } else {
        header("Location: index.php?action=catalogo");
        exit();
    }
}

/**
 * Muestra el formulario de login.
 */
function mostrarLogin() {
    global $titulo_pagina;
    $titulo_pagina = "Iniciar Sesión";
    require_once __DIR__ . '/../vista/login.php';
}

/**
 * Procesa el formulario de login.
 */
function procesarLogin() {
    global $usuario_model;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $recuerdame = isset($_POST['recuerdame']); 

        if (empty($email) || empty($password)) {
            header("Location: index.php?action=login&error=campos_vacios");
            exit();
        }

        $usuario_login = $usuario_model->verificarLogin($email, $password);

        if ($usuario_login) {
            $id_usu = $usuario_login[0];
            $nom_us = $usuario_login[1];
            $tipo_us = $usuario_login[2]; 

            set_session("id_us", $id_usu, "nom_us", $nom_us, "tipo_us", $tipo_us);

            if ($recuerdame) {
                set_cookie("usuario", $email);
            } else {
                unset_cookie("usuario"); 
            }

            // AHORA SE COMPRUEBA EL TIPO DE USUARIO 'administrador'
            if ($tipo_us === 'administrador') {
                header("Location: index.php?action=administracion");
                exit();
            } elseif ($tipo_us === 'registrado') {
                header("Location: index.php?action=catalogo_registrado");
                exit();
            } else {
                // Este caso debería ser raro si los tipos de usuario están controlados.
                header("Location: index.php?action=login&error=tipo_usuario_invalido");
                exit();
            }
        } else {
            header("Location: index.php?action=login&error=credenciales_invalidas");
            exit();
        }
    } else {
        header("Location: index.php?action=login");
        exit();
    }
}

/**
 * Muestra el formulario de registro.
 */
function mostrarRegistro() {
    global $titulo_pagina;
    $titulo_pagina = "Registrarse";
    require_once __DIR__ . '/../vista/registro.php';
}

/**
 * Procesa el formulario de registro de nuevos usuarios.
 */
function procesarRegistro() {
    global $usuario_model;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($nombre) || empty($apellidos) || empty($email) || empty($password) || empty($confirm_password)) {
            header("Location: index.php?action=registro&error=campos_vacios");
            exit();
        }

        if ($password !== $confirm_password) {
            header("Location: index.php?action=registro&error=passwords_no_coinciden");
            exit();
        }

        // Por defecto, se registra como 'registrado'. No 'admin' desde aquí.
        if ($usuario_model->registrarUsuario($nombre, $apellidos, $email, $password, 'registrado')) {
            header("Location: index.php?action=login&registro_exitoso=true");
            exit();
        } else {
            header("Location: index.php?action=registro&error=registro_fallido");
            exit();
        }
    } else {
        header("Location: index.php?action=registro");
        exit();
    }
}

/**
 * Maneja la acción de añadir un producto al carrito para usuarios NO REGISTRADOS.
 * Redirige al login con un mensaje si el usuario no está logueado.
 */
function anadirACarritoNoRegistrado() {
    // Siempre redirige al login si no está logueado
    header("Location: index.php?action=login&error=necesitas_login_carrito");
    exit();
}


$action = $_GET['action'] ?? $_POST['action'] ?? 'catalogo'; 

switch ($action) {
    case 'login':
        mostrarLogin();
        break;
    case 'catalogo':
        mostrarCatalogo();
        break;
    case 'filtrar_por_marca':
        filtrarProductos();
        break;
    case 'buscar_productos':
        buscarProductos();
        break;
    case 'ver_detalle_producto':
        verDetalleProducto();
        break;
    case 'procesar_login': 
        $correo = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $usuario = $usuario_model->verificarLogin($correo, $password);

        if ($usuario) {
            // $usuario = [id_usu, nombre, tipo_usu]
            set_session('id_us', $usuario[0], 'nom_us', $usuario[1], 'tipo_us', $usuario[2]);
            if ($usuario[2] === 'administrador') {
                header("Location: /TFG/index.php?action=administracion");
            } elseif ($usuario[2] === 'registrado') {
                header("Location: /TFG/index.php?action=catalogo_registrado");
            } else {
                header("Location: /TFG/index.php?action=login&error=tipo_usuario_invalido");
            }
            exit();
        } else {
            header("Location: /TFG/index.php?action=login&error=credenciales_invalidas");
            exit();
        }
        break;
    case 'registro': 
        mostrarRegistro();
        break;
    case 'procesar_registro': 
        procesarRegistro();
        break;
    case 'anadir_a_carrito': // Para usuarios NO REGISTRADOS, siempre redirige al login
        anadirACarritoNoRegistrado();
        break;
    default:
        mostrarCatalogo();
        break;
}

?>
