<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Check if date/time selected
if (!isset($_SESSION['booking']['date']) || !isset($_SESSION['booking']['time'])) {
    redirect('select-datetime.php');
}

$theme_id = json_decode($_SESSION['booking']['themes'])[0];
$theme = getThemeById($db, $theme_id);
$addons = getAddons($db);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['booking']['pax'] = $_POST['pax'];
    $_SESSION['booking']['selected_addons'] = isset($_POST['addons']) ? $_POST['addons'] : [];
    redirect('customer-info.php');
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
    <title>Pax & Tambahan - SPD Production</title>
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
                <a href="select-datetime.php" class="btn btn-link text-dark"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0 ml-3 font-weight-bold">Pax & Tambahan</h4>
                <div class="ml-auto">
                    <div class="progress-dots">
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot active"></span>
                        <span class="dot"></span>
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

                <h5 class="font-weight-bold mb-2">Pax & Tambahan</h5>
                <p class="text-muted mb-4">Masukkan total bilangan pax yang akan hadir.</p>

                <form method="POST" id="paxAddonsForm">
                    <!-- Pax Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users fa-2x text-muted mr-3"></i>
                                    <div>
                                        <h5 class="font-weight-bold mb-0">Bilangan Pax</h5>
                                        <small class="text-muted">Total Pax Hadir</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary rounded-circle pax-btn" id="decreasePax">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <h4 class="font-weight-bold mx-4 mb-0" id="paxCount">6</h4>
                                    <button type="button" class="btn btn-dark rounded-circle pax-btn" id="increasePax">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="pax" id="paxInput" value="6">

                    <!-- Add-ons Section -->
                    <h5 class="font-weight-bold mb-3">Tambahan (Add-ons)</h5>

                    <?php foreach ($addons as $addon): ?>
                    <div class="card addon-card shadow-sm mb-3" data-addon-id="<?php echo $addon['id']; ?>" data-price="<?php echo $addon['price']; ?>">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="addon-image-placeholder mr-3">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="font-weight-bold mb-0"><?php echo $addon['name']; ?></h6>
                                </div>
                                <div class="text-right">
                                    <h6 class="font-weight-bold mb-2">RM<?php echo number_format($addon['price'], 0); ?></h6>
                                    <button type="button" class="btn btn-sm btn-outline-dark add-addon-btn">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
                    <h4 class="font-weight-bold mb-0" id="totalAmount">RM99</h4>
                </div>
                <button type="button" class="btn btn-dark btn-lg px-5" id="continueBtn">
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
    let paxCount = 6;
    let selectedAddons = {};
    let basePrice = 99;

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

    // Pax controls
    $('#increasePax').on('click', function() {
        paxCount++;
        $('#paxCount').text(paxCount);
        $('#paxInput').val(paxCount);
    });

    $('#decreasePax').on('click', function() {
        if (paxCount > 1) {
            paxCount--;
            $('#paxCount').text(paxCount);
            $('#paxInput').val(paxCount);
        }
    });

    // Add-ons
    $('.add-addon-btn').on('click', function(e) {
        e.stopPropagation();
        const card = $(this).closest('.addon-card');
        const addonId = card.data('addon-id');
        const price = card.data('price');

        if (selectedAddons[addonId]) {
            // Remove addon
            delete selectedAddons[addonId];
            card.removeClass('selected');
            $(this).html('<i class="fas fa-plus"></i> Tambah')
                   .removeClass('btn-outline-danger')
                   .addClass('btn-outline-dark');
            card.find('.addon-badge').remove();
        } else {
            // Add addon
            selectedAddons[addonId] = price;
            card.addClass('selected');
            $(this).html('<i class="fas fa-trash"></i> Remove')
                   .removeClass('btn-outline-dark')
                   .addClass('btn-outline-danger');
            card.find('.flex-grow-1').append('<span class="badge badge-dark addon-badge ml-2">DITAMBAH</span>');
        }

        updateTotal();
    });

    function updateTotal() {
        let total = basePrice;
        for (let price of Object.values(selectedAddons)) {
            total += parseFloat(price);
        }
        $('#totalAmount').text('RM' + total.toFixed(0));
    }

    $('#continueBtn').on('click', function() {
        // Add selected addons to form
        for (let addonId in selectedAddons) {
            $('<input>').attr({
                type: 'hidden',
                name: 'addons[]',
                value: addonId
            }).appendTo('#paxAddonsForm');
        }

        $('#paxAddonsForm').submit();
    });
});
</script>
</body>
</html>