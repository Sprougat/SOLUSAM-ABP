<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Client;
use App\Models\MetodePembayaran;
use App\Models\Sampah;
use App\Models\Transaksi;

use App\Libraries\MidtransSnap;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PenjualanController extends BaseController
{
     
    protected $sampahModel;
    protected $metodeBayarModel;
    protected $klienModel;
    protected $transaksiModel;
    protected $midtransSnap;

    
    public function __construct()
    {
        $this->sampahModel = new Sampah();
        $this->metodeBayarModel = new MetodePembayaran();
        $this->klienModel = new Client();
        $this->transaksiModel = new Transaksi();
        $this->midtransSnap = new MidtransSnap();
    }

      
    public function index()
    {
        $data = [
            "title" => "Data Penjualan",
            
            "data" => $this->transaksiModel->getPenjualan(session('clientId'), 'out'),
        ];

        return view('penjualan/index', $data);
    }

    
    public function create()
    {
        $data = [
            "title" => "Tambah Data Penjualan",
             
            "sampah" => $this->sampahModel
                ->where('client_id', session('clientId'))
                ->findAll(),
             
            "bayar" => $this->metodeBayarModel
                ->where('client_id', session('clientId'))
                ->findAll(),
             
            "klien" => $this->klienModel
                ->where('client_id', session('clientId'))
                ->findAll(),
        ];

        return view('penjualan/create', $data);
    }

    
    public function sampahAjax()
    {
        $id = $this->request->getPost('id');
        $data = $this->sampahModel->find($id);

        
        if ($data) {
            $stokTersedia = $this->sampahModel->getStokTersedia($id);
            $data['stok_tersedia'] = $stokTersedia;
        }

        return $this->response->setJSON($data);
    }

    
    public function store()
    {
        
        $tanggal = $this->request->getPost('tanggal');
        $nama_sampah = $this->request->getPost('nama_sampah');
        $jumlah_jual = $this->request->getPost('jumlah_jual');
        $metode_bayar = $this->request->getPost('metode_bayar');
        $id = $this->request->getPost('id');

         
        $stokTersedia = $this->sampahModel->getStokTersedia($nama_sampah);
        if ($jumlah_jual > $stokTersedia) {
            $message = [
                'title' => 'Error',
                'text' => 'Jumlah yang dijual (' . $jumlah_jual . ' kg) melebihi stok tersedia (' . $stokTersedia . ' kg)',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }

       
        $sampahData = $this->sampahModel->find($nama_sampah);
        $totalHarga = $sampahData['harga_jual'] * $jumlah_jual;

         
        $data = [
            'tanggal' => $tanggal,
            'sampah_id' => $nama_sampah,
            'jumlah' => $jumlah_jual,
            'metode_bayar' => $metode_bayar,
        ];

         
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
             
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
            $data['jenis'] = 'out';
        }

       
        if ($metode_bayar === 'midtrans') {
            try {
                
                $this->transaksiModel->db->transException(true)->transStart();
                $this->transaksiModel->save($data);
                $transaksiId = $this->transaksiModel->getInsertID();

                
                $orderId = 'TRX-' . $transaksiId . '-' . time();

                
                $this->transaksiModel->update($transaksiId, [
                    'bukti'          => $orderId,
                    'payment_status' => 'pending',
                ]);

                
                $clientData = $this->klienModel->find(session('clientId'));

                
                $midtransParams = [
                     
                    'transaction_details' => [
                    
                        'order_id' => $orderId,
                    
                        'gross_amount' => (int) $totalHarga,
                    ],
                    
                    'item_details' => [
                        [
                             
                            'id' => 'SMPH-' . $nama_sampah,
                             
                            'price' => (int) $sampahData['harga_jual'],
                             
                            'quantity' => (int) $jumlah_jual,
                             
                            'name' => $sampahData['nama_sampah'] . ' (' . $jumlah_jual . ' kg)',
                        ]
                    ],
                    
                    'customer_details' => [
                         
                        'first_name' => $clientData['nama_lengkap'] ?? 'Customer',
                        
                        'email' => $clientData['email'] ?? 'customer@example.com',
                         
                        'phone' => $clientData['no_telp'] ?? '',
                    ],
                    
                    'callbacks' => [
                        
                        'finish' => base_url('penjualan/midtrans-finish'),
                        
                        'unfinish' => base_url('penjualan/midtrans-unfinish'),
                        
                        'error' => base_url('penjualan/midtrans-error'),
                    ]
                ];

                
                $midtransTransaction = $this->midtransSnap->createTransaction($midtransParams);
                
                $token = $midtransTransaction->token;

                
                $this->transaksiModel->db->transComplete();

               
                if ($this->request->isAJAX()) {
                     
                    return $this->response->setJSON([
                        'success' => true,
                        'token' => $token,
                        'transaksi_id' => $transaksiId,
                        'order_id' => $orderId
                    ]);
                }

               
              
             
                
                
                return redirect()->to('penjualan/midtrans-payment?token=' . $token);
            } catch (\Throwable $th) {
                 
                $this->transaksiModel->db->transRollback();

                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal membuat transaksi Midtrans: ' . $th->getMessage()
                    ]);
                }

                 
                $message = [
                    'title' => 'Error',
                    'text' => 'Gagal membuat transaksi Midtrans: ' . $th->getMessage(),
                    'icon' => 'error'
                ];
                session()->setFlashdata($message);
                return redirect()->back();
            }
        }

        
        $stokTersedia = $stokTersedia - $jumlah_jual;

        try {
             
            $this->transaksiModel->db->transException(true)->transStart();
             
            $this->transaksiModel->save($data);
             
            $this->sampahModel->update($nama_sampah, ['satuan' => $stokTersedia]);
             
            $this->transaksiModel->db->transComplete();

             
            if ($this->request->isAJAX()) {
                
                $transaksiId = $this->transaksiModel->getInsertID();
                  
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil ' . $text,
                    'transaksi_id' => $transaksiId,
                    'redirect_url' => base_url('penjualan/qrcode/' . $transaksiId)
                ]);
            }

             
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        } catch (\Throwable $th) {
            
            $this->transaksiModel->db->transRollback();
            
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data gagal ' . $text
                ]);
            }

             
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }

    
    public function delete()
{
      
    $id = $this->request->getPost('id');

    
    $transaksi = $this->transaksiModel->find($id);

       
    if (!$transaksi) {
        return $this->response->setJSON([
            "title" => "Gagal",
            "text" => "Transaksi tidak ditemukan",
            "icon" => "error"
        ]);
    }

   
    $stokSekarang = $this->sampahModel->getStokTersedia($transaksi['sampah_id']);

   
    $stokBaru = $stokSekarang + $transaksi['jumlah'];

    try {
         
        $this->transaksiModel->db->transStart();

        
        $this->sampahModel->update($transaksi['sampah_id'], [
            'satuan' => $stokBaru
        ]);

        
        $this->transaksiModel->delete($id);

         
        $this->transaksiModel->db->transComplete();

        
        return $this->response->setJSON([
            "title" => "Berhasil",
            "text" => "Transaksi berhasil dihapus, stok dikembalikan",
            "icon" => "success"
        ]);

    } catch (\Throwable $th) {

        
        $this->transaksiModel->db->transRollback();

        return $this->response->setJSON([
            "title" => "Gagal",
            "text" => "Gagal menghapus transaksi",
            "icon" => "error"
        ]);
    }
}
    

    public function generateQrCode($id)
    {
        try {
            
            $transaksi = $this->transaksiModel->find($id);

            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            
            if ($transaksi['client_id'] != session('clientId')) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak');
            }

            
            $qrData = base_url('public/transaksi/' . $id);

            
            $qrCode = new QrCode($qrData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="qrcode-' . $id . '.png"');

            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
            
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="error.png"');

            
            $errorImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            return $this->response->setBody($errorImage);
        }
    }

    

    public function showQrCode($id)
    {
        try {
            
            $builder = $this->transaksiModel->db->table('transaksi t');
            $builder->select('t.*, s.nama_sampah, s.harga_jual, s.satuan');
            $builder->join('data_sampah s', 's.id = t.sampah_id');
            $builder->where('t.id', $id);
            $builder->where('t.client_id', session('clientId'));

            $transaksi = $builder->get()->getRowArray();

            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            
            $qrData = base_url('public/transaksi/' . $id);

            $qrCode = new QrCode($qrData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

            $data = [
                'title' => 'QR Code Pembayaran',
                'transaksi' => $transaksi,
                'qr_code_base64' => $qrCodeBase64,
                'qr_code_url' => base_url('penjualan/qrcode-image/' . $id)
            ];

            return view('penjualan/qrcode', $data);
        } catch (\Exception $e) {
            
            $message = [
                'title' => 'Error',
                'text' => 'Gagal menampilkan QR Code: ' . $e->getMessage(),
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        }
    }
    

    public function generateQrCodeSimple($id)
    {
        try {
            
            $transaksi = $this->transaksiModel->find($id);

            
            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            
            if ($transaksi['client_id'] != session('clientId')) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak');
            }

            
            $qrData = base_url('public/transaksi/' . $id);

            
            $qrUrl = 'https://chart.googleapis.com/chart?chs=300x300&chld=M|0&cht=qr&chl=' . urlencode($qrData);

            
            return redirect()->to($qrUrl);
        } catch (\Exception $e) {
            
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Gagal generate QR Code: ' . $e->getMessage());
        }
    }

    

    public function testQrCode()
    {
        try {
            
            $testData = json_encode(['test' => 'data', 'id' => 123]);

            
            $qrCode = new QrCode($testData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            
            $this->response->setHeader('Content-Type', 'image/png');
            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
            
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    

    public function midtransPayment()
    {
         
        $token = $this->request->getGet('token');

        
        if (!$token) {
            $message = [
                'title' => 'Error',
                'text' => 'Token pembayaran tidak ditemukan',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        }

        
        $config = config('Midtrans');

        
        $data = [
            'title' => 'Pembayaran Midtrans',
            'token' => $token,
            'client_key' => $config->clientKey
        ];

        return view('penjualan/midtrans_payment', $data);
    }

   

    public function midtransNotification()
    {
        try {
            
            $notification = json_decode(file_get_contents('php://input'), true);

            
            if (!$notification) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid notification']);
            }

            
            $orderId           = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus       = $notification['fraud_status'] ?? null;
            $grossAmount       = $notification['gross_amount'] ?? null;
            $statusCode        = $notification['status_code'] ?? null;

            
            if (!$orderId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Order ID not found']);
            }

            
            
            
            
            $config        = config('Midtrans');
            $serverKey     = $config->serverKey;
            $signatureInput = $orderId . $statusCode . $grossAmount . $serverKey;
            $expectedSig   = hash('sha512', $signatureInput);
            $receivedSig   = $notification['signature_key'] ?? '';

            if (!hash_equals($expectedSig, $receivedSig)) {
                log_message('warning', 'Midtrans webhook: signature tidak valid untuk order ' . $orderId);
                return $this->response->setStatusCode(403)
                    ->setJSON(['status' => 'error', 'message' => 'Invalid signature']);
            }

            
            $transaksi = $this->transaksiModel->where('bukti', $orderId)->first();

            if (!$transaksi) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Transaction not found']);
            }

            
            
            
            $sampah = $this->sampahModel->find($transaksi['sampah_id']);
            $expectedGrossAmount = $transaksi['jumlah'] * $sampah['harga_jual'];

            if (abs((float)$grossAmount - (float)$expectedGrossAmount) > 0.01) {
                log_message('error', 'Midtrans webhook: Gross amount mismatch untuk order ' . $orderId . '. Expected: ' . $expectedGrossAmount . ', Received: ' . $grossAmount);
                return $this->response->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Invalid gross amount']);
            }

            
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'challenge') {
                    
                    $this->transaksiModel->update($transaksi['id'], ['payment_status' => 'challenge']);
                    log_message('warning', 'Midtrans: transaksi ' . $orderId . ' ditandai challenge (fraud detection).');
                } elseif ($fraudStatus === 'accept') {
                    
                    $this->updateStokAfterPayment($transaksi);
                }
            } elseif ($transactionStatus === 'settlement') {
                
                $this->updateStokAfterPayment($transaksi);
            } elseif ($transactionStatus === 'pending') {
                
                log_message('info', 'Midtrans: transaksi ' . $orderId . ' masih pending.');
            } elseif ($transactionStatus === 'expire') {
                
                $this->transaksiModel->update($transaksi['id'], ['payment_status' => 'expired']);
                log_message('info', 'Midtrans: transaksi ' . $orderId . ' expired.');
            } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
                
                $this->transaksiModel->update($transaksi['id'], ['payment_status' => 'failed']);
                log_message('info', 'Midtrans: transaksi ' . $orderId . ' ' . $transactionStatus . '.');
            }

            return $this->response->setJSON(['status' => 'ok']);

        } catch (\Exception $e) {
            log_message('error', 'Midtrans notification error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    

    public function midtransFinish()
    {
        
        $orderId = $this->request->getGet('order_id');

        
        if ($orderId) {
           
            $transaksi = $this->transaksiModel->where('bukti', $orderId)->first();

            
            if ($transaksi) {
               
                $config = config('Midtrans');
                 
                \Midtrans\Config::$serverKey = $config->serverKey;
                \Midtrans\Config::$isProduction = $config->isProduction;
                \Midtrans\Config::$isSanitized = $config->isSanitized;
                \Midtrans\Config::$is3ds = $config->is3ds;

                try {
                     
                    $status = \Midtrans\Transaction::status($orderId);
                    
                    $transactionStatus = is_object($status) ? $status->transaction_status : ($status['transaction_status'] ?? 'unknown');

                    
                    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                        
                        
                        $this->updateStokAfterPayment($transaksi);

                         
                        $message = [
                            'title' => 'Success',
                            'text' => 'Pembayaran berhasil!',
                            'icon' => 'success'
                        ];
                    } elseif ($transactionStatus === 'expire') {
                        
                        $this->transaksiModel->update($transaksi['id'], ['payment_status' => 'expired']);
                        $message = [
                            'title' => 'Warning',
                            'text'  => 'Pembayaran telah kedaluwarsa. Silakan buat transaksi baru.',
                            'icon'  => 'warning'
                        ];
                    } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
                        
                        $this->transaksiModel->update($transaksi['id'], ['payment_status' => 'failed']);
                        $message = [
                            'title' => 'Warning',
                            'text'  => 'Pembayaran ' . $transactionStatus . '. Silakan buat transaksi baru.',
                            'icon'  => 'warning'
                        ];
                    } else {
                          
                        $message = [
                            'title' => 'Info',
                            'text' => 'Pembayaran sedang diproses. Status: ' . $transactionStatus,
                            'icon' => 'info'
                        ];
                    }
                } catch (\Exception $e) {
                     
                    $message = [
                        'title' => 'Info',
                        'text' => 'Pembayaran sedang diproses',
                        'icon' => 'info'
                    ];
                }
            } else {
                
                $message = [
                    'title' => 'Error',
                    'text' => 'Transaksi tidak ditemukan',
                    'icon' => 'error'
                ];
            }
        } else {
            
            $message = [
                'title' => 'Info',
                'text' => 'Terima kasih! Pembayaran sedang diproses',
                'icon' => 'info'
            ];
        }

         
        session()->setFlashdata($message);

        
        return redirect()->to('penjualan');
    }

    

    public function midtransUnfinish()
    {
        
        $message = [
            'title' => 'Info',
            'text' => 'Pembayaran belum selesai. Silakan coba lagi atau hubungi customer service.',
            'icon' => 'info'
        ];
         
        session()->setFlashdata($message);
        
        return redirect()->to('penjualan');
    }

    

    public function midtransError()
    {
        
        $message = [
            'title' => 'Error',
            'text' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
            'icon' => 'error'
        ];
        
        
        session()->setFlashdata($message);

         
        return redirect()->to('penjualan');
    }

    

    private function updateStokAfterPayment(array $transaksi): bool
    {
        
        if (($transaksi['payment_status'] ?? null) === 'paid') {
            log_message('info', 'updateStokAfterPayment: transaksi ' . $transaksi['id'] . ' sudah paid, skip.');
            return false;
        }

        try {
            $sampahId   = $transaksi['sampah_id'];
            $jumlahJual = (int) $transaksi['jumlah'];

            
            $sampah = $this->sampahModel->find($sampahId);

            if (!$sampah) {
                log_message('error', 'updateStokAfterPayment: sampah ID ' . $sampahId . ' tidak ditemukan.');
                return false;
            }

            
            $this->transaksiModel->db->transException(true)->transStart();

            
            $stokBaru = (int) $sampah['satuan'] - $jumlahJual;
            $this->sampahModel->update($sampahId, ['satuan' => $stokBaru]);

            
            $this->transaksiModel->update($transaksi['id'], ['payment_status' => 'paid']);

            $this->transaksiModel->db->transComplete();

            log_message('info', 'updateStokAfterPayment: transaksi ' . $transaksi['id'] . ' berhasil diproses, stok baru: ' . $stokBaru);
            return true;

        } catch (\Exception $e) {
            $this->transaksiModel->db->transRollback();
            log_message('error', 'updateStokAfterPayment error: ' . $e->getMessage());
            return false;
        }
    }
}