<?php

namespace App\Database\Seeds;

use App\Models\Client;
use App\Models\Users;
use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $userModel = new Users();
        $clientModel = new Client();

        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $userData = [
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => $hashedPassword,
            'role' => 1,
        ];

        $userModel->insert($userData);

        $clientData = [
            'user_id' => $userModel->getInsertID(),
            'nama_lengkap' => 'Admin',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Admin No. 1',
            'jenis_usaha' => '-',
        ];
        $clientModel->insert($clientData);

        echo "User admin berhasil ditambahkan.";
    }
}
