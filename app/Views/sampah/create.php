<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-success mb-4 mt-4">
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        + Form Penambahan Data Sampah
    </div>
    <div class="card-body">
        <form action="<?= base_url('sampah/store') ?>" method="POST">
            <div class="row g-3">
                <!-- Nama Sampah -->
                <div class="col-md-3">
                    <label class="form-label">Nama Sampah</label>
                    <input
                        type="text"
                        name="nama_sampah"
                        class="form-control"
                        required>
                </div>

                <!-- Harga Beli -->
                <div class="col-md-3">
                    <label class="form-label">Harga Beli (Rp)</label>
                    <input
                        type="number"
                        name="harga_beli"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        required>
                </div>

                <!-- Harga Jual -->
                <div class="col-md-3">
                    <label class="form-label">Harga Jual (Rp)</label>
                    <input
                        type="number"
                        name="harga_jual"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        required>
                </div>

                <!-- Satuan -->
                <div class="col-md-3">
                    <label class="form-label">Satuan (kg)</label>
                    <input
                        type="number"
                        name="satuan"
                        class="form-control"
                        min="1"
                        required>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    Simpan
                </button>
                <a href="<?= base_url('sampah') ?>" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
