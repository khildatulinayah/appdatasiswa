<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helper/auth_helper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_admin();

if (!isset($_GET['id'])) {
    header('location: ../../main.php?module=user&pesan=0');
    exit();
}

$id_user = mysqli_real_escape_string($mysqli, $_GET['id']);
$current_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

if ($current_user === $id_user) {
    header('location: ../../main.php?module=user&pesan=4');
    exit();
}

$delete = mysqli_query($mysqli, "DELETE FROM user WHERE id_user='$id_user'")
    or die('Ada kesalahan pada query hapus user : ' . mysqli_error($mysqli));

if ($delete && mysqli_affected_rows($mysqli) > 0) {
    header('location: ../../main.php?module=user&pesan=3');
    exit();
} else {
    header('location: ../../main.php?module=user&pesan=0');
    exit();
}
