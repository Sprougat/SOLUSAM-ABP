<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Client extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'    => true
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'    => true
            ],
            'alamat' => [
                'type'       => 'TEXT',
                'null' => true,
            ],
            'jenis_usaha' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'    => true
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'    => true
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
        $this->forge->createTable('client');
    }

    public function down()
    {
        $this->forge->dropTable('client');
    }
}
