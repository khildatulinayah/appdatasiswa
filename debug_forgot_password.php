<?php
/**
 * Debug forgot_password_simple.php email sending
 */

session_start();

require_once __DIR__ . '/helper/simple_security_helper.php';
require_once __DIR__ . '/helper/password_reset_helper.php';

echo "<h2>Debug Forgot Password Email Sending</h2>";

// Test email yang ada di database
$test_email = "khildatulinayah1988@gmail.com";

echo "<h3>Step 1: Test Create Password Reset Request</h3>";
echo "<p>Testing with email: <strong>$test_email</strong></p>";

// Create password reset request
$result = create_password_reset_request($test_email);

if ($result['success']) {
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
    echo "? Password reset request created successfully!";
    echo "</div>";
    
    echo "<h4>Request Details:</h4>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($result['email']) . "</li>";
    echo "<li><strong>Token:</strong> " . htmlspecialchars(substr($result['token'], 0, 20)) . "...</li>";
    echo "<li><strong>Verification Code:</strong> <strong style='color: #007bff; font-size: 18px;'>" . htmlspecialchars($result['verification_code']) . "</strong></li>";
    echo "<li><strong>User:</strong> " . htmlspecialchars($result['user']['nama_lengkap']) . " (" . htmlspecialchars($result['user']['username']) . ")</li>";
    echo "<li><strong>Expires At:</strong> " . htmlspecialchars($result['expires_at']) . "</li>";
    echo "</ul>";
    
    // Test send email
    echo "<h3>Step 2: Test Send Email (Real SMTP)</h3>";
    
    // Enable debug mode temporarily
    $email_config = require __DIR__ . '/config/email.php';
    $original_debug = $email_config['debug'];
    
    echo "<p>Current debug mode: " . ($original_debug ? 'ON' : 'OFF') . "</p>";
    
    // Temporarily enable debug for testing
    $email_config['debug'] = true;
    
    $email_sent = send_password_reset_email(
        $result['email'], 
        $result['verification_code'], 
        $result['user'], 
        $result['expires_at']
    );
    
    // Restore original debug setting
    $email_config['debug'] = $original_debug;
    
    if ($email_sent) {
        echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? Email sent successfully!";
        echo "</div>";
        echo "<p><strong>Check your inbox for verification code: {$result['verification_code']}</strong></p>";
    } else {
        echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
        echo "? Email failed to send!";
        echo "</div>";
        echo "<p><strong>Development mode code: {$result['verification_code']}</strong></p>";
    }
    
} else {
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
    echo "? Password reset request failed: " . htmlspecialchars($result['message']);
    echo "</div>";
}

echo "<hr>";
echo "<h3>Debug Information:</h3>";
echo "<ul>";
echo "<li><strong>PHP Mail Function:</strong> " . (function_exists('mail') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>fsockopen Function:</strong> " . (function_exists('fsockopen') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>stream_socket_enable_crypto:</strong> " . (function_exists('stream_socket_enable_crypto') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>OpenSSL Extension:</strong> " . (extension_loaded('openssl') ? 'Loaded' : 'Not Loaded') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='forgot_password_simple.php'>Test Forgot Password</a> | <a href='test_real_smtp.php'>Test Real SMTP</a></p>";
?>
