<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$themes = getThemes($db);

// Handle theme selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_themes'])) {
    $_SESSION['booking']['themes'] = $_POST['selected_themes'];
    $_SESSION['booking']['theme_prices'] = $_POST['theme_prices'];
    redirect('select-datetime.php');
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Tema - SPD Production</title>
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
                <a href="index.php" class="btn btn-link text-dark"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0 ml-3 font-weight-bold">Pilih Tema</h4>
                <div class="ml-auto">
                    <div class="progress-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
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
                <h2 class="font-weight-bold mb-2">Pilih Tema</h2>
                <p class="text-muted mb-4">Sila pilih konsep yang anda inginkan untuk sesi fotografi anda.</p>

                <form method="POST" id="themeForm">
                    <div class="row">
                        <?php foreach ($themes as $theme): ?>
                        <div class="col-md-6 mb-3">
                            <div class="theme-card card h-100" data-theme-id="<?php echo $theme['id']; ?>" data-price="<?php echo $theme['price']; ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="theme-image mr-3" style="background: linear-gradient(135deg, #8B7355 0%, #654321 100%);">
                                            <i class="fas fa-camera fa-2x text-white"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-weight-bold mb-1"><?php echo $theme['name']; ?></h5>
                                            <small class="text-muted">
                                                <i class="far fa-clock"></i> <?php echo $theme['duration']; ?>m
                                                <i class="fas fa-users ml-2"></i> <?php echo $theme['max_pax']; ?> pax
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            <h5 class="font-weight-bold mb-0">RM<?php echo number_format($theme['price'], 0); ?></h5>
                                        </div>
                                    </div>
                                    <div class="check-icon"><i class="fas fa-check"></i></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <input type="hidden" name="selected_themes" id="selectedThemes">
                    <input type="hidden" name="theme_prices" id="themePrices">
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
                    <h4 class="font-weight-bold mb-0" id="totalAmount">RM0</h4>
                </div>
                <button type="button" class="btn btn-dark btn-lg px-5" id="continueBtn" disabled>
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
    let selectedThemes = new Set();
    let themePrices = {};

    $('.theme-card').on('click', function() {
        const themeId = $(this).data('theme-id');
        const price = $(this).data('price');

        if (selectedThemes.has(themeId)) {
            selectedThemes.delete(themeId);
            delete themePrices[themeId];
            $(this).removeClass('selected');
        } else {
            selectedThemes.add(themeId);
            themePrices[themeId] = price;
            $(this).addClass('selected');
        }

        updateTotal();
    });

    function updateTotal() {
        let total = 0;
        for (let price of Object.values(themePrices)) {
            total += parseFloat(price);
        }

        $('#totalAmount').text('RM' + total.toFixed(0));
        
        if (selectedThemes.size > 0) {
            $('#continueBtn').prop('disabled', false);
        } else {
            $('#continueBtn').prop('disabled', true);
        }
    }

    $('#continueBtn').on('click', function() {
        if (selectedThemes.size === 0) {
            alert('Sila pilih sekurang-kurangnya satu tema.');
            return;
        }

        $('#selectedThemes').val(JSON.stringify([...selectedThemes]));
        $('#themePrices').val(JSON.stringify(themePrices));
        $('#themeForm').submit();
    });
});
</script>
</body>
</html>