<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helper/auth_helper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_admin();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    $nama_lengkap = mysqli_real_escape_string($mysqli, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
    $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));
    $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));
    $password_confirm = mysqli_real_escape_string($mysqli, trim($_POST['password_confirm']));
    $level = mysqli_real_escape_string($mysqli, $_POST['level']);

    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password) || empty($password_confirm) || !in_array($level, ['admin', 'user'])) {
        header('location: ../../main.php?module=form_entri_user&error=empty');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: ../../main.php?module=form_entri_user&error=invalid_email');
        exit();
    }

    if ($password !== $password_confirm) {
        header('location: ../../main.php?module=form_entri_user&error=password_mismatch');
        exit();
    }

    if (!is_password_strong($password) || strlen($password) > 32) {
        header('location: ../../main.php?module=form_entri_user&error=password_weak');
        exit();
    }

    $query_username = "SELECT id_user FROM user WHERE username = '$username'";
    $result_username = mysqli_query($mysqli, $query_username);
    if (mysqli_num_rows($result_username) > 0) {
        header('location: ../../main.php?module=form_entri_user&error=username_exists');
        exit();
    }

    $query_email = "SELECT id_user FROM user WHERE email = '$email'";
    $result_email = mysqli_query($mysqli, $query_email);
    if (mysqli_num_rows($result_email) > 0) {
        header('location: ../../main.php?module=form_entri_user&error=email_exists');
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO user (username, password, nama_lengkap, email, level) VALUES ('$username', '$password_hash', '$nama_lengkap', '$email', '$level')";

    if (mysqli_query($mysqli, $query)) {
        header('location: ../../main.php?module=user&pesan=2');
        exit();
    } else {
        header('location: ../../main.php?module=form_entri_user&error=database');
        exit();
    }
}

header('location: ../../main.php?module=user');
exit();
