<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Tempahan';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $payment_status = $_POST['payment_status'];
    $booking_status = $_POST['booking_status'];
    
    $stmt = $db->prepare("UPDATE bookings SET payment_status = ?, booking_status = ? WHERE id = ?");
    if ($stmt->execute([$payment_status, $booking_status, $booking_id])) {
        $success = "Status tempahan berjaya dikemaskini";
    } else {
        $error = "Ralat mengemaskini status";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
    if ($stmt->execute([$booking_id])) {
        $success = "Tempahan berjaya dipadam";
    } else {
        $error = "Ralat memadam tempahan";
    }
}

// Get all bookings
$where = "1=1";
$params = [];

if (isset($_GET['status']) && $_GET['status'] != '') {
    $where .= " AND b.payment_status = ?";
    $params[] = $_GET['status'];
}

if (isset($_GET['date']) && $_GET['date'] == 'today') {
    $where .= " AND b.booking_date = CURDATE()";
}

if (isset($_GET['search']) && $_GET['search'] != '') {
    $where .= " AND (b.booking_reference LIKE ? OR b.customer_name LIKE ? OR b.customer_email LIKE ?)";
    $search = '%' . $_GET['search'] . '%';
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

$query = "
    SELECT b.*, t.name as theme_name 
    FROM bookings b 
    LEFT JOIN themes t ON b.theme_id = t.id 
    WHERE $where
    ORDER BY b.created_at DESC
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tempahan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tempahan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Senarai Tempahan</h3>
                <div class="card-tools">
                    <form method="GET" class="form-inline">
                        <div class="input-group input-group-sm mr-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?php echo $_GET['search'] ?? ''; ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="paid" <?php echo (isset($_GET['status']) && $_GET['status'] == 'paid') ? 'selected' : ''; ?>>Dibayar</option>
                            <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Batal</option>
                        </select>
                        <?php if (isset($_GET['search']) || isset($_GET['status'])): ?>
                        <a href="bookings.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rujukan</th>
                            <th>Pelanggan</th>
                            <th>Telefon</th>
                            <th>Tema</th>
                            <th>Tarikh</th>
                            <th>Masa</th>
                            <th>Pax</th>
                            <th>Jumlah</th>
                            <th>Status Bayaran</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo $booking['id']; ?></td>
                            <td><strong><?php echo $booking['booking_reference']; ?></strong></td>
                            <td><?php echo $booking['customer_name']; ?></td>
                            <td><?php echo $booking['customer_phone']; ?></td>
                            <td><?php echo $booking['theme_name']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></td>
                            <td><?php echo $booking['pax_count']; ?></td>
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
                                <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                    <span class="badge badge-info">Disahkan</span>
                                <?php elseif ($booking['booking_status'] == 'completed'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Batal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="booking-detail.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-info" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal<?php echo $booking['id']; ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Adakah anda pasti mahu memadam tempahan ini?')" title="Padam">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $booking['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Kemaskini Status</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            
                                            <div class="form-group">
                                                <label>Status Bayaran</label>
                                                <select name="payment_status" class="form-control" required>
                                                    <option value="pending" <?php echo ($booking['payment_status'] == 'pending') ? 'selected' : ''; ?>>Menunggu</option>
                                                    <option value="paid" <?php echo ($booking['payment_status'] == 'paid') ? 'selected' : ''; ?>>Dibayar</option>
                                                    <option value="cancelled" <?php echo ($booking['payment_status'] == 'cancelled') ? 'selected' : ''; ?>>Batal</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Status Tempahan</label>
                                                <select name="booking_status" class="form-control" required>
                                                    <option value="confirmed" <?php echo ($booking['booking_status'] == 'confirmed') ? 'selected' : ''; ?>>Disahkan</option>
                                                    <option value="completed" <?php echo ($booking['booking_status'] == 'completed') ? 'selected' : ''; ?>>Selesai</option>
                                                    <option value="cancelled" <?php echo ($booking['booking_status'] == 'cancelled') ? 'selected' : ''; ?>>Batal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" name="update_status" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#bookingsTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    });
});
</script>

<?php include 'includes/footer.php'; ?>