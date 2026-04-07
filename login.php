<?php
// panggil helper auth untuk cek sesi
require_once "helper/auth_helper.php";

// start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// jika sudah login, alihkan langsung ke dashboard
if (is_login()) {
    header('location: main.php?module=dashboard');
    exit();
}

// Paksa module login agar auth layout menampilkan halaman login
$_GET['module'] = 'form_login';

// gunakan layout auth
include "auth_layout.php";
?>

