<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>
<div class="d-flex flex-column flex-grow-1 overflow-hidden">

    <!-- Content -->
    <main class="flex-grow-1 p-4 overflow-auto d-flex align-items-center justify-content-center">
        <div class="card shadow border-success border-opacity-50" style="max-width: 450px; width:100%;">
            <div class="card-body">
                <h2 class="h4 fw-bold mb-2 text-dark">Ganti Password</h2>
                <p class="text-muted mb-4">Ubah password akun Anda untuk keamanan yang lebih baik</p>

                <form action="<?= base_url('reset/update') ?>" method="POST">
                    <!-- Password Saat Ini -->
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Masukkan password saat ini" name="current_password">
                            <!-- <button class="btn btn-outline-secondary" type="button"></button> -->
                        </div>
                    </div>

                    <!-- Password Baru -->
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Masukkan password baru" name="new_password">
                            <!-- <button class="btn btn-outline-secondary" type="button"></button> -->
                        </div>
                        <div class="form-text">Password minimal 6 karakter</div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Ulangi password baru" name="confirm_new_password">
                            <!-- <button class="btn btn-outline-secondary" type="button"></button> -->
                        </div>
                    </div>

                    <!-- Tombol -->
                    <button type="submit" class="btn btn-success w-100">
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
<?= $this->endSection(); ?>
