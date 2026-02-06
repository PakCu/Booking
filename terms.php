<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Check if customer info filled
if (!isset($_SESSION['booking']['customer_name'])) {
    redirect('customer-info.php');
}

$theme_id = json_decode($_SESSION['booking']['themes'])[0];
$theme = getThemeById($db, $theme_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agree_terms'])) {
    $_SESSION['booking']['terms_agreed'] = true;
    redirect('summary.php');
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
    <title>Terma & Syarat - SPD Production</title>
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
                <a href="customer-info.php" class="btn btn-link text-dark"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0 ml-3 font-weight-bold">Terma & Syarat</h4>
                <div class="ml-auto">
                    <div class="progress-dots">
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot active"></span>
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

                <!-- Terms & Conditions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-4">Terma & Syarat Tempahan</h5>
                        
                        <h6 class="font-weight-bold mt-4 mb-3">Terms & Conditions</h6>

                        <h6 class="font-weight-bold mt-4">1. BOOKING POLICY</h6>
                        <ul class="mb-3">
                            <li>All bookings must be paid in full or with minimum deposit to confirm slot</li>
                            <li>Deposit is <strong>non-refundable</strong></li>
                            <li>Rescheduling allowed up to <strong>48 hours</strong> before session</li>
                            <li>Max <strong>12 person</strong> per session, addon session if more than 12 person</li>
                            <li>Kid under 7 years old Free</li>
                            <li>Deposit RM50 per booking</li>
                        </ul>

                        <h6 class="font-weight-bold mt-4">2. PHOTOGRAPHY SESSION</h6>
                        <ul class="mb-3">
                            <li>Please arrive <strong>10 minutes</strong> before scheduled time</li>
                            <li>Late arrivals may result in shortened session time</li>
                            <li>Studio provides basic props and backdrops</li>
                        </ul>

                        <h6 class="font-weight-bold mt-4">3. PHOTO DELIVERY</h6>
                        <ul class="mb-3">
                            <li>Digital copies delivered within <strong>24hours</strong></li>
                            <li>Printed photos ready for collection within <strong>1month</strong></li>
                            <li>Photos delivered via cloud storage link permanently</li>
                        </ul>

                        <h6 class="font-weight-bold mt-4">4. CANCELLATION POLICY</h6>
                        <ul class="mb-3">
                            <li>Non-refundable</li>
                        </ul>

                        <h6 class="font-weight-bold mt-4">5. LIABILITY</h6>
                        <ul class="mb-3">
                            <li>Studio not responsible for lost or damaged personal items</li>
                            <li>Weather-related cancellations: Full reschedule or refund</li>
                            <li>Studio reserves right to refuse service</li>
                        </ul>

                        <h6 class="font-weight-bold mt-4">6. COPYRIGHT</h6>
                        <ul class="mb-3">
                            <li>Studio retains copyright to all photos</li>
                            <li>Customer may use photos for <strong>personal use only</strong></li>
                            <li>Commercial use requires written permission</li>
                        </ul>

                        <p class="text-muted font-italic border-top pt-3 mt-4">
                            <small>Last updated: 21 January 2026</small>
                        </p>
                    </div>
                </div>

                <!-- Agreement Checkbox -->
                <form method="POST" id="termsForm">
                    <div class="card agreement-card shadow-sm mb-4" id="agreementBox">
                        <div class="card-body">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="agreeTerms" name="agree_terms" required>
                                <label class="custom-control-label" for="agreeTerms">
                                    <h6 class="font-weight-bold mb-1">Saya bersetuju dengan Terma & Syarat</h6>
                                    <small class="text-muted">Saya telah membaca dan memahami semua terma dan syarat di atas.</small>
                                </label>
                            </div>
                        </div>
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

    // Agreement checkbox
    $('#agreeTerms').on('change', function() {
        if ($(this).is(':checked')) {
            $('#agreementBox').addClass('checked');
            $('#continueBtn').removeClass('btn-secondary').addClass('btn-dark').prop('disabled', false);
        } else {
            $('#agreementBox').removeClass('checked');
            $('#continueBtn').removeClass('btn-dark').addClass('btn-secondary').prop('disabled', true);
        }
    });

    $('#continueBtn').on('click', function() {
        if (!$('#agreeTerms').is(':checked')) {
            alert('Sila bersetuju dengan Terma & Syarat terlebih dahulu.');
            return;
        }
        $('#termsForm').submit();
    });
});
</script>
</body>
</html>