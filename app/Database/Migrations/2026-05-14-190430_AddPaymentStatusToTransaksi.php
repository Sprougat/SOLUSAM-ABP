<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentStatusToTransaksi extends Migration
{
    public function up()
    {
        
        
        
        
        
        $this->forge->addColumn('transaksi', [
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'challenge'],
                'null'       => true,
                'default'    => null,
                'after'      => 'bukti',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi', 'payment_status');
    }
}
