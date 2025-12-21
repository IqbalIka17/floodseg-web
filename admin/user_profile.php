<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_GET['id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle Block/Unblock
if (isset($_GET['block'])) {
    $uid = $_GET['block'];
    $conn->query("UPDATE users SET is_verified = 2 WHERE id = $uid");
    header("Location: user_profile.php?id=$uid");
    exit();
}
if (isset($_GET['unblock'])) {
    $uid = $_GET['unblock'];
    $conn->query("UPDATE users SET is_verified = 1 WHERE id = $uid");
    header("Location: user_profile.php?id=$uid");
    exit();
}

// User Stats
$total_uploads = $conn->query("SELECT COUNT(*) as c FROM analysis_history WHERE user_id = $user_id")->fetch_assoc()['c'];
$last_upload = $conn->query("SELECT MAX(upload_time) as last FROM analysis_history WHERE user_id = $user_id")->fetch_assoc()['last'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Admin</title>
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
        <div class="mb-4 mt-2">
            <a href="dashboard.php" class="btn btn-white btn-sm rounded-pill mb-3 border shadow-sm text-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
            <h2 class="fw-bold">Detail Profil Pengguna</h2>
        </div>

        <div class="row g-4">
            <!-- User Info Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-4 h-100 glass-card">
                    <div class="mx-auto bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-4x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo $user['name']; ?></h4>
                    <p class="text-muted mb-3"><?php echo $user['email']; ?></p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2"><?php echo ucfirst($user['role']); ?></span>
                        <?php if($user['is_verified'] == 1): ?>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Verified</span>
                        <?php elseif($user['is_verified'] == 2): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Blocked</span>
                        <?php else: ?>
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">Pending</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4 px-4">
                        <?php if($user['is_verified'] == 1 && $user['role'] != 'admin'): ?>
                            <a href="?id=<?php echo $user['id']; ?>&block=<?php echo $user['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill w-100 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin memblokir user ini?')"><i class="fas fa-ban me-2"></i>Blokir User</a>
                        <?php elseif($user['is_verified'] == 2): ?>
                            <a href="?id=<?php echo $user['id']; ?>&unblock=<?php echo $user['id']; ?>" class="btn btn-outline-success btn-sm rounded-pill w-100 shadow-sm" onclick="return confirm('Aktifkan kembali user ini?')"><i class="fas fa-check-circle me-2"></i>Aktifkan User</a>
                        <?php endif; ?>
                    </div>

                    <ul class="list-group list-group-flush text-start bg-transparent">
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                            <span class="text-muted">ID User</span>
                            <span class="fw-medium font-monospace">#<?php echo str_pad($user['id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                            <span class="text-muted">Bergabung</span>
                            <span class="fw-medium"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                            <span class="text-muted">Total Upload</span>
                            <span class="fw-bold text-primary"><?php echo $total_uploads; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                            <span class="text-muted">Terakhir Login</span>
                            <span class="fw-bold text-info"><?php echo $user['last_login'] ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Belum pernah'; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- User Activity History -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100 glass-card overflow-hidden">
                    <div class="card-header bg-transparent border-0 py-4 px-4">
                        <h5 class="fw-bold mb-0">Riwayat Analisis Pengguna Ini</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">ID</th>
                                        <th class="py-3">Gambar</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Waktu</th>
                                        <th class="pe-4 py-3 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $history = $conn->query("SELECT * FROM analysis_history WHERE user_id = $user_id ORDER BY upload_time DESC");
                                    if ($history->num_rows > 0):
                                        while($h = $history->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-muted">#<?php echo $h['id']; ?></td>
                                        <td>
                                            <img src="../uploads/original/<?php echo $h['original_filename']; ?>" width="40" height="40" class="rounded-3 object-fit-cover shadow-sm border">
                                        </td>
                                        <td>
                                            <?php if($h['status'] == 'completed'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Selesai</span>
                                            <?php elseif($h['status'] == 'failed'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Gagal</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?php echo date('d M Y, H:i', strtotime($h['upload_time'])); ?></td>
                                        <td class="pe-4 text-end">
                                            <a href="detail_result.php?id=<?php echo $h['id']; ?>" class="btn btn-sm btn-white border text-primary shadow-sm rounded-pill"><i class="fas fa-eye me-1"></i> Detail</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat analisis.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

/* Custom Table Styles */
.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #64748b;
    border-bottom: 2px solid #f1f5f9;
}

.table tbody td {
    border-bottom: 1px solid #f1f5f9;
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(59, 130, 246, 0.02);
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
