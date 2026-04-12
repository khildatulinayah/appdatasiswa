<?php
/**
 * Test register path
 */

echo "<h2>Test Register Path</h2>";

echo "<h3>Current Directory:</h3>";
echo "<p>" . getcwd() . "</p>";

echo "<h3>File Check:</h3>";
$files_to_check = [
    'modules/auth/form_register.php',
    'modules/auth/proses_register.php',
    'register.php',
    'auth_layout.php',
    'content.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? 'EXISTS' : 'NOT FOUND';
    
    echo "<p style='color: $color;'>$file - $status</p>";
}

echo "<h3>URL Test:</h3>";
echo "<p><a href='register.php'>Register Page</a></p>";
echo "<p><a href='modules/auth/form_register.php'>Direct Form Register</a></p>";
echo "<p><a href='modules/auth/proses_register.php'>Direct Proses Register</a></p>";

echo "<h3>Form Action Test:</h3>";
echo "<form action='modules/auth/proses_register.php' method='post'>";
echo "<input type='hidden' name='test' value='1'>";
echo "<button type='submit'>Test Absolute Path</button>";
echo "</form>";

echo "<form action='modules/auth/proses_register.php' method='post'>";
echo "<input type='hidden' name='test' value='2'>";
echo "<button type='submit'>Test Relative Path</button>";
echo "</form>";

if ($_POST) {
    echo "<h3>Form Submitted:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}
?>
