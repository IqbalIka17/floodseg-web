<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Update last activity if logged in
if (isset($_SESSION['user_id'])) {
    if (file_exists('includes/database.php')) include_once 'includes/database.php';
    elseif (file_exists('../includes/database.php')) include_once '../includes/database.php';
    
    if (isset($conn)) {
        $uid = $_SESSION['user_id'];
        $conn->query("UPDATE users SET last_activity = NOW() WHERE id = $uid");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Segmentation AI</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Modern Floating Navbar -->
<div class="container pt-3 sticky-top" style="z-index: 1030;">
    <nav class="navbar navbar-expand-lg navbar-light modern-navbar rounded-4 px-4 py-2">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <div class="logo-icon me-2">
                    <i class="fas fa-layer-group text-white"></i>
                </div>
                <span style="letter-spacing: -0.5px; color: #0f172a;">Flood<span class="text-primary">Seg</span></span>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Center Menu -->
                <ul class="navbar-nav mx-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="index.php">Beranda</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="history.php">Riwayat</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="about.php">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="contact.php">Kontak</a>
                    </li>
                </ul>

                <!-- Right Side Actions -->
                <ul class="navbar-nav align-items-center gap-2">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Logged In Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-medium" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['name']; ?>
                            </a>
                            <ul class="dropdown-menu border-0 shadow-sm rounded-3 dropdown-menu-end">
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item text-primary fw-medium" href="admin/dashboard.php">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item fw-medium" href="profile.php">
                                        <i class="fas fa-user-cog me-2"></i>Profil Saya
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <li class="nav-item">
                            <a class="btn btn-outline-primary rounded-pill px-4 fw-medium" href="login.php">Masuk</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary rounded-pill px-4 fw-medium shadow-primary-sm" href="register.php">Daftar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</div>

<div class="container main-content mt-4">
