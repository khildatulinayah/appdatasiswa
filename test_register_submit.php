<?php
/**
 * Test register submit dengan path yang benar
 */

echo "<h2>Test Register Submit</h2>";

if ($_POST) {
    echo "<h3>Form Submitted Successfully!</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test include proses_register
    echo "<h3>Testing proses_register.php include:</h3>";
    
    // Simulate POST data for testing
    $_POST['nama_lengkap'] = $_POST['nama_lengkap'] ?? 'Test User';
    $_POST['username'] = $_POST['username'] ?? 'testuser';
    $_POST['email'] = $_POST['email'] ?? 'test@example.com';
    $_POST['password'] = $_POST['password'] ?? 'test123';
    $_POST['password2'] = $_POST['password2'] ?? 'test123';
    
    // Check if file can be included
    if (file_exists('modules/auth/proses_register.php')) {
        echo "<p style='color: green;'>File exists, trying to include...</p>";
        
        try {
            include 'modules/auth/proses_register.php';
            echo "<p style='color: green;'>Include successful!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Include failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>File not found!</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='test_register_submit.php'>Back</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Register Submit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>Test Register Form</h3>
                        <p class="text-muted">Form action: modules/auth/proses_register.php</p>
                        
                        <form action="modules/auth/proses_register.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password2" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                        
                        <hr>
                        <div class="alert alert-info">
                            <strong>Debug Info:</strong><br>
                            Current Directory: <?php echo getcwd(); ?><br>
                            Form Target: modules/auth/proses_register.php<br>
                            File Exists: <?php echo file_exists('modules/auth/proses_register.php') ? 'YES' : 'NO'; ?>
                        </div>
                        
                        <p><a href="register.php">Original Register</a> | <a href="debug_path.php">Debug Path</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
