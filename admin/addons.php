<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Add-ons';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_addon'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    
    if (isset($_POST['addon_id']) && $_POST['addon_id']) {
        // Update
        $stmt = $db->prepare("UPDATE addons SET name = ?, description = ?, price = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $price, $status, $_POST['addon_id']])) {
            $success = "Add-on berjaya dikemaskini";
        }
    } else {
        // Insert
        $stmt = $db->prepare("INSERT INTO addons (name, description, price, status) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $price, $status])) {
            $success = "Add-on berjaya ditambah";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM addons WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        $success = "Add-on berjaya dipadam";
    }
}

// Get all addons
$addons = $db->query("SELECT * FROM addons ORDER BY name ASC")->fetchAll();

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add-ons</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Add-ons</li>
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
                <h3 class="card-title">Senarai Add-ons</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                        <i class="fas fa-plus"></i> Tambah Add-on
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Penerangan</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($addons as $addon): ?>
                        <tr>
                            <td><?php echo $addon['id']; ?></td>
                            <td><strong><?php echo $addon['name']; ?></strong></td>
                            <td><?php echo $addon['description']; ?></td>
                            <td>RM<?php echo number_format($addon['price'], 2); ?></td>
                            <td>
                                <?php if ($addon['status'] == 'active'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $addon['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?php echo $addon['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Adakah anda pasti?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $addon['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Add-on</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="addon_id" value="<?php echo $addon['id']; ?>">
                                            
                                            <div class="form-group">
                                                <label>Nama Add-on *</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo $addon['name']; ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Penerangan</label>
                                                <textarea name="description" class="form-control" rows="3"><?php echo $addon['description']; ?></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Harga (RM) *</label>
                                                        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $addon['price']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Status *</label>
                                                        <select name="status" class="form-control" required>
                                                            <option value="active" <?php echo ($addon['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                                            <option value="inactive" <?php echo ($addon['status'] == 'inactive') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" name="save_addon" class="btn btn-primary">Simpan</button>
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
                <h4 class="modal-title">Tambah Add-on</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Add-on *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Penerangan</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga (RM) *</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" class="form-control" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="save_addon" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>