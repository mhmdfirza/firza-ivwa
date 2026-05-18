<?php
/**
 * IVWA - Intentional Vulnerable Web Application
 * 
 * Aplikasi web yang sengaja dibuat vulnerable untuk tujuan pembelajaran
 * cybersecurity dan ethical hacking.
 * 
 * ⚠️ WARNING: Aplikasi ini HANYA untuk dijalankan di environment lokal/sandbox!
 * Jangan pernah deploy ke production atau internet-facing server!
 */

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IVWA - Intentional Vulnerable Web Application</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        
        .hero h1 {
            margin: 0;
            font-size: 2.5em;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .feature-card {
            background: #f5f5f5;
            padding: 20px;
            border-left: 4px solid #667eea;
            border-radius: 4px;
        }
        
        .feature-card h3 {
            margin-top: 0;
            color: #667eea;
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>🔓 IVWA</h1>
            <p>Intentional Vulnerable Web Application</p>
            <p>Platform pembelajaran cybersecurity dan ethical hacking</p>
        </div>
        
        <div style="text-align: center; margin-bottom: 30px;">
            <a href="login.php" class="btn btn-primary" style="font-size: 1.1em; padding: 12px 30px;">Login</a>
        </div>
        
        <h2>Tentang IVWA</h2>
        <p>
            IVWA adalah aplikasi web yang <strong>sengaja dibuat vulnerable</strong> untuk tujuan pembelajaran 
            dan pelatihan dalam bidang cybersecurity dan ethical hacking. Aplikasi ini dirancang untuk environment 
            lokal/offline saja dan <strong>TIDAK boleh digunakan di production</strong>.
        </p>
        
        <h2>Vulnerability yang Dipelajari</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <h3>🔴 SQL Injection</h3>
                <p>Belajar bagaimana input yang tidak disanitasi bisa digunakan untuk memanipulasi query SQL.</p>
                <p><strong>Lokasi:</strong> Login form</p>
            </div>
            
            <div class="feature-card">
                <h3>🟢 Stored XSS</h3>
                <p>Pelajari bagaimana JavaScript malicious bisa disimpan dan dijalankan untuk semua user.</p>
                <p><strong>Lokasi:</strong> Comment form di Dashboard</p>
            </div>
            
            <div class="feature-card">
                <h3>🔵 Reflected XSS</h3>
                <p>Pahami bagaimana parameter URL yang tidak di-escape bisa menjadi vektor XSS.</p>
                <p><strong>Lokasi:</strong> Search functionality</p>
            </div>
        </div>
        
        <h2>Fitur Aplikasi</h2>
        <ul>
            <li>🔐 Login dengan vulnerability SQL Injection</li>
            <li>💬 Dashboard dengan comment system (Stored XSS)</li>
            <li>🔍 Search functionality (Reflected XSS)</li>
            <li>📊 User management sederhana</li>
            <li>📚 Dokumentasi lengkap untuk setiap vulnerability</li>
        </ul>
        
        <h2>Cara Menggunakan</h2>
        <ol>
            <li>Import database dari <code>database/init.sql</code></li>
            <li>Pastikan MySQL/MariaDB sudah berjalan</li>
            <li>Login dengan credential di halaman login</li>
            <li>Jelajahi dan pelajari setiap vulnerability</li>
            <li>Baca kode source untuk memahami bagaimana vulnerability terjadi</li>
        </ol>
        
        <h2>Demo Credentials</h2>
        <p>Gunakan salah satu dari credential ini untuk login:</p>
        <div class="code-block">
username: admin        | password: password123
username: user1        | password: password123
username: user2        | password: password123
username: hacker       | password: password123
        </div>
        
        <h2>Contoh Payload</h2>
        
        <h3>SQL Injection - Login Form</h3>
        <p>Username:</p>
        <div class="code-block">admin' OR '1'='1</div>
        <p>Password:</p>
        <div class="code-block">anything</div>
        
        <h3>Stored XSS - Comment Form</h3>
        <div class="code-block">&lt;img src=x onerror="alert('XSS Vulnerability!')"&gt;</div>
        
        <h3>Reflected XSS - Search</h3>
        <div class="code-block">&lt;img src=x onerror="alert('Reflected XSS')"&gt;</div>
        
        <h2>⚠️ PENTING - DISCLAIMER</h2>
        <div class="alert alert-warning">
            <p>
                <strong>Aplikasi ini HANYA untuk pembelajaran dan penelitian di environment yang aman!</strong>
            </p>
            <ul>
                <li>Jangan deploy ke production server</li>
                <li>Jangan gunakan di internet-facing environment</li>
                <li>Hanya untuk penggunaan lokal/lab pribadi</li>
                <li>Jangan gunakan untuk purposes illegal</li>
                <li>Selalu meminta permission sebelum melakukan testing di aplikasi orang lain</li>
            </ul>
        </div>
        
        <h2>Struktur File</h2>
        <div class="code-block">
myOwn-ivwa/
├── index.php                  (halaman utama)
├── login.php                  (SQL Injection vulnerability)
├── dashboard.php              (Stored XSS vulnerability)
├── search.php                 (Reflected XSS vulnerability)
├── logout.php                 (logout script)
├── config/
│   └── db.php                (konfigurasi database)
├── database/
│   └── init.sql              (database schema & dummy data)
├── assets/
│   ├── style.css             (styling)
│   └── secure-examples.php   (contoh fix untuk setiap vulnerability)
└── modules/
    ├── csrf/
    ├── idor/
    ├── sqli/
    ├── upload/
    └── xss/
        </div>
        
        <h2>Resources untuk Pembelajaran</h2>
        <ul>
            <li><a href="https://owasp.org/www-community/attacks/SQL_Injection" target="_blank">OWASP - SQL Injection</a></li>
            <li><a href="https://owasp.org/www-community/attacks/xss/" target="_blank">OWASP - Cross Site Scripting (XSS)</a></li>
            <li><a href="https://portswigger.net/web-security" target="_blank">PortSwigger Web Security Academy</a></li>
            <li><a href="https://www.kali.linux/" target="_blank">Kali Linux Tools</a></li>
        </ul>
        
        <hr>
        <p style="text-align: center; color: #999;">
            IVWA v1.0 | Untuk pembelajaran cybersecurity | Use responsibly!
        </p>
    </div>
</body>
</html>