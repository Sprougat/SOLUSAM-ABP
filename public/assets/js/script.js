// JavaScript untuk Sistem Manajemen Sampah

// Global Variables
let currentUser = null;
let charts = {};

// Auto calculate total for sampah form
document.addEventListener('DOMContentLoaded', function() {
    // Auto calculate total for sampah form
    const beratSampah = document.getElementById('beratSampah');
    const hargaSampah = document.getElementById('hargaSampah');
    const totalSampah = document.getElementById('totalSampah');
    
    if (beratSampah && hargaSampah && totalSampah) {
        function calculateTotal() {
            const berat = parseFloat(beratSampah.value) || 0;
            const harga = parseFloat(hargaSampah.value) || 0;
            const total = berat * harga;
            totalSampah.value = formatCurrency(total);
        }
        
        beratSampah.addEventListener('input', calculateTotal);
        hargaSampah.addEventListener('input', calculateTotal);
    }
});

// Initialize App
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    updateCurrentDate();
});

// Initialize Application
function initializeApp() {
    // Check if user is logged in
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
        showDashboard();
    } else {
        showLogin();
    }
}

// Setup Event Listeners
function setupEventListeners() {
    // Login form submission
    const loginForm = document.querySelector('#loginPage form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            login();
        });
    }

    // Sidebar toggle for mobile
    const sidebarToggle = document.querySelector('[data-bs-target="#sidebarMenu"]');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            toggleSidebar();
        });
    }

    // Window resize handler
    window.addEventListener('resize', function() {
        handleResize();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        handleKeyboardShortcuts(e);
    });
}

// Login Function
function login() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Simple validation (in real app, this would be server-side)
    if (!username || !password) {
        showAlert('error', 'Username dan password harus diisi!');
        return;
    }

    // Simulate login process
    showLoading(true);
    
    setTimeout(() => {
        // Mock authentication
        if (username === 'admin' && password === 'admin') {
            currentUser = {
                id: 1,
                username: username,
                name: 'Administrator',
                role: 'admin'
            };
            
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            showAlert('success', 'Login berhasil!');
            showDashboard();
        } else {
            showAlert('error', 'Username atau password salah!');
        }
        showLoading(false);
    }, 1000);
}

// Logout Function
function logout() {
    if (confirm('Apakah Anda yakin ingin keluar?')) {
        currentUser = null;
        localStorage.removeItem('currentUser');
        showLogin();
        showAlert('info', 'Anda telah berhasil keluar.');
    }
}

// Show Login Page
function showLogin() {
    document.getElementById('loginPage').classList.remove('d-none');
    document.getElementById('dashboardPage').classList.add('d-none');
    document.getElementById('username').focus();
}

// Show Dashboard
function showDashboard() {
    document.getElementById('loginPage').classList.add('d-none');
    document.getElementById('dashboardPage').classList.remove('d-none');
    
    // Initialize charts
    setTimeout(() => {
        initializeCharts();
    }, 100);
    
    // Update sidebar active state
    updateSidebarActive('dashboard');
}

// Show Specific Page
function showPage(pageName) {
    // Hide all page contents
    const pageContents = document.querySelectorAll('.page-content');
    pageContents.forEach(page => {
        page.classList.add('d-none');
    });

    // Show selected page
    const selectedPage = document.getElementById(pageName);
    if (selectedPage) {
        selectedPage.classList.remove('d-none');
        
        // Update sidebar active state
        updateSidebarActive(pageName);
        
        // Initialize page-specific functionality
        initializePage(pageName);
    }
}

// Initialize Page Specific Functionality
function initializePage(pageName) {
    switch(pageName) {
        case 'dashboard':
            initializeCharts();
            break;
        case 'dataKlien':
            initializeDataKlien();
            break;
        case 'dataSampah':
            initializeDataSampah();
            break;
        case 'dataLaporan':
            initializeDataLaporan();
            break;
        case 'dataPemasukan':
            initializeDataPemasukan();
            break;
        case 'dataPengeluaran':
            initializeDataPengeluaran();
            break;
        case 'pembelian':
            initializePembelian();
            break;
        case 'penjualan':
            initializePenjualan();
            break;
        case 'gantiPassword':
            initializeGantiPassword();
            break;
    }
}

// Initialize Charts
function initializeCharts() {
    // Sampah Chart
    const sampahCtx = document.getElementById('sampahChart');
    if (sampahCtx && !charts.sampahChart) {
        charts.sampahChart = new Chart(sampahCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Berat Sampah (kg)',
                    data: [1200, 1900, 3000, 2500, 2200, 3000],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Jenis Sampah Chart
    const jenisSampahCtx = document.getElementById('jenisSampahChart');
    if (jenisSampahCtx && !charts.jenisSampahChart) {
        charts.jenisSampahChart = new Chart(jenisSampahCtx, {
            type: 'doughnut',
            data: {
                labels: ['Plastik', 'Kertas', 'Logam', 'Kaca', 'Lainnya'],
                datasets: [{
                    data: [35, 25, 15, 15, 10],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#fd7e14',
                        '#6f42c1',
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Update Sidebar Active State
function updateSidebarActive(activePage) {
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
    });

    const activeLink = document.querySelector(`[onclick="showPage('${activePage}')"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// Update Current Date
function updateCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        dateElement.textContent = now.toLocaleDateString('id-ID', options);
    }
}

// Show Loading State
function showLoading(show) {
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        if (show) {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.setAttribute('data-original-text', originalText);
            button.innerHTML = '<span class="loading"></span> Loading...';
        } else {
            button.disabled = false;
            const originalText = button.getAttribute('data-original-text');
            if (originalText) {
                button.innerHTML = originalText;
            }
        }
    });
}

// Show Alert
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Toggle Sidebar (Mobile)
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('show');
}

// Handle Window Resize
function handleResize() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth <= 991.98) {
        sidebar.classList.remove('show');
        mainContent.style.marginLeft = '0';
    } else {
        mainContent.style.marginLeft = '250px';
    }
}

// Handle Keyboard Shortcuts
function handleKeyboardShortcuts(e) {
    // Ctrl + K for search (if implemented)
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        // Implement search functionality
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }
}

// Initialize Page Functions
function initializeDataKlien() {
    // Add event listeners for data klien page
    console.log('Data Klien page initialized');
}

function initializeDataSampah() {
    // Add event listeners for data sampah page
    console.log('Data Sampah page initialized');
}

function initializeDataLaporan() {
    // Add event listeners for data laporan page
    console.log('Data Laporan page initialized');
}

function initializeDataPemasukan() {
    // Add event listeners for data pemasukan page
    console.log('Data Pemasukan page initialized');
}

function initializeDataPengeluaran() {
    // Add event listeners for data pengeluaran page
    console.log('Data Pengeluaran page initialized');
}

function initializePembelian() {
    // Add event listeners for pembelian page
    console.log('Pembelian page initialized');
}

function initializePenjualan() {
    // Add event listeners for penjualan page
    console.log('Penjualan page initialized');
}

function initializeGantiPassword() {
    // Add event listeners for ganti password page
    const form = document.querySelector('#gantiPassword form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            changePassword();
        });
    }
}

// Change Password Function
function changePassword() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Validation
    if (!currentPassword || !newPassword || !confirmPassword) {
        showAlert('error', 'Semua field harus diisi!');
        return;
    }

    if (newPassword !== confirmPassword) {
        showAlert('error', 'Password baru dan konfirmasi password tidak sama!');
        return;
    }

    if (newPassword.length < 6) {
        showAlert('error', 'Password baru minimal 6 karakter!');
        return;
    }

    // Simulate password change
    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Password berhasil diubah!');
        document.getElementById('gantiPassword').querySelector('form').reset();
        showLoading(false);
    }, 1000);
}

// Utility Functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}

function formatDateTime(date) {
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
}

// Modal Functions
function simpanKlien() {
    const namaKlien = document.getElementById('namaKlien').value;
    const emailKlien = document.getElementById('emailKlien').value;
    const teleponKlien = document.getElementById('teleponKlien').value;
    const statusKlien = document.getElementById('statusKlien').value;
    const alamatKlien = document.getElementById('alamatKlien').value;

    if (!namaKlien || !teleponKlien || !alamatKlien) {
        showAlert('error', 'Field yang bertanda * harus diisi!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Data klien berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('tambahKlienModal')).hide();
        document.getElementById('formTambahKlien').reset();
        showLoading(false);
    }, 1000);
}

function simpanSampah() {
    const tanggalSampah = document.getElementById('tanggalSampah').value;
    const klienSampah = document.getElementById('klienSampah').value;
    const jenisSampah = document.getElementById('jenisSampah').value;
    const beratSampah = document.getElementById('beratSampah').value;
    const hargaSampah = document.getElementById('hargaSampah').value;

    if (!tanggalSampah || !klienSampah || !jenisSampah || !beratSampah || !hargaSampah) {
        showAlert('error', 'Field yang bertanda * harus diisi!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Data sampah berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('tambahSampahModal')).hide();
        document.getElementById('formTambahSampah').reset();
        showLoading(false);
    }, 1000);
}

function simpanPemasukan() {
    const tanggalPemasukan = document.getElementById('tanggalPemasukan').value;
    const kategoriPemasukan = document.getElementById('kategoriPemasukan').value;
    const deskripsiPemasukan = document.getElementById('deskripsiPemasukan').value;
    const jumlahPemasukan = document.getElementById('jumlahPemasukan').value;

    if (!tanggalPemasukan || !kategoriPemasukan || !deskripsiPemasukan || !jumlahPemasukan) {
        showAlert('error', 'Field yang bertanda * harus diisi!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Data pemasukan berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('tambahPemasukanModal')).hide();
        document.getElementById('formTambahPemasukan').reset();
        showLoading(false);
    }, 1000);
}

function simpanPengeluaran() {
    const tanggalPengeluaran = document.getElementById('tanggalPengeluaran').value;
    const kategoriPengeluaran = document.getElementById('kategoriPengeluaran').value;
    const deskripsiPengeluaran = document.getElementById('deskripsiPengeluaran').value;
    const jumlahPengeluaran = document.getElementById('jumlahPengeluaran').value;

    if (!tanggalPengeluaran || !kategoriPengeluaran || !deskripsiPengeluaran || !jumlahPengeluaran) {
        showAlert('error', 'Field yang bertanda * harus diisi!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Data pengeluaran berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('tambahPengeluaranModal')).hide();
        document.getElementById('formTambahPengeluaran').reset();
        showLoading(false);
    }, 1000);
}

function simpanPembelian() {
    const tanggalPembelian = document.getElementById('tanggalPembelian').value;
    const supplierPembelian = document.getElementById('supplierPembelian').value;

    if (!tanggalPembelian || !supplierPembelian) {
        showAlert('error', 'Field yang bertanda * harus diisi!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Data pembelian berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('tambahPembelianModal')).hide();
        document.getElementById('formTambahPembelian').reset();
        showLoading(false);
    }, 1000);
}

function simpanPenjualan() {
    const tanggalPenjualan = document.getElementById('tanggalPenjualan').value;
    const pelangganPenjualan = document.getElementById('pelangganPenjualan').value;

    if (!tanggalPenjualan || !pelangganPenjualan) {
        showAlert('error', 'Field yang bertanda * harus diisi!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Data penjualan berhasil disimpan!');
        bootstrap.Modal.getInstance(document.getElementById('tambahPenjualanModal')).hide();
        document.getElementById('formTambahPenjualan').reset();
        showLoading(false);
    }, 1000);
}

// Item Management Functions
function tambahItemPembelian() {
    const container = document.getElementById('itemPembelianContainer');
    const newItem = document.createElement('div');
    newItem.className = 'row item-pembelian mb-3';
    newItem.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Nama Item</label>
            <input type="text" class="form-control" name="namaItem" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" class="form-control" name="jumlahItem" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Satuan</label>
            <select class="form-select" name="satuanItem">
                <option value="pcs">Pcs</option>
                <option value="kg">Kg</option>
                <option value="liter">Liter</option>
                <option value="meter">Meter</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Harga Satuan</label>
            <input type="number" class="form-control" name="hargaItem" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="text" class="form-control" name="totalItem" readonly>
        </div>
    `;
    container.appendChild(newItem);
}

function tambahItemPenjualan() {
    const container = document.getElementById('itemPenjualanContainer');
    const newItem = document.createElement('div');
    newItem.className = 'row item-penjualan mb-3';
    newItem.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Nama Item</label>
            <input type="text" class="form-control" name="namaItem" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" class="form-control" name="jumlahItem" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Satuan</label>
            <select class="form-select" name="satuanItem">
                <option value="kg">Kg</option>
                <option value="pcs">Pcs</option>
                <option value="liter">Liter</option>
                <option value="meter">Meter</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Harga Satuan</label>
            <input type="number" class="form-control" name="hargaItem" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="text" class="form-control" name="totalItem" readonly>
        </div>
    `;
    container.appendChild(newItem);
}

// Backup & Restore Functions
function backupData() {
    showAlert('info', 'Fitur backup akan segera tersedia!');
}

function restoreData() {
    showAlert('info', 'Fitur restore akan segera tersedia!');
}

function startBackup() {
    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Backup data berhasil dibuat!');
        showLoading(false);
    }, 2000);
}

function startRestore() {
    const fileInput = document.getElementById('backupFile');
    if (!fileInput.files[0]) {
        showAlert('error', 'Pilih file backup terlebih dahulu!');
        return;
    }

    if (confirm('Apakah Anda yakin ingin melakukan restore? Data yang ada akan diganti.')) {
        showLoading(true);
        
        setTimeout(() => {
            showAlert('success', 'Restore data berhasil!');
            showLoading(false);
        }, 2000);
    }
}

// Financial Report Functions
function generateLaporanKeuangan() {
    const periodeDari = document.getElementById('periodeDari').value;
    const periodeSampai = document.getElementById('periodeSampai').value;
    const jenisLaporan = document.getElementById('jenisLaporanKeuangan').value;

    if (!periodeDari || !periodeSampai) {
        showAlert('error', 'Pilih periode laporan terlebih dahulu!');
        return;
    }

    showLoading(true);
    
    setTimeout(() => {
        showAlert('success', 'Laporan keuangan berhasil dibuat!');
        showLoading(false);
        
        // Initialize financial charts
        initializeFinancialCharts();
    }, 1500);
}

function initializeFinancialCharts() {
    // Grafik Keuangan
    const grafikKeuanganCtx = document.getElementById('grafikKeuangan');
    if (grafikKeuanganCtx && !charts.grafikKeuangan) {
        charts.grafikKeuangan = new Chart(grafikKeuanganCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Pemasukan',
                    data: [5000000, 6000000, 7000000, 5500000, 8000000, 9000000],
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderColor: '#0d6efd',
                    borderWidth: 1
                }, {
                    label: 'Pengeluaran',
                    data: [3000000, 3500000, 4000000, 3200000, 4500000, 5000000],
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Kategori Pengeluaran Chart
    const kategoriPengeluaranCtx = document.getElementById('kategoriPengeluaranChart');
    if (kategoriPengeluaranCtx && !charts.kategoriPengeluaran) {
        charts.kategoriPengeluaran = new Chart(kategoriPengeluaranCtx, {
            type: 'pie',
            data: {
                labels: ['Operasional', 'Transportasi', 'Peralatan', 'Gaji', 'Lainnya'],
                datasets: [{
                    data: [40, 25, 20, 10, 5],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#fd7e14',
                        '#6f42c1',
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Export functions for global access
window.simpanKlien = simpanKlien;
window.simpanSampah = simpanSampah;
window.simpanPemasukan = simpanPemasukan;
window.simpanPengeluaran = simpanPengeluaran;
window.simpanPembelian = simpanPembelian;
window.simpanPenjualan = simpanPenjualan;
window.tambahItemPembelian = tambahItemPembelian;
window.tambahItemPenjualan = tambahItemPenjualan;
window.backupData = backupData;
window.restoreData = restoreData;
window.startBackup = startBackup;
window.startRestore = startRestore;
window.generateLaporanKeuangan = generateLaporanKeuangan;
