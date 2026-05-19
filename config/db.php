<?php
/**
 * Database Configuration
 * 
 * File ini berisi konfigurasi koneksi database
 * INTENTIONALLY VULNERABLE: Tidak menggunakan prepared statements di seluruh aplikasi
 */

// Database credentials
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ivwa');

// Pastikan ekstensi mysqli tersedia agar tidak terjadi fatal error
if (!class_exists('mysqli')) {
    // Pesan jelas untuk environment development lokal
    die("PHP MySQLi extension is not available. Install/enable the mysqli (or php-mysqli) extension.\n" .
        "On Debian/Ubuntu: sudo apt install php-mysqli (or php<version>-mysql)\n" .
        "On CentOS/Fedora: sudo yum install php-mysqlnd\n" .
        "If using XAMPP/LAMPP, enable mysqli in your PHP configuration.\n");
}

// Buat koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset ke UTF-8
$conn->set_charset("utf8mb4");

?>
