<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dataset.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT h.*, u.name as user_name, u.email as user_email 
        FROM analysis_history h 
        LEFT JOIN users u ON h.user_id = u.id 
        WHERE h.id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Data not found";
    exit();
}

$row = $result->fetch_assoc();
$original_image = "../uploads/original/" . $row['original_filename'];
$mask_image = $row['result_filename'] ? "../uploads/result/" . $row['result_filename'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Analisis #<?php echo $id; ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar-glass border-end min-vh-100 p-3 d-flex flex-column" style="width: 280px; position: fixed; height: 100vh; z-index: 1000;">
        <div class="px-2 mb-4 mt-2 d-flex align-items-center gap-3">
            <div class="logo-icon bg-primary text-white rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-layer-group text-white"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">FloodSeg</h5>
                <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Admin Panel</small>
            </div>
        </div>
        
        <div class="list-group list-group-flush flex-grow-1 gap-2">
            <a href="dashboard.php" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center p-3">
                <i class="fas fa-home me-3 fa-fw"></i> Dashboard
            </a>
            <a href="dataset.php" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center p-3">
                <i class="fas fa-database me-3 fa-fw"></i> Dataset & Analisis
            </a>
            <a href="analytics.php" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center p-3">
                <i class="fas fa-chart-line me-3 fa-fw"></i> Performa Model
            </a>
            <a href="messages.php" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center p-3">
                <i class="fas fa-envelope me-3 fa-fw"></i> Pesan Masuk
            </a>
        </div>

        <div class="mt-auto border-top pt-3">
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger rounded-3 border-0 d-flex align-items-center p-3 hover-danger">
                <i class="fas fa-sign-out-alt me-3 fa-fw"></i> Logout
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-grow-1 p-4" style="margin-left: 280px;">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
            <div>
                <a href="javascript:history.back()" class="btn btn-white btn-sm rounded-pill mb-2 border shadow-sm text-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                <h2 class="fw-bold">Detail Analisis #<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?></h2>
            </div>
            <div>
                <?php if($mask_image): ?>
                <a href="<?php echo $mask_image; ?>" download class="btn btn-success rounded-pill shadow-sm px-4"><i class="fas fa-download me-2"></i>Download Mask</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- Images -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-3 glass-card">
                    <div class="row g-0">
                        <div class="col-md-6 position-relative border-end p-0">
                            <div class="bg-light text-center py-2 border-bottom rounded-top">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-medium rounded-pill px-3">Citra Asli</span>
                            </div>
                            <div class="p-3 bg-white text-center rounded-bottom">
                                <img src="<?php echo $original_image; ?>" class="img-fluid rounded mb-3 shadow-sm border" style="max-height: 400px; width: 100%; object-fit: contain;">
                                <a href="<?php echo $original_image; ?>" target="_blank" class="btn btn-sm btn-outline-primary w-50 rounded-pill">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Asli
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 position-relative p-0">
                            <div class="bg-light text-center py-2 border-bottom rounded-top">
                                <span class="badge bg-success bg-opacity-10 text-success fw-medium rounded-pill px-3">Hasil Masking</span>
                            </div>
                            <div class="p-3 bg-white text-center rounded-bottom">
                                <?php if($mask_image): ?>
                                    <img src="<?php echo $mask_image; ?>" class="img-fluid rounded mb-3 shadow-sm border bg-dark" style="max-height: 400px; width: 100%; object-fit: contain;">
                                    <a href="<?php echo $mask_image; ?>" target="_blank" class="btn btn-sm btn-outline-success w-50 rounded-pill">
                                        <i class="fas fa-external-link-alt me-1"></i> Buka Masking
                                    </a>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 350px;">
                                        <span class="text-muted">Proses Gagal / Belum Selesai</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meta Data -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 glass-card">
                    <div class="card-header bg-transparent border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Informasi Pengguna</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?php echo $row['user_name']; ?></h6>
                                <small class="text-muted"><?php echo $row['user_email']; ?></small>
                            </div>
                        </div>
                        <hr class="opacity-25">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Waktu Upload</span>
                            <span class="fw-medium"><?php echo date('d M Y, H:i', strtotime($row['upload_time'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <?php if($row['status'] == 'completed'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Selesai</span>
                            <?php elseif($row['status'] == 'failed'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Gagal</span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm glass-card">
                    <div class="card-header bg-transparent border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Metrik Analisis</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold">Persentase Banjir</label>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $row['flood_percentage']; ?>%"></div>
                                </div>
                                <span class="fw-bold"><?php echo $row['flood_percentage']; ?>%</span>
                            </div>
                        </div>
                        
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="bg-light p-2 rounded border">
                                    <small class="d-block text-muted">IoU</small>
                                    <span class="fw-bold text-primary"><?php echo $row['iou_score'] ?? '-'; ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light p-2 rounded border">
                                    <small class="d-block text-muted">Dice</small>
                                    <span class="fw-bold text-info"><?php echo $row['dice_score'] ?? '-'; ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light p-2 rounded border">
                                    <small class="d-block text-muted">Akurasi</small>
                                    <span class="fw-bold text-success"><?php echo $row['pixel_accuracy'] ?? '-'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted d-block mb-2 fw-bold">Technical Info:</small>
                            <code class="d-block bg-dark text-white p-3 rounded small">
                                Process Time: <?php echo $row['processing_time']; ?>s<br>
                                Epoch: <?php echo $row['epoch'] ?? 10; ?><br>
                                Batch Size: <?php echo $row['batch_size'] ?? 4; ?><br>
                                Learning Rate: <?php echo $row['learning_rate'] ?? 0.01; ?><br>
                                Resolution: 128x128 (Resized)<br>
                                Model: U-Net Lite
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Admin Specific Styles */
.sidebar-glass {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
}

.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.list-group-item {
    transition: all 0.2s;
    background: transparent;
    color: #64748b;
    font-weight: 500;
}

.list-group-item:hover {
    background: rgba(59, 130, 246, 0.05);
    color: var(--primary-color);
    transform: translateX(5px);
}

.list-group-item.active {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
}

.list-group-item.active:hover {
    transform: none;
}

.hover-danger:hover {
    background: rgba(239, 68, 68, 0.05);
    color: #ef4444 !important;
}

.btn-white {
    background-color: white;
    border-color: #e2e8f0;
}
.btn-white:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
}
</style>

</body>
</html>
