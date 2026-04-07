<?php
// Test User Management Module

session_start();
require_once "config/database.php";
require_once "helper/auth_helper.php";

echo "<h2>Test User Management Module</h2>";

// Test 1: Check if table user exists and has data
echo "<h3>Test 1: Query Semua User</h3>";
$query = mysqli_query($mysqli, "SELECT id_user, nama_lengkap, username, email, level FROM user");
if ($query) {
    $count = mysqli_num_rows($query);
    echo "✓ Query berhasil. Jumlah user: " . $count . "<br>";
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nama</th><th>Username</th><th>Email</th><th>Level</th></tr>";
    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>" . $row['id_user'] . "</td>";
        echo "<td>" . $row['nama_lengkap'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['level'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "✗ Query gagal: " . mysqli_error($mysqli) . "<br>";
}

// Test 2: Check if is_admin function is available
echo "<h3>Test 2: Auth Helper Functions</h3>";
if (function_exists('is_admin')) {
    echo "✓ Function is_admin tersedia<br>";
} else {
    echo "✗ Function is_admin tidak ditemukan<br>";
}

if (function_exists('is_login')) {
    echo "✓ Function is_login tersedia<br>";
} else {
    echo "✗ Function is_login tidak ditemukan<br>";
}

if (function_exists('require_admin')) {
    echo "✓ Function require_admin tersedia<br>";
} else {
    echo "✗ Function require_admin tidak ditemukan<br>";
}

// Test 3: Check if modules/user files exist
echo "<h3>Test 3: File Structure</h3>";
$files = [
    'modules/user/tampil_data.php',
    'modules/user/form_ubah.php',
    'modules/user/proses_ubah.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✓ File $file ada<br>";
    } else {
        echo "✗ File $file tidak ditemukan<br>";
    }
}

// Test 4: Check routing in content.php
echo "<h3>Test 4: Manual Test Links</h3>";
echo "Link ke Manajemen Pengguna: <a href='main.php?module=user' target='_blank'>main.php?module=user</a><br>";
echo "Link ke Form Ubah User (ID=1): <a href='main.php?module=form_ubah_user&id=1' target='_blank'>main.php?module=form_ubah_user&id=1</a><br>";

echo "<h3>Catatan:</h3>";
echo "1. Login dengan akun admin terlebih dahulu<br>";
echo "2. Klik menu 'Manajemen Pengguna' di sidebar<br>";
echo "3. Untuk mengubah role, klik button 'Ubah Role'<br>";
echo "4. Jika error, check console atau network tab di browser DevTools<br>";

?>
