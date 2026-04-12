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
    header('Location: ../login.php?error=invalid_method');
    exit();
}

// Rate limiting check
if (!check_rate_limit('login', 10, 300)) { // 10 attempts per 5 minutes
    header('Location: ../login.php?error=rate_limit_exceeded');
    exit();
}

// Check if IP is blocked
if (is_ip_blocked()) {
    header('Location: ../login.php?error=ip_blocked');
    exit();
}

// buat tabel login_attempts jika belum ada
$create_table = "CREATE TABLE IF NOT EXISTS login_attempts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempt_count INT(11) NOT NULL DEFAULT 1,
    locked_until DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_username (username),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
mysqli_query($mysqli, $create_table);

// fungsi untuk mendapat IP pengguna
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// cek apakah form sudah di submit
if (isset($_POST["username"]) && isset($_POST["password"])) {
    // ambil data dari form
    $username = clean_input($_POST["username"]);
    $password = clean_input($_POST["password"]);
    $client_ip = get_client_ip();
    
    // cek apakah akun terblokir
    $username_safe = mysqli_real_escape_string($mysqli, $username);
    $check_lock = "SELECT * FROM login_attempts WHERE username = '$username_safe' AND locked_until >= NOW()";
    $lock_result = mysqli_query($mysqli, $check_lock);
    
    if (mysqli_num_rows($lock_result) > 0) {
        header("location: ../../login.php?error=account_locked");
        exit();
    }
    
    // cek apakah username dan password tidak kosong
    if (!empty($username) && !empty($password)) {
        // query untuk mencari user berdasarkan username
        $query = "SELECT * FROM user WHERE username = '$username_safe'";
        $result = mysqli_query($mysqli, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // verifikasi password
            if (password_verify($password, $user["password"])) {
                // password benar, hapus login attempts
                $delete_attempts = "DELETE FROM login_attempts WHERE username = '$username_safe'";
                mysqli_query($mysqli, $delete_attempts);
                
                // buat session
                $_SESSION["id_user"] = $user["id_user"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["nama_lengkap"] = $user["nama_lengkap"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["level"] = $user["level"];
                $_SESSION["login"] = true;
                
                // cek remember me (hanya simpan username)
                if (isset($_POST["remember"])) {
                    setcookie("username", $user["username"], time() + (30 * 24 * 60 * 60), "/");
                } elseif (isset($_COOKIE["username"])) {
                    setcookie("username", "", time() - 3600, "/");
                }
                
                // alihkan ke halaman dashboard
                header("location: ../../main.php?module=dashboard");
                exit();
            } else {
                // password salah, tambah attempt count
                $check_attempt = "SELECT * FROM login_attempts WHERE username = '$username_safe'";
                $attempt_result = mysqli_query($mysqli, $check_attempt);
                
                if (mysqli_num_rows($attempt_result) > 0) {
                    $attempt = mysqli_fetch_assoc($attempt_result);
                    $new_count = $attempt["attempt_count"] + 1;
                    
                    if ($new_count >= 5) {
                        // blokir akun selama 15 menit
                        $locked_until = date('Y-m-d H:i:s', time() + (15 * 60));
                        $update = "UPDATE login_attempts SET attempt_count = $new_count, locked_until = '$locked_until' WHERE username = '$username_safe'";
                        mysqli_query($mysqli, $update);
                        
                        header("location: ../../login.php?error=account_locked");
                        exit();
                    } else {
                        $update = "UPDATE login_attempts SET attempt_count = $new_count WHERE username = '$username_safe'";
                        mysqli_query($mysqli, $update);
                    }
                } else {
                    // record pertama
                    $insert = "INSERT INTO login_attempts (username, ip_address, attempt_count) VALUES ('$username_safe', '$client_ip', 1)";
                    mysqli_query($mysqli, $insert);
                }
                
                header("location: ../../login.php?error=invalid");
                exit();
            }
        } else {
            // username tidak ditemukan
            header("location: ../../login.php?error=invalid");
            exit();
        }
    } else {
        // email atau password kosong
        header("location: ../../login.php?error=empty");
        exit();
    }
} else {
    // form belum di submit
    header("location: ../../login.php?error=empty");
    exit();
}
?>


