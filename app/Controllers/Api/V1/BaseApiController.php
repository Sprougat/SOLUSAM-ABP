<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BaseApiController extends BaseController
{
    

    protected function sendResponse($data, $message = 'Success', $code = ResponseInterface::HTTP_OK)
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null
        ]);
    }

    

    protected function sendError($errors, $message = 'Error', $code = ResponseInterface::HTTP_BAD_REQUEST)
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors
        ]);
    }
    
    

    protected function getJwtUserId()
    {
        if (isset($this->request->jwt) && isset($this->request->jwt->uid)) {
            return $this->request->jwt->uid;
        }
        return null;
    }
    
    

    protected function getJwtClientId()
    {
        if (isset($this->request->jwt) && isset($this->request->jwt->clientId) && $this->request->jwt->clientId !== null) {
            return $this->request->jwt->clientId;
        }
        return 1; 
    }
}
