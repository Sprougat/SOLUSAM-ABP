<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Users;

class LoginController extends BaseController
{
    

    protected $m_users;

    

    public function __construct()
    {
        $this->m_users = new Users();
    }

    

    public function index()
    {
        $data = [
            "title" => "SOLUSAM",
        ];
        return view('login', $data);
    }

    

    public function attempLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $rules = [
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Username wajib diisi'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 6 karakter'
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

        $user = $this->m_users->getUser($username);

        if (!$user) {
            $message = [
                'title' => 'Error',
                'text' => 'Username atau password salah',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }

        if (!password_verify($password, $user['password'])) {
            $message = [
                'title' => 'Error',
                'text' => 'Username atau password salah',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }

        if ($user['status'] != '1') {
            $message = [
                'title' => 'Error',
                'text' => 'Akun Anda tidak aktif',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }

        $sessionData = [
            'kodeUser'   => $user['kode_user'],
            'userId'     => $user['id'],
            'clientId'   => $user['id'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'auth_type'  => 'local',
            'isLoggedIn' => true
        ];

        session()->set($sessionData);

        $this->m_users->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        return redirect()->to('dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
