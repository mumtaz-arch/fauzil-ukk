<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gudang');

// Dynamic Base URL Detection
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $dir = str_replace('\\', '/', dirname($script_name));
    
    // Handle root vs subdirectory
    $base = ($dir == '/' || $dir == '\\') ? '' : $dir;
    
    // Clear trailing slashes if in root but not in subdirectory
    // If script is in /auth/login.php, dirname is /auth
    // We want the project root.
    
    // Simplified: find the project root by absolute path
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $proj_root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $base_path = str_replace($doc_root, '', $proj_root);
    
    define('BASE_URL', $protocol . '://' . $host . $base_path);
}

// Global Connection Singleton
function getConnection() {
    static $conn = null;
    if ($conn === null) {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    return $conn;
}

// Fungsi untuk membersihkan input
function cleanInput($data) {
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>