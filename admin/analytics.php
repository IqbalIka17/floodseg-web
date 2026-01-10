<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- Data Aggregation ---

// 1. Overall Model Performance (Only from rows with IoU score)
$perf = $conn->query("SELECT 
    AVG(iou_score) as avg_iou, 
    AVG(dice_score) as avg_dice, 
    AVG(pixel_accuracy) as avg_acc,
    AVG(processing_time) as avg_time
    FROM analysis_history WHERE iou_score IS NOT NULL")->fetch_assoc();

// 2. Success Rate
$total = $conn->query("SELECT COUNT(*) as c FROM analysis_history")->fetch_assoc()['c'];
$success = $conn->query("SELECT COUNT(*) as c FROM analysis_history WHERE status='completed'")->fetch_assoc()['c'];
$success_rate = $total > 0 ? round(($success / $total) * 100, 1) : 0;

// 3. Flood Severity Distribution
$severity = $conn->query("SELECT 
    CASE 
        WHEN flood_percentage < 10 THEN 'Rendah'
        WHEN flood_percentage < 40 THEN 'Sedang'
        ELSE 'Tinggi'
    END as level,
    COUNT(*) as count
    FROM analysis_history WHERE status='completed'
    GROUP BY level");

$sev_labels = [];
$sev_data = [];
while($row = $severity->fetch_assoc()) {
    $sev_labels[] = $row['level'];
    $sev_data[] = $row['count'];
}

// 4. Daily Activity (Last 7 Days)
$activity = $conn->query("SELECT DATE(upload_time) as date, COUNT(*) as count 
    FROM analysis_history 
    WHERE upload_time >= DATE(NOW()) - INTERVAL 7 DAY 
    GROUP BY date ORDER BY date ASC");

$act_labels = [];
$act_data = [];
while($row = $activity->fetch_assoc()) {
    $act_labels[] = date('d M', strtotime($row['date']));
    $act_data[] = $row['count'];
}

// 5. Flood Trend (Last 7 Days)
$flood_trend = $conn->query("SELECT DATE(upload_time) as date, AVG(flood_percentage) as avg_flood 
    FROM analysis_history 
    WHERE status='completed' AND upload_time >= DATE(NOW()) - INTERVAL 7 DAY 
    GROUP BY date ORDER BY date ASC");

$trend_labels = [];
$trend_data = [];
while($row = $flood_trend->fetch_assoc()) {
    $trend_labels[] = date('d M', strtotime($row['date']));
    $trend_data[] = round($row['avg_flood'], 1);
}

// 6. Top Active Users
$top_users = $conn->query("SELECT u.name, u.email, COUNT(h.id) as total_uploads 
    FROM users u 
    JOIN analysis_history h ON u.id = h.user_id 
    GROUP BY u.id 
    ORDER BY total_uploads DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Performa Model - Admin FloodSeg</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="analytics.php" class="list-group-item list-group-item-action active rounded-3 border-0 d-flex align-items-center p-3">
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
                <h2 class="fw-bold mb-1">Analisis Performa Model</h2>
                <p class="text-muted mb-0">Evaluasi komprehensif metrik kinerja model Residual U-Net dan statistik operasional.</p>
            </div>
            <a href="javascript:window.print()" class="btn btn-white shadow-sm border rounded-pill px-4 fw-medium text-secondary">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </a>
        </div>

        <!-- Performance Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100 glass-card hover-up">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-bullseye fa-2x text-primary opacity-50"></i>
                        </div>
                        <h1 class="display-5 fw-bold mb-0 text-primary"><?php echo number_format($perf['avg_iou'] ?? 0, 3); ?></h1>
                        <small class="text-muted fw-bold">Rata-rata IoU Score</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100 glass-card hover-up">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-dice-d20 fa-2x text-info opacity-50"></i>
                        </div>
                        <h1 class="display-5 fw-bold mb-0 text-info"><?php echo number_format($perf['avg_dice'] ?? 0, 3); ?></h1>
                        <small class="text-muted fw-bold">Rata-rata Dice Score</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100 glass-card hover-up">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                        </div>
                        <h1 class="display-5 fw-bold mb-0 text-success"><?php echo $success_rate; ?>%</h1>
                        <small class="text-muted fw-bold">Tingkat Keberhasilan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100 glass-card hover-up">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-stopwatch fa-2x text-warning opacity-50"></i>
                        </div>
                        <h1 class="display-5 fw-bold mb-0 text-warning"><?php echo number_format($perf['avg_time'] ?? 0, 2); ?>s</h1>
                        <small class="text-muted fw-bold">Rata-rata Waktu Proses</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Usage Chart -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100 glass-card">
                    <div class="card-header bg-transparent border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Aktivitas Analisis (7 Hari Terakhir)</h5>
                    </div>
                    <div class="card-body px-4">
                        <canvas id="activityChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- Severity Chart -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 glass-card">
                    <div class="card-header bg-transparent border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Distribusi Level Banjir</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 250px;">
                            <canvas id="severityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <!-- Flood Trend -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 glass-card">
                    <div class="card-header bg-transparent border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Tren Rata-rata Area Banjir (7 Hari)</h5>
                    </div>
                    <div class="card-body px-4">
                        <canvas id="floodTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 glass-card">
                    <div class="card-header bg-transparent border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Top 5 User Teraktif</h5>
                        <i class="fas fa-trophy text-warning"></i>
                    </div>
                    <div class="card-body px-4 p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Nama User</th>
                                        <th class="py-3 text-center">Total Upload</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($top_users->num_rows > 0): ?>
                                        <?php while($user = $top_users->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold small" style="width: 32px; height: 32px;">
                                                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium"><?php echo $user['name']; ?></div>
                                                        <small class="text-muted" style="font-size: 0.75rem;"><?php echo $user['email']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-dark"><?php echo $user['total_uploads']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="2" class="text-center py-4 text-muted">Belum ada data user.</td></tr>
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

.btn-white {
    background-color: white;
    border-color: #e2e8f0;
}
.btn-white:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
}
</style>

<script>
// Activity Chart
const ctx1 = document.getElementById('activityChart').getContext('2d');
// Create gradient for Activity chart
let gradientActivity = ctx1.createLinearGradient(0, 0, 0, 400);
gradientActivity.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue
gradientActivity.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($act_labels); ?>,
        datasets: [{
            label: 'Jumlah Gambar Diproses',
            data: <?php echo json_encode($act_data); ?>,
            borderColor: '#3b82f6',
            backgroundColor: gradientActivity,
            borderWidth: 3,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#3b82f6',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                titleColor: '#1e293b',
                bodyColor: '#475569',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                padding: 10,
                displayColors: true
            }
        },
        scales: { 
            x: { grid: { display: false } },
            y: { 
                beginAtZero: true, 
                ticks: { stepSize: 1 },
                grid: { borderDash: [2, 4], color: '#f1f5f9' }
            } 
        }
    }
});

// Severity Chart
const ctx2 = document.getElementById('severityChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($sev_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($sev_data); ?>,
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',  // Green
                'rgba(234, 179, 8, 0.8)',   // Yellow
                'rgba(239, 68, 68, 0.8)'    // Red
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { 
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                bodyColor: '#475569',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                padding: 10
            }
        }
    }
});

    // Flood Trend Chart
    const ctxTrend = document.getElementById('floodTrendChart').getContext('2d');
    let gradientTrend = ctxTrend.createLinearGradient(0, 0, 0, 400);
    gradientTrend.addColorStop(0, 'rgba(249, 115, 22, 0.5)'); // Orange
    gradientTrend.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($trend_labels); ?>,
            datasets: [{
                label: 'Rata-rata Area Banjir (%)',
                data: <?php echo json_encode($trend_data); ?>,
                backgroundColor: gradientTrend,
                borderColor: '#f97316',
                borderWidth: 2,
                borderRadius: 5,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: { 
                    beginAtZero: true, 
                    title: { display: true, text: 'Persentase (%)' },
                    grid: { borderDash: [2, 4], color: '#f1f5f9' }
                }
            }
        }
    });
</script>

</body>
</html>
