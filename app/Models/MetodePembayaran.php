<?php

namespace App\Models;

use CodeIgniter\Model;

class MetodePembayaran extends Model
{
     
    protected $table            = 'metode_pembayaran';
    
    
    protected $allowedFields    = [
        'nama',  
        'client_id'  
    ];

    
    
    
    protected $useTimestamps = true;
     
    protected $createdField  = 'created_at';
     
    protected $updatedField  = 'updated_at';
}
