<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <!-- Hero Section -->
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5 mb-3">Tentang FloodSeg AI</h1>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">
            Misi kami adalah menyediakan solusi deteksi banjir yang cepat, akurat, dan mudah diakses menggunakan kecerdasan buatan mutakhir.
        </p>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
        <div class="row g-0">
            <div class="col-md-6 order-md-2">
                <div class="h-100 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center p-5">
                    <i class="fas fa-layer-group fa-10x text-primary opacity-25"></i>
                </div>
            </div>
            <div class="col-md-6 order-md-1">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4">Visi Kami</h3>
                    <p class="text-muted mb-4">
                        Banjir merupakan salah satu bencana alam yang paling sering terjadi dan merugikan. Metode pemetaan konvensional seringkali memakan waktu lama. FloodSeg AI hadir untuk memangkas waktu tersebut dari jam menjadi detik.
                    </p>
                    <p class="text-muted mb-4">
                        Dengan memanfaatkan teknologi <strong>Deep Learning (Residual U-Net)</strong>, sistem kami dapat menganalisis citra satelit atau foto udara dan secara otomatis memisahkan area air dari daratan dengan presisi tinggi.
                    </p>
                    
                    <h5 class="fw-bold mt-4 mb-3">Teknologi yang Digunakan</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2">Python</span>
                        <span class="badge bg-light text-dark border px-3 py-2">TensorFlow</span>
                        <span class="badge bg-light text-dark border px-3 py-2">Residual U-Net Architecture</span>
                        <span class="badge bg-light text-dark border px-3 py-2">PHP & MySQL</span>
                        <span class="badge bg-light text-dark border px-3 py-2">Bootstrap 5</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <h3 class="fw-bold text-center mb-4">Tim Pengembang</h3>
    <div class="row g-4 justify-content-center">
        <!-- Developer 1 -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4 hover-up">
                <div class="mb-3 mx-auto">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Iqbal</h5>
                <p class="text-muted small mb-3">Frontend Developer</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="https://github.com/IqbalIka17" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>

        <!-- Developer 2 -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4 hover-up">
                <div class="mb-3 mx-auto">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Fikri</h5>
                <p class="text-muted small mb-3">Backend Developer</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="https://github.com/fikriwildann" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>

        <!-- Developer 3 -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4 hover-up">
                <div class="mb-3 mx-auto">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Arya</h5>
                <p class="text-muted small mb-3">UI/UX Designer</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="https://github.com/aryalp19" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>

        <!-- Developer 4 -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4 hover-up">
                <div class="mb-3 mx-auto">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Diva</h5>
                <p class="text-muted small mb-3">Data Scientist</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
