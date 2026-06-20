<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Transaksi;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends BaseController
{
    
    protected $transaksiModel;

     
    public function __construct()
    {
        $this->transaksiModel = new Transaksi();
    }

    
    public function index()
    {
        $data = [
            "title" => "Data Laporan",
        ];

        return view('laporan/index', $data);
    }

    
    public function getLaporanData()
    {
        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $client_id = session()->get('clientId');

        $laporanData = $this->transaksiModel->getLaporan(
            $client_id, 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai
        );

        
        return $this->response->setJSON($laporanData);
    }

    
    public function pemasukan()
    {
        $data = [
            "title" => "Data Laporan Pemasukan",
        ];
        
        return view('laporan/pemasukan', $data);
    }

     
    public function pengeluaran()
    {
        $data = [
            "title" => "Data Laporan Pengeluaran",
        ];

        return view('laporan/pengeluaran', $data);
    }

    
    public function getDataInOut()
    {
        
        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $client_id = session()->get('clientId');
        $jenis = $this->request->getPost('jenis'); 

         
        $laporanData = $this->getDataResult(
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai, 
            $jenis, 
            $client_id
        );
    
        return $this->response->setJSON($laporanData);
    }

    
    protected function getDataResult(
        $tahun, 
        $bulan, 
        $tanggalMulai, 
        $tanggalSelesai, 
        $jenis, 
        $client_id) {
    
        $laporanData = $this->transaksiModel->getDataInOutLaporan(
            $client_id, 
            $jenis, 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai);

        return $laporanData;
    }

    
    public function exportPemasukan()
    {
         
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $client_id = session()->get('clientId');
        
        
        $data = $this->transaksiModel->getDataInOutLaporan(
            $client_id, 
            'out', 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai);

         
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        $sheet->setTitle('Laporan Pemasukan');

         
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

         
        $headers = ['No', 'Tanggal', 'Nama Sampah', 'Jumlah (Kg)', 'Harga Jual', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

       
        $row = 2;
        $totalBerat = 0;
        $totalPendapatan = 0;
        
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['tanggal']);
            $sheet->setCellValue('C' . $row, $item['nama_sampah']);
            $sheet->setCellValue('D' . $row, $item['jumlah']);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['harga_jual'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_pendapatan'], 0, ',', '.'));
            
            $totalBerat += $item['jumlah'];
            $totalPendapatan += $item['total_pendapatan'];
            $row++;
        }

         
        $summaryRow = $row + 1;
        $sheet->setCellValue('C' . $summaryRow, 'TOTAL');
        $sheet->setCellValue('D' . $summaryRow, $totalBerat);
        $sheet->setCellValue('F' . $summaryRow, 'Rp ' . number_format($totalPendapatan, 0, ',', '.'));
        
        
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('C' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

        
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

       
        $filename = 'Laporan_Pemasukan_' . date('Y-m-d_H-i-s') . '.xlsx';

        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPengeluaran()
    {
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $client_id = session()->get('clientId');

        $data = $this->transaksiModel->getDataInOutLaporan($client_id, 'in', $tahun, $bulan, $tanggalMulai, $tanggalSelesai);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        $sheet->setTitle('Laporan Pengeluaran');

        
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        
        $headers = ['No', 'Tanggal', 'Nama Sampah', 'Jumlah (Kg)', 'Harga Beli', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        
        $row = 2;
        $totalBerat = 0;
        $totalPengeluaran = 0;
        
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['tanggal']);
            $sheet->setCellValue('C' . $row, $item['nama_sampah']);
            $sheet->setCellValue('D' . $row, $item['jumlah']);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['harga_beli'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_pengeluaran'], 0, ',', '.'));
            
            $totalBerat += $item['jumlah'];
            $totalPengeluaran += $item['total_pengeluaran'];
            $row++;
        }

        
        $summaryRow = $row + 1;
        $sheet->setCellValue('C' . $summaryRow, 'TOTAL');
        $sheet->setCellValue('D' . $summaryRow, $totalBerat);
        $sheet->setCellValue('F' . $summaryRow, 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        
        
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8E8E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('C' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

        
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        
        $filename = 'Laporan_Pengeluaran_' . date('Y-m-d_H-i-s') . '.xlsx';

        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportLaporan()
    {
        
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
         
        $client_id = session()->get('clientId');

        
        
        $data = $this->transaksiModel->getLaporan(
            $client_id, 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai
        );

        
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        
        
        $sheet->setTitle('Laporan Keuangan');

        
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

       
        $headers = [
            'No', 
            'Periode', 
            'Jumlah Transaksi', 
            'Total Pemasukan', 
            'Total Pengeluaran', 
            'Keuntungan/Kerugian'
        ];

        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

       
        $row = 2;
        
        $totalTransaksi = 0;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        $totalKeuntungan = 0;

          
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['periode']);
            $sheet->setCellValue('C' . $row, $item['jumlah']);
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($item['total_pendapatan'], 0, ',', '.'));
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['total_pengeluaran'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_keuntungan'], 0, ',', '.'));

            
            $totalTransaksi += $item['jumlah'];
            $totalPemasukan += $item['total_pendapatan'];
            $totalPengeluaran += $item['total_pengeluaran'];
            $totalKeuntungan += $item['total_keuntungan'];
            $row++;
        }

       
        $summaryRow = $row + 1;
         
        $sheet->setCellValue('B' . $summaryRow, 'TOTAL');
        $sheet->setCellValue('C' . $summaryRow, $totalTransaksi);
        $sheet->setCellValue('D' . $summaryRow, 'Rp ' . number_format($totalPemasukan, 0, ',', '.'));
        $sheet->setCellValue('E' . $summaryRow, 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        $sheet->setCellValue('F' . $summaryRow, 'Rp ' . number_format($totalKeuntungan, 0, ',', '.'));
        
         
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('B' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

        
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        
        $filename = 'Laporan_Keuangan_' . date('Y-m-d_H-i-s') . '.xlsx';

         
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        
        exit;
    }
}
