<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Butiran Tempahan';

if (!isset($_GET['id'])) {
    header("Location: bookings.php");
    exit();
}

$booking_id = $_GET['id'];

// Get booking details
$stmt = $db->prepare("
    SELECT b.*, t.name as theme_name, t.duration 
    FROM bookings b 
    LEFT JOIN themes t ON b.theme_id = t.id 
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: bookings.php");
    exit();
}

// Get booking addons
$stmt = $db->prepare("
    SELECT ba.*, a.name as addon_name 
    FROM booking_addons ba 
    LEFT JOIN addons a ON ba.addon_id = a.id 
    WHERE ba.booking_id = ?
");
$stmt->execute([$booking_id]);
$addons = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Butiran Tempahan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="bookings.php">Tempahan</a></li>
                    <li class="breadcrumb-item active">Butiran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Maklumat Tempahan</h3>
                        <div class="card-tools">
                            <a href="bookings.php" class="btn btn-sm btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5>Rujukan Tempahan</h5>
                                <h3 class="text-primary"><?php echo $booking['booking_reference']; ?></h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <h5>Status</h5>
                                <?php if ($booking['payment_status'] == 'paid'): ?>
                                    <span class="badge badge-success badge-lg">DIBAYAR</span>
                                <?php elseif ($booking['payment_status'] == 'pending'): ?>
                                    <span class="badge badge-warning badge-lg">MENUNGGU</span>
                                <?php else: ?>
                                    <span class="badge badge-danger badge-lg">BATAL</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Maklumat Pelanggan</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Nama</th>
                                <td><?php echo $booking['customer_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Telefon</th>
                                <td>
                                    <a href="tel:<?php echo $booking['customer_phone']; ?>">
                                        <i class="fas fa-phone"></i> <?php echo $booking['customer_phone']; ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>
                                    <a href="mailto:<?php echo $booking['customer_email']; ?>">
                                        <i class="fas fa-envelope"></i> <?php echo $booking['customer_email']; ?>
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <h5 class="mb-3 mt-4">Butiran Sesi</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Tema</th>
                                <td><?php echo $booking['theme_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Tarikh</th>
                                <td><?php echo date('l, d F Y', strtotime($booking['booking_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Masa</th>
                                <td><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></td>
                            </tr>
                            <tr>
                                <th>Tempoh</th>
                                <td><?php echo $booking['duration']; ?> minit</td>
                            </tr>
                            <tr>
                                <th>Bilangan Pax</th>
                                <td><?php echo $booking['pax_count']; ?> orang</td>
                            </tr>
                        </table>

                        <?php if (!empty($addons)): ?>
                        <h5 class="mb-3 mt-4">Add-ons</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Kuantiti</th>
                                    <th class="text-right">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($addons as $addon): ?>
                                <tr>
                                    <td><?php echo $addon['addon_name']; ?></td>
                                    <td><?php echo $addon['quantity']; ?></td>
                                    <td class="text-right">RM<?php echo number_format($addon['price'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Ringkasan Bayaran</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Harga Pakej</td>
                                <td class="text-right">RM<?php echo number_format($booking['base_price'], 2); ?></td>
                            </tr>
                            <?php if ($booking['discount'] > 0): ?>
                            <tr>
                                <td>Diskaun</td>
                                <td class="text-right text-success">-RM<?php echo number_format($booking['discount'], 2); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($booking['addons_total'] > 0): ?>
                            <tr>
                                <td>Add-ons</td>
                                <td class="text-right">+RM<?php echo number_format($booking['addons_total'], 2); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($booking['coupon_code']): ?>
                            <tr>
                                <td>Kupon (<?php echo $booking['coupon_code']; ?>)</td>
                                <td class="text-right text-success">-RM10.00</td>
                            </tr>
                            <?php endif; ?>
                            <tr class="border-top">
                                <th>Jumlah</th>
                                <th class="text-right">RM<?php echo number_format($booking['total_price'], 2); ?></th>
                            </tr>
                            <tr>
                                <td>Deposit</td>
                                <td class="text-right">RM<?php echo number_format($booking['deposit'], 2); ?></td>
                            </tr>
                            <tr class="border-top">
                                <th>Baki</th>
                                <th class="text-right">RM<?php echo number_format($booking['balance'], 2); ?></th>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Maklumat Tambahan</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Ditempah pada:</strong><br>
                            <?php echo date('d/m/Y g:i A', strtotime($booking['created_at'])); ?>
                        </p>
                        <p class="mb-2">
                            <strong>Status Tempahan:</strong><br>
                            <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                <span class="badge badge-info">Disahkan</span>
                            <?php elseif ($booking['booking_status'] == 'completed'): ?>
                                <span class="badge badge-success">Selesai</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Batal</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="btn btn-primary btn-block" onclick="window.print(); return false;">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>