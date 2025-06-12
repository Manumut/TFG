<?php
// modelo/Carrito.php

// Incluye la clase de conexión a la base de datos
require_once ("class_bd.php");

class Carrito {
    private $conn; // Conexión a la base de datos
    // Propiedades de la clase (se mantienen por consistencia con tu estilo)
    private $id_carrito;
    private $id_usu;
    private $id_producto;
    private $cantidad;
    
    // private $precio_unitario; 

    public function __construct(){
        $this->conn = new bd();
        // Inicialización de propiedades
        $this->id_carrito = "";
        $this->id_usu = "";
        $this->id_producto = "";
        $this->cantidad = "";
        
    }

    /**
     * Añade un producto al carrito de un usuario.
     * Si el producto ya está en el carrito, actualiza su cantidad.
     *
     * @param int $id_usu ID del usuario.
     * @param int $id_producto ID del producto.
     * @param int $cantidad Cantidad a añadir (o establecer si es la primera vez).
     * @return bool True si la operación fue exitosa, false en caso contrario.
     */
    public function añadirProducto($id_usu, $id_producto, $cantidad) { 
        // Intentamos primero actualizar la cantidad si el producto ya está en el carrito. A lo que haya de cantidad le susmamos la nueva cantidad que quiere el usuario
        $actualizacion = "UPDATE carrito SET cantidad = cantidad + ? WHERE id_usu = ? AND id_producto = ?";
        $consulta = $this->conn->getConection()->prepare($actualizacion);

        

        // 'iii' -> i: integer (cantidad), i: integer (id_usu), i: integer (id_producto)
        $consulta->bind_param("iii", $cantidad, $id_usu, $id_producto);
        $consulta->execute();

        if ($consulta->affected_rows > 0) {
            // El producto ya estaba y se actualizó la cantidad.
            $consulta->close();
            return true;
        }
        $consulta->close(); // Cerrar la consulta de update


        // Si el producto no estaba en el carrito, lo insertamos.
        // NOTA: id_carrito se asume AUTO_INCREMENT y no se incluye en el INSERT.
        $sentencia = "INSERT INTO carrito (id_usu, id_producto, cantidad) VALUES (?, ?, ?)";
        $inserccion = $this->conn->getConection()->prepare($sentencia);
        
        // 'iii' -> i: integer (id_usu), i: integer (id_producto), i: integer (cantidad)
        // **CORRECCIÓN:** Ajustado bind_param para los 3 parámetros restantes.
        $inserccion->bind_param("iii", $id_usu, $id_producto, $cantidad);
        $result_insert = $inserccion->execute();

        $insertado = false;
        if ($result_insert && $inserccion->affected_rows === 1) {
            $insertado = true;
        }
        
        $inserccion->close();
        return $insertado;
    }

    /**
     * Elimina un producto específico del carrito de un usuario.
     *
     * @param int $id_usu ID del usuario.
     * @param int $id_producto ID del producto a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarProducto($id_usu, $id_producto) {
        
        $sentencia = "DELETE FROM carrito WHERE id_usu = ? AND id_producto = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        // 'ii' -> i: integer (id_usu), i: integer (id_producto)
        $consulta->bind_param("ii", $id_usu, $id_producto);
        $eliminacion = $consulta->execute();
        
        $eliminado = false;
        // affected_rows puede ser 0 si el producto no existía en el carrito, pero la operación fue "exitosa".
        // Para este caso, solo nos importa si no hubo error en la ejecución.
        if ($eliminacion) { 
            $eliminado = true;
        }
        
        $consulta->close();
        return $eliminado;
    }

    /**
     * Actualiza la cantidad de un producto específico en el carrito de un usuario.
     * Si la nueva cantidad es 0 o menos, el producto se elimina del carrito.
     *
     * @param int $id_usu ID del usuario.
     * @param int $id_producto ID del producto.
     * @param int $nueva_cantidad La nueva cantidad para el producto en el carrito.
     * @return bool True si la operación fue exitosa, false en caso contrario.
     */
    public function actualizarCantidadProducto($id_usu, $id_producto, $nueva_cantidad) {
        if ($nueva_cantidad <= 0) {
            // Si la cantidad es 0 o menos, eliminamos el producto del carrito.
            return $this->eliminarProducto($id_usu, $id_producto);
        }

        $sentencia = "UPDATE carrito SET cantidad = ? WHERE id_usu = ? AND id_producto = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("iii", $nueva_cantidad, $id_usu, $id_producto);
        $consulta->execute();
        
        $modificado = false;
        if ($consulta->affected_rows === 1) {
            $modificado = true;
        }
        
        $consulta->close();
        return $modificado;
    }

    
    /**
     * Obtiene todos los productos en el carrito de un usuario, junto con detalles del producto.
     *
     * @param int $id_usu ID del usuario.
     * @return array Un array de arrays numéricos con los detalles de los productos en el carrito.
     */
    public function getContenidoCarrito($id_usu) {
        // **CORRECCIÓN:** Cambiado el nombre de la tabla de 'carrito_items' a 'carrito'.
        // **CORRECCIÓN:** Se obtiene p.precio directamente de la tabla 'producto' para el cálculo del subtotal.
        $sentencia = "SELECT c.id_producto, p.titulo, p.imagen_producto, c.cantidad, p.precio precio_actual, (c.cantidad * p.precio) AS subtotal
                      FROM carrito c, producto p WHERE c.id_producto = p.id_producto
                      AND c.id_usu = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);


        $consulta->bind_param("i", $id_usu); // 'i' para integer (id_usu)
        $consulta->execute();
        
        // Variables locales donde meter los valores al introducirse en el while
        $id_producto_local = "";
        $titulo_local = "";
        $imagen_producto_local = "";
        $cantidad_local = "";
        $precio_actual_local = ""; // **CORRECCIÓN:** Variable para el precio actual del producto
        $subtotal_local = "";

        // **CORRECCIÓN:** Ajustado bind_result para los nuevos campos.
        $consulta->bind_result($id_producto_local, $titulo_local, $imagen_producto_local, $cantidad_local, $precio_actual_local, $subtotal_local);

        $items_carrito = [];
        while ($consulta->fetch()) {
            // Se mantiene el array numérico interno según tu preferencia.
            $items_carrito[$id_producto_local] = [
                $id_producto_local,
                $titulo_local,
                $imagen_producto_local,
                $cantidad_local,
                $precio_actual_local, // **CORRECCIÓN:** Incluir el precio actual del producto
                $subtotal_local
            ];
        }
        
        $consulta->close();
        return $items_carrito;
    }

    /**
     * Vacía completamente el carrito de un usuario.
     *
     * @param int $id_usu ID del usuario.
     * @return bool True si el carrito se vació con éxito, false en caso contrario.
     */
    public function vaciarCarrito($id_usu) {
        
        $sentencia = "DELETE FROM carrito WHERE id_usu = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);


        $consulta->bind_param("i", $id_usu); // 'i' para entero
        $vaciar = $consulta->execute();
        
        // No comprobamos affected_rows === 1 porque puede vaciar 0 o N elementos.
        // Solo nos importa que la ejecución no haya tenido errores.
        $vaciado = false;
        if ($vaciar) { 
            $vaciado = true;
        }
        
        $consulta->close();
        return $vaciado;
    }
}

?>
