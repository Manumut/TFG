<?php
// modelo/Marca.php

// Incluye la clase de conexión a la base de datos
require_once ("class_bd.php");

class Marca {
    private $conn; // Conexión a la base de datos
    // Propiedades de la clase para almacenar los datos de una marca (según tu estilo)
    private $id_marca;
    private $nombre; // Nombre de la marca
    private $imagen_marca;

    public function __construct(){
        $this->conn = new bd();
        // Inicialización de propiedades
        $this->id_marca = "";
        $this->nombre = "";
        $this->imagen_marca = "";

    }

    /**
     * Inserta una nueva marca en la base de datos.
     *
     * @param string $nombre Nombre de la marca.
     * @return bool True si la marca se insertó con éxito, false en caso contrario.
     */
    public function insertarMarca($nombre, $imagen_marca) {
        $sentencia = "INSERT INTO marca (nombre, imagen_marca) VALUES (?, ?)";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("ss", $nombre, $imagen_marca);
        $consulta->execute();

        $insertado = false;
        if ($consulta->affected_rows === 1) {
            $insertado = true;
        }
        
        $consulta->close();
        return $insertado;
    }

    /**
     * Obtiene todas las marcas de la base de datos.
     *
     * @return array Un array de arrays numéricos con los datos de todas las marcas.
     */
    public function getAllMarcas() {
        $sentencia = "SELECT id_marca, nombre, imagen_marca FROM marca";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->execute();
        
        // Vinculamos las columnas seleccionadas a las propiedades de la clase.
        // El orden de las variables DEBE COINCIDIR exactamente con el orden de las columnas en tu SELECT.
        $consulta->bind_result($id_marca, $nombre, $imagen_marca);
    
        $marcas = []; 
        while ($consulta->fetch()) {
            // NOTA: Se devuelve un array numérico para cada marca.
            // Se recomienda encarecidamente usar arrays asociativos (ej. ['id_marca' => $this->id_marca, ...])
            // para mejorar la legibilidad y mantenibilidad, pero se mantiene tu estilo.
            $marcas[$id_marca] = [$id_marca, $nombre, $imagen_marca];
        }

        $consulta->close();
        return $marcas;
    }

    /**
     * Obtiene los datos de una marca por su ID.
     *
     * @param int $id_marca ID de la marca.
     * @return array|null Un array numérico con los datos de la marca si se encuentra, null en caso contrario.
     */
    public function getMarcaById($id_marca) {
        $sentencia = "SELECT id_marca, nombre, imagen_marca FROM marca WHERE id_marca = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("i", $id_marca); // 'i' para entero
        $consulta->execute();
        
        // Vinculamos las columnas seleccionadas a las propiedades de la clase.
        $consulta->bind_result($id_marca, $nombre, $imagen_marca);
    
        $marca_id = null; // Inicializamos a null
        if ($consulta->fetch()) { // Intentamos obtener la primera (y única) fila
            // NOTA: Se devuelve un array numérico.
            $marca_id = [
                $id_marca,
                $nombre,
                $imagen_marca
            ];
        }

        $consulta->close();
        return $marca_id; // Devuelve null si no se encontró, o la marca si sí
    }

    /**
     * Actualiza los datos de una marca existente.
     *
     * @param int $id_marca ID de la marca a actualizar.
     * @param string $nombre Nuevo nombre de la marca.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarMarca($id_marca, $nombre, $imagen_marca) {
        $sentencia = "UPDATE marca SET nombre = ?, imagen_marca = ? WHERE id_marca = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        // 'si' -> s: string (nombre), i: integer (id_marca)
        $consulta->bind_param("ssi", $nombre, $imagen_marca, $id_marca);
        $consulta->execute();
        
        $modificado = false;
        if ($consulta->affected_rows === 1) {
            $modificado = true;
        }
        
        $consulta->close();
        return $modificado;
    }

    /**
     * Elimina una marca de la base de datos.
     *
     * @param int $id_marca ID de la marca a eliminar.
     * @return bool True si la marca fue eliminada con éxito, false en caso contrario.
     */
    public function eliminarMarca($id_marca) {
        $sentencia = "DELETE FROM marca WHERE id_marca = ?";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        $consulta->bind_param("i", $id_marca); // 'i' para entero
        $consulta->execute();
        
        $eliminado = false;
        if ($consulta->affected_rows === 1) {
            $eliminado = true;
        }
        
        $consulta->close();
        return $eliminado;
    }
}
