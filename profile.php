<?php
include 'includes/database.php';
include 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Stats
$total_uploads = $conn->query("SELECT COUNT(*) as c FROM analysis_history WHERE user_id = $user_id")->fetch_assoc()['c'];
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <span class="fw-bold text-primary display-4"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                        </div>
                    </div>
                    
                    <h3 class="fw-bold mb-1"><?php echo $user['name']; ?></h3>
                    <p class="text-muted mb-3"><?php echo $user['email']; ?></p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 text-uppercase"><?php echo $user['role']; ?></span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> Verified
                        </span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small fw-bold text-uppercase">Bergabung</div>
                                <div class="fw-bold text-dark mt-1"><?php echo date('d M Y', strtotime($user['created_at'])); ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small fw-bold text-uppercase">Total Analisis</div>
                                <div class="fw-bold text-primary mt-1"><?php echo $total_uploads; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-4 rounded-3 text-start mb-4">
                        <h6 class="fw-bold mb-3">Kontak Info</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone-alt text-muted me-3" style="width: 20px;"></i>
                            <span><?php echo $user['phone'] ? $user['phone'] : '<span class="text-muted fst-italic">Belum diatur</span>'; ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope text-muted me-3" style="width: 20px;"></i>
                            <span><?php echo $user['email']; ?></span>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="edit_profile.php" class="btn btn-outline-primary rounded-pill py-3 fw-bold">
                            <i class="fas fa-edit me-2"></i>Edit Profil & Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>