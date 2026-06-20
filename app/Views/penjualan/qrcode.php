<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-primary mb-4 mt-4">
    <div class="card-header bg-primary bg-opacity-10 text-primary fw-bold">
        <i class="ti ti-qrcode me-2"></i>
        <?= $title; ?> 
    </div>

    <div class="card-body text-center">

        <!-- Informasi Transaksi -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="card border-info">
                    <div class="card-header bg-info bg-opacity-10 text-info fw-bold">
                        Detail Transaksi
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-6">
                                <label class="form-label fw-bold">Nama Sampah:</label>
                                <p class="mb-0"><?= $transaksi['nama_sampah']; ?></p>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-bold">Jumlah:</label>
                                <p class="mb-0"><?= $transaksi['jumlah']; ?></p>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-bold">Harga per kg:</label>
                                <p class="mb-0">
                                    Rp <?= number_format($transaksi['harga_jual'], 0, ',', '.'); ?>
                                </p>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-bold">Total Harga:</label>
                                <p class="mb-0 fw-bold text-success">
                                    Rp <?= number_format($transaksi['jumlah'] * $transaksi['harga_jual'], 0, ',', '.'); ?>
                                </p>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Tanggal:</label>
                                <p class="mb-0">
                                    <?= date('d F Y', strtotime($transaksi['tanggal'])); ?>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="row mb-4">
            <div class="col-md-4 mx-auto">
                <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10 text-warning fw-bold">
                        <i class="ti ti-qrcode me-2"></i>
                        Scan QR Code
                    </div>

                    <div class="card-body">

                        <div class="qr-code-container p-3 bg-white rounded">

                            <?php if (isset($qr_code_base64)): ?>
                                
                                <img src="<?= $qr_code_base64; ?>"
                                    alt="QR Code Pembayaran"
                                    class="img-fluid"
                                    style="max-width: 200px;"
                                    onerror="this.src='<?= $qr_code_url; ?>'; this.onerror=function(){this.src='<?= base_url('penjualan/qrcode-simple/' . $transaksi['id']); ?>';};">

                            <?php else: ?>

                                <img src="<?= $qr_code_url; ?>"
                                    alt="QR Code Pembayaran"
                                    class="img-fluid"
                                    style="max-width: 300px;"
                                    onerror="this.src='<?= base_url('penjualan/qrcode-simple/' . $transaksi['id']); ?>'; 
                                    this.onerror=function(){
                                        this.style.display='none'; 
                                        this.nextElementSibling.style.display='block';
                                    };">

                                <div style="display: none;" class="alert alert-warning">
                                    <i class="ti ti-alert-triangle me-2"></i> 
                                    QR Code tidak dapat dimuat. Silakan refresh halaman atau gunakan tombol refresh di bawah.
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                Scan QR Code ini untuk melihat detail transaksi
                            </small>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        <!-- Tombol Aksi -->
        <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="ti ti-printer me-2"></i>
                Cetak QR Code
            </button>

            <a href="<?= base_url('penjualan'); ?>" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i>
                Kembali ke Daftar Penjualan
            </a>
            <!-- Tombol refresh QR Code disembunyikan -->
            <!-- <button type="button" class="btn btn-success" onclick="refreshQrCode()"> -->
            <!--     <i class="ti ti-refresh me-2"></i> -->
            <!--     Refresh QR Code -->
            <!-- </button> -->
        </div>
</div>
</div>

<!-- Modal untuk konfirmasi refresh -->
<div class="modal fade" id="refreshModal" tabindex="-1" aria-labelledby="refreshModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="refreshModalLabel">Refresh QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                Apakah Anda yakin ingin memperbarui QR Code? QR Code lama tidak akan berlaku lagi.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="confirmRefresh()">Ya, Refresh</button>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>

<script>
    function refreshQrCode() {
        $('#refreshModal').modal('show');
    }

    function confirmRefresh() {
        window.location.reload();
    }

    $(document).ready(function() {
        $('img[alt="QR Code Pembayaran"]').on('load', function() {
            console.log('QR Code berhasil dimuat');
        }).on('error', function() {
            console.error('QR Code gagal dimuat');
            $(this).after('<div class="alert alert-danger mt-2"><i class="ti ti-alert-triangle me-2"></i>QR Code tidak dapat dimuat. Silakan refresh halaman atau hubungi administrator.</div>');
        });
    });

    setInterval(function() {
        if (confirm('QR Code akan diperbarui untuk keamanan. Lanjutkan?')) {
            window.location.reload();
        }
    }, 300000);

    window.addEventListener('beforeprint', function() {
        document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', function() {
        document.body.classList.remove('printing');
    });
</script>
<style>
    @media print {

        .btn,
        .modal,
        .card-header {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .qr-code-container {
            border: 2px solid #000 !important;
        }

        body {
            background: white !important;
        }
    }

    .qr-code-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
</style>

<?= $this->endSection(); ?>
