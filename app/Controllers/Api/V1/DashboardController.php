<?php

namespace App\Controllers\Api\V1;

use App\Models\Transaksi;
use App\Models\MetodePembayaran;
use App\Services\RevenuePredictionService;

class DashboardController extends BaseApiController
{
    public function index()
    {
        $clientId = $this->getJwtClientId();
        if (!$clientId) return $this->sendError(null, 'Akses ditolak. Client ID tidak ditemukan pada token', 403);

        $transaksiModel = new Transaksi();
        $metodeModel    = new MetodePembayaran();

        $bulan = date('m');
        $tahun = date('Y');

        $ringkasan = $transaksiModel->getRingkasanBulan($clientId, $bulan, $tahun);
        $totalAll  = $transaksiModel->getTotalAll($clientId);
        $metode    = $metodeModel->where('client_id', $clientId)->findAll();

        $data = [
            'ringkasan_bulan_ini' => [
                'total_transaksi'   => $ringkasan['jumlah'] ?? 0,
                'total_pendapatan'  => $ringkasan['total_pendapatan'] ?? 0,
                'total_pengeluaran' => $ringkasan['total_pengeluaran'] ?? 0,
                'total_keuntungan'  => $ringkasan['total_keuntungan'] ?? 0,
            ],
            'ringkasan_semua' => [
                'total_transaksi'   => $totalAll['jumlah'] ?? 0,
                'total_pendapatan'  => $totalAll['total_pendapatan'] ?? 0,
                'total_pengeluaran' => $totalAll['total_pengeluaran'] ?? 0,
                'total_keuntungan'  => $totalAll['total_keuntungan'] ?? 0,
            ],
            'metode_pembayaran_aktif' => count($metode)
        ];

        return $this->sendResponse($data, 'Dashboard data retrieved successfully');
    }

    public function prediction()
    {
        $clientId = $this->getJwtClientId();
        if (!$clientId) return $this->sendError(null, 'Akses ditolak. Client ID tidak ditemukan pada token', 403);

        try {
            $predictionService = new RevenuePredictionService();
            $prediction = $predictionService->predictNextMonth($clientId);

            return $this->sendResponse($prediction, 'Revenue prediction retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 'Error generating prediction', 500);
        }
    }
}
