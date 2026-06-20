<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-success mb-4 mt-4">
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        + Form <?= $title; ?>
    </div>
    <div class="card-body">
        <form action="<?= base_url('metode-bayar/store') ?>" method="POST">
            <div class="row g-3">

                <input
                    type="hidden"
                    name="id"
                    class="form-control"
                    required
                    value="<?= $data['id'] ?>">

                <div class="col-md-12">
                    <label class="form-label">Metode</label>
                    <input
                        type="text"
                        name="nama_sampah"
                        class="form-control"
                        value="<?= $data['nama'] ?>"
                        required>
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        Simpan
                    </button>
                    <a href="<?= base_url('metode-bayar') ?>" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
