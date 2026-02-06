<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Check if theme selected
if (!isset($_SESSION['booking']['themes'])) {
    redirect('select-theme.php');
}

$theme_id = json_decode($_SESSION['booking']['themes'])[0];
$theme = getThemeById($db, $theme_id);

// Handle date/time selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_date']) && isset($_POST['booking_time'])) {
    $_SESSION['booking']['date'] = $_POST['booking_date'];
    $_SESSION['booking']['time'] = $_POST['booking_time'];
    $_SESSION['booking']['slot_expires_at'] = calculateSlotExpiry();
    redirect('pax-addons.php');
}

// Get available dates (next 30 days)
$dates = [];
for ($i = 0; $i < 30; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $dates[] = [
        'date' => $date,
        'day' => date('D', strtotime($date)),
        'day_num' => date('j', strtotime($date)),
        'month' => date('M', strtotime($date))
    ];
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarikh & Masa - SPD Production</title>
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
                <a href="select-theme.php" class="btn btn-link text-dark"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0 ml-3 font-weight-bold">Tarikh & Masa</h4>
                <div class="ml-auto">
                    <div class="progress-dots">
                        <span class="dot active"></span>
                        <span class="dot active"></span>
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
                <!-- Theme Info -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="theme-image-small mr-3">
                                <i class="fas fa-camera fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-weight-bold mb-1"><?php echo $theme['name']; ?></h5>
                                <small class="text-muted">Pilih Tarikh & Masa</small>
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-0">RM<?php echo number_format($theme['price'], 0); ?></h5>
                                <small><a href="select-theme.php" class="text-muted">Tukar</a></small>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="font-weight-bold mb-2">Pilih Tarikh & Masa</h5>
                <p class="text-muted mb-4">Sila pilih tarikh dan masa slot untuk sesi fotografi anda.</p>

                <!-- Date Slider -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-3">
                        <div class="date-scroll" id="dateScroll">
                            <?php foreach ($dates as $date): ?>
                            <div class="date-card" data-date="<?php echo $date['date']; ?>">
                                <div class="star-badge">⭐</div>
                                <div class="date-month"><?php echo strtoupper($date['month']); ?></div>
                                <div class="date-number"><?php echo $date['day_num']; ?></div>
                                <div class="date-day"><?php echo $date['day']; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Time Slots -->
                <div id="timeSlotsContainer" style="display: none;">
                    <div id="loadingSlots" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Memuat slot masa...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat slot masa...</p>
                    </div>
                    <div id="timeGrid" class="row" style="display: none;"></div>
                </div>
            </div>
        </section>
    </div>

    <!-- Bottom Bar -->
    <div class="bottom-bar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-uppercase text-muted d-block">Jumlah Anggaran</small>
                    <h4 class="font-weight-bold mb-0" id="totalAmount">RM<?php echo number_format($theme['price'], 0); ?></h4>
                </div>
                <button type="button" class="btn btn-secondary btn-lg px-5" id="continueBtn" disabled>
                    SETERUSNYA <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<form method="POST" id="dateTimeForm">
    <input type="hidden" name="booking_date" id="bookingDate">
    <input type="hidden" name="booking_time" id="bookingTime">
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    let selectedDate = null;
    let selectedTime = null;

    // Date selection
    $('.date-card').on('click', function() {
        $('.date-card').removeClass('selected');
        $(this).addClass('selected');
        selectedDate = $(this).data('date');
        selectedTime = null;
        
        $('#timeSlotsContainer').show();
        $('#loadingSlots').show();
        $('#timeGrid').hide().html('');
        
        loadTimeSlots(selectedDate);
        updateUI();
    });

    function loadTimeSlots(date) {
        $.ajax({
            url: 'ajax/get-time-slots.php',
            method: 'POST',
            data: { date: date },
            success: function(response) {
                const slots = JSON.parse(response);
                let html = '';
                
                slots.forEach(function(slot) {
                    const disabledClass = slot.available ? '' : 'disabled';
                    html += `
                        <div class="col-6 mb-3">
                            <div class="time-slot ${disabledClass}" data-time="${slot.time}">
                                ${slot.time}
                            </div>
                        </div>
                    `;
                });
                
                $('#timeGrid').html(html);
                $('#loadingSlots').hide();
                $('#timeGrid').show();
                
                // Bind click event to time slots
                $('.time-slot:not(.disabled)').on('click', function() {
                    $('.time-slot').removeClass('selected');
                    $(this).addClass('selected');
                    selectedTime = $(this).data('time');
                    updateUI();
                });
            },
            error: function() {
                $('#loadingSlots').html('<p class="text-danger">Ralat memuatkan slot masa. Sila cuba lagi.</p>');
            }
        });
    }

    function updateUI() {
        if (selectedDate && selectedTime) {
            $('#continueBtn').removeClass('btn-secondary').addClass('btn-dark').prop('disabled', false);
            
            // Update total with special price
            $('#totalAmount').text('RM99');
        } else {
            $('#continueBtn').removeClass('btn-dark').addClass('btn-secondary').prop('disabled', true);
        }
    }

    $('#continueBtn').on('click', function() {
        if (!selectedDate || !selectedTime) {
            alert('Sila pilih tarikh dan masa terlebih dahulu.');
            return;
        }

        $('#bookingDate').val(selectedDate);
        $('#bookingTime').val(selectedTime);
        $('#dateTimeForm').submit();
    });
});
</script>
</body>
</html>