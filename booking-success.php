<?php
require_once 'config/config.php';

// Check if booking success data exists
if (!isset($_SESSION['booking_success'])) {
    redirect('index.php');
}

$booking_data = $_SESSION['booking_success'];
unset($_SESSION['booking_success']);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempahan Berjaya - SPD Production</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="hold-transition">
<div class="wrapper">
    <div class="content-wrapper" style="min-height: 100vh; background: #f5f5f5;">
        <section class="content">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow-lg">
                            <div class="card-body text-center py-5">
                                <div class="success-icon mb-4">
                                    <i class="fas fa-check-circle fa-5x text-success"></i>
                                </div>
                                <h2 class="font-weight-bold mb-3">Tempahan Berjaya!</h2>
                                <p class="lead text-muted mb-4">Terima kasih kerana memilih SPD Production</p>

                                <div class="bg-light p-4 rounded mb-4">
                                    <h5 class="font-weight-bold mb-3">Butiran Tempahan</h5>
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <p class="mb-2"><strong>Rujukan:</strong></p>
                                            <p class="mb-2"><strong>Jumlah Dibayar:</strong></p>
                                        </div>
                                        <div class="col-6 text-left">
                                            <p class="mb-2"><?php echo $booking_data['reference']; ?></p>
                                            <p class="mb-2 text-success">RM<?php echo number_format($booking_data['amount_paid'], 2); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Pengesahan tempahan telah dihantar ke email anda.
                                </div>

                                <div class="mt-4">
                                    <a href="index.php" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-home mr-2"></i> Kembali ke Laman Utama
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>