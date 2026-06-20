<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'LoginController::index');
$routes->get('login', 'LoginController::index');
$routes->post('login', 'LoginController::attempLogin');
$routes->get('logout', 'LoginController::logout');
$routes->get('daftar', 'DaftarController::index');
$routes->post('register', 'DaftarController::register');

$routes->get('forgot-password', 'ForgotPasswordController::index');
$routes->post('forgot-password/send', 'ForgotPasswordController::sendResetLink');
$routes->get('reset-password/(:any)', 'ForgotPasswordController::resetPassword/$1');
$routes->post('reset-password/update', 'ForgotPasswordController::updatePassword');

$routes->get('auth/google', 'AuthController::redirectToGoogle');
$routes->get('auth/google/callback', 'AuthController::handleGoogleCallback');

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

$routes->group('sampah', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SampahController::index');
    $routes->get('create', 'SampahController::create');
    $routes->post('store', 'SampahController::store');
    $routes->get('edit/(:num)', 'SampahController::edit/$1');
    $routes->post('delete', 'SampahController::delete');
});

$routes->group('data-klien', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DataKlienController::index');
    $routes->get('create', 'DataKlienController::create');
    $routes->post('store', 'DataKlienController::store');
    $routes->get('edit/(:num)', 'DataKlienController::edit/$1');
    $routes->post('delete', 'DataKlienController::delete');
});

$routes->group('penjualan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PenjualanController::index');
    $routes->get('create', 'PenjualanController::create');
    $routes->post('sampah-ajax', 'PenjualanController::sampahAjax');
    $routes->post('store', 'PenjualanController::store');
    $routes->post('delete', 'PenjualanController::delete');
    $routes->get('qrcode/(:num)', 'PenjualanController::showQrCode/$1');
    $routes->get('qrcode-image/(:num)', 'PenjualanController::generateQrCode/$1');
    $routes->get('qrcode-simple/(:num)', 'PenjualanController::generateQrCodeSimple/$1');
    $routes->get('test-qrcode', 'PenjualanController::testQrCode');
    
    $routes->get('midtrans-payment', 'PenjualanController::midtransPayment');
    $routes->get('midtrans-finish', 'PenjualanController::midtransFinish');
    $routes->get('midtrans-unfinish', 'PenjualanController::midtransUnfinish');
    $routes->get('midtrans-error', 'PenjualanController::midtransError');
});

$routes->post('penjualan/midtrans-notification', 'PenjualanController::midtransNotification');

$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function($routes) {
    
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/google', 'AuthController::google');
    $routes->post('auth/forgot-password', 'AuthController::forgotPassword');
    $routes->post('auth/reset-password', 'AuthController::resetPassword');

    
    $routes->group('', ['filter' => 'api_auth'], function($routes) {
        $routes->get('dashboard', 'DashboardController::index');
        $routes->get('dashboard/prediction', 'DashboardController::prediction');
        
        $routes->get('sampah', 'SampahController::index');
        $routes->post('sampah', 'SampahController::create');
        $routes->put('sampah/(:num)', 'SampahController::update/$1');
        $routes->delete('sampah/(:num)', 'SampahController::delete/$1');

        $routes->get('klien', 'KlienController::index');
        $routes->post('klien', 'KlienController::create');
        $routes->put('klien/(:num)', 'KlienController::update/$1');
        $routes->delete('klien/(:num)', 'KlienController::delete/$1');

        $routes->get('metode-bayar', 'MetodePembayaranController::index');
        $routes->post('metode-bayar', 'MetodePembayaranController::create');
        $routes->put('metode-bayar/(:num)', 'MetodePembayaranController::update/$1');
        $routes->delete('metode-bayar/(:num)', 'MetodePembayaranController::delete/$1');

        $routes->get('transaksi/pembelian', 'PembelianController::index');
        $routes->post('transaksi/pembelian', 'PembelianController::create');
        $routes->delete('transaksi/pembelian/(:num)', 'PembelianController::delete/$1');

        $routes->get('transaksi/penjualan', 'PenjualanController::index');
        $routes->post('transaksi/penjualan', 'PenjualanController::create'); 
        $routes->delete('transaksi/penjualan/(:num)', 'PenjualanController::delete/$1');

        $routes->get('laporan/pemasukan', 'LaporanController::pemasukan');
        $routes->get('laporan/pengeluaran', 'LaporanController::pengeluaran');
        $routes->get('laporan/pemasukan/export', 'LaporanController::exportPemasukan');
        $routes->get('laporan/pengeluaran/export', 'LaporanController::exportPengeluaran');
        $routes->get('laporan/prediction', 'LaporanController::prediction');
        $routes->get('laporan/evaluate-model', 'LaporanController::evaluateModel');
    });
});

$routes->group('pembelian', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PembelianController::index');
    $routes->get('create', 'PembelianController::create');
    $routes->post('sampah-ajax', 'PembelianController::sampahAjax');
    $routes->post('store', 'PembelianController::store');
    $routes->get('edit/(:num)', 'PembelianController::edit/$1');
    $routes->post('delete', 'PembelianController::delete');
});

$routes->group('laporan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'LaporanController::index');
    $routes->post('getLaporanData', 'LaporanController::getLaporanData');
    $routes->post('store', 'LaporanController::store');
    $routes->get('edit/(:num)', 'LaporanController::edit/$1');
    $routes->post('delete', 'LaporanController::delete');
    $routes->get('pemasukan', 'LaporanController::pemasukan');
    $routes->get('export', 'LaporanController::exportLaporan');
});

$routes->get('pemasukan', 'LaporanController::pemasukan', ['filter' => 'auth']);
$routes->get('pengeluaran', 'LaporanController::pengeluaran', ['filter' => 'auth']);
$routes->post('getDataInOut', 'LaporanController::getDataInOut', ['filter' => 'auth']);
$routes->get('export-pemasukan', 'LaporanController::exportPemasukan', ['filter' => 'auth']);
$routes->get('export-pengeluaran', 'LaporanController::exportPengeluaran', ['filter' => 'auth']);

$routes->group('metode-bayar', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'MetodePembayaranController::index');
    $routes->get('create', 'MetodePembayaranController::create');
    $routes->post('store', 'MetodePembayaranController::store');
    $routes->get('edit/(:num)', 'MetodePembayaranController::edit/$1');
    $routes->post('delete', 'MetodePembayaranController::delete');
});

$routes->group('reset', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ResetController::index');
    $routes->post('update', 'ResetController::update');
});

$routes->group('public', function ($routes) {
    $routes->get('transaksi/(:num)', 'PublicTransaksiController::detail/$1');
    $routes->get('qrcode/(:num)', 'PublicTransaksiController::generateQrCode/$1');
});
