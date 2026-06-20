<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MetodePembayaranController extends BaseController
{
    
    protected $metodeBayarModel;

    
    
    public function __construct()
    {
         
        $this->metodeBayarModel = new \App\Models\MetodePembayaran();
    }

    
    
    public function index()
    {
        
        $data = [
            "title" => "Data Metode Pembayaran",
            
            
            "data" => $this->metodeBayarModel
            ->where('client_id', session()->get('clientId'))
            ->findAll()
        ];

        
        return view('metode_pembayaran/index', $data);
    }

    
    
    public function create()
    {   
        
        $data = [
            "title" => "Tambah Data Pembayaran"
        ];

          
        return view('metode_pembayaran/create', $data);
    }

     
    
    public function edit($id)
    {
        
        $data = [
            "title" => "Edit Data Metode Pembayaran",
            
             
            "data" => $this->metodeBayarModel
            ->where('id', $id)
            ->first()
        ];

         
        return view('metode_pembayaran/edit', $data);
    }

    
    
    public function store()
    {
        
        $nama = $this->request->getPost('nama');
         
        $id = $this->request->getPost('id');
        
        
        $data = [
            'nama' => $nama,
        ];

        
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
              
            $text = 'ditambahkan';
             
            $data['client_id'] = session('clientId');
        }

        try {
             
            $this->metodeBayarModel->save($data);
            
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
             
            session()->setFlashdata($message);
             
            return redirect()->to('metode-bayar');
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
             
            $this->metodeBayarModel->delete($id);
            
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"
            ];
        } catch (\Throwable $th) {
           
            $response = [
                "title" => "Gagal", 
                "text" => "Data gagal dihapus", 
                "icon" => "error"];
        }
        return $this->response->setJSON($response);
    }
}
