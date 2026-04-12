<?php
// Form Login dengan Security Features
require_once __DIR__ . '/../../helper/security_helper.php';
require_once __DIR__ . '/../../helper/captcha_helper.php';

// Generate CSRF token
$csrf_token = generate_secure_csrf_token();

// Check if CAPTCHA should be shown
$show_captcha = should_show_captcha();
$captcha = $show_captcha ? generate_math_captcha() : null;
?>

<!-- Form Login -->
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="90" class="mb-3">
                    <h3 class="fw-bold">Aplikasi Data Siswa</h3>
                    <p class="text-muted">Silakan login untuk melanjutkan</p>
                </div>

                <!-- Alert -->
                <?php 
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    $alert_class = 'danger';
                    $alert_message = '';
                    
                    switch($error) {
                        case 'empty':
                            $alert_message = 'Username dan password tidak boleh kosong!';
                            break;
                        case 'invalid':
                            $alert_message = 'Username atau password salah!';
                            break;
                        case 'not_logged_in':
                            $alert_message = 'Anda harus login terlebih dahulu!';
                            break;
                        case 'account_locked':
                            $alert_message = 'Akun Anda terkunci karena terlalu banyak percobaan login gagal. Silakan coba lagi dalam 15 menit.';
                            break;
                        case 'reset_required':
                            $alert_message = 'Silakan atur ulang password Anda menggunakan tautan yang dikirim ke email.';
                            break;
                        case 'google_auth_failed':
                            $alert_message = 'Autentikasi Google gagal. Silakan coba lagi.';
                            break;
                        case 'google_denied':
                            $alert_message = 'Anda menolak akses Google. Silakan coba lagi.';
                            break;
                        case 'google_no_code':
                            $alert_message = 'Tidak ada kode otorisasi dari Google. Silakan coba lagi.';
                            break;
                        case 'google_callback_failed':
                            $alert_message = 'Terjadi kesalahan saat memproses login Google. Silakan coba lagi.';
                            break;
                        case 'rate_limit_exceeded':
                            $alert_message = 'Terlalu banyak percobaan login. Silakan coba lagi dalam 5 menit.';
                            break;
                        case 'ip_blocked':
                            $alert_message = 'IP Anda diblokir karena aktivitas mencurigakan. Silakan coba lagi nanti.';
                            break;
                        case 'csrf_invalid':
                            $alert_message = 'Sesi login tidak valid. Silakan refresh halaman dan coba lagi.';
                            break;
                        case 'captcha_invalid':
                            $alert_message = 'Jawaban verifikasi keamanan salah. Silakan coba lagi.';
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
                    if ($success === '1') {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <i class='fas fa-check-circle me-2'></i>Registrasi berhasil! Silakan login.
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
                    } elseif ($success === 'reset') {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <i class='fas fa-check-circle me-2'></i>Password berhasil diubah. Silakan login dengan password baru.
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
                    }
                }
                ?>

                <!-- Form Login -->
                <form action="modules/auth/proses_login_secure.php" method="post" id="formLogin">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Masukkan username" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <!-- CAPTCHA (shown after failed attempts) -->
                    <?php if ($show_captcha): ?>
                    <div class="mb-3">
                        <label for="captcha_answer" class="form-label">
                            <i class="fas fa-shield-alt me-2"></i>Verifikasi Keamanan
                        </label>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="alert alert-info mb-0" style="padding: 8px 12px;">
                                    <strong><?php echo $captcha['question']; ?></strong>
                                </div>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" id="captcha_answer" name="captcha_answer" 
                                       placeholder="Jawaban" required autocomplete="off">
                            </div>
                        </div>
                        <small class="text-muted">Masukkan jawaban untuk verifikasi keamanan</small>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="divider d-flex align-items-center my-4">
                    <p class="text-center mx-3 mb-0">ATAU</p>
                </div>

                <!-- Google Login Button -->
                <div class="d-grid gap-2">
                    <a href="<?php echo get_base_url(); ?>/modules/auth/google_login.php" class="btn btn-outline-danger btn-lg">
                        <i class="fab fa-google me-2"></i>Login dengan Google
                    </a>
                </div>

                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="text-decoration-none">
                        <strong>Lupa password?</strong>
                    </a>
                </div>
                <div class="text-center mt-4">
                    <p class="mb-0">Belum punya akun? 
                        <a href="register.php" class="text-decoration-none" target="_self" onclick="window.location.href='register.php'; return false;">
                            <strong>Daftar di sini</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});
</script>


