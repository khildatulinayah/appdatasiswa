<?php
// panggil file "database.php" untuk koneksi ke database
require_once "config/database.php";
// panggil file "fungsi_tanggal_indo.php" untuk membuat format tanggal indonesia
require_once "helper/fungsi_tanggal_indo.php";
// panggil file "auth_helper.php" untuk fungsi autentikasi
require_once "helper/auth_helper.php";

// start session
session_start();

// cek apakah user sudah login
if (is_login()) {
    header('location: main.php?module=dashboard');
    exit();
}

// set module ke form_register
$module = 'form_register';

// gunakan layout auth
include "auth_layout.php";
?>

