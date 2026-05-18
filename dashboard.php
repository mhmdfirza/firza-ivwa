<?php
/**
 * Dashboard Page
 * 
 * INTENTIONALLY VULNERABLE TO: STORED XSS (Persistent XSS)
 * 
 * Vulnerability explanation:
 * - Comments disimpan langsung ke database tanpa sanitasi
 * - Saat ditampilkan, HTML/JavaScript tidak di-escape
 * - Attacker bisa menginject JavaScript yang akan dijalankan untuk semua user yang melihat comment tersebut
 * 
 * Contoh payload Stored XSS:
 * Comment: <img src=x onerror="alert('XSS Vulnerability!')">
 * atau
 * Comment: <script>alert('Stored XSS Attack')</script>
 * 
 * Payload akan disimpan dan dijalankan setiap kali halaman dimuat
 */

session_start();

// Check apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$success_message = '';
$error_message = '';

// Handle form submission untuk menambah comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_text'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $comment_text = $_POST['comment_text'];
    
    // VULNERABILITY: Tidak ada sanitasi, langsung disimpan ke database
    $query = "INSERT INTO comments (user_id, username, comment_text) VALUES ('" . 
             $user_id . "', '" . $username . "', '" . addslashes($comment_text) . "')";
    
    // Catatan: addslashes() di sini hanya mencegah SQL error, bukan mencegah XSS!
    // XSS tetap akan terjadi saat ditampilkan
    
    if ($conn->query($query)) {
        $success_message = "Comment berhasil ditambahkan!";
    } else {
        $error_message = "Error menambah comment: " . $conn->error;
    }
}

// Fetch comments dari database
$comments_query = "SELECT * FROM comments ORDER BY created_at DESC";
$comments_result = $conn->query($comments_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IVWA - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">IVWA Dashboard</div>
            <div class="navbar-user">
                <span>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </nav>
        
        <div class="dashboard-content">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Form untuk menambah comment - VULNERABLE TO STORED XSS -->
            <div class="comment-form">
                <h2>Add Your Comment</h2>
                <form method="POST" action="dashboard.php">
                    <div class="form-group">
                        <label for="comment_text">Comment:</label>
                        <textarea id="comment_text" name="comment_text" rows="4" 
                                  placeholder="Write your comment here..." required></textarea>
                        <p style="font-size: 0.8em; color: #666;">
                            💡 Tip: Coba input HTML atau JavaScript di sini!
                        </p>
                    </div>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
            </div>
            
            <!-- Tampilkan semua comments - VULNERABLE: XSS karena tidak di-escape -->
            <div class="comments-section">
                <h2>Comments (<?php echo $comments_result->num_rows; ?>)</h2>
                
                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                            <span class="comment-date">
                                <?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?>
                            </span>
                        </div>
                        
                        <!-- VULNERABILITY: Comment text ditampilkan tanpa escaping! -->
                        <!-- Ini memungkinkan Stored XSS attack -->
                        <div class="comment-text">
                            <?php echo $comment['comment_text']; ?>
                        </div>
                        <!-- END VULNERABILITY -->
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="vulnerability-info">
                <h3>⚠️ Stored XSS Vulnerability Info</h3>
                <p>Halaman ini SENGAJA vulnerable terhadap Stored XSS!</p>
                <p><strong>Coba payload di comment form:</strong></p>
                <code>&lt;img src=x onerror="alert('XSS Vulnerability!')"&gt;</code>
                <br><br>
                <code>&lt;script&gt;alert('Stored XSS')&lt;/script&gt;</code>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
