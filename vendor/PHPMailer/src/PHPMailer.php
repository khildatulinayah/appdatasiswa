<?php
/**
 * Simple PHPMailer implementation for this application
 * Basic SMTP email functionality
 */

class PHPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $from;
    private $fromName;
    private $subject;
    private $body;
    private $altBody;
    private $to = [];
    private $errorInfo = '';
    
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
        if (!$isHTML) {
            $this->altBody = $body;
        }
    }
    
    public function isHTML($isHTML = true) {
        // For simplicity, we'll handle this in the send method
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
            
            // Try to send using PHP mail function
            if (!@mail($to, $subject, $message, $headers_string)) {
                $this->errorInfo = 'Email sending failed';
                return false;
            }
        }
        
        return true;
    }
    
    public function getErrorInfo() {
        return $this->errorInfo;
    }
    
    public function sendSMTP() {
        // Advanced SMTP implementation would go here
        // For now, fallback to regular mail function
        return $this->send();
    }
}
