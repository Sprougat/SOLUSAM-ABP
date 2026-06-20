<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MetodePembayaran extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,

            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,

            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('metode_pembayaran');
    }

    public function down()
    {
        $this->forge->dropTable('metode_pembayaran');
    }
}
