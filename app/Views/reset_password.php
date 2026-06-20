<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SOLUSAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .input-group-text {
            background-color: transparent;
            border-left: none;
            cursor: pointer;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #198754;
        }
        .input-group .form-control {
            border-right: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: #198754;
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-sm p-4" style="width: 380px;">
    <div class="text-center mb-3">
        <div class="rounded-circle bg-success text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 28px;">S</div>
        <h4 class="fw-bold">SOLUSAM</h4>
        <p class="text-muted">Solusi Sampah - Sistem Manajemen Sampah</p>
    </div>

    <h5 class="text-center mb-3">Reset Password</h5>

<!--menjaga tampilan bersih saat ada error-->
    <?php if(session()->getFlashdata('error')): ?> 
        <div class="alert alert-danger py-2"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>

    <form action="<?= base_url('reset-password/update'); ?>" method="post">
        <input type="hidden" name="token" value="<?= $token; ?>"> <!--membuktikan bahwa user ini datang dari link email yang sah-->

        <!-- Password Baru -->
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru" required>
                <span class="input-group-text" id="togglePassword1" onclick="togglePassword('password', 'togglePassword1')">
                    <i class="bi bi-eye-slash"></i>
                </span>
            </div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required>
                <span class="input-group-text" id="togglePassword2" onclick="togglePassword('confirm_password', 'togglePassword2')">
                    <i class="bi bi-eye-slash"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Perbarui Password</button>
    </form>

    <div class="text-center mt-3">
        <a href="<?= base_url('login'); ?>" class="text-decoration-none">Kembali ke Login</a>
    </div>
</div>

<script>
function togglePassword(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const icon = document.querySelector(`#${toggleId} i`);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye-slash", "bi-eye");
    } else {
        input.type = "password";
        icon.classList.replace("bi-eye", "bi-eye-slash");
    }
}
</script>

</body>
</html>
