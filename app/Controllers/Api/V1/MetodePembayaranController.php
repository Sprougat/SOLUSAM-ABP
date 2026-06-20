<?php

namespace App\Controllers\Api\V1;

use App\Models\MetodePembayaran;

class MetodePembayaranController extends BaseApiController
{
    public function index()
    {
        $clientId = $this->getJwtClientId();
        $model = new MetodePembayaran();
        $data = $model->where('client_id', $clientId)->findAll();
        
        return $this->sendResponse($data, 'Data metode pembayaran retrieved');
    }

    public function create()
    {
        $rules = [
            'nama_metode'  => 'required',
            'nomor_rekening' => 'required',
            'atas_nama'    => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $model = new MetodePembayaran();
        $data = [
            'client_id'      => $this->getJwtClientId(),
            'nama_metode'    => $this->request->getVar('nama_metode'),
            'nomor_rekening' => $this->request->getVar('nomor_rekening'),
            'atas_nama'      => $this->request->getVar('atas_nama'),
        ];
        
        $model->insert($data);
        $data['id'] = $model->getInsertID();

        return $this->sendResponse($data, 'Data metode pembayaran created', 201);
    }

    public function update($id = null)
    {
        $model = new MetodePembayaran();
        $metode = $model->find($id);

        if (!$metode || $metode['client_id'] != $this->getJwtClientId()) {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        $rules = [
            'nama_metode'  => 'required',
            'nomor_rekening' => 'required',
            'atas_nama'    => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $data = [
            'nama_metode'    => $this->request->getVar('nama_metode'),
            'nomor_rekening' => $this->request->getVar('nomor_rekening'),
            'atas_nama'      => $this->request->getVar('atas_nama'),
        ];
        
        $model->update($id, $data);
        $data['id'] = $id;

        return $this->sendResponse($data, 'Data metode pembayaran updated');
    }

    public function delete($id = null)
    {
        $model = new MetodePembayaran();
        $metode = $model->find($id);

        if (!$metode || $metode['client_id'] != $this->getJwtClientId()) {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        $model->delete($id);
        return $this->sendResponse(null, 'Data metode pembayaran deleted');
    }
}
