<?= $this->extend('template/index'); ?>
<?php ?>

<?= $this->section('content'); ?>
<?php ?>

<div class="card border-success mb-4 mt-4">
    <!-- Card utama dengan border hijau + margin -->

    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        <!-- Header card dengan background hijau transparan -->
        + Form Edit Klien
        <!-- Judul form edit klien -->
    </div>

    <div class="card-body">
        <!-- Isi card -->

        <form action="<?= base_url('data-klien/store') ?>" method="POST">
            <!-- Form dikirim ke route 'data-klien/store' dengan metode POST -->

            <div class="row g-3">
                <!-- Row utama dengan gap antar elemen -->

                <input
                    type="hidden"
                    name="id"
                    class="form-control"
                    required
                    value="<?= $data['id'] ?>">
                <!-- Input hidden untuk mengirim ID klien yang diedit -->

                <div class="row g-3">
                    <!-- Row untuk input fields -->

                    <!-- Nama Lengkap -->
                    <div class="col-md-6">
                        <!-- Kolom 6 pada perangkat medium -->
                        <label class="form-label">Nama Lengkap</label>
                        <!-- Label input nama lengkap -->

                        <input 
                            type="text" 
                            name="nama_lengkap" 
                            class="form-control" 
                            required 
                            value="<?= $data['nama_lengkap'] ?>">
                        <!-- Input nama lengkap, otomatis terisi data lama -->
                    </div>

                    <!-- No Telp -->
                    <div class="col-md-6">
                        <!-- Kolom 6 -->
                        <label class="form-label">No Telp</label>
                        <!-- Label input no telp -->

                        <input 
                            type="text" 
                            name="no_telp" 
                            class="form-control" 
                            required 
                            value="<?= $data['no_telp'] ?>">
                        <!-- Input nomor telepon klien -->
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12">
                        <!-- Kolom penuh -->

                        <label class="form-label">Alamat</label>
                        <!-- Label input alamat -->

                        <input 
                            type="text" 
                            name="alamat" 
                            class="form-control" 
                            required 
                            value="<?= $data['alamat'] ?>">
                        <!-- Input alamat klien -->
                    </div>

                    <!-- Jenis Usaha -->
                    <div class="col-md-12">
                        <!-- Kolom penuh -->

                        <label class="form-label">Jenis Usaha</label>
                        <!-- Label input jenis usaha -->

                        <input 
                            type="text" 
                            name="jenis_usaha" 
                            class="form-control" 
                            required 
                            value="<?= $data['jenis_usaha'] ?>">
                        <!-- Input jenis usaha klien -->
                    </div>

                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <!-- Flexbox dengan jarak antar tombol -->

                <button type="submit" class="btn btn-success">
                    <!-- Tombol submit untuk update -->
                    Update
                </button>

                <a href="<?= base_url('data-klien') ?>" class="btn btn-secondary">
                    <!-- Tombol kembali ke halaman daftar klien -->
                    Batal
                </a>
            </div>

        </form>
        <!-- Penutup form -->

    </div>
</div>

<!-- menuput section content -->
<?= $this->endSection(); ?>
