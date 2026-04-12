<?php
// Security Helper Functions - Brute Force Protection System
// Sistem keamanan komprehensif untuk melindungi aplikasi dari berbagai serangan

require_once __DIR__ . '/../config/database.php';

// Security Configuration
define('MAX_LOGIN_ATTEMPTS', 5); // Maksimal percobaan login
define('LOGIN_ATTEMPT_WINDOW', 900); // 15 menit dalam detik
define('ACCOUNT_LOCKOUT_DURATION', 1800); // 30 menit lockout
define('IP_BLOCK_THRESHOLD', 10); // Maksimal attempt dari IP
define('IP_BLOCK_DURATION', 3600); // 1 jam block IP
define('RATE_LIMIT_REQUESTS', 100); // Maksimal request per window
define('RATE_LIMIT_WINDOW', 300); // 5 menit window

// Get real IP address
function get_real_ip() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

// Check if IP is blocked
function is_ip_blocked($ip = null) {
    global $mysqli;
    $ip = $ip ?: get_real_ip();
    
    $query = "SELECT * FROM blocked_ips WHERE ip_address = ? AND (blocked_until IS NULL OR blocked_until > NOW())";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $ip);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}

// Block IP address
function block_ip($ip, $reason, $duration = null, $permanent = false) {
    global $mysqli;
    
    $blocked_until = $permanent ? null : date('Y-m-d H:i:s', time() + ($duration ?: IP_BLOCK_DURATION));
    
    $query = "INSERT INTO blocked_ips (ip_address, reason, blocked_until, is_permanent) 
              VALUES (?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE 
              reason = VALUES(reason), 
              blocked_until = VALUES(blocked_until), 
              is_permanent = VALUES(is_permanent),
              attempts_count = attempts_count + 1";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $ip, $reason, $blocked_until, $permanent);
    mysqli_stmt_execute($stmt);
    
    log_security_event('ip_blocked', 'high', $ip, null, "IP blocked: $reason");
}

// Check if account is locked
function is_account_locked($username) {
    global $mysqli;
    
    $query = "SELECT * FROM locked_accounts WHERE username = ? AND (locked_until IS NULL OR locked_until > NOW())";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}

// Lock user account
function lock_account($user_id, $username, $reason, $duration = null, $permanent = false) {
    global $mysqli;
    
    $locked_until = $permanent ? null : date('Y-m-d H:i:s', time() + ($duration ?: ACCOUNT_LOCKOUT_DURATION));
    
    $query = "INSERT INTO locked_accounts (user_id, username, lock_reason, locked_until, is_permanent) 
              VALUES (?, ?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE 
              lock_reason = VALUES(lock_reason), 
              locked_until = VALUES(locked_until), 
              is_permanent = VALUES(is_permanent),
              failed_attempts = failed_attempts + 1";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "isssi", $user_id, $username, $reason, $locked_until, $permanent);
    mysqli_stmt_execute($stmt);
    
    log_security_event('account_locked', 'high', get_real_ip(), $user_id, "Account locked: $reason");
}

// Check rate limiting
function check_rate_limit($endpoint, $max_requests = RATE_LIMIT_REQUESTS, $window = RATE_LIMIT_WINDOW) {
    global $mysqli;
    $ip = get_real_ip();
    $window_start = date('Y-m-d H:i:s', time() - $window);
    
    // Clean old rate limit records
    $cleanup_query = "DELETE FROM rate_limits WHERE window_start < ?";
    $cleanup_stmt = mysqli_prepare($mysqli, $cleanup_query);
    mysqli_stmt_bind_param($cleanup_stmt, "s", $window_start);
    mysqli_stmt_execute($cleanup_stmt);
    
    // Check current requests
    $query = "SELECT SUM(request_count) as total_requests FROM rate_limits 
              WHERE ip_address = ? AND endpoint = ? AND window_start >= ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "sss", $ip, $endpoint, $window_start);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    $total_requests = $row['total_requests'] ?? 0;
    
    if ($total_requests >= $max_requests) {
        log_security_event('rate_limit_exceeded', 'medium', $ip, null, "Rate limit exceeded for $endpoint");
        return false;
    }
    
    // Record this request
    $current_window = date('Y-m-d H:i:s', time() - (time() % $window));
    $insert_query = "INSERT INTO rate_limits (ip_address, endpoint, window_start, request_count) 
                    VALUES (?, ?, ?, 1) 
                    ON DUPLICATE KEY UPDATE 
                    request_count = request_count + 1";
    $insert_stmt = mysqli_prepare($mysqli, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "sss", $ip, $endpoint, $current_window);
    mysqli_stmt_execute($insert_stmt);
    
    return true;
}

// Log login attempt
function log_login_attempt($username, $success, $attempt_type = 'local') {
    global $mysqli;
    $ip = get_real_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $query = "INSERT INTO login_attempts (ip_address, username, success, user_agent, attempt_type) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "ssiss", $ip, $username, $success, $user_agent, $attempt_type);
    mysqli_stmt_execute($stmt);
    
    // Check for brute force patterns
    check_brute_force_patterns($ip, $username, $attempt_type);
    
    // Log security event for failed attempts
    if (!$success) {
        log_security_event('login_failed', 'medium', $ip, null, "Failed login attempt for: $username");
    }
}

// Check brute force patterns
function check_brute_force_patterns($ip, $username, $attempt_type) {
    global $mysqli;
    
    // Check recent failed attempts from this IP
    $recent_time = date('Y-m-d H:i:s', time() - LOGIN_ATTEMPT_WINDOW);
    $query = "SELECT COUNT(*) as failed_count FROM login_attempts 
              WHERE ip_address = ? AND success = 0 AND attempt_time > ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "ss", $ip, $recent_time);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $failed_count = $row['failed_count'];
    
    // Block IP if too many failed attempts
    if ($failed_count >= IP_BLOCK_THRESHOLD) {
        block_ip($ip, "Too many failed login attempts ($failed_count)", IP_BLOCK_DURATION);
        return true;
    }
    
    // Check attempts for specific username
    if ($username) {
        $username_query = "SELECT COUNT(*) as username_attempts FROM login_attempts 
                        WHERE username = ? AND success = 0 AND attempt_time > ?";
        $username_stmt = mysqli_prepare($mysqli, $username_query);
        mysqli_stmt_bind_param($username_stmt, "ss", $username, $recent_time);
        mysqli_stmt_execute($username_stmt);
        $username_result = mysqli_stmt_get_result($username_stmt);
        $username_row = mysqli_fetch_assoc($username_result);
        $username_attempts = $username_row['username_attempts'];
        
        // Lock account if too many failed attempts for this username
        if ($username_attempts >= MAX_LOGIN_ATTEMPTS) {
            // Get user ID
            $user_query = "SELECT id_user FROM user WHERE username = ? OR email = ?";
            $user_stmt = mysqli_prepare($mysqli, $user_query);
            mysqli_stmt_bind_param($user_stmt, "ss", $username, $username);
            mysqli_stmt_execute($user_stmt);
            $user_result = mysqli_stmt_get_result($user_stmt);
            $user_row = mysqli_fetch_assoc($user_result);
            
            if ($user_row) {
                lock_account($user_row['id_user'], $username, "Too many failed login attempts ($username_attempts)", ACCOUNT_LOCKOUT_DURATION);
            }
            return true;
        }
    }
    
    return false;
}

// Log security events
function log_security_event($event_type, $severity, $ip_address, $user_id = null, $description = null) {
    global $mysqli;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $query = "INSERT INTO security_events (event_type, severity, ip_address, user_agent, user_id, description) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "ssssis", $event_type, $severity, $ip_address, $user_agent, $user_id, $description);
    mysqli_stmt_execute($stmt);
}

// Get security statistics
function get_security_stats() {
    global $mysqli;
    
    $stats = [];
    
    // Recent failed logins
    $query = "SELECT COUNT(*) as count FROM login_attempts WHERE success = 0 AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $result = mysqli_query($mysqli, $query);
    $stats['recent_failed_logins'] = mysqli_fetch_assoc($result)['count'];
    
    // Blocked IPs
    $result = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM blocked_ips WHERE (blocked_until IS NULL OR blocked_until > NOW())");
    $stats['blocked_ips'] = mysqli_fetch_assoc($result)['count'];
    
    // Locked accounts
    $result = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM locked_accounts WHERE (locked_until IS NULL OR locked_until > NOW())");
    $stats['locked_accounts'] = mysqli_fetch_assoc($result)['count'];
    
    // Security events in last 24h
    $result = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM security_events WHERE event_time > DATE_SUB(NOW(), INTERVAL 1 DAY)");
    $stats['security_events_24h'] = mysqli_fetch_assoc($result)['count'];
    
    return $stats;
}

// Generate secure CSRF token with expiration
function generate_secure_csrf_token($expire_minutes = 60) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $token = bin2hex(random_bytes(32));
    $expires = time() + ($expire_minutes * 60);
    
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_expires'] = $expires;
    
    return $token;
}

// Validate CSRF token with expiration
function validate_secure_csrf_token($token) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && 
           isset($_SESSION['csrf_expires']) && 
           hash_equals($_SESSION['csrf_token'], $token) && 
           $_SESSION['csrf_expires'] > time();
}

// Clean up expired security records
function cleanup_security_records() {
    global $mysqli;
    
    // Clean old login attempts
    mysqli_query($mysqli, "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 7 DAY)");
    
    // Clean expired blocked IPs
    mysqli_query($mysqli, "DELETE FROM blocked_ips WHERE blocked_until IS NOT NULL AND blocked_until < NOW()");
    
    // Clean expired locked accounts
    mysqli_query($mysqli, "DELETE FROM locked_accounts WHERE locked_until IS NOT NULL AND locked_until < NOW()");
    
    // Clean old rate limits
    mysqli_query($mysqli, "DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 DAY)");
    
    // Clean old security events
    mysqli_query($mysqli, "DELETE FROM security_events WHERE event_time < DATE_SUB(NOW(), INTERVAL 30 DAY)");
}

?>
