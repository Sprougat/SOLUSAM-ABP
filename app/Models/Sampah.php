<?php

namespace App\Models; 

use CodeIgniter\Model; 

class Sampah extends Model
{
    protected $table = 'data_sampah'; 
    
    protected $allowedFields = [
        'nama_sampah',  
        'harga_beli',   
        'harga_jual',   
        'satuan',       
        'client_id'     
    ];

    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 

    

    public function getStokTersedia($sampah_id)
    {
        
        $builder = $this->db->table('data_sampah');

        
        $builder->select('satuan as stok_tersedia');

        
        $builder->where('id', $sampah_id);

        
        $result = $builder->get()->getRowArray();

        
        return $result['stok_tersedia'] ?? 0;
    }
}
