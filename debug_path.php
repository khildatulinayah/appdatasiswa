<?php
/**
 * Debug path resolution
 */

echo "<h2>Debug Path Resolution</h2>";

echo "<h3>Server Info:</h3>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>PHP Self: " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h3>Current File Path:</h3>";
echo "<p>File: " . __FILE__ . "</p>";
echo "<p>Dir: " . __DIR__ . "</p>";
echo "<p>Getcwd: " . getcwd() . "</p>";

echo "<h3>Path Tests:</h3>";
$paths = [
    'proses_register.php',
    './proses_register.php',
    '../proses_register.php',
    'modules/auth/proses_register.php',
    './modules/auth/proses_register.php',
    '/app-siswa1/modules/auth/proses_register.php',
    $_SERVER['DOCUMENT_ROOT'] . '/app-siswa1/modules/auth/proses_register.php'
];

foreach ($paths as $path) {
    $exists = file_exists($path);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? 'EXISTS' : 'NOT FOUND';
    
    echo "<p style='color: $color;'>";
    echo "Path: '$path' - $status";
    if ($exists) {
        echo " (Real: " . realpath($path) . ")";
    }
    echo "</p>";
}

echo "<h3>Form Tests:</h3>";
echo "<form method='post'>";
echo "<input type='hidden' name='test' value='1'>";
echo "<button type='submit' name='action' value='relative'>Test Relative: proses_register.php</button>";
echo "</form>";

echo "<form method='post'>";
echo "<input type='hidden' name='test' value='2'>";
echo "<button type='submit' name='action' value='absolute'>Test Absolute: /app-siswa1/modules/auth/proses_register.php</button>";
echo "</form>";

if ($_POST) {
    echo "<h3>Form Submitted:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    if ($_POST['action'] == 'relative') {
        echo "<p>Trying to include: proses_register.php</p>";
        if (file_exists('proses_register.php')) {
            echo "<p style='color: green;'>File exists!</p>";
        } else {
            echo "<p style='color: red;'>File not found!</p>";
        }
    }
}

echo "<hr>";
echo "<p><a href='register_test.php'>Test Register</a> | <a href='register.php'>Original Register</a></p>";
?>
