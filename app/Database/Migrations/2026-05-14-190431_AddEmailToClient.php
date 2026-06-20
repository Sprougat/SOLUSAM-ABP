<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToClient extends Migration
{
    public function up()
    {
        
        
        $this->forge->addColumn('client', [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'nama_lengkap',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('client', 'email');
    }
}
