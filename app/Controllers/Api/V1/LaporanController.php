<?php

namespace App\Controllers\Api\V1;

use App\Models\Transaksi;
use App\Services\RevenuePredictionService;

class LaporanController extends BaseApiController
{
    public function pemasukan()
    {
        $clientId = $this->getJwtClientId();
        $transaksiModel = new Transaksi();

        $tahun = $this->request->getVar('tahun');
        $bulan = $this->request->getVar('bulan');
        $tanggal_mulai = $this->request->getVar('tanggal_mulai');
        $tanggal_selesai = $this->request->getVar('tanggal_selesai');

        $data = $transaksiModel->getDataInOutLaporan($clientId, 'out', $tahun, $bulan, $tanggal_mulai, $tanggal_selesai);
        $chartData = $transaksiModel->getLaporan($clientId, $tahun, $bulan, $tanggal_mulai, $tanggal_selesai);

        return $this->sendResponse([
            'laporan' => $data,
            'chart' => $chartData
        ], 'Laporan pemasukan retrieved');
    }

    public function pengeluaran()
    {
        $clientId = $this->getJwtClientId();
        $transaksiModel = new Transaksi();

        $tahun = $this->request->getVar('tahun');
        $bulan = $this->request->getVar('bulan');
        $tanggal_mulai = $this->request->getVar('tanggal_mulai');
        $tanggal_selesai = $this->request->getVar('tanggal_selesai');

        $data = $transaksiModel->getDataInOutLaporan($clientId, 'in', $tahun, $bulan, $tanggal_mulai, $tanggal_selesai);
        $chartData = $transaksiModel->getLaporan($clientId, $tahun, $bulan, $tanggal_mulai, $tanggal_selesai);

        return $this->sendResponse([
            'laporan' => $data,
            'chart' => $chartData
        ], 'Laporan pengeluaran retrieved');
    }

    public function prediction()
    {
        $clientId = $this->getJwtClientId();
        if (!$clientId) return $this->sendError(null, 'Akses ditolak. Client ID tidak ditemukan pada token', 403);

        try {
            $predictionService = new RevenuePredictionService();
            $prediction = $predictionService->predictNextThreeMonths($clientId);

            return $this->sendResponse($prediction, 'Revenue prediction with forecast retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 'Error generating prediction', 500);
        }
    }

    /**
     * Get model evaluation metrics
     */
    public function evaluateModel()
    {
        $clientId = $this->getJwtClientId();
        if (!$clientId) return $this->sendError(null, 'Akses ditolak. Client ID tidak ditemukan pada token', 403);

        try {
            $predictionService = new RevenuePredictionService();
            $evaluation = $predictionService->evaluateModel($clientId);

            return $this->sendResponse($evaluation, 'Model evaluation completed successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 'Error evaluating model', 500);
        }
    }

    public function exportPemasukan()
    {
        $clientId = $this->getJwtClientId();
        $transaksiModel = new Transaksi();

        $tahun = $this->request->getVar('tahun');
        $bulan = $this->request->getVar('bulan');
        $tanggal_mulai = $this->request->getVar('tanggal_mulai');
        $tanggal_selesai = $this->request->getVar('tanggal_selesai');

        $data = $transaksiModel->getDataInOutLaporan($clientId, 'out', $tahun, $bulan, $tanggal_mulai, $tanggal_selesai);

        $filename = 'laporan_pemasukan_' . date('Ymd_His') . '.csv';
        $csv = $this->generateCsv($data, 'out');

        return $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    public function exportPengeluaran()
    {
        $clientId = $this->getJwtClientId();
        $transaksiModel = new Transaksi();

        $tahun = $this->request->getVar('tahun');
        $bulan = $this->request->getVar('bulan');
        $tanggal_mulai = $this->request->getVar('tanggal_mulai');
        $tanggal_selesai = $this->request->getVar('tanggal_selesai');

        $data = $transaksiModel->getDataInOutLaporan($clientId, 'in', $tahun, $bulan, $tanggal_mulai, $tanggal_selesai);

        $filename = 'laporan_pengeluaran_' . date('Ymd_His') . '.csv';
        $csv = $this->generateCsv($data, 'in');

        return $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function generateCsv(array $data, string $jenis): string
    {
        $output = fopen('php://temp', 'r+');

        // Header
        fputcsv($output, ['Tanggal', 'Nama Sampah', 'Jumlah', 'Harga Satuan', 'Total', 'Metode Bayar', 'Nama Klien']);

        foreach ($data as $row) {
            $hargaSatuan = $jenis === 'out' ? (float) $row['harga_jual'] : (float) $row['harga_beli'];
            $total = $jenis === 'out' ? (float) $row['total_pendapatan'] : (float) $row['total_pengeluaran'];

            fputcsv($output, [
                $row['tanggal'],
                $row['nama_sampah'],
                (int) $row['jumlah'],
                $hargaSatuan,
                $total,
                $row['metode_bayar'],
                $row['nama_lengkap'] ?? '-',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
