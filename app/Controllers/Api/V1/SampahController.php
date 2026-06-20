<?php

namespace App\Controllers\Api\V1;

use App\Models\Sampah;

class SampahController extends BaseApiController
{
    public function index()
    {
        $clientId = $this->getJwtClientId();
        $model = new Sampah();
        $data = $model->where('client_id', $clientId)->findAll();
        
        return $this->sendResponse($data, 'Data sampah retrieved');
    }

    public function create()
    {
        $rules = [
            'nama_sampah' => 'required',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric',
            'satuan'      => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $model = new Sampah();
        $data = [
            'client_id'   => $this->getJwtClientId(),
            'nama_sampah' => $this->request->getVar('nama_sampah'),
            'harga_beli'  => $this->request->getVar('harga_beli'),
            'harga_jual'  => $this->request->getVar('harga_jual'),
            'satuan'      => $this->request->getVar('satuan'),
        ];
        
        $model->insert($data);
        $data['id'] = $model->getInsertID();

        return $this->sendResponse($data, 'Data sampah created', 201);
    }

    public function update($id = null)
    {
        $model = new Sampah();
        $sampah = $model->find($id);

        if (!$sampah || $sampah['client_id'] != $this->getJwtClientId()) {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        $rules = [
            'nama_sampah' => 'required',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric',
            'satuan'      => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $data = [
            'nama_sampah' => $this->request->getVar('nama_sampah'),
            'harga_beli'  => $this->request->getVar('harga_beli'),
            'harga_jual'  => $this->request->getVar('harga_jual'),
            'satuan'      => $this->request->getVar('satuan'),
        ];
        
        $model->update($id, $data);
        $data['id'] = $id;

        return $this->sendResponse($data, 'Data sampah updated');
    }

    public function delete($id = null)
    {
        $model = new Sampah();
        $sampah = $model->find($id);

        if (!$sampah || $sampah['client_id'] != $this->getJwtClientId()) {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        $model->delete($id);
        return $this->sendResponse(null, 'Data sampah deleted');
    }
}
