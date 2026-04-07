<?php
// panggil helper auth untuk fungsi autentikasi
require_once "helper/auth_helper.php";

// ambil module dari URL, default ke dashboard jika tidak ada
$module = isset($_GET['module']) ? $_GET['module'] : 'dashboard';

// tampilkan info user yang sedang login
if (is_login()) {
    $user_data = get_user_data();
?>
    <div class="user-info mb-4 p-3 text-center">
        <div class="user-avatar mb-2">
            <i class="fas fa-user-circle fa-3x text-primary"></i>
        </div>
        <div class="user-details">
            <h6 class="mb-1"><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h6>
            <small class="text-muted">
                <i class="fas fa-user-tag me-1"></i>
                <?php echo htmlspecialchars($user_data['username']); ?>
            </small><br>
            <small class="badge bg-<?php echo $user_data['level'] == 'admin' ? 'danger' : 'primary'; ?>">
                <?php echo ucfirst(htmlspecialchars($user_data['level'])); ?>
            </small>
        </div>
    </div>
    
    <hr class="my-3">
<?php
}
?>

<!-- Menu Navigasi -->
<div class="menu-section mb-3">
    <small class="text-muted text-uppercase fw-bold">Menu Utama</small>
</div>

<?php
// pengecekan menu aktif
// jika menu dashboard dipilih, menu dashboard aktif
if ($module == 'dashboard') { ?>
	<div class="item active d-flex align-items-center">
		<i class="fa-solid fa-chart-simple"></i>
		<a href="?module=dashboard"> Dashboard </a>
	</div>
<?php
}
// jika tidak dipilih, menu dashboard tidak aktif
else { ?>
	<div class="item d-flex align-items-center">
		<i class="fa-solid fa-chart-simple"></i>
		<a href="?module=dashboard"> Dashboard </a>
	</div>
<?php
}

// jika menu siswa (tampil data / tampil detail / form entri / form ubah / tampil pencarian) dipilih, menu siswa aktif
if ($module == 'siswa' || $module == 'tampil_detail_siswa' || $module == 'form_entri_siswa' || $module == 'form_ubah_siswa' || $module == 'tampil_pencarian_siswa') { ?>
	<div class="item active d-flex align-items-center">
		<i class="fa-regular fa-user"></i>
		<a href="?module=siswa"> Anggota </a>
	</div>
<?php
}
// jika tidak dipilih, menu siswa tidak aktif
else { ?>
	<div class="item d-flex align-items-center">
		<i class="fa-regular fa-user"></i>
		<a href="?module=siswa"> Anggota </a>
	</div>
<?php
}

// jika menu asrama (tampil data / tampil detail / form entri / form ubah / tampil pencarian) dipilih, menu asrama aktif
if ($module == 'asrama' || $module == 'tampil_detail_asrama' || $module == 'form_entri_asrama' || $module == 'form_ubah_asrama' || $module == 'tampil_pencarian_asrama') { ?>
	<div class="item active d-flex align-items-center">
		<i class="fa-solid fa-house"></i>
		<a href="?module=asrama"> Asrama </a>
	</div>
<?php
}
// jika tidak dipilih, menu asrama tidak aktif
else { ?>
	<div class="item d-flex align-items-center">
		<i class="fa-solid fa-house"></i>
		<a href="?module=asrama"> Asrama </a>
	</div>
<?php
}

// jika menu laporan dipilih, menu laporan aktif
if ($module == 'laporan') { ?>
	<div class="item active d-flex align-items-center">
		<i class="fa-regular fa-file-lines"></i>
		<a href="?module=laporan"> Laporan </a>
	</div>
<?php
}
// jika tidak dipilih, menu laporan tidak aktif
else { ?>
	<div class="item d-flex align-items-center">
		<i class="fa-regular fa-file-lines"></i>
		<a href="?module=laporan"> Laporan </a>
	</div>
<?php
}

// jika menu tentang aplikasi dipilih, menu tentang aplikasi aktif
if ($module == 'tentang') { ?>
	<div class="item active d-flex align-items-center">
		<i class="fa-solid fa-info"></i>
		<a href="?module=tentang"> Tentang Aplikasi </a>
	</div>
<?php
}
// jika tidak dipilih, menu tentang aplikasi tidak aktif
else { ?>
	<div class="item d-flex align-items-center">
		<i class="fa-solid fa-info"></i>
		<a href="?module=tentang"> Tentang Aplikasi </a>
	</div>
<?php
}
?>

<hr class="my-3">

<div class="menu-section mb-3">
    <small class="text-muted text-uppercase fw-bold">Akun</small>
</div>

<?php if (is_login()) { ?>
    <!-- Menu Logout -->
    <div class="item d-flex align-items-center">
        <i class="fa-solid fa-sign-out-alt text-danger"></i>
        <a href="main.php?module=logout" class="text-danger" onclick="return confirm('Apakah Anda yakin ingin keluar?')"> Logout </a>
    </div>
<?php } ?>

<?php
// jika user belum login, tampilkan menu login
if (!is_login()) {
?>
    <div class="item d-flex align-items-center">
        <i class="fa-solid fa-sign-in-alt text-success"></i>
        <a href="login.php" class="text-success"> Login </a>
    </div>
<?php
}
?>
