<?php
include 'includes/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = "SELECT id FROM users WHERE email = '$email'";
    if ($conn->query($check)->num_rows > 0) {
        $error = "Email sudah terdaftar. Silakan gunakan email lain.";
    } else {
        $sql = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$password')";
        if ($conn->query($sql) === TRUE) {
            $success = "Registrasi berhasil! Akun Anda menunggu verifikasi Admin.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - FloodSeg AI</title>
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
                    <i class="fas fa-users fa-4x text-white"></i>
                </div>
            </div>
            <h1 class="fw-bold display-4 mb-3">Gabung Komunitas</h1>
            <p class="lead text-white-50 mb-4">Akses alat analisis canggih dan<br>kontribusikan data untuk penanggulangan banjir.</p>
            
            <div class="d-inline-block text-start mt-3">
                <div class="d-flex align-items-center text-white-50 mb-3">
                    <i class="fas fa-check-circle text-white me-3"></i>
                    <span class="fs-6">Akses penuh ke model segmentasi</span>
                </div>
                <div class="d-flex align-items-center text-white-50 mb-3">
                    <i class="fas fa-check-circle text-white me-3"></i>
                    <span class="fs-6">Riwayat analisis tak terbatas</span>
                </div>
                <div class="d-flex align-items-center text-white-50">
                    <i class="fas fa-check-circle text-white me-3"></i>
                    <span class="fs-6">Dukungan prioritas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Register Form -->
    <div class="col-lg-6 auth-form-container">
        <div class="w-100" style="max-width: 420px;">
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Buat Akun Baru 🚀</h3>
                <p class="text-muted">Isi formulir di bawah ini untuk mendaftar.</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger d-flex align-items-center border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div class="small fw-medium"><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success d-flex align-items-center border-0 bg-success bg-opacity-10 text-success rounded-3 mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div class="small fw-medium"><?php echo $success; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Nama Lengkap</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Alamat Email</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Nomor Telepon</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        <input type="tel" name="phone" class="form-control" placeholder="081234567890" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Password</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-text mt-2">Minimal 6 karakter kombinasi huruf & angka.</div>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary rounded-3 py-3 fw-bold shadow-primary">
                        Daftar Akun <i class="fas fa-user-plus ms-2"></i>
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-muted mb-0">Sudah memiliki akun? <a href="login.php" class="text-primary fw-bold text-decoration-none">Masuk</a></p>
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
