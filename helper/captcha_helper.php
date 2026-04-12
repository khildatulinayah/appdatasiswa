<?php
// Simple CAPTCHA Implementation
// CAPTCHA sederhana namun efektif untuk melindungi form

// session_start() should be called in the main file (login.php)

// Generate simple math CAPTCHA
function generate_math_captcha() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $operators = ['+', '-', '×'];
    $operator = $operators[array_rand($operators)];
    
    switch ($operator) {
        case '+':
            $answer = $num1 + $num2;
            break;
        case '-':
            // Ensure positive result
            if ($num1 < $num2) {
                list($num1, $num2) = [$num2, $num1];
            }
            $answer = $num1 - $num2;
            break;
        case '×':
            $answer = $num1 * $num2;
            break;
    }
    
    $_SESSION['captcha_answer'] = $answer;
    $_SESSION['captcha_time'] = time();
    
    return [
        'question' => "$num1 $operator $num2 = ?",
        'answer' => $answer
    ];
}

// Generate text-based CAPTCHA
function generate_text_captcha($length = 6) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $captcha = substr(str_shuffle($chars), 0, $length);
    
    $_SESSION['captcha_answer'] = $captcha;
    $_SESSION['captcha_time'] = time();
    
    return $captcha;
}

// Create CAPTCHA image
function create_captcha_image($text) {
    // Image dimensions
    $width = 120;
    $height = 40;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg_color = imagecolorallocate($image, 240, 240, 240);
    $text_color = imagecolorallocate($image, 50, 50, 50);
    $line_color = imagecolorallocate($image, 200, 200, 200);
    $dot_color = imagecolorallocate($image, 150, 150, 150);
    
    // Fill background
    imagefill($image, 0, 0, $bg_color);
    
    // Add random lines
    for ($i = 0; $i < 5; $i++) {
        imageline($image, 
            rand(0, $width), rand(0, $height),
            rand(0, $width), rand(0, $height),
            $line_color);
    }
    
    // Add random dots
    for ($i = 0; $i < 50; $i++) {
        imagesetpixel($image, rand(0, $width), rand(0, $height), $dot_color);
    }
    
    // Add text
    $font_size = 16;
    $angle = rand(-5, 5);
    $x = rand(10, 30);
    $y = rand(20, 30);
    
    // Try to use built-in font, otherwise use simple text
    if (function_exists('imagettftext')) {
        imagettftext($image, $font_size, $angle, $x, $y, $text_color, __DIR__ . '/../assets/fonts/arial.ttf', $text);
    } else {
        imagestring($image, 5, $x, $y, $text, $text_color);
    }
    
    // Output image
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

// Validate CAPTCHA answer
function validate_captcha($user_answer) {
    // Check if CAPTCHA exists and is not expired (5 minutes)
    if (!isset($_SESSION['captcha_answer']) || 
        !isset($_SESSION['captcha_time']) || 
        (time() - $_SESSION['captcha_time']) > 300) {
        return false;
    }
    
    $is_valid = (string)$_SESSION['captcha_answer'] === (string)$user_answer;
    
    // Clear CAPTCHA after validation
    unset($_SESSION['captcha_answer']);
    unset($_SESSION['captcha_time']);
    
    return $is_valid;
}

// Check if CAPTCHA should be shown (after failed attempts)
function should_show_captcha() {
    global $mysqli;
    
    // Include security_helper if not already included
    if (!function_exists('get_real_ip')) {
        require_once __DIR__ . '/security_helper.php';
    }
    
    $ip = get_real_ip();
    
    // Check recent failed attempts from this IP
    $recent_time = date('Y-m-d H:i:s', time() - 900); // 15 minutes
    $query = "SELECT COUNT(*) as failed_count FROM login_attempts 
              WHERE ip_address = ? AND success = 0 AND attempt_time > ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "ss", $ip, $recent_time);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    // Show CAPTCHA after 3 failed attempts
    return ($row['failed_count'] ?? 0) >= 3;
}

// get_real_ip() function is already defined in security_helper.php

?>
