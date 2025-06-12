<?php
    // Generar una cookie
    function set_cookie(String $nom, $val){
        setcookie($nom,$val,time()+(86400*30));
    }

    // Eliminar una cookie
    function unset_cookie(String $nom){
        $comp = false;

        if(isset($_COOKIE[$nom])){
            setcookie($nom, "", time()-(86400*30));
            $comp=true;
        }
        return $comp;
    }

    // Genero una sesion
    function start_session(){
        if(session_status() === PHP_SESSION_NONE){
                session_start();
        }
    }

 //Abrir una sesión
 function set_session(String $nom1, $val1, String $nom2, $val2, String $nom3, $val3){
    start_session();
    $_SESSION[$nom1]=$val1; //Acepta el nombre
    $_SESSION[$nom2]=$val2; //Acepta la psw
    $_SESSION[$nom3]=$val3; //Acepta el tipo de usuario
}

    // Devolver los datos de una sesion
    function get_session(String $nom){
        start_session();
        return $_SESSION[$nom];
    }

    // Funcion para borrar la sesion
    function unset_session(){
        start_session();
        session_unset();
        session_destroy();
    }

    function is_session(String $nom){
        start_session();
        $comp = isset($_SESSION[$nom]);
        return $comp;
    }

?>