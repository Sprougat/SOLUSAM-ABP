<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Transaksi;

use App\Models\Sampah;

class PublicTransaksiController extends BaseController
{
     
    protected $transaksiModel;
     
    protected $sampahModel;

     

    public function __construct()
    {
        
        $this->transaksiModel = new Transaksi();
         
        $this->sampahModel = new Sampah();
    }

     

    public function detail($id)
    {
        try {
           
            
            $builder = $this->transaksiModel->db->table('transaksi t');
            
             
            $builder->select(
                't.*, 
                s.nama_sampah, 
                s.harga_jual, 
                s.satuan, 
                c.nama_lengkap, 
                c.no_telp, 
                c.alamat, 
                c.jenis_usaha'
            );
            
            $builder->join('data_sampah s', 's.id = t.sampah_id');
            
            $builder->join('client c', 'c.id = t.client_id', 'left');
             
            $builder->where('t.id', $id);

             
            $transaksi = $builder->get()->getRowArray();

             
            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

             
            $data = [
                'title' => 'Detail Transaksi #' . $id,
                'transaksi' => $transaksi,
                'total_harga' => $transaksi['jumlah'] * $transaksi['harga_jual']
            ];

            
            return view('public/transaksi_detail', $data);
        } catch (\Exception $e) {
            
            return view('public/error', [
                'title' => 'Error',
                'message' => 'Transaksi tidak ditemukan atau telah dihapus.',
                'error_code' => 404
            ]);
        }
    }

    

    public function generateQrCode($id)
    {
        try {
            
            $transaksi = $this->transaksiModel->find($id);

             
            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            
            $qrData = base_url('public/transaksi/' . $id);

             
            $qrCode = new \Endroid\QrCode\QrCode($qrData);
              
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            
            $result = $writer->write($qrCode);

          
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="qrcode-' . $id . '.png"');

             
            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
             
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="error.png"');

            
            $errorImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            return $this->response->setBody($errorImage);
        }
    }
}
