<?php
    // modelo/class_bd.php

    // Incluye el archivo de credenciales de la base de datos
    // **CORRECCIÓN DE RUTA:** La ruta ahora es '../../cred.php' para que funcione desde TFG/modelo/
    // y alcance la raíz de htdocs donde está cred.php.
    require_once(__DIR__ . "/../../cred.php"); 

    class bd{
        private $conn;

        public function __construct(){
            // Intenta establecer la conexión a la base de datos
            // Los parámetros son: servidor, usuario, contraseña, nombre_base_de_datos
            $this->conn= new mysqli("localhost", USU_CONN, PSW_CONN,"allintoys");
            // Establece el conjunto de caracteres a UTF-8
            // Esto es vital para asegurar que los caracteres especiales se manejen correctamente.
            $this->conn->set_charset("utf8");
        }

        /**
         * Obtiene el objeto de conexión a la base de datos.
         *
         * @return mysqli El objeto de conexión mysqli.
         */
        public function getConection(){
            // Devuelve el objeto de conexión
            return $this->conn;
        }
    }
?>
