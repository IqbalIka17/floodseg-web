<?php
include 'includes/database.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM analysis_history WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $original_image = "uploads/original/" . $row['original_filename'];
    
    // Default fallback if processing
    $mask_image = "https://via.placeholder.com/800x600/CCCCCC/666666?text=Processing...";
    
    if ($row['status'] == 'completed') {
        $mask_image = "uploads/result/" . $row['result_filename'];
    }
} else {
    echo "<div class='alert alert-danger'>Analysis not found.</div>";
    exit();
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold" style="color: var(--primary-color);">Segmentation Result</h2>
        <p class="text-muted">Analysis ID: #<?php echo $row['id']; ?> | Processed on: <?php echo date("M d, Y", strtotime($row['upload_time'])); ?></p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="history.php" class="btn btn-outline-secondary me-2">Back</a>
        <?php if ($row['status'] == 'completed'): ?>
            <a href="<?php echo $mask_image; ?>" download class="btn btn-primary"><i class="fas fa-download me-2"></i>Download Mask</a>
        <?php elseif ($row['status'] == 'failed'): ?>
            <button class="btn btn-danger" disabled><i class="fas fa-exclamation-triangle me-2"></i>Analysis Failed</button>
        <?php else: ?>
             <button class="btn btn-secondary" disabled><i class="fas fa-spinner fa-spin me-2"></i>Processing...</button>
             <script>
                 // Auto refresh page every 3 seconds if status is pending
                 setTimeout(function(){
                    location.reload();
                 }, 3000);
             </script>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center print-content">
    <div class="col-12 d-none d-print-block mb-4">
        <div class="border-bottom pb-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary p-3 rounded-3">
                        <i class="fas fa-layer-group fa-2x text-white"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">FloodSeg</h3>
                        <p class="text-muted mb-0 small text-uppercase letter-spacing-1">Advanced Flood Detection System</p>
                    </div>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold mb-1">LAPORAN ANALISIS</h5>
                    <p class="text-muted mb-0 font-monospace">#ID-<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <h6 class="text-uppercase text-muted small fw-bold mb-3">Informasi Analisis</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" style="width: 120px;">Tanggal</td>
                        <td class="fw-medium">: <?php echo date("d F Y", strtotime($row['upload_time'])); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu</td>
                        <td class="fw-medium">: <?php echo date("H:i:s", strtotime($row['upload_time'])); ?> WIB</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Model</td>
                        <td class="fw-medium">: U-Net Lite (MobileNet Backbone)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu Proses</td>
                        <td class="fw-medium">: <?php echo $row['processing_time']; ?> detik</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <h6 class="text-uppercase text-muted small fw-bold mb-3">Parameter Model</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" style="width: 120px;">Epoch</td>
                        <td class="fw-medium">: <?php echo isset($row['epoch']) ? $row['epoch'] : 10; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Batch Size</td>
                        <td class="fw-medium">: <?php echo isset($row['batch_size']) ? $row['batch_size'] : 4; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Learning Rate</td>
                        <td class="fw-medium">: <?php echo isset($row['learning_rate']) ? $row['learning_rate'] : 0.01; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td class="fw-bold text-uppercase text-success">: Selesai (Completed)</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                <!-- Side-by-Side Comparison -->
                <?php if ($row['status'] == 'completed'): ?>
                <div class="row g-0">
                    <div class="col-md-6 position-relative border-end p-0">
                        <div class="bg-light text-center py-2 border-bottom">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-medium">Citra Asli</span>
                        </div>
                        <div class="p-3 bg-white text-center">
                            <img src="<?php echo $original_image; ?>" class="img-fluid rounded mb-3 shadow-sm" style="height: 350px; object-fit: contain; width: 100%;">
                            <a href="view_image.php?src=<?php echo urlencode($original_image); ?>&title=Citra Asli" class="btn btn-sm btn-outline-primary w-50 rounded-pill">
                                <i class="fas fa-expand me-1"></i> Perbesar
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 position-relative p-0">
                        <div class="bg-light text-center py-2 border-bottom">
                            <span class="badge bg-success bg-opacity-10 text-success fw-medium">Hasil Masking</span>
                        </div>
                        <div class="p-3 bg-white text-center">
                            <img src="<?php echo $mask_image; ?>" class="img-fluid rounded mb-3 shadow-sm" style="height: 350px; object-fit: contain; width: 100%; image-rendering: pixelated;">
                            <a href="view_image.php?src=<?php echo urlencode($mask_image); ?>&title=Hasil Masking" class="btn btn-sm btn-outline-success w-50 rounded-pill">
                                <i class="fas fa-expand me-1"></i> Perbesar
                            </a>
                        </div>
                    </div>
                </div>
                <?php elseif ($row['status'] == 'failed'): ?>
                    <div class="text-center py-5">
                         <div class="text-danger mb-3"><i class="fas fa-times-circle fa-4x"></i></div>
                         <h4 class="text-danger">Processing Failed</h4>
                         <p class="text-muted">Something went wrong during the AI analysis.</p>
                         <p class="small text-muted">Please check if the image format is supported or try another image.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                         <div class="spinner-border text-primary mb-3" role="status"></div>
                         <h4>AI is analyzing the image...</h4>
                         <p>This usually takes 2-5 seconds.</p>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Footer removed as buttons moved inside -->
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body">
                <h5 class="card-title text-primary fw-bold mb-4"><i class="fas fa-chart-pie me-2"></i>Analisis Dampak Banjir</h5>
                
                <?php
                $pct = isset($row['flood_percentage']) ? $row['flood_percentage'] : 0;
                
                // Logic Status
                if ($pct < 10) {
                    $status_label = "Rendah / Aman";
                    $status_color = "success";
                    $status_icon = "fa-check-circle";
                    $status_desc = "Genangan air minimal terdeteksi.";
                } elseif ($pct < 40) {
                    $status_label = "Sedang / Waspada";
                    $status_color = "warning";
                    $status_icon = "fa-exclamation-circle";
                    $status_desc = "Area terdampak cukup signifikan. Perlu pemantauan.";
                } else {
                    $status_label = "Tinggi / Bahaya";
                    $status_color = "danger";
                    $status_icon = "fa-radiation";
                    $status_desc = "Banjir meluas. Area terdampak sangat besar.";
                }
                ?>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold text-dark">Persentase Area Tergenang</span>
                        <span class="fw-bold text-<?php echo $status_color; ?>"><?php echo $pct; ?>%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar bg-<?php echo $status_color; ?>" role="progressbar" style="width: <?php echo $pct; ?>%" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="alert alert-<?php echo $status_color; ?> d-flex align-items-center border-0 shadow-sm" role="alert">
                    <i class="fas <?php echo $status_icon; ?> fa-2x me-3"></i>
                    <div>
                        <div class="fw-bold text-uppercase small">Status Level</div>
                        <div class="fs-5 fw-bold"><?php echo $status_label; ?></div>
                        <div class="small mt-1 opacity-75"><?php echo $status_desc; ?></div>
                    </div>
                </div>

                <hr class="my-4 opacity-10">

                <ul class="list-unstyled">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Model AI:</span>
                        <span class="fw-medium">Residual U-Net</span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Epoch Setting:</span>
                        <span class="fw-medium"><?php echo isset($row['epoch']) ? $row['epoch'] : 10; ?></span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Batch Size:</span>
                        <span class="fw-medium"><?php echo isset($row['batch_size']) ? $row['batch_size'] : 4; ?></span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Learning Rate:</span>
                        <span class="fw-medium"><?php echo isset($row['learning_rate']) ? $row['learning_rate'] : 0.01; ?></span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Waktu Inferensi:</span>
                        <span class="fw-medium"><?php echo ($row['processing_time']) ? $row['processing_time'] . ' detik' : '-'; ?></span>
                    </li>
                    <li class="mb-0 d-flex justify-content-between">
                        <span class="text-muted">Resolusi Input:</span>
                        <span class="fw-medium">256x256 px (Resized)</span>
                    </li>
                </ul>

                <?php if ($row['iou_score'] !== null): ?>
                <hr class="my-4 opacity-10">
                <h6 class="fw-bold text-dark mb-3">Evaluasi Metrik (vs Ground Truth)</h6>
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="small text-muted mb-1">IoU</div>
                            <div class="fw-bold text-primary"><?php echo $row['iou_score']; ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="small text-muted mb-1">Dice</div>
                            <div class="fw-bold text-info"><?php echo $row['dice_score']; ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="small text-muted mb-1">Akurasi</div>
                            <div class="fw-bold text-success"><?php echo $row['pixel_accuracy']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-center text-center p-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fas fa-map-marked-alt fa-3x text-primary"></i>
                    </div>
                </div>
                <h4 class="fw-bold">Langkah Selanjutnya</h4>
                <p class="text-muted mb-4">Gunakan hasil masking ini untuk menghitung total luas area banjir secara real (km²) atau overlay pada peta GIS.</p>
                
                <div class="d-grid gap-2">
                    <a href="index.php" class="btn btn-outline-primary py-2 fw-medium">
                        <i class="fas fa-redo me-2"></i>Proses Gambar Lain
                    </a>
                    <a href="history.php" class="btn btn-link text-muted">
                        Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
