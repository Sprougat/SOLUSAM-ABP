<?php

namespace App\Controllers\Api\V1;

use App\Models\Client;

class KlienController extends BaseApiController
{
    public function index()
    {
        
        
        
        
        
        
        $clientId = $this->getJwtClientId();
        $model = new Client();
        $data = $model->where('client_id', $clientId)->findAll();
        
        return $this->sendResponse($data, 'Data klien retrieved');
    }

    public function create()
    {
        $rules = [
            'nama_lengkap' => 'required',
            'no_telp'      => 'required',
            'alamat'       => 'required',
            'jenis_usaha'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $model = new Client();
        $data = [
            'client_id'    => $this->getJwtClientId(),
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'no_telp'      => $this->request->getVar('no_telp'),
            'alamat'       => $this->request->getVar('alamat'),
            'jenis_usaha'  => $this->request->getVar('jenis_usaha'),
        ];
        
        $model->insert($data);
        $data['id'] = $model->getInsertID();

        return $this->sendResponse($data, 'Data klien created', 201);
    }

    public function update($id = null)
    {
        $model = new Client();
        $klien = $model->find($id);

        if (!$klien || $klien['client_id'] != $this->getJwtClientId()) {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        $rules = [
            'nama_lengkap' => 'required',
            'no_telp'      => 'required',
            'alamat'       => 'required',
            'jenis_usaha'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $data = [
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'no_telp'      => $this->request->getVar('no_telp'),
            'alamat'       => $this->request->getVar('alamat'),
            'jenis_usaha'  => $this->request->getVar('jenis_usaha'),
        ];
        
        $model->update($id, $data);
        $data['id'] = $id;

        return $this->sendResponse($data, 'Data klien updated');
    }

    public function delete($id = null)
    {
        $model = new Client();
        $klien = $model->find($id);

        if (!$klien || $klien['client_id'] != $this->getJwtClientId()) {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        $model->delete($id);
        return $this->sendResponse(null, 'Data klien deleted');
    }
}
