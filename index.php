<!--
	index.php adalah file yang dipanggil pertama kali saat user mengakses sebuah alamat website
	disini file index.php hanya digunakan untuk pengalihan halaman 
-->
<?php
// start session
session_start();

// cek apakah user sudah login
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    // jika sudah login, alihkan ke dashboard
    header('location: main.php?module=dashboard');
} else {
    // jika belum login, alihkan ke halaman login
    header('location: login.php');
}
?>
