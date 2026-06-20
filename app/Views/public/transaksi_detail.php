<!DOCTYPE html> // Mendefinisikan dokumen HTML5
<html lang="id"> // Tag pembuka HTML dengan bahasa Indonesia

<head> // Bagian head berisi metadata dan link stylesheet
    <meta charset="UTF-8"> // Mengatur encoding karakter sebagai UTF-8
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> // Mengatur viewport agar responsive di mobile
    <title><?= $title; ?></title> // Judul halaman menggunakan variabel dari server (PHP)

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> // Memuat CSS Bootstrap 5.3
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@1.0.0/icons-sprite.svg" rel="stylesheet"> // Memuat ikon Tabler

    <style> // Bagian CSS internal
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); // Background gradien biru ke ungu
            min-height: 100vh; // Tinggi minimum satu layar penuh
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; // Font utama
        }

        .card {
            border: none; // Menghilangkan border bawaan
            border-radius: 15px; // Membuat sudut membulat
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); // Menambahkan bayangan lembut
        }

        .card-header {
            background: linear-gradient(45deg, #28a745, #20c997); // Gradien hijau untuk header card
            color: white; // Teks putih
            border-radius: 15px 15px 0 0 !important; // Membuat sudut atas membulat
            border: none; // Menghilangkan border default
        }

        .badge {
            font-size: 0.9em; // Ukuran teks badge
            padding: 0.5em 0.8em; // Padding badge
        }

        .info-item {
            background: #f8f9fa; // Warna latar abu-abu terang
            border-radius: 8px; // Sudut membulat
            padding: 1rem; // Spasi dalam kasan
            margin-bottom: 1rem; // Jarak bawah antar elemen
            border-left: 4px solid #28a745; // Garis hijau di sebelah kiri
        }

        .qr-code-container {
            background: white; // Latar putih untuk kotak QR
            border-radius: 10px; // Sudut membulat
            padding: 1.5rem; // Padding
            text-align: center; // Isi berada di tengah
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); // Bayangan lembut
        }

        .btn-custom {
            border-radius: 25px; // Tombol dengan bentuk kapsul
            padding: 0.7rem 2rem; // Padding besar
            font-weight: 600; // Ketebalan font
            text-transform: uppercase; // Teks kapital
            letter-spacing: 0.5px; // Jarak antar huruf
        }

        .status-badge {
            font-size: 1.1em; // Ukuran badge status
            padding: 0.6rem 1.2rem; // Padding badge
            border-radius: 25px; // Sudut badge membulat
        }
    </style>
</head> // Penutup head

<body> // Tag pembuka body

    <div class="container py-5"> // Container utama dengan padding vertical
        <div class="row justify-content-center"> // Row untuk memusatkan konten
            <div class="col-lg-8"> // Kolom dengan lebar 8 grid pada layar besar

                <div class="text-center mb-4"> // Bagian header halaman, teks rata tengah
                    <h1 class="text-white fw-bold mb-2"> // Judul besar berwarna putih
                        <i class="ti ti-receipt me-3"></i> // Ikon nota dari Tabler
                        Detail Transaksi // Teks judul
                    </h1>
                    <p class="text-white-50">Informasi lengkap transaksi pembelian sampah</p> // Subjudul
                </div>

                <div class="card"> // Card utama untuk menampilkan detail transaksi
                    <div class="card-header"> // Bagian header card
                        <div class="d-flex justify-content-between align-items-center"> // Membuat elemen tersusun horizontal
                            <h4 class="mb-0"> // Judul kecil
                                <i class="ti ti-shopping-cart me-2"></i> // Ikon keranjang
                                Transaksi #<?= $transaksi['id']; ?> // Menampilkan ID transaksi
                            </h4>

                            <span class="status-badge bg-success"> // Badge status berwarna hijau
                                <i class="ti ti-check me-1"></i> // Ikon check
                                Selesai // Status transaksi
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4"> // Isi utama card dengan padding 4

                        <div class="row g-4 mb-4"> // Baris pertama: informasi tanggal dan waktu transaksi
                            <div class="col-md-6"> // Kolom kiri
                                <div class="info-item"> // Kotak informasi dengan border kiri hijau
                                    <h6 class="text-primary fw-bold mb-2"> // Judul info
                                        <i class="ti ti-calendar me-2"></i> // Ikon kalender
                                        Tanggal Transaksi
                                    </h6>
                                    <p class="mb-0 fs-5">
                                        <?= date('d F Y', strtotime($transaksi['tanggal'])); ?> // Format tanggal transaksi
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6"> // Kolom kanan
                                <div class="info-item">
                                    <h6 class="text-primary fw-bold mb-2"> // Judul info
                                        <i class="ti ti-clock me-2"></i> // Ikon jam
                                        Waktu Transaksi
                                    </h6>
                                    <p class="mb-0 fs-5">
                                        <?= date('H:i', strtotime($transaksi['created_at'])); ?> WIB // Format jam transaksi
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4"> // Baris kedua: nama sampah dan jumlah
                            <div class="col-md-6"> // Kolom kiri
                                <div class="info-item">
                                    <h6 class="text-success fw-bold mb-2"> // Judul info
                                        <i class="ti ti-package me-2"></i> // Ikon paket
                                        Nama Sampah
                                    </h6>
                                    <p class="mb-0 fs-5">
                                        <?= $transaksi['nama_sampah']; ?> // Menampilkan nama sampah
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6"> // Kolom kanan
                                <div class="info-item">
                                    <h6 class="text-success fw-bold mb-2"> // Judul info
                                        <i class="ti ti-scale me-2"></i> // Ikon timbangan
                                        Jumlah
                                    </h6>
                                    <p class="mb-0 fs-5">
                                        <?= $transaksi['jumlah']; ?> kg // Menampilkan jumlah sampah
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4"> // Baris ketiga: harga per kg dan total harga
                            <div class="col-md-6"> // Kolom kiri
                                <div class="info-item">
                                    <h6 class="text-warning fw-bold mb-2"> // Judul info
                                        <i class="ti ti-currency-dollar me-2"></i> // Ikon mata uang
                                        Harga per kg
                                    </h6>
                                    <p class="mb-0 fs-5">
                                        Rp <?= number_format($transaksi['harga_jual'], 0, ',', '.'); ?> // Format harga per kg
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6"> // Kolom kanan
                                <div class="info-item bg-success bg-opacity-10"> // Latar hijau tipis
                                    <h6 class="text-success fw-bold mb-2">
                                        <i class="ti ti-calculator me-2"></i> // Ikon kalkulator
                                        Total Harga
                                    </h6>
                                    <p class="mb-0 fs-4 fw-bold text-success">
                                        Rp <?= number_format($total_harga, 0, ',', '.'); ?> // Total harga (jumlah * harga)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4"> // Baris keempat: metode pembayaran + bukti (jika ada)
                            <div class="col-md-6"> // Kolom kiri
                                <div class="info-item">
                                    <h6 class="text-info fw-bold mb-2">
                                        <i class="ti ti-credit-card me-2"></i> // Ikon kartu
                                        Metode Pembayaran
                                    </h6>
                                    <p class="mb-0 fs-5">

                                        <?php if ($transaksi['metode_bayar'] === 'qris'): ?> // Jika metode = QRIS
                                            <span class="badge bg-primary"> // Badge warna biru
                                                <i class="ti ti-qrcode me-1"></i> // Ikon QR
                                                QRIS
                                            </span>
                                        <?php else: ?> // Jika metode = tunai
                                            <span class="badge bg-success"> // Badge hijau
                                                <i class="ti ti-cash me-1"></i> // Ikon uang
                                                Tunai
                                            </span>
                                        <?php endif; ?>

                                    </p>
                                </div>
                            </div>

                            <?php if ($transaksi['bukti']): ?> // Jika ada file bukti pembayaran
                                <div class="col-md-6"> // Kolom kanan
                                    <div class="info-item">
                                        <h6 class="text-info fw-bold mb-2">
                                            <i class="ti ti-file-text me-2"></i> // Ikon file
                                            Bukti Pembayaran
                                        </h6>

                                        <a href="<?= base_url('bukti/' . $transaksi['bukti']); ?>" // Link ke gambar bukti
                                           target="_blank" // Buka di tab baru
                                           class="btn btn-outline-primary btn-sm"> // Tombol lihat bukti
                                            <i class="ti ti-eye me-1"></i> // Ikon mata
                                            Lihat Bukti
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <?php if ($transaksi['nama_lengkap']): ?> // Jika transaksi punya data penjual
                            <div class="row g-4 mb-4"> // Baris kelima: informasi penjual
                                <div class="col-12">
                                    <div class="info-item">
                                        <h6 class="text-secondary fw-bold mb-3">
                                            <i class="ti ti-building-store me-2"></i> // Ikon toko
                                            Informasi Penjual
                                        </h6>

                                        <div class="row"> // Membagi info penjual menjadi 2 kolom
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Nama:</strong> <?= $transaksi['nama_lengkap']; ?></p> // Nama penjual
                                                <p class="mb-1"><strong>Jenis Usaha:</strong> <?= $transaksi['jenis_usaha']; ?></p> // Usaha
                                            </div>

                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>No. Telepon:</strong> <?= $transaksi['no_telp']; ?></p> // Nomor HP
                                                <p class="mb-1"><strong>Alamat:</strong> <?= $transaksi['alamat']; ?></p> // Alamat
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row justify-content-center"> // Baris untuk menampilkan QR Code di tengah
                            <div class="col-md-6"> // Lebar kolom QR
                                <div class="qr-code-container"> // Kotak khusus QR Code
                                    <h6 class="text-muted mb-3">
                                        <i class="ti ti-qrcode me-2"></i> // Ikon QR
                                        QR Code Verifikasi
                                    </h6>

                                    <img
                                        src="<?= base_url('public/qrcode/' . $transaksi['id']); ?>" // Path QR Code
                                        alt="QR Code Verifikasi"
                                        class="img-fluid" // Responsive gambar
                                        style="max-width: 200px;"> // Maksimal lebar QR

                                    <p class="text-muted mt-3 small">
                                        Scan QR Code ini untuk memverifikasi transaksi // Informasi tambahan
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div> <!-- END card-body -->
                </div> <!-- END card -->

                <div class="text-center mt-4"> // Bagian footer halaman
                    <p class="text-white-50">
                        <i class="ti ti-shield-check me-2"></i> // Ikon shield
                        Transaksi ini telah diverifikasi dan tercatat dalam sistem
                    </p>

                    <div class="mt-3">
                        <button onclick="window.print()" class="btn btn-light btn-custom me-2"> // Tombol cetak
                            <i class="ti ti-printer me-2"></i> // Ikon printer
                            Cetak Detail
                        </button>

                        <button onclick="window.close()" class="btn btn-outline-light btn-custom"> // Tombol tutup halaman
                            <i class="ti ti-x me-2"></i> // Ikon X
                            Tutup
                        </button>
                    </div>
                </div>

            </div> <!-- END col -->
        </div> <!-- END row -->
    </div> <!-- END container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> // Script Bootstrap

    <script>
        // Event sebelum print
        window.addEventListener('beforeprint', function() {
            document.body.style.background = 'white'; // Ubah background agar bersih saat print

            document.querySelectorAll('.card').forEach(card => {
                card.style.boxShadow = 'none'; // Hilangkan shadow
                card.style.border = '1px solid #ddd'; // Tambah border ringan
            });
        });

        // Event setelah print
        window.addEventListener('afterprint', function() {
            document.body.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; // Kembalikan background asli
        });
    </script>

</body>

</html>
