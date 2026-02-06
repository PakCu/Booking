<?php
session_start();
require_once '../config/config.php';
require_once 'includes/auth.php';

$page_title = 'Tetapan';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    // In production, you would store these in a settings table
    $success = "Tetapan berjaya dikemaskini";
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Simple validation (In production, use proper password hashing)
    if ($current_password === 'admin123') {
        if ($new_password === $confirm_password) {
            // Update password here
            $success = "Password berjaya ditukar";
        } else {
            $error = "Password baru tidak sepadan";
        }
    } else {
        $error = "Password semasa tidak betul";
    }
}

include 'includes/header.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tetapan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tetapan</li>
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

        <div class="row">
            <!-- General Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tetapan Umum</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Studio</label>
                                <input type="text" class="form-control" name="studio_name" value="SPD Production">
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea class="form-control" name="address" rows="3">Kampung Baru Balakong, Selangor, Malaysia</textarea>
                            </div>

                            <div class="form-group">
                                <label>Telefon</label>
                                <input type="text" class="form-control" name="phone" value="+60 12-345 6789">
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" value="info@spdproduction.my">
                            </div>

                            <div class="form-group">
                                <label>Waktu Operasi</label>
                                <input type="text" class="form-control" name="operating_hours" value="Isnin - Ahad: 9:00 AM - 10:00 PM">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="update_settings" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Tetapan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Booking Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tetapan Tempahan</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Jumlah Deposit (RM)</label>
                                <input type="number" class="form-control" name="deposit_amount" value="50" step="0.01">
                                <small class="form-text text-muted">Jumlah deposit yang perlu dibayar untuk mengesahkan tempahan</small>
                            </div>

                            <div class="form-group">
                                <label>Tempoh Kunci Slot (minit)</label>
                                <input type="number" class="form-control" name="slot_lock_duration" value="10">
                                <small class="form-text text-muted">Tempoh masa slot dikunci semasa proses tempahan</small>
                            </div>

                            <div class="form-group">
                                <label>Diskaun Automatik (%)</label>
                                <input type="number" class="form-control" name="auto_discount" value="0" step="0.01">
                                <small class="form-text text-muted">Diskaun automatik untuk semua tempahan</small>
                            </div>

                            <div class="form-group">
                                <label>Max. Tempahan Per Hari</label>
                                <input type="number" class="form-control" name="max_bookings_per_day" value="20">
                                <small class="form-text text-muted">Had maksimum tempahan yang boleh diterima sehari</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_notification" name="email_notification" checked>
                                    <label class="custom-control-label" for="email_notification">Hantar Email Pengesahan</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="whatsapp_notification" name="whatsapp_notification" checked>
                                    <label class="custom-control-label" for="whatsapp_notification">Hantar WhatsApp Reminder</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="update_settings" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Tetapan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tukar Password</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Password Semasa *</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>

                            <div class="form-group">
                                <label>Password Baru *</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>

                            <div class="form-group">
                                <label>Sahkan Password Baru *</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="change_password" class="btn btn-warning">
                                <i class="fas fa-key"></i> Tukar Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Gateway Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tetapan Payment Gateway</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Gateway Provider</label>
                                <select class="form-control" name="payment_gateway">
                                    <option value="billplz">Billplz</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="ipay88">iPay88</option>
                                    <option value="senangpay">SenangPay</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>API Key</label>
                                <input type="text" class="form-control" name="api_key" placeholder="Enter API Key">
                            </div>

                            <div class="form-group">
                                <label>API Secret</label>
                                <input type="password" class="form-control" name="api_secret" placeholder="Enter API Secret">
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="sandbox_mode" name="sandbox_mode" checked>
                                    <label class="custom-control-label" for="sandbox_mode">Sandbox Mode (Testing)</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="update_settings" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Tetapan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tetapan Email</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" class="form-control" name="smtp_host" placeholder="smtp.gmail.com">
                            </div>

                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="number" class="form-control" name="smtp_port" value="587">
                            </div>

                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="email" class="form-control" name="smtp_username" placeholder="your-email@gmail.com">
                            </div>

                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" class="form-control" name="smtp_password">
                            </div>

                            <div class="form-group">
                                <label>From Email</label>
                                <input type="email" class="form-control" name="from_email" value="noreply@spdproduction.my">
                            </div>

                            <div class="form-group">
                                <label>From Name</label>
                                <input type="text" class="form-control" name="from_name" value="SPD Production">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="update_settings" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Tetapan
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="testEmail()">
                                <i class="fas fa-envelope"></i> Test Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Media -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Media Sosial</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label><i class="fab fa-facebook"></i> Facebook URL</label>
                                <input type="url" class="form-control" name="facebook_url" placeholder="https://facebook.com/yourpage">
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-instagram"></i> Instagram URL</label>
                                <input type="url" class="form-control" name="instagram_url" placeholder="https://instagram.com/yourpage">
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-whatsapp"></i> WhatsApp Number</label>
                                <input type="text" class="form-control" name="whatsapp_number" placeholder="+60123456789">
                            </div>

                            <div class="form-group">
                                <label><i class="fab fa-tiktok"></i> TikTok URL</label>
                                <input type="url" class="form-control" name="tiktok_url" placeholder="https://tiktok.com/@yourpage">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="update_settings" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Tetapan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Maklumat Sistem</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Versi PHP</th>
                                <td><?php echo phpversion(); ?></td>
                            </tr>
                            <tr>
                                <th>Versi MySQL</th>
                                <td><?php echo $db->query('SELECT VERSION()')->fetchColumn(); ?></td>
                            </tr>
                            <tr>
                                <th>Server Software</th>
                                <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                            </tr>
                            <tr>
                                <th>Versi Sistem</th>
                                <td>SPD Production v1.0.0</td>
                            </tr>
                            <tr>
                                <th>Tarikh Install</th>
                                <td><?php echo date('d/m/Y'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function testEmail() {
    alert('Fungsi test email akan diimplementasikan. Email ujian akan dihantar ke alamat yang ditentukan.');
}
</script>

<?php include 'includes/footer.php'; ?>