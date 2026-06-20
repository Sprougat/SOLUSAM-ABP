<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET');
        if (!$key) {
            $key = 'SOLUSAM_DEFAULT_SECRET_KEY_CHANGE_ME'; 
        }

        $header = $request->getHeaderLine("Authorization");
        
        
        if (!$header) {
            return \Config\Services::response()
                ->setJSON([
                    'success' => false,
                    'message' => 'Token required',
                    'data'    => null,
                    'errors'  => 'Missing Authorization Header'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        
        $token = null;
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            return \Config\Services::response()
                ->setJSON([
                    'success' => false,
                    'message' => 'Token required',
                    'data'    => null,
                    'errors'  => 'Token is missing or invalid format'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            
            $request->jwt = $decoded;

        } catch (Exception $ex) {
            return \Config\Services::response()
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid token',
                    'data'    => null,
                    'errors'  => $ex->getMessage()
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
