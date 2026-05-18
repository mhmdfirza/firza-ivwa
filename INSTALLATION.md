# IVWA - Installation & Setup Guide

## 🔧 Installation Guide

### A. Prasyarat

Sebelum menginstall IVWA, pastikan sistem Anda memiliki:

1. **Web Server** (Apache atau Nginx)
   - Untuk XAMPP/LAMPP user: sudah termasuk
   
2. **PHP 7.0 atau lebih baru**
   ```bash
   php -v
   ```

3. **MySQL atau MariaDB 5.5+**
   ```bash
   mysql --version
   # atau
   mariadb --version
   ```

4. **Browser modern** (Chrome, Firefox, Safari, Edge)

---

### B. Instalasi Step-by-Step

#### **Opsi 1: Menggunakan XAMPP/LAMPP (Recommended untuk Pemula)**

**Di Windows:**

1. Download XAMPP dari https://www.apachefriends.org/
2. Install dengan default settings
3. Copy folder `myOwn-ivwa` ke `C:\xampp\htdocs\`
4. Buka XAMPP Control Panel
5. Klik tombol "Start" untuk Apache dan MySQL
6. Lanjut ke bagian "Database Setup"

**Di Linux (Ubuntu/Debian):**

```bash
# Install LAMPP
wget https://sourceforge.net/projects/lampp/files/LAMPP%201.6.8a/lampp-1.6.8a-5-x64.tar.gz
tar xvfz lampp-1.6.8a-5-x64.tar.gz -C /opt

# Start services
sudo /opt/lampp/lampp start

# Copy IVWA
sudo cp -r myOwn-ivwa /opt/lampp/htdocs/
sudo chown -R nobody:nogroup /opt/lampp/htdocs/myOwn-ivwa
```

**Di macOS:**

```bash
# Dengan Homebrew
brew install xampp

# Atau download dari https://www.apachefriends.org/
# Kemudian copy folder ke /Applications/XAMPP/htdocs/
```

---

#### **Opsi 2: Instalasi Manual Apache + PHP + MySQL**

**Di Ubuntu/Debian:**

```bash
# Update package list
sudo apt update

# Install Apache
sudo apt install apache2

# Install PHP dan modules
sudo apt install php php-mysql php-mysqli php-curl php-json

# Install MySQL/MariaDB
sudo apt install mysql-server

# Start services
sudo systemctl start apache2
sudo systemctl start mysql

# Enable to start on boot
sudo systemctl enable apache2
sudo systemctl enable mysql

# Copy IVWA ke web root
sudo cp -r myOwn-ivwa /var/www/html/
sudo chown -R www-data:www-data /var/www/html/myOwn-ivwa
```

**Di CentOS/RHEL:**

```bash
# Install Apache
sudo yum install httpd

# Install PHP
sudo yum install php php-mysql php-mysqli

# Install MariaDB
sudo yum install mariadb-server

# Start services
sudo systemctl start httpd
sudo systemctl start mariadb

# Copy IVWA
sudo cp -r myOwn-ivwa /var/www/html/
sudo chown -R apache:apache /var/www/html/myOwn-ivwa
```

---

### C. Database Setup

#### **Metode 1: Menggunakan Command Line**

```bash
# Login ke MySQL
mysql -u root -p

# Atau jika tidak ada password
mysql -u root

# Kemudian jalankan:
CREATE DATABASE ivwa;
USE ivwa;
SOURCE /path/to/database/init.sql;

# Verify
SHOW TABLES;
SELECT * FROM users;
```

Atau dalam satu command:

```bash
mysql -u root -p < database/init.sql
```

#### **Metode 2: Menggunakan phpMyAdmin**

1. Buka http://localhost/phpmyadmin/
2. Login dengan credential MySQL Anda
3. Klik "New" untuk membuat database baru
4. Beri nama database: `ivwa`
5. Klik tab "Import"
6. Pilih file `database/init.sql`
7. Klik tombol "Import"

#### **Metode 3: Menggunakan Setup Script**

```bash
cd myOwn-ivwa
chmod +x setup.sh
./setup.sh

# Kemudian ikuti prompt yang muncul
```

---

### D. Konfigurasi Database

Edit file `config/db.php` dan sesuaikan dengan konfigurasi MySQL Anda:

```php
define('DB_HOST', 'localhost');      // Alamat server MySQL
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL (kosong jika tidak ada)
define('DB_NAME', 'ivwa');           // Nama database
```

---

### E. Verifikasi Instalasi

Setelah semua langkah di atas, verify bahwa semuanya berjalan:

```bash
# Check Apache
sudo systemctl status apache2
# atau
/opt/lampp/lampp status

# Check MySQL
sudo systemctl status mysql
# atau mysql -u root

# Check PHP
php -v

# Check database
mysql -u root -e "SELECT * FROM ivwa.users;"
```

---

## 🌐 Akses Aplikasi

### Di Browser

Buka browser Anda dan akses aplikasi:

```
http://localhost/myOwn-ivwa
```

atau jika XAMPP/LAMPP menggunakan port yang berbeda:

```
http://localhost:8080/myOwn-ivwa
```

### Halaman-Halaman Utama

| URL | Deskripsi |
|-----|-----------|
| `/myOwn-ivwa/` | Halaman utama / Welcome |
| `/myOwn-ivwa/login.php` | Login page (SQL Injection vulnerability) |
| `/myOwn-ivwa/dashboard.php` | Dashboard (Stored XSS vulnerability) |
| `/myOwn-ivwa/search.php` | Search page (Reflected XSS vulnerability) |
| `/myOwn-ivwa/logout.php` | Logout script |

---

## 🔑 Demo Credentials

Gunakan credentials berikut untuk login:

```
Username: admin
Password: password123
```

Atau credentials lainnya:
```
Username: user1       Password: password123
Username: user2       Password: password123
Username: hacker      Password: password123
```

---

## 🧪 Testing Vulnerabilities

### SQL Injection Test

1. Buka halaman login
2. Masukkan di field username:
   ```
   admin' OR '1'='1
   ```
3. Password boleh apa saja
4. Klik Login
5. Anda seharusnya berhasil login sebagai admin

### Stored XSS Test

1. Login ke aplikasi
2. Di halaman Dashboard, di form "Add Your Comment"
3. Masukkan payload:
   ```html
   <img src=x onerror="alert('Stored XSS Vulnerability!')">
   ```
4. Klik "Post Comment"
5. Refresh halaman - alert seharusnya muncul

### Reflected XSS Test

1. Buka URL berikut:
   ```
   http://localhost/myOwn-ivwa/search.php?q=<img src=x onerror="alert('Reflected XSS')">
   ```
2. Alert seharusnya muncul

Atau klik link ini jika sudah login:
```
http://localhost/myOwn-ivwa/search.php?q=%3Cimg%20src=x%20onerror=%22alert('XSS')%22%3E
```

---

## ❌ Troubleshooting

### Error: "Connection refused" atau "Cannot connect to database"

**Solusi:**
1. Pastikan MySQL/MariaDB service sudah running
   ```bash
   sudo systemctl status mysql
   # atau
   /opt/lampp/lampp status
   ```
2. Check credential di `config/db.php`
3. Pastikan database `ivwa` sudah dibuat
   ```bash
   mysql -u root -e "SHOW DATABASES;"
   ```

### Error: "Database ivwa doesn't exist"

**Solusi:**
```bash
# Import database
mysql -u root < database/init.sql

# Verify
mysql -u root -e "SHOW DATABASES;" | grep ivwa
```

### Error: "Access denied for user 'root'@'localhost'"

**Solusi:**
1. Edit `config/db.php` dan sesuaikan username/password
2. Atau reset MySQL root password:
   ```bash
   # Ubuntu/Debian
   sudo mysql_secure_installation
   
   # MySQL
   mysql -u root -p
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';
   FLUSH PRIVILEGES;
   ```

### Error: "File not found" atau "404 Page Not Found"

**Solusi:**
1. Pastikan folder `myOwn-ivwa` di tempat yang benar
   - XAMPP: `C:\xampp\htdocs\myOwn-ivwa\`
   - Linux: `/var/www/html/myOwn-ivwa/`
   - LAMPP: `/opt/lampp/htdocs/myOwn-ivwa/`

2. Restart Apache
   ```bash
   sudo systemctl restart apache2
   # atau
   /opt/lampp/lampp restart
   ```

3. Check file permissions
   ```bash
   ls -la /var/www/html/myOwn-ivwa/
   # Seharusnya ada index.php, login.php, dll
   ```

### Error: "PHP syntax error" atau "Parse error"

**Solusi:**
1. Check PHP version
   ```bash
   php -v
   ```
2. Pastikan PHP >= 7.0
3. Check syntax file PHP
   ```bash
   php -l index.php
   ```

### Pages tidak menampilkan dengan benar (CSS tidak loaded)

**Solusi:**
1. Hard refresh browser: `Ctrl+Shift+R` (Ctrl+Cmd+R di Mac)
2. Check console browser (F12) untuk errors
3. Pastikan file `assets/style.css` ada
4. Check Apache configuration untuk `.htaccess`

---

## 🔒 Security Notes

### Untuk Development

```php
// config/db.php
// Jangan commit database password ke version control!
define('DB_PASS', getenv('DB_PASSWORD') ?: '');

// Atau gunakan .env file
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

### Untuk Production (JANGAN GUNAKAN IVWA!)

```php
// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000");
header("Content-Security-Policy: default-src 'self'");

// Use HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

---

## 📚 Next Steps

1. **Pelajari Code** - Baca source code dari setiap halaman
2. **Experiment** - Coba berbagai payload
3. **Understand** - Pahami bagaimana setiap vulnerability terjadi
4. **Fix** - Lihat contoh fix di `assets/secure-examples.php`
5. **Learn** - Baca referensi di README.md

---

## 🆘 Support

Jika mengalami masalah:

1. Baca README.md untuk dokumentasi lengkap
2. Check file `assets/secure-examples.php` untuk contoh kode yang benar
3. Cek terminal output untuk error messages
4. Search di Google atau StackOverflow

---

## ⚠️ Important Reminders

- ✅ Aplikasi ini hanya untuk PEMBELAJARAN
- ✅ Hanya jalankan di LOCALHOST/SANDBOX
- ❌ Jangan deploy ke PRODUCTION
- ❌ Jangan gunakan di INTERNET-FACING server
- ❌ Jangan gunakan untuk purposes ILLEGAL

---

Created for educational purposes only!
Use responsibly and ethically.
