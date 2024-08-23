<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbPresensiKaryawan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_presensi' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jam_masuk' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'id_kehadiran' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_presensi', true); // Primary key
        $this->forge->addKey('id_karyawan'); // Index for id_karyawan
        $this->forge->addKey('id_kehadiran'); // Index for id_kehadiran
        $this->forge->createTable('tb_presensi_karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_presensi_karyawan');
    }
}
