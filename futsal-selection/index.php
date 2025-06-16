<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<div class="container">
    <div class="text-center my-5">
        <h1 class="mb-3">Sistem Pendukung Keputusan dengan Metode Profil Matching</h1>
        <h1>Untuk Seleksi Pemain Futsal</h3>
        <h1>(Studi Kasus di Asosiasi Futsal Kota U-19 Jepara)</h4>
        <img src="assets/img/UPGRIS-logo-normal.png" alt="Logo Placeholder" class="img-fluid my-4" style="max-width: 300px;">
        <p class="lead">Sistem ini dirancang untuk membantu dalam proses seleksi pemain futsal dengan menggunakan metode profil matching.</p>
        <h2>Kelompok 4</h2>
        <p class="lead">Anggota:</p>
        <ul class="list-unstyled">
            <li>1. Hanif Pria Sembodo (23670036)</li>
            <li>2. Wihandi Khoerul Umam (23670129)</li>
            <li>3. Fa'izal Yoga Aryansyah (23670043)</li>
            <li>4. Dimas Nugroho Hardianto (23670044)</li>
            <br>
        <p class="lead">Silakan pilih salah satu opsi di bawah ini untuk melanjutkan.</p>
    </div>
    <div class="row g-4 justify-content-center">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Pemain</h5>
                    <a href="players/list.php" class="btn btn-primary">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Kriteria</h5>
                    <a href="criteria/manage.php" class="btn btn-primary">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Hasil</h5>
                    <a href="results/display.php" class="btn btn-primary">Lihat</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>