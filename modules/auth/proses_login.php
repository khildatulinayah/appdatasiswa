<?php
// panggil file database untuk koneksi
require_once "../../config/database.php";
require_once "../../helper/auth_helper.php";

// start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// cek apakah form sudah di submit
if (isset($_POST["email"]) && isset($_POST["password"])) {
    // ambil data dari form
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);
    
    // cek apakah email dan password tidak kosong
    if (!empty($email) && !empty($password)) {
        // query untuk mencari user berdasarkan email
        $email_safe = mysqli_real_escape_string($mysqli, $email);
        $query = "SELECT * FROM user WHERE email = '$email_safe'";
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
                
                // cek remember me (hanya simpan email)
                if (isset($_POST["remember"])) {
                    setcookie("email", $email, time() + (30 * 24 * 60 * 60), "/");
                } elseif (isset($_COOKIE["email"])) {
                    setcookie("email", "", time() - 3600, "/");
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

