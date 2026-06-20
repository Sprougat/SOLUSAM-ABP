<?php

namespace App\Controllers;

use App\Models\Users;
use CodeIgniter\Controller;
use Config\Services;

class ForgotPasswordController extends Controller
{
    
    public function index()
    {
        return view('forgot_password');
    }

    
    public function sendResetLink()
    {
        
        $email = $this->request->getPost('email');
         
        $userModel = new Users();
        
        $user = $userModel->where('email', $email)->first();

        
        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        }

        
        if ($user['auth_type'] === 'google') {
            return redirect()->back()->with('error', 'Akun ini terdaftar menggunakan Google. Tidak dapat melakukan reset password lokal.');
        }
        
        
        $token = bin2hex(random_bytes(16));
        
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        
        $db = \Config\Database::connect();
        $builder = $db->table('password_resets');

        
        $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $recentRequests = $builder->where('email', $email)
                                  ->where('created_at >=', $oneHourAgo)
                                  ->countAllResults();

        if ($recentRequests >= 3) {
            return redirect()->back()->with('error', 'Terlalu banyak permintaan. Silakan coba lagi setelah 1 jam.');
        }
        
        
        $dataInsert = [
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
         
        $builder->insert($dataInsert);
        
         
        $resetLink = base_url('reset-password/' . $token);

        
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password');
        
        
        $htmlMessage = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
            <h2 style='color: #28a745; text-align: center;'>Reset Password</h2>
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mereset password akun Anda di <strong>SOLUSAM</strong>. Jika Anda merasa tidak melakukan permintaan ini, abaikan email ini.</p>
            <p>Klik tombol di bawah ini untuk mereset password Anda (tautan ini hanya berlaku selama 15 menit):</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$resetLink}' style='background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Reset Password</a>
            </div>
            <p style='font-size: 12px; color: #777; text-align: center;'>Atau copy link berikut:<br><a href='{$resetLink}' style='color: #28a745;'>{$resetLink}</a></p>
            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #aaa; text-align: center;'>SOLUSAM &copy; " . date('Y') . "</p>
        </div>";
        
        
        $emailService->setMessage($htmlMessage);
        
        
        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
        }
}
    
    public function resetPassword($token)
    {
        
        $db = \Config\Database::connect();
        
        $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

        
        if (!$reset || strtotime($reset->expires_at) < time()) {
            return redirect()->to('forgot-password')->with('error', 'Token tidak valid atau telah kadaluarsa.');
        }

         
        return view('reset_password', ['token' => $token]);
    }

     
    public function updatePassword()
{
    
    $token = $this->request->getPost('token');
    $password = $this->request->getPost('password');
    $confirmPassword = $this->request->getPost('confirm_password');

    
    $rules = [
        'password' => [
            'rules' => 'required|min_length[6]',
            'errors' => [
                'required' => 'Password baru wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.'
            ]
        ],
        'confirm_password' => [
            'rules' => 'required|matches[password]',
            'errors' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches' => 'Konfirmasi password tidak cocok.'
            ]
        ]
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
    }

     
    $db = \Config\Database::connect();
    
    $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

    
    if (!$reset || strtotime($reset->expires_at) < time()) {
        return redirect()->to('forgot-password')->with('error', 'Token tidak valid atau telah kadaluarsa.');
    }

    
    $userModel = new \App\Models\Users();
    $user = $userModel->where('email', $reset->email)->first();

    
    if (!$user) {
        return redirect()->to('forgot-password')->with('error', 'User tidak ditemukan.');
    }

    
    $passwordHistoryModel = new \App\Models\PasswordHistory();

    
    if (password_verify($password, $user['password'])) {
        return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password saat ini.');
    }

    
    $oldPasswords = $passwordHistoryModel
        ->where('user_id', $user['id'])
        ->orderBy('created_at', 'DESC')
        ->findAll(3);

    
    foreach ($oldPasswords as $old) {
        if (password_verify($password, $old['password_hash'])) {
            return redirect()->back()->with('error', 'Password ini sudah pernah digunakan sebelumnya.');
        }
    }

    
    $passwordHistoryModel->insert([
        'user_id' => $user['id'],
        'password_hash' => $user['password'],
    ]);
    
     
    $userModel->update($user['id'], [
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    
    $db->table('password_resets')->where('token', $token)->delete();

    
    return redirect()->to('login')->with('success', 'Password berhasil diubah. Silakan login.');
}

}
