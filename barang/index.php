<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireLogin();

$barang = getAllBarang();

$page_title = 'Data Barang';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Barang</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($barang)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada data barang. 
                <a href="create.php" class="alert-link">Tambah barang pertama</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Varian</th>
                            <th>Stok</th>
                            <th>Harga Satuan</th>
                            <th>Harga Jual</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($barang as $item): 
                            // Hitung margin/keuntungan
                            $margin = $item['harga_jual'] - $item['harga_satuan'];
                            $persenMargin = ($item['harga_satuan'] > 0) ? 
                                round(($margin / $item['harga_satuan']) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($item['kode_barang']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($item['varian_barang']); ?></td>
                            <td>
                                <?php if ($item['stok'] > 10): ?>
                                    <span class="badge bg-success text-dark"><?php echo $item['stok']; ?></span>
                                <?php elseif ($item['stok'] < 10): ?>
                                    <span class="badge bg-warning text-dark"><?php echo $item['stok']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatRupiah($item['harga_satuan']); ?></td>
                            <td>
                                <strong><?php echo formatRupiah($item['harga_jual']); ?></strong>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger delete-btn"
                                       data-id="<?php echo $item['id']; ?>"
                                       data-name="<?php echo htmlspecialchars($item['nama_barang']); ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tangani semua tombol hapus
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah navigasi langsung
            
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const deleteUrl = this.getAttribute('href');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus barang:<br><strong>${name}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke halaman delete jika dikonfirmasi
                    window.location.href = deleteUrl;
                }
            });
        });
    });
    
    // Tangani pesan dari URL parameter untuk SweetAlert
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('message')) {
        const message = urlParams.get('message');
        let alertConfig = {};
        
        switch(message) {
            case 'add_success':
                alertConfig = {
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Barang berhasil ditambahkan!'
                };
                break;
            case 'update_success':
                alertConfig = {
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Barang berhasil diperbarui!'
                };
                break;
            case 'delete_success':
                alertConfig = {
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Barang berhasil dihapus!'
                };
                break;
            case 'has_transaction':
                alertConfig = {
                    icon: 'warning',
                    title: 'Ditolak!',
                    text: 'Barang tidak bisa dihapus karena sudah memiliki riwayat transaksi!'
                };
                break;
            case 'delete_failed':
                alertConfig = {
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menghapus barang dari database!'
                };
                break;
        }
        
        if (Object.keys(alertConfig).length > 0) {
            Swal.fire({
                ...alertConfig,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                width: '400px'
            });
            
            // Hapus parameter dari URL tanpa refresh
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>