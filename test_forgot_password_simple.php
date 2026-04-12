<?php
/**
 * Test Forgot Password Simple Flow
 */

require_once __DIR__ . '/helper/simple_security_helper.php';
require_once __DIR__ . '/helper/password_reset_helper.php';

echo "<h2>Test Forgot Password Simple Flow</h2>";

// Test email yang ada di database
$test_email = "khildatulinayah1988@gmail.com";

echo "<h3>Step 1: Test Create Password Reset Request</h3>";
echo "<p>Testing with email: <strong>$test_email</strong></p>";

// Test create password reset request
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
    echo "<h3>Step 2: Test Send Email</h3>";
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
    } else {
        echo "<div style='color: orange; font-weight: bold; margin: 10px 0;'>";
        echo "! Email sent in development mode (code displayed above)";
        echo "</div>";
    }
    
    // Test verification
    echo "<h3>Step 3: Test Verification Code</h3>";
    $verify_result = verify_reset_request($result['token'], $result['verification_code']);
    
    if ($verify_result['success']) {
        echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? Verification code valid!";
        echo "</div>";
        
        // Test password reset
        echo "<h3>Step 4: Test Password Reset</h3>";
        $new_password = "TestPassword123";
        $reset_result = reset_password($result['token'], $new_password);
        
        if ($reset_result['success']) {
            echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
            echo "? Password reset successful!";
            echo "</div>";
            echo "<p>New password: <strong>$new_password</strong></p>";
        } else {
            echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
            echo "? Password reset failed: " . htmlspecialchars($reset_result['message']);
            echo "</div>";
        }
    } else {
        echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
        echo "? Verification failed: " . htmlspecialchars($verify_result['message']);
        echo "</div>";
    }
    
} else {
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
    echo "? Password reset request failed: " . htmlspecialchars($result['message']);
    echo "</div>";
}

echo "<hr>";

// Test CSRF functionality
echo "<h3>Step 5: Test CSRF Token</h3>";
session_start();

$token1 = generate_simple_csrf_token();
$token2 = generate_simple_csrf_token();

echo "<p>CSRF Token 1: " . htmlspecialchars($token1) . "</p>";
echo "<p>CSRF Token 2: " . htmlspecialchars($token2) . "</p>";
echo "<p>Tokens match: " . ($token1 === $token2 ? 'YES' : 'NO') . "</p>";

$validation1 = validate_simple_csrf_token($token1);
$validation2 = validate_simple_csrf_token('invalid_token');

echo "<p>Valid token validation: " . ($validation1 ? 'YES' : 'NO') . "</p>";
echo "<p>Invalid token validation: " . ($validation2 ? 'YES' : 'NO') . "</p>";

echo "<hr>";

// Test rate limiting
echo "<h3>Step 6: Test Rate Limiting</h3>";
$rate_limit1 = check_simple_rate_limit('test_action', 3, 300);
$rate_limit2 = check_simple_rate_limit('test_action', 3, 300);
$rate_limit3 = check_simple_rate_limit('test_action', 3, 300);
$rate_limit4 = check_simple_rate_limit('test_action', 3, 300);

echo "<p>Attempt 1: " . ($rate_limit1 ? 'ALLOWED' : 'BLOCKED') . "</p>";
echo "<p>Attempt 2: " . ($rate_limit2 ? 'ALLOWED' : 'BLOCKED') . "</p>";
echo "<p>Attempt 3: " . ($rate_limit3 ? 'ALLOWED' : 'BLOCKED') . "</p>";
echo "<p>Attempt 4: " . ($rate_limit4 ? 'ALLOWED' : 'BLOCKED') . "</p>";

echo "<hr>";
echo "<h3>Manual Testing Instructions:</h3>";
echo "<ol>";
echo "<li>Open <a href='forgot_password_simple.php'>forgot_password_simple.php</a> in browser</li>";
echo "<li>Enter email: <strong>khildatulinayah1988@gmail.com</strong></li>";
echo "<li>Look for verification code in development mode display</li>";
echo "<li>Enter the verification code</li>";
echo "<li>Set new password</li>";
echo "<li>Test login with new password</li>";
echo "</ol>";

echo "<h3>Features:</h3>";
echo "<ul>";
echo "<li>? No database dependency issues</li>";
echo "<li>? Simple CSRF protection</li>";
echo "<li>? Session-based rate limiting</li>";
echo "<li>? Development mode with code display</li>";
echo "<li>? Complete password reset flow</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='index.php'>Kembali ke Dashboard</a> | <a href='forgot_password_simple.php'>Test Forgot Password</a></p>";
?>
