<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-success mb-4 mt-4">
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        + Form <?= $title; ?>
    </div>
    <div class="card-body">
        <form action="<?= base_url('penjualan/store') ?>" method="POST" enctype="multipart/form-data" id="form_penjualan">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nama Sampah</label>
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required>
                        <option value="" selected disabled>-- Pilih Sampah --</option>
                        <?php foreach ($sampah as $row) : ?>
                            <option value="<?= $row['id'] ?>">
                                <?= $row['nama_sampah'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!--//readonly Harganya akan muncul otomatis via AJAX setelah admin memilih jenis sampah-->
                <div class="col-md-4">
                    <label class="form-label">Harga</label>
                    <input
                        type="number"
                        name="harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="harga"
                        readonly 
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jumlah (kg)</label>
                    <input
                        type="number"
                        min="1"
                        name="jumlah_jual"
                        class="form-control"
                        id="jumlah_jual"
                        onkeyup="jumlah()"
                        required>
                    <div class="form-text" id="stok_info" style="display: none;">
                        <span class="text-muted">Stok tersedia setelah penjualan: </span>
                        <span id="stok_tersedia" class="fw-bold text-primary">0</span>
                        <span class="text-muted"> kg</span>
                    </div>
                    <div class="invalid-feedback" id="stok_error" style="display: none;">
                        Jumlah melebihi stok tersedia!
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Harga</label>
                    <input
                        type="number"
                        name="total_harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="total_harga"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Metode Bayar</label>
                    <select name="metode_bayar" class="form-select" id="metode_bayar" required>
                        <option value="" selected disabled>-- Pilih Metode --</option>
                        <option value="midtrans">Midtrans</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    Bayar
                </button>
                <a href="<?= base_url('penjualan') ?>" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
<script>
    let stokAwal = 0;
    $('#nama_sampah').change(function() {
        let sampahId = $(this).val();

        if (sampahId) {
            $.ajax({
                url: "<?= base_url('penjualan/sampah-ajax') ?>",
                type: "POST",
                data: {
                    id: sampahId
                },
                success: function(response) {
                    $('#harga').val(response.harga_jual);
                    stokAwal = parseFloat(response.stok_tersedia) || 0;
                    $('#stok_tersedia').text(stokAwal);
                    $('#stok_info').show();
                    $('#jumlah_jual').removeClass('is-invalid');
                    $('#stok_error').hide();
                    $('#jumlah_jual').val('');
                    $('#total_harga').val('');
                    $('#stok_tersedia').removeClass('text-danger').addClass('text-primary');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Terjadi kesalahan saat mengambil data harga.');
                }
            });
        } else {
            $('#harga').val('');
            $('#jumlah_jual').val('');
            $('#total_harga').val('');
            $('#stok_info').hide();
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            stokAwal = 0;
        }
    });

    function jumlah() {
        let harga = parseFloat($('#harga').val()) || 0;
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0;

        let totalHarga = harga * jumlahJual;
        $('#total_harga').val(totalHarga);
        let stokSetelahJual = stokAwal - jumlahJual;
        $('#stok_tersedia').text(stokSetelahJual);
        if (jumlahJual > stokAwal) {
            $('#jumlah_jual').addClass('is-invalid');
            $('#stok_error').show();
            $('#stok_tersedia').removeClass('text-primary').addClass('text-danger');
        } else if (jumlahJual > 0) {
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            $('#stok_tersedia').removeClass('text-primary').addClass('text-warning');
        } else {
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            $('#stok_tersedia').removeClass('text-warning text-danger').addClass('text-primary');
        }
    }
    $('#form_penjualan').on('submit', function(e) {
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0;
        let metodeBayar = $('#metode_bayar').val();

        if (jumlahJual > stokAwal) {
            e.preventDefault();
            alert('Jumlah yang dijual (' + jumlahJual + ' kg) melebihi stok tersedia (' + stokAwal + ' kg). Silakan periksa kembali.');
            return false;
        }
        if (metodeBayar === 'midtrans') {
            e.preventDefault();
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-2"></i>Memproses...').prop('disabled', true);
            let formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        let data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (data.success && data.token) {
                            submitBtn.html(originalText).prop('disabled', false);
                            if (typeof snap === 'undefined') {
                                let script = document.createElement('script');
                                <?php
                                $midtransConfig = config('Midtrans');
                                $snapUrl = $midtransConfig->isProduction
                                    ? 'https://app.midtrans.com/snap/snap.js'
                                    : 'https://app.sandbox.midtrans.com/snap/snap.js';
                                ?>
                                script.src = '<?= $snapUrl ?>';
                                script.setAttribute('data-client-key', '<?= $midtransConfig->clientKey ?? "" ?>');
                                document.body.appendChild(script);
                                script.onload = function() {
                                    snap.pay(data.token, {
                                        onSuccess: function(result) {
                                            console.log('success', result);
                                            window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                        },
                                        onPending: function(result) {
                                            console.log('pending', result);
                                            window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                        },
                                        onError: function(result) {
                                            console.log('error', result);
                                            window.location.href = '<?= base_url('penjualan/midtrans-error') ?>';
                                        },
                                        onClose: function() {
                                            console.log('customer closed the popup without finishing the payment');
                                        }
                                    });
                                };
                            } else {
                                snap.pay(data.token, {
                                    onSuccess: function(result) {
                                        console.log('success', result);
                                        window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                    },
                                    onPending: function(result) {
                                        console.log('pending', result);
                                        window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                    },
                                    onError: function(result) {
                                        console.log('error', result);
                                        window.location.href = '<?= base_url('penjualan/midtrans-error') ?>';
                                    },
                                    onClose: function() {
                                        console.log('customer closed the popup without finishing the payment');
                                    }
                                });
                            }
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat membuat transaksi Midtrans.');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Terjadi kesalahan saat memproses pembayaran.');
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data.');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    });
</script>
<?= $this->endSection(); ?>
