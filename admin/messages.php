<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $mid = $_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id = $mid");
    header("Location: messages.php");
    exit();
}

// Handle Mark as Read
if (isset($_GET['read'])) {
    $mid = $_GET['read'];
    $conn->query("UPDATE messages SET is_read = 1 WHERE id = $mid");
    header("Location: messages.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - Admin FloodSeg</title>
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
            <a href="messages.php" class="list-group-item list-group-item-action active rounded-3 border-0 d-flex align-items-center p-3">
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
                <h2 class="fw-bold mb-1">Pesan Masuk</h2>
                <p class="text-muted mb-0">Kelola pertanyaan dan pesan dari pengguna.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm glass-card overflow-hidden">
            <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Daftar Pesan</h5>
                <?php
                $unread_count = $conn->query("SELECT COUNT(*) as c FROM messages WHERE is_read = 0")->fetch_assoc()['c'];
                if($unread_count > 0): 
                ?>
                <span class="badge bg-danger rounded-pill px-3"><?php echo $unread_count; ?> Belum Dibaca</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Pengirim</th>
                                <th class="py-3">Subjek</th>
                                <th class="py-3">Pesan</th>
                                <th class="py-3">Waktu</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $msgs = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
                            if ($msgs->num_rows > 0):
                                while($row = $msgs->fetch_assoc()):
                                    $bg_class = $row['is_read'] ? '' : 'bg-primary bg-opacity-10';
                            ?>
                            <tr class="<?php echo $bg_class; ?>">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($row['email']); ?></div>
                                </td>
                                <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td class="text-muted small" style="max-width: 300px;">
                                    <?php echo substr(htmlspecialchars($row['message']), 0, 100) . (strlen($row['message']) > 100 ? '...' : ''); ?>
                                </td>
                                <td class="small text-muted text-nowrap"><?php echo date('d M, H:i', strtotime($row['created_at'])); ?></td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group shadow-sm rounded-pill">
                                        <?php if(!$row['is_read']): ?>
                                        <a href="?read=<?php echo $row['id']; ?>" class="btn btn-sm btn-white border text-success" title="Tandai Sudah Dibaca"><i class="fas fa-check-double"></i></a>
                                        <?php endif; ?>
                                        <a href="mailto:<?php echo $row['email']; ?>" class="btn btn-sm btn-white border text-primary" title="Balas Email"><i class="fas fa-reply"></i></a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-white border text-danger" onclick="return confirm('Hapus pesan ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada pesan masuk.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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