# 📝 IVWA - Changelog

Dokumentasi perubahan dan pembaruan untuk IVWA (Intentional Vulnerable Web Application).

---

## Version 1.1 - Database & Error Handling Improvements

**Release Date:** May 19, 2026

### ✨ Fitur & Improvements

#### 1. **Database Connection Improvements** (`config/db.php`)
- ✅ Perubahan DB_HOST dari `localhost` menjadi `127.0.0.1` (lebih stabil di beberapa environment)
- ✅ Tambahan MySQL extension check untuk validasi mysqli availability
- ✅ Pesan error yang jelas jika mysqli extension tidak tersedia
- ✅ Support untuk berbagai platform (Debian/Ubuntu, CentOS/Fedora, XAMPP/LAMPP)

**Alasan:** Mencegah fatal error saat mysqli extension tidak terinstall, memberikan panduan instalasi yang tepat kepada user.

#### 2. **Enhanced Error Handling** (`login.php`)
- ✅ Tambahan error reporting untuk development environment
- ✅ Better separation antara SQL error dan invalid credentials
- ✅ Proper database connection closing management
- ✅ Connection close di multiple points untuk mencegah resource leak

**Perubahan Detail:**
```php
// Sebelum:
if ($result && $result->num_rows > 0) { ... }

// Sesudah:
if ($result === FALSE) {
    // Query error (SQL error)
    $error_message = "Database error: " . $conn->error;
} elseif ($result->num_rows > 0) {
    // Login berhasil - user ditemukan dengan password yang sesuai
    ...
}
```

**Alasan:** Memudahkan debugging di environment lokal sambil tetap maintaining keamanan login logic.

#### 3. **Database Connection Testing** (`test-db.php`) - **NEW FILE**
- ✅ File baru untuk testing database connectivity
- ✅ Sederhana dan cepat untuk memverifikasi koneksi
- ✅ Helpful untuk troubleshooting setup issues

**Penggunaan:**
```bash
php test-db.php
```

**Output yang diharapkan:**
```
CONNECTED
```

#### 4. **Script Executable Permissions**
- ✅ `START_HERE.sh` - Mode perubahan dari 644 → 755
- ✅ `setup.sh` - Mode perubahan dari 644 → 755
- ✅ Memungkinkan script untuk dijalankan langsung tanpa `bash` command

**Penggunaan:**
```bash
./START_HERE.sh
./setup.sh
```

#### 5. **Minor Text Formatting Fixes** (`START_HERE.sh`)
- ✅ Text alignment fixes untuk display yang lebih rapi
- ✅ Konsistensi spacing di console output

---

## 🔧 Troubleshooting Guide (Baru)

### ❌ Error: "PHP MySQLi extension is not available"

**Solusi:**

**Windows (XAMPP):**
1. Buka `C:\xampp\php\php.ini`
2. Cari line dengan `;extension=mysqli`
3. Hapus titik koma (`;`) di depannya → `extension=mysqli`
4. Restart Apache

**Linux (Debian/Ubuntu):**
```bash
sudo apt install php-mysqli
sudo systemctl restart apache2
```

**Linux (CentOS/Fedora):**
```bash
sudo yum install php-mysqlnd
sudo systemctl restart httpd
```

**macOS (Homebrew):**
```bash
brew install php@7.4-mysql
```

---

### ❌ Error: "127.0.0.1:3306 connection refused"

**Penyebab:** MySQL service tidak running

**Solusi:**

**XAMPP/LAMPP:**
```bash
/opt/lampp/lampp start
```

**Linux (Manual Installation):**
```bash
sudo systemctl start mysql
# atau
sudo systemctl start mariadb
```

---

## 📊 Testing Perubahan

### Sebelum Update:
- ❌ Fatal error jika mysqli tidak terinstall (blank page)
- ⚠️ Login error tidak jelas membedakan SQL error vs invalid credentials
- ❌ Koneksi database tidak selalu ditutup dengan baik

### Sesudah Update:
- ✅ Clear error message dengan solusi instalasi extension
- ✅ Proper error differentiation dan helpful messages
- ✅ Reliable database connection management
- ✅ Easy way to test database connectivity

---

## 🚀 Update Instructions

### Cara Update dari Versi Sebelumnya:

1. **Backup database (optional):**
   ```bash
   mysqldump -u root ivwa > backup.sql
   ```

2. **Copy/replace files:**
   - `config/db.php` - Replace dengan versi baru
   - `login.php` - Replace dengan versi baru
   - `test-db.php` - Copy file baru
   - `START_HERE.sh` & `setup.sh` - Update executable permission

3. **Verify:**
   ```bash
   php test-db.php
   ```
   Should output: `CONNECTED`

4. **Test login:**
   - Username: `admin`
   - Password: `password123`

---

## 📚 Documentation Updates

File dokumentasi yang sudah diupdate:
- ✅ `COMPLETION_CHECKLIST.md` - Tambahan section untuk improvements
- ✅ `CHANGELOG.md` - **File baru ini**
- ⏳ `INSTALLATION.md` - Akan ditambahkan troubleshooting section

---

## 🔐 Security Notes

**Tidak ada perubahan keamanan yang mengubah learning objectives:**
- ✅ SQL Injection di `login.php` tetap vulnerable untuk pembelajaran
- ✅ Stored XSS di `dashboard.php` tetap vulnerable untuk pembelajaran
- ✅ Reflected XSS di `search.php` tetap vulnerable untuk pembelajaran

Perubahan hanya untuk:
- Error handling yang lebih baik
- Debugging yang lebih mudah
- Setup yang lebih reliable

---

## 👨‍💻 Contributors

- Initial Implementation: myOwn-ivwa Team
- Version 1.1 Updates: Database & Error Handling Improvements

---

## 📞 Support & Feedback

Jika Anda mengalami issues:
1. Baca section "Troubleshooting Guide" di atas
2. Jalankan `php test-db.php` untuk check database connectivity
3. Lihat file dokumentasi lain: `README.md`, `INSTALLATION.md`, `QUICKSTART.md`

---

**Last Updated:** May 19, 2026
