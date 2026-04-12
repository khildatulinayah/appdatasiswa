<?php
/**
 * Test Manual Forgot Password (simulate browser behavior)
 */

session_start();

require_once __DIR__ . '/helper/simple_security_helper.php';
require_once __DIR__ . '/helper/password_reset_helper.php';

echo "<h2>Test Manual Forgot Password</h2>";

// Simulate form submission
$email = "khildatulinayah1988@gmail.com";

echo "<h3>Simulating: User enters email: $email</h3>";

// Simple rate limiting using session
if (!isset($_SESSION['forgot_password_attempts'])) {
    $_SESSION['forgot_password_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

// Simple rate limiting (max 3 attempts per 5 minutes)
$current_time = time();
if ($current_time - $_SESSION['last_attempt_time'] < 300) { // 5 minutes
    if ($_SESSION['forgot_password_attempts'] >= 3) {
        echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
        echo "? Rate limit exceeded!";
        echo "</div>";
        exit;
    }
    $_SESSION['forgot_password_attempts']++;
} else {
    $_SESSION['forgot_password_attempts'] = 1;
    $_SESSION['last_attempt_time'] = $current_time;
}

// Create password reset request
echo "<h3>Creating password reset request...</h3>";
$result = create_password_reset_request($email);

if ($result['success']) {
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
    echo "? Password reset request created!";
    echo "</div>";
    
    echo "<h4>Details:</h4>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($result['email']) . "</li>";
    echo "<li><strong>Verification Code:</strong> <strong style='color: #007bff; font-size: 18px;'>" . htmlspecialchars($result['verification_code']) . "</strong></li>";
    echo "<li><strong>User:</strong> " . htmlspecialchars($result['user']['nama_lengkap']) . "</li>";
    echo "</ul>";
    
    // Send email
    echo "<h3>Sending email...</h3>";
    $email_sent = send_password_reset_email(
        $result['email'], 
        $result['verification_code'], 
        $result['user'], 
        $result['expires_at']
    );
    
    if ($email_sent) {
        echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? Email sent successfully!";
        echo "</div>";
        
        // Store token in session for verification
        $_SESSION['reset_token'] = $result['token'];
        $_SESSION['reset_email'] = $result['email'];
        
        echo "<div class='alert alert-success'>";
        echo "<i class='fas fa-check-circle me-2'></i>";
        echo "Kode verifikasi telah dikirim ke email Anda. Silakan periksa inbox/spam.";
        echo "</div>";
        
    } else {
        echo "<div style='color: orange; font-weight: bold; margin: 10px 0;'>";
        echo "! Email failed to send!";
        echo "</div>";
        
        // Store token in session for verification
        $_SESSION['reset_token'] = $result['token'];
        $_SESSION['reset_email'] = $result['email'];
        $_SESSION['verification_code'] = $result['verification_code'];
        
        echo "<div class='alert alert-warning'>";
        echo "<i class='fas fa-exclamation-triangle me-2'></i>";
        echo "Email gagal dikirim. Kode verifikasi: <strong style='font-size: 18px; color: #007bff;'>" . htmlspecialchars($result['verification_code']) . "</strong>";
        echo "</div>";
    }
    
    // Test verification
    echo "<h3>Testing verification...</h3>";
    $verify_result = verify_reset_request($_SESSION['reset_token'], $result['verification_code']);
    
    if ($verify_result['success']) {
        echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? Verification successful!";
        echo "</div>";
    } else {
        echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
        echo "? Verification failed: " . htmlspecialchars($verify_result['message']);
        echo "</div>";
    }
    
} else {
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
    echo "? Request failed: " . htmlspecialchars($result['message']);
    echo "</div>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Check your email inbox for verification code</li>";
echo "<li>If no email received, use the code displayed above</li>";
echo "<li>Go to forgot_password_simple.php to test manually</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='forgot_password_simple.php'>Test Forgot Password Manual</a></p>";
?>
