<?php

namespace App\Models;

use App\Models\PresensiInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Libraries\enums\Kehadiran;

class PresensiKaryawanModel extends Model implements PresensiInterface
{
   protected $primaryKey = 'id_presensi';

   protected $allowedFields = [
      'id_karyawan',
      'tanggal',
      'jam_masuk',
      'jam_keluar',
      'id_kehadiran',
      'keterangan'
   ];

   protected $table = 'tb_presensi_karyawan';

   public function cekAbsen(string|int $id, string|Time $date)
   {
      $result = $this->where(['id_karyawan' => $id, 'tanggal' => $date])->first();

      if (empty($result)) return false;

      return $result[$this->primaryKey];
   }

   public function absenMasuk(string $id, $date, $time)
   {
      $this->save([
         'id_karyawan' => $id,
         'tanggal' => $date,
         'jam_masuk' => $time,
         // 'jam_keluar' => '',
         'id_kehadiran' => Kehadiran::Hadir->value,
         'keterangan' => ''
      ]);
   }

   public function absenKeluar(string $id, $time)
   {
      $this->update($id, [
         'jam_keluar' => $time,
         'keterangan' => ''
      ]);
   }

   public function getPresensiByIdKaryawanTanggal($idKaryawan, $date)
   {
      return $this->where(['id_karyawan' => $idKaryawan, 'tanggal' => $date])->first();
   }

   public function getPresensiById(string $idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getPresensiByTanggal($tanggal)
   {
      return $this->setTable('tb_karyawan')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_karyawan AS id_karyawan_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_karyawan) tb_presensi_karyawan",
            "{$this->table}.id_karyawan = tb_presensi_karyawan.id_karyawan_presensi AND tb_presensi_karyawan.tanggal = '$tanggal'",
            'left'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_karyawan.id_kehadiran = tb_kehadiran.id_kehadiran',
            'left'
         )
         ->orderBy("nama_karyawan")
         ->findAll();
   }

   public function getPresensiByKehadiran(string $idKehadiran, $tanggal)
   {
      $this->join(
         'tb_karyawan',
         "tb_presensi_karyawan.id_karyawan = tb_karyawan.id_karyawan AND tb_presensi_karyawan.tanggal = '$tanggal'",
         'right'
      );

      if ($idKehadiran == '4') {
         $result = $this->findAll();

         $filteredResult = [];

         foreach ($result as $value) {
            if ($value['id_kehadiran'] != ('1' || '2' || '3')) {
               array_push($filteredResult, $value);
            }
         }

         return $filteredResult;
      } else {
         $this->where(['tb_presensi_karyawan.id_kehadiran' => $idKehadiran]);
         return $this->findAll();
      }
   }

   public function updatePresensi(
      $idPresensi,
      $idKaryawan,
      $tanggal,
      $idKehadiran,
      $jamMasuk,
      $jamKeluar,
      $keterangan
   ) {
      $presensi = $this->getPresensiByIdKaryawanTanggal($idKaryawan, $tanggal);

      $data = [
         'id_karyawan' => $idKaryawan,
         'tanggal' => $tanggal,
         'id_kehadiran' => $idKehadiran,
         'keterangan' => $keterangan ?? $presensi['keterangan'] ?? ''
      ];

      if ($idPresensi != null) {
         $data[$this->primaryKey] = $idPresensi;
      }

      if ($jamMasuk != null) {
         $data['jam_masuk'] = $jamMasuk;
      }

      if ($jamKeluar != null) {
         $data['jam_keluar'] = $jamKeluar;
      }

      return $this->save($data);
   }
}
