<?php
// Google OAuth2 Configuration
// Pastikan Anda sudah setup Google Cloud Console dan mendapatkan Client ID & Client Secret

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        return false;
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
    return true;
}

// Load .env file
loadEnv(__DIR__ . '/../.env');

// Google OAuth2 Credentials
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
define('GOOGLE_REDIRECT_URI', $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost/app-siswa1/modules/auth/google_callback.php');

// Get base URL for dynamic redirects
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    return $protocol . $host . $path;
}

// Scopes yang dibutuhkan
define('GOOGLE_SCOPES', [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile'
]);

// Create Google Client instance
function createGoogleClient() {
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->setScopes(GOOGLE_SCOPES);
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    
    return $client;
}

// Get Google OAuth URL
function getGoogleAuthUrl() {
    $client = createGoogleClient();
    return $client->createAuthUrl();
}

// Validate Google ID Token
function validateGoogleToken($id_token) {
    $client = createGoogleClient();
    
    try {
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            return $payload;
        }
    } catch (Exception $e) {
        error_log("Google Token Validation Error: " . $e->getMessage());
    }
    
    return false;
}

// Get User Info from Google
function getGoogleUserInfo($access_token) {
    $client = createGoogleClient();
    $client->setAccessToken($access_token);
    
    $oauth_service = new Google_Service_Oauth2($client);
    
    try {
        $user_info = $oauth_service->userinfo->get();
        return [
            'id' => $user_info->getId(),
            'email' => $user_info->getEmail(),
            'name' => $user_info->getName(),
            'picture' => $user_info->getPicture(),
            'verified_email' => $user_info->getVerifiedEmail()
        ];
    } catch (Exception $e) {
        error_log("Google User Info Error: " . $e->getMessage());
        return false;
    }
}

?>
