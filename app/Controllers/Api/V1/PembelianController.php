<?php

namespace App\Controllers\Api\V1;

use App\Models\Transaksi;
use App\Models\Sampah;

class PembelianController extends BaseApiController
{
    public function index()
    {
        $clientId = $this->getJwtClientId();
        $model = new Transaksi();
        $data = $model->getPenjualan($clientId, 'in');
        
        return $this->sendResponse($data, 'Data transaksi pembelian retrieved');
    }

    public function create()
    {
        $rules = [
            'tanggal'      => 'required|valid_date',
            'nama_sampah'  => 'required|numeric',
            'jumlah_beli'  => 'required|numeric',
            'nama_pembeli' => 'required|numeric', 
            'metode_bayar' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $transaksiModel = new Transaksi();
        $sampahModel = new Sampah();

        $data = [
            'tanggal'      => $this->request->getVar('tanggal'),
            'sampah_id'    => $this->request->getVar('nama_sampah'),
            'jumlah'       => $this->request->getVar('jumlah_beli'),
            'pembeli'      => $this->request->getVar('nama_pembeli'),
            'metode_bayar' => $this->request->getVar('metode_bayar'),
            'client_id'    => $this->getJwtClientId(),
            'jenis'        => 'in'
        ];

        try {
            $transaksiModel->db->transStart();
            
            $transaksiModel->insert($data);
            $transaksiId = $transaksiModel->getInsertID();
            
            
            $sampahId = $data['sampah_id'];
            $stokTersedia = $sampahModel->getStokTersedia($sampahId); 
            
            
            
            
            $sampahData = $sampahModel->find($sampahId);
            $stokBaru = (int) $sampahData['satuan'] + (int) $data['jumlah'];
            $sampahModel->update($sampahId, ['satuan' => $stokBaru]);

            $transaksiModel->db->transComplete();

            if ($transaksiModel->db->transStatus() === false) {
                return $this->sendError(null, 'Gagal menyimpan transaksi', 500);
            }

            $data['id'] = $transaksiId;
            return $this->sendResponse($data, 'Transaksi pembelian berhasil ditambahkan', 201);

        } catch (\Exception $e) {
            $transaksiModel->db->transRollback();
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }

    public function delete($id = null)
    {
        $transaksiModel = new Transaksi();
        $sampahModel = new Sampah();
        
        $transaksi = $transaksiModel->find($id);

        if (!$transaksi || $transaksi['client_id'] != $this->getJwtClientId() || $transaksi['jenis'] != 'in') {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        try {
            $transaksiModel->db->transStart();
            
            
            $sampahData = $sampahModel->find($transaksi['sampah_id']);
            $stokBaru = (int) $sampahData['satuan'] - (int) $transaksi['jumlah'];
            $sampahModel->update($transaksi['sampah_id'], ['satuan' => $stokBaru]);

            $transaksiModel->delete($id);

            $transaksiModel->db->transComplete();

            return $this->sendResponse(null, 'Transaksi pembelian berhasil dihapus');
        } catch (\Exception $e) {
            $transaksiModel->db->transRollback();
            return $this->sendError(null, 'Gagal menghapus transaksi', 500);
        }
    }
}
