<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Users;

use Google\Client as GoogleClient;

use Google\Service\Oauth2;

class AuthController extends BaseController
{
     

    protected $googleClient;
     

    protected $usersModel;

    

    public function __construct()
    {
        
        $clientId     = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri  = env('GOOGLE_REDIRECT_URI');

         
        
        
        if (!$clientId || !$clientSecret || !$redirectUri) {
            log_message('error', 'Google OAuth Configuration Missing in .env');
            
        }

        
        $this->usersModel = new Users();

        
        $this->googleClient = new GoogleClient();

         
        $this->googleClient->setClientId($clientId);
        $this->googleClient->setClientSecret($clientSecret);
        $this->googleClient->setRedirectUri($redirectUri);

         
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
    }

    
    public function redirectToGoogle()
    {
        
        if (!$this->googleClient->getClientId()) {
            return redirect()->to(base_url('login'))->with('errors-login', ['Google OAuth belum dikonfigurasi. Hubungi administrator.']);
        }
        
        
        return redirect()->to($this->googleClient->createAuthUrl());
    }

     
     private function generateUniqueUsername($email)
    {
        
        $baseUsername = explode('@', $email)[0];
        $baseUsername = strtolower($baseUsername);
        
        
        if (strlen($baseUsername) > 15) {
            $baseUsername = substr($baseUsername, 0, 15);
        }
        
        
        $username = $baseUsername;
        $counter = 1;

         
        while ($this->usersModel->where('username', $username)->first()) {
            
            $suffix = (string)$counter;
             
            $maxLength = 15 - strlen($suffix);
            $username = substr($baseUsername, 0, $maxLength) . $suffix;
            $counter++;
        }
         
        return $username;
    }

    
    public function handleGoogleCallback()
    {
        
        $error = $this->request->getVar('error');
        if ($error) {
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login dibatalkan atau gagal.']);
        }

        
        $code = $this->request->getVar('code');

         
        if (!$code) {
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: No code received.']);
        }

        try {
            
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);

            
            if (isset($token['error'])) {
                log_message('error', 'Google OAuth Token Error: ' . ($token['error_description'] ?? $token['error']));
                return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: ' . ($token['error_description'] ?? $token['error'])]);
            }

             
            $this->googleClient->setAccessToken($token);

            
            $oauth2   = new Oauth2($this->googleClient);
            $userInfo = $oauth2->userinfo->get(); 
            $existingUser = null;
            
            log_message('debug', 'Google User Info: ' . json_encode($userInfo));

              
            if (!$userInfo || !isset($userInfo->id)) {
                log_message('error', 'Google OAuth Error: userInfo is null or missing ID');
                return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: user info not found']);
            }

            
            $user = $this->usersModel->where('google_id', $userInfo->id)->first();
            log_message('debug', 'User berdasarkan google_id: ' . json_encode($user));
            
            if (!$user) {
             
            $existingUser = $this->usersModel->where('email', $userInfo->email)->first();
                
            log_message('debug', 'User berdasarkan email: ' . json_encode($existingUser));
            
            if ($existingUser) {
                
                 session()->remove('is_new_user');
                
                if (empty($existingUser['google_id']) || $existingUser['google_id'] !== $userInfo->id) {
                    $this->usersModel->update($existingUser['id'], [
                        'google_id' => $userInfo->id,
                    ]);
                    log_message('debug', 'Update google_id untuk user: ' . $existingUser['id']);
        }

        
        $user = $this->usersModel->where('id', $existingUser['id'])->first();
                
   } else {
    
    
   
    $username = $this->generateUniqueUsername($userInfo->email);

    
    $kodeBaru = $this->usersModel->generateKode($userInfo->email);

    
    $newUserData = [
        'kode_user' => $kodeBaru, 
        'username'  => $username,
        'email'     => $userInfo->email,
        'google_id' => $userInfo->id,
        'role'      => 2,  
        'status'    => 1,   
        'auth_type' => 'google' 
    ];

    log_message('debug', 'User baru akan dibuat: ' . json_encode($newUserData));

    
    $newUserId = $this->usersModel->insert($newUserData);

     
    $user = $this->usersModel->find($newUserId);
     
    session()->set('is_new_user', true); 
   }

}

log_message('debug', 'Final User Session: ' . json_encode($user)); 

session()->set([
    'isLoggedIn' => true,
    'userId'    => $user['id'],
    'clientId'   => $user['id'],
    'email'      => $user['email'],
    'role'       => $user['role'],
    'auth_type'  => $user['auth_type']
]);

return redirect()->to(base_url('dashboard')); 

        } catch (\Exception $e) {
             
            log_message('error', 'Google Login Exception: ' . $e->getMessage());
            
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: ' . $e->getMessage()]);
        }
    }
}
