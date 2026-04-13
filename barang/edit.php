<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireLogin();
$conn = getConnection();

// Ambil ID dari URL
$id = $_GET['id'] ?? 0;

// Ambil data barang dari database
$barang = getBarangById($id);

// Jika barang tidak ditemukan, redirect ke halaman index
if (!$barang) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = cleanInput($_POST['kode_barang']);
    $nama_barang = cleanInput($_POST['nama_barang']);
    $varian_barang = cleanInput($_POST['varian_barang']);
    $stok_barang = intval($_POST['stok_barang']);
    $keterangan = cleanInput($_POST['keterangan']);
    $harga_satuan = intval($_POST['harga_satuan']);
    
    // Gunakan harga_jual dari input user jika ada, jika tidak hitung otomatis
    $harga_jual = $_POST['harga_jual'] ?? calculateHargaJual($harga_satuan, 50);
    
    $query = "UPDATE barang SET 
              kode_barang = ?, nama_barang = ?, varian_barang = ?, 
              stok_barang = ?, keterangan = ?, harga_satuan = ?, harga_jual = ?
              WHERE id_barang = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssisiii', 
        $kode_barang, $nama_barang, $varian_barang, 
        $stok_barang, $keterangan, $harga_satuan, $harga_jual, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: index.php?message=update_success');
        exit();
    } else {
        $error = "Gagal mengupdate barang! Error: " . mysqli_error($conn);
    }
}

$page_title = 'Edit Barang';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Barang</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="kode_barang" class="form-label">Kode Barang</label>
                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" 
                           value="<?php echo htmlspecialchars($barang['kode_barang']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                           value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="varian_barang" class="form-label">Varian</label>
                    <input type="text" class="form-control" id="varian_barang" name="varian_barang" 
                           value="<?php echo htmlspecialchars($barang['varian_barang']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="stok_barang" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stok_barang" name="stok_barang" 
                           value="<?php echo htmlspecialchars($barang['stok']); ?>" required min="0">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?php echo htmlspecialchars($barang['keterangan']); ?></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="harga_satuan" class="form-label">Harga Satuan (Harga Beli)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" 
                               value="<?php echo htmlspecialchars($barang['harga_satuan']); ?>" 
                               required min="0" onchange="calculateHargaJual()">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="harga_jual" class="form-label">Harga Jual</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" 
                               value="<?php echo htmlspecialchars($barang['harga_jual']); ?>" 
                               required min="0">
                    </div>
                    <div class="form-text">
                        <small>Margin saat ini: 
                            <span id="margin_text">
                                <?php 
                                $margin = $barang['harga_jual'] - $barang['harga_satuan'];
                                $persen = ($barang['harga_satuan'] > 0) ? round(($margin/$barang['harga_satuan'])*100, 1) : 0;
                                echo formatRupiah($margin) . " ($persen%)";
                                ?>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="applyDefaultMarkup()">
                                <i class="bi bi-calculator"></i> Hitung 50% Markup
                            </button>
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Barang
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='index.php'">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calculateHargaJual() {
    const hargaSatuan = document.getElementById('harga_satuan').value;
    const hargaJualInput = document.getElementById('harga_jual');
    
    if (hargaSatuan) {
        // Hitung margin saat ini
        const currentJual = parseFloat(hargaJualInput.value) || 0;
        const currentSatuan = parseFloat(hargaSatuan);
        
        if (currentSatuan > 0) {
            const margin = currentJual - currentSatuan;
            const persen = Math.round((margin / currentSatuan) * 100 * 10) / 10;
            document.getElementById('margin_text').innerText = 
                `Rp ${margin.toLocaleString('id-ID')} (${persen}%)`;
        }
    }
}

function applyDefaultMarkup() {
    const hargaSatuan = document.getElementById('harga_satuan').value;
    if (hargaSatuan) {
        const markup = 50; // 50%
        const hargaJual = Math.round(parseInt(hargaSatuan) * (1 + (markup/100)));
        document.getElementById('harga_jual').value = hargaJual;
        calculateHargaJual(); // Update margin display
    }
}

// Hitung margin saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    calculateHargaJual();
});
</script>

<?php include '../includes/footer.php'; ?>
