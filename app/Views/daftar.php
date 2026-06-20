<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <meta name="description" content="SOLUSAM - Sistem Manajemen Sampah cerdas untuk solusi pengelolaan limbah yang lebih bersih dan terorganisir." />
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.35.0/dist/tabler-icons.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/datatable-bs5/bootstrap.min.css') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logosolus.png') ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico?v=2') ?>">
    <script src="<?= base_url('assets/js/sweetalert2.js') ?>"></script>
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 my-5">

    <!-- Card utama form registrasi -->
    <div class="card shadow-sm rounded-4 p-4" style="max-width: 500px; width: 100%;">
        <!-- Logo dan judul -->
        <div class="text-center mb-4">
            <img src="<?= base_url('assets/img/logosolus.png') ?>" 
                alt="Logo Solusam"
                class="mx-auto d-block rounded-circle shadow-sm"
                style="width: 64px; height: 64px; object-fit: cover;">
            <h1 class="mt-3 h4 fw-semibold text-dark">Silahkan Daftar</h1>
            <p class="text-muted small mb-0">Solusi Sampah - Sistem Manajemen Sampah</p>
        </div>

        <!-- Flash Error -->
        <?php if (session()->getFlashdata('errors-daftar')) : ?>
            <div class="alert alert-danger small" role="alert">
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors-daftar') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- SweetAlert2 Notification -->
        <script>
            <?php if (session('title')): ?>
                Swal.fire({
                    title: "<?= session('title') ?>",
                    text: '<?= session('text') ?>',
                    icon: "<?= session('icon') ?>",
                    showConfirmButton: false,
                    timer: 2000
                })
            <?php endif ?>
        </script>

        <!-- Form registrasi -->
        <form action="<?= base_url('register') ?>" method="POST">
            <!-- Username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username"
                    value="<?= old('username') ?>">
            </div>
            
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Masukkan email"
                    value="<?= old('email') ?>">
            </div>

            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan nama lengkap"
                    value="<?= old('nama_lengkap') ?>">
            </div>

            <!-- No Telp -->
            <div class="mb-3">
                <label class="form-label">No Telp</label>
                <input type="text" class="form-control" name="no_telp" placeholder="Masukkan no telepon"
                    value="<?= old('no_telp') ?>">
            </div>

            <!-- Alamat -->
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" class="form-control" name="alamat" placeholder="Masukkan alamat"
                    value="<?= old('alamat') ?>">
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="ti ti-eye-off"></i>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="konfirmasi_password" id="confirmPassword" placeholder="Ulangi password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword2">
                        <i class="ti ti-eye-off"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Daftar -->
            <button type="submit" class="btn btn-success w-100">Daftar</button>

            <!-- Tombol Daftar Google -->
            <a href="<?= base_url('auth/google'); ?>" 
               class="w-100 mt-3 d-flex align-items-center justify-content-center"
               style="background-color: #fff; border: 1px solid #ccc; border-radius: 6px; text-decoration: none; padding: 8px;">
                <img src="https://developers.google.com/identity/images/g-logo.png" 
                     alt="Google Logo" width="20" class="me-2">
                <span style="color: #555; font-weight: 500;">Daftar dengan Google</span>
            </a>
        </form>

        <!-- Sudah punya akun -->
        <div class="mt-4 text-center">
            <p class="text-muted small mb-2">Sudah Punya Akun ?</p>
            <a href="<?= base_url('/') ?>" class="btn btn-outline-success btn-sm">
                <i class="ti ti-login"></i> Login
            </a>
        </div>
    </div>

    <!-- Script toggle password -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#password');
            const togglePassword2 = document.querySelector('#togglePassword2');
            const confirmInput = document.querySelector('#confirmPassword');
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.querySelector('i').classList.toggle('ti-eye');
                this.querySelector('i').classList.toggle('ti-eye-off');
            });
            togglePassword2.addEventListener('click', function() {
                const type = confirmInput.type === 'password' ? 'text' : 'password';
                confirmInput.type = type;
                this.querySelector('i').classList.toggle('ti-eye');
                this.querySelector('i').classList.toggle('ti-eye-off');
            });
        });
    </script>
</body>

</html>
