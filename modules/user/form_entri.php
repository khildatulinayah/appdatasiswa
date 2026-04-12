<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../helper/auth_helper.php";
require_admin();
require_once __DIR__ . "/../../config/database.php";
?>

<div class="d-flex flex-column flex-lg-row mb-4">
    <div class="flex-grow-1 d-flex align-items-center">
        <i class="fa-solid fa-user-plus icon-title"></i>
        <h3>Tambah Pengguna</h3>
    </div>
    <div class="ms-5 ms-lg-0 pt-lg-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?module=dashboard" class="text-dark text-decoration-none"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item"><a href="?module=user" class="text-dark text-decoration-none">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (isset($_GET['error'])) : ?>
    <?php
        $error = $_GET['error'];
        $alertClass = 'danger';
        $alertTitle = 'Error!';
        $alertMessage = 'Terjadi kesalahan. Silakan coba lagi.';
        switch ($error) {
            case 'empty':
                $alertMessage = 'Semua field harus diisi!';
                break;
            case 'invalid_email':
                $alertMessage = 'Format email tidak valid!';
                break;
            case 'username_exists':
                $alertMessage = 'Username sudah digunakan!';
                break;
            case 'email_exists':
                $alertMessage = 'Email sudah terdaftar!';
                break;
            case 'password_mismatch':
                $alertMessage = 'Password dan konfirmasi password tidak cocok!';
                break;
            case 'password_weak':
                $alertMessage = 'Password harus 8-32 karakter, dan mengandung huruf besar, huruf kecil, serta angka!';
                break;
        }
    ?>
    <div class="alert alert-<?php echo $alertClass; ?> alert-dismissible rounded-4 fade show mb-4" role="alert">
        <strong><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo $alertTitle; ?></strong> <?php echo htmlspecialchars($alertMessage); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="bg-white rounded-4 shadow-sm p-4 mb-4">
    <form action="/modules/user/proses_simpan.php" method="post" class="needs-validation" novalidate>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email aktif" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="8-32 karakter, huruf besar kecil, angka" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Ulangi password" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="level" class="form-label">Role Akun</label>
            <select class="form-select" id="level" name="level" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" name="simpan" class="btn btn-brand px-4">
                <i class="fa-solid fa-plus me-2"></i> Tambah Pengguna
            </button>
            <a href="?module=user" class="btn btn-outline-secondary px-4">Kembali</a>
        </div>
    </form>
</div>
