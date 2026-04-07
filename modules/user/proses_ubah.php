<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../../helper/auth_helper.php";
require_admin();
require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    $id_user = mysqli_real_escape_string($mysqli, $_POST['id_user']);
    $level = mysqli_real_escape_string($mysqli, $_POST['level']);

    if (!in_array($level, ['admin', 'user'])) {
        header('location: ../../main.php?module=user&pesan=0');
        exit();
    }

    $update = mysqli_query($mysqli, "UPDATE user SET level='$level' WHERE id_user='$id_user'")
        or die('Ada kesalahan pada query update role : ' . mysqli_error($mysqli));

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
