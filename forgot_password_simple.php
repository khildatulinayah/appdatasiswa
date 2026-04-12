<?php
// Forgot Password with Email Verification Code (Simple Version)
session_start();

require_once __DIR__ . '/helper/simple_security_helper.php';
require_once __DIR__ . '/helper/password_reset_helper.php';

// Generate simple CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Simple rate limiting using session
if (!isset($_SESSION['forgot_password_attempts'])) {
    $_SESSION['forgot_password_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        header('Location: forgot_password_simple.php?error=csrf_invalid');
        exit();
    }
    
    // Simple rate limiting (max 3 attempts per 5 minutes)
    $current_time = time();
    if ($current_time - $_SESSION['last_attempt_time'] < 300) { // 5 minutes
        if ($_SESSION['forgot_password_attempts'] >= 3) {
            header('Location: forgot_password_simple.php?error=rate_limit_exceeded');
            exit();
        }
        $_SESSION['forgot_password_attempts']++;
    } else {
        $_SESSION['forgot_password_attempts'] = 1;
        $_SESSION['last_attempt_time'] = $current_time;
    }
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: forgot_password_simple.php?error=invalid_email');
        exit();
    }
    
    // Create password reset request
    $result = create_password_reset_request($email);
    
    if ($result['success']) {
        // Send email
        $email_sent = send_password_reset_email(
            $result['email'], 
            $result['verification_code'], 
            $result['user'], 
            $result['expires_at']
        );
        
        if ($email_sent) {
            // Store token in session for verification
            $_SESSION['reset_token'] = $result['token'];
            $_SESSION['reset_email'] = $result['email'];
            
            header('Location: forgot_password_simple.php?success=code_sent');
            exit();
        } else {
            // Email failed but still show success with development mode code
            $_SESSION['reset_token'] = $result['token'];
            $_SESSION['reset_email'] = $result['email'];
            $_SESSION['verification_code'] = $result['verification_code'];
            
            header('Location: forgot_password_simple.php?success=email_failed');
            exit();
        }
    } else {
        // Always show success message for security (don't reveal if email exists)
        header('Location: forgot_password_simple.php?success=code_sent');
        exit();
    }
}

// Handle verification code submission
if (isset($_POST['verify_code'])) {
    $token = $_SESSION['reset_token'] ?? '';
    $verification_code = trim($_POST['verification_code'] ?? '');
    
    if (empty($token) || empty($verification_code)) {
        header('Location: forgot_password_simple.php?error=missing_code');
        exit();
    }
    
    $result = verify_reset_request($token, $verification_code);
    
    if ($result['success']) {
        $_SESSION['reset_verified'] = true;
        header('Location: forgot_password_simple.php?step=reset');
        exit();
    } else {
        header('Location: forgot_password_simple.php?error=invalid_code');
        exit();
    }
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    if (!($_SESSION['reset_verified'] ?? false)) {
        header('Location: forgot_password_simple.php?error=not_verified');
        exit();
    }
    
    $token = $_SESSION['reset_token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if (empty($new_password) || empty($confirm_password)) {
        header('Location: forgot_password_simple.php?error=empty_password');
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        header('Location: forgot_password_simple.php?error=password_mismatch');
        exit();
    }
    
    $result = reset_password($token, $new_password);
    
    if ($result['success']) {
        // Clear session
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_verified']);
        unset($_SESSION['csrf_token']);
        
        header('Location: login.php?success=reset');
        exit();
    } else {
        header('Location: forgot_password_simple.php?error=reset_failed');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Aplikasi Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="90" class="mb-3">
                            <h3 class="fw-bold">Lupa Password</h3>
                            <p class="text-muted">Reset password Anda dengan aman</p>
                        </div>

                        <!-- Error/Success Messages -->
                        <?php
                        if (isset($_GET['error'])) {
                            $error = $_GET['error'];
                            $alert_class = 'danger';
                            $alert_message = '';
                            
                            switch ($error) {
                                case 'csrf_invalid':
                                    $alert_message = 'Sesi tidak valid. Silakan refresh halaman.';
                                    break;
                                case 'rate_limit_exceeded':
                                    $alert_message = 'Terlalu banyak permintaan. Silakan tunggu 5 menit.';
                                    break;
                                case 'invalid_email':
                                    $alert_message = 'Email tidak valid. Silakan masukkan email yang benar.';
                                    break;
                                case 'email_failed':
                                    $alert_message = 'Gagal mengirim email. Silakan coba lagi.';
                                    break;
                                case 'missing_code':
                                    $alert_message = 'Kode verifikasi diperlukan.';
                                    break;
                                case 'invalid_code':
                                    $alert_message = 'Kode verifikasi salah. Silakan coba lagi.';
                                    break;
                                case 'not_verified':
                                    $alert_message = 'Verifikasi diperlukan. Silakan mulai ulang proses.';
                                    break;
                                case 'empty_password':
                                    $alert_message = 'Password tidak boleh kosong.';
                                    break;
                                case 'password_mismatch':
                                    $alert_message = 'Password tidak cocok.';
                                    break;
                                case 'reset_failed':
                                    $alert_message = 'Gagal reset password. Silakan coba lagi.';
                                    break;
                                default:
                                    $alert_message = 'Terjadi kesalahan. Silakan coba lagi.';
                            }
                            
                            echo "<div class='alert alert-$alert_class alert-dismissible fade show' role='alert'>
                                    <i class='fas fa-exclamation-triangle me-2'></i>$alert_message
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                  </div>";
                        }
                        
                        if (isset($_GET['success'])) {
                            $success = $_GET['success'];
                            $alert_message = '';
                            
                            switch ($success) {
                                case 'code_sent':
                                    $alert_message = 'Kode verifikasi telah dikirim ke email Anda. Silakan periksa inbox/spam.';
                                    break;
                                case 'email_failed':
                                    $alert_message = 'Email gagal dikirim. Kode verifikasi: <strong style="font-size: 18px; color: #007bff;">' . ($_SESSION['verification_code'] ?? 'N/A') . '</strong>';
                                    break;
                                default:
                                    $alert_message = 'Operasi berhasil.';
                            }
                            
                            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                    <i class='fas fa-check-circle me-2'></i>$alert_message
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                  </div>";
                        }
                        ?>

                        <?php if (!isset($_GET['step']) || $_GET['step'] !== 'reset'): ?>
                            <!-- Step 1: Email Request -->
                            <?php if (!isset($_SESSION['reset_token'])): ?>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Masukkan email Anda" required>
                                    <small class="text-muted">Masukkan email yang terdaftar di akun Anda</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Kode Verifikasi
                                    </button>
                                </div>
                            </form>
                            <?php else: ?>
                            <!-- Step 2: Verification Code -->
                            <form method="post">
                                <div class="mb-3">
                                    <label for="verification_code" class="form-label">
                                        <i class="fas fa-shield-alt me-2"></i>Kode Verifikasi
                                    </label>
                                    <input type="text" class="form-control text-center" id="verification_code" 
                                           name="verification_code" placeholder="Masukkan 6 digit kode" 
                                           maxlength="6" pattern="[0-9]{6}" required>
                                    <small class="text-muted">Masukkan kode 6 digit yang dikirim ke <?php echo htmlspecialchars($_SESSION['reset_email']); ?></small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="verify_code" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i>Verifikasi Kode
                                    </button>
                                </div>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Tidak menerima kode? 
                                        <a href="forgot_password_simple.php?reset=1" class="text-decoration-none">Kirim ulang</a>
                                    </small>
                                </div>
                            </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Step 3: Reset Password -->
                            <form method="post">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password Baru
                                    </label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           placeholder="Masukkan password baru" required>
                                    <small class="text-muted">Minimal 8 karakter dengan huruf besar, kecil, dan angka</small>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Konfirmasi Password
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Konfirmasi password baru" required>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="reset_password" class="btn btn-success btn-lg">
                                        <i class="fas fa-key me-2"></i>Reset Password
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <!-- Back to Login -->
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
