<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Update admin activity
$conn->query("UPDATE users SET last_activity = NOW() WHERE id = " . $_SESSION['user_id']);

// Handle Verification
if (isset($_GET['approve'])) {
    $uid = $_GET['approve'];
    $conn->query("UPDATE users SET is_verified = 1 WHERE id = $uid");
    header("Location: dashboard.php");
}

if (isset($_GET['reject'])) {
    $uid = $_GET['reject'];
    $conn->query("DELETE FROM users WHERE id = $uid");
    header("Location: dashboard.php");
}

// Stats
$total_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$pending_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE is_verified=0")->fetch_assoc()['c'];
$total_analysis = $conn->query("SELECT COUNT(*) as c FROM analysis_history")->fetch_assoc()['c'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FloodSeg</title>
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
            <a href="dashboard.php" class="list-group-item list-group-item-action active rounded-3 border-0 d-flex align-items-center p-3">
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
        <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
            <div>
                <h2 class="fw-bold mb-1">Dashboard Overview</h2>
                <p class="text-muted mb-0">Selamat datang kembali, Administrator.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="bg-white px-3 py-2 rounded-pill shadow-sm text-muted border">
                    <i class="far fa-calendar-alt me-2"></i> <?php echo date('d F Y'); ?>
                </span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 h-100 hover-up glass-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4 p-4 me-4">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold"><?php echo $total_users; ?></h2>
                            <p class="text-muted mb-0 fw-medium">Total Users</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 h-100 hover-up glass-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-4 p-4 me-4">
                            <i class="fas fa-user-clock fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold"><?php echo $pending_users; ?></h2>
                            <p class="text-muted mb-0 fw-medium">Menunggu Verifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 h-100 hover-up glass-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 p-4 me-4">
                            <i class="fas fa-chart-area fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold"><?php echo $total_analysis; ?></h2>
                            <p class="text-muted mb-0 fw-medium">Total Citra Dianalisis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="card border-0 shadow-sm mb-4 glass-card overflow-hidden">
            <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Permintaan Registrasi Baru</h5>
                <?php if($pending_users > 0): ?>
                <span class="badge bg-danger rounded-pill px-3"><?php echo $pending_users; ?> Pending</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Nama Pengguna</th>
                                <th class="py-3">Email Address</th>
                                <th class="py-3">Tanggal Daftar</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pending = $conn->query("SELECT * FROM users WHERE is_verified = 0 ORDER BY created_at DESC");
                            if ($pending->num_rows > 0):
                                while($row = $pending->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="ps-4 fw-medium">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-muted fw-bold" style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                        </div>
                                        <?php echo $row['name']; ?>
                                    </div>
                                </td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td class="pe-4 text-end">
                                    <a href="?approve=<?php echo $row['id']; ?>" class="btn btn-sm btn-success rounded-pill px-3 me-1 shadow-sm"><i class="fas fa-check me-1"></i> Terima</a>
                                    <a href="?reject=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm" onclick="return confirm('Hapus user ini?')"><i class="fas fa-times me-1"></i> Tolak</a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="far fa-check-circle fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Tidak ada permintaan registrasi baru.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Verified Users List -->
        <div class="card border-0 shadow-sm glass-card overflow-hidden">
            <div class="card-header bg-transparent border-0 py-4 px-4">
                <h5 class="fw-bold mb-0">Daftar Pengguna Aktif</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th class="py-3">Nama Lengkap</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Role</th>
                                <th class="pe-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $active = $conn->query("SELECT *, UNIX_TIMESTAMP(last_activity) as last_activity_ts FROM users WHERE is_verified = 1 ORDER BY id ASC LIMIT 10");
                            while($row = $active->fetch_assoc()):
                                // Check online status (5 minutes threshold)
                                $is_online = false;
                                if ($row['last_activity_ts']) {
                                    if (time() - $row['last_activity_ts'] < 300) { // 300 seconds = 5 minutes
                                        $is_online = true;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td class="fw-medium">
                                    <a href="user_profile.php?id=<?php echo $row['id']; ?>" class="text-decoration-none fw-bold text-dark">
                                        <?php echo $row['name']; ?>
                                    </a>
                                </td>
                                <td class="text-muted"><?php echo $row['email']; ?></td>
                                <td>
                                    <?php if($row['role'] == 'admin'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4">
                                    <?php if($is_online): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="p-1 bg-success rounded-circle animate-pulse"></span>
                                        <span class="text-success fw-bold small">Online</span>
                                    </div>
                                    <?php else: ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="p-1 bg-secondary rounded-circle"></span>
                                        <span class="text-secondary fw-medium small">Offline</span>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Admin Specific Styles */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(74, 222, 128, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0);
    }
}

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
</style>

</body>
</html>
