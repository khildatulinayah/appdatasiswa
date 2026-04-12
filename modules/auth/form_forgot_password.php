<!-- Form Forgot Password -->
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="90" class="mb-3">
                    <h3 class="fw-bold">Lupa Password</h3>
                    <p class="text-muted">Masukkan email terdaftar untuk mengatur ulang password Anda.</p>
                </div>

                <?php
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    $alert_message = '';
                    switch ($error) {
                        case 'empty':
                            $alert_message = 'Email tidak boleh kosong!';
                            break;
                        case 'invalid_email':
                            $alert_message = 'Format email tidak valid!';
                            break;
                        default:
                            $alert_message = 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='fas fa-exclamation-triangle me-2'></i>$alert_message
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }

                if (isset($_GET['status']) && $_GET['status'] === 'sent') {
                    $message = 'Jika email terdaftar, tautan reset password telah diproses. Periksa kotak masuk atau spam.';
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>$message
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";

                    if (!empty($_GET['link'])) {
                        $reset_link = htmlspecialchars($_GET['link']);
                        echo "<div class='alert alert-info' role='alert'>
                                Tautan reset langsung: <a href='$reset_link'>$reset_link</a>
                              </div>";
                        echo "<div class='alert alert-warning' role='alert'>
                                Jika email tidak terkirim, salin tautan di atas dan buka langsung di browser.
                              </div>";
                    } else {
                        echo "<div class='alert alert-warning' role='alert'>
                                Jika email tidak terkirim, kemungkinan mail server lokal belum terkonfigurasi.
                              </div>";
                    }
                }
                ?>

                <form action="<?php echo get_base_url(); ?>/modules/auth/proses_forgot_password.php" method="post" id="formForgotPassword">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Terdaftar
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan email aktif" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Tautan Reset
                        </button>
                    </div>
                </form>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formForgotPassword');
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Format email tidak valid!');
            return false;
        }
    });
});
</script>
