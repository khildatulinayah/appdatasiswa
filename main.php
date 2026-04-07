<?php
// tangani akses login/register langsung ke main.php sebelum HTML dikirim
require_once "helper/auth_helper.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$module = isset($_GET['module']) ? $_GET['module'] : 'dashboard';
if ($module == 'form_login') {
    if (is_login()) {
        header('location: main.php?module=dashboard');
        exit();
    }
    header('location: login.php');
    exit();
}

if ($module == 'form_register') {
    if (is_login()) {
        header('location: main.php?module=dashboard');
        exit();
    }
    header('location: register.php');
    exit();
}

if ($module == 'logout') {
    require_once "helper/auth_helper.php";
    logout();
    exit();
}

?><!-- Aplikasi Pengelolaan Data Siswa Hogwarts
********************************************
* Developer   : -
* Company     : -
* Release     : Maret 2026
* Update      : -
* Website     : microdata.co.id
* E-mail      : -
* WhatsApp    : -
-->

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Pengelolaan Data Siswa Hogwarts">
    <meta name="author" content="Micro data">

    <!-- Title -->
    <title>Aplikasi Pengelolaan Data Siswa</title>

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css" integrity="sha256-RXPAyxHVyMLxb0TYCM2OW5R4GWkcDe02jdYgyZp41OU=" crossorigin="anonymous">

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Sidebar Menu -->
    <section class="sidebar-menu d-none d-md-block shadow-sm">
        <!-- Brand -->
        <div class="brand text-center mb-5">
            <!-- Logo -->
            <img src="assets/img/hogwards-logo.svg" alt="Hogwards Logo">
            <!-- Title -->
            <h3 class="mt-4 mb-3"> Hogwards </h3>
        </div>
        <div class="menus">

            <!-- panggil file "sidebar_menu.php" untuk menampilkan menu sidebar -->
            <?php include "sidebar_menu.php"; ?>

        </div>
    </section>

    <!-- Main Content -->
    <main class="content-wrapper d-block">
        <div class="container">

            <!-- panggil file "content.php" untuk menampilkan halaman konten -->
            <?php include "content.php"; ?>

        </div>
    </main>

    <!-- Mobile Menu -->
    <section class="mobile-menu d-block d-md-none">
        <div class="row bottom-navigation">
            <div class="col-12 col-lg-12">
                <div class="card-menu shadow-lg">
                    <div class="row justify-content-center">

                        <!-- panggil file "mobile_menu.php" untuk menampilkan menu mobile -->
                        <?php include "mobile_menu.php"; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Popper and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.js" integrity="sha256-AkQap91tDcS4YyQaZY2VV34UhSCxu2bDEIgXXXuf5Hg=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/l10n/id.js" integrity="sha256-cvHCpHmt9EqKfsBeDHOujIlR5wZ8Wy3s90da1L3sGkc=" crossorigin="anonymous"></script>

    <!-- Custom Scripts -->
    <script src="assets/js/flatpickr.js"></script>
    <script src="assets/js/form-validation.js"></script>
</body>

</html>
