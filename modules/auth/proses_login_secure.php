<?php
// Proses login user dengan keamanan brute force protection
session_start();

// Include required files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helper/auth_helper.php';
require_once __DIR__ . '/../../helper/security_helper.php';
require_once __DIR__ . '/../../helper/captcha_helper.php';

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login.php?error=invalid_method');
    exit();
}

// Rate limiting check
if (!check_rate_limit('login', 10, 300)) { // 10 attempts per 5 minutes
    header('Location: ../../login.php?error=rate_limit_exceeded');
    exit();
}

// Check if IP is blocked
if (is_ip_blocked()) {
    header('Location: ../../login.php?error=ip_blocked');
    exit();
}

// Validate CSRF token
if (!validate_secure_csrf_token($_POST['csrf_token'] ?? '')) {
    header('Location: ../../login.php?error=csrf_invalid');
    exit();
}

// Get form data
$username = clean_input($_POST["username"] ?? '');
$password = $_POST["password"] ?? '';
$captcha_answer = $_POST["captcha_answer"] ?? '';
$remember = isset($_POST["remember"]);

// Check if CAPTCHA should be shown and validate it
if (should_show_captcha()) {
    if (!validate_captcha($captcha_answer)) {
        log_login_attempt($username, false, 'local');
        header('Location: ../../login.php?error=captcha_invalid');
        exit();
    }
}

// Check if account is locked
if (is_account_locked($username)) {
    header('Location: ../../login.php?error=account_locked');
    exit();
}

// Validate input
if (empty($username) || empty($password)) {
    log_login_attempt($username, false, 'local');
    header('Location: ../../login.php?error=empty');
    exit();
}

// Query user dengan prepared statement untuk security
$query = "SELECT * FROM user WHERE username = ? OR email = ?";
$stmt = mysqli_prepare($mysqli, $query);
mysqli_stmt_bind_param($stmt, "ss", $username, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    
    // Verifikasi password
    if (password_verify($password, $user["password"])) {
        // Login successful
        log_login_attempt($username, true, 'local');
        
        // Clean up any existing failed attempts
        $cleanup_query = "DELETE FROM login_attempts WHERE username = ? OR ip_address = ?";
        $cleanup_stmt = mysqli_prepare($mysqli, $cleanup_query);
        mysqli_stmt_bind_param($cleanup_stmt, "ss", $username, get_real_ip());
        mysqli_stmt_execute($cleanup_stmt);
        
        // Create session
        $_SESSION["id_user"] = $user["id_user"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["nama_lengkap"] = $user["nama_lengkap"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["level"] = $user["level"];
        $_SESSION["login"] = true;
        $_SESSION["auth_method"] = 'local';
        
        // Handle remember me
        if ($remember) {
            setcookie("username", $user["username"], time() + (30 * 24 * 60 * 60), "/", "", true, true);
        } elseif (isset($_COOKIE["username"])) {
            setcookie("username", "", time() - 3600, "/", "", true, true);
        }
        
        // Log successful login event
        log_security_event('login_success', 'low', get_real_ip(), $user["id_user"], "Successful login for: $username");
        
        // Redirect to dashboard
        header('Location: ../../main.php?module=dashboard');
        exit();
        
    } else {
        // Password salah
        log_login_attempt($username, false, 'local');
        
        // Check for brute force patterns (ini akan auto-lock/block)
        check_brute_force_patterns(get_real_ip(), $username, 'local');
        
        header('Location: ../../login.php?error=invalid');
        exit();
    }
} else {
    // Username tidak ditemukan
    log_login_attempt($username, false, 'local');
    
    // Check for brute force patterns
    check_brute_force_patterns(get_real_ip(), $username, 'local');
    
    header('Location: ../../login.php?error=invalid');
    exit();
}

?>
