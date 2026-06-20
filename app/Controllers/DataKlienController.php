<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DataKlienController extends BaseController
{
    
    protected $klienModel;
    
    protected $transaksiModel;

    
    public function __construct()
    {
         
        $this->klienModel = new \App\Models\Client();
        
        $this->transaksiModel = new \App\Models\Transaksi();
    }
    
    
    public function index()
    {
        $data = [
            "title" => "Data Klien",
            
            "data" => $this->klienModel ->where('client_id', session('clientId'))
            ->findAll()
        ];

        
        return view('klien/index', $data);
    }

    
    public function create()
    {
        $data = [
            "title" => "Tambah Data Klien"
        ];

        
        return view('klien/create', $data);
    }

    
    public function edit($id)
    {
       $data = [
    "title" => "Edit Data Klien",
    
            
            
            
    "data" => $this->klienModel
        ->where('id', $id)
        ->where('user_id', session('userId'))
        ->where('client_id', session('clientId'))
        ->first()
];
        
        return view('klien/edit', $data);
    }

    
    public function store()
    {
        
        $nama_lengkap = strtoupper($this->request->getPost('nama_lengkap'));
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $jenis_usaha = $this->request->getPost('jenis_usaha');
        $id = $this->request->getPost('id');
        
        
        $data = [
            'user_id'  => session('userId'),
            'client_id' => session('clientId'),
            'nama_lengkap' => $nama_lengkap,
            'no_telp' => $no_telp,
            'alamat' => $alamat,
            'jenis_usaha' => $jenis_usaha,
        ];

         
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
            
            $text = 'ditambahkan';
        }

        try {
            
            $this->klienModel->save($data);
            
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('data-klien');
        } catch (\Throwable $th) {
            
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }
    
    
   public function delete()
{
    

    $id = $this->request->getPost('id');
    
    try {
        
        $transaksiPembelian = $this->transaksiModel
            ->where('pembeli', $id)
            ->where('jenis', 'in')
            ->countAllResults();
        
        
        $transaksiPenjualan = $this->transaksiModel
            ->where('pembeli', $id)
            ->where('jenis', 'out')
            ->countAllResults();

         
        $totalTransaksi = $transaksiPembelian + $transaksiPenjualan;
        
         
        if ($totalTransaksi > 0) {
            $jenisTransaksi = [];
            if ($transaksiPembelian > 0) {
                $jenisTransaksi[] = "pembelian ($transaksiPembelian transaksi)";
            }
            if ($transaksiPenjualan > 0) {
                $jenisTransaksi[] = "penjualan ($transaksiPenjualan transaksi)";
            }
            
            $response = [
                "title" => "Tidak Dapat Dihapus", 
                "text" => "Klien ini tidak dapat dihapus karena masih memiliki " . implode(" dan ", $jenisTransaksi) . ". Hapus transaksi terkait terlebih dahulu.", 
                "icon" => "warning"
            ];
        } else {
            
            $this->klienModel->delete($id);
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"
            ];
        }
        
    } catch (\Throwable $th) {
        
        $response = [
            "title" => "Gagal", 
            "text" => "Data gagal dihapus: " . $th->getMessage(), 
            "icon" => "error"
        ];
    }

     
    return $this->response->setJSON($response);
}

}
