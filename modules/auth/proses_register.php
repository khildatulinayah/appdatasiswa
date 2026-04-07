<?php
// panggil file database untuk koneksi
require_once "../../config/database.php";

// start session
session_start();

// cek apakah form sudah di submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ambil data dari form
    $nama_lengkap = mysqli_real_escape_string($mysqli, trim($_POST["nama_lengkap"]));
    $username = mysqli_real_escape_string($mysqli, trim($_POST["username"]));
    $email = mysqli_real_escape_string($mysqli, trim($_POST["email"]));
    $password = mysqli_real_escape_string($mysqli, trim($_POST["password"]));
    $password_confirm = mysqli_real_escape_string($mysqli, trim($_POST["password_confirm"]));
    
    // validasi input
    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        header("location: /register.php?error=empty");
        exit();
    }
    
    // validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: /register.php?error=invalid_email");
        exit();
    }
    
    // validasi panjang password
    if (strlen($password) < 6) {
        header("location: /register.php?error=password_short");
        exit();
    }
    
    // validasi konfirmasi password
    if ($password !== $password_confirm) {
        header("location: /register.php?error=password_mismatch");
        exit();
    }
    
    // cek apakah username sudah ada
    $query_username = "SELECT username FROM user WHERE username = '$username'";
    $result_username = mysqli_query($mysqli, $query_username);
    
    if (mysqli_num_rows($result_username) > 0) {
        header("location: /register.php?error=username_exists");
        exit();
    }
    
    // cek apakah email sudah ada
    $query_email = "SELECT email FROM user WHERE email = '$email'";
    $result_email = mysqli_query($mysqli, $query_email);
    
    if (mysqli_num_rows($result_email) > 0) {
        header("location: /register.php?error=email_exists");
        exit();
    }
    
    // hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // query insert user baru sebagai role user
    $query = "INSERT INTO user (username, password, nama_lengkap, email, level) 
              VALUES ('$username', '$password_hash', '$nama_lengkap', '$email', 'user')";
    
    // eksekusi query
    if (mysqli_query($mysqli, $query)) {
        // registrasi berhasil, alihkan ke halaman login dengan pesan sukses
        header("location: /login.php?success=1");
        exit();
    } else {
        // registrasi gagal
        header("location: /register.php?error=database");
        exit();
    }
} else {
    // form belum di submit
    header("location: /register.php?error=empty");
    exit();
}
?>

