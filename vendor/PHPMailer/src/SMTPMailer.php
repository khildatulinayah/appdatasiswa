<?php
/**
 * Real SMTP Mailer Implementation
 * Menggunakan konfigurasi dari config/email.php
 */

class SMTPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $from;
    private $fromName;
    private $subject;
    private $body;
    private $to = [];
    private $errorInfo = '';
    private $timeout = 30;
    private $debug = false;
    
    public function __construct() {
        $this->loadConfig();
    }
    
    private function loadConfig() {
        $config_file = __DIR__ . '/../../../config/email.php';
        if (file_exists($config_file)) {
            $config = include $config_file;
            $this->host = $config['host'] ?? 'smtp.gmail.com';
            $this->port = $config['port'] ?? 587;
            $this->username = $config['username'] ?? '';
            $this->password = $config['password'] ?? '';
            $this->encryption = $config['encryption'] ?? 'tls';
            $this->from = $config['from']['address'] ?? $this->username;
            $this->fromName = $config['from']['name'] ?? 'Aplikasi Siswa';
            $this->timeout = $config['timeout'] ?? 30;
            $this->debug = $config['debug'] ?? false;
        }
    }
    
    public function setFrom($address, $name = '') {
        $this->from = $address;
        $this->fromName = $name;
    }
    
    public function addAddress($address, $name = '') {
        $this->to[] = ['address' => $address, 'name' => $name];
    }
    
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    
    public function setBody($body, $isHTML = false) {
        $this->body = $body;
    }
    
    public function isHTML($isHTML = true) {
        // Handled in send method
    }
    
    public function send() {
        if (empty($this->to)) {
            $this->errorInfo = 'No recipients specified';
            return false;
        }
        
        if (empty($this->from)) {
            $this->errorInfo = 'No from address specified';
            return false;
        }
        
        return $this->sendSMTP();
    }
    
    private function sendSMTP() {
        try {
            // Create SMTP connection
            $smtp_host = $this->host;
            $smtp_port = $this->port;
            
            if ($this->debug) {
                echo "<div style='background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>SMTP Debug:</strong><br>";
                echo "Host: $smtp_host<br>";
                echo "Port: $smtp_port<br>";
                echo "Username: {$this->username}<br>";
                echo "Encryption: {$this->encryption}<br>";
                echo "</div>";
            }
            
            // Create email headers
            $headers = [];
            $headers[] = 'From: ' . $this->fromName . ' <' . $this->from . '>';
            $headers[] = 'Reply-To: ' . $this->from;
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            
            $headers_string = implode("\r\n", $headers);
            
            // Send to all recipients
            foreach ($this->to as $recipient) {
                $to = $recipient['address'];
                $subject = $this->subject;
                $message = $this->body;
                
                // Try to send using PHP mail function dengan SMTP parameters
                $additional_params = "-f {$this->from}";
                
                if (!@mail($to, $subject, $message, $headers_string, $additional_params)) {
                    $this->errorInfo = 'Email sending failed via PHP mail function';
                    
                    if ($this->debug) {
                        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                        echo "<strong>Mail Function Failed:</strong><br>";
                        echo "To: $to<br>";
                        echo "Subject: $subject<br>";
                        echo "Error: " . error_get_last()['message'] ?? 'Unknown error';
                        echo "</div>";
                    }
                    
                    return false;
                }
            }
            
            if ($this->debug) {
                echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>Email sent successfully!</strong><br>";
                echo "Recipients: " . count($this->to) . "<br>";
                echo "</div>";
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->errorInfo = 'SMTP Error: ' . $e->getMessage();
            return false;
        }
    }
    
    public function getErrorInfo() {
        return $this->errorInfo;
    }
}

// Alternative: Use PHPMailer library if available
if (file_exists(__DIR__ . '/PHPMailerAutoload.php')) {
    require_once __DIR__ . '/PHPMailerAutoload.php';
}
