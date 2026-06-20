<?php

namespace App\Libraries; 

class MidtransSnap
{
    
    public function __construct()
    {
        $config = config('Midtrans'); 

        
        \Midtrans\Config::$serverKey    = $config->serverKey;

        
        \Midtrans\Config::$isProduction = $config->isProduction;

        
        \Midtrans\Config::$isSanitized  = $config->isSanitized;

        
        \Midtrans\Config::$is3ds        = $config->is3ds;
    }

    

    public function createTransaction(array $params)
    {
        
        return \Midtrans\Snap::createTransaction($params);
    }
}
