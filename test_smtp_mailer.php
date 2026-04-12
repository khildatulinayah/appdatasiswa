<?php
/**
 * Test SMTPMailer
 */

$email_config = require_once __DIR__ . '/config/email.php';
require_once __DIR__ . '/vendor/PHPMailer/src/SMTPMailer.php';

echo "<h2>Test SMTPMailer</h2>";

// Test email recipient
$test_email = "khildatulinayah1988@gmail.com";

try {
    $mail = new SMTPMailer();
    
    // Email setup
    $mail->setFrom($email_config['from']['address'], $email_config['from']['name']);
    $mail->addAddress($test_email);
    $mail->setSubject("Test SMTPMailer - Aplikasi Data Siswa");
    
    // Message content
    $message = "Ini adalah test email dari SMTPMailer.

Test Code: 123456

Waktu pengiriman: " . date('H:i:s, d M Y') . "

Jika Anda menerima email ini, berarti SMTPMailer berjalan dengan baik.

Ini adalah pesan test otomatis dari Aplikasi Data Siswa.
Mohon jangan balas email ini.";
    
    $mail->setBody($message);
    
    // Send email
    $sent = $mail->send();
    
    if ($sent) {
        echo "<div style='color: green; font-weight: bold; margin: 20px 0;'>";
        echo "? Email berhasil dikirim ke: " . htmlspecialchars($test_email);
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
        echo "<li>Jika email diterima, SMTPMailer berjalan dengan baik</li>";
        echo "<li>Test forgot password flow di aplikasi</li>";
        echo "</ol>";
        
    } else {
        echo "<div style='color: red; font-weight: bold; margin: 20px 0;'>";
        echo "? Email gagal dikirim!";
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
    echo "? Exception occurred!";
    echo "</div>";
    
    echo "<h3>Error Details:</h3>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<strong>Exception:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>Kembali ke Dashboard</a> | <a href='test_email_config.php'>Test Original Mailer</a></p>";
?>
