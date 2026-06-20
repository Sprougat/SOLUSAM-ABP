<!DOCTYPE html> <!-- Deklarasi dokumen HTML -->
<html lang="id"> <!-- Bahasa dokumen Indonesia -->

<head>
    <meta charset="UTF-8"> <!-- Set karakter encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif pada semua device -->
    <title><?= $title; ?></title> <!-- Judul halaman dinamis dari controller -->

    <!-- Import Bootstrap 5 untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Import icon Tabler (SVG sprite) -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@1.0.0/icons-sprite.svg" rel="stylesheet">

    <style>
        body { /* Style umum body halaman error */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Background gradient */
            min-height: 100vh; /* Tinggi minimal layar penuh */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font modern */
        }

        .error-container { /* Container utama untuk center vertikal & horizontal */
            min-height: 100vh; /* Full layar */
            display: flex; /* Flexbox */
            align-items: center; /* Posisi tengah vertikal */
            justify-content: center; /* Posisi tengah horizontal */
        }

        .error-card { /* Card blur kaca transparan */
            border: none; /* Hilangkan border */
            border-radius: 20px; /* Rounded */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); /* Shadow lembut */
            background: rgba(255, 255, 255, 0.95); /* Putih agak transparan */
            backdrop-filter: blur(10px); /* Efek kaca */
        }

        .error-icon { /* Style icon error */
            font-size: 5rem; /* Ukuran besar */
            color: #dc3545; /* Warna merah (Bootstrap danger) */
        }
    </style>
</head>

<body>
    <div class="error-container"> <!-- Wrapper utama agar rapi di tengah -->
        <div class="container"> <!-- Bootstrap container -->

            <div class="row justify-content-center"> <!-- Baris dengan konten di tengah -->
                <div class="col-md-6"> <!-- Ukuran card di medium ke atas -->

                    <div class="error-card p-5 text-center"> <!-- Card transparan dengan padding -->

                        <div class="error-icon mb-4"> <!-- Div icon besar -->
                            <i class="ti ti-alert-triangle"></i> <!-- Icon warning Tabler -->
                        </div>

                        <h1 class="text-danger fw-bold mb-3">
                            <?= $title; ?> <!-- Judul pesan error -->
                        </h1>

                        <p class="text-muted fs-5 mb-4">
                            <?= $message; ?> <!-- Pesan error dinamis -->
                        </p>

                        <!-- Tombol aksi -->
                        <div class="mt-4">

                            <button onclick="history.back()" class="btn btn-primary me-2"> <!-- Kembali ke halaman sebelumnya -->
                                <i class="ti ti-arrow-left me-2"></i> <!-- Icon arrow back -->
                                Kembali
                            </button>

                            <button onclick="window.close()" class="btn btn-outline-secondary"> <!-- Menutup halaman -->
                                <i class="ti ti-x me-2"></i> <!-- Icon close -->
                                Tutup
                            </button>

                        </div>
                    </div> <!-- End of error-card -->

                </div> <!-- End col -->
            </div> <!-- End row -->

        </div> <!-- End container -->
    </div> <!-- End error-container -->
</body>

</html> <!-- Penutup dokumen HTML -->
