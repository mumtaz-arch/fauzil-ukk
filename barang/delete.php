<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireLogin();

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$barang = getBarangById($id);

// Cek apakah barang ditemukan
if (!$barang) {
    header('Location: index.php');
    exit();
}

// Cek apakah barang memiliki transaksi
$conn = getConnection();

// Prepared statement untuk pengecekan
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM transaksi WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$check_data = $result->fetch_assoc();
$stmt->close();

if ($check_data['count'] > 0) {
    header('Location: index.php?message=has_transaction');
    exit();
}

// HAPUS BARANG DARI DATABASE dengan prepared statement
$stmt = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$delete_result = $stmt->execute();
$stmt->close();

if ($delete_result) {
    header('Location: index.php?message=delete_success');
} else {
    header('Location: index.php?message=delete_failed');
}
exit;
?>