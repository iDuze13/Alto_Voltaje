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

    //Elimina exceso de espacios entre palabras y previene inyecciones
    function strClean($strCadena){
        if (empty($strCadena)) {
            return '';
        }
        
        $string = trim($strCadena); // Elimina espacios en blanco al inicio y al final
        $string = stripslashes($string); // Elimina las \ invertidas
        
        // Normalizar espacios
        $string = preg_replace('/\s+/', ' ', $string);
        
        // Array de patrones peligrosos más completo
        $dangerous_patterns = [
            '<script', '</script', '<script src', '<script type=',
            'SELECT * FROM', 'DELETE FROM', 'INSERT INTO', 'UPDATE SET',
            'SELECT COUNT(*) FROM', 'DROP TABLE', 'DROP DATABASE',
            "OR '1'='1'", 'OR "1"="1"', 'OR ´1´=´1´',
            'is NULL; --', "LIKE '", 'LIKE "', 'LIKE ´',
            "OR ´a´=´a´", 'OR "a"="a"', "OR 'a'='a'",
            '--', '^', '[', ']', '==',
            'UNION SELECT', 'UNION ALL', 'CONCAT(', 'CHAR(',
            'CONVERT(', 'CAST(', 'EXEC(', 'EXECUTE(',
            '<iframe', '<object', '<embed', '<link', 
            'javascript:', 'vbscript:', 'data:',
            'onload=', 'onerror=', 'onclick=', 'onmouseover='
        ];
        
        // Aplicar filtros de seguridad
        foreach ($dangerous_patterns as $pattern) {
            $string = str_ireplace($pattern, '', $string);
        }
        
        return $string;
    }
    //Genera una contraseña segura
    function passGenerator($length = 10){
        if ($length < 4) {
            $length = 10; // Mínimo de seguridad
        }
        
        // Usar caracteres más seguros y balanceados
        $uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $lowercase = "abcdefghijklmnopqrstuvwxyz";
        $numbers = "0123456789";
        $special = "!@#$%&*";
        
        // Asegurar que la contraseña tenga al menos un carácter de cada tipo
        $pass = '';
        $pass .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $pass .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $pass .= $numbers[random_int(0, strlen($numbers) - 1)];
        $pass .= $special[random_int(0, strlen($special) - 1)];
        
        // Completar el resto con caracteres aleatorios
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $pass .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar la contraseña
        $passArray = str_split($pass);
        shuffle($passArray);
        
        return implode('', $passArray);
    }

    function getPermisos(int $idmodulo){
        require_once ("Models/PermisosModel.php");
        $objPermisos = new PermisosModel();
        if(!empty($_SESSION['userData'])){
            $idrol = $_SESSION['userData']['idrol'];
            $arrPermisos = $objPermisos->permisosModulo($idrol);
            $permisos = '';
            $permisosMod = '';
            if(count($arrPermisos) > 0 ){
                $permisos = $arrPermisos;
                $permisosMod = isset($arrPermisos[$idmodulo]) ? $arrPermisos[$idmodulo] : "";
            }
            $_SESSION['permisos'] = $permisos;
            $_SESSION['permisosMod'] = $permisosMod;
        }
    }

    //Envio de correos
    function sendEmail($data,$template)
    {
        if(ENVIRONMENT == 1){
            $asunto = $data['asunto'];
            $emailDestino = $data['email'];
            $empresa = NOMBRE_REMITENTE;
            $remitente = EMAIL_REMITENTE;
            $emailCopia = !empty($data['emailCopia']) ? $data['emailCopia'] : "";
            //ENVIO DE CORREO
            $de = "MIME-Version: 1.0\r\n";
            $de .= "Content-type: text/html; charset=UTF-8\r\n";
            $de .= "From: {$empresa} <{$remitente}>\r\n";
            $de .= "Bcc: $emailCopia\r\n";
            ob_start();
            require_once("Views/Template/Email/".$template.".php");
            $mensaje = ob_get_clean();
            $send = mail($emailDestino, $asunto, $mensaje, $de);
            return $send;
        }else{
           //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);
            ob_start();
            require_once("Views/Template/Email/".$template.".php");
            $mensaje = ob_get_clean();

            try {
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'toolsfordeveloper@gmail.com';          //SMTP username
                $mail->Password   = '@dmin08a';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('toolsfordeveloper@gmail.com', 'Servidor Local');
                $mail->addAddress($data['email']);     //Add a recipient
                if(!empty($data['emailCopia'])){
                    $mail->addBCC($data['emailCopia']);
                }
                $mail->CharSet = 'UTF-8';
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $data['asunto'];
                $mail->Body    = $mensaje;
                
                $mail->send();
                return true;
            } catch (Exception $e) {
                return false;
            } 
        }
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

    /**
     * Formatea un número como moneda
     * @param float $amount El monto a formatear
     * @param int $decimals Número de decimales (por defecto 2)
     * @return string El número formateado
     */
    function formatMoney($amount, $decimals = 2) {
        return number_format(floatval($amount), $decimals, '.', ',');
    }

    /**
     * Valida y limpia una URL
     * @param string $url URL a validar
     * @return string|false URL limpia o false si es inválida
     */
    function validateUrl($url) {
        $url = filter_var(trim($url), FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : false;
    }

    /**
     * Genera un token CSRF seguro
     * @return string Token CSRF
     */
    function generateCSRFToken() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }

    /**
     * Valida un token CSRF
     * @param string $token Token a validar
     * @param int $maxAge Edad máxima del token en segundos (por defecto 3600)
     * @return bool True si es válido
     */
    function validateCSRFToken($token, $maxAge = 3600) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Verificar que el token no haya expirado
        if ((time() - $_SESSION['csrf_token_time']) > $maxAge) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Cifra un valor de forma segura con IV
     * @param string $data Datos a cifrar
     * @return string Datos cifrados en base64
     */
    function encryptData($data) {
        // Usar una clave derivada de la configuración de la aplicación
        $key = hash('sha256', DB_NAME . DB_USER . BASE_URL, true);
        $method = 'AES-256-CBC';
        
        if (!extension_loaded('openssl')) {
            throw new Exception('OpenSSL extension is required for encryption');
        }
        
        $iv = random_bytes(openssl_cipher_iv_length($method));
        $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new Exception('Encryption failed');
        }
        
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * Descifra un valor cifrado con IV
     * @param string $data Datos cifrados en base64
     * @return string Datos descifrados
     */
    function decryptData($data) {
        // Usar la misma clave derivada
        $key = hash('sha256', DB_NAME . DB_USER . BASE_URL, true);
        $method = 'AES-256-CBC';
        
        if (!extension_loaded('openssl')) {
            throw new Exception('OpenSSL extension is required for decryption');
        }
        
        $decoded = base64_decode($data);
        if ($decoded === false) {
            throw new Exception('Invalid base64 data');
        }
        
        $parts = explode('::', $decoded, 2);
        if (count($parts) !== 2) {
            throw new Exception('Invalid encrypted data format');
        }
        
        list($encrypted_data, $iv) = $parts;
        $decrypted = openssl_decrypt($encrypted_data, $method, $key, 0, $iv);
        
        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }
        
        return $decrypted;
    }