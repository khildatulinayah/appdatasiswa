# Code Citations

## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/kelas/tampil_data.php

```
/ jika "paginasi_halaman" < "jumlah_paginasi_halaman", maka button link ">" aktif
                else { ?>
                    <li class="page-item pagination-pill">
                        <a class="page-link" href="?module=kelas&paginasi=<?php echo $paginasi_halaman + 1; ?>" aria-label="Next">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php
    }
    // jika data kelas tidak ada
    else { ?>
        <!-- tampilkan pesan data tidak tersedia -->
        <div>Tidak ada data yang tersedia.</div>
    <
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/laporan/cetak.php

```
statement untuk menampilkan data dari tabel "tbl_siswa" berdasarkan "id_siswa" dan tabel "tbl_kelas" berdasarkan "id_siswa"
    $query = mysqli_query($mysqli, "SELECT a.id_siswa, a.tanggal_daftar, a.kelas, a.nama_lengkap, a.jenis_kelamin, a.alamat, a.email, a.whatsapp, a.foto_profil, b.nama_kelas 
                                    FROM tbl_siswa as a INNER JOIN tbl_kelas as b ON a.kelas=b.id_kelas 
                                    WHERE a.id_siswa='$id_siswa'")
                                    or die('Ada kesalahan pada query tampil data :
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/siswa/form_ubah.php

```
statement untuk menampilkan data dari tabel "tbl_siswa" berdasarkan "id_siswa" dan tabel "tbl_kelas" berdasarkan "id_siswa"
    $query = mysqli_query($mysqli, "SELECT a.id_siswa, a.tanggal_daftar, a.kelas, a.nama_lengkap, a.jenis_kelamin, a.alamat, a.email, a.whatsapp, a.foto_profil, b.nama_kelas 
                                    FROM tbl_siswa as a INNER JOIN tbl_kelas as b ON a.kelas=b.id_kelas 
                                    WHERE a.id_siswa='$id_siswa'")
                                    or die('Ada kesalahan pada query tampil data :
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/siswa/form_ubah.php

```
"col-xl-6">
                <div class="mb-3 ps-xl-3">
                    <label class="form-label">Kelas <span class="text-danger">*</span></label>
                    <select name="kelas" class="form-select" autocomplete="off" required>
                        <option value="<?php echo $data['kelas']; ?>"><?php echo $data['nama_kelas']; ?></option>
                        <option disabled value="">-- Pilih --</option>
                        <?php
                        // sql statement untuk menampilkan data dari tabel "tbl_kelas"
                        $query_kelas = mysqli_query($mysqli, "SELECT id_kelas, nama_kelas FROM tbl_kelas ORDER BY nama_kelas ASC")
                                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                        // ambil data hasil query
                        while ($data_kelas = mysqli_fetch_assoc($query_kelas)) {
                            // tampilkan data
                            echo "<option value='$data_kelas[id_kelas]'>$data_kelas[nama_kelas]</option>";
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Kelas tidak boleh kosong.</div
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/siswa/form_entri.php

```
"col-xl-6">
                <div class="mb-3 ps-xl-3">
                    <label class="form-label">Kelas <span class="text-danger">*</span></label>
                    <select name="kelas" class="form-select" autocomplete="off" required>
                        <option value="<?php echo $data['kelas']; ?>"><?php echo $data['nama_kelas']; ?></option>
                        <option disabled value="">-- Pilih --</option>
                        <?php
                        // sql statement untuk menampilkan data dari tabel "tbl_kelas"
                        $query_kelas = mysqli_query($mysqli, "SELECT id_kelas, nama_kelas FROM tbl_kelas ORDER BY nama_kelas ASC")
                                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                        // ambil data hasil query
                        while ($data_kelas = mysqli_fetch_assoc($query_kelas)) {
                            // tampilkan data
                            echo "<option value='$data_kelas[id_kelas]'>$data_kelas[nama_kelas]</option>";
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Kelas tidak boleh kosong.</div
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/siswa/form_entri.php

```
>*</span></label>
                    <select name="kelas" class="form-select" autocomplete="off" required>
                        <option selected disabled value="">-- Pilih --</option>
                        <?php
                        // sql statement untuk menampilkan data dari tabel "tbl_kelas"
                        $query_kelas = mysqli_query($mysqli, "SELECT id_kelas, nama_kelas FROM tbl_kelas ORDER BY nama_kelas ASC")
                                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                        // ambil data hasil query
                        while ($data_kelas = mysqli_fetch_assoc($query_kelas)) {
                            // tampilkan data
                            echo "<option value='$data_kelas[id_kelas]'>$data_kelas[nama_kelas]</option>";
                        }
                        ?>
                    </select>
                    <div
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-member/blob/98b85fdee89d7f8ce84e9e33e552dd8da9dbaf9f/modules/laporan/form_filter.php

```
boleh kosong.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" rows="10" class="form-control" autocomplete="off" required></textarea>
                <div class="invalid-feedback">Deskripsi tidak boleh kosong.</div>
            </div>
        </div>

        <div class="pt-4 pb-2 mt-5 border-top">
            <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                <!-- button simpan data -->
                <input type="submit" name="simpan" value="Simpan" class="btn btn-outline-brand px-4">
                <!-- button kembali ke halaman tampil data -->
                <a href="?module=
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/kelas/form_ubah.php

```
boleh kosong.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" rows="10" class="form-control" autocomplete="off" required></textarea>
                <div class="invalid-feedback">Deskripsi tidak boleh kosong.</div>
            </div>
        </div>

        <div class="pt-4 pb-2 mt-5 border-top">
            <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                <!-- button simpan data -->
                <input type="submit" name="simpan" value="Simpan" class="btn btn-outline-brand px-4">
                <!-- button kembali ke halaman tampil data -->
                <a href="?module=
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/kelas/form_entri.php

```
boleh kosong.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" rows="10" class="form-control" autocomplete="off" required></textarea>
                <div class="invalid-feedback">Deskripsi tidak boleh kosong.</div>
            </div>
        </div>

        <div class="pt-4 pb-2 mt-5 border-top">
            <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                <!-- button simpan data -->
                <input type="submit" name="simpan" value="Simpan" class="btn btn-outline-brand px-4">
                <!-- button kembali ke halaman tampil data -->
                <a href="?module=
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-member/blob/98b85fdee89d7f8ce84e9e33e552dd8da9dbaf9f/modules/member/form_entri.php

```
boleh kosong.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" rows="10" class="form-control" autocomplete="off" required></textarea>
                <div class="invalid-feedback">Deskripsi tidak boleh kosong.</div>
            </div>
        </div>

        <div class="pt-4 pb-2 mt-5 border-top">
            <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                <!-- button simpan data -->
                <input type="submit" name="simpan" value="Simpan" class="btn btn-outline-brand px-4">
                <!-- button kembali ke halaman tampil data -->
                <a href="?module=
```


## License: unknown
https://github.com/pustakakoding/aplikasi-pengelolaan-data-siswa-kursus/blob/3da20ab97a346454a243b87bdce7c623186f58a4/modules/kelas/proses_simpan.php

```
form
if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $nama_kelas = mysqli_real_escape_string($mysqli, trim($_POST['nama_kelas']));
    $deskripsi  = mysqli_real_escape_string($mysqli, trim($_POST['deskripsi']));

    // sql statement untuk insert data ke tabel "tbl_kelas"
    $insert = mysqli_query($mysqli, "INSERT INTO tbl_kelas(nama_kelas, deskripsi) VALUES('$nama_kelas', '$deskripsi')")
                                     or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
    // cek query
    // jika proses insert berhasil
    if ($insert) {
        // alihkan ke halaman data kelas dan tampilkan pesan berhasil simpan data
        header('location: ../../main.php?module=kelas&pesan=1');
    }
}
```

