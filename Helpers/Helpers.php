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
        // Evitar duplicación del header
        static $headerRendered = false;
        if ($headerRendered) {
            return;
        }
        $headerRendered = true;
        
        $view_header = __DIR__ . "/../Views/Template/headerAdmin.php";
        require_once($view_header);
    }

    function footerAdmin($data=""){
        // Evitar duplicación del footer
        static $footerRendered = false;
        if ($footerRendered) {
            return;
        }
        $footerRendered = true;
        
        $view_footer = __DIR__ . "/../Views/Template/footerAdmin.php";
        require_once($view_footer);
    }


    function headerTienda($data="")
    {
        // Evitar duplicación del header
        static $headerRendered = false;
        if ($headerRendered) {
            return;
        }
        $headerRendered = true;
        
        $view_header = __DIR__ . "/../Views/Template/headerTienda.php";
        require_once($view_header);
    }

    function footerTienda($data="")
    {
        // Evitar duplicación del footer
        static $footerRendered = false;
        if ($footerRendered) {
            return;
        }
        $footerRendered = true;
        
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
    
    function getFile(string $url, $data)
    {
        ob_start();
        require_once("Views/{$url}.php");
        $file = ob_get_clean();
        return $file;        
    }

    function uploadImage(array $data, string $name){
        $url_temp = $data['tmp_name'];
        $destino    = 'Assets/images/uploads/'.$name;        
        $move = move_uploaded_file($url_temp, $destino);
        return $move;
    }

    function deleteFile(string $name){
        if(file_exists('Assets/images/uploads/'.$name)){
            unlink('Assets/images/uploads/'.$name);
        }
    }

    // Subir imagen temporal
    function uploadTempImage(array $data, string $name){
        $url_temp = $data['tmp_name'];
        $destino = 'Assets/images/temp/'.$name;        
        $move = move_uploaded_file($url_temp, $destino);
        return $move;
    }

    // Verificar si una imagen existe
    function imageExists(string $name, string $folder = 'uploads'){
        return file_exists('Assets/images/'.$folder.'/'.$name);
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

    function clear_cadena(string $cadena){
        //Reemplazamos la A y a
        $cadena = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $cadena
        );
 
        //Reemplazamos la E y e
        $cadena = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $cadena );
 
        //Reemplazamos la I y i
        $cadena = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $cadena );
 
        //Reemplazamos la O y o
        $cadena = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $cadena );
 
        //Reemplazamos la U y u
        $cadena = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $cadena );
 
        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç',',','.',';',':'),
        array('N', 'n', 'C', 'c','','','',''),
        $cadena
        );
        return $cadena;
    }