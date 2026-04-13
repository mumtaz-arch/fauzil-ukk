<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireLogin();

$errors = [];
$barang_list = getAllBarang();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = intval($_POST['barang_id']);
    $jenis = cleanInput($_POST['jenis']);
    $jumlah = intval($_POST['jumlah']);
    $keterangan = cleanInput($_POST['keterangan']);
    
    // Validasi
    if (empty($barang_id)) $errors[] = 'Pilih barang terlebih dahulu';
    if ($jumlah <= 0) $errors[] = 'Jumlah harus lebih dari 0';
    
    // Untuk transaksi keluar, cek stok
    if ($jenis == 'keluar') {
        $barang = getBarangById($barang_id);
        if ($barang['stok'] < $jumlah) {
            $errors[] = 'Stok tidak mencukupi. Stok tersedia: ' . $barang['stok'];
        }
    }
    
    if (empty($errors)) {
        $data = [
            'barang_id' => $barang_id,
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan
        ];
        
        if (addTransaksi($data)) {
            $_SESSION['success'] = 'Transaksi berhasil dicatat!';
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Gagal mencatat transaksi. Silakan coba lagi.';
        }
    }
}

$page_title = 'Transaksi Baru';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaksi Baru</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle"></i> Terdapat kesalahan:</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" onsubmit="return validateTransaction()">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Transaksi *</label>
                        <select class="form-control" id="jenis" name="jenis" required 
                                onchange="updateBarangOptions()">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="masuk" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'masuk') ? 'selected' : ''; ?>>Barang Masuk</option>
                            <option value="keluar" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'keluar') ? 'selected' : ''; ?>>Barang Keluar</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang *</label>
                        <select class="form-control" id="barang_id" name="barang_id" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang_list as $barang): ?>
                            <option value="<?php echo $barang['id']; ?>" 
                                    data-stok="<?php echo $barang['stok']; ?>"
                                    <?php echo (isset($_POST['barang_id']) && $_POST['barang_id'] == $barang['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($barang['kode_barang'] . ' - ' . $barang['nama_barang'] . ' (Stok: ' . $barang['stok'] . ')'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" 
                               value="<?php echo isset($_POST['jumlah']) ? htmlspecialchars($_POST['jumlah']) : ''; ?>" 
                               min="1" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4"><?php echo isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ''; ?></textarea>
                        <div class="form-text">
                            Contoh: Pembelian dari supplier, Penjualan ke customer, Retur barang, dll.
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Informasi:</h6>
                        <ul class="mb-0">
                            <li><strong>Barang Masuk:</strong> Menambah stok barang</li>
                            <li><strong>Barang Keluar:</strong> Mengurangi stok barang</li>
                            <li>Sistem akan otomatis memperbarui stok setelah transaksi</li>
                            <li>Transaksi tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
function validateTransaction() {
    const jenis = document.getElementById('jenis').value;
    const barangSelect = document.getElementById('barang_id');
    const jumlah = document.getElementById('jumlah').value;
    
    if (!jenis) {
        alert('Pilih jenis transaksi terlebih dahulu!');
        return false;
    }
    
    if (!barangSelect.value) {
        alert('Pilih barang terlebih dahulu!');
        return false;
    }
    
    if (jumlah <= 0) {
        alert('Jumlah harus lebih dari 0!');
        return false;
    }
    
    // Cek stok untuk transaksi keluar
    if (jenis === 'keluar') {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const stokTersedia = selectedOption.getAttribute('data-stok');
        
        if (parseInt(jumlah) > parseInt(stokTersedia)) {
            alert('Stok tidak mencukupi! Stok tersedia: ' + stokTersedia);
            return false;
        }
    }
    
    return true;
}

function updateBarangOptions() {
    const jenis = document.getElementById('jenis').value;
    const barangSelect = document.getElementById('barang_id');
    
    // Reset selection
    barangSelect.selectedIndex = 0;
    
    // Jika keluar, highlight barang dengan stok rendah
    if (jenis === 'keluar') {
        for (let i = 0; i < barangSelect.options.length; i++) {
            const option = barangSelect.options[i];
            const stok = parseInt(option.getAttribute('data-stok') || 0);
            
            if (stok < 10 && i > 0) { // i > 0 untuk skip option pertama
                option.style.color = 'red';
                option.text += ' ⚠️ (Stok Rendah)';
            }
        }
    }
}

function updateVarianOptions() {
    const barangSelect = document.getElementById('barang_id');
    const varianInput = document.getElementById('varian');
    const selectedOption = barangSelect.options[barangSelect.selectedIndex];
    const varian = selectedOption.getAttribute('data-varian') || '';
    
    varianInput.value = varian;
}
       
</script>

<?php include '../includes/footer.php'; ?>
