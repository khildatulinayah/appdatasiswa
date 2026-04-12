<?php
// CAPTCHA Image Generator
// Endpoint untuk generate CAPTCHA image

session_start();
require_once 'helper/captcha_helper.php';

// Generate new CAPTCHA
$captcha_text = generate_text_captcha();

// Create and display image
create_captcha_image($captcha_text);
?>
