<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete Analysis
if (isset($_GET['delete'])) {
    $aid = $_GET['delete'];
    // Get file paths first to delete files
    $f = $conn->query("SELECT original_filename, result_filename FROM analysis_history WHERE id=$aid")->fetch_assoc();
    if ($f) {
        if(file_exists("../uploads/original/".$f['original_filename'])) unlink("../uploads/original/".$f['original_filename']);
        if($f['result_filename'] && file_exists("../uploads/result/".$f['result_filename'])) unlink("../uploads/result/".$f['result_filename']);
        
        $conn->query("DELETE FROM analysis_history WHERE id = $aid");
    }
    header("Location: dataset.php");
}

// Pagination Logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search Logic
$search_query = "";
if (isset($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $search_query = " AND (h.id LIKE '%$q%' OR u.name LIKE '%$q%')";
}

// Count Total
$total_sql = "SELECT COUNT(*) as total FROM analysis_history h LEFT JOIN users u ON h.user_id = u.id WHERE 1=1 $search_query";
$total_result = $conn->query($total_sql)->fetch_assoc();
$total_rows = $total_result['total'];
$total_pages = ceil($total_rows / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositori Dataset - Admin FloodSeg</title>
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
            <a href="dataset.php" class="list-group-item list-group-item-action active rounded-3 border-0 d-flex align-items-center p-3">
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
                <h2 class="fw-bold mb-1">Repositori Dataset</h2>
                <p class="text-muted mb-0">Manajemen terpusat data citra masukan dan hasil segmentasi model.</p>
            </div>
            <a href="../index.php" target="_blank" class="btn btn-white shadow-sm border rounded-pill px-4 fw-medium text-primary">
                <i class="fas fa-external-link-alt me-2"></i> Buka Aplikasi Utama
            </a>
        </div>

        <div class="card border-0 shadow-sm glass-card overflow-hidden">
            <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Riwayat Analisis Lengkap</h5>
                <div class="input-group" style="width: 250px;">
                    <form action="" method="GET" class="d-flex w-100">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari ID atau User..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th class="py-3">Preview Citra</th>
                                <th class="py-3">User Pengunggah</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Coverage (%)</th>
                                <th class="py-3">Process Time</th>
                                <th class="py-3">Waktu Upload</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT h.*, u.name as user_name 
                                    FROM analysis_history h 
                                    LEFT JOIN users u ON h.user_id = u.id 
                                    WHERE 1=1 $search_query
                                    ORDER BY h.upload_time DESC
                                    LIMIT $limit OFFSET $offset";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <div class="position-relative">
                                            <img src="../uploads/original/<?php echo $row['original_filename']; ?>" width="48" height="48" class="rounded-3 object-fit-cover border shadow-sm" data-bs-toggle="tooltip" title="Original">
                                        </div>
                                        <?php if($row['result_filename']): ?>
                                        <div class="position-relative">
                                            <img src="../uploads/result/<?php echo $row['result_filename']; ?>" width="48" height="48" class="rounded-3 object-fit-cover border shadow-sm bg-dark" data-bs-toggle="tooltip" title="Masking Result">
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-muted fw-bold small" style="width: 32px; height: 32px;">
                                            <?php echo strtoupper(substr($row['user_name'] ?? 'G', 0, 1)); ?>
                                        </div>
                                        <span class="fw-medium"><?php echo $row['user_name'] ?? 'Guest'; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'completed'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Selesai</span>
                                    <?php elseif($row['status'] == 'failed'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Gagal</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['flood_percentage']): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="width: 60px; height: 4px;">
                                                <div class="progress-bar <?php echo $row['flood_percentage'] > 40 ? 'bg-danger' : 'bg-primary'; ?>" role="progressbar" style="width: <?php echo $row['flood_percentage']; ?>%"></div>
                                            </div>
                                            <span class="small fw-bold"><?php echo $row['flood_percentage']; ?>%</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace small"><?php echo $row['processing_time'] ? $row['processing_time'].'s' : '-'; ?></td>
                                <td class="small text-muted"><?php echo date('d M, H:i', strtotime($row['upload_time'])); ?></td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group shadow-sm rounded-pill">
                                        <a href="detail_result.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-white border text-primary" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                        <?php if($row['result_filename']): ?>
                                        <a href="../uploads/result/<?php echo $row['result_filename']; ?>" download class="btn btn-sm btn-white border text-success" title="Download Masking"><i class="fas fa-download"></i></a>
                                        <?php endif; ?>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-white border text-danger" onclick="return confirm('Hapus data ini permanen?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data analisis yang tersimpan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <!-- Prev -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1" href="?page=<?php echo $page-1; ?><?php echo isset($_GET['q']) ? '&q='.$_GET['q'] : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                        </li>
                        
                        <!-- Numbers -->
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 <?php echo ($page == $i) ? 'shadow-sm' : ''; ?>" href="?page=<?php echo $i; ?><?php echo isset($_GET['q']) ? '&q='.$_GET['q'] : ''; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1" href="?page=<?php echo $page+1; ?><?php echo isset($_GET['q']) ? '&q='.$_GET['q'] : ''; ?>"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
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
