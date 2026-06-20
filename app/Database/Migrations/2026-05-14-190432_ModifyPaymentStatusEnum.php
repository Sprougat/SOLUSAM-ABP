<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPaymentStatusEnum extends Migration
{
    public function up()
    {
        
        $this->forge->modifyColumn('transaksi', [
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'challenge', 'expired'],
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }

    public function down()
    {
        
        $this->forge->modifyColumn('transaksi', [
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'challenge'],
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }
}
