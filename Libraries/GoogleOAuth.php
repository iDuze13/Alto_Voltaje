<?php
require_once __DIR__ . '/../Config/Google.php';

class GoogleOAuth {
    public static function getAuthUrl(?string $state = null): string {
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'scope' => 'email profile',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
        ];
        if ($state) { $params['state'] = $state; }
        return GOOGLE_AUTH_URL . '?' . http_build_query($params);
    }

    public static function exchangeCodeForToken(string $code): ?array {
        $data = [
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        if ($response === false) { curl_close($ch); return null; }
        curl_close($ch);
        return json_decode($response, true);
    }

    public static function fetchUserInfo(string $accessToken): ?array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_USER_INFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $response = curl_exec($ch);
        if ($response === false) { curl_close($ch); return null; }
        curl_close($ch);
        return json_decode($response, true);
    }
}
?>
