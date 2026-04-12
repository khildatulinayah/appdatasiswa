<?php
/**
 * Debug session untuk forgot password
 */

session_start();

echo "<h2>Debug Session</h2>";

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Session ID:</h3>";
echo "<p>" . session_id() . "</p>";

echo "<h3>Session Status:</h3>";
echo "<p>Session status: " . session_status() . "</p>";
echo "<p>Session save path: " . session_save_path() . "</p>";

if (isset($_SESSION['reset_token'])) {
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
    echo "? Reset token found in session!";
    echo "</div>";
    echo "<p>Token: " . htmlspecialchars(substr($_SESSION['reset_token'], 0, 20)) . "...</p>";
    echo "<p>Email: " . htmlspecialchars($_SESSION['reset_email'] ?? 'N/A') . "</p>";
    echo "<p>Verification Code: " . htmlspecialchars($_SESSION['verification_code'] ?? 'N/A') . "</p>";
    
    echo "<h3>Form should show verification code input</h3>";
    echo "<p>Check forgot_password_simple.php</p>";
} else {
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
    echo "? No reset token in session!";
    echo "</div>";
    
    echo "<h3>Create test session:</h3>";
    
    // Create test session data
    require_once __DIR__ . '/helper/password_reset_helper.php';
    
    $result = create_password_reset_request("khildatulinayah1988@gmail.com");
    
    if ($result['success']) {
        $_SESSION['reset_token'] = $result['token'];
        $_SESSION['reset_email'] = $result['email'];
        $_SESSION['verification_code'] = $result['verification_code'];
        
        echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
        echo "? Test session created!";
        echo "</div>";
        echo "<p>Verification Code: <strong style='color: #007bff; font-size: 18px;'>" . htmlspecialchars($result['verification_code']) . "</strong></p>";
        echo "<p><a href='forgot_password_simple.php'>Test forgot password with session</a></p>";
    }
}

echo "<hr>";
echo "<p><a href='forgot_password_simple.php'>Back to Forgot Password</a></p>";
?>
