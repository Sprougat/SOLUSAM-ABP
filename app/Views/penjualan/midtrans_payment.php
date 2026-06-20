<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-primary mb-4 mt-4">
    <div class="card-header bg-primary bg-opacity-10 text-primary fw-bold">
        <i class="ti ti-credit-card me-2"></i>
        <?= $title; ?>
    </div>

    <div class="card-body text-center py-5">

        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="mb-4">
                    <i class="ti ti-shield-check text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold">Selesaikan Pembayaran Anda</h5>
                    <p class="text-muted">
                        Klik tombol di bawah untuk membuka halaman pembayaran Midtrans.
                        Anda dapat membayar menggunakan QRIS, transfer bank, kartu kredit, dan metode lainnya.
                    </p>
                </div>

                <!-- Tombol trigger Midtrans Snap -->
                <button id="pay-button" class="btn btn-primary btn-lg px-5">
                    <i class="ti ti-credit-card me-2"></i>
                    Bayar Sekarang
                </button>

                <div class="mt-4">
                    <a href="<?= base_url('penjualan'); ?>" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Kembali ke Daftar Penjualan
                    </a>
                </div>

                <div class="mt-4 text-muted small">
                    <i class="ti ti-lock me-1"></i>
                    Pembayaran diproses secara aman oleh Midtrans
                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>

<!-- Midtrans Snap JS -->
<?php
$snapUrl = (getenv('MIDTRANS_IS_PRODUCTION') === 'true')
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';
?>
<script src="<?= $snapUrl; ?>" data-client-key="<?= esc($client_key); ?>"></script>

<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memuat...';
        snap.pay('<?= esc($token); ?>', {
            onSuccess: function (result) {
                window.location.href = '<?= base_url('penjualan/midtrans-finish'); ?>?order_id=' + result.order_id;
            },
            onPending: function (result) {
                window.location.href = '<?= base_url('penjualan/midtrans-finish'); ?>?order_id=' + result.order_id;
            },
            onError: function (result) {
                window.location.href = '<?= base_url('penjualan/midtrans-error'); ?>';
            },
            onClose: function () {
                window.location.href = '<?= base_url('penjualan/midtrans-unfinish'); ?>';
            }
        });
    });
</script>

<?= $this->endSection(); ?>
