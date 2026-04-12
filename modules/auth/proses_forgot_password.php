<?php
// panggil file database dan helper autentikasi
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helper/auth_helper.php";
require_once __DIR__ . "/../../vendor/PHPMailer/src/PHPMailer.php";

session_start();
$base_url = get_base_url();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = mysqli_real_escape_string($mysqli, trim($_POST["email"]));

    if (empty($email)) {
        header("location: {$base_url}/forgot_password.php?error=empty");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: {$base_url}/forgot_password.php?error=invalid_email");
        exit();
    }

    $query = "SELECT id_user, email FROM user WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($mysqli, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + 3600);

        // buat tabel password_resets jika belum ada
        $create_table = "CREATE TABLE IF NOT EXISTS password_resets (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            email VARCHAR(100) NOT NULL,
            token VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY token_unique (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        mysqli_query($mysqli, $create_table);

        $delete_old = "DELETE FROM password_resets WHERE user_id = {$user['id_user']} OR email = '{$user['email']}'";
        mysqli_query($mysqli, $delete_old);

        $safe_token = mysqli_real_escape_string($mysqli, $token);
        $insert = "INSERT INTO password_resets (user_id, email, token, expires_at) 
            VALUES ({$user['id_user']}, '{$user['email']}', '$safe_token', '$expires_at')";
        mysqli_query($mysqli, $insert);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $reset_link = $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_url . '/reset_password.php?token=' . $token;

        // Gunakan PHPMailer untuk pengiriman email
        $mail = new PHPMailer();
        
        $subject = 'Reset Password Aplikasi Siswa';
        $message = "Halo,\n\nSilakan klik tautan di bawah ini untuk mengatur ulang password Anda:\n$reset_link\n\nTautan ini berlaku selama 1 jam.\n\nJika Anda tidak meminta pengaturan ulang password, abaikan pesan ini.";
        
        $mail->setSubject($subject);
        $mail->setBody($message);
        $mail->addAddress($email);
        
        $sent = $mail->send();
        
        if ($sent) {
            header("location: {$base_url}/forgot_password.php?status=sent");
            exit();
        }
        
        // Jika email gagal dikirim, tampilkan error dan fallback link
        $error = $mail->getErrorInfo();
        error_log("Email sending failed: " . $error);
        
        // fallback jika email tidak dapat dikirim
        header("location: {$base_url}/forgot_password.php?status=sent&link=" . urlencode($reset_link));
        exit();
    }

    // jangan berikan informasi apakah email terdaftar atau tidak
    header("location: {$base_url}/forgot_password.php?status=sent");
    exit();
}

header("location: {$base_url}/forgot_password.php?error=empty");
exit();
