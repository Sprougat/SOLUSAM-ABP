<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGoogleAuthToUsers extends Migration
{
    public function up()
    {
        
        $this->forge->addColumn('users', [
            'google_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'email',
            ],
        ]);

        
        $this->forge->addColumn('users', [
            'auth_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
                'default'    => 'local',
                'after'      => 'google_id',
            ],
        ]);

        
        $this->forge->addColumn('users', [
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'auth_type',
            ],
        ]);

        
        $this->forge->addColumn('users', [
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'nama_lengkap',
            ],
        ]);

        
        $this->forge->addColumn('users', [
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'no_telp',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'google_id');
        $this->forge->dropColumn('users', 'auth_type');
        $this->forge->dropColumn('users', 'nama_lengkap');
        $this->forge->dropColumn('users', 'no_telp');
        $this->forge->dropColumn('users', 'alamat');
    }
}
