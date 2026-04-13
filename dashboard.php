<?php
require_once 'includes/session.php';
require_once 'includes/functions.php';
requireLogin();

// Statistik can be fetched via functions or direct query using getConnection()
$conn = getConnection();

// Total barang
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");
$total_barang = mysqli_fetch_assoc($result)['total'];

// Total stok
$result = mysqli_query($conn, "SELECT SUM(stok_barang) as total FROM barang");
$total_stok = mysqli_fetch_assoc($result)['total'] ?? 0;

// Barang dengan stok rendah
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE stok_barang < 10");
$stok_rendah = mysqli_fetch_assoc($result)['total'];

// Total transaksi hari ini
$today = date('Y-m-d');
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = '$today'");
$transaksi_hari_ini = mysqli_fetch_assoc($result)['total'];

// Barang stok rendah list
$barang_stok_rendah = getAllBarang();
$barang_stok_rendah = array_filter($barang_stok_rendah, function($item) {
    return $item['stok'] < 10;
});

$page_title = 'Dashboard';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<!-- Statistik Cards -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Barang</h6>
                        <h2 class="mb-0"><?php echo $total_barang; ?></h2>
                    </div>
                    <i class="bi bi-box" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Stok</h6>
                        <h2 class="mb-0"><?php echo $total_stok; ?></h2>
                    </div>
                    <i class="bi bi-stack" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Stok Rendah</h6>
                        <h2 class="mb-0"><?php echo $stok_rendah; ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Transaksi Hari Ini</h6>
                        <h2 class="mb-0"><?php echo $transaksi_hari_ini; ?></h2>
                    </div>
                    <i class="bi bi-arrow-left-right" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barang dengan Stok Rendah -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Barang dengan Stok Rendah
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($barang_stok_rendah) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Varian</th>
                                <th>Stok</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($barang_stok_rendah as $item): ?>
                            <tr>
                                 <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['kode_barang']); ?></span></td>
                                 <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                 <td><?php echo htmlspecialchars($item['varian_barang']); ?></td>
                                 <td>
                                     <span class="badge bg-warning text-dark">
                                       <?php echo $item['stok']; ?>
                                     </span>
                                 </td>
                                 <td><?php echo htmlspecialchars($item['keterangan']); ?></td>
                                 <td>
                                     <a href="barang/edit.php?id=<?php echo $item['id']; ?>" 
                                        class="btn btn-sm btn-outline-primary">
                                         <i class="bi bi-pencil"></i> Edit
                                     </a>
                                 </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle"></i> Tidak ada barang dengan stok rendah.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>