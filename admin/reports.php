<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Laporan';

// Date filter
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

// Revenue statistics
$revenue_query = "
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_bookings,
        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
        SUM(CASE WHEN payment_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
        SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END) as total_revenue,
        SUM(CASE WHEN payment_status = 'paid' THEN deposit ELSE 0 END) as total_deposits,
        SUM(CASE WHEN payment_status = 'paid' THEN balance ELSE 0 END) as total_balance
    FROM bookings
    WHERE DATE(created_at) BETWEEN ? AND ?
";
$stmt = $db->prepare($revenue_query);
$stmt->execute([$date_from, $date_to]);
$stats = $stmt->fetch();

// Top themes
$top_themes = $db->prepare("
    SELECT t.name, COUNT(b.id) as bookings, SUM(b.total_price) as revenue
    FROM bookings b
    LEFT JOIN themes t ON b.theme_id = t.id
    WHERE DATE(b.created_at) BETWEEN ? AND ?
    GROUP BY t.id
    ORDER BY bookings DESC
    LIMIT 5
");
$top_themes->execute([$date_from, $date_to]);
$themes_data = $top_themes->fetchAll();

// Daily revenue
$daily_revenue = $db->prepare("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as bookings,
        SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END) as revenue
    FROM bookings
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
$daily_revenue->execute([$date_from, $date_to]);
$daily_data = $daily_revenue->fetchAll();

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Laporan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Filter -->
        <div class="card">
            <div class="card-body">
                <form method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label class="mr-2">Dari:</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" required>
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2">Hingga:</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Tapis
                    </button>
                    <a href="reports.php" class="btn btn-secondary ml-2">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </form>
            </div>
        </div>

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
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>RM<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                        <p>Jumlah Pendapatan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $stats['paid_bookings']; ?></h3>
                        <p>Tempahan Dibayar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo $stats['pending_bookings']; ?></h3>
                        <p>Menunggu Bayaran</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Daily Revenue Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pendapatan Harian</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyRevenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Themes -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tema Terpopular</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tema</th>
                                    <th class="text-right">Tempahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($themes_data as $theme): ?>
                                <tr>
                                    <td><?php echo $theme['name']; ?></td>
                                    <td class="text-right"><strong><?php echo $theme['bookings']; ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pecahan Pendapatan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Deposit Diterima</span>
                                        <span class="info-box-number">RM<?php echo number_format($stats['total_deposits'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Baki (Bayar di Studio)</span>
                                        <span class="info-box-number">RM<?php echo number_format($stats['total_balance'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Jumlah Keseluruhan</span>
                                        <span class="info-box-number">RM<?php echo number_format($stats['total_revenue'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
// Daily Revenue Chart
const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($daily_data, 'date')); ?>,
        datasets: [{
            label: 'Pendapatan (RM)',
            data: <?php echo json_encode(array_column($daily_data, 'revenue')); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgb(75, 192, 192)',
            borderWidth: 1
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