<?php

namespace App\Controllers\Api\V1;

use App\Models\Transaksi;
use App\Models\Sampah;
use App\Models\Client;
use App\Libraries\MidtransSnap;

class PenjualanController extends BaseApiController
{
    public function index()
    {
        $clientId = $this->getJwtClientId();
        $model = new Transaksi();
        $data = $model->getPenjualan($clientId, 'out');
        
        return $this->sendResponse($data, 'Data transaksi penjualan retrieved');
    }

    public function create()
    {
        $rules = [
            'tanggal'      => 'required|valid_date',
            'nama_sampah'  => 'required|numeric',
            'jumlah_jual'  => 'required|numeric',
            'metode_bayar' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->sendError($this->validator->getErrors(), 'Validation Error');
        }

        $transaksiModel = new Transaksi();
        $sampahModel = new Sampah();
        $klienModel = new Client();
        $clientId = $this->getJwtClientId();

        $jumlahJual = (int) $this->request->getVar('jumlah_jual');
        $sampahId = (int) $this->request->getVar('nama_sampah');
        $metodeBayar = $this->request->getVar('metode_bayar');

        $sampahData = $sampahModel->find($sampahId);
        if (!$sampahData || (int) $sampahData['satuan'] < $jumlahJual) {
            return $this->sendError(null, 'Stok tidak mencukupi atau sampah tidak ditemukan', 400);
        }

        $totalHarga = (float) $sampahData['harga_jual'] * $jumlahJual;

        $data = [
            'tanggal'      => $this->request->getVar('tanggal'),
            'sampah_id'    => $sampahId,
            'jumlah'       => $jumlahJual,
            'metode_bayar' => $metodeBayar,
            'client_id'    => $clientId,
            'jenis'        => 'out'
        ];

        
        if ($metodeBayar === 'midtrans') {
            try {
                $transaksiModel->db->transStart();
                $transaksiModel->insert($data);
                $transaksiId = $transaksiModel->getInsertID();

                $orderId = 'TRX-' . $transaksiId . '-' . time();

                $transaksiModel->update($transaksiId, [
                    'bukti'          => $orderId,
                    'payment_status' => 'pending',
                ]);

                $clientData = $klienModel->find($clientId);

                $midtransParams = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $totalHarga,
                    ],
                    'item_details' => [
                        [
                            'id' => 'SMPH-' . $sampahId,
                            'price' => (int) $sampahData['harga_jual'],
                            'quantity' => (int) $jumlahJual,
                            'name' => $sampahData['nama_sampah'] . ' (' . $jumlahJual . ' kg)',
                        ]
                    ],
                    'customer_details' => [
                        'first_name' => $clientData['nama_lengkap'] ?? 'Customer',
                        'email' => $clientData['email'] ?? 'customer@example.com',
                        'phone' => $clientData['no_telp'] ?? '',
                    ]
                ];

                $midtransSnap = new MidtransSnap();
                $midtransTransaction = $midtransSnap->createTransaction($midtransParams);
                $token = $midtransTransaction->token;

                $transaksiModel->db->transComplete();

                return $this->sendResponse([
                    'transaksi_id' => $transaksiId,
                    'order_id'     => $orderId,
                    'snap_token'   => $token,
                    'redirect_url' => $midtransTransaction->redirect_url ?? ''
                ], 'Transaksi Midtrans berhasil dibuat', 201);

            } catch (\Exception $e) {
                $transaksiModel->db->transRollback();
                return $this->sendError(null, 'Gagal membuat transaksi Midtrans: ' . $e->getMessage(), 500);
            }
        }

        
        try {
            $transaksiModel->db->transStart();
            $transaksiModel->insert($data);
            $transaksiId = $transaksiModel->getInsertID();

            
            $stokBaru = (int) $sampahData['satuan'] - (int) $jumlahJual;
            $sampahModel->update($sampahId, ['satuan' => $stokBaru]);

            $transaksiModel->db->transComplete();

            $data['id'] = $transaksiId;
            return $this->sendResponse($data, 'Transaksi penjualan berhasil', 201);

        } catch (\Exception $e) {
            $transaksiModel->db->transRollback();
            return $this->sendError(null, 'Gagal menyimpan transaksi', 500);
        }
    }

    public function delete($id = null)
    {
        $transaksiModel = new Transaksi();
        $sampahModel = new Sampah();
        
        $transaksi = $transaksiModel->find($id);

        if (!$transaksi || $transaksi['client_id'] != $this->getJwtClientId() || $transaksi['jenis'] != 'out') {
            return $this->sendError(null, 'Data tidak ditemukan', 404);
        }

        try {
            $transaksiModel->db->transStart();
            
            
            $sampahData = $sampahModel->find($transaksi['sampah_id']);
            $stokBaru = (int) $sampahData['satuan'] + (int) $transaksi['jumlah'];
            $sampahModel->update($transaksi['sampah_id'], ['satuan' => $stokBaru]);

            $transaksiModel->delete($id);

            $transaksiModel->db->transComplete();

            return $this->sendResponse(null, 'Transaksi penjualan berhasil dihapus');
        } catch (\Exception $e) {
            $transaksiModel->db->transRollback();
            return $this->sendError(null, 'Gagal menghapus transaksi', 500);
        }
    }
}
