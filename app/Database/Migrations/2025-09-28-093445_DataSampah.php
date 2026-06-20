<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataSampah extends Migration
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
            'nama_sampah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'harga_beli' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'harga_jual' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
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
        $this->forge->createTable('data_sampah');
    }

    public function down()
    {
        $this->forge->dropTable('data_sampah');
    }
}
