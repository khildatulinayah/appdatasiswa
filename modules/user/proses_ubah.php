<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../helper/auth_helper.php";
require_admin();
require_once __DIR__ . "/../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    $id_user = mysqli_real_escape_string($mysqli, $_POST['id_user']);
    $nama_lengkap = mysqli_real_escape_string($mysqli, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
    $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));
    $level = mysqli_real_escape_string($mysqli, $_POST['level']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password_confirm = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';

    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($level) || !in_array($level, ['admin', 'user'])) {
        header('location: ../../main.php?module=user&pesan=0');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: ../../main.php?module=user&pesan=0');
        exit();
    }

    // cek username lain
    $result_username = mysqli_query($mysqli, "SELECT id_user FROM user WHERE username = '$username' AND id_user <> '$id_user'")
        or die('Ada kesalahan pada query cek username : ' . mysqli_error($mysqli));
    if (mysqli_num_rows($result_username) > 0) {
        header('location: ../../main.php?module=user&pesan=0');
        exit();
    }

    // cek email lain
    $result_email = mysqli_query($mysqli, "SELECT id_user FROM user WHERE email = '$email' AND id_user <> '$id_user'")
        or die('Ada kesalahan pada query cek email : ' . mysqli_error($mysqli));
    if (mysqli_num_rows($result_email) > 0) {
        header('location: ../../main.php?module=user&pesan=0');
        exit();
    }

    $password_sql = '';
    if ($password !== '' || $password_confirm !== '') {
        if ($password !== $password_confirm || !is_password_strong($password) || strlen($password) > 32) {
            header('location: ../../main.php?module=user&pesan=0');
            exit();
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_sql = ", password='$password_hash'";
    }

    $update = mysqli_query($mysqli, "UPDATE user SET nama_lengkap='$nama_lengkap', username='$username', email='$email', level='$level'$password_sql WHERE id_user='$id_user'")
        or die('Ada kesalahan pada query update user : ' . mysqli_error($mysqli));

    if ($update) {
        header('location: ../../main.php?module=user&pesan=1');
        exit();
    } else {
        header('location: ../../main.php?module=user&pesan=0');
        exit();
    }
}

header('location: ../../main.php?module=user');
exit();

