<?php
/**
 * Real SMTP Mailer - Direct SMTP Connection
 * Benar-benar konek ke Gmail SMTP tanpa PHP mail function
 */

class RealSMTPMailer {
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
        
        return $this->sendRealSMTP();
    }
    
    private function sendRealSMTP() {
        try {
            if ($this->debug) {
                echo "<div style='background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>Real SMTP Debug:</strong><br>";
                echo "Host: {$this->host}<br>";
                echo "Port: {$this->port}<br>";
                echo "Username: {$this->username}<br>";
                echo "Encryption: {$this->encryption}<br>";
                echo "</div>";
            }
            
            // Create socket connection to SMTP server
            $smtp_host = $this->host;
            $smtp_port = $this->port;
            
            // Connect to SMTP server
            $socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, $this->timeout);
            
            if (!$socket) {
                $this->errorInfo = "Failed to connect to {$smtp_host}:{$smtp_port} - {$errstr} ({$errno})";
                return false;
            }
            
            if ($this->debug) {
                echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>Connected to SMTP server</strong><br>";
                echo "</div>";
            }
            
            // Read greeting
            $response = $this->readResponse($socket);
            if (!$this->isSuccessResponse($response)) {
                fclose($socket);
                $this->errorInfo = "SMTP greeting failed: $response";
                return false;
            }
            
            // Say EHLO
            $this->sendCommand($socket, "EHLO " . gethostname());
            $ehlo_response = $this->readResponse($socket);
            
            // Start TLS if required
            if ($this->encryption === 'tls') {
                $this->sendCommand($socket, "STARTTLS");
                $tls_response = $this->readResponse($socket);
                
                if (!$this->isSuccessResponse($tls_response)) {
                    fclose($socket);
                    $this->errorInfo = "STARTTLS failed: $tls_response";
                    return false;
                }
                
                // Enable crypto
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    fclose($socket);
                    $this->errorInfo = "Failed to enable TLS encryption";
                    return false;
                }
                
                // Say EHLO again after TLS
                $this->sendCommand($socket, "EHLO " . gethostname());
                $ehlo_response = $this->readResponse($socket);
            }
            
            // Authenticate
            $this->sendCommand($socket, "AUTH LOGIN");
            $auth_response = $this->readResponse($socket);
            
            if (!$this->isSuccessResponse($auth_response)) {
                fclose($socket);
                $this->errorInfo = "AUTH LOGIN failed: $auth_response";
                return false;
            }
            
            // Send username
            $this->sendCommand($socket, base64_encode($this->username));
            $user_response = $this->readResponse($socket);
            
            if (!$this->isSuccessResponse($user_response)) {
                fclose($socket);
                $this->errorInfo = "Username authentication failed: $user_response";
                return false;
            }
            
            // Send password
            $this->sendCommand($socket, base64_encode($this->password));
            $pass_response = $this->readResponse($socket);
            
            if (!$this->isSuccessResponse($pass_response)) {
                fclose($socket);
                $this->errorInfo = "Password authentication failed: $pass_response";
                return false;
            }
            
            if ($this->debug) {
                echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>Authentication successful</strong><br>";
                echo "</div>";
            }
            
            // Send mail to each recipient
            foreach ($this->to as $recipient) {
                $to = $recipient['address'];
                
                // Set sender
                $this->sendCommand($socket, "MAIL FROM:<{$this->from}>");
                $mail_from_response = $this->readResponse($socket);
                
                if (!$this->isSuccessResponse($mail_from_response)) {
                    fclose($socket);
                    $this->errorInfo = "MAIL FROM failed: $mail_from_response";
                    return false;
                }
                
                // Set recipient
                $this->sendCommand($socket, "RCPT TO:<{$to}>");
                $rcpt_to_response = $this->readResponse($socket);
                
                if (!$this->isSuccessResponse($rcpt_to_response)) {
                    fclose($socket);
                    $this->errorInfo = "RCPT TO failed: $rcpt_to_response";
                    return false;
                }
                
                // Start data
                $this->sendCommand($socket, "DATA");
                $data_response = $this->readResponse($socket);
                
                if (!$this->isSuccessResponse($data_response)) {
                    fclose($socket);
                    $this->errorInfo = "DATA failed: $data_response";
                    return false;
                }
                
                // Send email headers and body
                $headers = "From: {$this->fromName} <{$this->from}>\r\n";
                $headers .= "To: {$to}\r\n";
                $headers .= "Subject: {$this->subject}\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                
                $email_data = $headers . "\r\n" . $this->body . "\r\n.\r\n";
                $this->sendCommand($socket, $email_data);
                
                $end_response = $this->readResponse($socket);
                
                if (!$this->isSuccessResponse($end_response)) {
                    fclose($socket);
                    $this->errorInfo = "Email data failed: $end_response";
                    return false;
                }
                
                if ($this->debug) {
                    echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                    echo "<strong>Email sent to: $to</strong><br>";
                    echo "</div>";
                }
            }
            
            // Quit
            $this->sendCommand($socket, "QUIT");
            $quit_response = $this->readResponse($socket);
            
            fclose($socket);
            
            if ($this->debug) {
                echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>SMTP session completed successfully!</strong><br>";
                echo "Recipients: " . count($this->to) . "<br>";
                echo "</div>";
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->errorInfo = 'SMTP Error: ' . $e->getMessage();
            return false;
        }
    }
    
    private function sendCommand($socket, $command) {
        fwrite($socket, $command . "\r\n");
        if ($this->debug) {
            echo "> " . htmlspecialchars($command) . "<br>";
        }
    }
    
    private function readResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        
        if ($this->debug) {
            echo "< " . htmlspecialchars(trim($response)) . "<br>";
        }
        
        return trim($response);
    }
    
    private function isSuccessResponse($response) {
        return substr($response, 0, 3) === '220' || substr($response, 0, 3) === '250' || substr($response, 0, 3) === '235' || substr($response, 0, 3) === '334' || substr($response, 0, 3) === '354';
    }
    
    public function getErrorInfo() {
        return $this->errorInfo;
    }
}
