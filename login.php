<?php
/**
 * Login Page
 * 
 * INTENTIONALLY VULNERABLE TO: SQL INJECTION
 * 
 * Vulnerability explanation:
 * - Login menggunakan raw SQL concatenation (tidak menggunakan prepared statements)
 * - User input langsung dimasukkan ke dalam query tanpa sanitasi
 * - Attacker bisa bypass authentication atau mengakses data yang seharusnya tidak bisa diakses
 * 
 * Contoh payload SQL Injection:
 * Username: admin' OR '1'='1
 * Password: anything
 * 
 * Query yang dijalankan menjadi:
 * SELECT * FROM users WHERE username='admin' OR '1'='1' AND password='anything'
 * Karena '1'='1' selalu TRUE, query akan mengembalikan data user pertama (biasanya admin)
 */

// Untuk debugging di environment lokal: tampilkan error agar tidak muncul HTTP 500 tanpa info
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$login_attempted = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_attempted = true;
    
    // VULNERABILITY: Raw SQL concatenation tanpa prepared statement
    require_once 'config/db.php';
    
    // Get input dari form (TIDAK DI-SANITASI!)
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // VULNERABLE: SQL Injection - direct string concatenation
    // Seharusnya menggunakan prepared statement
    $query = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "'";
    
    // Debug: tampilkan query (untuk learning purposes saja)
    // echo "<!-- Query: " . htmlspecialchars($query) . " -->";
    
    $result = $conn->query($query);
    
    // Check apakah query berhasil dijalankan
    if ($result === FALSE) {
        // Query error (SQL error)
        $error_message = "Database error: " . $conn->error;
    } elseif ($result->num_rows > 0) {
        // Login berhasil - user ditemukan dengan password yang sesuai
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        
        // Close connection sebelum redirect
        $conn->close();
        
        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Query berhasil tapi tidak ada hasil - password/username salah
        $error_message = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IVWA - Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>IVWA - Intentional Vulnerable Web App</h1>
            <h2>Login</h2>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="info-box">
                <h3>🔓 Demo Credentials (untuk testing):</h3>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> password123</p>
                <hr>
                <p><strong>Atau coba user lain:</strong> user1, user2, hacker</p>
            </div>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability Notice</h3>
                <p>Halaman ini SENGAJA vulnerable terhadap SQL Injection!</p>
                <p><strong>Coba payload:</strong><br>
                Username: <code>admin' OR '1'='1</code><br>
                Password: <code>anything</code>
                </p>
            </div>
        </div>
    </div>
    
    <?php
    // Close database connection jika masih terbuka
    if (isset($conn) && is_object($conn)) {
        $conn->close();
    }
    ?>
</body>
</html>
