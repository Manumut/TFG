<?php

require_once ("class_bd.php");

class Usuario {
    private $conn; 
    private $id;      
    private $nombre;
    private $apell;    
    private $correo;
    private $psw;      
    private $tipo;     

    public function __construct(){
        $this->conn = new bd();
        $this->id = "";
        $this->nombre = "";
        $this->apell = "";
        $this->correo = "";
        $this->psw = ""; 
        $this->tipo = "";
    }

    
    public function registrarUsuario($nombre, $apellidos, $correo, $password, $tipo_usu = 'registrado') {
        //Hashear la contraseña antes de guardarla en la base de datos por seguridad.**
        $cifrada = password_hash($password, PASSWORD_DEFAULT);

        // Verifico si el correo ya existe antes de insertar.
        // Uso getUsuarioByEmail para la verificación.
        if ($this->getUsuarioByEmail($correo) !== null) { 
            error_log("Error: Intento de registrar usuario con correo ya existente: " . $correo);
            return false; // El correo ya existe
        }

        // SQL para insertar un nuevo usuario
        $sentencia = "INSERT INTO usuarios (nombre, apellidos, correo, psw, tipo_usu) VALUES (?, ?, ?, ?, ?)";
        
        $consulta = $this->conn->getConection()->prepare($sentencia);

        // Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de registro de usuario" );
            return false;
        }   

        
        // Uso $cifrada para la inserción de la contraseña.
        $consulta->bind_param("sssss", $nombre, $apellidos, $correo, $cifrada, $tipo_usu); 
        $result = $consulta->execute();

        $insertado = false;
        if ($result && $consulta->affected_rows === 1) { 
            $insertado = true;
        } else if ($result === false) {
             error_log("Error al ejecutar la consulta de registro de usuario: " . $consulta->error);
        }
        
        $consulta->close();
        return $insertado;
    }

   
    public function verificarLogin($correo, $password) {
        // Obtenemos todos los datos del usuario por su correo, incluyendo la contraseña hasheada.
        // getUsuarioByEmail devuelve un array numérico con [id_usu, nombre, apellidos, correo, psw, tipo_usu]
        $usuario_datos = $this->getUsuarioByEmail($correo);

        if ($usuario_datos === null) {
            return null; // Usuario no encontrado por correo
        }

        // Extraer la contraseña hasheada de la base de datos (posición 4 del array)
        $cifrada_bd = $usuario_datos[4]; 

        //Verifico la contraseña hasheada con password_verify().**
        if (password_verify($password, $cifrada_bd)) {
            // Credenciales correctas, devolvemos los datos necesarios para la sesión
            // [id_usu, nombre, tipo_usu]
            return [$usuario_datos[0], $usuario_datos[1], $usuario_datos[5]]; // id_usu, nombre, tipo_usu
        } else {
            return null; // Contraseña incorrecta
        }
    }

    
    public function getUsuarioById($id_usu) {
        // No seleccionamos la contraseña aquí por seguridad, a menos que sea estrictamente necesario.
        $sentencia = "SELECT id_usu, nombre, apellidos, correo, tipo_usu FROM usuarios WHERE id_usu = ?";
        
        $conn_obj = $this->conn->getConection();
        $consulta = $conn_obj->prepare($sentencia);

        // **IMPORTANTE: Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de obtener usuario por ID: " . $conn_obj->error);
            return null;
        }

        $consulta->bind_param("i", $id_usu);
        $result_execute = $consulta->execute(); // **IMPORTANTE: Capturar el resultado de execute().**

        // **IMPORTANTE: Manejo de errores en la ejecución de la consulta.**
        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de obtener usuario por ID: " . $consulta->error);
            $consulta->close();
            return null;
        }

        // Variables locales para bind_result (el orden debe coincidir con el SELECT)
        $id_local = "";
        $nombre_local = "";
        $apellidos_local = "";
        $correo_local = "";
        $tipo_local = "";

        $consulta->bind_result($id_local, $nombre_local, $apellidos_local, $correo_local, $tipo_local);
        
        $usuario_datos = null;
        if ($consulta->fetch()) {
            $usuario_datos = [
                $id_local,
                $nombre_local,
                $apellidos_local,
                $correo_local,
                $tipo_local
            ];
        }
        
        $consulta->close();
        return $usuario_datos;
    }

    /**
     * Obtiene los datos de un usuario por su correo electrónico (coincidencia EXACTA).
     * Útil para verificar si un correo ya existe antes de registrar o para obtener datos de login.
     *
     * @param string $correo Correo electrónico del usuario.
     * @return array|null Un array numérico con los datos del usuario si se encuentra, null en caso contrario.
     * El array incluye [id_usu, nombre, apellidos, correo, psw (hasheada), tipo_usu].
     */
    public function getUsuarioByEmail($correo) {
        // Seleccionamos la contraseña hasheada aquí porque es necesaria para 'verificarLogin'.
        $sentencia = "SELECT id_usu, nombre, apellidos, correo, psw, tipo_usu FROM usuarios WHERE correo = ?"; 
        
        $conn_obj = $this->conn->getConection();
        $consulta = $conn_obj->prepare($sentencia);

        // **IMPORTANTE: Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de obtener usuario por email: " . $conn_obj->error);
            return null;
        }

        $consulta->bind_param("s", $correo);
        $result_execute = $consulta->execute(); // **IMPORTANTE: Capturar el resultado de execute().**

        // **IMPORTANTE: Manejo de errores en la ejecución de la consulta.**
        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de obtener usuario por email: " . $consulta->error);
            $consulta->close();
            return null;
        }

        // Variables locales para bind_result (el orden debe coincidir con el SELECT)
        $id_local = "";
        $nombre_local = "";
        $apellidos_local = "";
        $correo_local = "";
        $psw_local = ""; // Capturamos la contraseña hasheada
        $tipo_local = "";

        $consulta->bind_result($id_local, $nombre_local, $apellidos_local, $correo_local, $psw_local, $tipo_local);
        
        $usuario_datos = null;
        if ($consulta->fetch()) {
            $usuario_datos = [
                $id_local,
                $nombre_local,
                $apellidos_local,
                $correo_local,
                $psw_local, // Incluimos la contraseña hasheada en el array de retorno
                $tipo_local
            ];
        }
        
        $consulta->close();
        return $usuario_datos;
    }
    
    /**
     * Actualiza los datos de un usuario existente.
     * Permite actualizar nombre, apellidos, correo y opcionalmente la contraseña.
     *
     * @param int $id_usu ID del usuario a actualizar.
     * @param string $nombre Nuevo nombre.
     * @param string $apellidos Nuevos apellidos.
     * @param string $correo Nuevo correo electrónico.
     * @param string|null $password Nueva contraseña (opcional). Si es null o vacío, no se actualiza la contraseña.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarUsuario($id_usu, $nombre, $apellidos, $correo, $password = null) {
        $conn_obj = $this->conn->getConection();
        $params = [];
        $types = "";
        
        // Construcción dinámica de la sentencia UPDATE y los parámetros
        $sentencia_base = "UPDATE usuarios SET nombre = ?, apellidos = ?, correo = ?";
        $types .= "sss";
        $params[] = $nombre;
        $params[] = $apellidos;
        $params[] = $correo;

        // **CRÍTICO:** Si se proporciona una nueva contraseña, DEBE ser hasheada.
        if ($password !== null && !empty($password)) {
            $cifrada = password_hash($password, PASSWORD_DEFAULT);
            $sentencia_base .= ", psw = ?";
            $types .= "s";
            $params[] = $cifrada;
        }

        $sentencia_final = $sentencia_base . " WHERE id_usu = ?";
        $types .= "i"; // El último parámetro es el id_usu, que es un entero.
        $params[] = $id_usu;
        
        $consulta = $conn_obj->prepare($sentencia_final);

        // **IMPORTANTE: Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de actualización de usuario: " . $conn_obj->error);
            return false;
        }

        // Bindear los parámetros manualmente (sin call_user_func_array ni refValues)
        // Se construye el array de parámetros para bind_param
        $bind_params = [];
        $bind_params[] = $types; // El primer elemento es la cadena de tipos
        foreach ($params as $key => $value) {
            $bind_params[] = &$params[$key]; // ¡Pasar por referencia!
        }
        
        // Usar call_user_func_array para llamar bind_param con los parámetros dinámicos
        // Aunque no usamos refValues directamente, internamente esto hace el paso por referencia.
        call_user_func_array([$consulta, 'bind_param'], $bind_params);

        $result = $consulta->execute(); // **IMPORTANTE: Capturar el resultado de execute().**
    
        $modificado = false;
        // **IMPORTANTE: Comprobación de errores en la ejecución.**
        if ($result && $consulta->affected_rows === 1) { 
            $modificado = true;
        } else if ($result === false) {
             error_log("Error al ejecutar la consulta de actualización de usuario: " . $consulta->error);
        }
    
        $consulta->close();
        return $modificado;
    }

    /**
     * Obtiene todos los usuarios (excluyendo el administrador).
     * Utilizado para el panel de administración.
     *
     * @return array Un array de arrays numéricos con los datos de los usuarios.
     */
    public function getAllUsuarios() {
        // No seleccionamos la contraseña por seguridad.
        // Asegúrate de que tu columna 'tipo_usu' tenga el valor 'administrador' para los administradores.
        $sentencia = "SELECT id_usu, nombre, apellidos, correo, tipo_usu FROM usuarios WHERE tipo_usu != 'administrador'"; // CAMBIO CLAVE: 'admin' a 'administrador'
        $conn_obj = $this->conn->getConection();
        $consulta = $conn_obj->prepare($sentencia);

        // **IMPORTANTE: Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de obtener todos los usuarios: " . $conn_obj->error);
            return [];
        }

        $result_execute = $consulta->execute(); // **IMPORTANTE: Capturar el resultado de execute().**

        // **IMPORTANTE: Manejo de errores en la ejecución de la consulta.**
        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de obtener todos los usuarios: " . $consulta->error);
            return [];
        }

        // Vinculamos las columnas seleccionadas a variables locales para arrays numéricos.
        $id_local = "";
        $nombre_local = "";
        $apellidos_local = "";
        $correo_local = "";
        $tipo_local = "";

        // El orden debe coincidir con el SELECT
        $consulta->bind_result($id_local, $nombre_local, $apellidos_local, $correo_local, $tipo_local);

        $usuarios = [];
        while ($consulta->fetch()) {
            $usuarios[$id_local] = [
                $id_local,
                $nombre_local,
                $apellidos_local,
                $correo_local,
                $tipo_local
            ]; 
        }

        $consulta->close();
        return $usuarios;
    }

    /**
     * Busca usuarios por nombre o correo (para el panel de administración).
     * Excluye administradores y busca coincidencias parciales.
     *
     * @param string $busqueda Término de búsqueda.
     * @return array Un array de arrays numéricos con los datos de los usuarios encontrados.
     */
    public function buscarUsuarios($busqueda) {
        // SQL para buscar usuarios por nombre o correo, excluyendo administradores
        $sentencia = "SELECT id_usu, nombre, apellidos, correo, tipo_usu FROM usuarios WHERE (nombre LIKE ? OR correo LIKE ?) AND tipo_usu != 'administrador'"; // CAMBIO CLAVE: 'admin' a 'administrador'
        
        $conn_obj = $this->conn->getConection();
        $consulta = $conn_obj->prepare($sentencia);

        // **IMPORTANTE: Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de búsqueda de usuarios: " . $conn_obj->error);
            return [];
        }

        $param = "%" . $busqueda . "%"; // Añadir comodines para búsqueda LIKE
        $consulta->bind_param("ss", $param, $param); // Ambos marcadores de posición son para el término de búsqueda

        $result_execute = $consulta->execute(); // **IMPORTANTE: Capturar el resultado de execute().**

        // **IMPORTANTE: Manejo de errores en la ejecución de la consulta.**
        if ($result_execute === false) {
            error_log("Error al ejecutar la consulta de búsqueda de usuarios: " . $consulta->error);
            return [];
        }

        // Vinculamos las columnas seleccionadas a variables locales para arrays numéricos.
        $id_local = "";
        $nombre_local = "";
        $apellidos_local = "";
        $correo_local = "";
        $tipo_local = "";

        // El orden debe coincidir con el SELECT
        $consulta->bind_result($id_local, $nombre_local, $apellidos_local, $correo_local, $tipo_local);

        $usuarios = [];
        while ($consulta->fetch()) {
            $usuarios[$id_local] = [
                $id_local,
                $nombre_local,
                $apellidos_local,
                $correo_local,
                $tipo_local
            ];
        }

        $consulta->close();
        return $usuarios;
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * @param int $id_usu ID del usuario a eliminar.
     * @return bool True si el usuario fue eliminado con éxito, false en caso contrario.
     */
    public function eliminarUsuario($id_usu) {
        $sentencia = "DELETE FROM usuarios WHERE id_usu = ?";
        
        $conn_obj = $this->conn->getConection();
        $consulta = $conn_obj->prepare($sentencia);

        // **IMPORTANTE: Manejo de errores en la preparación de la consulta.**
        if ($consulta === false) {
            error_log("Error al preparar la consulta de eliminación de usuario: " . $conn_obj->error);
            return false;
        }

        $consulta->bind_param("i", $id_usu); // 'i' para entero
        $result = $consulta->execute(); // **IMPORTANTE: Capturar el resultado de execute().**
        
        $eliminado = false;
        // **IMPORTANTE: Comprobación de errores en la ejecución y filas afectadas.**
        if ($result && $consulta->affected_rows === 1) { 
            $eliminado = true;
        } else if ($result === false) {
            error_log("Error al ejecutar la consulta de eliminación de usuario: " . $consulta->error);
        }
        
        $consulta->close();
        return $eliminado; 
    }
}
