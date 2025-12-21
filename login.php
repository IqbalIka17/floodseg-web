<?php
include 'includes/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            if ($row['is_verified'] == 1) {
                // Update last_login
                $conn->query("UPDATE users SET last_login = NOW() WHERE id = " . $row['id']);

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = $row['name'];
                
                if ($row['role'] == 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } elseif ($row['is_verified'] == 2) {
                $error = "Akun Anda telah dinonaktifkan/diblokir oleh Admin.";
            } else {
                $error = "Akun Anda sedang dalam peninjauan Admin.";
            }
        } else {
            $error = "Password yang Anda masukkan salah.";
        }
    } else {
        $error = "Email tidak terdaftar dalam sistem.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FloodSeg AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="row g-0 auth-wrapper">
    <!-- Left Side: Art & Branding -->
    <div class="col-lg-6 auth-sidebar d-none d-lg-flex flex-column align-items-center justify-content-center text-center p-5">
        <div class="auth-sidebar-content">
            <div class="mb-4">
                <div class="bg-white bg-opacity-25 p-3 rounded-4 d-inline-block">
                    <i class="fas fa-layer-group fa-4x text-white"></i>
                </div>
            </div>
            <h1 class="fw-bold display-4 mb-3">FloodSeg AI</h1>
            <p class="lead text-white-50 mb-4">Sistem Cerdas Deteksi & Analisis Banjir<br>Berbasis Deep Learning</p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="col-lg-6 auth-form-container">
        <div class="w-100" style="max-width: 420px;">
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Selamat Datang Kembali! 👋</h3>
                <p class="text-muted">Silakan masuk untuk melanjutkan analisis.</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger d-flex align-items-center border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div class="small fw-medium"><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Alamat Email</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Password</label>
                    </div>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary rounded-3 py-3 fw-bold shadow-primary">
                        Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-muted mb-0">Belum memiliki akun? <a href="register.php" class="text-primary fw-bold text-decoration-none">Daftar Sekarang</a></p>
                </div>
            </form>
            
            <div class="mt-5 text-center text-muted small opacity-50">
                &copy; <?php echo date('Y'); ?> FloodSeg AI. All rights reserved.
            </div>
        </div>
    </div>
</div>

</body>
</html>
