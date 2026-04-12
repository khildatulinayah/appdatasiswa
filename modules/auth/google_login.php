<?php
// Google OAuth Login Handler
// File ini menghandle redirect ke Google OAuth2

session_start();

// Include required files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/google_oauth.php';
require_once __DIR__ . '/../../helper/auth_helper.php';

// Check if Google credentials are configured
if (GOOGLE_CLIENT_ID === 'YOUR_GOOGLE_CLIENT_ID_HERE' || GOOGLE_CLIENT_SECRET === 'YOUR_GOOGLE_CLIENT_SECRET_HERE') {
    die('
    <div class="alert alert-danger">
        <h4>Konfigurasi Google OAuth2 Belum Lengkap</h4>
        <p>Silakan setup Google OAuth2 credentials terlebih dahulu:</p>
        <ol>
            <li>Buka <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
            <li>Buat project baru atau pilih project yang sudah ada</li>
            <li>Enable Google+ API dan Google OAuth2 API</li>
            <li>Buat OAuth2 Client ID dan Client Secret</li>
            <li>Update file config/google_oauth.php dengan credentials Anda</li>
            <li>Set redirect URI: http://localhost/app-siswa1/modules/auth/google_callback.php</li>
        </ol>
        <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
    </div>
    ');
}

try {
    // Generate Google OAuth URL
    $auth_url = getGoogleAuthUrl();
    
    // Store session state for security
    $_SESSION['google_oauth_state'] = bin2hex(random_bytes(16));
    
    // Redirect to Google OAuth
    header('Location: ' . $auth_url);
    exit();
    
} catch (Exception $e) {
    // Log error
    error_log("Google OAuth Login Error: " . $e->getMessage());
    
    // Redirect to login with error
    $base_url = get_base_url();
    header('Location: ' . $base_url . '/login.php?error=google_auth_failed');
    exit();
}

?>
