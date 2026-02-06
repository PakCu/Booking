<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Dashboard';

// Get statistics
$stats = [];

// Total bookings
$stmt = $db->query("SELECT COUNT(*) as total FROM bookings");
$stats['total_bookings'] = $stmt->fetch()['total'];

// Today's bookings
$stmt = $db->query("SELECT COUNT(*) as total FROM bookings WHERE DATE(created_at) = CURDATE()");
$stats['today_bookings'] = $stmt->fetch()['total'];

// Total revenue
$stmt = $db->query("SELECT SUM(total_price) as total FROM bookings WHERE payment_status = 'paid'");
$stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;

// Pending bookings
$stmt = $db->query("SELECT COUNT(*) as total FROM bookings WHERE payment_status = 'pending'");
$stats['pending_bookings'] = $stmt->fetch()['total'];

// Recent bookings
$recent_bookings = $db->query("
    SELECT b.*, t.name as theme_name 
    FROM bookings b 
    LEFT JOIN themes t ON b.theme_id = t.id 
    ORDER BY b.created_at DESC 
    LIMIT 10
")->fetchAll();

// Upcoming sessions (next 7 days)
$upcoming_sessions = $db->query("
    SELECT b.*, t.name as theme_name 
    FROM bookings b 
    LEFT JOIN themes t ON b.theme_id = t.id 
    WHERE b.booking_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND b.booking_status = 'confirmed'
    ORDER BY b.booking_date ASC, b.booking_time ASC
    LIMIT 5
")->fetchAll();

// Monthly revenue chart data
$monthly_revenue = $db->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(total_price) as revenue
    FROM bookings
    WHERE payment_status = 'paid'
    AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
")->fetchAll();

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $stats['total_bookings']; ?></h3>
                        <p>Jumlah Tempahan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <a href="bookings.php" class="small-box-footer">
                        Lihat detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>RM<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                        <p>Jumlah Pendapatan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="reports.php" class="small-box-footer">
                        Lihat laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $stats['pending_bookings']; ?></h3>
                        <p>Menunggu Bayaran</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="bookings.php?status=pending" class="small-box-footer">
                        Lihat detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo $stats['today_bookings']; ?></h3>
                        <p>Tempahan Hari Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <a href="bookings.php?date=today" class="small-box-footer">
                        Lihat detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Revenue Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Pendapatan Bulanan
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Sesi Akan Datang
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            <?php foreach ($upcoming_sessions as $session): ?>
                            <li class="item">
                                <div class="product-info">
                                    <span class="product-title">
                                        <?php echo $session['customer_name']; ?>
                                        <span class="badge badge-info float-right">
                                            <?php echo $session['theme_name']; ?>
                                        </span>
                                    </span>
                                    <span class="product-description">
                                        <i class="far fa-calendar"></i> 
                                        <?php echo date('d M Y', strtotime($session['booking_date'])); ?>
                                        <br>
                                        <i class="far fa-clock"></i> 
                                        <?php echo date('g:i A', strtotime($session['booking_time'])); ?>
                                    </span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($upcoming_sessions)): ?>
                            <li class="item text-center py-3">
                                <span class="text-muted">Tiada sesi akan datang</span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="bookings.php">Lihat Semua Tempahan</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i>
                            Tempahan Terkini
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Rujukan</th>
                                    <th>Pelanggan</th>
                                    <th>Tema</th>
                                    <th>Tarikh</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['booking_reference']; ?></td>
                                    <td><?php echo $booking['customer_name']; ?></td>
                                    <td><?php echo $booking['theme_name']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                    <td>RM<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <?php if ($booking['payment_status'] == 'paid'): ?>
                                            <span class="badge badge-success">Dibayar</span>
                                        <?php elseif ($booking['payment_status'] == 'pending'): ?>
                                            <span class="badge badge-warning">Menunggu</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Batal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="booking-detail.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($monthly_revenue, 'month')); ?>,
        datasets: [{
            label: 'Pendapatan (RM)',
            data: <?php echo json_encode(array_column($monthly_revenue, 'revenue')); ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>