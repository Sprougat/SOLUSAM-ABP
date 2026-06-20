<?php

namespace App\Models; 

use CodeIgniter\Model; 

class Transaksi extends Model
{
    protected $table = 'transaksi'; 
    
    protected $allowedFields = [
        'tanggal',         
        'sampah_id',       
        'jumlah',          
        'jenis',           
        'client_id',       
        'pembeli',         
        'metode_bayar',    
        'bukti',           
        'payment_status',  
    ];

    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 

    

    public function getPenjualan($client_id, $jenis)
    {
        
        $builder = $this->db->table('transaksi t');

        
        $builder->select('
            t.*, 
            s.nama_sampah, s.harga_beli, s.harga_jual, s.satuan,
            c.nama_lengkap, c.no_telp, c.alamat, c.jenis_usaha
        ');

        
        $builder->join('data_sampah s', 's.id = t.sampah_id');
        

        
        $builder->join('client c', 'c.id = t.pembeli', 'left');

        
        $builder->where('t.client_id', $client_id);

        $builder->where('t.jenis', $jenis);

        $builder->groupStart()
                ->where('t.payment_status', null)
                ->orWhere('t.payment_status', 'paid')
                ->groupEnd();

        
        $builder->orderBy('t.tanggal', 'DESC');

        
        $query = $builder->get()->getResultArray();

        return $query; 
    }

   public function getLaporan($client_id, $tahun = null, $bulan = null, $tanggal_mulai = null, $tanggal_selesai = null)
{
    $builder = $this->db->table('transaksi t'); 
    $builder->select(
        'COUNT(t.id) as jumlah, 
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) - 
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_keuntungan'
    ); 

    $builder->join('data_sampah s', 's.id = t.sampah_id'); 
    $builder->where('t.client_id', $client_id); 
    $builder->groupStart()
            ->where('t.payment_status', null)
            ->orWhere('t.payment_status', 'paid')
            ->groupEnd();

    
    if ($tahun && !$bulan && !$tanggal_mulai && !$tanggal_selesai) {
        $builder->select('MONTHNAME(t.tanggal) as periode'); 
        $builder->where('YEAR(t.tanggal)', $tahun); 
        $builder->groupBy('YEAR(t.tanggal), MONTH(t.tanggal)'); 
        $builder->orderBy('MONTH(t.tanggal)', 'ASC'); 
    }

    
    if ($bulan && $tahun) {
        $builder->select('DATE(t.tanggal) as periode'); 
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('MONTH(t.tanggal)', $bulan);
        $builder->groupBy('DATE(t.tanggal)'); 
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); 
    }

    
    if ($tanggal_mulai && $tanggal_selesai) {
        $builder->select('DATE(t.tanggal) as periode'); 
        $builder->where('DATE(t.tanggal) >=', $tanggal_mulai); 
        $builder->where('DATE(t.tanggal) <=', $tanggal_selesai); 
        $builder->groupBy('DATE(t.tanggal)'); 
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); 
    }

    $query = $builder->get()->getResultArray(); 
    return $query; 
}
    public function getLastTransaction($client_id, $bulan)
    {
    $builder = $this->db->table('transaksi t'); 
    $builder->select('t.*, s.nama_sampah, s.harga_beli, s.harga_jual, s.satuan'); 
    $builder->join('data_sampah s', 's.id = t.sampah_id'); 
    $builder->where('t.client_id', $client_id); 
    $builder->where('MONTH(t.tanggal)', $bulan); 
    $builder->groupStart()
            ->where('t.payment_status', null)
            ->orWhere('t.payment_status', 'paid')
            ->groupEnd();
    $builder->orderBy('t.tanggal', 'DESC'); 
    $builder->limit(3); 
    $query = $builder->get()->getResultArray(); 
    return $query; 
    }

    public function getRingkasanBulan($client_id, $bulan, $tahun)
    {
    $builder = $this->db->table('transaksi t'); 
    $builder->select(
        'COUNT(t.id) as jumlah, 
        SUM(t.jumlah) as total_jml,
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
        SUM(
            CASE 
                WHEN t.jenis = "out"  THEN (t.jumlah * s.harga_jual)
                WHEN t.jenis = "in" THEN -(t.jumlah * s.harga_beli)
                ELSE 0 
            END
        ) as total_keuntungan'
    ); 
    $builder->join('data_sampah s', 's.id = t.sampah_id'); 
    $builder->where('t.client_id', $client_id); 
    $builder->groupStart()
            ->where('t.payment_status', null)
            ->orWhere('t.payment_status', 'paid')
            ->groupEnd();

    
    if ($bulan && $tahun) {
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('MONTH(t.tanggal)', $bulan);
        }

    $query = $builder->get()->getRowArray(); 
    return $query; 
    }

    public function getTotalAll($client_id)
    {
    $builder = $this->db->table('transaksi t'); 
    $builder->select(
        'COUNT(t.id) as jumlah, 
        SUM(t.jumlah) as total_jml,
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
        SUM(
            CASE 
                WHEN t.jenis = "out"  THEN (t.jumlah * s.harga_jual)
                WHEN t.jenis = "in" THEN -(t.jumlah * s.harga_beli)
                ELSE 0 
            END
        ) as total_keuntungan'
    ); 
    $builder->join('data_sampah s', 's.id = t.sampah_id'); 
    $builder->where('t.client_id', $client_id); 
    $builder->groupStart()
            ->where('t.payment_status', null)
            ->orWhere('t.payment_status', 'paid')
            ->groupEnd();

    $query = $builder->get()->getRowArray(); 
    return $query; 
    }

    public function getDataInOutLaporan($client_id, $jenis = null, $tahun = null, $bulan = null, $tanggal_mulai = null, $tanggal_selesai = null)
{
    $builder = $this->db->table('transaksi t'); 
    $builder->select(
        't.*,
        s.nama_sampah,
        s.harga_jual,
        s.harga_beli,
        s.satuan,
        c.nama_lengkap,
        (t.jumlah * s.harga_jual) as total_pendapatan,
        (t.jumlah * s.harga_beli) as total_pengeluaran'
    ); 
    $builder->join('data_sampah s', 's.id = t.sampah_id'); 
    $builder->join('client c', 'c.id = t.pembeli', 'left'); 
    $builder->where('t.client_id', $client_id); 
    $builder->groupStart()
            ->where('t.payment_status', null)
            ->orWhere('t.payment_status', 'paid')
            ->groupEnd();

    if ($jenis == 'in') {
        $builder->where('t.jenis', 'in'); 
        } else {
        $builder->where('t.jenis', 'out'); 
        }

    
        if ($tahun && !$bulan && !$tanggal_mulai && !$tanggal_selesai) {
        $builder->where('YEAR(t.tanggal)', $tahun); 
        
        $builder->orderBy('MONTH(t.tanggal)', 'ASC'); 
        }

    
        if ($bulan && $tahun) {
        $builder->where('YEAR(t.tanggal)', $tahun); 
        $builder->where('MONTH(t.tanggal)', $bulan); 
        
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); 
        }

    
        if ($tanggal_mulai && $tanggal_selesai) {
        $builder->where('DATE(t.tanggal) >=', $tanggal_mulai); 
        $builder->where('DATE(t.tanggal) <=', $tanggal_selesai); 
        
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); 
        }

    $query = $builder->get()->getResultArray(); 
    return $query; 
    }

    

    public function getStokTersedia($sampah_id, $client_id)
    {
    $builder = $this->db->table('transaksi'); 
    $builder->select('
        SUM(CASE WHEN jenis = "in" THEN jumlah ELSE 0 END) - 
        SUM(CASE WHEN jenis = "out" THEN jumlah ELSE 0 END) as stok_tersedia
    '); 

    $builder->where('sampah_id', $sampah_id); 
    $builder->where('client_id', $client_id); 

    $result = $builder->get()->getRowArray(); 
    return $result['stok_tersedia'] ?? 0; 
    }

}
