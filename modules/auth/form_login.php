<!-- Form Login -->
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="90" class="mb-3">
                    <h3 class="fw-bold">Aplikasi Siswa</h3>
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
                            $alert_message = 'Email dan password tidak boleh kosong!';
                            break;
                        case 'invalid':
                            $alert_message = 'Email atau password salah!';
                            break;
                        case 'not_logged_in':
                            $alert_message = 'Anda harus login terlebih dahulu!';
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
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>Registrasi berhasil! Silakan login.
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }
                ?>

                <!-- Form Login -->
                <form action="/modules/auth/proses_login.php" method="post" id="formLogin">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan email" required>
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

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>

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

