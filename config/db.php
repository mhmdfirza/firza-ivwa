<?php
/**
 * Database Configuration
 * 
 * File ini berisi konfigurasi koneksi database
 * INTENTIONALLY VULNERABLE: Tidak menggunakan prepared statements di seluruh aplikasi
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ivwa');

// Buat koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset ke UTF-8
$conn->set_charset("utf8mb4");

?>
