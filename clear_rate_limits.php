<?php
/**
 * Clear rate limiting data for testing
 */

echo "<h2>Clear Rate Limits and Reset Data</h3>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=dbb;charset=utf8mb4', 'root', 'nayah19');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Clear password reset attempts
    $delete_attempts = $pdo->exec("DELETE FROM password_reset_attempts");
    echo "<p>? Cleared $delete_attempts password reset attempts from database</p>";
    
    // Clear password_resets table
    $delete_resets = $pdo->exec("DELETE FROM password_resets");
    echo "<p>? Cleared $delete_resets password reset tokens from database</p>";
    
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>";
    echo "? Database cleared successfully!";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>";
    echo "? Database error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='test_forgot_password_simple.php'>Test Again</a> | <a href='forgot_password_simple.php'>Test Forgot Password</a></p>";
?>
