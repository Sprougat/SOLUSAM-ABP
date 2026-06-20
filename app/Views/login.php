<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <meta name="description" content="SOLUSAM - Sistem Manajemen Sampah cerdas untuk solusi pengelolaan limbah yang lebih bersih dan terorganisir." />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.35.0/dist/tabler-icons.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/datatable-bs5/bootstrap.min.css') ?>">
    <script src="<?= base_url('assets/js/sweetalert2.js') ?>"></script>

</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-sm p-4" style="max-width: 420px; width: 100%;">
        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="<?= base_url('assets/img/logosolus.png') ?>" 
                alt="Logo Solusam"
                class="mx-auto d-block rounded-circle shadow-sm"
                style="width: 64px; height: 64px; object-fit: cover;">
            <h1 class="mt-3 h4 fw-semibold text-dark">SOLUSAM</h1>
            <p class="text-muted small mb-0">Solusi Sampah - Sistem Manajemen Sampah</p>
        </div>
        
        <!-- Alert Success -->
        <?php if (session()->getFlashdata('login')) : ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <?= session()->getFlashdata('login'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Alert Error -->
        <?php if (session()->getFlashdata('errors-login')) : ?>
            <div class="alert alert-danger small" role="alert">
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors-login') as $error) : ?>
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

        <!-- Form -->
        <form action="<?= base_url('login') ?>" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label small">Username/Email</label>
                <input type="text" class="form-control form-control-sm" name="username" placeholder="Masukkan username atau email" required>
            </div>

            <div class="mb-3">
                <label class="form-label small" for="floatingInput1">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control form-control-sm" id="floatingInput1" name="password" placeholder="Masukkan password" required>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="togglePassword">
                        <i class="ti ti-eye-off"></i>
                    </button>
                </div>
            </div>
            <p class="text-end mb-3">
                <a href="<?= base_url('forgot-password'); ?>" class="small text-muted text-decoration-none">Lupa Password?</a>
            </p>

            <button type="submit" class="btn btn-success w-100 fw-semibold">
                Login
            </button>
        </form>

        <!-- Tombol Login Google -->
        <a href="<?= base_url('auth/google'); ?>" 
        class="w-100 mt-3 d-flex align-items-center justify-content-center"
        style="background-color: #fff; border: 1px solid #ccc; border-radius: 6px; text-decoration: none; padding: 8px;">
        
        <!-- Logo Google -->
         <img src="https://developers.google.com/identity/images/g-logo.png" 
         alt="Google Logo" width="20" class="me-2">
         
         <!-- Teks -->
          <span style="color: #555; font-weight: 500;">Login dengan Google</span>
        </a>

        <!-- Daftar -->
        <div class="text-center mt-4">
            <p class="text-muted small mb-2">Belum punya akun?</p>
            <a href="<?= base_url('daftar') ?>" class="btn btn-outline-success btn-sm">
                <i class="ti ti-user-plus"></i> Daftar
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#floatingInput1');
            const eyeIcon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    eyeIcon.classList.remove('ti-eye-off');
                    eyeIcon.classList.add('ti-eye');
                } else {
                    eyeIcon.classList.remove('ti-eye');
                    eyeIcon.classList.add('ti-eye-off');
                }
            });
        });
    </script>
</body>

</html>
