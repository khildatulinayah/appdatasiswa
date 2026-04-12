<!-- Form Reset Password -->
<?php
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$valid_token = false;

if (!empty($token)) {
    $create_table = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY token_unique (token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    mysqli_query($mysqli, $create_table);

    $safe_token = mysqli_real_escape_string($mysqli, $token);
    $query = "SELECT user_id FROM password_resets WHERE token = '$safe_token' AND expires_at >= NOW() LIMIT 1";
    $result = mysqli_query($mysqli, $query);
    if ($result && mysqli_num_rows($result) === 1) {
        $valid_token = true;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="90" class="mb-3">
                    <h3 class="fw-bold">Atur Ulang Password</h3>
                    <p class="text-muted">Masukkan password baru untuk akun Anda.</p>
                </div>

                <?php
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    $alert_message = '';
                    switch ($error) {
                        case 'empty':
                            $alert_message = 'Semua field harus diisi!';
                            break;
                        case 'password_mismatch':
                            $alert_message = 'Password dan konfirmasi password tidak cocok!';
                            break;
                        case 'password_weak':
                            $alert_message = 'Password harus 8-32 karakter dan mengandung huruf besar, huruf kecil, serta angka!';
                            break;
                        case 'invalid_token':
                            $alert_message = 'Tautan reset tidak valid atau sudah kadaluarsa.';
                            break;
                        default:
                            $alert_message = 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='fas fa-exclamation-triangle me-2'></i>$alert_message
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }

                if (!$valid_token) {
                    echo "<div class='alert alert-warning' role='alert'>
                            Tautan reset tidak ditemukan atau sudah kadaluarsa. Silakan ajukan permintaan reset ulang.
                          </div>";
                }
                ?>

                <?php if ($valid_token): ?>
                <form action="<?php echo get_base_url(); ?>/modules/auth/proses_reset_password.php" method="post" id="formResetPassword">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password Baru
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="8-32 karakter, huruf besar kecil, angka" required minlength="8" maxlength="32" pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,32}">
                        <small class="text-muted">8-32 karakter, harus mengandung huruf besar, huruf kecil, dan angka</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">
                            <i class="fas fa-lock me-2"></i>Konfirmasi Password
                        </label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                               placeholder="Ulangi password" required minlength="8" maxlength="32">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-key me-2"></i>Atur Ulang Password
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <p class="mb-0">
                        <a href="login.php" class="text-decoration-none">
                            <strong>Kembali ke login</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($valid_token): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formResetPassword');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,32}$/;

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return false;
        }

        if (!passwordRegex.test(password)) {
            e.preventDefault();
            alert('Password harus 8-32 karakter dan mengandung huruf besar, huruf kecil, serta angka!');
            return false;
        }
    });
});
</script>
<?php endif; ?>
