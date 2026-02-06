<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>SPD Production Admin</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <!-- Custom CSS -->
    <style>
        .brand-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background: linear-gradient(45deg, #f39c12, #e74c3c);
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="../index.php" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Lihat Website
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index.php" class="brand-link text-center">
            <span class="brand-text font-weight-bold">SPD Production</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo $_SESSION['admin_username']; ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="bookings.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bookings.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Tempahan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="themes.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'themes.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-palette"></i>
                            <p>Tema</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="addons.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'addons.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Add-ons</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="coupons.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'coupons.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-ticket-alt"></i>
                            <p>Kupon</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Tetapan</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">