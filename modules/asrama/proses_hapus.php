<?php
// panggil file "database.php" untuk koneksi ke database
require_once "../../config/database.php";

// mengecek data GET "id_asrama"
if (isset($_GET['id'])) {
    // ambil data GET dari tombol hapus
    $id_asrama = mysqli_real_escape_string($mysqli, $_GET['id']);

    // sql statement untuk delete data dari tabel "tbl_asrama" berdasarkan "id_asrama"
    $delete = mysqli_query($mysqli, "DELETE FROM tbl_asrama WHERE id_asrama='$id_asrama'")
                                     or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
    // cek query
    // jika proses delete berhasil
    if ($delete) {
        // alihkan ke halaman data asrama dan tampilkan pesan berhasil hapus data
        header('location: ../../main.php?module=asrama&pesan=2');
    }
}

