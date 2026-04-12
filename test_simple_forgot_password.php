<?php
/**
 * Simple Test Forgot Password Flow (tanpa security_helper)
 * Test langsung fungsi password reset
 */

echo "<h2>Simple Test Forgot Password Flow</h2>";

// Test email yang ada di database
$test_email = "khildatulinayah1988@gmail.com";

echo "<h3>Step 1: Test Create Password Reset Request</h3>";
echo "<p>Testing with email: <strong>$test_email</strong></p>";

// Database configuration
$host = "localhost";
$username = "root";
$password = "nayah19";
$database = "dbb";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user exists
    $user_query = "SELECT id_user, username, nama_lengkap FROM user WHERE email = ?";
    $user_stmt = $pdo->prepare($user_query);
    $user_stmt->execute([$test_email]);
    $user_result = $user_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_result) {
        echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
        echo "? User not found in database!";
        echo "</div>";
        exit;
    }
    
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? User found: " . htmlspecialchars($user_result['nama_lengkap']) . " (" . htmlspecialchars($user_result['username']) . ")";
    echo "</div>";
    
    // Generate token and verification code
    $token = bin2hex(random_bytes(32));
    $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date('Y-m-d H:i:s', time() + 3600);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Test Agent';
    
    echo "<h3>Step 2: Generate Reset Data</h3>";
    echo "<ul>";
    echo "<li><strong>Token:</strong> " . htmlspecialchars(substr($token, 0, 20)) . "...</li>";
    echo "<li><strong>Verification Code:</strong> <strong style='color: #007bff; font-size: 18px;'>" . htmlspecialchars($verification_code) . "</strong></li>";
    echo "<li><strong>Expires At:</strong> " . htmlspecialchars($expires_at) . "</li>";
    echo "</ul>";
    
    // Clean old tokens
    $cleanup_query = "DELETE FROM password_resets WHERE email = ? OR expires_at < NOW()";
    $cleanup_stmt = $pdo->prepare($cleanup_query);
    $cleanup_stmt->execute([$test_email]);
    
    // Insert new reset request
    $insert_query = "INSERT INTO password_resets 
                    (email, token, verification_code, ip_address, user_agent, expires_at) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = $pdo->prepare($insert_query);
    $insert_success = $insert_stmt->execute([$test_email, $token, $verification_code, $ip, $user_agent, $expires_at]);
    
    if ($insert_success) {
        echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? Password reset request created successfully!";
        echo "</div>";
        
        // Test email sending
        echo "<h3>Step 3: Test Email Sending</h3>";
        
        // Load email config
        $email_config = require __DIR__ . '/config/email.php';
        require_once __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';
        
        $mail = new PHPMailer();
        $mail->setFrom($email_config['from']['address'], $email_config['from']['name']);
        $mail->addAddress($test_email);
        $mail->setSubject("Test Kode Reset Password - Aplikasi Data Siswa");
        
        $message = "Halo {$user_result['nama_lengkap']},\n\n" .
                  "Kode reset password Anda: $verification_code\n\n" .
                  "Kode ini akan kadaluarsa dalam 1 jam: " . date('H:i, d M Y', strtotime($expires_at)) . "\n\n" .
                  "Jika Anda tidak meminta reset password, abaikan email ini.";
        
        $mail->setBody($message);
        $email_sent = $mail->send();
        
        if ($email_sent) {
            echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
            echo "? Email sent successfully!";
            echo "</div>";
        } else {
            echo "<div style='color: orange; font-weight: bold; margin: 10px 0;'>";
            echo "! Email failed to send (development mode)";
            echo "</div>";
            echo "<p>Error: " . htmlspecialchars($mail->getErrorInfo()) . "</p>";
        }
        
        // Show development mode info
        if ($email_config['debug']) {
            echo "<div class='alert alert-info' style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
            echo "<strong>Development Mode - Verification Code:</strong><br>";
            echo "Email: $test_email<br>";
            echo "Kode Verifikasi: <strong style='font-size: 18px; color: #007bff;'>$verification_code</strong><br>";
            echo "User: {$user_result['nama_lengkap']}<br>";
            echo "Expires: " . date('H:i, d M Y', strtotime($expires_at)) . "<br>";
            echo "</div>";
        }
        
        // Test verification
        echo "<h3>Step 4: Test Verification</h3>";
        
        // Find the reset request (fix timezone issue)
        $current_time = date('Y-m-d H:i:s');
        $verify_query = "SELECT * FROM password_resets 
                        WHERE token = ? AND expires_at > ? AND used_at IS NULL";
        $verify_stmt = $pdo->prepare($verify_query);
        $verify_stmt->execute([$token, $current_time]);
        $reset_request = $verify_stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p>Debug: Looking for token: " . htmlspecialchars(substr($token, 0, 20)) . "...</p>";
        echo "<p>Debug: Verification code: $verification_code</p>";
        
        if ($reset_request) {
            echo "<p>Debug: Found reset request with code: {$reset_request['verification_code']}</p>";
        }
        
        if ($reset_request && $reset_request['verification_code'] === $verification_code) {
            echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
            echo "? Verification successful!";
            echo "</div>";
            
            // Mark as verified
            $mark_verified = "UPDATE password_resets SET is_verified = 1 WHERE token = ?";
            $mark_stmt = $pdo->prepare($mark_verified);
            $mark_stmt->execute([$token]);
            
            // Test password reset
            echo "<h3>Step 5: Test Password Reset</h3>";
            $new_password = "TestPassword123";
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_query = "UPDATE user SET password = ?, updated_at = NOW() WHERE id_user = ?";
            $update_stmt = $pdo->prepare($update_query);
            $reset_success = $update_stmt->execute([$hashed_password, $user_result['id_user']]);
            
            if ($reset_success) {
                // Mark as used
                $mark_used = "UPDATE password_resets SET used_at = NOW() WHERE token = ?";
                $used_stmt = $pdo->prepare($mark_used);
                $used_stmt->execute([$token]);
                
                echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
                echo "? Password reset successful!";
                echo "</div>";
                echo "<p>New password: <strong>$new_password</strong></p>";
                echo "<p>You can now login with this password.</p>";
            } else {
                echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
                echo "? Password reset failed!";
                echo "</div>";
            }
        } else {
            echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
            echo "? Verification failed!";
            echo "</div>";
        }
        
    } else {
        echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
        echo "? Failed to create password reset request!";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
    echo "? Database error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<h3>Manual Testing Instructions:</h3>";
echo "<ol>";
echo "<li>Open <a href='forgot_password.php'>forgot_password.php</a> in browser</li>";
echo "<li>Enter email: <strong>khildatulinayah1988@gmail.com</strong></li>";
echo "<li>Look for verification code in development mode display</li>";
echo "<li>Enter the verification code: <strong style='color: #007bff;'>$verification_code</strong></li>";
echo "<li>Set new password</li>";
echo "<li>Test login with new password</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='index.php'>Kembali ke Dashboard</a> | <a href='check_users.php'>Check Users</a></p>";
?>
