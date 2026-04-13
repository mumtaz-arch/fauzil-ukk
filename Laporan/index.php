<?php
date_default_timezone_set('Asia/Jakarta');
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireLogin();

// Ambil semua data transaksi
$transaksi = getAllTransaksi();
$page_title = 'Laporan Transaksi';

// Kelompokkan transaksi berdasarkan tanggal
$transaksi_per_hari = [];
foreach ($transaksi as $trx) {
    $tanggal = date('Y-m-d', strtotime($trx['tanggal_transaksi']));
    $tanggal_format = date('d F Y', strtotime($trx['tanggal_transaksi']));
    
    if (!isset($transaksi_per_hari[$tanggal])) {
        $transaksi_per_hari[$tanggal] = [
            'tanggal_format' => $tanggal_format,
            'data' => []
        ];
    }
    
    $transaksi_per_hari[$tanggal]['data'][] = $trx;
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Transaksi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>
</div>

<?php if (empty($transaksi)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada data transaksi.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <!-- Filter Tanggal -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="filterTanggal" class="form-label">Filter Tanggal:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="filterTanggal">
                        <button class="btn btn-outline-secondary" type="button" onclick="resetFilter()">Reset</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Statistik:</label>
                    <small class="text-muted">
                        Total Transaksi: <strong><?php echo count($transaksi); ?></strong>
                    </small>
                </div>
            </div>

            <!-- Laporan per Tanggal -->
            <?php foreach ($transaksi_per_hari as $tanggal => $data): ?>
                <div class="laporan-hari" data-tanggal="<?php echo $tanggal; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-4 pt-3 border-top">
                        <h5 class="mb-0">
                            <!-- <i class="bi bi-calendar3"></i>  -->
                            <?php echo $data['tanggal_format']; ?>
                        </h5>
                        <span class="badge bg-primary"><?php echo count($data['data']); ?> Transaksi</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%">No</th>
                                    <th style="width: 12%">Kode</th>
                                    <th style="width: 25%">Barang</th>
                                    <th style="width: 12%">Jenis</th>
                                    <th style="width: 12%">Jumlah</th>
                                    <th style="width: 31%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($data['data'] as $trx): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $no++; ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($trx['id_transaksi'] ?? $trx['id']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($trx['nama_barang']); ?></td>
                                        <td>
                                            <?php if ($trx['jenis'] == 'masuk'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-arrow-down"></i> Masuk
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-arrow-up"></i> Keluar
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo intval($trx['jumlah']); ?></strong>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($trx['keterangan'] ?? '-'); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<style>
    @media print {
        .navbar, .sidebar {
            display: none !important;
        }
        .ms-sm-auto {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .btn-toolbar, .input-group, .btn-outline-secondary, label[for="filterTanggal"] {
            display: none !important;
        }
        .laporan-hari {
            page-break-inside: avoid;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }

    .laporan-hari {
        transition: all 0.3s ease;
    }

    .laporan-hari.hidden {
        display: none;
    }

    .table-light th {
        font-weight: 600;
        background-color: #f8f9fa;
    }

    h5 {
        color: #495057;
        font-weight: 600;
    }
</style>

<script>
    // Filter berdasarkan tanggal
    document.getElementById('filterTanggal').addEventListener('change', function() {
        const selectedDate = this.value;
        const laporan = document.querySelectorAll('.laporan-hari');

        laporan.forEach(function(item) {
            if (selectedDate === '') {
                item.classList.remove('hidden');
            } else {
                if (item.getAttribute('data-tanggal') === selectedDate) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            }
        });
    });

    // Reset filter
    function resetFilter() {
        document.getElementById('filterTanggal').value = '';
        const laporan = document.querySelectorAll('.laporan-hari');
        laporan.forEach(function(item) {
            item.classList.remove('hidden');
        });
    }
</script>

<?php include '../includes/footer.php'; ?>
