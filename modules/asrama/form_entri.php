<div class="d-flex flex-column flex-lg-row mb-4">
    <!-- judul halaman -->
    <div class="flex-grow-1 d-flex align-items-center">
        <i class="fa-solid fa-house icon-title"></i>
        <h3>Asrama</h3>
    </div>
    <!-- breadcrumbs -->
    <div class="ms-5 ms-lg-0 pt-lg-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?module=dashboard" class="text-dark text-decoration-none"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item"><a href="?module=asrama" class="text-dark text-decoration-none">Asrama</a></li>
                <li class="breadcrumb-item" aria-current="page">Entri</li>
            </ol>
        </nav>
    </div>
</div>

<div class="bg-white rounded-4 shadow-sm p-4 mb-4">
    <!-- judul form -->
    <div class="alert alert-secondary rounded-4 mb-5" role="alert">
        <i class="fa-solid fa-pen-to-square me-2"></i> Entri Data Asrama
    </div>
    <!-- form entri data -->
    <form action="modules/asrama/proses_simpan.php" method="post" class="needs-validation" novalidate>
        <div class="row">
            <div class="mb-3">
                <label class="form-label">Nama Asrama <span class="text-danger">*</span></label>
                <input type="text" name="nama_asrama" class="form-control" autocomplete="off" required>
                <div class="invalid-feedback">Nama asrama tidak boleh kosong.</div>
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
                <a href="?module=asrama" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </div>
    </form>
</div>
