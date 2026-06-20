<?= $this->extend('template/index'); ?> <!--Mengambil kerangka dasar website (sidebar, navbar, footer)-->
<?php echo $this->section('content'); 

function formatRupiah($number)
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>

<div class="container-fluid my-4">
    <p class="text-end text-muted"><i class="ti ti-calendar-week"></i><?= $tanggal; ?></p>

    <!-- Judul -->
    <h1 class="h3 fw-bold text-dark">Dashboard SOLUSAM</h1>
    <p class="text-muted">
        Ringkasan data dan statistik sistem
        
    </p>

    <!-- Cards -->
    <div class="row g-4 mt-2">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Transaksi</p>
                    <h5 class="fw-bold"><?= $ringkasanBulan['jumlah']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Berat Sampah</p>
                    <h5 class="fw-bold"><?= $ringkasanBulan['total_jml']; ?> kg</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Uang Masuk</p>
                    <?php 
                        $totalUangMasuk = $ringkasanBulan['total_pendapatan'];
                        $warnaUangMasuk = $totalUangMasuk < 0 ? 'text-danger' : 'text-success';
                    ?>
                    <h5 class="fw-bold <?= $warnaUangMasuk; ?>"><?= formatRupiah($totalUangMasuk); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Uang Keluar</p>
                    <h5 class="fw-bold text-danger"><?= formatRupiah($ringkasanBulan['total_pengeluaran']); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions & Summary menggunakan looping--> 
    <div class="row g-4 mt-3">
        <!-- Transaksi Terbaru -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold text-dark mb-3">Transaksi Terbaru</h5>
                    <ul class="list-unstyled">
                        <?php
                        
                        
                        foreach ($lastTransaksi as $row) {
                            $harga = $row['jenis'] == 'in' ? $row['harga_beli'] : $row['harga_jual'];
                            $jenis = $row['jenis'] == 'in' ? 'Pembelian' : 'Penjualan';
                            $total = $harga * $row['jumlah'];
                        ?>
                            <li class="d-flex justify-content-between align-items-start bg-success bg-opacity-10 p-2 rounded mb-2">
                                <div>
                                    <p class="fw-medium text-dark mb-0"><?= $jenis . ' - ' . $row['nama_sampah']; ?> </p>
                                    <small class="text-muted"><?= $row['tanggal']; ?></small>
                                </div>
                                <span class="text-success fw-medium"><?= formatRupiah($total); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Ringkasan Bulan Ini -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold text-dark mb-3">Ringkasan Bulan Ini <?= date('M'); ?></h5>
                    <ul class="list-unstyled small">
                        <li class="d-flex justify-content-between mb-2">
                            <span>Penjualan</span> <span class="text-success"><?= formatRupiah($ringkasanBulan['total_pendapatan']); ?></span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Pembelian</span> <span class="text-success"><?= formatRupiah($ringkasanBulan['total_pengeluaran']); ?></span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Keuntungan</span> <span class="text-success"><?= formatRupiah($ringkasanBulan['total_keuntungan']); ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Total Berat</span> <span class="fw-medium"><?= $ringkasanBulan['total_jml']; ?> kg</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Prediction Widget -->
    <div class="row g-4 mt-3">
        <div class="col-12">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold text-dark mb-3">Prediksi Pendapatan Bulan Depan</h5>
                    
                    <div id="prediction-loading" class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 small">Memproses data prediksi...</p>
                    </div>
                    
                    <div id="prediction-content" style="display: none;">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <p class="text-muted small mb-1">Pendapatan Bulan Ini</p>
                                    <h6 class="fw-bold text-success" id="current-revenue">Rp 0</h6>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted small mb-1">Prediksi Bulan Depan</p>
                                    <h6 class="fw-bold text-primary" id="next-revenue">Rp 0</h6>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Tingkat Kepercayaan</p>
                                    <span id="confidence-badge" class="badge bg-info small">Loading...</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <canvas id="predictionChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div id="prediction-error" style="display: none;" class="alert alert-warning alert-sm mb-0">
                        <small id="error-message" class="mb-0"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchPrediction();
});

async function fetchPrediction() {
    try {
        const token = '<?= $jwtToken ?? ''; ?>';
        
        if (!token) {
            showError('Token tidak ditemukan. Silakan login terlebih dahulu.');
            return;
        }

        const response = await fetch('<?= base_url('/api/v1/laporan/prediction'); ?>', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        console.log('API Response:', response.status, data);

        if (!response.ok || !data.success) {
            const errorMsg = data.errors || data.message || 'Gagal memproses prediksi';
            console.error('Prediction Error:', errorMsg);
            showError(errorMsg);
            return;
        }

        displayPrediction(data.data);
    } catch (error) {
        console.error('Error:', error);
        showError('Terjadi kesalahan: ' + error.message);
    }
}

function displayPrediction(data) {
    document.getElementById('prediction-loading').style.display = 'none';
    document.getElementById('prediction-error').style.display = 'none';
    document.getElementById('prediction-content').style.display = 'block';

    const currentRevenue = data.historical && data.historical.length > 0
        ? data.historical[data.historical.length - 1].revenue
        : 0;
    const nextRevenue = data.predictions && data.predictions.length > 0
        ? data.predictions[0].revenue
        : 0;

    document.getElementById('current-revenue').textContent = formatRupiah(currentRevenue);
    document.getElementById('next-revenue').textContent = formatRupiah(nextRevenue);

    const confidenceBadge = document.getElementById('confidence-badge');
    const confidenceClass = {
        'insufficient_data': 'bg-secondary',
        'low':               'bg-danger',
        'medium':            'bg-warning text-dark',
        'high':              'bg-info',
        'very_high':         'bg-success'
    };
    const confidenceLabel = {
        'insufficient_data': 'Data Kurang',
        'low':               'Low',
        'medium':            'Medium',
        'high':              'High',
        'very_high':         'Very High'
    };
    const conf = data.confidence || 'low';
    confidenceBadge.className = 'badge ' + (confidenceClass[conf] || 'bg-secondary');
    confidenceBadge.textContent = (confidenceLabel[conf] || conf)
        + (data.r2_score !== undefined ? ' (R²=' + data.r2_score.toFixed(2) + ')' : '');

    if (data.data_points < 3) {
        const warningEl = document.getElementById('prediction-error');
        warningEl.style.display = 'block';
        warningEl.className = 'alert alert-info alert-sm mb-2';
        document.getElementById('error-message').textContent =
            'Prediksi membutuhkan minimal 3 bulan data. Saat ini hanya ada ' + data.data_points + ' bulan. Tambah lebih banyak data transaksi untuk hasil yang lebih akurat.';
    }

    drawChart(data);
}

function drawChart(data) {
    const ctx = document.getElementById('predictionChart').getContext('2d');

    const historical     = data.historical  || [];
    const predictions    = data.predictions || [];

    const historicalLabels = historical.map(h => h.month);
    const historicalVals   = historical.map(h => h.revenue);
    const predictionLabels = predictions.map(p => p.month);
    const predictionVals   = predictions.map(p => p.revenue);

    const allLabels = [...historicalLabels, ...predictionLabels];

    const historicalDataset = [
        ...historicalVals,
        ...new Array(predictionLabels.length).fill(null)
    ];

    const bridgeNulls = new Array(Math.max(0, historicalLabels.length - 1)).fill(null);
    const lastActual  = historicalVals.length > 0 ? historicalVals[historicalVals.length - 1] : null;
    const predictionDataset = [...bridgeNulls, lastActual, ...predictionVals];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                {
                    label: 'Data Aktual',
                    data: historicalDataset,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#0d6efd',
                    spanGaps: false
                },
                {
                    label: 'Prediksi',
                    data: predictionDataset,
                    borderColor: '#fd7e14',
                    borderDash: [6, 4],
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointBackgroundColor: '#fd7e14',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    spanGaps: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'top' },
                title: {
                    display: true,
                    text: 'Historis Pendapatan & Prediksi'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed.y;
                            if (val === null) return null;
                            return context.dataset.label + ': Rp ' +
                                val.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000)    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
}

function showError(message) {
    document.getElementById('prediction-loading').style.display = 'none';
    document.getElementById('prediction-content').style.display = 'none';
    document.getElementById('prediction-error').style.display = 'block';
    document.getElementById('error-message').textContent = message;
}

function formatRupiah(number) {
    return 'Rp ' + number.toLocaleString('id-ID', { 
        minimumFractionDigits: 0, 
        maximumFractionDigits: 0 
    });
}
</script>

<?= $this->endSection(); ?>
