<?php
// Devuelve la ruta de Assets para uso en headerAdmin.php
function media(){
    return BASE_URL . "/Assets";
}
    //Retorna la url del proyecto
    function base_url(){
        return BASE_URL;
    }
    //Retorna la url del Assets
    function assets_url(){
        return BASE_URL."/Assets";
    }

    function headerAdmin($data=""){
        $view_header = __DIR__ . "/../Views/Template/headerAdmin.php";
        require_once($view_header);
    }

    function footerAdmin($data=""){
        $view_footer = __DIR__ . "/../Views/Template/footerAdmin.php";
        require_once($view_footer);
    }


    function headerTienda($data="")
    {
        $view_header = __DIR__ . "/../Views/Template/headerTienda.php";
        require_once($view_header);
    }

    function footerTienda($data="")
    {
        $view_footer = __DIR__ . "/../Views/Template/footerTienda.php";
        require_once($view_footer);
    }

    function getModal($modalName, $data = [])
    {
        $modalFile = __DIR__ . "/../Views/Template/Modals/$modalName.php";
        if (file_exists($modalFile)) {
            require_once($modalFile);
        }
    }
    
    //Muestra información formateada
    function dep($data){
        $format  = print_r('<pre>');
        $format .= print_r($data);
        $format .= print_r('</pre>');
        return $format;
    }

    //Elimina exceso de espacios entre palabras
    function strClean($strCadena){
        $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena);
        $string = trim($string);//Elimina espacios en blanco al inicio y al final
        $string = stripslashes($string);//Elimina las \ invertidas
        $string = str_ireplace("<script>","",$string);
        $string = str_ireplace("</script>","",$string);
        $string = str_ireplace("<script src>","",$string);
        $string = str_ireplace("<script type=>","",$string);
        $string = str_ireplace("SELECT * FROM","",$string);
        $string = str_ireplace("DELETE FROM","",$string);
        $string = str_ireplace("INSERT INTO","",$string);
        $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
        $string = str_ireplace("DROP TABLE","",$string);
        $string = str_ireplace("OR '1'='1'","",$string);
        $string = str_ireplace('OR "1"="1"',"",$string);
        $string = str_ireplace('OR ´1´=´1´',"",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("LIKE '","",$string);
        $string = str_ireplace('LIKE "',"",$string);
        $string = str_ireplace("LIKE ´","",$string);
        $string = str_ireplace("OR ´a´=´a´","",$string);
        $string = str_ireplace('OR "a"="a"',"",$string);
        $string = str_ireplace("OR 'a'='a'","",$string);
        $string = str_ireplace("--","",$string);
        $string = str_ireplace("^","",$string);
        $string = str_ireplace("[","",$string);
        $string = str_ireplace("]","",$string);
        $string = str_ireplace("==","",$string);
        return $string;
    }
    //Genera una contraseña de 10 caracteres
    function passGenerator($length = 10){
        $pass = "";
        $longitudPass = $length;
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena = strlen($cadena);

        for($i=1; $i<=$longitudPass; $i++){
            $pos = rand(0,$longitudCadena-1);
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;
    }

    // --- Auth helpers ---
    function current_user_name() {
        if (isset($_SESSION['admin']['nombre'])) return $_SESSION['admin']['nombre'];
        if (isset($_SESSION['empleado']['nombre'])) return $_SESSION['empleado']['nombre'];
        if (isset($_SESSION['usuario']['nombre'])) return $_SESSION['usuario']['nombre'];
        return null;
    }
    function is_logged_in() {
        return !empty($_SESSION['admin']) || !empty($_SESSION['empleado']) || !empty($_SESSION['usuario']);
    }