<div class="d-flex flex-column flex-lg-row mb-4">
    <!-- judul halaman -->
    <div class="flex-grow-1 d-flex align-items-center">
        <i class="fa-solid fa-chart-simple icon-title"></i>
        <h3>Dashboard</h3>
    </div>
    <!-- breadcrumbs -->
    <div class="ms-5 ms-lg-0 pt-lg-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?module=dashboard" class="text-dark text-decoration-none"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item"><a href="?module=dashboard" class="text-dark text-decoration-none">Dashboard</a></li>
            </ol>
        </nav>
    </div>
</div>

<!-- tampilkan pesan selamat datang -->
<div class="bg-white rounded-4 shadow-sm p-4 mb-5">
    <div class="row align-items-center justify-content-between">
        <div class="col-lg-3 d-block mt-xxl-n4">
            <img src="assets/img/hogwardslogo.jpg" alt="Hogwards Logo" width="250">
        </div>
        <div class="col-lg-9">
            <?php if (is_admin()) { ?>
                <h4 class="mt-3 mt-lg-0 mb-2">Selamat datang, Admin <strong>Hogwards</strong>!</h4>
                <p class="text-muted fw-light mb-4">
                    Ini adalah dashboard admin. Anda dapat mengelola data siswa, asrama, laporan, dan akun pengguna dari sistem.
                </p>
                <p class="text-muted fw-light mb-4">
                    Akses cepat:
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="?module=siswa" class="btn btn-brand">Kelola Anggota</a>
                    <a href="?module=asrama" class="btn btn-outline-brand">Kelola Asrama</a>
                    <a href="?module=user" class="btn btn-outline-secondary">Manajemen Pengguna</a>
                </div>
            <?php } else { ?>
                <h4 class="mt-3 mt-lg-0 mb-2">Selamat datang di <strong>Hogwards</strong>!</h4>
                <p class="text-muted fw-light mb-4">
                    Selamat datang di sistem pengelolaan siswa Asrama Hogwards. Di sini Anda dapat melihat informasi siswa dan asrama secara lengkap.
                </p>
                <p class="text-muted fw-light mb-4">
                    #Hogwards #Asrama #Siswa #MagicalSchool
                </p>
                <p class="text-muted fw-light mb-4">
                    Nikmati tampilan data read-only. Untuk perubahan data, hubungi admin.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="?module=dashboard" class="btn btn-outline-brand">Lihat Asrama <i class="fa-solid fa-angle-right ms-3"></i></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row">
    <!-- menampilkan informasi jumlah siswa asrama gryffindor -->
    <div class="col-lg-6 col-xl-4">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-4-2 mb-4">
            <div class="d-flex align-items-center justify-content-start">
                <div class="me-4">
                    <i class="fa-brands fa-html5 icon-widget"></i>
                </div>
                <div>
                    <p class="text-muted mb-1"><small>Gryffindor</small></p>
                    <?php
                    // sql statement untuk menampilkan jumlah data pada tabel "tbl_siswa" berdasarkan "asrama"
                    $query = mysqli_query($mysqli, "SELECT COUNT(a.id_siswa) as jumlah FROM tbl_siswa as a INNER JOIN tbl_asrama as b ON a.asrama=b.id_asrama 
                                                    WHERE b.nama_asrama='Gryffindor'")
                                                    or die('Ada kesalahan pada query jumlah data siswa gratis : ' . mysqli_error($mysqli));
                    // ambil data hasil query
                    $data = mysqli_fetch_assoc($query);
                    // buat variabel untuk menampilkan data
                    $jumlah_siswa = $data['jumlah'];
                    ?>
                    <!-- tampilkan data -->
                    <h5 class="fw-bold mb-0"><?php echo number_format($jumlah_siswa, 0, '', '.'); ?></h5>
                </div>
            </div>
        </div>
    </div>
    <!-- menampilkan informasi jumlah siswa asrama hufflepuff -->
    <div class="col-lg-6 col-xl-4">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-4-2 mb-4">
            <div class="d-flex align-items-center justify-content-start">
                <div class="me-4">
                    <i class="fa-solid fa-laptop-code icon-widget"></i>
                </div>
                <div>
                    <p class="text-muted mb-1"><small>Hufflepuff</small></p>
                    <?php
                    // sql statement untuk menampilkan jumlah data pada tabel "tbl_siswa" berdasarkan "asrama"
                    $query = mysqli_query($mysqli, "SELECT COUNT(a.id_siswa) as jumlah FROM tbl_siswa as a INNER JOIN tbl_asrama as b ON a.asrama=b.id_asrama 
                                                    WHERE b.nama_asrama='Hufflepuff'")
                                                    or die('Ada kesalahan pada query jumlah data siswa gratis : ' . mysqli_error($mysqli));
                    // ambil data hasil query
                    $data = mysqli_fetch_assoc($query);
                    // buat variabel untuk menampilkan data
                    $jumlah_siswa = $data['jumlah'];
                    ?>
                    <!-- tampilkan data -->
                    <h5 class="fw-bold mb-0"><?php echo number_format($jumlah_siswa, 0, '', '.'); ?></h5>
                </div>
            </div>
        </div>
    </div>
    <!-- menampilkan informasi jumlah siswa asrama ravenclaw -->
    <div class="col-lg-6 col-xl-4">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-4-2 mb-4">
            <div class="d-flex align-items-center justify-content-start">
                <div class="text-muted me-4">
                    <i class="fa-solid fa-mobile-screen icon-widget"></i>
                </div>
                <div>
                    <p class="mb-1"><small>Ravenclaw</small></p>
                    <?php
                    // sql statement untuk menampilkan jumlah data pada tabel "tbl_siswa" berdasarkan "asrama"
                    $query = mysqli_query($mysqli, "SELECT COUNT(a.id_siswa) as jumlah FROM tbl_siswa as a INNER JOIN tbl_asrama as b ON a.asrama=b.id_asrama 
                                                    WHERE b.nama_asrama='Ravenclaw'")
                                                    or die('Ada kesalahan pada query jumlah data siswa gratis : ' . mysqli_error($mysqli));
                    // ambil data hasil query
                    $data = mysqli_fetch_assoc($query);
                    // buat variabel untuk menampilkan data
                    $jumlah_siswa = $data['jumlah'];
                    ?>
                    <!-- tampilkan data -->
                    <h5 class="fw-bold mb-0"><?php echo number_format($jumlah_siswa, 0, '', '.'); ?></h5>
                </div>
            </div>
        </div>
    </div>
    <!-- menampilkan informasi jumlah siswa asrama slytherin -->
    <div class="col-lg-6 col-xl-4">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-4-2 mb-4">
            <div class="d-flex align-items-center justify-content-start">
                <div class="text-muted me-4">
                    <i class="fa-solid fa-gamepad icon-widget"></i>
                </div>
                <div>
                    <p class="mb-1"><small>Slytherin</small></p>
                    <?php
                    // sql statement untuk menampilkan jumlah data pada tabel "tbl_siswa" berdasarkan "asrama"
                    $query = mysqli_query($mysqli, "SELECT COUNT(a.id_siswa) as jumlah FROM tbl_siswa as a INNER JOIN tbl_asrama as b ON a.asrama=b.id_asrama 
                                                    WHERE b.nama_asrama='Slytherin'")
                                                    or die('Ada kesalahan pada query jumlah data siswa gratis : ' . mysqli_error($mysqli));
                    // ambil data hasil query
                    $data = mysqli_fetch_assoc($query);
                    // buat variabel untuk menampilkan data
                    $jumlah_siswa = $data['jumlah'];
                    ?>
                    <!-- tampilkan data -->
                    <h5 class="fw-bold mb-0"><?php echo number_format($jumlah_siswa, 0, '', '.'); ?></h5>
                </div>
            </div>
        </div>
    </div>

</div>

