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
        <i class="fa-solid fa-users-gear icon-title"></i>
        <h3>Manajemen Pengguna</h3>
    </div>
    <div class="ms-5 ms-lg-0 pt-lg-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?module=dashboard" class="text-dark text-decoration-none"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item"><a href="?module=user" class="text-dark text-decoration-none">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daftar Akun</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (isset($_GET['pesan']) && $_GET['pesan'] == 1) : ?>
    <div class="alert alert-success alert-dismissible rounded-4 fade show mb-4" role="alert">
        <strong><i class="fa-solid fa-circle-check me-2"></i>Sukses!</strong> Role pengguna berhasil diperbarui.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 0) : ?>
    <div class="alert alert-danger alert-dismissible rounded-4 fade show mb-4" role="alert">
        <strong><i class="fa-solid fa-circle-exclamation me-2"></i>Error!</strong> Gagal mengubah role pengguna.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="bg-white rounded-4 shadow-sm p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($mysqli, "SELECT id_user, nama_lengkap, username, email, level FROM user ORDER BY id_user DESC")
                    or die('Ada kesalahan pada query tampil pengguna : ' . mysqli_error($mysqli));

                if (mysqli_num_rows($query) > 0) {
                    $no = 1;
                    while ($user = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['level'] === 'admin' ? 'danger' : 'primary'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['level'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="?module=form_ubah_user&id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-brand">
                                    <i class="fa-solid fa-pen-to-square"></i> Ubah Role
                                </a>
                            </td>
                        </tr>
                    <?php }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada akun pengguna.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

