<?php
include 'includes/database.php';
include 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = '';
$msg_type = '';

// Handle Info Update
if (isset($_POST['update_info'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    // Check if email taken by other
    $check = $conn->query("SELECT id FROM users WHERE email='$email' AND id != $user_id");
    if ($check->num_rows > 0) {
        $msg = "Email sudah digunakan oleh pengguna lain.";
        $msg_type = "danger";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$user_id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['name'] = $name;
            $msg = "Profil berhasil diperbarui!";
            $msg_type = "success";
        } else {
            $msg = "Gagal memperbarui profil: " . $conn->error;
            $msg_type = "danger";
        }
    }
}

// Handle Password Update
if (isset($_POST['update_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Verify current password
    $user_data = $conn->query("SELECT password FROM users WHERE id=$user_id")->fetch_assoc();
    
    if (password_verify($current_pass, $user_data['password'])) {
        if ($new_pass === $confirm_pass) {
            if (strlen($new_pass) >= 6) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password='$hashed_pass' WHERE id=$user_id");
                $msg = "Password berhasil diubah!";
                $msg_type = "success";
            } else {
                $msg = "Password baru minimal 6 karakter.";
                $msg_type = "danger";
            }
        } else {
            $msg = "Konfirmasi password tidak cocok.";
            $msg_type = "danger";
        }
    } else {
        $msg = "Password saat ini salah.";
        $msg_type = "danger";
    }
}

$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="mb-3">
                <a href="profile.php" class="text-decoration-none text-muted fw-bold"><i class="fas fa-arrow-left me-2"></i>Kembali ke Profil</a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white p-4 border-bottom">
                    <h4 class="fw-bold mb-0">Edit Profil</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php if($msg): ?>
                        <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <?php if($msg_type == 'success'): ?><i class="fas fa-check-circle me-2"></i><?php else: ?><i class="fas fa-exclamation-circle me-2"></i><?php endif; ?>
                            <?php echo $msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Info Form -->
                    <form method="POST" class="mb-5">
                        <h6 class="fw-bold text-primary mb-3 text-uppercase small">Informasi Pribadi</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-pill px-3" value="<?php echo $user['name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" name="email" class="form-control rounded-pill px-3" value="<?php echo $user['email']; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Nomor Telepon</label>
                            <input type="tel" name="phone" class="form-control rounded-pill px-3" value="<?php echo $user['phone']; ?>" placeholder="08123456789">
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="update_info" class="btn btn-primary rounded-pill fw-bold shadow-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                    <hr class="my-4 opacity-10">

                    <!-- Password Form -->
                    <form method="POST">
                        <h6 class="fw-bold text-danger mb-3 text-uppercase small">Ganti Password</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control rounded-pill px-3" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Password Baru</label>
                                <input type="password" name="new_password" class="form-control rounded-pill px-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Konfirmasi Baru</label>
                                <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="update_password" class="btn btn-outline-danger rounded-pill fw-bold">
                                Update Password
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>