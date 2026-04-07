<?php
// panggil file "database.php" untuk koneksi ke database
require_once "../../config/database.php";

// mengecek data hasil submit dari form
if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $id_asrama   = mysqli_real_escape_string($mysqli, $_POST['id_asrama']);
    $nama_asrama = mysqli_real_escape_string($mysqli, trim($_POST['nama_asrama']));
    $deskripsi   = mysqli_real_escape_string($mysqli, trim($_POST['deskripsi']));

    // sql statement untuk update data di tabel "tbl_asrama" berdasarkan "id_asrama"
    $update = mysqli_query($mysqli, "UPDATE tbl_asrama SET nama_asrama='$nama_asrama', deskripsi='$deskripsi'
                                     WHERE id_asrama='$id_asrama'")
                                     or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
    // cek query
    // jika proses update berhasil
    if ($update) {
        // alihkan ke halaman detail data asrama dan tampilkan pesan berhasil ubah data
        header("location: ../../main.php?module=tampil_detail_asrama&id=$id_asrama&pesan=1");
    }
}

