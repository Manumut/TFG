<?php
// TFG/index.php

// Router principal: decide qué controlador cargar según la acción
$action = $_GET['action'] ?? $_POST['action'] ?? 'catalogo';

// Acciones de administración
$acciones_admin = [
    'administracion',
    'admin_dashboard',
    'gestionar_usuarios',
    'gestionar_productos',
    'anadir_producto',
    'editar_producto',
    'eliminar_producto',
    'procesar_crear_producto',
    'procesar_editar_producto',
    'gestionar_marcas',
    'gestionar_pedidos',
    'cerrar_sesion_admin'
];

// Acciones de usuarios registrados
$acciones_registrado = [
    'catalogo_registrado',
    'filtrar_por_marca',
    'buscar_productos',
    'ver_detalle_producto',
    'anadir_a_carrito',
    'ver_carrito',
    'actualizar_cantidad_carrito', // <-- Añade esta línea
    'eliminar_producto_carrito',   // <-- Y esta línea
];

// Si la acción es de admin, carga el controlador de admin
if (in_array($action, $acciones_admin)) {
    require_once __DIR__ . '/controlador/Admin.php';
} elseif (in_array($action, $acciones_registrado)) {
    require_once __DIR__ . '/controlador/Registrado.php';
} else {
    // Por defecto, controlador de usuarios no registrados/registrados
    require_once __DIR__ . '/controlador/noRegistrado.php';
}



?>
