<?php
/**
 * Debug password_resets table
 */

echo "<h2>Debug Password Resets Table</h2>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=dbb;charset=utf8mb4', 'root', 'nayah19');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>Recent Password Reset Requests:</h3>";
    
    $stmt = $pdo->query('SELECT * FROM password_resets ORDER BY created_at DESC LIMIT 10');
    
    if ($stmt->rowCount() > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; font-size: 12px;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Token (first 20)</th><th>Verification Code</th><th>Is Verified</th><th>Created At</th><th>Expires At</th><th>Used At</th><th>Attempts</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['token'], 0, 20)) . "...</td>";
            echo "<td><strong>" . htmlspecialchars($row['verification_code']) . "</strong></td>";
            echo "<td>" . ($row['is_verified'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($row['expires_at']) . "</td>";
            echo "<td>" . ($row['used_at'] ? htmlspecialchars($row['used_at']) : 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['attempts_count']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No password reset requests found.</p>";
    }
    
    echo "<h3>Test Specific Token:</h3>";
    
    // Test with the most recent token
    $latest_stmt = $pdo->query('SELECT * FROM password_resets ORDER BY created_at DESC LIMIT 1');
    $latest = $latest_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($latest) {
        echo "<p>Testing with latest token:</p>";
        echo "<ul>";
        echo "<li><strong>Token:</strong> " . htmlspecialchars($latest['token']) . "</li>";
        echo "<li><strong>Verification Code:</strong> " . htmlspecialchars($latest['verification_code']) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($latest['email']) . "</li>";
        echo "<li><strong>Expires At:</strong> " . htmlspecialchars($latest['expires_at']) . "</li>";
        echo "<li><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</li>";
        echo "<li><strong>Is Expired:</strong> " . (strtotime($latest['expires_at']) < time() ? 'YES' : 'NO') . "</li>";
        echo "</ul>";
        
        // Test query
        $test_query = "SELECT * FROM password_resets 
                      WHERE token = ? AND expires_at > NOW() AND used_at IS NULL";
        $test_stmt = $pdo->prepare($test_query);
        $test_stmt->execute([$latest['token']]);
        $test_result = $test_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($test_result) {
            echo "<p style='color: green;'>? Token found and valid!</p>";
        } else {
            echo "<p style='color: red;'>? Token not found or invalid!</p>";
            
            // Check individual conditions
            echo "<h4>Debug Conditions:</h4>";
            
            // Check if token exists
            $token_check = $pdo->prepare("SELECT * FROM password_resets WHERE token = ?");
            $token_check->execute([$latest['token']]);
            echo "<p>Token exists: " . ($token_check->rowCount() > 0 ? 'YES' : 'NO') . "</p>";
            
            // Check if expired
            echo "<p>Expires > NOW(): " . (strtotime($latest['expires_at']) > time() ? 'YES' : 'NO') . "</p>";
            
            // Check if used
            echo "<p>Used At is NULL: " . ($latest['used_at'] === null ? 'YES' : 'NO') . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><a href='index.php'>Kembali ke Dashboard</a> | <a href='check_users.php'>Check Users</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>Database Error</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
