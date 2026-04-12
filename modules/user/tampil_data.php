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

<?php if (isset($_GET['pesan'])) : ?>
    <?php
        $alertClass = 'success';
        $alertIcon = 'fa-circle-check';
        $alertTitle = 'Sukses!';
        $alertMessage = '';
        switch ($_GET['pesan']) {
            case '1':
                $alertMessage = 'Data pengguna berhasil diperbarui.';
                break;
            case '2':
                $alertMessage = 'Pengguna baru berhasil ditambahkan.';
                break;
            case '3':
                $alertMessage = 'Pengguna berhasil dihapus.';
                break;
            case '4':
                $alertClass = 'danger';
                $alertIcon = 'fa-circle-exclamation';
                $alertTitle = 'Error!';
                $alertMessage = 'Tidak dapat menghapus akun Anda sendiri.';
                break;
            default:
                $alertClass = 'danger';
                $alertIcon = 'fa-circle-exclamation';
                $alertTitle = 'Error!';
                $alertMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                break;
        }
    ?>
    <div class="alert alert-<?php echo $alertClass; ?> alert-dismissible rounded-4 fade show mb-4" role="alert">
        <strong><i class="fa-solid <?php echo $alertIcon; ?> me-2"></i><?php echo $alertTitle; ?></strong> <?php echo htmlspecialchars($alertMessage); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-end mb-4">
    <a href="?module=form_entri_user" class="btn btn-brand btn-sm">
        <i class="fa-solid fa-plus me-2"></i> Tambah Pengguna
    </a>
</div>

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
                                <a href="?module=form_ubah_user&id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-brand mb-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Ubah
                                </a>
                                <?php if ($_SESSION['id_user'] != $user['id_user']) : ?>
                                    <a href="/modules/user/proses_hapus.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>
                                <?php else : ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary disabled">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                <?php endif; ?>
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

