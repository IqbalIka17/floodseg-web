<?php
include 'includes/database.php';
include 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    // Redirect guests to login if they try to access history directly
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: var(--primary-color);">Analysis History</h2>
    <a href="index.php" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>New Analysis</a>
</div>

<div class="row" id="history-container">
    <?php
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM analysis_history WHERE user_id = $user_id ORDER BY upload_time DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $status_badge = '';
            if ($row['status'] == 'completed') {
                $status_badge = '<span class="badge bg-success bg-opacity-75 shadow-sm position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill fw-medium" style="backdrop-filter: blur(4px);">Selesai</span>';
            } else if ($row['status'] == 'failed') {
                $status_badge = '<span class="badge bg-danger bg-opacity-75 shadow-sm position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill fw-medium" style="backdrop-filter: blur(4px);">Gagal</span>';
            } else {
                $status_badge = '<span class="badge bg-warning bg-opacity-75 text-dark shadow-sm position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill fw-medium" style="backdrop-filter: blur(4px);">Proses</span>';
            }
            
            // Format date
            $date = date("d M Y • H:i", strtotime($row['upload_time']));
            
            echo '
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card history-card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-up">
                    <div class="position-relative">
                        <img src="uploads/original/'.$row['original_filename'].'" class="card-img-top" alt="Original Image" style="height: 220px; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-to-t from-black-50">
                            '.$status_badge.'
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title fw-bold mb-1 text-dark">Analisis #'.str_pad($row['id'], 3, '0', STR_PAD_LEFT).'</h5>
                                <div class="text-muted small"><i class="far fa-calendar-alt me-2"></i>'.$date.'</div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-4">
                            <span class="badge bg-light text-secondary border fw-normal px-3 py-2 rounded-pill" title="Epoch"><i class="fas fa-layer-group me-1 text-primary"></i> '.($row['epoch'] ?? 10).'</span>
                            <span class="badge bg-light text-secondary border fw-normal px-3 py-2 rounded-pill" title="Batch Size"><i class="fas fa-boxes me-1 text-info"></i> '.($row['batch_size'] ?? 4).'</span>
                            <span class="badge bg-light text-secondary border fw-normal px-3 py-2 rounded-pill" title="Learning Rate"><i class="fas fa-bolt me-1 text-warning"></i> '.($row['learning_rate'] ?? 0.01).'</span>
                        </div>';
                        
            if ($row['status'] == 'completed') {
                echo '<a href="result.php?id='.$row['id'].'" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-primary-sm">Lihat Hasil <i class="fas fa-arrow-right ms-2"></i></a>';
            } else {
                echo '<button class="btn btn-secondary w-100 rounded-pill py-2 fw-medium" disabled>Sedang Memproses...</button>';
            }
            
            echo '
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '
        <div class="col-12 text-center py-5">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No history yet</h4>
            <p class="text-muted">Upload an image to start your first analysis.</p>
            <a href="index.php" class="btn btn-primary mt-2">Start Analysis</a>
        </div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>
