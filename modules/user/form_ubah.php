<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../helper/auth_helper.php";
require_admin();
require_once __DIR__ . "/../../config/database.php";

// ambil id user dari URL
if (!isset($_GET['id'])) {
    header('location: ../../main.php?module=user');
    exit();
}

$id_user = mysqli_real_escape_string($mysqli, $_GET['id']);
$query = mysqli_query($mysqli, "SELECT id_user, nama_lengkap, username, email, level FROM user WHERE id_user='$id_user'")
    or die('Ada kesalahan pada query tampil user : ' . mysqli_error($mysqli));

if (mysqli_num_rows($query) !== 1) {
    header('location: ../../main.php?module=user');
    exit();
}
$user = mysqli_fetch_assoc($query);
?>

<div class="d-flex flex-column flex-lg-row mb-4">
    <div class="flex-grow-1 d-flex align-items-center">
        <i class="fa-solid fa-user-gear icon-title"></i>
        <h3>Ubah Role Pengguna</h3>
    </div>
    <div class="ms-5 ms-lg-0 pt-lg-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?module=dashboard" class="text-dark text-decoration-none"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item"><a href="?module=user" class="text-dark text-decoration-none">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Role</li>
            </ol>
        </nav>
    </div>
</div>

<div class="bg-white rounded-4 shadow-sm p-4 mb-4">
    <form action="/modules/user/proses_ubah.php" method="post" class="needs-validation" novalidate>
        <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($user['id_user']); ?>">

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>

        <div class="mb-3">
            <label for="level" class="form-label">Role Akun</label>
            <select class="form-select" id="level" name="level" required>
                <option value="user" <?php echo $user['level'] === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $user['level'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" name="simpan" class="btn btn-brand px-4">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan
            </button>
            <a href="?module=user" class="btn btn-outline-secondary px-4">Kembali</a>
        </div>
    </form>
</div>

