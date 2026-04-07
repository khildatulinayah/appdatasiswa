<?php
// panggil file "database.php" untuk koneksi ke database
require_once "config/database.php";
// panggil file "fungsi_tanggal_indo.php" untuk membuat format tanggal indonesia
require_once "helper/fungsi_tanggal_indo.php";
// panggil file "auth_helper.php" untuk fungsi autentikasi
require_once "helper/auth_helper.php";

// start session jika belum berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ambil module dari URL, default ke dashboard jika tidak ada (tapi jaga jika module sudah di-set dari file include seperti register.php)
if (!isset($module)) {
    $module = isset($_GET['module']) ? $_GET['module'] : 'dashboard';
}

// pemanggilan file halaman konten sesuai "module" yang dipilih
// jika module yang dipilih "form_login"
if ($module == 'form_login') {
    if (basename($_SERVER['SCRIPT_NAME']) === 'main.php') {
        header('location: login.php');
        exit();
    }
    // jika sudah login, alihkan ke dashboard
    if (is_login()) {
        header('location: main.php?module=dashboard');
        exit();
    }
    // panggil file form login
    include "modules/auth/form_login.php";
}
// jika module yang dipilih "form_register"
elseif ($module == 'form_register') {
    if (basename($_SERVER['SCRIPT_NAME']) === 'main.php') {
        header('location: register.php');
        exit();
    }
    // jika sudah login, alihkan ke dashboard
    if (is_login()) {
        header('location: main.php?module=dashboard');
        exit();
    }
    // panggil file form register
    include "modules/auth/form_register.php";
}
// jika module yang dipilih "dashboard"
elseif ($module == 'dashboard') {
    // harus login untuk mengakses dashboard
    require_login();
    // panggil file tampil data dashboard
    include "modules/dashboard/tampil_data.php";
}
// jika module yang dipilih "siswa"
elseif ($module == 'siswa') {
    // harus login untuk mengakses halaman siswa
    require_login();
    // panggil file tampil data siswa
    include "modules/siswa/tampil_data.php";
}
// jika module yang dipilih "form_entri_siswa"
elseif ($module == 'form_entri_siswa') {
    // harus login untuk mengakses form entri siswa
    require_login();
    // panggil file form entri siswa
    include "modules/siswa/form_entri.php";
}
// jika module yang dipilih "form_ubah_siswa"
elseif ($module == 'form_ubah_siswa') {
    // harus login untuk mengakses form ubah siswa
    require_login();
    // panggil file form ubah siswa
    include "modules/siswa/form_ubah.php";
}
// jika module yang dipilih "tampil_detail_siswa"
elseif ($module == 'tampil_detail_siswa') {
    // harus login untuk mengakses detail siswa
    require_login();
    // panggil file tampil detail siswa
    include "modules/siswa/tampil_detail.php";
}
// jika module yang dipilih "tampil_pencarian_siswa"
elseif ($module == 'tampil_pencarian_siswa') {
    // harus login untuk mengakses pencarian siswa
    require_login();
    // panggil file tampil pencarian siswa
    include "modules/siswa/tampil_pencarian.php";
}
// jika module yang dipilih "asrama"
elseif ($module == 'asrama') {
    // harus login untuk mengakses halaman asrama
    require_login();
    // panggil file tampil data asrama
    include "modules/asrama/tampil_data.php";
}
// jika module yang dipilih "form_entri_asrama"
elseif ($module == 'form_entri_asrama') {
    // harus login untuk mengakses form entri asrama
    require_login();
    // panggil file form entri asrama
    include "modules/asrama/form_entri.php";
}
// jika module yang dipilih "form_ubah_asrama"
elseif ($module == 'form_ubah_asrama') {
    // harus login untuk mengakses form ubah asrama
    require_login();
    // panggil file form ubah asrama
    include "modules/asrama/form_ubah.php";
}
// jika module yang dipilih "tampil_detail_asrama"
elseif ($module == 'tampil_detail_asrama') {
    // harus login untuk mengakses detail asrama
    require_login();
    // panggil file tampil detail asrama
    include "modules/asrama/tampil_detail.php";
}
// jika module yang dipilih "tampil_pencarian_asrama"
elseif ($module == 'tampil_pencarian_asrama') {
    // harus login untuk mengakses pencarian asrama
    require_login();
    // panggil file tampil pencarian asrama
    include "modules/asrama/tampil_pencarian.php";
}
// jika module yang dipilih "laporan"
elseif ($module == 'laporan') {
    // harus login untuk mengakses laporan
    require_login();
    // panggil file form filter laporan
    include "modules/laporan/form_filter.php";
}
// jika module yang dipilih "tentang"
elseif ($module == 'tentang') {
    // harus login untuk mengakses halaman tentang
    require_login();
    // panggil file tampil data tentang
    include "modules/tentang/tampil_data.php";
}
// jika tidak ada module yang dipilih
else {
    // cek apakah user sudah login
    if (is_login()) {
        // jika sudah login, alihkan ke dashboard
        header('location: main.php?module=dashboard');
    } else {
        // jika belum login, alihkan ke halaman login
        header('location: login.php');
    }
}

