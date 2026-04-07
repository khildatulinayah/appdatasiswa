<?php
// panggil file database untuk koneksi
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helper/auth_helper.php";

// start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// cek apakah form sudah di submit
if (isset($_POST["username"]) && isset($_POST["password"])) {
    // ambil data dari form
    $username = clean_input($_POST["username"]);
    $password = clean_input($_POST["password"]);
    
    // cek apakah username dan password tidak kosong
    if (!empty($username) && !empty($password)) {
        // query untuk mencari user berdasarkan username
        $username_safe = mysqli_real_escape_string($mysqli, $username);
        $query = "SELECT * FROM user WHERE username = '$username_safe'";
        $result = mysqli_query($mysqli, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // verifikasi password
            if (password_verify($password, $user["password"])) {
                // password benar, buat session
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
                // password salah
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


