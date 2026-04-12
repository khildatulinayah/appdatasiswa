<?php
// panggil file database dan helper autentikasi
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helper/auth_helper.php";

session_start();
$base_url = get_base_url();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = mysqli_real_escape_string($mysqli, trim($_POST["token"]));
    $password = mysqli_real_escape_string($mysqli, trim($_POST["password"]));
    $password_confirm = mysqli_real_escape_string($mysqli, trim($_POST["password_confirm"]));

    if (empty($token) || empty($password) || empty($password_confirm)) {
        header("location: {$base_url}/reset_password.php?token=" . urlencode($token) . "&error=empty");
        exit();
    }

    if ($password !== $password_confirm) {
        header("location: {$base_url}/reset_password.php?token=" . urlencode($token) . "&error=password_mismatch");
        exit();
    }

    if (!is_password_strong($password)) {
        header("location: {$base_url}/reset_password.php?token=" . urlencode($token) . "&error=password_weak");
        exit();
    }

    $query = "SELECT user_id FROM password_resets WHERE token = '$token' AND expires_at >= NOW() LIMIT 1";
    $result = mysqli_query($mysqli, $query);

    if (!$result || mysqli_num_rows($result) !== 1) {
        header("location: {$base_url}/reset_password.php?error=invalid_token");
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    $user_id = (int)$row['user_id'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $update = "UPDATE user SET password = '$password_hash' WHERE id_user = $user_id";
    if (mysqli_query($mysqli, $update)) {
        mysqli_query($mysqli, "DELETE FROM password_resets WHERE user_id = $user_id");
        header("location: {$base_url}/login.php?success=reset");
        exit();
    }

    header("location: {$base_url}/reset_password.php?token=" . urlencode($token) . "&error=database");
    exit();
}

header("location: {$base_url}/reset_password.php?error=empty");
exit();
