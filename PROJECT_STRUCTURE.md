# 📦 IVWA - Project Structure & File Overview

## Project Complete! ✅

Selamat! IVWA (Intentional Vulnerable Web Application) sudah siap untuk digunakan sebagai platform pembelajaran cybersecurity.

---

## 📁 Struktur Lengkap

```
myOwn-ivwa/
│
├── 📄 index.php                      ← Halaman utama / Welcome Page
├── 📄 login.php                      ← Login (SQL Injection ⚠️)
├── 📄 dashboard.php                  ← Dashboard (Stored XSS ⚠️)
├── 📄 search.php                     ← Search (Reflected XSS ⚠️)
├── 📄 logout.php                     ← Logout script
│
├── 📚 README.md                      ← Dokumentasi lengkap (BACA INI!)
├── 📚 INSTALLATION.md                ← Panduan instalasi step-by-step
├── 📚 QUICKSTART.md                  ← Quick start 5-minute setup
├── 📚 PAYLOADS.md                    ← Kumpulan payload & testing guide
│
├── 📁 config/
│   └── 📄 db.php                     ← Database configuration
│
├── 📁 database/
│   └── 📄 init.sql                   ← Database schema & dummy data
│
├── 📁 assets/
│   ├── 📄 style.css                  ← CSS styling
│   └── 📄 secure-examples.php        ← Contoh secure implementation
│
├── 📁 modules/
│   ├── csrf/                         ← Placeholder untuk CSRF (future)
│   ├── idor/                         ← Placeholder untuk IDOR (future)
│   ├── sqli/                         ← Placeholder untuk SQLi (future)
│   ├── upload/                       ← Placeholder untuk File Upload (future)
│   └── xss/                          ← Placeholder untuk XSS (future)
│
├── 📁 uploads/                       ← Directory untuk uploaded files
│
└── 📄 setup.sh                       ← Automated setup script
```

---

## 📋 File Descriptions

### Main Files

| File | Purpose | Vulnerability |
|------|---------|---|
| `index.php` | Home page & documentation | None (educational) |
| `login.php` | User authentication | **SQL Injection** |
| `dashboard.php` | User dashboard & comments | **Stored XSS** |
| `search.php` | Comment search | **Reflected XSS** |
| `logout.php` | Logout functionality | None |

### Configuration Files

| File | Purpose |
|------|---------|
| `config/db.php` | Database connection settings |
| `database/init.sql` | Database schema & sample data |

### Documentation

| File | Content |
|------|---------|
| `README.md` | Lengkap documentation (Bahasa Indonesia) |
| `INSTALLATION.md` | Step-by-step installation guide |
| `QUICKSTART.md` | 5-minute quick start |
| `PAYLOADS.md` | Payload examples & testing techniques |

### Assets

| File | Purpose |
|------|---------|
| `assets/style.css` | Complete CSS styling |
| `assets/secure-examples.php` | Secure implementations untuk setiap vulnerability |

---

## 🔓 Vulnerabilities Overview

### 1️⃣ SQL Injection (SQLi)
- **File:** `login.php`
- **Issue:** Raw SQL concatenation tanpa prepared statements
- **Severity:** 🔴 CRITICAL
- **Payload Example:** `admin' OR '1'='1`
- **Secure Fix:** Lihat `assets/secure-examples.php`

### 2️⃣ Stored XSS
- **File:** `dashboard.php` (comment form)
- **Issue:** Input tidak di-sanitasi, output tidak di-escape
- **Severity:** 🔴 CRITICAL
- **Payload Example:** `<img src=x onerror="alert('XSS')">`
- **Secure Fix:** Gunakan `htmlspecialchars()` saat output

### 3️⃣ Reflected XSS
- **File:** `search.php` (search parameter)
- **Issue:** URL parameter ditampilkan tanpa escaping
- **Severity:** 🟠 HIGH
- **Payload Example:** `?q=<img src=x onerror="alert('XSS')">`
- **Secure Fix:** Escape semua user input saat display

---

## 🎯 Quick Links

### Untuk Pemula
1. Start dengan [QUICKSTART.md](QUICKSTART.md) - 5 menit setup
2. Baca [README.md](README.md) - Dokumentasi lengkap
3. Coba 3 basic payloads di setiap halaman

### Untuk Intermediate
1. Baca source code setiap file PHP
2. Bandingkan dengan secure version di `assets/secure-examples.php`
3. Coba payloads lebih advanced dari [PAYLOADS.md](PAYLOADS.md)

### Untuk Advanced
1. Gunakan Burp Suite untuk deep testing
2. Modify kode untuk create custom vulnerabilities
3. Develop secure version dan compare dengan vulnerable version

---

## 🚀 Getting Started

### Minimal Setup (2 menit)
```bash
# 1. Import database
mysql -u root < database/init.sql

# 2. Start web server
/opt/lampp/lampp start

# 3. Open browser
# http://localhost/myOwn-ivwa

# 4. Login
# Username: admin
# Password: password123
```

### Full Setup (5 menit)
```bash
# Jalankan script otomatis
chmod +x setup.sh
./setup.sh
```

---

## 📊 Project Statistics

- **Total Files:** 15+
- **PHP Files:** 6
- **SQL Files:** 1
- **CSS Files:** 1
- **Documentation:** 4 files
- **Shell Scripts:** 1
- **Lines of Code:** ~2000+

### Database
- **Tables:** 2 (users, comments)
- **Sample Users:** 4
- **Sample Comments:** 3

---

## ✨ Features

### ✅ Implemented
- [x] SQL Injection vulnerability
- [x] Stored XSS vulnerability
- [x] Reflected XSS vulnerability
- [x] Basic user authentication
- [x] Comment system
- [x] Search functionality
- [x] Database schema
- [x] CSS styling
- [x] Comprehensive documentation
- [x] Secure examples
- [x] Payload examples
- [x] Setup automation

### 🔜 Future (untuk expansion)
- [ ] CSRF vulnerability
- [ ] IDOR vulnerability
- [ ] File upload vulnerability
- [ ] Authentication bypass
- [ ] API security examples
- [ ] More advanced payloads
- [ ] Multi-language support

---

## 🔒 Security Notes

### Remember:
- ✅ Aplikasi ini **SENGAJA** vulnerable
- ✅ Hanya untuk **PEMBELAJARAN** lokal
- ✅ Baca semua **DISCLAIMER** di README.md
- ❌ Jangan deploy ke **PRODUCTION**
- ❌ Jangan gunakan untuk **ILLEGAL** purposes

---

## 📞 File Dependencies

```
index.php
├── assets/style.css
└── (session management)

login.php
├── config/db.php (database connection)
├── assets/style.css
└── (session management)

dashboard.php
├── config/db.php
├── assets/style.css
└── (session check)

search.php
├── config/db.php
├── assets/style.css
└── (session check)

config/db.php
└── Database credentials

database/init.sql
└── (no dependencies - standalone SQL)

assets/secure-examples.php
└── (reference examples only)
```

---

## 🧪 Recommended Testing Flow

### Day 1: Environment Setup
- [ ] Install prerequisites
- [ ] Run setup script
- [ ] Verify database
- [ ] Test basic access

### Day 2: SQL Injection Learning
- [ ] Read `README.md` SQL Injection section
- [ ] Study `login.php` code
- [ ] Try basic payloads
- [ ] Review secure fix

### Day 3: Stored XSS Learning
- [ ] Read `README.md` Stored XSS section
- [ ] Study `dashboard.php` code
- [ ] Try different payloads
- [ ] Understand impact

### Day 4: Reflected XSS Learning
- [ ] Read `README.md` Reflected XSS section
- [ ] Study `search.php` code
- [ ] Create test URLs
- [ ] Document findings

### Day 5: Advanced Testing
- [ ] Install security tools (Burp, ZAP)
- [ ] Perform automated scanning
- [ ] Combine multiple vulnerabilities
- [ ] Create comprehensive report

---

## 🎓 Learning Objectives

Setelah menggunakan IVWA, Anda akan memahami:

✅ Bagaimana SQL Injection terjadi dan dampaknya
✅ Perbedaan Stored XSS dan Reflected XSS
✅ Mengapa input validation penting
✅ Bagaimana output encoding mencegah XSS
✅ Pentingnya prepared statements
✅ Security best practices dalam web development
✅ Cara testing web applications
✅ Etika dalam cybersecurity

---

## 📚 Learning Resources

### OWASP
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- SQL Injection: https://owasp.org/www-community/attacks/SQL_Injection
- XSS: https://owasp.org/www-community/attacks/xss/

### Platforms
- PortSwigger Academy: https://portswigger.net/web-security
- HackTheBox: https://www.hackthebox.com/
- TryHackMe: https://tryhackme.com/

### Tools
- Burp Suite: https://portswigger.net/burp
- OWASP ZAP: https://owasp.org/www-project-zap/
- SQLMap: https://sqlmap.org/

---

## 🏁 Conclusion

IVWA adalah platform pembelajaran cybersecurity yang complete dan easy-to-understand. Dengan dokumentasi lengkap dan contoh payload, Anda bisa explore web application vulnerabilities dalam environment yang aman.

**Happy Learning! 🎓**

---

## 📝 Version Info

- **Version:** 1.0
- **Created:** 2024
- **PHP Version Required:** 7.0+
- **Database:** MySQL 5.5+ / MariaDB
- **License:** Educational Use Only

---

## ✍️ Credits

Dibuat untuk tujuan edukasi cybersecurity dan ethical hacking.

**Use responsibly and ethically!**
