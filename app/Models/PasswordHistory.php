<?php
namespace App\Models; 

use CodeIgniter\Model; 

class PasswordHistory extends Model
{
    protected $table = 'password_history'; 

    
    protected $allowedFields = [
        'user_id',       
        'password_hash', 
        'created_at',    
        'updated_at'     
    ];

    
    protected $useTimestamps = true;
}
