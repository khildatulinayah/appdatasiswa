<?php
// Simple Security Helper Functions (tanpa database dependency)

// Clean input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Get real IP address
function get_real_ip() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

// Check password strength
function is_password_strong($password) {
    if (strlen($password) < 8) {
        return false;
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    return true;
}

// Generate secure random string
function generate_secure_string($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Generate CSRF token
function generate_simple_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generate_secure_string(32);
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validate_simple_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Simple rate limiting using session
function check_simple_rate_limit($action, $max_attempts = 5, $window_seconds = 300) {
    $key = $action . '_attempts';
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [];
    }
    
    $current_time = time();
    
    // Clean old attempts
    $_SESSION[$key] = array_filter($_SESSION[$key], function($timestamp) use ($current_time, $window_seconds) {
        return $current_time - $timestamp < $window_seconds;
    });
    
    // Check if exceeded
    if (count($_SESSION[$key]) >= $max_attempts) {
        return false;
    }
    
    // Add current attempt
    $_SESSION[$key][] = $current_time;
    
    return true;
}

// Log security event (simple version)
function log_security_event($event_type, $severity, $ip, $user_id = null, $description = '') {
    $log_message = sprintf(
        "[%s] %s - %s - IP: %s - User: %s - %s",
        date('Y-m-d H:i:s'),
        strtoupper($severity),
        $event_type,
        $ip,
        $user_id ?? 'N/A',
        $description
    );
    
    error_log($log_message);
}
?>
