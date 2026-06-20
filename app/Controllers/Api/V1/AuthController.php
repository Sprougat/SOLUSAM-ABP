<?php

namespace App\Controllers\Api\V1;

use App\Models\Users;
use App\Models\Client;
use Firebase\JWT\JWT;
use Google_Client;

class AuthController extends BaseApiController
{
    

    private function generateJwtToken($user)
    {
        $key = getenv('JWT_SECRET') ?: 'SOLUSAM_DEFAULT_SECRET_KEY_CHANGE_ME';
        $issuedAt   = time();
        $expire     = $issuedAt + (60 * 60 * 24 * 7); 

        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $expire,
            'uid'  => $user['id'],
            'role' => $user['role'] ?? 'client',
            'clientId' => $user['client_id'] ?? null
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public function login()
    {
        $rules = [
            'username_or_email' => 'required',
            'password'          => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $userModel = new Users();
        $input = $this->request->getPost('username_or_email') ?? $this->request->getVar('username_or_email');
        $password = $this->request->getPost('password') ?? $this->request->getVar('password');

        $user = $userModel->groupStart()
                            ->where('username', $input)
                            ->orWhere('email', $input)
                          ->groupEnd()
                          ->first();

        if (!$user) {
            return $this->sendError(null, 'Kredensial tidak valid', 401);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->sendError(null, 'Kredensial tidak valid', 401);
        }

        
        $clientData = null;
        if ($user['role'] == 'client' && !empty($user['client_id'])) {
            $clientModel = new Client();
            $clientData = $clientModel->find($user['client_id']);
        }

        $token = $this->generateJwtToken($user);

        return $this->sendResponse([
            'token' => $token,
            'user'  => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role']
            ],
            'client' => $clientData
        ], 'Login berhasil');
    }

    public function register()
    {
        $rules = [
            'nama_lengkap' => 'required',
            'username'     => 'required|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'no_telp'      => 'required',
            'password'     => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $clientModel = new Client();
            $userModel = new Users();

            $clientData = [
                'nama_lengkap' => $this->request->getVar('nama_lengkap'),
                'no_telp'      => $this->request->getVar('no_telp'),
                'alamat'       => $this->request->getVar('alamat') ?? '',
                'jenis_usaha'  => $this->request->getVar('jenis_usaha') ?? 'Lainnya',
            ];
            $clientModel->insert($clientData);
            $clientId = $clientModel->getInsertID();

            $userData = [
                'client_id' => $clientId,
                'username'  => $this->request->getVar('username'),
                'email'     => $this->request->getVar('email'),
                'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                'role'      => 'client',
                'auth_type' => 'local'
            ];
            $userModel->insert($userData);
            $userId = $userModel->getInsertID();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->sendError(null, 'Gagal mendaftarkan akun', 500);
            }

            
            $user = $userModel->find($userId);
            $token = $this->generateJwtToken($user);

            return $this->sendResponse([
                'token' => $token,
                'user'  => [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'email'    => $user['email'],
                    'role'     => $user['role']
                ],
                'client' => $clientData
            ], 'Registrasi berhasil', 201);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }

    public function google()
    {
        $idToken = $this->request->getVar('idToken');
        if (!$idToken) {
            return $this->sendError(null, 'idToken required', 400);
        }

        try {
            $client = new Google_Client(['client_id' => getenv('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($idToken);

            if (!$payload) {
                return $this->sendError(null, 'Invalid Google Token', 401);
            }

            $email = $payload['email'];
            $googleId = $payload['sub'];
            $name = $payload['name'];
            
            $userModel = new Users();
            $user = $userModel->where('email', $email)->first();

            $db = \Config\Database::connect();
            $db->transStart();

            if ($user) {
                
                if (empty($user['google_id'])) {
                    $userModel->update($user['id'], [
                        'google_id' => $googleId,
                        'auth_type' => 'google'
                    ]);
                }
            } else {
                
                $clientModel = new Client();
                $clientData = [
                    'nama_lengkap' => $name,
                    'email'        => $email,
                    'no_telp'      => '-',
                    'alamat'       => '-'
                ];
                $clientModel->insert($clientData);
                $clientId = $clientModel->getInsertID();

                
                $username = explode('@', $email)[0] . '_' . rand(1000, 9999);
                
                $userData = [
                    'client_id' => $clientId,
                    'username'  => $username,
                    'email'     => $email,
                    'password'  => '', 
                    'role'      => 'client',
                    'auth_type' => 'google',
                    'google_id' => $googleId
                ];
                $userModel->insert($userData);
                $userId = $userModel->getInsertID();
                $user = $userModel->find($userId);
            }

            $db->transComplete();

            $token = $this->generateJwtToken($user);
            
            $clientModel = new Client();
            $clientData = $clientModel->find($user['client_id']);

            return $this->sendResponse([
                'token' => $token,
                'user'  => [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'email'    => $user['email'],
                    'role'     => $user['role']
                ],
                'client' => $clientData
            ], 'Google Login berhasil');

        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }

    public function forgotPassword()
    {
        
        
        $email = $this->request->getVar('email');
        if (!$email) return $this->sendError(null, 'Email required', 400);

        $userModel = new Users();
        $user = $userModel->where('email', $email)->first();

        if (!$user) return $this->sendError(null, 'Email tidak ditemukan', 404);
        if ($user['auth_type'] === 'google') return $this->sendError(null, 'Akun Google tidak dapat mereset password lokal', 403);

        $db = \Config\Database::connect();
        $builder = $db->table('password_resets');

        
        $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $recentReq = $builder->where('email', $email)->where('created_at >=', $oneHourAgo)->countAllResults();
        
        if ($recentReq >= 3) return $this->sendError(null, 'Terlalu banyak permintaan. Coba lagi setelah 1 jam.', 429);

        
        
        
        $token = bin2hex(random_bytes(16));
        
        $builder->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        
        $resetLink = base_url('reset-password/' . $token); 
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password (API)');
        $emailService->setMessage("Token reset password Anda: {$token} <br> Atau link: {$resetLink}");
        
        if ($emailService->send()) {
            return $this->sendResponse(null, 'Instruksi reset password telah dikirim ke email');
        } else {
            return $this->sendError(null, 'Gagal mengirim email', 500);
        }
    }

    public function resetPassword()
    {
        $token = $this->request->getVar('token');
        $password = $this->request->getVar('password');

        if (!$token || !$password) return $this->sendError(null, 'Token & password required', 400);
        if (strlen($password) < 6) return $this->sendError(null, 'Password minimal 6 karakter', 400);

        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

        if (!$reset || strtotime($reset->expires_at) < time()) {
            return $this->sendError(null, 'Token tidak valid atau kedaluwarsa', 400);
        }

        $userModel = new Users();
        $user = $userModel->where('email', $reset->email)->first();

        
        $historyModel = new \App\Models\PasswordHistory();
        $oldPasswords = $historyModel->where('user_id', $user['id'])->orderBy('created_at', 'DESC')->findAll(3);
        
        if (password_verify($password, $user['password'])) {
            return $this->sendError(null, 'Password baru tidak boleh sama dengan password saat ini', 400);
        }

        foreach ($oldPasswords as $old) {
            if (password_verify($password, $old['password_hash'])) {
                return $this->sendError(null, 'Password ini sudah pernah digunakan sebelumnya', 400);
            }
        }

        
        $historyModel->insert(['user_id' => $user['id'], 'password_hash' => $user['password']]);
        $userModel->update($user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        $db->table('password_resets')->where('token', $token)->delete();

        return $this->sendResponse(null, 'Password berhasil diubah');
    }
}
