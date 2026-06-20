<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="card border-success mb-4 mt-4">
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        + Form <?= $title; ?>
    </div>
    <div class="card-body">
        <form action="<?= base_url('data-klien/store') ?>" method="POST">
            <div class="row g-3">
                <div class="row g-3">
                    <!-- Nama Lengkap -->
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control">
                    </div>

                    <!-- No Telp -->
                    <div class="col-md-6">
                        <label class="form-label">No Telp</label>
                        <input type="text" name="no_telp" class="form-control">
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control">
                    </div>

                    <!-- Jenis Usaha -->
                    <div class="col-md-12">
                        <label class="form-label">Jenis Usaha</label>
                        <input type="text" name="jenis_usaha" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    Simpan
                </button>
                <a href="<?= base_url('data-klien') ?>" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
