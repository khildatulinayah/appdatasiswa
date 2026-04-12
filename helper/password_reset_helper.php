<?php
// Password Reset Helper Functions with Email Verification Code System
// Sistem reset password yang aman dengan verifikasi email

require_once __DIR__ . '/simple_security_helper.php';

// Configuration
define('RESET_TOKEN_LENGTH', 64);
define('VERIFICATION_CODE_LENGTH', 6);
define('RESET_TOKEN_EXPIRY', 3600); // 1 hour
define('MAX_RESET_ATTEMPTS', 3); // Max attempts per hour
define('RESET_RATE_LIMIT', 300); // 5 minutes between requests

// Generate secure reset token
function generate_reset_token() {
    return bin2hex(random_bytes(RESET_TOKEN_LENGTH / 2));
}

// Generate 6-digit verification code
function generate_verification_code() {
    return str_pad(rand(0, 999999), VERIFICATION_CODE_LENGTH, '0', STR_PAD_LEFT);
}

// Check rate limiting for password reset
function check_password_reset_rate_limit($email, $ip) {
    // Database configuration
    $host = "localhost";
    $username = "root";
    $password = "nayah19";
    $database = "dbb";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check recent requests from this IP
        $recent_time = date('Y-m-d H:i:s', time() - RESET_RATE_LIMIT);
        $query = "SELECT COUNT(*) as count FROM password_reset_attempts 
                  WHERE ip_address = ? AND attempt_time > ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ip, $recent_time]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['count'] >= 2) { // Max 2 requests per 5 minutes
            return false;
        }
        
        // Check recent requests for this email
        $email_query = "SELECT COUNT(*) as count FROM password_reset_attempts 
                        WHERE email = ? AND attempt_time > ?";
        $email_stmt = $pdo->prepare($email_query);
        $email_stmt->execute([$email, $recent_time]);
        $email_row = $email_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($email_row['count'] >= 1) { // Max 1 request per email per 5 minutes
            return false;
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Database error in rate limiting: " . $e->getMessage());
        return true; // Allow request if database fails
    }
}

// Create password reset request
function create_password_reset_request($email) {
    // Database configuration
    $host = "localhost";
    $username = "root";
    $password = "nayah19";
    $database = "dbb";
    
    $ip = get_real_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check rate limiting
        if (!check_password_reset_rate_limit($email, $ip)) {
            log_security_event('password_reset_rate_limit', 'medium', $ip, null, "Rate limit exceeded for password reset: $email");
            return ['success' => false, 'message' => 'Too many reset requests. Please wait 5 minutes.'];
        }
        
        // Check if user exists
        $user_query = "SELECT id_user, username, nama_lengkap FROM user WHERE email = ?";
        $user_stmt = $pdo->prepare($user_query);
        $user_stmt->execute([$email]);
        $user_result = $user_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user_result) {
            // Log attempt even if user doesn't exist (security)
            log_password_reset_attempt($email, false, 'request', $ip);
            return ['success' => false, 'message' => 'If this email exists, a reset code has been sent.'];
        }
        
        $user = $user_result;
        
        // Clean old tokens for this email
        $cleanup_query = "DELETE FROM password_resets WHERE email = ? OR expires_at < NOW()";
        $cleanup_stmt = $pdo->prepare($cleanup_query);
        $cleanup_stmt->execute([$email]);
        
        // Generate new token and verification code
        $token = generate_reset_token();
        $verification_code = generate_verification_code();
        $expires_at = date('Y-m-d H:i:s', time() + RESET_TOKEN_EXPIRY);
        
        // Insert new reset request
        $insert_query = "INSERT INTO password_resets 
                        (email, token, verification_code, ip_address, user_agent, expires_at) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->execute([$email, $token, $verification_code, $ip, $user_agent, $expires_at]);
        
        // Log successful request
        log_password_reset_attempt($email, true, 'request', $ip);
        log_security_event('password_reset_requested', 'medium', $ip, $user['id_user'], "Password reset requested for: $email");
        
        return [
            'success' => true,
            'token' => $token,
            'verification_code' => $verification_code,
            'email' => $email,
            'user' => $user,
            'expires_at' => $expires_at
        ];
        
    } catch (PDOException $e) {
        error_log("Database error in create_password_reset_request: " . $e->getMessage());
        log_password_reset_attempt($email, false, 'request', $ip);
        return ['success' => false, 'message' => 'Failed to create reset request. Please try again.'];
    }
}

// Verify reset token and code
function verify_reset_request($token, $verification_code) {
    // Database configuration
    $host = "localhost";
    $username = "root";
    $password = "nayah19";
    $database = "dbb";
    
    $ip = get_real_ip();
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Find valid reset request (fix timezone issue)
        $current_time = date('Y-m-d H:i:s');
        $query = "SELECT * FROM password_resets 
                  WHERE token = ? AND expires_at > ? AND used_at IS NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$token, $current_time]);
        $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reset_request) {
            log_security_event('password_reset_invalid_token', 'medium', $ip, null, "Invalid reset token used");
            return ['success' => false, 'message' => 'Invalid or expired reset link.'];
        }
        
        // Check verification code
        if ($reset_request['verification_code'] !== $verification_code) {
            // Increment attempts
            $attempts_query = "UPDATE password_resets SET attempts_count = attempts_count + 1 
                              WHERE token = ?";
            $attempts_stmt = $pdo->prepare($attempts_query);
            $attempts_stmt->execute([$token]);
            
            // Check if too many attempts
            if ($reset_request['attempts_count'] + 1 >= MAX_RESET_ATTEMPTS) {
                // Mark as used to prevent further attempts
                $mark_used_query = "UPDATE password_resets SET used_at = NOW() WHERE token = ?";
                $mark_used_stmt = $pdo->prepare($mark_used_query);
                $mark_used_stmt->execute([$token]);
                
                log_security_event('password_reset_max_attempts', 'high', $ip, null, "Max verification attempts reached for: {$reset_request['email']}");
                return ['success' => false, 'message' => 'Too many verification attempts. Please request a new reset code.'];
            }
            
            log_password_reset_attempt($reset_request['email'], false, 'verify', $ip);
            return ['success' => false, 'message' => 'Invalid verification code.'];
        }
        
        // Mark as verified
        $verify_query = "UPDATE password_resets SET is_verified = 1 WHERE token = ?";
        $verify_stmt = $pdo->prepare($verify_query);
        $verify_stmt->execute([$token]);
        
        log_password_reset_attempt($reset_request['email'], true, 'verify', $ip);
        
        return [
            'success' => true,
            'email' => $reset_request['email'],
            'token' => $token
        ];
        
    } catch (PDOException $e) {
        error_log("Database error in verify_reset_request: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error. Please try again.'];
    }
}

// Reset password
function reset_password($token, $new_password) {
    // Database configuration
    $host = "localhost";
    $username = "root";
    $password = "nayah19";
    $database = "dbb";
    
    $ip = get_real_ip();
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Find verified reset request (fix timezone issue)
        $current_time = date('Y-m-d H:i:s');
        $query = "SELECT pr.*, u.id_user FROM password_resets pr 
                  JOIN user u ON pr.email = u.email 
                  WHERE pr.token = ? AND pr.is_verified = 1 AND pr.expires_at > ? AND pr.used_at IS NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$token, $current_time]);
        $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reset_request) {
            log_security_event('password_reset_invalid', 'medium', $ip, null, "Invalid password reset attempt");
            return ['success' => false, 'message' => 'Invalid or expired reset request.'];
        }
        
        // Validate new password
        if (!is_password_strong($new_password)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, lowercase, and numbers.'];
        }
        
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $update_query = "UPDATE user SET password = ?, updated_at = NOW() WHERE id_user = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$hashed_password, $reset_request['id_user']]);
        
        // Mark reset request as used
        $mark_used_query = "UPDATE password_resets SET used_at = NOW() WHERE token = ?";
        $mark_used_stmt = $pdo->prepare($mark_used_query);
        $mark_used_stmt->execute([$token]);
        
        // Log successful reset
        log_password_reset_attempt($reset_request['email'], true, 'reset', $ip);
        log_security_event('password_reset_success', 'low', $ip, $reset_request['id_user'], "Password reset successful for: {$reset_request['email']}");
        
        return ['success' => true, 'message' => 'Password has been reset successfully.'];
        
    } catch (PDOException $e) {
        error_log("Database error in reset_password: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error. Please try again.'];
    }
}

// Log password reset attempts
function log_password_reset_attempt($email, $success, $attempt_type, $ip) {
    // Database configuration
    $host = "localhost";
    $username = "root";
    $password = "nayah19";
    $database = "dbb";
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "INSERT INTO password_reset_attempts (email, ip_address, success, attempt_type, user_agent) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email, $ip, $success, $attempt_type, $user_agent]);
        
    } catch (PDOException $e) {
        error_log("Failed to log password reset attempt: " . $e->getMessage());
    }
}

// Send password reset email using custom PHPMailer
function send_password_reset_email($email, $verification_code, $user, $expires_at) {
    // Load email configuration
    $email_config = require __DIR__ . '/../config/email.php';
    
    // Load RealSMTPMailer
    require_once __DIR__ . '/../vendor/PHPMailer/src/RealSMTPMailer.php';
    
    try {
        $mail = new RealSMTPMailer();
        
        // Email setup
        $mail->setFrom($email_config['from']['address'], $email_config['from']['name']);
        $mail->addAddress($email);
        $mail->setSubject("Kode Reset Password - Aplikasi Data Siswa");
        
        // Email template in Indonesian (HTML)
        $message = "
        <html>
        <head>
            <title>Kode Reset Password</title>
        </head>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: #f8f9fa; padding: 30px; border-radius: 10px; text-align: center;'>
                <h2 style='color: #007bff; margin-bottom: 20px;'>? Reset Password</h2>
                
                <p>Halo {$user['nama_lengkap']},</p>
                
                <p>Anda meminta untuk reset password. Gunakan kode verifikasi berikut:</p>
                
                <div style='background: #007bff; color: white; font-size: 24px; font-weight: bold; 
                            padding: 20px; margin: 20px 0; border-radius: 5px; letter-spacing: 5px;'>
                    $verification_code
                </div>
                
                <p><strong>Kode ini akan kadaluarsa dalam 1 jam:</strong> " . date('H:i, d M Y', strtotime($expires_at)) . "</p>
                
                <p style='color: #6c757d; font-size: 14px;'>
                    Jika Anda tidak meminta reset password, abaikan email ini atau hubungi support.
                </p>
                
                <hr style='margin: 30px 0; border: none; border-top: 1px solid #dee2e6;'>
                
                <p style='color: #6c757d; font-size: 12px;'>
                    Ini adalah pesan otomatis dari Aplikasi Data Siswa.<br>
                    Mohon jangan balas email ini.
                </p>
            </div>
        </body>
        </html>
        ";
        
        // Plain text alternative
        $plain_message = "Halo {$user['nama_lengkap']},\n\n" .
                        "Kode reset password Anda: $verification_code\n\n" .
                        "Kode ini akan kadaluarsa dalam 1 jam: " . date('H:i, d M Y', strtotime($expires_at)) . "\n\n" .
                        "Jika Anda tidak meminta reset password, abaikan email ini.";
        
        // Set body (custom PHPMailer doesn't support HTML properly, so use plain text)
        $mail->setBody($plain_message);
        
        // Send email
        $sent = $mail->send();
        
        if ($sent) {
            // Log success
            error_log("Password reset email sent successfully to: $email, Code: $verification_code");
            return true;
        } else {
            // Log error
            error_log("Password reset email failed to send to: $email. Error: " . $mail->getErrorInfo());
            
            // For development, show the verification code
            if ($email_config['debug']) {
                echo "<div class='alert alert-warning'>";
                echo "<strong>Development Mode:</strong><br>";
                echo "Email gagal terkirim. Kode verifikasi untuk $email: <strong>$verification_code</strong><br>";
                echo "Error: " . $mail->getErrorInfo();
                echo "</div>";
            }
            
            return false;
        }
        
    } catch (Exception $e) {
        // Log error
        error_log("Password reset email failed to send to: $email. Exception: " . $e->getMessage());
        
        // For development, always show the verification code
        if ($email_config['debug']) {
            echo "<div class='alert alert-info' style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
            echo "<strong>Development Mode - Verification Code:</strong><br>";
            echo "Email: $email<br>";
            echo "Kode Verifikasi: <strong style='font-size: 18px; color: #007bff;'>$verification_code</strong><br>";
            echo "User: {$user['nama_lengkap']}<br>";
            echo "Expires: " . date('H:i, d M Y', strtotime($expires_at)) . "<br>";
            echo "Exception: " . $e->getMessage();
            echo "</div>";
        }
        
        // In development mode, still return true so the process continues
        return $email_config['debug'];
    }
}

// Clean up expired password reset requests
function cleanup_password_resets() {
    // Database configuration
    $host = "localhost";
    $username = "root";
    $password = "nayah19";
    $database = "dbb";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Clean expired requests
        $pdo->exec("DELETE FROM password_resets WHERE expires_at < NOW()");
        
        // Clean old attempts (older than 24 hours)
        $pdo->exec("DELETE FROM password_reset_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        
    } catch (PDOException $e) {
        error_log("Failed to cleanup password resets: " . $e->getMessage());
    }
}

?>
