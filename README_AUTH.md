# Setup Autentikasi Aplikasi Siswa

## 📋 Overview
Sistem autentikasi ini telah ditambahkan ke aplikasi pengelolaan data siswa dengan fitur:
- Login dan Register user
- Session management
- Level access (Admin/User)
- Password hashing dengan bcrypt
- Remember me functionality

## 🗂️ File yang Ditambahkan

### Database
- `database/create_user_table.sql` - Struktur tabel user
- `database/insert_admin.sql` - User admin default

### Autentikasi
- `modules/auth/form_login.php` - Form login
- `modules/auth/form_register.php` - Form register
- `modules/auth/proses_login.php` - Proses login
- `modules/auth/proses_register.php` - Proses register
- `modules/auth/logout.php` - Proses logout

### Helper
- `helper/auth_helper.php` - Fungsi-fungsi autentikasi

### Layout
- `auth_layout.php` - Layout khusus halaman login/register
- `login.php` - Halaman login
- `register.php` - Halaman register

## 🚀 Cara Setup

### 1. Import Database
```sql
-- Jalankan query ini di database Anda
SOURCE database/create_user_table.sql;
SOURCE database/insert_admin.sql;
```

### 2. Konfigurasi Database
Pastikan file `config/database.php` sudah terkonfigurasi dengan benar:
```php
$host     = "localhost";
$username = "root";
$password = "password_anda";
$database = "dbb";
```

### 3. Akses Aplikasi
- Buka browser: `http://localhost/app-siswa1`
- Akan otomatis redirect ke halaman login

### 4. Login Default
- **Username**: `admin`
- **Password**: `admin123`

Atau user biasa:
- **Username**: `user`
- **Password**: `user123`

## 🔐 Fitur Keamanan

### Password Hashing
- Menggunakan `password_hash()` dengan bcrypt
- Password tidak disimpan dalam plain text

### Session Management
- Session timeout otomatis
- Secure session configuration
- Logout yang aman

### Input Validation
- SQL Injection protection dengan `mysqli_real_escape_string()`
- XSS protection dengan `htmlspecialchars()`
- Email validation
- Password strength validation

### Access Control
- Role-based access control (Admin/User)
- Protected routes
- Session validation

## 📱 Halaman yang Diproteksi

Semua halaman aplikasi sekarang memerlukan login:
- Dashboard
- Data Siswa (CRUD)
- Data Asrama (CRUD)
- Laporan
- Tentang Aplikasi

## 🎨 UI/UX Features

### Login Form
- Modern gradient background
- Password visibility toggle
- Remember me checkbox
- Error/success notifications
- Responsive design

### Register Form
- Real-time validation
- Password confirmation
- Email validation
- Username uniqueness check
- Terms and conditions modal

### Sidebar Enhancement
- User info display
- Level badge
- Logout confirmation
- Login link untuk guest

## 🔧 Customization

### Mengubah Password Admin
```sql
UPDATE user SET password = '$2y$10$NEW_HASH_HERE' WHERE username = 'admin';
```

### Menambah Level Baru
Edit tabel user dan helper functions untuk level tambahan.

### Custom Redirect
Ubah redirect URLs di file:
- `modules/auth/proses_login.php`
- `modules/auth/proses_register.php`
- `index.php`

## 🐛 Troubleshooting

### Error: "Koneksi Database Gagal"
- Periksa konfigurasi database di `config/database.php`
- Pastikan MySQL server running
- Verifikasi database exists

### Error: "Session not working"
- Pastikan session_start() dipanggil di awal file
- Check folder permissions untuk session storage

### Error: "Redirect loop"
- Clear browser cookies dan cache
- Check session configuration

## 📝 Next Steps

1. **Implementasi Email Verification**
   - Add email verification token
   - Send verification email

2. **Password Recovery**
   - Forgot password functionality
   - Email reset link

3. **Two-Factor Authentication**
   - Add 2FA using OTP
   - Google Authenticator integration

4. **Activity Logging**
   - Log user activities
   - Admin audit trail

5. **API Authentication**
   - JWT token implementation
   - API rate limiting

## 🤝 Kontribusi

Untuk menambah fitur atau reporting bugs:
1. Fork repository
2. Create feature branch
3. Submit pull request

## 📄 License

Copyright © 2026 - Aplikasi Pengelolaan Data Siswa
