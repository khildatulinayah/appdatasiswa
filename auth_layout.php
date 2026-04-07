<!-- Aplikasi Pengelolaan Data Siswa Hogwarts
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

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background: linear-gradient(135deg, #ead866 0%, #a28e4b 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .card {
            border-radius: 15px;
            border: none;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #eadf66 0%, #a2934b 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #d8b45a 0%, #c0c623 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .input-group .btn-outline-secondary {
            border-radius: 0 10px 10px 0;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="container">
            <!-- panggil file "content.php" untuk menampilkan halaman konten -->
            <?php include "content.php"; ?>
        </div>
    </div>

    <!-- Popper and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

    <!-- Custom Scripts -->
    <script src="assets/js/form-validation.js"></script>
</body>

</html>

