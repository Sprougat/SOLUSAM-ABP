<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-success mb-4 mt-4">
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        + Form <?= $title; ?>
    </div>
    <div class="card-body">
        <form action="<?= base_url('pembelian/store') ?>" method="POST">
            <div class="row g-3">
                <input
                    type="hidden"
                    name="id"
                    class="form-control"
                    required
                    value="<?= $data['id'] ?>">

                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?= $data['tanggal'] ?>"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nama Sampah</label>
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required>
                        <option value="" selected disabled>-- Pilih Sampah --</option>
                        <?php foreach ($sampah as $row) :
                            $selected  = $row['id'] == $data['sampah_id'] ? 'selected' : '';
                        ?>
                            <option value="<?= $row['id'] ?>" <?= $selected ?>>
                                <?= $row['nama_sampah'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                    <label class="form-label">Jumlah</label>
                    <input
                    type="number"
                    min="1"
                    name="jumlah_beli"
                    class="form-control"
                    id="jumlah_beli"
                    value="<?= $data['jumlah'] ?>"
                    onkeyup="jumlah()"
                    required>
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
                    <label class="form-label">Klien</label>
                    <select name="pembeli" class="form-select" id="pembeli" required>
                        <option value="" selected disabled>-- Pilih Pembeli --</option>
                        <?php foreach ($klien as $row) :
                            $selected  = $row['id'] == $data['pembeli'] ? 'selected' : '';
                        ?>
                            <option value="<?= $row['id'] ?>" <?= $selected ?>>
                                <?= $row['nama_lengkap'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    Update
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
    let idsampah = '<?= $data['sampah_id'] ?>';
    hargaSampah(idsampah);

    $('#nama_sampah').change(function() {
        let id = $(this).val();

        hargaSampah(id)
    });

    function hargaSampah(id) {
        $.ajax({
            url: "<?= base_url('pembelian/sampah-ajax') ?>",
            type: "POST",
            data: {
                id
            },
            success: function(response) {
                $('#harga').val(response.harga_beli);
                jumlah()

            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Terjadi kesalahan saat mengambil data harga.');
            }
        });
    }

    function jumlah() {
        let harga = parseFloat($('#harga').val()) || 0;
        let jumlahJual = parseFloat($('#jumlah_beli').val()) || 0;

        let totalHarga = harga * jumlahJual;

        $('#total_harga').val(totalHarga);
    }
</script>
<?= $this->endSection(); ?>
