<?php
// Ensure database config is loaded for BASE_URL
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect ke login jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Silakan login terlebih dahulu!';
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit();
    }
}

// Cek role user
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Require admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = 'Akses ditolak! Anda bukan admin.';
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
    return true;
}
?>