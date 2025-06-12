<?php
// modelo/Pedido.php

// Incluye la clase de conexión a la base de datos
require_once __DIR__ . '/class_bd.php';
// Necesitamos el modelo Carrito para obtener el contenido del carrito y vaciarlo.
require_once ("carrito.php"); 

class Pedido {
    private $conn; // Conexión a la base de datos
    // Propiedades de la clase para almacenar los datos de un pedido (según tu estilo)
    private $id_pedido;
    private $id_usu;
    private $total_pedido;
    private $fecha_pedido;
    private $estado_pedido;

    public function __construct(){
        $this->conn = new bd();
        // Inicialización de propiedades
        $this->id_pedido = "";
        $this->id_usu = "";
        $this->total_pedido = "";
        $this->fecha_pedido = "";
        $this->estado_pedido = "";
    }

    /**
     * Crea un nuevo pedido a partir del contenido del carrito de un usuario.
     * Este método realiza los siguientes pasos:
     * 1. Obtiene los productos del carrito del usuario.
     * 2. Calcula el total del pedido.
     * 3. Inserta el pedido principal en la tabla 'pedidos'.
     * 4. Obtiene el ID del pedido recién creado.
     * 5. Inserta cada producto del carrito como un detalle en la tabla 'detalle_pedido'.
     * 6. Vacía el carrito del usuario.
     *
     * @param int $id_usu ID del usuario que realiza el pedido.
     * @return int|false El ID del nuevo pedido si se creó con éxito, o false en caso contrario.
     */
    public function crearPedido($id_usu) {
        // 1. Obtener el contenido del carrito del usuario
        $carro = new Carrito();
        $filas_carrito = $carro->getContenidoCarrito($id_usu);

        if (empty($filas_carrito)) {
            error_log("Intento de crear pedido para el usuario " . $id_usu . " con un carrito vacío.");
            return false; // No se puede crear un pedido de un carrito vacío
        }

        $total_pedido = 0;
        foreach ($filas_carrito as $item) {
            // La estructura de $filas_carrito es:
            // [0 => id_producto, 1 => titulo, 2 => imagen_producto, 3 => cantidad, 4 => precio_actual, 5 => subtotal]
            $cantidad_item = $item[3]; // La cantidad de este producto en el carrito
            $precio_item = $item[4]; // El precio actual del producto
            $total_pedido += ($precio_item * $cantidad_item);
        }

        // 2. Insertar el pedido principal en la tabla 'pedidos'
        // NOW() se usa para obtener la fecha y hora actual de la base de datos.
        $sentencia_pedido = "INSERT INTO pedidos (id_usu, total_pedido,fecha_pedido,  estado_pedido) VALUES (?, ?, NOW(), ?)";
        $consul = $this->conn->getConection();
        $consulta_pedido = $consul->prepare($sentencia_pedido);


        $estado_inicial = 'pendiente'; // Definimos un estado inicial para el pedido
        // 'ids' -> i: integer (id_usu), d: double (total_pedido), s: string (estado_inicial)
        $consulta_pedido->bind_param("ids", $id_usu, $total_pedido, $estado_inicial);
        $result_pedido = $consulta_pedido->execute();

        // IMPORTANTE: Manejo de errores en la ejecución de la consulta
        if ($result_pedido === false) {
            error_log("Error al ejecutar la consulta de inserción de pedido: " . $consulta_pedido->error);
            $consulta_pedido->close();
            return false;
        }

        $id_nuevo_pedido = $consulta_pedido->insert_id; // Guardar el ID del pedido recién insertado
        $consulta_pedido->close();


        // 3. Insertar los detalles del pedido en la tabla 'detalle_pedido'
        
        $sentencia_detalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
        $consulta_detalle = $consul->prepare($sentencia_detalle);


        foreach ($filas_carrito as $item) {
            //Este foreach recorre la funcion de getContenidoCarrito para obtener sus datos
            $id_producto_item = $item[0]; // ID del producto
            $cantidad_item = $item[3];   // Cantidad del producto en el carrito
            $precio_actual_item = $item[4]; // Precio del producto en el momento de la compra (para calcular subtotal)
            // **CORRECCIÓN:** Calculamos el subtotal para la línea del detalle de pedido.
            $subtotal_item = $cantidad_item * $precio_actual_item; 

            // 'iiid' -> i: id_pedido, i: id_producto, i: cantidad, d: subtotal
            $consulta_detalle->bind_param("iiid", $id_nuevo_pedido, $id_producto_item, $cantidad_item, $subtotal_item);
            $result_detalle = $consulta_detalle->execute();

            // IMPORTANTE: Manejo de errores en la ejecución de cada detalle
            if ($result_detalle === false) {
                error_log("Error al insertar detalle de pedido para producto " . $id_producto_item . " en pedido " . $id_nuevo_pedido . ": " . $consulta_detalle->error);
                // Aquí también, en un sistema real, se consideraría un rollback de toda la transacción.
                $consulta_detalle->close();
                return false;
            }
        }
        $consulta_detalle->close();

        // 4. Vaciar el carrito del usuario después de crear el pedido
        $carro_vaciado = $carro->vaciarCarrito($id_usu);
        if ($carro_vaciado === false) {
            error_log("Advertencia: No se pudo vaciar el carrito del usuario " . $id_usu . " después de crear el pedido " . $id_nuevo_pedido);
            // Esto es una advertencia, no un error que impida devolver el ID del pedido,
            // pero es un problema que debe ser investigado.
        }

        return $id_nuevo_pedido; // Devolvemos el ID del pedido creado
    }








    /**
     * Obtiene todos los pedidos de un usuario específico.
     *
     * @param int $id_usu ID del usuario.
     * @return array Un array de arrays numéricos con los pedidos del usuario.
     */
    public function getPedidosByUsuario($id_usu) {
        // Ordenamos por fecha_pedido de forma descendente para ver los más recientes primero.
        $sentencia = "SELECT id_pedido, total_pedido, fecha_pedido,estado_pedido FROM pedidos WHERE id_usu = ? ORDER BY fecha_pedido DESC";
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("i", $id_usu);
        $consulta->execute();

        // Vinculamos las columnas seleccionadas a las propiedades de la clase (o variables locales).
        $consulta->bind_result($id_pedido, $total_pedido, $fecha_pedido, $estado_pedido);

        $pedidos = [];
        while ($consulta->fetch()) {
            // Se mantiene el array numérico interno según tu preferencia.
            $pedidos[$id_pedido] = [$id_pedido, $total_pedido, $fecha_pedido, $estado_pedido];
        }
        $consulta->close();
        return $pedidos;
    }




    /**
     * Obtiene los detalles de un pedido específico (los productos que contiene).
     *
     * @param int $id_pedido ID del pedido.
     * @return array Un array de arrays numéricos con los detalles del pedido.
     */
    public function getDetallePedido($id_pedido) {
        // Unimos con la tabla 'producto' para obtener detalles del producto como título e imagen.
        // Se usa JOIN implícito y p.precio sin alias, según tu estilo.
        // **CORRECCIÓN:** Se selecciona 'dp.subtotal' directamente de detalle_pedido.
        $sentencia = "SELECT dp.id_detalle, dp.id_producto, p.titulo, p.imagen_producto, dp.cantidad, dp.subtotal 
        FROM detalle_pedido dp, producto p 
        WHERE dp.id_producto = p.id_producto AND dp.id_pedido = ?";

        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("i", $id_pedido);
        $consulta->execute();

    
        // Variables locales para guardar lo que venga de la base de datos al hacer el bind_result)
        $id_detalle_local = "";
        $id_producto_local = "";
        $titulo_local = "";
        $imagen_producto_local = "";
        $cantidad_local = "";
        $subtotal_local = "";         
        
        $consulta->bind_result($id_detalle_local, $id_producto_local, $titulo_local, $imagen_producto_local, $cantidad_local, $subtotal_local);

        $detalles = [];
        while ($consulta->fetch()) {
            $detalles[$id_detalle_local] = [
                $id_detalle_local,
                $id_producto_local,
                $titulo_local,
                $imagen_producto_local,
                $cantidad_local,
                $subtotal_local 
            ];
        }
        $consulta->close();
        return $detalles;
    }









    /**
     * Actualiza el estado de un pedido.
     *
     * @param int $id_pedido ID del pedido a actualizar.
     * @param string $nuevo_estado El nuevo estado del pedido (ej. 'procesando', 'enviado', 'entregado').
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarEstadoPedido($id_pedido, $nuevo_estado) {
        $sentencia = "UPDATE pedidos SET estado_pedido = ? WHERE id_pedido = ?";
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("si", $nuevo_estado, $id_pedido);
        $consulta->execute();

        $modificado = false;
        // IMPORTANTE: Comprobación de que funcione en la ejecución.
        if ( $consulta->affected_rows === 1) {
            $modificado = true;
        }
        $consulta->close();
        return $modificado;
    }






    /**
     * Obtiene todos los pedidos (para administración).
     *
     * @return array Un array de arrays numéricos con todos los pedidos.
     */
    public function getAllPedidos() {
        // Unimos con la tabla 'usuarios' para obtener el nombre del usuario que realizó el pedido.
        $sentencia = "SELECT p.id_pedido, p.id_usu, u.nombre,p.total_pedido, p.fecha_pedido,  p.estado_pedido 
                      FROM pedidos p, usuarios u WHERE p.id_usu = u.id_usu ORDER BY p.fecha_pedido DESC";
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->execute();

        // Vinculamos las columnas seleccionadas a variables locales.
        $id_pedido_local = "";
        $id_usu_local = "";
        $nombre_usuario_local = ""; // Variable local para el nombre del usuario
        $total_pedido_local = "";
        $fecha_pedido_local = "";
        $estado_pedido_local = "";
        // El orden de las variables DEBE COINCIDIR exactamente con el orden de las columnas en tu SELECT.
        $consulta->bind_result($id_pedido_local, $id_usu_local, $nombre_usuario_local, $total_pedido_local, $fecha_pedido_local,  $estado_pedido_local);

        $pedidos = [];
        while ($consulta->fetch()) {
            $pedidos[$id_pedido_local] = [
                $id_pedido_local,
                $id_usu_local,
                $nombre_usuario_local, // Incluimos el nombre del usuario
                $total_pedido_local,
                $fecha_pedido_local,
                $estado_pedido_local
            ];
        }
        $consulta->close();
        return $pedidos;
    }
}
