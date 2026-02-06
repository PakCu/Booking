# SPD Production - Sistem Tempahan Fotografi

Sistem tempahan fotografi profesional dengan panel admin lengkap.

## 📋 Keperluan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache/Nginx Web Server
- PHP Extensions: PDO, PDO_MySQL

## 🚀 Cara Pemasangan

### 1. Clone/Download Projek
```bash
git clone https://github.com/yourusername/spd-production.git
cd spd-production
```

### 2. Setup Database
```bash
# Import database
mysql -u root -p < database.sql

# Atau melalui phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Create database: spd_production
# 3. Import file database.sql
```

### 3. Konfigurasi Database

Edit fail `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'spd_production';
private $username = 'root';
private $password = 'your_password';
```

### 4. Set Permissions
```bash
chmod -R 755 .
chmod -R 777 uploads/
```

### 5. Akses Sistem

- **Website**: http://localhost/spd-production/
- **Admin Panel**: http://localhost/spd-production/admin/

**Login Admin:**
- Username: `admin`
- Password: `admin123`

## 📁 Struktur Fail
```
spd-production/
├── config/
│   ├── database.php       # Konfigurasi database
│   └── config.php          # Konfigurasi sistem
├── includes/
│   ├── header.php          # Header template
│   ├── footer.php          # Footer template
│   └── functions.php       # Helper functions
├── admin/
│   ├── login.php           # Login admin
│   ├── index.php           # Dashboard
│   ├── bookings.php        # Pengurusan tempahan
│   ├── themes.php          # Pengurusan tema
│   ├── addons.php          # Pengurusan add-ons
│   ├── coupons.php         # Pengurusan kupon
│   ├── reports.php         # Laporan
│   ├── settings.php        # Tetapan sistem
│   └── includes/           # Admin includes
├── assets/
│   ├── css/
│   │   └── custom.css      # Custom styling
│   └── js/
│       └── custom.js       # Custom JavaScript
├── ajax/
│   └── get-time-slots.php  # AJAX endpoints
├── index.php               # Landing page
├── select-theme.php        # Pilih tema
├── select-datetime.php     # Pilih tarikh & masa
├── pax-addons.php          # Pax & tambahan
├── customer-info.php       # Maklumat pelanggan
├── terms.php               # Terma & syarat
├── summary.php             # Ringkasan tempahan
├── payment.php             # Pembayaran
├── booking-success.php     # Kejayaan tempahan
└── database.sql            # Database schema
```

## 🎯 Ciri-ciri Utama

### Customer Features:
- ✅ Responsive landing page
- ✅ Pilihan tema fotografi
- ✅ Calendar booking dengan time slots
- ✅ Add-ons selection
- ✅ Real-time slot countdown
- ✅ Coupon system
- ✅ Payment gateway integration ready
- ✅ Email notifications
- ✅ Booking confirmation

### Admin Features:
- ✅ Dashboard dengan statistik
- ✅ Pengurusan tempahan lengkap
- ✅ Pengurusan tema & harga
- ✅ Pengurusan add-ons
- ✅ Sistem kupon diskaun
- ✅ Laporan pendapatan
- ✅ Export data
- ✅ Tetapan sistem
- ✅ Multi-status booking

## 🔧 Konfigurasi

### Deposit Amount
Edit dalam `config/config.php`:
```php
define('DEPOSIT_AMOUNT', 50);
```

### Slot Lock Duration
```php
define('SLOT_LOCK_DURATION', 10); // minutes
```

### Time Slots
Edit dalam `includes/functions.php` - function `generateTimeSlots()`

## 💳 Payment Gateway Integration

Sistem ini ready untuk integration dengan:
- Billplz
- Stripe
- iPay88
- SenangPay

Edit `payment.php` untuk integrate payment gateway pilihan anda.

## 📧 Email Configuration

Edit tetapan email dalam admin panel atau file `config/config.php`:
```php
// SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
```

## 🔐 Security

### Production Checklist:
- [ ] Tukar password admin default
- [ ] Implement proper password hashing
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Disable error display
- [ ] Enable CSRF protection
- [ ] Sanitize all inputs
- [ ] Use prepared statements (already implemented)

## 🐛 Troubleshooting

### Database Connection Error
```
Semak credentials dalam config/database.php
Pastikan MySQL service running
```

### Time Slots Tidak Muncul
```
Semak fail ajax/get-time-slots.php
Check browser console untuk errors
Pastikan jQuery loaded
```

### Admin Cannot Login
```
Default credentials:
Username: admin
Password: admin123
```

## 📝 Changelog

### Version 1.0.0 (2024-12-01)
- Initial release
- Complete booking system
- Admin panel
- Payment integration ready

## 👨‍💻 Developer

Developed by SPD Production Team

## 📄 License

Copyright © 2024 SPD Production. All rights reserved.

## 🤝 Support

Untuk support dan pertanyaan:
- Email: support@spdproduction.my
- WhatsApp: +60 12-345 6789

## 🎓 Tutorial Penggunaan

### Untuk Pelanggan:
1. Buka website
2. Klik "Tempah Sekarang"
3. Pilih tema yang dikehendaki
4. Pilih tarikh dan masa
5. Masukkan bilangan pax dan add-ons
6. Isi maklumat peribadi
7. Baca dan setuju T&C
8. Semak ringkasan
9. Buat pembayaran

### Untuk Admin:
1. Login ke admin panel
2. Dashboard - Lihat statistik
3. Tempahan - Urus semua booking
4. Tema - Tambah/edit tema
5. Add-ons - Urus add-ons
6. Kupon - Buat kupon diskaun
7. Laporan - Lihat laporan
8. Tetapan - Konfigurasi sistem