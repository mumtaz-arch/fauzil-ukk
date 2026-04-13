<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireLogin();

$transaksi = getAllTransaksi();
$page_title = 'Data Transaksi';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Transaksi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Transaksi Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($transaksi)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada data transaksi.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaksi as $trx): ?>
                        <tr>
                            <td>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($trx['id_transaksi'] ?? $trx['id']); ?></span>
                            </td>
                            <td><?php echo date('d-m-Y', strtotime($trx['tanggal_transaksi'])); ?></td>
                            <td><?php echo htmlspecialchars($trx['nama_barang']); ?></td>
                            <td>
                                <?php if ($trx['jenis'] == 'masuk'): ?>
                                    <span class="badge bg-success">MASUK</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">KELUAR</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $trx['jumlah']; ?></td>
                            <td><?php echo htmlspecialchars($trx['keterangan']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
