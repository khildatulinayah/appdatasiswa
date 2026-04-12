<?php
// Google OAuth Callback Handler
// File ini menghandle callback dari Google OAuth2

session_start();

// Include required files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/google_oauth.php';
require_once __DIR__ . '/../../helper/auth_helper.php';

// Get base URL for redirects
$base_url = get_base_url();

// Check if this is register or login flow
$is_register = isset($_SESSION['google_oauth_state']) && $_SESSION['google_oauth_state'] === 'register';
$redirect_page = $is_register ? 'register.php' : 'login.php';

// Check if user denied access
if (isset($_GET['error']) && $_GET['error'] === 'access_denied') {
    header('Location: ' . $base_url . '/' . $redirect_page . '?error=google_denied');
    exit();
}

// Check if authorization code is present
if (!isset($_GET['code'])) {
    header('Location: ' . $base_url . '/' . $redirect_page . '?error=google_no_code');
    exit();
}

try {
    // Create Google Client
    $client = createGoogleClient();
    
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error'])) {
        throw new Exception('Token error: ' . $token['error']);
    }
    
    // Get user info from Google
    $user_info = getGoogleUserInfo($token['access_token']);
    
    if (!$user_info) {
        throw new Exception('Failed to get user info from Google');
    }
    
    // Check if email is verified
    if (!$user_info['verified_email']) {
        throw new Exception('Email is not verified');
    }
    
    // Check if user already exists in database
    $google_id = $user_info['id'];
    $email = $user_info['email'];
    $name = $user_info['name'];
    $picture = $user_info['picture'];
    
    // Check existing user by Google ID
    $query = "SELECT * FROM user WHERE google_id = ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $google_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        // User exists with Google ID - login
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['level'] = $user['level'];
        $_SESSION['auth_method'] = 'google';
        $_SESSION['google_picture'] = $user['google_picture'];
        
        // Update last login and picture
        $update_query = "UPDATE user SET updated_at = NOW(), google_picture = ? WHERE id_user = ?";
        $update_stmt = mysqli_prepare($mysqli, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $picture, $user['id_user']);
        mysqli_stmt_execute($update_stmt);
        
    } else {
        // Check if user exists with same email
        $email_query = "SELECT * FROM user WHERE email = ?";
        $email_stmt = mysqli_prepare($mysqli, $email_query);
        mysqli_stmt_bind_param($email_stmt, "s", $email);
        mysqli_stmt_execute($email_stmt);
        $email_result = mysqli_stmt_get_result($email_stmt);
        
        if ($existing_user = mysqli_fetch_assoc($email_result)) {
            // User exists with same email - link Google account
            $link_query = "UPDATE user SET google_id = ?, google_picture = ?, auth_method = 'both' WHERE id_user = ?";
            $link_stmt = mysqli_prepare($mysqli, $link_query);
            mysqli_stmt_bind_param($link_stmt, "ssi", $google_id, $picture, $existing_user['id_user']);
            mysqli_stmt_execute($link_stmt);
            
            // Login user
            $_SESSION['id_user'] = $existing_user['id_user'];
            $_SESSION['username'] = $existing_user['username'];
            $_SESSION['nama_lengkap'] = $existing_user['nama_lengkap'];
            $_SESSION['email'] = $existing_user['email'];
            $_SESSION['level'] = $existing_user['level'];
            $_SESSION['auth_method'] = 'google';
            $_SESSION['google_picture'] = $picture;
            
        } else {
        // Create new user
        $username = 'google_' . substr($google_id, 0, 10);
        $password = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT); // Random password
        $level = 'user'; // Default level for Google users
        
        $insert_query = "INSERT INTO user (username, password, nama_lengkap, email, google_id, google_picture, auth_method, level) 
                         VALUES (?, ?, ?, ?, ?, ?, 'google', ?)";
        $insert_stmt = mysqli_prepare($mysqli, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "sssssss", $username, $password, $name, $email, $google_id, $picture, $level);
        mysqli_stmt_execute($insert_stmt);
        
        // Get new user ID
        $user_id = mysqli_insert_id($mysqli);
        
        // Login new user
        $_SESSION['id_user'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = $level;
        $_SESSION['auth_method'] = 'google';
        $_SESSION['google_picture'] = $picture;
    }
}

// Clear session state
unset($_SESSION['google_oauth_state']);

    // Redirect to dashboard
    header('Location: ' . $base_url . '/main.php');
    exit();
    
} catch (Exception $e) {
    // Log error with more details
    $error_details = "Google OAuth Callback Error: " . $e->getMessage() . 
                  " in " . $e->getFile() . ":" . $e->getLine() . 
                  " Stack: " . $e->getTraceAsString();
    error_log($error_details);
    
    // For debugging, show error details (remove in production)
    if (strpos($base_url, 'localhost') !== false) {
        echo "<div style='padding: 20px; font-family: Arial;'>";
        echo "<h3>Google OAuth Debug Info</h3>";
        echo "<pre>" . htmlspecialchars($error_details) . "</pre>";
        echo "<p><a href='" . $base_url . "/" . $redirect_page . "'>Kembali</a></p>";
        echo "</div>";
        exit();
    }
    
    // Redirect to login with error
    header('Location: ' . $base_url . '/' . $redirect_page . '?error=google_callback_failed');
    exit();
}

?>
