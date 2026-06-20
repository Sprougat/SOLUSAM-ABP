<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    public string $serverKey;
    public string $clientKey;
    public bool $isProduction;
    public bool $isSanitized = true;
    public bool $is3ds       = true;

    public function __construct()
    {
        parent::__construct();

        $this->serverKey    = getenv('MIDTRANS_SERVER_KEY');
        $this->clientKey    = getenv('MIDTRANS_CLIENT_KEY');
        $this->isProduction = getenv('MIDTRANS_IS_PRODUCTION') === 'true';
    }
}