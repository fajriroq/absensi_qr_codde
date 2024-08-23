<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbKaryawan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_karyawan' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'nama_karyawan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'], // L = Laki-laki, P = Perempuan
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'unique_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
        ]);
        $this->forge->addKey('id_karyawan', true); // Primary key
        $this->forge->createTable('tb_karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_karyawan');
    }
}
