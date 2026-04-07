<?php
// File untuk testing sistem autentikasi
// Hapus file ini setelah testing selesai

require_once "config/database.php";
require_once "helper/auth_helper.php";

session_start();

echo "<h1>Testing Sistem Autentikasi</h1>";

// Test 1: Koneksi Database
echo "<h2>1. Test Koneksi Database</h2>";
if ($mysqli) {
    echo "✅ Koneksi database berhasil<br>";
} else {
    echo "❌ Koneksi database gagal: " . mysqli_connect_error() . "<br>";
}

// Test 2: Cek Tabel User
echo "<h2>2. Test Tabel User</h2>";
$result = mysqli_query($mysqli, "SHOW TABLES LIKE 'user'");
if (mysqli_num_rows($result) > 0) {
    echo "✅ Tabel user exists<br>";
    
    // Cek data user
    $users = mysqli_query($mysqli, "SELECT username, email, level FROM user");
    echo "Data user:<br>";
    while ($user = mysqli_fetch_assoc($users)) {
        echo "- {$user['username']} ({$user['email']}) - Level: {$user['level']}<br>";
    }
} else {
    echo "❌ Tabel user tidak exists. Jalankan SQL: database/create_user_table.sql<br>";
}

// Test 3: Test Password Hashing
echo "<h2>3. Test Password Hashing</h2>";
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "Verify: " . (password_verify($password, $hash) ? "✅ Valid" : "❌ Invalid") . "<br>";

// Test 4: Test Session
echo "<h2>4. Test Session</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session active<br>";
    $_SESSION['test'] = 'Hello World';
    echo "Session test: " . $_SESSION['test'] . "<br>";
} else {
    echo "❌ Session not active<br>";
}

// Test 5: Test Auth Functions
echo "<h2>5. Test Auth Functions</h2>";
echo "is_login(): " . (is_login() ? "true" : "false") . "<br>";
echo "get_user_level(): " . (get_user_level() ?? "null") . "<br>";
echo "is_admin(): " . (is_admin() ? "true" : "false") . "<br>";

// Test 6: Test Form Validation
echo "<h2>6. Test Form Validation</h2>";
$test_email = "test@example.com";
echo "Email validation ($test_email): " . (filter_var($test_email, FILTER_VALIDATE_EMAIL) ? "✅ Valid" : "❌ Invalid") . "<br>";

$test_password = "password123";
echo "Password strength ($test_password): " . (strlen($test_password) >= 6 ? "✅ Valid" : "❌ Too short") . "<br>";

// Test 7: Test File Paths
echo "<h2>7. Test File Paths</h2>";
$files_to_check = [
    'modules/auth/form_login.php',
    'modules/auth/form_register.php',
    'modules/auth/proses_login.php',
    'modules/auth/proses_register.php',
    'helper/auth_helper.php',
    'auth_layout.php',
    'login.php',
    'register.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 8: Test URL Generation
echo "<h2>8. Test URL Generation</h2>";
$base_url = "http://localhost/app-siswa1";
echo "Login URL: <a href='$base_url/login.php' target='_blank'>$base_url/login.php</a><br>";
echo "Register URL: <a href='$base_url/register.php' target='_blank'>$base_url/register.php</a><br>";
echo "Main URL: <a href='$base_url/main.php' target='_blank'>$base_url/main.php</a><br>";

// Test 9: Test Security Functions
echo "<h2>9. Test Security Functions</h2>";
$test_input = "<script>alert('xss')</script>";
echo "Original: $test_input<br>";
echo "Cleaned: " . htmlspecialchars($test_input) . "<br>";

$test_sql = "'; DROP TABLE user; --";
echo "SQL Input: $test_sql<br>";
echo "Escaped: " . mysqli_real_escape_string($mysqli, $test_sql) . "<br>";

echo "<h2>🎉 Testing Complete!</h2>";
echo "<p><strong>Catatan:</strong> Hapus file ini setelah testing selesai untuk alasan keamanan.</p>";

// Link untuk testing manual
echo "<h2>🔗 Manual Testing Links</h2>";
echo "<ul>";
echo "<li><a href='login.php' target='_blank'>Test Login Form</a></li>";
echo "<li><a href='register.php' target='_blank'>Test Register Form</a></li>";
echo "<li><a href='main.php?module=dashboard' target='_blank'>Test Dashboard (should redirect to login)</a></li>";
echo "</ul>";

// Clear session test
unset($_SESSION['test']);
?>

