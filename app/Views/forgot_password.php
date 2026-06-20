<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SOLUSAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-sm p-4" style="width: 380px;">
    <div class="text-center mb-3">
         <img src="<?= base_url('assets/img/logosolus.png') ?>" 
                alt="Logo Solusam"
                class="mx-auto d-block rounded-circle shadow-sm"
                style="width: 64px; height: 64px; object-fit: cover;">
        <h4 class="fw-bold">SOLUSAM</h4>
        <p class="text-muted">Solusi Sampah - Sistem Manajemen Sampah</p>
    </div>

    <h5 class="text-center mb-3">Lupa Password</h5>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger py-2"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success py-2"><?= session()->getFlashdata('success'); ?></div>
    <?php endif; ?>

    <form action="<?= base_url('forgot-password/send'); ?>" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email terdaftar" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Kirim Link Reset</button>
    </form>

    <div class="text-center mt-3">
        <a href="<?= base_url('login'); ?>" class="text-decoration-none">Kembali ke Login</a>
    </div>
</div>

</body>
</html>
