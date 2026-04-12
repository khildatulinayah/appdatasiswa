<?php
/**
 * Direct Register Test - No layout, just form
 */

echo "<h2>Direct Register Test</h2>";

if ($_POST) {
    echo "<h3>Form Submitted!</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    echo "<p><a href='register_test.php'>Back</a></p>";
    exit;
}
?>

<form action="modules/auth/proses_register.php" method="post">
    <div style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h3>Register Form</h3>
        
        <div class="mb-3">
            <label>Nama Lengkap:</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Konfirmasi Password:</label>
            <input type="password" name="password2" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
        
        <hr>
        <p><a href="register.php">Back to Original Register</a></p>
    </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
?>
