<?php
// modelo/Producto.php

// Incluye la clase de conexión a la base de datos
require_once ("class_bd.php"); // La ruta es correcta si class_bd.php está en la misma carpeta modelo/

class Producto {
    private $conn; // Conexión a la base de datos
    // Propiedades de la clase para almacenar los datos de un producto (según tu estilo)
    private $id_producto;
    private $titulo;
    private $precio;
    private $imagen_producto;
    private $descripcion;
    private $id_marca;
    private $cantidad; // Stock disponible

    public function __construct(){
        $this->conn = new bd();
        // Inicialización de propiedades
        $this->id_producto = "";
        $this->titulo = "";
        $this->precio = "";
        $this->imagen_producto = "";
        $this->descripcion = "";
        $this->id_marca = "";
        $this->cantidad = "";
    }

    /**
     * Inserta un nuevo producto en la base de datos.
     *
     * @param string $titulo Título del producto.
     * @param float $precio Precio del producto.
     * @param string $imagen_producto Ruta o URL de la imagen del producto.
     * @param string $descripcion Descripción detallada del producto.
     * @param int $id_marca ID de la marca a la que pertenece el producto.
     * @param int $cantidad Cantidad inicial en stock.
     * @return bool True si el producto se insertó con éxito, false en caso contrario.
     */
    public function insertarProducto($titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad) {
        $sentencia = "INSERT INTO producto (titulo, precio, imagen_producto, descripcion, id_marca, cantidad) VALUES (?, ?, ?, ?, ?, ?)";
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de inserción de producto: " . $conn->error); 
            return false; 
        }

        // 'sdssii' -> s: string (titulo), d: double (precio), s: string (imagen), s: string (descripcion), i: integer (id_marca), i: integer (cantidad)
        $consulta->bind_param("sdssii", $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad);
        $result = $consulta->execute(); // Capturamos el resultado de execute()

        $insertado = false;
        if ($result && $consulta->affected_rows === 1) { // Comprobamos que la ejecución fue exitosa Y que se insertó 1 fila
            $insertado = true;
        } else if ($result === false) {
             error_log("Error al ejecutar la consulta de inserción de producto: " . $consulta->error);
        }
        
        $consulta->close();
        return $insertado;
    }

    /**
     * Obtiene todos los productos de la base de datos.
     *
     * @return array Un array de arrays numéricos con los datos de todos los productos.
     */
    public function getAllProductos() {
        // SQL para seleccionar todos los productos. Se une con la tabla 'marca' para obtener el nombre de la marca.
        $sentencia = "SELECT p.id_producto, p.titulo, p.precio, p.imagen_producto, p.descripcion, p.id_marca, p.cantidad, m.nombre AS nombre_marca
                      FROM producto p JOIN marca m ON p.id_marca = m.id_marca"; // Usamos JOIN explícito para claridad
        
        $conn = $this->conn->getConection(); // Obtener la conexión
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de obtener todos los productos: " . $conn->error); 
            return []; 
        }

        $result_execute = $consulta->execute(); // Capturamos el resultado de execute()

        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de obtener todos los productos: " . $consulta->error);
            return [];
        }
        
        $id_producto = "";
        $titulo = "";
        $precio = "";
        $imagen_producto = "";
        $descripcion = "";
        $id_marca = "";
        $cantidad = "";
        $nombre_marca = ""; // Variable para el nombre de la marca

        // El orden de las variables en bind_result DEBE COINCIDIR exactamente con el orden de las columnas en el SELECT.
        $consulta->bind_result($id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad, $nombre_marca);
    
        $productos = []; 
        while ($consulta->fetch()) {
            // Se devuelve un array numérico para cada producto, según tu estilo.
            $productos[$id_producto] = [$id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad, $nombre_marca];
        }

        $consulta->close();
        return $productos;
    }

    /**
     * Obtiene los datos de un producto por su ID.
     *
     * @param int $id_producto ID del producto.
     * @return array|null Un array numérico con los datos del producto si se encuentra, null en caso contrario.
     */
    public function getProductoById($id_producto) {
        $sentencia = "SELECT p.id_producto, p.titulo, p.precio, p.imagen_producto, p.descripcion, p.id_marca, p.cantidad, m.nombre AS nombre_marca
                      FROM producto p JOIN marca m ON p.id_marca = m.id_marca 
                      WHERE p.id_producto = ?";
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de obtener producto por ID: " . $conn->error); 
            return null; 
        }

        $consulta->bind_param("i", $id_producto); // 'i' para entero
        $result_execute = $consulta->execute();

        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de obtener producto por ID: " . $consulta->error);
            $consulta->close();
            return null;
        }
        
        $id_producto_local = "";
        $titulo_local = "";
        $precio_local = "";
        $imagen_producto_local = "";
        $descripcion_local = "";
        $id_marca_local = "";
        $cantidad_local = "";
        $nombre_marca_local = ""; 
        
        $consulta->bind_result($id_producto_local, $titulo_local, $precio_local, $imagen_producto_local, $descripcion_local, $id_marca_local, $cantidad_local, $nombre_marca_local);
    
        $producto = null; 
        if ($consulta->fetch()) {
            $producto = [
                $id_producto_local,
                $titulo_local,
                $precio_local,
                $imagen_producto_local,
                $descripcion_local,
                $id_marca_local,
                $cantidad_local,
                $nombre_marca_local
            ];
        }

        $consulta->close();
        return $producto; 
    }

    /**
     * Obtiene productos filtrados por ID de marca.
     *
     * @param int $id_marca ID de la marca.
     * @return array Un array de arrays numéricos con los productos de la marca especificada.
     */
    public function getProductosByMarca($id_marca) {
        $sentencia = "SELECT p.id_producto, p.titulo, p.precio, p.imagen_producto, p.descripcion, p.id_marca, p.cantidad, m.nombre AS nombre_marca
                      FROM producto p JOIN marca m ON p.id_marca = m.id_marca 
                      WHERE p.id_marca = ?";
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de productos por marca: " . $conn->error); 
            return []; 
        }

        $consulta->bind_param("i", $id_marca); 
        $result_execute = $consulta->execute();

        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de productos por marca: " . $consulta->error);
            return [];
        }
        
        $id_producto = "";
        $titulo = "";
        $precio = "";
        $imagen_producto = "";
        $descripcion = "";
        $id_marca = "";
        $cantidad = "";
        $nombre_marca = "";
        $consulta->bind_result($id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad, $nombre_marca);

        $productos = [];
        while ($consulta->fetch()) {
            $productos[$id_producto] = [$id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad, $nombre_marca];
        }
        
        $consulta->close();
        return $productos;
    }

    /**
     * Actualiza los datos de un producto existente.
     *
     * @param int $id_producto ID del producto a actualizar.
     * @param string $titulo Nuevo título.
     * @param float $precio Nuevo precio.
     * @param string $imagen_producto Nueva ruta o URL de la imagen.
     * @param string $descripcion Nueva descripción.
     * @param int $id_marca Nuevo ID de marca.
     * @param int $cantidad Nueva cantidad en stock.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarProducto($id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad) {
        $sentencia = "UPDATE producto SET titulo = ?, precio = ?, imagen_producto = ?, descripcion = ?, id_marca = ?, cantidad = ? WHERE id_producto = ?";
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de actualización de producto: " . $conn->error); 
            return false; 
        }

        $consulta->bind_param("sdssiii", $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad, $id_producto);
        $result = $consulta->execute();
        
        $modificado = false;
        if ($result && $consulta->affected_rows === 1) {
            $modificado = true;
        } else if ($result === false) {
             error_log("Error al ejecutar la consulta de actualización de producto: " . $consulta->error);
        }
        
        $consulta->close();
        return $modificado;
    }

    /**
     * Elimina un producto de la base de datos.
     *
     * @param int $id_producto ID del producto a eliminar.
     * @return bool True si el producto fue eliminado con éxito, false en caso contrario.
     */
    public function eliminarProducto($id_producto) {
        $sentencia = "DELETE FROM producto WHERE id_producto = ?";
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de eliminación de producto: " . $conn->error); 
            return false; 
        }

        $consulta->bind_param("i", $id_producto); 
        $result = $consulta->execute();
        
        $eliminado = false;
        if ($result && $consulta->affected_rows === 1) { 
            $eliminado = true;
        } else if ($result === false) {
            error_log("Error al ejecutar la consulta de eliminación de producto: " . $consulta->error);
        }
        
        $consulta->close();
        return $eliminado;
    }

    /**
     * Busca productos por título.
     *
     * @param string $busqueda Término de búsqueda.
     * @return array Un array de arrays numéricos con los productos encontrados.
     */
    public function buscarProductos($busqueda) {
        $sentencia = "SELECT p.id_producto, p.titulo, p.precio, p.imagen_producto, p.descripcion, p.id_marca, p.cantidad, m.nombre AS nombre_marca
                      FROM producto p JOIN marca m ON p.id_marca = m.id_marca 
                      AND p.titulo LIKE ?"; 
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de búsqueda de productos: " . $conn->error); 
            return []; 
        }

        $param = "%" . $busqueda . "%"; 
        $consulta->bind_param("s", $param); 
        $result_execute = $consulta->execute();
        
        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de búsqueda de productos: " . $consulta->error);
            return [];
        }

        $id_producto = "";
        $titulo = "";
        $precio = "";
        $imagen_producto = "";
        $descripcion = "";
        $id_marca = "";
        $cantidad = "";
        $nombre_marca = "";
        $consulta->bind_result($id_producto, $titulo, $precio, $imagen_producto, $descripcion, $id_marca, $cantidad, $nombre_marca);

        $productos = [];
        while ($consulta->fetch()) {
            $productos[$id_producto] = [ 
                $id_producto, 
                $titulo, 
                $precio, 
                $imagen_producto, 
                $descripcion, 
                $id_marca, 
                $cantidad, 
                $nombre_marca
            ];
        }
        
        $consulta->close();
        return $productos;
    }

    /**
     * Actualiza la cantidad (stock) de un producto.
     *
     * @param int $id_producto ID del producto.
     * @param int $nueva_cantidad La nueva cantidad de stock.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarCantidad($id_producto, $nueva_cantidad) {
        $sentencia = "UPDATE producto SET cantidad = ? WHERE id_producto = ?";
        
        $conn = $this->conn->getConection();
        $consulta = $conn->prepare($sentencia);

        if ($consulta === false) { 
            error_log("Error al preparar la consulta de actualización de cantidad de producto: " . $conn->error); 
            return false; 
        }

        $consulta->bind_param("ii", $nueva_cantidad, $id_producto); 
        $result = $consulta->execute();
        
        $modificado = false;
        if ($result && $consulta->affected_rows === 1) {
            $modificado = true;
        } else if ($result === false) {
             error_log("Error al ejecutar la consulta de actualización de cantidad de producto: " . $consulta->error);
        }
        
        $consulta->close();
        return $modificado;
    }
}
