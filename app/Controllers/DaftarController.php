<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Users;

class DaftarController extends BaseController
{
    

    public function index()
    {
        
        $data = [
            "title" => "Daftar - Solusam", 
        ];
         
        return view('daftar', $data);
    }

     

    public function register()
    {
        
        $userModel = new Users();

         

        $rules = [
            'username' => [
                'rules' => 'required|min_length[5]|max_length[15]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username wajib diisi',
                    'min_length' => 'Username minimal 5 karakter',
                    'max_length' => 'Username maksimal 15 karakter',
                    'is_unique' => 'Username sudah digunakan'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Email tidak valid',
                    'is_unique' => 'Email sudah digunakan'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ],
            'konfirmasi_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'matches' => 'Konfirmasi Password tidak sesuai'
                ]
            ],
            'nama_lengkap' => [
                'rules' => 'required|max_length[200]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi',
                    'max_length' => 'Nama lengkap maksimal 200 karakter'
                ]
            ],
            'no_telp' => [
                'rules' => 'required|numeric|max_length[15]',
                'errors' => [
                    'required' => 'No telp wajib diisi',
                    'numeric' => 'No telp hars berupa angka',
                    'max_length' => 'No telp maksimal 15 karakter'
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat wajib diisi',
                ]
            ],
        ];
        
         

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors-daftar', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        
        

        $username = trim($this->request->getPost('username'));
        $email = trim($this->request->getPost('email'));
        $nama_lengkap = trim(strtoupper($this->request->getPost('nama_lengkap')));
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $password = $this->request->getPost('password');

        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            

            $userData = [
                'kode_user' => $userModel->generateKode($nama_lengkap),
                'username' => trim(strtolower($username)),
                'email' => $email,
                'password' => $hashedPassword,
                'nama_lengkap' => $nama_lengkap,
                'no_telp' => $no_telp,
                'alamat' => $alamat, 
                'role' => 2,
                'auth_type' => 'local', 
            ];

            
            log_message('debug', 'Register attempt with data: ' . json_encode($userData));

            
            $result = $userModel->insert($userData);
            
            if (!$result) {
                
                log_message('error', 'Register failed: ' . json_encode($userModel->errors()));
                throw new \Exception('Insert failed');
            }


            $message = [
                'title' => 'Success',
                'text' => 'Registrasi berhasil. Silakan login.',
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to(base_url('/'));
        } catch (\Throwable $th) {

            log_message('error', 'Register exception: ' . $th->getMessage());
            $message = [
                'title' => 'Error',
                'text' => 'Terjadi kesalahan saat menyimpan data: ' . $th->getMessage(),
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back()->withInput();
        }
    }
}
