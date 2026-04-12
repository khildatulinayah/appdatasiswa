<?php
/**
 * Migration untuk update tabel password_resets menggunakan PDO
 * Jalankan file ini melalui browser: http://localhost/app-siswa1/run_password_reset_migration_pdo.php
 */

// Database configuration
$host = "localhost";
$username = "root";
$password = "nayah19";
$database = "dbb";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Migration Password Reset Tables (PDO)</h2>";
    echo "<p><strong>Database:</strong> $database</p>";
    echo "<p><strong>Host:</strong> $host</p>";
    echo "<hr>";
    
    // SQL queries
    $sql_queries = [
        // Drop existing table if exists
        "DROP TABLE IF EXISTS `password_resets`",
        
        // Create new password_resets table with verification code system
        "CREATE TABLE `password_resets` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(100) NOT NULL,
          `token` varchar(255) NOT NULL,
          `verification_code` varchar(6) NOT NULL,
          `is_verified` tinyint(1) NOT NULL DEFAULT '0',
          `ip_address` varchar(45) NOT NULL,
          `user_agent` text DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `expires_at` timestamp NOT NULL,
          `used_at` timestamp NULL DEFAULT NULL,
          `attempts_count` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          UNIQUE KEY `token` (`token`),
          KEY `email` (`email`),
          KEY `verification_code` (`verification_code`),
          KEY `expires_at` (`expires_at`),
          KEY `ip_address` (`ip_address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Add password reset tracking to security_events if not exists
        "ALTER TABLE `security_events` 
        ADD COLUMN IF NOT EXISTS `reset_token` varchar(255) NULL AFTER `user_id`,
        ADD INDEX IF NOT EXISTS `reset_token` (`reset_token`)",
        
        // Create password_reset_attempts table for rate limiting
        "CREATE TABLE IF NOT EXISTS `password_reset_attempts` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(100) NOT NULL,
          `ip_address` varchar(45) NOT NULL,
          `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
          `success` tinyint(1) NOT NULL DEFAULT '0',
          `attempt_type` enum('request','verify','reset') NOT NULL DEFAULT 'request',
          `user_agent` text DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `email` (`email`),
          KEY `ip_address` (`ip_address`),
          KEY `attempt_time` (`attempt_time`),
          KEY `success` (`success`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    $success = true;
    $executed_queries = [];
    
    foreach ($sql_queries as $index => $query) {
        echo "<p><strong>Query " . ($index + 1) . ":</strong><br>";
        echo "<code>" . htmlspecialchars(substr($query, 0, 100)) . "...</code><br>";
        
        try {
            $pdo->exec($query);
            echo "<span style='color: green;'>&check; Success</span></p>";
            $executed_queries[] = "Query " . ($index + 1) . ": SUCCESS";
        } catch (PDOException $e) {
            echo "<span style='color: orange;'>&#9888; Warning: " . $e->getMessage() . "</span></p>";
            $executed_queries[] = "Query " . ($index + 1) . ": WARNING - " . $e->getMessage();
            // Continue execution for warnings (like column already exists)
        }
    }
    
    echo "<hr>";
    
    if ($success) {
        echo "<h3 style='color: green;'>&check; Migration berhasil!</h3>";
        echo "<p>Semua tabel password reset telah dibuat/updated dengan sukses.</p>";
    } else {
        echo "<h3 style='color: orange;'>&#9888; Migration selesai dengan warnings!</h3>";
        echo "<p>Beberapa query menghasilkan warning, tapi kemungkinan berhasil.</p>";
    }
    
    echo "<h4>Summary:</h4>";
    echo "<ul>";
    foreach ($executed_queries as $result) {
        echo "<li>" . htmlspecialchars($result) . "</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='index.php'>Kembali ke Dashboard</a></p>";
    echo "<p><strong>Catatan:</strong> Hapus file ini setelah migration selesai untuk keamanan.</p>";
    
    // Verify tables exist
    echo "<h3>Verification:</h3>";
    $tables_to_check = ['password_resets', 'password_reset_attempts'];
    
    foreach ($tables_to_check as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>&check; Tabel '$table' exists</p>";
            
            // Show table structure
            $structure = $pdo->query("DESCRIBE $table");
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>&cross; Tabel '$table' tidak ditemukan</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Database Connection Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Periksa konfigurasi database di file ini.</p>";
}
?>
