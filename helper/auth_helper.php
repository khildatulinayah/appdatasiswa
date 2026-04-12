<?php
// fungsi untuk mengecek apakah user sudah login
function is_login() {
    // Support both old and new session structure for backward compatibility
    if ((isset($_SESSION['login']) && $_SESSION['login'] === true) || 
        (isset($_SESSION['id_user']) && !empty($_SESSION['id_user']))) {
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
            'level' => $_SESSION['level'],
            'auth_method' => isset($_SESSION['auth_method']) ? $_SESSION['auth_method'] : 'local',
            'google_picture' => isset($_SESSION['google_picture']) ? $_SESSION['google_picture'] : null
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
    // minimal 8 karakter, maksimal 32 karakter, mengandung huruf besar, huruf kecil, dan angka
    if (strlen($password) < 8 || strlen($password) > 32) {
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

// fungsi untuk mendapatkan base URL aplikasi
function get_base_url() {
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $script = trim($script, '/');
    if ($script === '') {
        return '';
    }

    $parts = explode('/', $script);

    // jika aplikasi berjalan di folder root
    if (count($parts) === 1) {
        return '';
    }

    // jika script berada di dalam folder modules/auth, kembalikan root aplikasi
    if (count($parts) >= 3 && $parts[count($parts) - 3] === 'modules') {
        $rootParts = array_slice($parts, 0, count($parts) - 3);
        $path = '/' . implode('/', $rootParts);
        return $path === '/' ? '' : $path;
    }

    // jika script berada langsung di folder aplikasi, kembalikan folder aplikasi
    $rootParts = array_slice($parts, 0, count($parts) - 1);
    $path = '/' . implode('/', $rootParts);
    return $path === '/' ? '' : $path;
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
        'timestamp' => date('Y-m-d H:i:s'),
        'auth_method' => $user_data['auth_method']
    ];
    
    // untuk sementara bisa disimpan di session atau file log
    // bisa dikembangkan untuk disimpan di database
    return $log_data;
}

// fungsi untuk mengecek apakah user login via Google
function is_google_login() {
    $user_data = get_user_data();
    return $user_data && $user_data['auth_method'] === 'google';
}

// fungsi untuk mengecek apakah user login via local (username/password)
function is_local_login() {
    $user_data = get_user_data();
    return $user_data && $user_data['auth_method'] === 'local';
}

// fungsi untuk mendapatkan URL foto profil user
function get_user_picture($default_size = 40) {
    $user_data = get_user_data();
    
    if ($user_data && $user_data['google_picture']) {
        // Jika user login via Google dan ada foto Google
        return $user_data['google_picture'] . '?sz=' . $default_size;
    }
    
    // Default avatar untuk local login atau jika tidak ada foto Google
    return "https://ui-avatars.com/api/?name=" . urlencode($user_data['nama_lengkap']) . "&size=" . $default_size . "&background=007bff&color=fff";
}
?>

