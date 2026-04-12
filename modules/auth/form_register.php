<!-- Form Register -->
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="90" class="mb-3">
                    <h3 class="fw-bold">Buat Akun Baru</h3>
                    <p class="text-muted">Daftar untuk mengakses aplikasi siswa</p>
                </div>

                <!-- Alert -->
                <?php 
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    $alert_class = 'danger';
                    $alert_message = '';
                    
                    switch($error) {
                        case 'empty':
                            $alert_message = 'Semua field harus diisi!';
                            break;
                        case 'username_exists':
                            $alert_message = 'Username sudah digunakan!';
                            break;
                        case 'email_exists':
                            $alert_message = 'Email sudah terdaftar!';
                            break;
                        case 'password_mismatch':
                            $alert_message = 'Password dan konfirmasi password tidak cocok!';
                            break;
                        case 'invalid_email':
                            $alert_message = 'Format email tidak valid!';
                            break;
                        case 'password_weak':
                            $alert_message = 'Password harus 8-32 karakter, dan mengandung huruf besar, huruf kecil, serta angka!';
                            break;
                        case 'password_long':
                            $alert_message = 'Password maksimal 32 karakter!';
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
                            $alert_message = 'Terjadi kesalahan saat memproses registrasi Google. Silakan coba lagi.';
                            break;
                        default:
                            $alert_message = 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    
                    echo "<div class='alert alert-$alert_class alert-dismissible fade show' role='alert'>
                            <i class='fas fa-exclamation-triangle me-2'></i>$alert_message
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }
                ?>

                <!-- Form Register -->
                <form action="modules/auth/proses_register.php" method="post" id="formRegister">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_lengkap" class="form-label">
                                <i class="fas fa-user me-2"></i>Nama Lengkap
                            </label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user-tag me-2"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Masukkan username" required>
                            <small class="text-muted">Username unik untuk login</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan email aktif" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="8-32 karakter, huruf besar kecil, angka" required minlength="8" maxlength="32" pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,32}">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">8-32 karakter, harus mengandung huruf besar, huruf kecil, dan angka</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">
                                <i class="fas fa-lock me-2"></i>Konfirmasi Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                       placeholder="Ulangi password" required minlength="8" maxlength="32">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="fas fa-eye" id="eyeIconConfirm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a>
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="divider d-flex align-items-center my-4">
                    <p class="text-center mx-3 mb-0">ATAU</p>
                </div>

                <!-- Google Register Button -->
                <div class="d-grid gap-2">
                    <a href="<?php echo get_base_url(); ?>/modules/auth/google_register.php" class="btn btn-outline-danger btn-lg">
                        <i class="fab fa-google me-2"></i>Daftar dengan Google
                    </a>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-0">Sudah punya akun? 
                        <a href="login.php" class="text-decoration-none" target="_self" onclick="window.location.href='login.php'; return false;">
                            <strong>Login di sini</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Syarat dan Ketentuan -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Penggunaan Data</h6>
                <p>Data yang Anda masukkan akan digunakan untuk keperluan pengelolaan data siswa.</p>
                
                <h6>2. Keamanan Akun</h6>
                <p>Anda bertanggung jawab untuk menjaga kerahasiaan username dan password Anda.</p>
                
                <h6>3. Privasi</h6>
                <p>Kami berkomitmen untuk melindungi privasi data Anda sesuai dengan kebijakan privasi yang berlaku.</p>
                
                <h6>4. Penggunaan yang Sah</h6>
                <p>Akun hanya boleh digunakan untuk tujuan yang sah dan sesuai dengan peraturan yang berlaku.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
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

    // Toggle confirm password visibility
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirm = document.getElementById('password_confirm');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirm.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIconConfirm.classList.remove('fa-eye');
            eyeIconConfirm.classList.add('fa-eye-slash');
        } else {
            eyeIconConfirm.classList.remove('fa-eye-slash');
            eyeIconConfirm.classList.add('fa-eye');
        }
    });

    // Form validation
    const form = document.getElementById('formRegister');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const email = document.getElementById('email').value;
        
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Format email tidak valid!');
            return false;
        }
        
        // Validate password match
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return false;
        }
        
        // Validate password length and strength
        const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,32}$/;
        if (!passwordRegex.test(password)) {
            e.preventDefault();
            alert('Password harus 8-32 karakter dan mengandung huruf besar, huruf kecil, serta angka!');
