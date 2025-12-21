<?php 
include 'includes/database.php';
include 'includes/header.php'; 

$msg_sent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if ($conn->query($sql) === TRUE) {
        $msg_sent = true;
    }
}
?>

<div class="container py-5">
    <div class="row g-5">
        <!-- Contact Info -->
        <div class="col-lg-5">
            <h1 class="fw-bold display-5 mb-4">Hubungi Kami</h1>
            <p class="lead text-muted mb-5">
                Punya pertanyaan tentang cara kerja sistem atau ingin berkolaborasi? Jangan ragu untuk menghubungi kami.
            </p>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-white p-3 rounded-circle shadow-sm text-primary me-3 border">
                    <i class="fas fa-map-marker-alt fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Alamat</h5>
                    <p class="text-muted">Jl. Kampus No. 123, Gedung Riset AI,<br>Jakarta, Indonesia 10110</p>
                </div>
            </div>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-white p-3 rounded-circle shadow-sm text-primary me-3 border">
                    <i class="fas fa-envelope fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Email</h5>
                    <p class="text-muted">hello@floodseg-ai.com<br>support@floodseg-ai.com</p>
                </div>
            </div>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-white p-3 rounded-circle shadow-sm text-primary me-3 border">
                    <i class="fas fa-phone-alt fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Telepon</h5>
                    <p class="text-muted">+62 812 3456 7890</p>
                </div>
            </div>

            <!-- Social Media -->
            <div class="mt-5">
                <h6 class="fw-bold text-uppercase text-muted small mb-3">Ikuti Kami</h6>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                <h3 class="fw-bold mb-4">Kirim Pesan</h3>
                
                <?php if($msg_sent): ?>
                <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2 fa-lg"></i>
                    <div>
                        <strong>Pesan Terkirim!</strong> Terima kasih telah menghubungi kami. Kami akan membalas segera.
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-pill py-2 px-3" placeholder="Nama Anda" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" name="email" class="form-control rounded-pill py-2 px-3" placeholder="nama@email.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Subjek</label>
                            <input type="text" name="subject" class="form-control rounded-pill py-2 px-3" placeholder="Topik pesan..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Pesan</label>
                            <textarea name="message" class="form-control rounded-4 px-3 py-3" rows="5" placeholder="Tulis pesan Anda di sini..." required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-primary w-100">
                                Kirim Pesan <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
