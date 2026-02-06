<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Reset session for new booking
unset($_SESSION['booking']);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPD Production - Studio Fotografi Profesional</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-dark" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="brand-text font-weight-bold" style="background: linear-gradient(45deg, #f39c12, #e74c3c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 1.5rem;">SPD Production</span>
            </a>
            
            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="#home" class="nav-link">Utama</a></li>
                    <li class="nav-item"><a href="#services" class="nav-link">Perkhidmatan</a></li>
                    <li class="nav-item"><a href="#gallery" class="nav-link">Galeri</a></li>
                    <li class="nav-item"><a href="#testimonials" class="nav-link">Testimoni</a></li>
                    <li class="nav-item"><a href="#contact" class="nav-link">Hubungi</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="hero-overlay">
            <div class="hero-content text-center text-white">
                <h1 class="display-3 font-weight-bold mb-4" data-aos="fade-down">Abadikan Detik Berharga Anda</h1>
                <p class="lead mb-5" data-aos="fade-up">Perkhidmatan Fotografi & Videografi Profesional</p>
                <a href="select-theme.php" class="btn btn-lg btn-gradient px-5 py-3" data-aos="zoom-in">
                    <i class="fas fa-calendar-check mr-2"></i> Tempah Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5 bg-light" id="services">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Perkhidmatan Kami</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="service-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">💍</div>
                            <h5 class="card-title font-weight-bold">Perkahwinan</h5>
                            <p class="card-text">Rakaman istimewa untuk hari perkahwinan anda dengan pakej lengkap fotografi dan videografi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">🎓</div>
                            <h5 class="card-title font-weight-bold">Konvokesyen</h5>
                            <p class="card-text">Abadikan pencapaian gemilang anda dengan sesi fotografi konvokesyen yang profesional.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">🎂</div>
                            <h5 class="card-title font-weight-bold">Majlis & Event</h5>
                            <p class="card-text">Liputan penuh untuk majlis aqiqah, birthday, anniversary dan pelbagai majlis istimewa.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">👨‍👩‍👧‍👦</div>
                            <h5 class="card-title font-weight-bold">Potret Keluarga</h5>
                            <p class="card-text">Sesi fotografi keluarga di studio atau outdoor dengan konsep yang menarik.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">🏢</div>
                            <h5 class="card-title font-weight-bold">Korporat</h5>
                            <p class="card-text">Fotografi profesional untuk profil syarikat, produk dan acara korporat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">🎬</div>
                            <h5 class="card-title font-weight-bold">Video Production</h5>
                            <p class="card-text">Perkhidmatan videografi untuk komersial, dokumentari dan video promosi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-5" id="gallery">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Galeri Kerja Kami</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row">
                <?php
                $galleries = [
                    ['title' => 'Perkahwinan Melayu', 'subtitle' => 'Seri Pengantin', 'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'],
                    ['title' => 'Konvokesyen 2024', 'subtitle' => 'Universiti Malaya', 'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'],
                    ['title' => 'Majlis Aqiqah', 'subtitle' => 'Baby Aryan', 'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
                    ['title' => 'Family Portrait', 'subtitle' => 'Keluarga Ahmad', 'gradient' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'],
                    ['title' => 'Corporate Event', 'subtitle' => 'Annual Dinner', 'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'],
                    ['title' => 'Video Production', 'subtitle' => 'Commercial Shoot', 'gradient' => 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)']
                ];
                
                foreach ($galleries as $item):
                ?>
                <div class="col-md-4 mb-4">
                    <div class="gallery-item">
                        <div class="gallery-image" style="background: <?php echo $item['gradient']; ?>">
                            <span class="gallery-icon">📸</span>
                        </div>
                        <div class="gallery-overlay">
                            <h5><?php echo $item['title']; ?></h5>
                            <p><?php echo $item['subtitle']; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 testimonial-section" id="testimonials">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title text-white">Testimoni Pelanggan</h2>
                <div class="section-divider bg-white"></div>
            </div>
            <div class="row">
                <?php
                $testimonials = [
                    ['text' => 'Terima kasih SPD Production kerana mengabadikan detik perkahwinan kami dengan sempurna. Hasil foto dan video sangat memuaskan hati!', 'author' => 'Nurhanis & Ahmad'],
                    ['text' => 'Sangat profesional dan kreatif. Sesi fotografi konvokesyen kami berjalan lancar dan hasil gambar sangat cantik!', 'author' => 'Sarah Aisyah'],
                    ['text' => 'Pakej yang berbaloi dengan harga yang berpatutan. Kualiti kerja yang sangat baik. Highly recommended!', 'author' => 'En. Shahrul']
                ];
                
                foreach ($testimonials as $testimonial):
                ?>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card card h-100">
                        <div class="card-body">
                            <p class="card-text font-italic">"<?php echo $testimonial['text']; ?>"</p>
                            <p class="font-weight-bold text-warning mt-3 mb-0">- <?php echo $testimonial['author']; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5 bg-light" id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Hubungi Kami</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <form id="contactForm">
                                <div class="form-group">
                                    <label for="name">Nama Penuh</label>
                                    <input type="text" class="form-control" id="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">No. Telefon</label>
                                    <input type="tel" class="form-control" id="phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="service">Perkhidmatan</label>
                                    <input type="text" class="form-control" id="service" placeholder="Cth: Perkahwinan, Konvokesyen" required>
                                </div>
                                <div class="form-group">
                                    <label for="message">Mesej</label>
                                    <textarea class="form-control" id="message" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-gradient btn-block">
                                    <i class="fas fa-paper-plane mr-2"></i> Hantar Mesej
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-4">Maklumat Perhubungan</h5>
                            <div class="contact-info mb-4">
                                <i class="fas fa-map-marker-alt text-warning mr-3"></i>
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Alamat</h6>
                                    <p class="text-muted mb-0">Kampung Baru Balakong, Selangor, Malaysia</p>
                                </div>
                            </div>
                            <div class="contact-info mb-4">
                                <i class="fas fa-phone text-warning mr-3"></i>
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Telefon</h6>
                                    <p class="text-muted mb-0">+60 12-345 6789</p>
                                </div>
                            </div>
                            <div class="contact-info mb-4">
                                <i class="fas fa-envelope text-warning mr-3"></i>
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0">info@spdproduction.my</p>
                                </div>
                            </div>
                            <div class="contact-info">
                                <i class="fas fa-clock text-warning mr-3"></i>
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Waktu Operasi</h6>
                                    <p class="text-muted mb-0">Isnin - Ahad: 9:00 AM - 10:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer text-center" style="background: #1a1a2e; color: #fff; padding: 2rem 0;">
        <div class="container">
            <p class="mb-3">&copy; 2024 SPD Production. Hak Cipta Terpelihara.</p>
            <div class="social-links">
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-whatsapp fa-2x"></i></a>
            </div>
        </div>
    </footer>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="assets/js/custom.js"></script>

<script>
$(document).ready(function() {
    // Smooth scrolling
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });

    // Contact form
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        alert('Terima kasih! Mesej anda telah diterima. Kami akan menghubungi anda tidak lama lagi.');
        this.reset();
    });
});
</script>
</body>
</html>