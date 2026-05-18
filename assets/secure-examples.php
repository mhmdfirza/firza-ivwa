<?php
/**
 * Secure Examples - Contoh Implementasi Yang Benar
 * 
 * File ini menunjukkan cara yang AMAN untuk menangani vulnerability
 * yang ada di aplikasi IVWA
 */

// ==========================================
// 1. SECURE LOGIN - Prepared Statements
// ==========================================

/**
 * Secure Login Implementation
 * Menggunakan Prepared Statements untuk mencegah SQL Injection
 */
function secure_login($conn, $username, $password) {
    // SECURE: Gunakan prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    
    if (!$stmt) {
        return ['success' => false, 'error' => 'Database error'];
    }
    
    // Bind parameters - ? akan diganti dengan nilai tanpa concatenation
    $stmt->bind_param("ss", $username, $password);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Database error'];
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Dalam aplikasi real, gunakan password_hash dan password_verify
        return ['success' => true, 'user' => $user];
    }
    
    return ['success' => false, 'error' => 'Invalid credentials'];
}

// Penggunaan:
/*
$result = secure_login($conn, $_POST['username'], $_POST['password']);
if ($result['success']) {
    $_SESSION['user_id'] = $result['user']['id'];
} else {
    echo $result['error'];
}
*/


// ==========================================
// 2. SECURE COMMENT STORAGE - Input Sanitization
// ==========================================

/**
 * Secure Comment Storage
 * Menggunakan prepared statement dan input sanitization
 */
function secure_post_comment($conn, $user_id, $username, $comment_text) {
    // SECURE: Validasi dan sanitasi input
    
    // 1. Strip HTML tags
    $comment_text = strip_tags($comment_text);
    
    // 2. Trim whitespace
    $comment_text = trim($comment_text);
    
    // 3. Validate length
    if (strlen($comment_text) < 1 || strlen($comment_text) > 5000) {
        return ['success' => false, 'error' => 'Comment must be 1-5000 characters'];
    }
    
    // 4. Prepared statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("INSERT INTO comments (user_id, username, comment_text) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        return ['success' => false, 'error' => 'Database error'];
    }
    
    $stmt->bind_param("iss", $user_id, $username, $comment_text);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Failed to post comment'];
    }
    
    return ['success' => true, 'message' => 'Comment posted successfully'];
}

// Penggunaan:
/*
$result = secure_post_comment($conn, $_SESSION['user_id'], $_SESSION['username'], $_POST['comment_text']);
if ($result['success']) {
    echo $result['message'];
} else {
    echo $result['error'];
}
*/


// ==========================================
// 3. SECURE COMMENT DISPLAY - Output Escaping
// ==========================================

/**
 * Secure Comment Display
 * Menggunakan htmlspecialchars untuk mencegah XSS saat display
 */
function display_comments_securely($conn) {
    $query = "SELECT * FROM comments ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    if (!$result) {
        return [];
    }
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // SECURE: Escape semua output yang berasal dari user input
        $comments[] = [
            'id' => $row['id'],
            'username' => htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'),
            'comment_text' => htmlspecialchars($row['comment_text'], ENT_QUOTES, 'UTF-8'),
            'created_at' => htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8')
        ];
    }
    
    return $comments;
}

// Penggunaan dalam template:
/*
<?php foreach (display_comments_securely($conn) as $comment): ?>
    <div class="comment">
        <strong><?php echo $comment['username']; ?></strong>
        <p><?php echo $comment['comment_text']; ?></p>
        <small><?php echo $comment['created_at']; ?></small>
    </div>
<?php endforeach; ?>
*/


// ==========================================
// 4. SECURE SEARCH - URL Parameter Escaping
// ==========================================

/**
 * Secure Search Implementation
 * Menggunakan htmlspecialchars untuk output dan prepared statement untuk SQL
 */
function secure_search($conn, $search_query) {
    if (strlen($search_query) < 1) {
        return ['success' => false, 'error' => 'Search query cannot be empty'];
    }
    
    if (strlen($search_query) > 500) {
        return ['success' => false, 'error' => 'Search query too long'];
    }
    
    // SECURE: Gunakan prepared statement
    $stmt = $conn->prepare("SELECT * FROM comments WHERE comment_text LIKE ? LIMIT 10");
    
    if (!$stmt) {
        return ['success' => false, 'error' => 'Database error'];
    }
    
    // Tambahkan wildcard untuk LIKE query
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_param);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Search failed'];
    }
    
    $result = $stmt->get_result();
    $results = [];
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    
    return ['success' => true, 'results' => $results, 'query' => $search_query];
}

// Penggunaan dalam template:
/*
<?php 
$search_result = secure_search($conn, $_GET['q']);
if ($search_result['success']): 
    // SECURE: Escape search query saat display
    $display_query = htmlspecialchars($search_result['query'], ENT_QUOTES, 'UTF-8');
    echo "Search results for: " . $display_query;
    
    foreach ($search_result['results'] as $result):
        echo htmlspecialchars($result['comment_text'], ENT_QUOTES, 'UTF-8');
    endforeach;
endif;
?>
*/


// ==========================================
// 5. ADDITIONAL SECURITY FUNCTIONS
// ==========================================

/**
 * Input Validation
 */
function validate_username($username) {
    // Username harus alphanumeric dan underscore, 3-50 karakter
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        return false;
    }
    return true;
}

/**
 * Input Validation
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Password Hashing (untuk production)
 * Menggunakan bcrypt untuk hashing password
 */
function hash_password($password) {
    // SECURE: Hash password menggunakan bcrypt
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Password Verification
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * CSRF Token Generation
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token Validation
 */
function validate_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * XSS Protection - Sanitize HTML Input
 */
function sanitize_html($input) {
    // Strip all HTML tags
    $input = strip_tags($input);
    // Escape HTML special characters
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Rate Limiting
 */
function rate_limit_check($key, $max_attempts = 5, $time_window = 300) {
    $cache_key = 'ratelimit_' . $key;
    
    // Dalam production, gunakan Redis atau Memcached
    if (!isset($_SESSION[$cache_key])) {
        $_SESSION[$cache_key] = ['attempts' => 0, 'reset_time' => time()];
    }
    
    $now = time();
    $data = $_SESSION[$cache_key];
    
    // Reset jika time window sudah expired
    if ($now - $data['reset_time'] > $time_window) {
        $_SESSION[$cache_key] = ['attempts' => 0, 'reset_time' => $now];
        return true;
    }
    
    // Check apakah sudah melebihi max attempts
    if ($data['attempts'] >= $max_attempts) {
        return false;
    }
    
    // Increment attempts
    $_SESSION[$cache_key]['attempts']++;
    return true;
}


// ==========================================
// 6. COMPLETE SECURE LOGIN EXAMPLE
// ==========================================

function secure_login_complete($conn, $username, $password) {
    // 1. VALIDATE INPUT
    if (empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Username and password required'];
    }
    
    // 2. RATE LIMITING - Prevent brute force
    $rate_limit_key = 'login_' . $username;
    if (!rate_limit_check($rate_limit_key, 5, 300)) {
        return ['success' => false, 'error' => 'Too many login attempts. Try again later.'];
    }
    
    // 3. PREPARED STATEMENT - Prevent SQL Injection
    $stmt = $conn->prepare("SELECT id, username, email, full_name, password FROM users WHERE username=?");
    
    if (!$stmt) {
        return ['success' => false, 'error' => 'Database error'];
    }
    
    $stmt->bind_param("s", $username);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Database error'];
    }
    
    $result = $stmt->get_result();
    
    // 4. VERIFY CREDENTIALS
    if ($result->num_rows === 0) {
        // Jangan reveal apakah user ada atau tidak
        return ['success' => false, 'error' => 'Invalid credentials'];
    }
    
    $user = $result->fetch_assoc();
    
    // 5. VERIFY PASSWORD (dalam production gunakan hashed password)
    if ($user['password'] !== $password) {
        return ['success' => false, 'error' => 'Invalid credentials'];
    }
    
    // 6. LOGIN SUCCESS - Setup session
    session_regenerate_id(true); // Prevent session fixation
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = htmlspecialchars($user['username']);
    $_SESSION['email'] = htmlspecialchars($user['email']);
    $_SESSION['full_name'] = htmlspecialchars($user['full_name']);
    
    return ['success' => true, 'message' => 'Login successful'];
}


// ==========================================
// HTML TEMPLATE EXAMPLE - SECURE DISPLAY
// ==========================================

?>
<!-- 
SECURE TEMPLATE EXAMPLE:

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
</head>
<body>
    <!-- 1. Display user data - ESCAPED -->
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></h1>
    
    <!-- 2. Display search results - ESCAPED -->
    <?php foreach ($search_results as $result): ?>
        <div class="result">
            <p><?php echo htmlspecialchars($result['text'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    <?php endforeach; ?>
    
    <!-- 3. Form dengan CSRF token -->
    <form method="POST" action="process.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="text" name="comment" placeholder="Your comment">
        <button type="submit">Post</button>
    </form>
    
    <!-- 4. URL parameter - ESCAPED -->
    <p>Search for: <?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
</body>
</html>
-->

<!-- 
SECURE BEST PRACTICES CHECKLIST:

✓ Gunakan prepared statements untuk semua database queries
✓ Validate semua user input
✓ Escape semua output dengan htmlspecialchars()
✓ Use CSRF tokens untuk form submissions
✓ Hash password menggunakan bcrypt (password_hash)
✓ Implement rate limiting untuk prevent brute force
✓ Use HTTPS untuk semua komunikasi
✓ Set security headers (X-Frame-Options, X-XSS-Protection, dll)
✓ Keep dependencies updated
✓ Log security events
✓ Use Web Application Firewall (WAF)
✓ Regular security testing dan penetration testing
-->
