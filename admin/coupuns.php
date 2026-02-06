<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Kupon';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_coupon'])) {
    $code = strtoupper($_POST['code']);
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $min_purchase = $_POST['min_purchase'];
    $max_usage = $_POST['max_usage'];
    $valid_from = $_POST['valid_from'] ?: null;
    $valid_until = $_POST['valid_until'] ?: null;
    $status = $_POST['status'];
    
    if (isset($_POST['coupon_id']) && $_POST['coupon_id']) {
        // Update
        $stmt = $db->prepare("UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, min_purchase = ?, max_usage = ?, valid_from = ?, valid_until = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$code, $discount_type, $discount_value, $min_purchase, $max_usage, $valid_from, $valid_until, $status, $_POST['coupon_id']])) {
            $success = "Kupon berjaya dikemaskini";
        }
    } else {
        // Insert
        $stmt = $db->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_purchase, max_usage, valid_from, valid_until, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$code, $discount_type, $discount_value, $min_purchase, $max_usage, $valid_from, $valid_until, $status])) {
            $success = "Kupon berjaya ditambah";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM coupons WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        $success = "Kupon berjaya dipadam";
    }
}

// Get all coupons
$coupons = $db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kupon</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kupon</li>
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Senarai Kupon</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                        <i class="fas fa-plus"></i> Tambah Kupon
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kod</th>
                            <th>Jenis</th>
                            <th>Nilai</th>
                            <th>Min. Pembelian</th>
                            <th>Penggunaan</th>
                            <th>Tempoh Sah</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $coupon): ?>
                        <tr>
                            <td><?php echo $coupon['id']; ?></td>
                            <td><strong><?php echo $coupon['code']; ?></strong></td>
                            <td>
                                <?php if ($coupon['discount_type'] == 'fixed'): ?>
                                    <span class="badge badge-info">Tetap</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Peratus</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ($coupon['discount_type'] == 'fixed') {
                                    echo 'RM' . number_format($coupon['discount_value'], 2);
                                } else {
                                    echo $coupon['discount_value'] . '%';
                                }
                                ?>
                            </td>
                            <td>RM<?php echo number_format($coupon['min_purchase'], 2); ?></td>
                            <td><?php echo $coupon['used_count']; ?> / <?php echo $coupon['max_usage'] ?: '∞'; ?></td>
                            <td>
                                <?php 
                                if ($coupon['valid_from'] && $coupon['valid_until']) {
                                    echo date('d/m/Y', strtotime($coupon['valid_from'])) . ' - ' . date('d/m/Y', strtotime($coupon['valid_until']));
                                } else {
                                    echo 'Tiada had';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($coupon['status'] == 'active'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $coupon['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?php echo $coupon['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Adakah anda pasti?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $coupon['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Kupon</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                            
                                            <div class="form-group">
                                                <label>Kod Kupon *</label>
                                                <input type="text" name="code" class="form-control text-uppercase" value="<?php echo $coupon['code']; ?>" required>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Jenis Diskaun *</label>
                                                        <select name="discount_type" class="form-control" required>
                                                            <option value="fixed" <?php echo ($coupon['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Tetap (RM)</option>
                                                            <option value="percentage" <?php echo ($coupon['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Peratus (%)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nilai Diskaun *</label>
                                                        <input type="number" step="0.01" name="discount_value" class="form-control" value="<?php echo $coupon['discount_value']; ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Min. Pembelian (RM)</label>
                                                        <input type="number" step="0.01" name="min_purchase" class="form-control" value="<?php echo $coupon['min_purchase']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Max. Penggunaan</label>
                                                        <input type="number" name="max_usage" class="form-control" value="<?php echo $coupon['max_usage']; ?>" placeholder="0 = Tanpa had">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sah Dari</label>
                                                        <input type="date" name="valid_from" class="form-control" value="<?php echo $coupon['valid_from']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sah Hingga</label>
                                                        <input type="date" name="valid_until" class="form-control" value="<?php echo $coupon['valid_until']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Status *</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="active" <?php echo ($coupon['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                                    <option value="inactive" <?php echo ($coupon['status'] == 'inactive') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" name="save_coupon" class="btn btn-primary">Simpan</button>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Kupon</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kod Kupon *</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="Contoh: SAVE10" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Diskaun *</label>
                                <select name="discount_type" class="form-control" required>
                                    <option value="fixed">Tetap (RM)</option>
                                    <option value="percentage">Peratus (%)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Diskaun *</label>
                                <input type="number" step="0.01" name="discount_value" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Min. Pembelian (RM)</label>
                                <input type="number" step="0.01" name="min_purchase" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Max. Penggunaan</label>
                                <input type="number" name="max_usage" class="form-control" value="0" placeholder="0 = Tanpa had">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sah Dari</label>
                                <input type="date" name="valid_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sah Hingga</label>
                                <input type="date" name="valid_until" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="save_coupon" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>