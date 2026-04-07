<?php
// fungsi untuk mengecek apakah user sudah login
function is_login() {
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        return true;
    }
    return false;
}

// fungsi untuk mengecek level user
function get_user_level() {
    if (isset($_SESSION['level'])) {
        return $_SESSION['level'];
    }
    return null;
}

// fungsi untuk mendapatkan data user yang sedang login
function get_user_data() {
    if (is_login()) {
        return [
            'id_user' => $_SESSION['id_user'],
            'username' => $_SESSION['username'],
            'nama_lengkap' => $_SESSION['nama_lengkap'],
            'email' => $_SESSION['email'],
            'level' => $_SESSION['level']
        ];
    }
    return null;
}

// fungsi untuk mengecek apakah user adalah admin
function is_admin() {
    return get_user_level() === 'admin';
}

// fungsi untuk memaksa user login
function require_login() {
    if (!is_login()) {
        header("location: login.php?error=not_logged_in");
        exit();
    }
}

// fungsi untuk memaksa user harus admin
function require_admin() {
    require_login();
    if (!is_admin()) {
        // jika bukan admin, redirect ke dashboard dengan pesan error
        header("location: main.php?module=dashboard&error=access_denied");
        exit();
    }
}

// fungsi untuk logout
function logout() {
    // hapus semua session
    session_unset();
    session_destroy();
    
    // hapus cookie jika ada
    if (isset($_COOKIE['email'])) {
        setcookie('email', '', time() - 3600, '/');
    }
    
    // alihkan ke halaman login
    header("location: login.php");
    exit();
}

// fungsi untuk membersihkan input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// fungsi untuk generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// fungsi untuk validasi CSRF token
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// fungsi untuk mengecek password strength
function is_password_strong($password) {
    // minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka
    if (strlen($password) < 8) {
        return false;
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    return true;
}

// fungsi untuk log activity
function log_activity($activity, $description = '') {
    // bisa dikembangkan untuk menyimpan log ke database
    $user_data = get_user_data();
    $log_data = [
        'user_id' => $user_data['id_user'],
        'username' => $user_data['username'],
        'activity' => $activity,
        'description' => $description,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // untuk sementara bisa disimpan di session atau file log
    // bisa dikembangkan untuk disimpan di database
    return $log_data;
}
?>

