<?php
/**
 * Test Forgot Password Flow
 * Simulasi forgot password request untuk testing
 */

require_once __DIR__ . '/helper/password_reset_helper.php';
require_once __DIR__ . '/helper/security_helper.php';

echo "<h2>Test Forgot Password Flow</h2>";

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

// Test invalid email
echo "<h3>Step 5: Test Invalid Email</h3>";
$invalid_email = "nonexistent@example.com";
echo "<p>Testing with invalid email: <strong>$invalid_email</strong></p>";

$invalid_result = create_password_reset_request($invalid_email);

if (!$invalid_result['success']) {
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
    echo "? Invalid email properly handled (security measure)";
    echo "</div>";
    echo "<p>Message: " . htmlspecialchars($invalid_result['message']) . "</p>";
} else {
    echo "<div style='color: orange; font-weight: bold; margin: 10px 0;'>";
        echo "! Unexpected result for invalid email";
        echo "</div>";
}

echo "<hr>";
echo "<h3>Manual Testing Instructions:</h3>";
echo "<ol>";
echo "<li>Open <a href='forgot_password.php'>forgot_password.php</a> in browser</li>";
echo "<li>Enter email: <strong>khildatulinayah1988@gmail.com</strong></li>";
echo "<li>Look for verification code in development mode display</li>";
echo "<li>Enter the verification code</li>";
echo "<li>Set new password</li>";
echo "<li>Test login with new password</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='index.php'>Kembali ke Dashboard</a> | <a href='check_users.php'>Check Users</a></p>";
?>
