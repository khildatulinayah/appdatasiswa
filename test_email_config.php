<?php
/**
 * Test Email Configuration
 * Jalankan file ini untuk testing pengiriman email
 */

$email_config = require_once __DIR__ . '/config/email.php';
require_once __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';

echo "<h2>Test Email Configuration</h2>";

// Test email recipient (gunakan email Anda untuk testing)
$test_email = "khildatulinayah1988@gmail.com"; // Ganti dengan email test Anda

try {
    $mail = new PHPMailer();
    
    // Email setup
    $mail->setFrom($email_config['from']['address'], $email_config['from']['name']);
    $mail->addAddress($test_email);
    $mail->setSubject("Test Email - Aplikasi Data Siswa");
    
    // Message content (plain text)
    $message = "Ini adalah email test dari Aplikasi Data Siswa.

Test Code: 123456

Waktu pengiriman: " . date('H:i:s, d M Y') . "

Jika Anda menerima email ini, berarti konfigurasi email sudah benar.

Ini adalah pesan test otomatis dari Aplikasi Data Siswa.
Mohon jangan balas email ini.";
    
    $mail->setBody($message);
    
    // Send email
    $sent = $mail->send();
    
    if ($sent) {
        echo "<div style='color: green; font-weight: bold; margin: 20px 0;'>";
        echo "? Email test berhasil dikirim ke: " . htmlspecialchars($test_email);
        echo "</div>";
        
        echo "<h3>Configuration Details:</h3>";
        echo "<ul>";
        echo "<li><strong>SMTP Host:</strong> " . htmlspecialchars($email_config['host']) . "</li>";
        echo "<li><strong>SMTP Port:</strong> " . htmlspecialchars($email_config['port']) . "</li>";
        echo "<li><strong>Encryption:</strong> " . htmlspecialchars($email_config['encryption']) . "</li>";
        echo "<li><strong>From Email:</strong> " . htmlspecialchars($email_config['from']['address']) . "</li>";
        echo "<li><strong>From Name:</strong> " . htmlspecialchars($email_config['from']['name']) . "</li>";
        echo "<li><strong>Debug Mode:</strong> " . ($email_config['debug'] ? 'ON' : 'OFF') . "</li>";
        echo "</ul>";
        
        echo "<h3>Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Check inbox atau folder spam untuk email test</li>";
        echo "<li>Jika email diterima, konfigurasi sudah benar</li>";
        echo "<li>Test forgot password flow di aplikasi</li>";
        echo "<li>Hapus file ini setelah testing selesai</li>";
        echo "</ol>";
        
        echo "<hr>";
        echo "<p><a href='index.php'>Kembali ke Dashboard</a></p>";
    } else {
        echo "<div style='color: red; font-weight: bold; margin: 20px 0;'>";
        echo "? Email test gagal dikirim!";
        echo "</div>";
        
        echo "<h3>Error Details:</h3>";
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
        echo "<strong>Error:</strong> " . htmlspecialchars($mail->getErrorInfo());
        echo "</div>";
        
        echo "<h3>Troubleshooting:</h3>";
        echo "<ul>";
        echo "<li>Periksa konfigurasi PHP mail server</li>";
        echo "<li>Check firewall tidak blocking email</li>";
        echo "<li>Enable debug mode di config/email.php: 'debug' => true</li>";
        echo "<li>Verify email dan password tidak ada typo</li>";
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold; margin: 20px 0;'>";
    echo "? Email test gagal dikirim!";
    echo "</div>";
    
    echo "<h3>Error Details:</h3>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<strong>Exception:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
    
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Periksa konfigurasi PHP mail server</li>";
    echo "<li>Check firewall tidak blocking email</li>";
    echo "<li>Enable debug mode di config/email.php: 'debug' => true</li>";
    echo "<li>Verify email dan password tidak ada typo</li>";
    echo "</ul>";
}
?>
