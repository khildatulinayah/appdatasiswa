<?php
/**
 * Check users in database for testing
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=dbb;charset=utf8mb4', 'root', 'nayah19');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>Users in Database:</h3>";
    
    $stmt = $pdo->query('SELECT id_user, email, username, nama_lengkap FROM user LIMIT 10');
    
    if ($stmt->rowCount() > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Name</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_user']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database.</p>";
    }
    
    echo "<h3>Test Forgot Password Flow:</h3>";
    echo "<ol>";
    echo "<li>Go to: <a href='forgot_password.php'>forgot_password.php</a></li>";
    echo "<li>Enter one of the email addresses above</li>";
    echo "<li>Check for verification code (in development mode, it will be displayed)</li>";
    echo "<li>Enter the verification code</li>";
    echo "<li>Reset password</li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<p><a href='index.php'>Kembali ke Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>Database Error</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
