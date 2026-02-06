<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Check if pax selected
if (!isset($_SESSION['booking']['pax'])) {
    redirect('pax-addons.php');
}

$theme_id = json_decode($_SESSION['booking']['themes'])[0];
$theme = getThemeById($db, $theme_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['booking']['customer_name'] = $_POST['customer_name'];
    $_SESSION['booking']['customer_phone'] = $_POST['customer_phone'];
    $_SESSION['booking']['customer_email'] = $_POST['customer_email'];
    redirect('terms.php');
}

// Calculate time remaining
$expires_at = strtotime($_SESSION['booking']['slot_expires_at']);
$now = time();
$remaining = max(0, $expires_at - $now);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maklumat Anda - SPD Production</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="hold-transition">
<div class="wrapper">
    <!-- Header -->
    <div class="booking-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="pax-addons.php" class="btn btn-link text-dark"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0 ml-3 font-weight-bold">Maklumat Anda</h4>
                <div class="ml-auto">
                    <div class="progress-dots">
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content-wrapper" style="min-height: calc(100vh - 150px); background: #f5f5f5;">
        <section class="content">
            <div class="container py-4">
                <!-- Theme Info -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="theme-image-small mr-3">
                                <i class="fas fa-camera fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-weight-bold mb-1"><?php echo $theme['name']; ?></h5>
                                <small class="text-muted">
                                    <?php 
                                    echo date('j M Y', strtotime($_SESSION['booking']['date']));
                                    echo ', ' . $_SESSION['booking']['time'];
                                    ?>
                                </small>
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-0">RM<?php echo number_format($theme['price'], 0); ?></h5>
                                <small><a href="select-datetime.php" class="text-muted">Tukar</a></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timer Alert -->
                <div class="alert alert-warning text-center">
                    <i class="fas fa-clock"></i> 
                    <strong>SLOT DIKUNCI: <span id="countdown"><?php echo gmdate("i:s", $remaining); ?></span></strong>
                     • <a href="select-datetime.php" class="alert-link">TUKAR</a>
                </div>

                <h5 class="font-weight-bold mb-2">Maklumat Anda</h5>
                <p class="text-muted mb-4">Sila isi maklumat anda di bawah untuk pengesahan tempahan.</p>

                <form method="POST" id="customerForm">
                    <div class="form-group">
                        <label class="text-uppercase text-muted small font-weight-bold">Nama Penuh</label>
                        <input type="text" class="form-control form-control-lg" name="customer_name" id="customerName" placeholder="Nama Penuh" required>
                    </div>

                    <div class="form-group">
                        <label class="text-uppercase text-muted small font-weight-bold">Nombor Telefon</label>
                        <input type="tel" class="form-control form-control-lg" name="customer_phone" id="customerPhone" placeholder="Nombor Telefon" required>
                        <small class="form-text text-muted">Lebih baik jika nombor mempunyai WhatsApp</small>
                    </div>

                    <div class="form-group">
                        <label class="text-uppercase text-muted small font-weight-bold">Emel</label>
                        <input type="email" class="form-control form-control-lg" name="customer_email" id="customerEmail" placeholder="Emel" required>
                        <small class="form-text text-muted">Pengesahan tempahan akan dihantar ke emel ini</small>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <!-- Bottom Bar -->
    <div class="bottom-bar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-uppercase text-muted d-block">Jumlah Anggaran</small>
                    <h4 class="font-weight-bold mb-0">RM219</h4>
                </div>
                <button type="button" class="btn btn-secondary btn-lg px-5" id="continueBtn" disabled>
                    SETERUSNYA <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Countdown timer
    let remainingSeconds = <?php echo $remaining; ?>;
    
    function updateCountdown() {
        if (remainingSeconds <= 0) {
            $('#countdown').text('0:00');
            alert('Slot telah tamat tempoh. Sila pilih slot baru.');
            window.location.href = 'select-datetime.php';
            return;
        }
        
        let mins = Math.floor(remainingSeconds / 60);
        let secs = remainingSeconds % 60;
        $('#countdown').text(mins + ':' + (secs < 10 ? '0' : '') + secs);
        remainingSeconds--;
        setTimeout(updateCountdown, 1000);
    }
    
    updateCountdown();

    // Form validation
    function validateForm() {
        const name = $('#customerName').val().trim();
        const phone = $('#customerPhone').val().trim();
        const email = $('#customerEmail').val().trim();

        // Update input styles
        if (name) {
            $('#customerName').addClass('filled');
        } else {
            $('#customerName').removeClass('filled');
        }

        if (phone) {
            $('#customerPhone').addClass('filled');
        } else {
            $('#customerPhone').removeClass('filled');
        }

        if (email) {
            $('#customerEmail').addClass('filled');
        } else {
            $('#customerEmail').removeClass('filled');
        }

        // Enable/disable button
        if (name && phone && email) {
            $('#continueBtn').removeClass('btn-secondary').addClass('btn-dark').prop('disabled', false);
        } else {
            $('#continueBtn').removeClass('btn-dark').addClass('btn-secondary').prop('disabled', true);
        }
    }

    $('input').on('input', validateForm);

    $('#continueBtn').on('click', function() {
        if ($('#customerForm')[0].checkValidity()) {
            $('#customerForm').submit();
        } else {
            alert('Sila lengkapkan semua maklumat yang diperlukan.');
        }
    });
});
</script>
</body>
</html>