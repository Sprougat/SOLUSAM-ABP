<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Users;

use CodeIgniter\HTTP\ResponseInterface;

class ResetController extends BaseController
{
     

    public function index()
    {
         
        
        if(session()->get('auth_type') != 'local'){
        return redirect()->to(base_url('dashboard'));
    }
          
        $data = [
            "title" => "Reset Password",
        ];

         
        return view('reset', $data);
    }

    

    public function update()
    {
        
         if(session()->get('auth_type') != 'local'){
        return redirect()->to(base_url('dashboard'));
    }
         
        $users = new Users();
         
        $userId = session('userId');

        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        
        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required|min_length[8]',
            'confirm_new_password'  => 'required|matches[new_password]',

             
            'current_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password wajib diisi',
                ]
            ],
            
            'new_password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'New Password wajib diisi',
                    'min_length' => 'New Password minimal 6 karakter'
                ]
            ],
            
            'confirm_new_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Konfirmasi Password wajib diisi',
                    'matches' => 'Konfirmasi Password tidak sesuai dengan New Password'
                ]
            ]
        ];


        if (!$this->validate($rules)) {
            $message = [
                'title' => 'Error',
                'text' => implode(', ', $this->validator->getErrors()),
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back()->withInput();
        }


        $currentPassword = $this->request->getPost('current_password');


        $newPassword     = $this->request->getPost('new_password');


        $user = $users->find($userId);



        if (!$user) {
            $message = [
                'title' => 'Error',
                'text' => 'User tidak ditemukan.',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }


        if (!password_verify($currentPassword, $user['password'])) {
            $message = [
                'title' => 'Error',
                'text' => 'Password lama tidak sesuai.',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }

        
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        
        $users->update($userId, [
            'password' => $hash
        ]);

        
        $message = [
            'title' => 'Success',
            'text' => 'Reset Password Berhasil',
            'icon' => 'success'
        ];

         
        session()->setFlashdata($message);
        
        return redirect()->back();
    }
}
