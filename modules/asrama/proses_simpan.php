<?php
// panggil file "database.php" untuk koneksi ke database
require_once "../../config/database.php";

// mengecek data hasil submit dari form
if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $nama_asrama = mysqli_real_escape_string($mysqli, trim($_POST['nama_asrama']));
    $deskripsi   = mysqli_real_escape_string($mysqli, trim($_POST['deskripsi']));

    // sql statement untuk insert data ke tabel "tbl_asrama"
    $insert = mysqli_query($mysqli, "INSERT INTO tbl_asrama(nama_asrama, deskripsi) VALUES('$nama_asrama', '$deskripsi')")
                                     or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
    // cek query
    // jika proses insert berhasil
    if ($insert) {
        // alihkan ke halaman data asrama dan tampilkan pesan berhasil simpan data
        header('location: ../../main.php?module=asrama&pesan=1');
    }
}

