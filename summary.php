<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Check if all booking data is complete
if (!isset($_SESSION['booking']['terms_agreed'])) {
    redirect('terms.php');
}

$theme_id = json_decode($_SESSION['booking']['themes'])[0];
$theme = getThemeById($db, $theme_id);

// Calculate final pricing
$base_price = $theme['price'];
$discount = 51;
$addons_total = 0;
$selected_addon_details = [];

if (isset($_SESSION['booking']['selected_addons'])) {
    foreach ($_SESSION['booking']['selected_addons'] as $addon_id) {
        $addon = $db->prepare("SELECT * FROM addons WHERE id = ?");
        $addon->execute([$addon_id]);
        $addon_data = $addon->fetch();
        if ($addon_data) {
            $addons_total += $addon_data['price'];
            $selected_addon_details[] = $addon_data;
        }
    }
}

$subtotal = $base_price - $discount + $addons_total;
$deposit = DEPOSIT_AMOUNT;
$balance = $subtotal - $deposit;

// Generate booking reference
$booking_reference = generateBookingReference();

// Prepare booking data
$bookingData = [
    'reference' => $booking_reference,
    'theme_id' => $theme_id,
    'date' => $_SESSION['booking']['date'],
    'time' => $_SESSION['booking']['time'],
    'duration' => $theme['duration'],
    'customer_name' => $_SESSION['booking']['customer_name'],
    'customer_phone' => $_SESSION['booking']['customer_phone'],
    'customer_email' => $_SESSION['booking']['customer_email'],
    'pax' => $_SESSION['booking']['pax'],
    'base_price' => $base_price,
    'discount' => $discount,
    'addons_total' => $addons_total,
    'total_price' => $subtotal,
    'deposit' => $deposit,
    'balance' => $balance,
    'coupon_code' => isset($_SESSION['booking']['coupon_code']) ? $_SESSION['booking']['coupon_code'] : null,
    'slot_expires_at' => $_SESSION['booking']['slot_expires_at'],
    'addons' => []
];

// Prepare addons data
foreach ($selected_addon_details as $addon) {
    $bookingData['addons'][] = [
        'id' => $addon['id'],
        'quantity' => 1,
        'price' => $addon['price']
    ];
}

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    // Save booking to database
    $booking_id = saveBooking($db, $bookingData);
    
    if ($booking_id) {
        // Clear session
        unset($_SESSION['booking']);
        
        // Redirect to success page
        $_SESSION['booking_success'] = [
            'booking_id' => $booking_id,
            'reference' => $booking_reference,
            'amount_paid' => $deposit
        ];
        
        redirect('booking-success.php');
    } else {
        $error = "Ralat menyimpan tempahan. Sila cuba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - SPD Production</title>
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
                <a href="summary.php" class="btn btn-link text-dark"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0 ml-3 font-weight-bold">Pembayaran</h4>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content-wrapper" style="min-height: calc(100vh - 150px); background: #f5f5f5;">
        <section class="content">
            <div class="container py-4">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card mr-2"></i> Butiran Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted small text-uppercase mb-2">Rujukan Tempahan</h6>
                                <h5 class="font-weight-bold"><?php echo $booking_reference; ?></h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted small text-uppercase mb-2">Nama Pelanggan</h6>
                                <h5 class="font-weight-bold"><?php echo $_SESSION['booking']['customer_name']; ?></h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted small text-uppercase mb-2">Tarikh & Masa</h6>
                                <p class="mb-0">
                                    <?php 
                                    echo date('j F Y', strtotime($_SESSION['booking']['date']));
                                    echo '<br>' . $_SESSION['booking']['time'];
                                    ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted small text-uppercase mb-2">Tema</h6>
                                <p class="mb-0"><?php echo $theme['name']; ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <span>Harga Pakej:</span>
                                    <span class="float-right font-weight-bold">RM<?php echo number_format($base_price, 2); ?></span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-success">Diskaun:</span>
                                    <span class="float-right font-weight-bold text-success">-RM<?php echo number_format($discount, 2); ?></span>
                                </div>
                                <?php if ($addons_total > 0): ?>
                                <div class="mb-2">
                                    <span>Add-ons:</span>
                                    <span class="float-right font-weight-bold">+RM<?php echo number_format($addons_total, 2); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="mb-2">
                                        <span class="font-weight-bold">Jumlah:</span>
                                        <span class="float-right font-weight-bold">RM<?php echo number_format($subtotal, 2); ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-weight-bold">Deposit:</span>
                                        <span class="float-right font-weight-bold">RM<?php echo number_format($deposit, 2); ?></span>
                                    </div>
                                    <div class="border-top pt-2">
                                        <span class="font-weight-bold">Baki (bayar di studio):</span>
                                        <span class="float-right font-weight-bold">RM<?php echo number_format($balance, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Payment Method Selection -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wallet mr-2"></i> Pilih Kaedah Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="paymentForm">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="fpx" value="fpx" checked>
                                <label class="form-check-label" for="fpx">
                                    <strong>FPX Online Banking</strong>
                                    <small class="d-block text-muted">Bayar melalui online banking</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                <label class="form-check-label" for="card">
                                    <strong>Kad Kredit/Debit</strong>
                                    <small class="d-block text-muted">Visa, Mastercard</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="ewallet" value="ewallet">
                                <label class="form-check-label" for="ewallet">
                                    <strong>E-Wallet</strong>
                                    <small class="d-block text-muted">Touch 'n Go, GrabPay, Boost</small>
                                </label>
                            </div>

                            <input type="hidden" name="confirm_payment" value="1">
                            
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle"></i> 
                                <small>Anda akan diarahkan ke payment gateway untuk membuat pembayaran deposit sebanyak <strong>RM<?php echo number_format($deposit, 2); ?></strong></small>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg btn-block mt-4">
                                <i class="fas fa-lock mr-2"></i> BAYAR SEKARANG - RM<?php echo number_format($deposit, 2); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#paymentForm').on('submit', function(e) {
        const confirmed = confirm('Adakah anda pasti mahu meneruskan pembayaran sebanyak RM<?php echo number_format($deposit, 2); ?>?');
        return confirmed;
    });
});
</script>
</body>
</html>