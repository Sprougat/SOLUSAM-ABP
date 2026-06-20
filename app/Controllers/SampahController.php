<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SampahController extends BaseController
{   
    
    protected $sampahModel;
    
    protected $transaksiModel;

      

    public function __construct()
    {
        $this->sampahModel = new \App\Models\Sampah();
         $this->transaksiModel = new \App\Models\Transaksi();
    }

    
    

    public function index()
    {
        $data = [
            "title" => "Data Sampah",
             
            "data" => $this->sampahModel
                ->where('client_id', session()->get('clientId'))
                ->findAll()
        ];

        return view('sampah/index', $data);
    }

    

    public function create()
    {
        $data = [
            "title" => "Tambah Data Sampah"
        ];

        return view('sampah/create', $data);
    }

    
    

    public function edit($id)
    {
        $data = [
            "title" => "Edit Data Sampah",
            
            "data" => $this->sampahModel
                ->where('id', $id)
                ->first()
        ];

        return view('sampah/edit', $data);
    }

     

    public function store()
    {
         
        $nama_sampah = $this->request->getPost('nama_sampah');
        $harga_beli = $this->request->getPost('harga_beli');
        $harga_jual = $this->request->getPost('harga_jual');
        $satuan = $this->request->getPost('satuan');
        $id = $this->request->getPost('id');

         
        $data = [
            'nama_sampah' => $nama_sampah,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'satuan' => $satuan,
        ];

        
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
         
        } else {
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
        }

        try {
             
            $this->sampahModel->save($data);
              
            $message = [
                'title' => 'Success',
                'text' => 'Data sampah berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('sampah');
        } catch (\Throwable $th) {
             
            $message = [
                'title' => 'Error',
                'text' => 'Data sampah gagal ' . $text,
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
        
        $transaksiPenjualan = $this->transaksiModel
            ->where('sampah_id', $id)
            ->where('jenis', 'out')
            ->countAllResults();
        
        
        if ($transaksiPenjualan > 0) {
            $response = [
                "title" => "Tidak Dapat Dihapus", 
                "text" => "Data sampah ini tidak dapat dihapus karena masih memiliki $transaksiPenjualan transaksi penjualan. Hapus transaksi terkait terlebih dahulu.", 
                "icon" => "warning"
            ];
        } else {
            
            
            
            $this->transaksiModel
                ->where('sampah_id', $id)
                ->where('jenis', 'in')
                ->delete();
            
            
            $this->sampahModel->delete($id);
            
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"
            ];
        }
        
    } catch (\Exception $e) {
        
        $response = [
            "title" => "Gagal", 
            "text" => "Data gagal dihapus: " . $e->getMessage(), 
            "icon" => "error"
        ];
    }

    
    return $this->response->setJSON($response);
}

}
