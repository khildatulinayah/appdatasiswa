<?php
// cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// panggil helper auth
require_once __DIR__ . '/../../helper/auth_helper.php';

// lakukan logout
logout();
?>


