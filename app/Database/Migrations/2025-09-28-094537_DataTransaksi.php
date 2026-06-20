<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataTransaksi extends Migration
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
            'tanggal' => [
                'type'       => 'DATE'
            ],
            'sampah_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jenis' => [
                'type' => 'ENUM',
                'constraint' => ['in', 'out'],
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'pembeli' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'metode_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null' => true
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
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi');
    }
}
