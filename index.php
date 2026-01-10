<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section text-center py-5 mb-5">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-9">
                <div class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill fw-medium">
                    <i class="fas fa-sparkles me-2"></i>Ditenagai oleh Deep Learning Residual U-Net
                </div>
                <h1 class="display-3 fw-bold mb-4" style="color: #0f172a; letter-spacing: -1px; line-height: 1.2;">
                    Deteksi Area Banjir dari <br>
                    <span class="text-primary position-relative">Citra Satelit
                        <svg class="position-absolute w-100" style="bottom: -10px; left: 0; height: 8px; opacity: 0.3;" viewBox="0 0 100 10" preserveAspectRatio="none">
                            <path d="M0 5 Q 50 10 100 5" stroke="var(--primary-color)" stroke-width="3" fill="none" />
                        </svg>
                    </span>
                </h1>
                <p class="lead text-muted mb-5 px-lg-5">
                    Analisis foto udara secara instan dan identifikasi zona terdampak banjir dengan presisi tinggi menggunakan model AI kami. Tanpa proses pemetaan manual yang memakan waktu.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#upload-section" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-primary fw-semibold">
                        Mulai Analisis <i class="fas fa-arrow-down ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-5 mb-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center hover-up">
                    <div class="icon-box mb-3 mx-auto bg-blue-50 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: #eff6ff;">
                        <i class="fas fa-brain fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Kecerdasan Buatan</h5>
                    <p class="text-muted small">Dibangun di atas arsitektur residual U-Net yang dilatih khusus untuk segmentasi semantik objek air dan banjir.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center hover-up">
                    <div class="icon-box mb-3 mx-auto bg-teal-50 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: #f0fdfa;">
                        <i class="fas fa-bolt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Performa Cepat</h5>
                    <p class="text-muted small">Dapatkan hasil segmentasi hanya dalam hitungan detik menggunakan mesin inferensi lokal yang optimal.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center hover-up">
                    <div class="icon-box mb-3 mx-auto bg-purple-50 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: #faf5ff;">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Aman & Privat</h5>
                    <p class="text-muted small">Data citra Anda diproses sepenuhnya di server lokal ini dan tidak pernah dikirim ke layanan cloud pihak ketiga.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upload Tool Section -->
<section id="upload-section" class="py-5" style="background: linear-gradient(to bottom, transparent, rgba(37, 99, 235, 0.03));">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 overflow-hidden">
                    <div class="card-header bg-white border-0 text-center pt-5 pb-0">
                        <h2 class="fw-bold mb-2">Mulai Analisis Anda</h2>
                        <p class="text-muted">Upload gambar untuk menghasilkan masking area banjir</p>
                    </div>
                    <div class="card-body p-5">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form id="upload-form" action="process.php" method="POST" enctype="multipart/form-data">
                                <div id="upload-area" class="upload-area mb-4">
                                    <div class="mb-3">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold">Tarik & Lepas atau Klik untuk Upload</h4>
                                    <p class="text-muted mb-0">Bisa pilih banyak gambar sekaligus (JPG, PNG)</p>
                                    <input type="file" name="image[]" id="file-input" class="d-none" accept="image/*" multiple required>
                                </div>
                                
                                <div id="preview-container" class="text-center bg-light rounded-4 p-4 border border-dashed" style="display: none;">
                                    <h5 class="mb-3 fw-bold text-dark">Gambar Terpilih <span id="file-count" class="badge bg-primary rounded-pill">0</span></h5>
                                    
                                    <div id="preview-gallery" class="d-flex flex-wrap justify-content-center gap-3" style="max-height: 400px; overflow-y: auto;">
                                        <!-- Images will be injected here -->
                                    </div>

                                    <div class="mt-4 text-start bg-white p-4 rounded-3 shadow-sm border">
                                        <div class="row g-4">
                                            <div class="col-md-4">
                                                <label for="epochRange" class="form-label fw-bold d-flex justify-content-between">
                                                    <span>Epoch</span>
                                                    <span class="badge bg-primary rounded-pill" id="epochValue">10</span>
                                                </label>
                                                <input type="range" class="form-range" min="10" max="100" step="10" id="epochRange" name="epoch" value="10" oninput="document.getElementById('epochValue').innerText = this.value">
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <span>10</span>
                                                    <span>100</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="batchSizeRange" class="form-label fw-bold d-flex justify-content-between">
                                                    <span>Batch Size</span>
                                                    <span class="badge bg-info rounded-pill" id="batchSizeValue">4</span>
                                                </label>
                                                <input type="range" class="form-range" min="0" max="2" step="1" id="batchSizeRange" value="1" oninput="const batchValues=[2,4,8]; document.getElementById('batchSizeValue').innerText=batchValues[this.value]; document.getElementById('realBatchSize').value=batchValues[this.value];">
                                                <input type="hidden" name="batch_size" id="realBatchSize" value="4">
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <span>2</span>
                                                    <span>8</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="lrRange" class="form-label fw-bold d-flex justify-content-between">
                                                    <span>Learning Rate</span>
                                                    <span class="badge bg-warning text-dark rounded-pill" id="lrValue">0.01</span>
                                                </label>
                                                <input type="range" class="form-range" min="0" max="2" step="1" id="lrRange" value="1" oninput="const v=[0.1, 0.01, 0.001]; document.getElementById('lrValue').innerText=v[this.value]; document.getElementById('realLR').value=v[this.value];">
                                                <input type="hidden" name="learning_rate" id="realLR" value="0.01">
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <span>0.1</span>
                                                    <span>0.001</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-muted small mt-3 mb-0"><i class="fas fa-info-circle me-1"></i> Sesuaikan parameter Epoch, Batch Size, dan Learning Rate untuk hasil optimal.</p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" id="process-btn" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-primary fw-bold w-100" disabled>
                                            <i class="fas fa-wand-magic-sparkles me-2"></i>Jalankan Segmentasi Batch
                                        </button>
                                        <button type="button" class="btn btn-link text-muted mt-2" onclick="resetUpload()">Batal / Reset</button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-lock fa-4x text-muted opacity-25"></i>
                                </div>
                                <h3 class="fw-bold mb-3">Akses Terbatas</h3>
                                <p class="text-muted mb-4 lead">Anda perlu login terlebih dahulu untuk menggunakan fitur analisis AI kami.</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="login.php" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-primary">Masuk</a>
                                    <a href="register.php" class="btn btn-outline-secondary btn-lg rounded-pill px-5 fw-bold">Daftar</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Pertanyaan Umum</h2>
            <p class="text-muted">Jawaban untuk hal-hal yang mungkin Anda tanyakan</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-accordion" id="faqAccordion">
                    
                    <!-- Item 1 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold py-3 px-4 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana cara kerja FloodSeg AI?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-4 pb-4 text-muted">
                                Sistem kami menggunakan model Deep Learning bernama residual U-Net yang telah dilatih dengan ribuan citra satelit banjir. Model ini menganalisis setiap piksel gambar Anda dan memprediksi apakah piksel tersebut adalah air atau daratan.
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold py-3 px-4 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Format gambar apa saja yang didukung?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-4 pb-4 text-muted">
                                Saat ini kami mendukung format gambar standar seperti <strong>JPG, JPEG, dan PNG</strong>. Untuk hasil terbaik, gunakan gambar citra udara atau satelit dengan resolusi yang jelas.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold py-3 px-4 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Seberapa akurat hasil deteksi banjirnya?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-4 pb-4 text-muted">
                                Model residual U-Net kami telah mencapai tingkat akurasi (IoU Score) rata-rata di atas <strong>87%</strong> , (Dice Score) rata-rata di atas <strong>96%</strong> , dan (Pixel Accuracy) rata-rata di atas <strong>91%</strong> , pada dataset pengujian. Namun, hasil dapat bervariasi tergantung pada kualitas, pencahayaan, dan kompleksitas citra yang diunggah.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold py-3 px-4 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Berapa lama waktu yang dibutuhkan untuk analisis?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-4 pb-4 text-muted">
                                Sangat cepat. Rata-rata proses inferensi hanya memakan waktu <strong>2 hingga 5 detik</strong> per gambar, tergantung pada spesifikasi perangkat keras komputer server Anda.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="text-center">
        <div class="spinner-grow text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h3 class="mt-3 fw-bold text-dark">Menganalisis Gambar...</h3>
        <p class="text-muted">AI kami sedang mengidentifikasi pola air pixel demi pixel.</p>
    </div>
</div>

<script>
    // Helper function to reset upload
    function resetUpload() {
        document.getElementById('upload-form').reset();
        
        // Reset manual displays and hidden inputs to defaults
        document.getElementById('epochValue').innerText = '10';
        
        document.getElementById('batchSizeValue').innerText = '4';
        document.getElementById('realBatchSize').value = '4';
        
        document.getElementById('lrValue').innerText = '0.01';
        document.getElementById('realLR').value = '0.01';

        document.getElementById('preview-container').style.display = 'none';
        document.getElementById('upload-area').style.display = 'block';
        document.getElementById('preview-gallery').innerHTML = '';
        document.getElementById('process-btn').disabled = true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const uploadForm = document.getElementById('upload-form');
        
        if (uploadForm) {
            uploadForm.addEventListener('submit', function() {
                document.getElementById('loading-overlay').style.display = 'flex';
            });
        }

        const fileInput = document.getElementById('file-input');
        const uploadArea = document.getElementById('upload-area');
        const previewContainer = document.getElementById('preview-container');
        const previewGallery = document.getElementById('preview-gallery');
        const fileCountBadge = document.getElementById('file-count');
        const processBtn = document.getElementById('process-btn');
        
        // Handle file selection
        fileInput.addEventListener('change', handleFiles);
        
        // Handle Drag & Drop (update upload area logic)
        uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('bg-light'); });
        uploadArea.addEventListener('dragleave', (e) => { e.preventDefault(); uploadArea.classList.remove('bg-light'); });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('bg-light');
            fileInput.files = e.dataTransfer.files;
            handleFiles();
        });

        function handleFiles() {
            const files = fileInput.files;
            if (files.length > 0) {
                uploadArea.style.display = 'none';
                previewContainer.style.display = 'block';
                previewGallery.innerHTML = '';
                fileCountBadge.textContent = files.length;
                processBtn.disabled = false;

                // Sync sliders on show (handles browser form persistence)
                document.getElementById('epochRange').dispatchEvent(new Event('input'));
                document.getElementById('batchSizeRange').dispatchEvent(new Event('input'));
                document.getElementById('lrRange').dispatchEvent(new Event('input'));

                // Limit preview to first 10 images for performance if user selects 100
                const maxPreview = Math.min(files.length, 12);
                
                for (let i = 0; i < maxPreview; i++) {
                    const file = files[i];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imgDiv = document.createElement('div');
                            imgDiv.className = 'position-relative';
                            imgDiv.innerHTML = `
                                <img src="${e.target.result}" class="rounded-3 shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                            `;
                            previewGallery.appendChild(imgDiv);
                        }
                        reader.readAsDataURL(file);
                    }
                }
                
                if (files.length > 12) {
                    const moreDiv = document.createElement('div');
                    moreDiv.className = 'd-flex align-items-center justify-content-center bg-light rounded-3 text-muted fw-bold border';
                    moreDiv.style.width = '100px';
                    moreDiv.style.height = '100px';
                    moreDiv.textContent = `+${files.length - 12} Lainnya`;
                    previewGallery.appendChild(moreDiv);
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
