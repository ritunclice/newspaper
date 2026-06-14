<?php
// --- DATABASE CONFIGURATION ---
// Reads from environment variables (set in Railway dashboard)
// Falls back to localhost defaults for local development
$host = getenv('MYSQLHOST')     ?: 'localhost';
$db   = getenv('MYSQLDATABASE') ?: 'newspeper';
$user = getenv('MYSQLUSER')     ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$port = getenv('MYSQLPORT')     ?: 3306;

// --- SECURITY SETTINGS ---
// Set to 'false' on a live server to hide errors from hackers
define('DEBUG_MODE', false);

if (!DEBUG_MODE) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    if (DEBUG_MODE) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("System is currently undergoing maintenance. Please try again later.");
    }
}

// Set Charset to support all languages (Bangla + English)
$conn->set_charset("utf8mb4");

// Helper function for base URL
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];

    $script_name = $_SERVER['SCRIPT_NAME'];
    $project_dir = str_replace('\\', '/', dirname($script_name));

    if ($project_dir == '/') {
        $project_dir = '';
    } else {
        if (strpos($project_dir, '/admin') !== false) {
            $project_dir = str_replace('/admin', '', $project_dir);
        }
    }

    return $protocol . "://" . $host . rtrim($project_dir, '/') . '/' . ltrim($path, '/');
}

// Helper to truncate text
function limit_words($string, $word_limit) {
    $words = explode(" ", trim(strip_tags($string)));
    if (count($words) <= $word_limit) return $string;
    return implode(" ", array_splice($words, 0, $word_limit)) . '...';
}

// Helper to fetch ads
function get_ad($type) {
    global $conn;
    $type = $conn->real_escape_string($type);
    $sql = "SELECT code FROM ads WHERE type = '$type' AND status = 1 ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['code'];
    }
    return '';
}
?>