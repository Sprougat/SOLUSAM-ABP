<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title; ?></title>
    <meta name="description" content="SOLUSAM - Sistem Manajemen Sampah cerdas untuk solusi pengelolaan limbah yang lebih bersih dan terorganisir." />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.35.0/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/datatable-bs5/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/datatable-bs5/dataTables.bootstrap5.css') ?>" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico?v=2') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logosolus.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/logosolus.png') ?>">
    
    <style>

    </style>
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?= $this->include('template/sidebar'); ?>

        <!-- Main Content -->
        <div id="content" class="flex-grow-1 d-flex flex-column">
            <!-- Header -->
            <header class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2 bg-white">
                <!-- Tombol open sidebar (mobile) -->
                <button class="btn btn-sm d-lg-none" onclick="toggleSidebar()">☰</button>
                <h2 class="h6 fw-semibold mb-0">SOLUSAM Dashboard</h2>
            </header>

            <!-- Content -->
            <main class="p-4 overflow-auto">
                <?= $this->renderSection('content'); ?>

            </main>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.js') ?>"></script>
    <script src="<?= base_url('assets/datatable-bs5/dataTables.js') ?>"></script>
    <script src="<?= base_url('assets/datatable-bs5/dataTables.bootstrap5.js') ?>"></script>

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

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("show");
        }

        $(document).ready(function() {
            $('.dataTable').DataTable();
        });
    </script>

    <?= $this->renderSection('js'); ?>
</body>

</html>
